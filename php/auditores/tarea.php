<?php
session_start();
include('conexion.php');
$conn = conectarDB();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Consulta SQL para obtener las tareas con nombre de proyecto y nombre de responsable
$sql = "SELECT 
            t.Idtarea, 
            t.Descripcion, 
            t.Fecha_inicio, 
            t.Fecha_fin, 
            t.Fecha_creacion,  
            t.Prioridad, 
            t.Estado_tarea, 
            u.Nombre AS NombreResponsable,
            p.Descripcion AS NombreProyecto  
        FROM 
            tareas t
            LEFT JOIN proyecto_auditoria p ON t.Idproyecto = p.Idproyecto
            LEFT JOIN usuarios u ON t.Responsable = u.IDusuario"; // Unir la tabla usuarios para obtener el nombre del responsable

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
        body {
            background-color: #a6bbd7;
            color: #333;
        }

        .container {
            max-width: 2200px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            margin: auto;
            margin-top: 50px;
        }

        h1 {
            color: #000;
            text-align: center;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .table th {
            background-color: #343a40;
            color: #fff;
        }

        .table td {
            background-color: #f8f9fa;
        }

        .icon {
            font-size: 18px;
            margin-right: 5px;
        }

        .priority-high {
            color: #ff3333;
        }

        .priority-medium {
            color: #ffa64d;
        }

        .priority-low {
            color: #99cc00;
        }

        .status-in-progress {
            color: #ffcc00;
        }

        .status-completed {
            color: #00cc00;
        }

        .status-pending {
            color: #999999;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Tareas de Auditoría</h1>
    <a href="agregar_tarea.php" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Agregar Tarea</a>
    <a href="registro_auditoria.php" class="btn btn-info mb-3"><i class="fas fa-file-alt"></i> Ver Registro de Auditoría</a>
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
        case 'En Progreso':
            return '<i class="fas fa-spinner status-in-progress icon"></i>';
        case 'Completado':
            return '<i class="fas fa-check status-completed icon"></i>';
        case 'Pendiente':
            return '<i class="far fa-clock status-pending icon"></i>';
        case 'Cancelado':
            return '<i class="fas fa-times-circle status-cancelled icon"></i>';
        default:
            return '';
    }
}

?>
