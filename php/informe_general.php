<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe General - Auditoría</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/css/bootstrap.min.css">
    <style>
        /* Estilos para el informe */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 20px;
        }
        
        .container {
            max-width: 800px; /* Ancho máximo para el contenido */
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #006064;
            margin-bottom: 10px;
        }
        
        .data-list {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Informe General - Auditoría</div>

        <div class="section">
            <div class="section-title">Tareas por Auditor y Proyecto</div>
            <div class="data-list">
                <?php
                include('conexion.php'); // Incluir archivo de conexión

                $conn = conectarDB(); // Establecer conexión

                if ($conn->connect_error) {
                    die("Error en la conexión: " . $conn->connect_error);
                }

                $conn->set_charset("utf8"); // Establecer codificación UTF-8

                // Consulta para obtener la cantidad de tareas por auditor y proyecto
                $sql_tareas_auditor = "SELECT 
                                        a.Nombre as Auditor,
                                        p.Descripcion as Proyecto,
                                        COUNT(t.IDtarea) as TotalTareas
                                    FROM 
                                        auditores a
                                        LEFT JOIN equipo_trabajo et ON a.IDusuario = et.IDusuario
                                        LEFT JOIN tareas t ON et.Idproyecto = t.Idproyecto AND t.Responsable = a.Nombre
                                        LEFT JOIN proyecto_auditoria p ON t.Idproyecto = p.Idproyecto
                                    WHERE t.IDtarea IS NOT NULL
                                    GROUP BY a.Nombre, p.Descripcion
                                    ORDER BY a.Nombre, p.Descripcion";

                $result_tareas_auditor = $conn->query($sql_tareas_auditor);

                if ($result_tareas_auditor->num_rows > 0) {
                    $current_auditor = null;
                    while ($row_tareas_auditor = $result_tareas_auditor->fetch_assoc()) {
                        $auditor = $row_tareas_auditor['Auditor'];
                        $proyecto = $row_tareas_auditor['Proyecto'];
                        $total_tareas = $row_tareas_auditor['TotalTareas'];

                        // Mostrar el nombre del auditor solo la primera vez que aparece
                        if ($auditor != $current_auditor) {
                            echo "<b>Auditor:</b> $auditor <br>";
                            $current_auditor = $auditor;
                        }

                        // Mostrar la información de la tarea
                        echo "<b>Proyecto:</b> $proyecto <br>";
                        echo "<b>Total de Tareas Asignadas:</b> $total_tareas <br>";
                        echo "<br>";
                    }
                } else {
                    echo "No se encontraron datos de tareas asignadas por auditor.";
                }

                // Cerrar conexión
                $conn->close();
                ?>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Tareas por Estado</div>
            <div class="data-list">
                <?php
                $conn = conectarDB(); // Establecer conexión nuevamente

                // Consulta para obtener la cantidad de tareas por estado
                $sql_tareas_estado = "SELECT 
                                        Estado_tarea,
                                        COUNT(*) as Cantidad
                                    FROM 
                                        tareas
                                    GROUP BY Estado_tarea";

                $result_tareas_estado = $conn->query($sql_tareas_estado);

                if ($result_tareas_estado->num_rows > 0) {
                    while ($row_tareas_estado = $result_tareas_estado->fetch_assoc()) {
                        $estado_tarea = $row_tareas_estado['Estado_tarea'];
                        $cantidad_tareas = $row_tareas_estado['Cantidad'];

                        echo "<b>Estado de la Tarea:</b> $estado_tarea <br>";
                        echo "<b>Cantidad de Tareas:</b> $cantidad_tareas <br>";
                        echo "<br>";
                    }
                } else {
                    echo "No se encontraron datos de tareas.";
                }

                // Cerrar conexión
                $conn->close();
                ?>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Proyectos por Estado</div>
            <div class="data-list">
                <?php
                $conn = conectarDB(); // Establecer conexión nuevamente

                // Consulta para obtener la cantidad de proyectos por estado
                $sql_proyectos = "SELECT 
                                        Estado,
                                        COUNT(*) as Cantidad
                                    FROM 
                                        proyecto_auditoria
                                    GROUP BY Estado";

                $result_proyectos = $conn->query($sql_proyectos);

                if ($result_proyectos->num_rows > 0) {
                    while ($row_proyectos = $result_proyectos->fetch_assoc()) {
                        $estado_proyecto = $row_proyectos['Estado'];
                        $cantidad_proyectos = $row_proyectos['Cantidad'];

                        echo "<b>Estado del Proyecto:</b> $estado_proyecto <br>";
                        echo "<b>Cantidad de Proyectos:</b> $cantidad_proyectos <br>";
                        echo "<br>";
                    }
                } else {
                    echo "No se encontraron datos de proyectos.";
                }

                // Cerrar conexión
                $conn->close();
                ?>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Requerimientos por Estado</div>
            <div class="data-list">
                <?php
                $conn = conectarDB(); // Establecer conexión nuevamente

                // Consulta para obtener la cantidad de requerimientos por estado
                $sql_requerimientos = "SELECT 
                                            Estado_requerimiento,
                                            COUNT(*) as Cantidad
                                        FROM 
                                            requerimientos
                                        GROUP BY Estado_requerimiento";

                $result_requerimientos = $conn->query($sql_requerimientos);

                if ($result_requerimientos->num_rows > 0) {
                    while ($row_requerimientos = $result_requerimientos->fetch_assoc()) {
                        $estado_requerimiento = $row_requerimientos['Estado_requerimiento'];
                        $cantidad_requerimientos = $row_requerimientos['Cantidad'];

                        echo "<b>Estado del Requerimiento:</b> $estado_requerimiento <br>";
                        echo "<b>Cantidad de Requerimientos:</b> $cantidad_requerimientos <br>";
                        echo "<br>";
                    }
                } else {
                    echo "No se encontraron datos de requerimientos.";
                }

                // Cerrar conexión
                $conn->close();
                ?>
            </div>
        </div>
    </div>
</body>
</html>
