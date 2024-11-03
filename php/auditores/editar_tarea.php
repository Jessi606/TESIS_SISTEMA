<?php
session_start();
require_once("conexion.php");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Función para registrar auditoría
function registrarAuditoria($conn, $usuario_id, $accion, $detalles) {
    $sql_auditoria = "INSERT INTO auditoria (IDusuario, Accion, Detalles) VALUES (?, ?, ?)";
    $stmt_auditoria = $conn->prepare($sql_auditoria);
    $stmt_auditoria->bind_param("iss", $usuario_id, $accion, $detalles);
    $stmt_auditoria->execute();
}

$conn = conectarDB();

// Obtener el nombre del usuario que ha iniciado sesión
$user_id = $_SESSION['usuario_id'];
$sql_usuario = "SELECT u.Nombre, r.Descripcion AS Rol
                FROM usuarios u
                LEFT JOIN roles r ON u.Idrol = r.Idrol
                WHERE u.IDusuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $user_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows > 0) {
    $user = $result_usuario->fetch_assoc();
    $solicitante = $user['Nombre'];
} else {
    $solicitante = "";
}

// Variables para almacenar los valores de la tarea a editar
$id_tarea = $descripcion = $fecha_inicio = $fecha_fin = $prioridad = $estado_tarea = $responsable = $proyecto_id = '';

// Verificar si se ha proporcionado un ID de tarea a editar
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_tarea = $_GET['id'];

    // Consultar la información de la tarea a editar utilizando consulta preparada
    $sql_select = "SELECT t.*, p.Descripcion AS ProyectoDescripcion, u.Nombre AS ResponsableNombre
                   FROM tareas t 
                   LEFT JOIN proyecto_auditoria p ON t.Idproyecto = p.Idproyecto
                   LEFT JOIN usuarios u ON t.Responsable = u.IDusuario
                   WHERE t.Idtarea = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("i", $id_tarea);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Asignar valores a las variables
        $id_tarea = $row['Idtarea'];
        $descripcion = $row['Descripcion'];
        $fecha_inicio = $row['Fecha_inicio'];
        $fecha_fin = $row['Fecha_fin'];
        $prioridad = $row['Prioridad'];
        $estado_tarea = $row['Estado_tarea'];
        $responsable = $row['Responsable'];
        $proyecto_id = $row['Idproyecto'];
    } else {
        echo "No se encontró la tarea a editar.";
        exit;
    }
}

// Manejar la acción de editar tarea si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_tarea'])) {
    // Obtener los valores del formulario
    $id_tarea = $_POST['id_tarea'];
    $descripcion_nueva = $_POST['descripcion'];
    $fecha_inicio_nueva = $_POST['fecha_inicio'];
    $fecha_fin_nueva = $_POST['fecha_fin'];
    $prioridad_nueva = $_POST['prioridad'];
    $estado_tarea_nueva = $_POST['estado_tarea'];
    $responsable_nuevo = $_POST['responsable'];
    $proyecto_id_nuevo = $_POST['proyecto_id'];

    // Obtener los valores originales de la tarea antes de la actualización
    $sql_select_original = "SELECT t.*, p.Descripcion AS ProyectoDescripcion, u.Nombre AS ResponsableNombre
                            FROM tareas t 
                            LEFT JOIN proyecto_auditoria p ON t.Idproyecto = p.Idproyecto
                            LEFT JOIN usuarios u ON t.Responsable = u.IDusuario
                            WHERE t.Idtarea = ?";
    $stmt_original = $conn->prepare($sql_select_original);
    $stmt_original->bind_param("i", $id_tarea);
    $stmt_original->execute();
    $result_original = $stmt_original->get_result();

    if ($result_original->num_rows == 1) {
        $row_original = $result_original->fetch_assoc();
        // Guardar los valores originales de la tarea
        $descripcion_original = $row_original['Descripcion'];
        $fecha_inicio_original = $row_original['Fecha_inicio'];
        $fecha_fin_original = $row_original['Fecha_fin'];
        $prioridad_original = $row_original['Prioridad'];
        $estado_tarea_original = $row_original['Estado_tarea'];
        $responsable_original_nombre = $row_original['ResponsableNombre'];
        $proyecto_descripcion_original = $row_original['ProyectoDescripcion'];
        $proyecto_id_original = $row_original['Idproyecto'];
    } else {
        echo "No se encontró la tarea original.";
        exit;
    }

    // Actualizar la tarea en la base de datos utilizando consulta preparada
    $sql_update = "UPDATE tareas SET Descripcion = ?, Fecha_inicio = ?, 
                   Fecha_fin = ?, Prioridad = ?, Estado_tarea = ?,
                   Responsable = ?, Idproyecto = ?
                   WHERE Idtarea = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssssssii", $descripcion_nueva, $fecha_inicio_nueva, $fecha_fin_nueva, $prioridad_nueva, $estado_tarea_nueva, $responsable_nuevo, $proyecto_id_nuevo, $id_tarea);

    if ($stmt->execute()) {
        // Obtener el nombre del responsable y la descripción del proyecto para el log de auditoría
        $sql_responsable = "SELECT Nombre FROM usuarios WHERE IDusuario = ?";
        $stmt_responsable = $conn->prepare($sql_responsable);
        $stmt_responsable->bind_param("i", $responsable_nuevo);
        $stmt_responsable->execute();
        $result_responsable = $stmt_responsable->get_result();
        $responsable_nombre = ($result_responsable->num_rows > 0) ? $result_responsable->fetch_assoc()['Nombre'] : "";

        $sql_proyecto = "SELECT Descripcion FROM proyecto_auditoria WHERE Idproyecto = ?";
        $stmt_proyecto = $conn->prepare($sql_proyecto);
        $stmt_proyecto->bind_param("i", $proyecto_id_nuevo);
        $stmt_proyecto->execute();
        $result_proyecto = $stmt_proyecto->get_result();
        $proyecto_descripcion = ($result_proyecto->num_rows > 0) ? $result_proyecto->fetch_assoc()['Descripcion'] : "";

        // Construir los detalles de auditoría con nombres y descripciones
        $detalles_auditoria = "Antes: [Descripción: $descripcion_original, Fecha de inicio: $fecha_inicio_original, Fecha de fin: $fecha_fin_original, Prioridad: $prioridad_original, Estado: $estado_tarea_original, Responsable: $responsable_original_nombre, Proyecto: $proyecto_descripcion_original]. " .
                              "Ahora: [Descripción: $descripcion_nueva, Fecha de inicio: $fecha_inicio_nueva, Fecha de fin: $fecha_fin_nueva, Prioridad: $prioridad_nueva, Estado: $estado_tarea_nueva, Responsable: $responsable_nombre, Proyecto: $proyecto_descripcion]";

        // Registrar la acción de auditoría
        registrarAuditoria($conn, $user_id, "Editar tarea de auditoría", $detalles_auditoria);

        // Redireccionar a la página principal después de editar la tarea
        header("Location: tarea.php");
        exit;
    } else {
        echo "Error al editar la tarea: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #a6bbd7;
    }
    .container {
        max-width: 1500px;
        margin: auto;
        border-radius: 10px;
        margin-top: 50px;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
    }
    h1 {
        text-align: center;
        color: #333;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Tarea de Auditoría</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="id_tarea" value="<?php echo $id_tarea; ?>">
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $fecha_fin; ?>" required>
            </div>
            <div class="form-group">
                <label for="prioridad">Prioridad:</label>
                <select class="form-control" id="prioridad" name="prioridad" required>
                    <option value="Baja" <?php if ($prioridad == 'Baja') echo 'selected'; ?>>Baja</option>
                    <option value="Media" <?php if ($prioridad == 'Media') echo 'selected'; ?>>Media</option>
                    <option value="Alta" <?php if ($prioridad == 'Alta') echo 'selected'; ?>>Alta</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado_tarea">Estado:</label>
                <select class="form-control" id="estado_tarea" name="estado_tarea" required>
                    <option value="Pendiente" <?php if ($estado_tarea == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                    <option value="En Progreso" <?php if ($estado_tarea == 'En Progreso') echo 'selected'; ?>>En Progreso</option>
                    <option value="Completado" <?php if ($estado_tarea == 'Completado') echo 'selected'; ?>>Completado</option>
                    <option value="Cancelado" <?php if ($estado_tarea == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
                </select>
            </div>
            <div class="form-group">
                <label for="responsable">Responsable:</label>
                <select class="form-control" id="responsable" name="responsable" required>
                    <?php
                    // Consulta para obtener solo los usuarios con rol de auditor
                    $sql_auditores = "SELECT IDusuario, Nombre FROM usuarios WHERE Idrol = (SELECT Idrol FROM roles WHERE Descripcion = 'Auditor')";
                    $result_auditores = $conn->query($sql_auditores);
                    
                    if ($result_auditores->num_rows > 0) {
                        while ($row_auditores = $result_auditores->fetch_assoc()) {
                            $selected = ($row_auditores['IDusuario'] == $responsable) ? 'selected' : '';
                            echo "<option value='{$row_auditores['IDusuario']}' $selected>{$row_auditores['Nombre']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="proyecto_id">Proyecto:</label>
                <select class="form-control" id="proyecto_id" name="proyecto_id" required>
                    <?php
                    $sql_proyectos = "SELECT Idproyecto, Descripcion FROM proyecto_auditoria ORDER BY Descripcion";
                    $result_proyectos = $conn->query($sql_proyectos);
                    if ($result_proyectos->num_rows > 0) {
                        while ($row_proyecto = $result_proyectos->fetch_assoc()) {
                            $selected = ($row_proyecto['Idproyecto'] == $proyecto_id) ? "selected" : "";
                            echo "<option value='" . $row_proyecto['Idproyecto'] . "' $selected>" . $row_proyecto['Descripcion'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="editar_tarea"><i class="fas fa-edit"></i> Editar Tarea</button>
            <a href="tarea.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </form>
    </div>
</body>
</html>
