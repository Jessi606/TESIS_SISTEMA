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

// Consulta SQL para obtener las tareas con nombre de proyecto y nombre de responsable
$sql = "SELECT t.Idtarea, t.Descripcion, t.Fecha_inicio, t.Fecha_fin, t.Fecha_creacion, 
        t.Prioridad, t.Estado_tarea, u.Nombre AS NombreResponsable, p.Descripcion AS NombreProyecto 
        FROM tareas t 
        LEFT JOIN proyecto_auditoria p ON t.Idproyecto = p.Idproyecto 
        LEFT JOIN usuarios u ON t.Responsable = u.IDusuario";
$result = $conn->query($sql);

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
    $stmt_insert->bind_param("ssssssssi", $descripcion, $fecha_inicio, $fecha_fin, $fecha_creacion, $prioridad, $estado_tarea, $solicitante, $responsable_id, $proyecto_id);
    if ($stmt_insert->execute()) {
        $tarea_id = $stmt_insert->insert_id;
        $accion_auditoria = "AGREGAR TAREA";
        $detalles_auditoria = "Descripción: $descripcion, Fecha de inicio: $fecha_inicio, Fecha de fin: $fecha_fin, Prioridad: $prioridad, Estado: $estado_tarea, Responsable: $responsable, Proyecto: $proyecto_descripcion";
        registrarAuditoria($conn, $user_id, $accion_auditoria, $detalles_auditoria, $tarea_id);
        header("Location: tarea.php");
        exit;
    } else {
        echo "Error al agregar la tarea: " . $stmt_insert->error;
    }
}

// Manejar la acción de modificar tarea
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modificar_tarea'])) {
    $idtarea = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $prioridad = $_POST['prioridad'];
    $estado_tarea = $_POST['estado_tarea'];
    $responsable_id = $_POST['responsable'];
    $user_id = $_SESSION['usuario_id'];

    $sql_update = "UPDATE tareas SET Descripcion = ?, Fecha_inicio = ?, Fecha_fin = ?, Prioridad = ?, Estado_tarea = ?, Responsable = ? WHERE Idtarea = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssssi", $descripcion, $fecha_inicio, $fecha_fin, $prioridad, $estado_tarea, $responsable_id, $idtarea);

    if ($stmt_update->execute()) {
        $accion_auditoria = "MODIFICAR TAREA";
        $detalles_auditoria = "Descripción: $descripcion, Fecha de inicio: $fecha_inicio, Fecha de fin: $fecha_fin, Prioridad: $prioridad, Estado: $estado_tarea";
        registrarAuditoria($conn, $user_id, $accion_auditoria, $detalles_auditoria, $idtarea);
        header("Location: tarea.php");
        exit;
    } else {
        echo "Error al modificar la tarea: " . $stmt_update->error;
    }
}

// Manejar la acción de eliminar tarea
if (isset($_GET['eliminar_tarea'])) {
    $idtarea = $_GET['id'];
    $user_id = $_SESSION['usuario_id'];

    $sql_detalle = "SELECT * FROM tareas WHERE Idtarea = ?";
    $stmt_detalle = $conn->prepare($sql_detalle);
    $stmt_detalle->bind_param("i", $idtarea);
    $stmt_detalle->execute();
    $result_detalle = $stmt_detalle->get_result();
    $row_detalle = $result_detalle->fetch_assoc();
    $descripcion = $row_detalle['Descripcion'];

    $sql_delete = "DELETE FROM tareas WHERE Idtarea = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $idtarea);

    if ($stmt_delete->execute()) {
        $accion_auditoria = "ELIMINAR TAREA";
        $detalles_auditoria = "Descripción: $descripcion";
        registrarAuditoria($conn, $user_id, $accion_auditoria, $detalles_auditoria, $idtarea);
        header("Location: tarea.php");
        exit;
    } else {
        echo "Error al eliminar la tarea: " . $stmt_delete->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #a6bbd7; color: #333; }
        .container { max-width: 2200px; background-color: #fff; border-radius: 10px; padding: 20px; box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1); margin: auto; margin-top: 50px; }
        h1 { color: #000; text-align: center; margin-top: 20px; }
        .btn-primary { background-color: #007bff; border-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; border-color: #0056b3; }
        .table th { background-color: #343a40; color: #fff; }
        .table td { background-color: #f8f9fa; }
        .icon { font-size: 18px; margin-right: 5px; }
        .priority-high { color: #ff3333; }
        .priority-medium { color: #ffa64d; }
        .priority-low { color: #99cc00; }
        .status-in-progress { color: #ffcc00; }
        .status-completed { color: #00cc00; }
        .status-pending { color: #999999; }
        .btn-danger { background-color: #dc3545; border-color: #dc3545; color: #fff; }
        .btn-danger:hover { background-color: #c82333; border-color: #bd2130; color: #fff; }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Tareas de Auditoría</h1>
        <a href="agregar_tarea.php" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Agregar Tarea</a>
        <a href="registro_auditoria.php" class="btn btn-info mb-3"><i class="fas fa-file-alt"></i> Ver Registro de Auditoría</a>
        <a href="/TESIS_SISTEMA/manuales_usuario/Gestión de Auditoría-Tareas.pdf" target="_blank" class="btn btn-secondary"><i class="fas fa-question-circle"></i> Ayuda</a>
        <h2 class="mt-3">Tareas Registradas</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Fecha Creación</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Responsable</th>
                    <th>Proyecto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Iconos de prioridad y estado
                        $priority_icon = get_priority_icon($row['Prioridad']);
                        $status_icon = get_status_icon($row['Estado_tarea']);
                        echo "<tr>
                                <td>{$row['Idtarea']}</td>
                                <td>{$row['Descripcion']}</td>
                                <td>{$row['Fecha_inicio']}</td>
                                <td>{$row['Fecha_fin']}</td>
                                <td>{$row['Fecha_creacion']}</td>
                                <td>{$priority_icon} {$row['Prioridad']}</td>
                                <td>{$status_icon} {$row['Estado_tarea']}</td>
                                <td>{$row['NombreResponsable']}</td>
                                <td>{$row['NombreProyecto']}</td>
                                <td>
                                    <a href='editar_tarea.php?id={$row['Idtarea']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Editar</a>
                                    <a href='eliminar_tarea.php?id={$row['Idtarea']}' class='btn btn-danger btn-sm' onclick='return confirmarEliminacion()'><i class='fas fa-trash'></i> Eliminar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No hay tareas registradas</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
        <a href="admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
            function confirmarEliminacion() {
        return confirm('¿Estás seguro de que deseas eliminar esta tarea? Esta acción no se puede deshacer.');
    }
</script>
</body>
</html>
<?php
// Función para obtener el icono de prioridad
function get_priority_icon($prioridad) {
    switch ($prioridad) {
        case 'Alta':
            return '<i class="fas fa-exclamation-circle priority-high icon"></i>';
        case 'Media':
            return '<i class="fas fa-exclamation-circle priority-medium icon"></i>';
        case 'Baja':
            return '<i class="fas fa-exclamation-circle priority-low icon"></i>';
        default:
            return '';
    }
}

// Función para obtener el icono de estado de la tarea
function get_status_icon($estado_tarea) {
    switch ($estado_tarea) {
        case 'En Proceso':
            return '<i class="fas fa-circle-notch fa-spin status-in-progress icon"></i>';
        case 'Completada':
            return '<i class="fas fa-check status-completed icon"></i>';
        case 'Pendiente':
            return '<i class="far fa-clock status-pending icon"></i>';
        default:
            return '';
    }
}
?>
