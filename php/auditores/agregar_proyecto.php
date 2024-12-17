<?php
session_start();

// Establecer la zona horaria de Paraguay, Asunción
date_default_timezone_set('America/Asuncion');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
require_once("conexion.php");

// Obtener el nombre del usuario que ha iniciado sesión
$user_id = $_SESSION['usuario_id'];
$conn = conectarDB();
$sql = "SELECT u.Nombre, r.Descripcion AS Rol
        FROM usuarios u
        LEFT JOIN roles r ON u.Idrol = r.Idrol
        WHERE u.IDusuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $solicitante = $user['Nombre'];
} else {
    $solicitante = "";
}

// Obtener la lista de usuarios por nivel de experiencia desde la tabla auditores
function obtenerUsuariosPorNivel($conn, $nivel) {
    $sql = "SELECT a.IDusuario, CONCAT(a.Nombre, ' ', a.Apellido) AS NombreCompleto
            FROM auditores a
            WHERE a.NivelExperiencia = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nivel);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0 ? $result : null;
}

// Obtener listas de usuarios por nivel específico
$gerentes_result = obtenerUsuariosPorNivel($conn, 'Gerente');
$seniores_result = obtenerUsuariosPorNivel($conn, 'Senior');
$juniors_result = obtenerUsuariosPorNivel($conn, 'Junior');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $prioridad = $_POST['prioridad'];
    $gerente = $_POST['gerente'];
    $senior = $_POST['senior'];
    $junior = $_POST['junior'];
    $fase_proyecto = $_POST['fase_proyecto'];
    $estado = $_POST['estado'];

    // Insertar el proyecto en la base de datos
    $sql_insert_proyecto = "INSERT INTO proyecto_auditoria (Descripcion, Fecha_inicio, Fecha_fin, Prioridad, Fase_proyecto, Estado, Creador_proyecto, Fecha_creacion)
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt_insert_proyecto = $conn->prepare($sql_insert_proyecto);
    $stmt_insert_proyecto->bind_param("sssssss", $descripcion, $fecha_inicio, $fecha_fin, $prioridad, $fase_proyecto, $estado, $solicitante);

    if ($stmt_insert_proyecto->execute()) {
        $id_proyecto = $conn->insert_id;

        // Insertar los usuarios seleccionados como gerente, senior y junior en la tabla equipo_trabajo
        $equipo_trabajo = [$gerente, $senior, $junior];
        foreach ($equipo_trabajo as $id_usuario) {
            $sql_equipo = "INSERT INTO equipo_trabajo (Idproyecto, IDusuario) VALUES (?, ?)";
            $stmt_equipo = $conn->prepare($sql_equipo);
            $stmt_equipo->bind_param("ii", $id_proyecto, $id_usuario);
            $stmt_equipo->execute();
        }

        // Registrar la acción en el log de auditoría
        $detalles = "Se agregó el proyecto '$descripcion' con ID $id_proyecto";
        registrarAuditoriaProyecto($conn, $id_proyecto, $detalles, $user_id);

        echo "Nuevo proyecto creado exitosamente";
        header("Location: proyectos_auditoria.php");
        exit();
    } else {
        echo "Error al agregar el proyecto: " . $stmt_insert_proyecto->error;
    }
    $stmt_insert_proyecto->close();
    $conn->close();
}

// Función para registrar acciones en la auditoría de proyectos
function registrarAuditoriaProyecto($con, $idProyecto, $detalles, $idUsuario) {
    $sql = "INSERT INTO auditoria_proyectos (Idproyecto, Detalles, FechaHora, IDusuario) VALUES (?, ?, current_timestamp(), ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('isi', $idProyecto, $detalles, $idUsuario);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Proyecto de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
        }
        .container {
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Agregar Proyecto de Auditoría</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" required>
        </div>
        <div class="form-group">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
        </div>
        <div class="form-group">
            <label for="fecha_fin">Fecha Fin</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
        </div>
        <div class="form-group">
            <label for="prioridad">Prioridad</label>
            <select class="form-control" id="prioridad" name="prioridad" required>
                <option value="Alta">Alta</option>
                <option value="Media">Media</option>
                <option value="Baja">Baja</option>
            </select>
        </div>
        <div class="form-group">
            <label for="gerente">Gerente</label>
            <select class="form-control" id="gerente" name="gerente" required>
                <?php
                if ($gerentes_result && $gerentes_result->num_rows > 0) {
                    while ($gerente = $gerentes_result->fetch_assoc()) {
                        echo "<option value='{$gerente['IDusuario']}'>{$gerente['NombreCompleto']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay gerentes disponibles</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="senior">Senior</label>
            <select class="form-control" id="senior" name="senior" required>
                <?php
                if ($seniores_result && $seniores_result->num_rows > 0) {
                    while ($senior = $seniores_result->fetch_assoc()) {
                        echo "<option value='{$senior['IDusuario']}'>{$senior['NombreCompleto']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay seniors disponibles</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="junior">Junior</label>
            <select class="form-control" id="junior" name="junior" required>
                <?php
                if ($juniors_result && $juniors_result->num_rows > 0) {
                    while ($junior = $juniors_result->fetch_assoc()) {
                        echo "<option value='{$junior['IDusuario']}'>{$junior['NombreCompleto']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay juniors disponibles</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fase_proyecto">Fase del Proyecto</label>
            <select class="form-control" id="fase_proyecto" name="fase_proyecto" required>
                <option value="Planificación">Planificación</option>
                <option value="Ejecución">Ejecución</option>
                <option value="Cierre">Cierre</option>
            </select>
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" id="estado" name="estado" required>
                <option value="Pendiente">Pendiente</option>
                <option value="En Progreso">En Progreso</option>
                <option value="Completado">Completado</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="creador_proyecto">Creador del Proyecto</label>
            <input type="text" class="form-control" id="creador_proyecto" name="creador_proyecto" value="<?php echo htmlspecialchars($solicitante); ?>" readonly>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Proyecto</button>
        <a href="proyectos_auditoria.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
