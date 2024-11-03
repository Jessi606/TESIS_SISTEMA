<?php
// Incluir archivo de conexión a la base de datos
include('conexion.php');
$conn = conectarDB();

// Función para obtener la lista de evidencias de un requerimiento
function obtenerEvidenciasRequerimiento($conn, $id_requerimiento) {
    $sql_evidencias = "SELECT Evidencia FROM evidencias WHERE Idrequerimiento = '$id_requerimiento'";
    $result_evidencias = $conn->query($sql_evidencias);
    $evidencias = [];

    if ($result_evidencias->num_rows > 0) {
        while ($row_evidencia = $result_evidencias->fetch_assoc()) {
            $evidencias[] = $row_evidencia['Evidencia'];
        }
    }

    return $evidencias;
}

// Verificar si se recibió el parámetro 'id' en la URL
if (isset($_GET['id'])) {
    // Obtener y sanitizar el ID del proyecto
    $id_proyecto = $conn->real_escape_string($_GET['id']);

    // Consulta para obtener los detalles del proyecto
    $sql_proyecto = "SELECT 
                        p.Idproyecto, 
                        p.Descripcion AS ProyectoDescripcion, 
                        p.Fecha_inicio, 
                        p.Fecha_fin, 
                        p.Prioridad, 
                        p.Fase_proyecto, 
                        p.Estado, 
                        p.Fecha_creacion,
                        GROUP_CONCAT(a.Nombre SEPARATOR ', ') AS NombresAuditores,
                        GROUP_CONCAT(a.NivelExperiencia SEPARATOR ', ') AS NivelesExperiencia
                    FROM 
                        proyecto_auditoria p
                        LEFT JOIN equipo_trabajo et ON p.Idproyecto = et.Idproyecto
                        LEFT JOIN auditores a ON et.IDusuario = a.IDusuario
                    WHERE 
                        p.Idproyecto = '$id_proyecto'
                    GROUP BY 
                        p.Idproyecto";

    $result_proyecto = $conn->query($sql_proyecto);

    if ($result_proyecto->num_rows > 0) {
        $row_proyecto = $result_proyecto->fetch_assoc();

        // Consulta para obtener los requerimientos del proyecto
        $sql_requerimientos = "SELECT r.*, GROUP_CONCAT(e.Evidencia SEPARATOR ', ') AS Evidencias
                                FROM requerimientos r
                                LEFT JOIN evidencias e ON r.Idrequerimiento = e.Idrequerimiento
                                WHERE r.Idproyecto = '$id_proyecto'
                                GROUP BY r.Idrequerimiento";
        $result_requerimientos = $conn->query($sql_requerimientos);

        // Consulta para obtener las tareas del proyecto
        $sql_tareas = "SELECT * FROM tareas WHERE Idproyecto = '$id_proyecto'";
        $result_tareas = $conn->query($sql_tareas);
    } else {
        // Si no se encuentra el proyecto, redirigir o mostrar un mensaje de error
        header("Location: index.php"); // Cambiar a la página principal o una página de error según corresponda
        exit();
    }
} else {
    // Si no se proporciona el parámetro 'id', redirigir o mostrar un mensaje de error
    header("Location: index.php"); // Cambiar a la página principal o una página de error según corresponda
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Proyecto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }
        .card {
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
        }

        .card-header {
            background-color: #ededaf; /* Color de fondo suave */
            color: #333; /* Color de texto oscuro */
            font-weight: bold;
        }

        .card-body {
            padding: 1.25rem;
        }

        .evidencia-link {
            display: block;
            margin-bottom: 5px;
        }
        .container {
        /* Estilos generales del contenedor */
        width: 80%; /* Ancho del contenedor */
        max-width: 1200px; /* Ancho máximo del contenedor */
        margin: 0 auto; /* Centrar el contenedor en la página */
        padding: 20px; /* Espacio interno del contenedor */
        background-color: #556e83; /* Color de fondo del contenedor */
        border: 1px solid #ccc; /* Borde del contenedor */
        border-radius: 8px; /* Radio de borde para esquinas redondeadas */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra */
    }

    .mt-4 {
        /* Estilos para la clase de margen top 4 */
        margin-top: 40px; /* Ajustar margen superior */
    }

    /* Estilos adicionales específicos para elementos dentro del contenedor */
    .container h2 {
        color: #333; /* Color de texto para los títulos */
        font-size: 24px; /* Tamaño de fuente para los títulos */
        margin-bottom: 15px; /* Margen inferior para los títulos */
    }

    .container p {
        line-height: 1.6; /* Altura de línea del texto */
    }
    h1.mb-4 {
        font-size: 32px; /* Tamaño de fuente */
        color: #FFFFFF; /* Color de texto */
        margin-bottom: 20px; /* Margen inferior */
        text-align: center; /* Alinear texto al centro */
        text-transform: uppercase; /* Convertir texto a mayúsculas */
        border-bottom: 2px solid #ccc; /* Línea inferior */
        padding-bottom: 10px; /* Espacio debajo de la línea */
    }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Detalles del Proyecto</h1>

        <div class="card">
            <div class="card-header">
                Detalles Generales del Proyecto
            </div>
            <div class="card-body">
                <p><strong>Descripción:</strong> <?php echo $row_proyecto['ProyectoDescripcion']; ?></p>
                <p><strong>Fecha de Inicio:</strong> <?php echo $row_proyecto['Fecha_inicio']; ?></p>
                <p><strong>Fecha de Fin:</strong> <?php echo $row_proyecto['Fecha_fin']; ?></p>
                <p><strong>Prioridad:</strong> <?php echo $row_proyecto['Prioridad']; ?></p>
                <p><strong>Fase del Proyecto:</strong> <?php echo $row_proyecto['Fase_proyecto']; ?></p>
                <p><strong>Estado:</strong> <?php echo $row_proyecto['Estado']; ?></p>
                <p><strong>Fecha de Creación:</strong> <?php echo $row_proyecto['Fecha_creacion']; ?></p>
                <p><strong>Auditores asignados:</strong> <?php echo $row_proyecto['NombresAuditores']; ?></p>
                <p><strong>Niveles de experiencia:</strong> <?php echo $row_proyecto['NivelesExperiencia']; ?></p>
            </div>
        </div>

        <?php if ($result_requerimientos->num_rows > 0): ?>
                <div class="card">
                    <div class="card-header">
                        Requerimientos del Proyecto
                    </div>
                    <div class="card-body">
                        <?php while ($row_req = $result_requerimientos->fetch_assoc()): ?>
                            <div class="card">
                                <div class="card-header">
                                    <?php echo $row_req['Titulo']; ?>
                                </div>
                                <div class="card-body">
                                    <p><strong>Descripción:</strong> <?php echo $row_req['Descripcion']; ?></p>
                                    <p><strong>Solicitante:</strong> <?php echo $row_req['Solicitante']; ?></p>
                                    <p><strong>Fecha de Creación:</strong> <?php echo $row_req['Fecha_creacion']; ?></p>
                                    <p><strong>Fecha de Vencimiento:</strong> <?php echo $row_req['Fecha_vencimiento']; ?></p>
                                    <p><strong>Estado del Requerimiento:</strong> <?php echo $row_req['Estado_requerimiento']; ?></p>
                                    <p><strong>Remitente:</strong> <?php echo $row_req['Remitente']; ?></p>
                                    <p><strong>Comentario:</strong> <?php echo $row_req['Comentario']; ?></p>
                                    <?php if (!empty($row_req['Evidencias'])): ?>
                                        <p><strong>Evidencias:</strong><br>
                                            <?php 
                                            $evidencias = explode(', ', $row_req['Evidencias']);
                                            foreach ($evidencias as $evidencia) {
                                                echo "<a href='archivos/{$evidencia}' class='evidencia-link' target='_blank'>$evidencia</a>";
                                            }
                                            ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php if ($result_tareas->num_rows > 0): ?>
            <div class="card">
                <div class="card-header">
                    Tareas del Proyecto
                </div>
                <div class="card-body">
                    <ul>
                        <?php while ($row_tarea = $result_tareas->fetch_assoc()): ?>
                            <li><strong><?php echo $row_tarea['Descripcion']; ?>:</strong> <?php echo $row_tarea['Estado_tarea']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <a href="proyectos_auditoria.php" class="btn btn-primary mt-4"><i class="fas fa-arrow-left"></i> Volvera la página anterior</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos al finalizar
$conn->close();
?>
