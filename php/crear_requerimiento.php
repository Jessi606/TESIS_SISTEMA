<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("conexion.php");
$conn = conectarDB();

// Obtener el nombre del usuario que ha iniciado sesión
$user_id = $_SESSION['usuario_id'];
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

// Obtener los remitentes con rol de cliente
$sqlRemitentes = "SELECT Nombre FROM usuarios WHERE Idrol = 3"; // 3 representa el rol de cliente
$resultRemitentes = $conn->query($sqlRemitentes);

// Obtener la lista de proyectos
$sqlProyectos = "SELECT Idproyecto, Descripcion FROM proyecto_auditoria";
$resultProyectos = $conn->query($sqlProyectos);

// Función para obtener la descripción del proyecto por ID
function obtenerDescripcionProyecto($conn, $idProyecto) {
    $sql = "SELECT Descripcion FROM proyecto_auditoria WHERE Idproyecto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idProyecto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $proyecto = $result->fetch_assoc();
        return $proyecto['Descripcion'];
    } else {
        return "Proyecto no encontrado";
    }
}

// Función para registrar auditoría de requerimientos
function registrarAuditoriaRequerimiento($conn, $usuario_id, $accion, $detalles, $idRequerimiento) {
    try {
        // Preparar la consulta para insertar auditoría de requerimiento
        $sql_auditoria = "INSERT INTO auditoria_requerimientos (IDusuario, Accion, Detalles, FechaHora, IdRequerimiento) VALUES (?, ?, ?, NOW(), ?)";
        $stmt_auditoria = $conn->prepare($sql_auditoria);
        
        if (!$stmt_auditoria) {
            throw new Exception("Error al preparar la consulta de auditoría de requerimiento: " . $conn->error);
        }
        
        $stmt_auditoria->bind_param("isss", $usuario_id, $accion, $detalles, $idRequerimiento);

        // Ejecutar la consulta preparada
        if ($stmt_auditoria->execute()) {
            return true;
        } else {
            throw new Exception("Error al ejecutar la consulta de auditoría de requerimiento: " . $stmt_auditoria->error);
        }
    } catch (Exception $e) {
        echo "Error al registrar auditoría de requerimiento: " . $e->getMessage();
        return false;
    }
}

// Verificar si el formulario ha sido enviado y procesarlo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $remitente = $_POST['remitente'];
    $fecha_creacion = date("Y-m-d");
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $proyecto = $_POST['proyecto'];
    
    // Validar datos del formulario (ejemplo básico)
    if (empty($titulo) || empty($descripcion) || empty($remitente) || empty($fecha_vencimiento) || empty($proyecto)) {
        echo "Por favor complete todos los campos.";
        exit();
    }

    // Verificar si la fecha de vencimiento ha sido superada
    if (date("Y-m-d") > $fecha_vencimiento) {
        $estado_requerimiento = "Vencido";
    } else {
        $estado_requerimiento = "Enviado";
    }

    // Insertar el requerimiento en la base de datos
    $stmt = $conn->prepare("INSERT INTO requerimientos (Titulo, Descripcion, Solicitante, Fecha_creacion, Fecha_vencimiento, Estado_requerimiento, Remitente, Idproyecto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $titulo, $descripcion, $solicitante, $fecha_creacion, $fecha_vencimiento, $estado_requerimiento, $remitente, $proyecto);

    if ($stmt->execute()) {
        // Obtener el ID del requerimiento creado
        $idRequerimiento = $stmt->insert_id;

        // Obtener la descripción del proyecto
        $descripcionProyecto = obtenerDescripcionProyecto($conn, $proyecto);

        // Registrar acción de auditoría de requerimiento
        $accion = "Crear requerimiento";
        $detalles = "Título: $titulo, Descripción: $descripcion, Solicitante: $solicitante, Fecha de Creación: $fecha_creacion, Fecha de Vencimiento: $fecha_vencimiento, Remitente: $remitente, Proyecto: $descripcionProyecto";
        registrarAuditoriaRequerimiento($conn, $_SESSION['usuario_id'], $accion, $detalles, $idRequerimiento);
        
        header("Location: requerimiento.php?success=1");
        exit();
    } else {
        echo "Error al crear el requerimiento: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Requerimiento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            padding-top: 20px; /* Ajuste para mejorar visualización en dispositivos móviles */
        }
        .container {
            max-width: 800px; /* Reducido para mejor legibilidad y estructura */
            margin: auto;
            border-radius: 10px;
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
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px; /* Espacio adicional debajo del título */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Crear Requerimiento</h2>
        <form method="post" action="crear_requerimiento.php">
            <div class="form-group">
                <label for="titulo">Título del Requerimiento:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="solicitante">Solicitante:</label>
                <input type="text" class="form-control" id="solicitante" name="solicitante" value="<?php echo htmlspecialchars($solicitante); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="remitente">Remitente:</label>
                <select class="form-control" id="remitente" name="remitente" required>
                    <?php while($row = $resultRemitentes->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['Nombre']); ?>"><?php echo htmlspecialchars($row['Nombre']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_creacion">Fecha de Creación:</label>
                <input type="text" class="form-control" id="fecha_creacion" name="fecha_creacion" value="<?php echo date("Y-m-d"); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
            </div>
            <div class="form-group">
                <label for="proyecto">Proyecto:</label>
                <select class="form-control" id="proyecto" name="proyecto" required>
                    <?php while($rowProyecto = $resultProyectos->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($rowProyecto['Idproyecto']); ?>"><?php echo htmlspecialchars($rowProyecto['Descripcion']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Crear Requerimiento</button>
            </div>
        </form>
        <a href="requerimiento.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver a la lista de requerimientos</a>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
