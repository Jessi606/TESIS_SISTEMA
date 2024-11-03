<?php
include('conexion.php');
$conn = conectarDB();
$sql = "SELECT 
            p.Idproyecto, 
            p.Descripcion, 
            p.Fecha_inicio, 
            p.Fecha_fin, 
            p.Prioridad, 
            GROUP_CONCAT(a.Nombre SEPARATOR '<br>') as NombresAuditores,
            GROUP_CONCAT(a.NivelExperiencia SEPARATOR '<br>') as NivelesExperiencia,
            p.Fase_proyecto, 
            p.Estado, 
            p.Fecha_creacion,
            p.Creador_proyecto  /* Agregamos el campo Creador_proyecto */
        FROM 
            proyecto_auditoria p
            LEFT JOIN equipo_trabajo et ON p.Idproyecto = et.Idproyecto
            LEFT JOIN auditores a ON et.IDusuario = a.IDusuario
        GROUP BY p.Idproyecto";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Proyectos de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }

        .container {
            max-width: 1880px;
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

        /* Estilos de iconos de prioridad */
        .priority-high {
            color: #ff3333; /* Rojo */
        }

        .priority-medium {
            color: #ffa64d; /* Naranja */
        }

        .priority-low {
            color: #99cc00; /* Verde */
        }

        /* Estilos de iconos de fase del proyecto */
        .phase-planning {
            color: #3377ff; /* Azul */
        }

        .phase-execution {
            color: #ff9933; /* Naranja */
        }

        .phase-closure {
            color: #33cc33; /* Verde */
        }

        /* Estilos de iconos de estado del proyecto */
        .status-pending {
            color: #999999; /* Gris */
        }

        .status-progress {
            color: #ffcc00; /* Amarillo */
        }

        .status-completed {
            color: #00cc00; /* Verde */
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .status-cancelled {
            color: #dc3545; /* Rojo, por ejemplo */
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
    <h1 class="mt-5">Proyectos de Auditoría</h1>
    <a href="agregar_proyecto.php" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Agregar Proyecto</a>
    <a href="auditoria_proyectos.php" class="btn btn-info mb-3"><i class="fas fa-file-alt"></i> Ver Registro de Auditoría</a> <!-- Nuevo botón agregado -->
    <h2 class="mt-3">Proyectos Registrados</h2>
    <table class="table table-striped mt-3">
        <thead>
        <tr>
            <th>Código</th>
            <th>Descripción</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Prioridad</th>
            <th>Auditores asignados</th>
            <th>Fase del Proyecto</th>
            <th>Estado</th>
            <th>Fecha de Creación</th>
            <th>Creador</th>  <!-- Agregamos la columna para mostrar el creador -->
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Iconos de prioridad
                $priority_icon = '';
                switch ($row['Prioridad']) {
                    case 'Alta':
                        $priority_icon = '<i class="fas fa-exclamation-circle priority-high icon"></i>';
                        break;
                    case 'Media':
                        $priority_icon = '<i class="fas fa-exclamation-circle priority-medium icon"></i>';
                        break;
                    case 'Baja':
                        $priority_icon = '<i class="fas fa-exclamation-circle priority-low icon"></i>';
                        break;
                    default:
                        $priority_icon = '';
                }

                // Iconos de fase del proyecto
                $phase_icon = '';
                switch ($row['Fase_proyecto']) {
                    case 'Planificación':
                        $phase_icon = '<i class="fas fa-cogs phase-planning icon"></i>';
                        break;
                    case 'Ejecución':
                        $phase_icon = '<i class="fas fa-wrench phase-execution icon"></i>';
                        break;
                    case 'Cierre':
                        $phase_icon = '<i class="fas fa-check-circle phase-closure icon"></i>';
                        break;
                    default:
                        $phase_icon = '';
                }

                // Iconos de estado del proyecto
                $status_icon = '';
                switch ($row['Estado']) {
                    case 'Pendiente':
                        $status_icon = '<i class="far fa-clock status-pending icon"></i>';
                        break;
                    case 'En Progreso':
                        $status_icon = '<i class="fas fa-spinner status-progress icon"></i>';
                        break;
                    case 'Completado':
                        $status_icon = '<i class="fas fa-check status-completed icon"></i>';
                        break;
                    case 'Cancelado':
                        $status_icon = '<i class="fas fa-ban status-cancelled icon"></i>';
                        break;
                    default:
                        $status_icon = '';
                }
                

                // Mostrar los auditores y sus niveles de experiencia
                $auditores = explode('<br>', $row['NombresAuditores']);
                $niveles = explode('<br>', $row['NivelesExperiencia']);
                $auditores_info = '';
                for ($i = 0; $i < count($auditores); $i++) {
                    $auditores_info .= $auditores[$i] . ' (' . $niveles[$i] . ')';
                    if ($i < count($auditores) - 1) {
                        $auditores_info .= '<br>';
                    }
                }

                echo "<tr>
                        <td>{$row['Idproyecto']}</td>
                        <td>{$row['Descripcion']}</td>
                        <td>{$row['Fecha_inicio']}</td>
                        <td>{$row['Fecha_fin']}</td>
                        <td>{$priority_icon} {$row['Prioridad']}</td>
                        <td>{$auditores_info}</td>
                        <td>{$phase_icon} {$row['Fase_proyecto']}</td>
                        <td>{$status_icon} {$row['Estado']}</td>
                        <td>{$row['Fecha_creacion']}</td>
                        <td>{$row['Creador_proyecto']}</td>  <!-- Mostramos el creador -->
                        <td>
                            <a href='editar_proyecto.php?id={$row['Idproyecto']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Editar</a>
                            <a href='eliminar_proyecto.php?id={$row['Idproyecto']}' class='btn btn-danger btn-sm' onclick='return confirmarEliminacion()'><i class='fas fa-trash'></i> Eliminar</a>
                            <a href='ver_proyecto.php?id={$row['Idproyecto']}' class='btn btn-info btn-sm'><i class='fas fa-eye'></i> Ver Detalles</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No hay proyectos registrados</td></tr>";
        }
        $conn->close();
        ?>
        </tbody>
    </table>
    <a href="auditor.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function confirmarEliminacion() {
    return confirm('¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer.');
}
</script>
</body>
</html>
