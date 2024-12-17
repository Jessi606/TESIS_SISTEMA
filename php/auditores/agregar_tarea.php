<?php
session_start();
include('conexion.php');
$conn = conectarDB();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Función para registrar auditoría
function registrarAuditoria($conn, $usuario_id, $accion, $detalles, $tarea_id) {
    $sql_auditoria = "INSERT INTO auditoria_tarea (IDusuario, Accion, Detalles, FechaHora, Idtarea) 
                      VALUES (?, ?, ?, NOW(), ?)";
    $stmt_auditoria = $conn->prepare($sql_auditoria);
    $stmt_auditoria->bind_param("issi", $usuario_id, $accion, $detalles, $tarea_id);
    $stmt_auditoria->execute();
}

// Configurar la zona horaria a Asunción, Paraguay
date_default_timezone_set('America/Asuncion');

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

// Manejar la acción de agregar tarea
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_tarea'])) {
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
    $prioridad = isset($_POST['prioridad']) ? $_POST['prioridad'] : '';
    $estado_tarea = isset($_POST['estado_tarea']) ? $_POST['estado_tarea'] : '';
    $proyecto_id = isset($_POST['proyecto_id']) ? $_POST['proyecto_id'] : '';
    $responsable_id = isset($_POST['responsable']) ? $_POST['responsable'] : '';

    $sql_responsable = "SELECT Nombre FROM usuarios WHERE IDusuario = ?";
    $stmt_responsable = $conn->prepare($sql_responsable);
    $stmt_responsable->bind_param("i", $responsable_id);
    $stmt_responsable->execute();
    $result_responsable = $stmt_responsable->get_result();
    if ($result_responsable->num_rows > 0) {
        $row_responsable = $result_responsable->fetch_assoc();
        $responsable = $row_responsable['Nombre'];
    } else {
        $responsable = "Desconocido";
    }

    $sql_proyecto = "SELECT Descripcion FROM proyecto_auditoria WHERE Idproyecto = ?";
    $stmt_proyecto = $conn->prepare($sql_proyecto);
    $stmt_proyecto->bind_param("i", $proyecto_id);
    $stmt_proyecto->execute();
    $result_proyecto = $stmt_proyecto->get_result();
    if ($result_proyecto->num_rows > 0) {
        $row_proyecto = $result_proyecto->fetch_assoc();
        $proyecto_descripcion = $row_proyecto['Descripcion'];
    } else {
        $proyecto_descripcion = "Proyecto Desconocido";
    }

    $fecha_creacion = date('Y-m-d H:i:s');
    $sql_insert = "INSERT INTO tareas (Descripcion, Fecha_inicio, Fecha_fin, Fecha_creacion, Prioridad, Estado_tarea, Creador_tarea, Responsable, Idproyecto) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssssssi", $descripcion, $fecha_inicio, $fecha_fin, $fecha_creacion, $prioridad, $estado_tarea, $user_id, $responsable_id, $proyecto_id);
    
    if ($stmt_insert->execute()) {
        $tarea_id = $stmt_insert->insert_id;
        $accion_auditoria = "AGREGAR TAREA";
        $detalles_auditoria = "Descripción: $descripcion, Fecha de inicio: $fecha_inicio, Fecha de fin: $fecha_fin, Prioridad: $prioridad, Estado: $estado_tarea, Responsable: $responsable, Proyecto: $proyecto_descripcion";
        
        // Registrar la acción en el log de auditoría
        registrarAuditoria($conn, $user_id, $accion_auditoria, $detalles_auditoria, $tarea_id);
        
        header("Location: tarea.php");
        exit;
    } else {
        echo "Error al agregar la tarea: " . $stmt_insert->error;
    }
}

// Obtener la lista de proyectos de auditoría
$proyectos_sql = "SELECT Idproyecto, Descripcion FROM proyecto_auditoria";
$proyectos_result = $conn->query($proyectos_sql);
if (!$proyectos_result) {
    echo "Error al obtener la lista de proyectos: " . $conn->error;
    exit();
}

// Obtener la lista de usuarios auditores
$usuarios_auditores_sql = "SELECT IDusuario, Nombre FROM usuarios WHERE IDrol = 2";
$usuarios_auditores_result = $conn->query($usuarios_auditores_sql);
if (!$usuarios_auditores_result) {
    echo "Error al obtener la lista de usuarios auditores: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tarea de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #a6bbd7; }
        .container { max-width: 1500px; margin: auto; border-radius: 10px; margin-top: 50px; background-color: #fff; padding: 20px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .btn-primary { background-color: #007bff; border-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; border-color: #0056b3; }
        .alert { margin-top: 20px; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Agregar Tarea de Auditoría</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
            </div>
            <div class="form-group">
                <label for="prioridad">Prioridad:</label>
                <select class="form-control" id="prioridad" name="prioridad" required>
                    <option value="Alta">Alta</option>
                    <option value="Media">Media</option>
                    <option value="Baja">Baja</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado_tarea">Estado:</label>
                <select class="form-control" id="estado_tarea" name="estado_tarea" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="En Progreso">En Progreso</option>
                    <option value="Completado">Completado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>
            <div class="form-group">
                <label for="proyecto_id">Proyecto:</label>
                <select class="form-control" id="proyecto_id" name="proyecto_id" required>
                    <?php while ($row = $proyectos_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['Idproyecto']; ?>"><?php echo $row['Descripcion']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="responsable">Responsable:</label>
                <select class="form-control" id="responsable" name="responsable" required>
                    <?php while ($row = $usuarios_auditores_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['IDusuario']; ?>"><?php echo $row['Nombre']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="agregar_tarea"><i class="fas fa-plus"></i> Agregar Tarea</button>
            <a href="tarea.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
        </form>
    </div>
</body>
</html>
