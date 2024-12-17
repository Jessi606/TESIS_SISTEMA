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

// Anular tarea
if (isset($_GET['anular_tarea'])) {
    $id_tarea = $_GET['anular_tarea'];
    $estadoAnulado = "Anulada";

    // Obtener la descripción de la tarea antes de anularla
    $stmtNombre = $conn->prepare("SELECT Descripcion FROM tareas WHERE Idtarea = ?");
    $stmtNombre->bind_param('i', $id_tarea);
    $stmtNombre->execute();
    $resultNombre = $stmtNombre->get_result();
    $descripcionTarea = $resultNombre->fetch_assoc()['Descripcion'];

    // Actualizar el estado de la tarea a "Anulada"
    $stmt = $conn->prepare("UPDATE tareas SET Estado_tarea = ? WHERE Idtarea = ?");
    $stmt->bind_param('si', $estadoAnulado, $id_tarea);
    
    if ($stmt->execute()) {
        // Registrar en la auditoría con el nombre de la tarea
        $usuario_id = $_SESSION['usuario_id'];
        registrarAuditoria($conn, $usuario_id, "ANULAR TAREA", "Se anuló la tarea '$descripcionTarea'", $id_tarea);
        
        header("Location: tarea.php?success=anulada");
        exit();
    }
}

// Consulta SQL para obtener las tareas y los datos del proyecto asociado
$sql = "SELECT t.Idtarea, t.Descripcion, t.Fecha_inicio, t.Fecha_fin, t.Fecha_creacion, 
        t.Prioridad, t.Estado_tarea, t.Creador_tarea, u.Nombre AS NombreResponsable, 
        p.Descripcion AS NombreProyecto, u2.Nombre AS NombreCreador 
        FROM tareas t 
        LEFT JOIN proyecto_auditoria p ON t.Idproyecto = p.Idproyecto 
        LEFT JOIN usuarios u ON t.Responsable = u.IDusuario 
        LEFT JOIN usuarios u2 ON t.Creador_tarea = u2.IDusuario";
$result = $conn->query($sql);

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
        .status-anulada { color: #dc3545; }
        .btn-anular { background-color: #f8d7da; color: #dc3545; border: 1px solid #f5c6cb; }
        .btn-anular:disabled { background-color: #d6d8db; color: #6c757d; border: 1px solid #c4c8cc; cursor: not-allowed; }
        .row-anulada { background-color: #c4c8cc !important; color: #5a5c5e; }
        .button-group { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Tareas de Auditoría</h1>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'anulada'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>¡Éxito!</strong> La tarea fue anulada correctamente.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="button-group">
            <a href="agregar_tarea.php" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Tarea</a>
            <a href="registro_auditoria.php" class="btn btn-info"><i class="fas fa-file-alt"></i> Ver Registro de Auditoría</a>
            <a href="/TESIS_SISTEMA/Manuales de usuario/Gestión de Auditoría_tareas_actualizado.pdf" target="_blank" class="btn btn-secondary"><i class="fas fa-question-circle"></i> Ayuda</a>
        </div>
        <h2 class="mt-3">Tareas Registradas</h2>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Fecha Creación</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Creador</th>
                    <th>Responsable</th>
                    <th>Proyecto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $priority_icon = get_priority_icon($row['Prioridad']);
                        $status_icon = get_status_icon($row['Estado_tarea']);
                        $row_class = $row['Estado_tarea'] === 'Anulada' ? 'row-anulada' : '';

                        // Botones de acción
                        $acciones = $row['Estado_tarea'] === 'Anulada' ? 
                            "<button class='btn btn-anular btn-sm' disabled><i class='fas fa-ban'></i> Anular</button>" : 
                            "<a href='editar_tarea.php?id={$row['Idtarea']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Editar</a>
                             <a href='?anular_tarea={$row['Idtarea']}' class='btn btn-anular btn-sm' onclick='return confirmarAnulacion()'><i class='fas fa-ban'></i> Anular</a>";

                        echo "<tr class='$row_class'>
                                <td>{$row['Idtarea']}</td>
                                <td>{$row['Descripcion']}</td>
                                <td>{$row['Fecha_inicio']}</td>
                                <td>{$row['Fecha_fin']}</td>
                                <td>{$row['Fecha_creacion']}</td>
                                <td>{$priority_icon} {$row['Prioridad']}</td>
                                <td>{$status_icon} {$row['Estado_tarea']}</td>
                                <td>{$row['NombreCreador']}</td>
                                <td>{$row['NombreResponsable']}</td>
                                <td>{$row['NombreProyecto']}</td>
                                <td>$acciones</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No hay tareas registradas</td></tr>";
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
        function confirmarAnulacion() {
            return confirm('¿Estás seguro de que deseas anular esta tarea? Esta acción no se puede deshacer.');
        }
    </script>
</body>
</html>

<?php
// Funciones para los iconos
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

function get_status_icon($estado_tarea) {
    switch ($estado_tarea) {
        case 'En Proceso':
            return '<i class="fas fa-circle-notch fa-spin status-in-progress icon"></i>';
        case 'Completada':
            return '<i class="fas fa-check status-completed icon"></i>';
        case 'Pendiente':
            return '<i class="far fa-clock status-pending icon"></i>';
        case 'Anulada':
            return '<i class="fas fa-ban status-anulada icon"></i>';
        default:
            return '';
    }
}
?>
