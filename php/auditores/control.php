<?php
// Incluir el archivo de conexión
require_once 'conexion.php';

// Conectar a la base de datos
$conn = conectarDB();

// Consultas para obtener los datos necesarios

// Consultas para proyectos
$sql_proyectos_pendientes = "SELECT * FROM proyecto_auditoria WHERE Estado = 'Pendiente'";
$result_proyectos_pendientes = $conn->query($sql_proyectos_pendientes);
$totalProyectosPendientes = $result_proyectos_pendientes->num_rows;

$sql_proyectos_finalizados = "SELECT * FROM proyecto_auditoria WHERE Estado = 'Completado'";
$result_proyectos_finalizados = $conn->query($sql_proyectos_finalizados);
$totalProyectosFinalizados = $result_proyectos_finalizados->num_rows;

$sql_proyectos_en_proceso = "SELECT * FROM proyecto_auditoria WHERE Estado = 'En Progreso'";
$result_proyectos_en_proceso = $conn->query($sql_proyectos_en_proceso);
$totalProyectosEnProceso = $result_proyectos_en_proceso->num_rows;

$sql_proyectos_cancelados = "SELECT * FROM proyecto_auditoria WHERE Estado = 'Cancelado'";
$result_proyectos_cancelados = $conn->query($sql_proyectos_cancelados);
$totalProyectosCancelados = $result_proyectos_cancelados->num_rows;

// Consultas para tareas
$sql_tareas_pendientes = "SELECT * FROM tareas WHERE Estado_tarea = 'Pendiente'";
$result_tareas_pendientes = $conn->query($sql_tareas_pendientes);
$totalTareasPendientes = $result_tareas_pendientes->num_rows;

$sql_tareas_completadas = "SELECT * FROM tareas WHERE Estado_tarea = 'Completada'";
$result_tareas_completadas = $conn->query($sql_tareas_completadas);
$totalTareasCompletadas = $result_tareas_completadas->num_rows;

$sql_tareas_en_proceso = "SELECT * FROM tareas WHERE Estado_tarea = 'En proceso'";
$result_tareas_en_proceso = $conn->query($sql_tareas_en_proceso);
$totalTareasEnProceso = $result_tareas_en_proceso->num_rows;

// Consultas para requerimientos
$sql_requerimientos_vencidos = "SELECT Titulo, Fecha_vencimiento FROM requerimientos WHERE Estado_requerimiento = 'Vencido'";
$result_requerimientos_vencidos = $conn->query($sql_requerimientos_vencidos);
$totalRequerimientosVencidos = $result_requerimientos_vencidos->num_rows;

$sql_requerimientos_aceptados = "SELECT Titulo, Fecha_creacion FROM requerimientos WHERE Estado_requerimiento = 'Aceptado'";
$result_requerimientos_aceptados = $conn->query($sql_requerimientos_aceptados);
$totalRequerimientosAceptados = $result_requerimientos_aceptados->num_rows;

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Estilos CSS */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #a6bbd7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .card-custom {
            border-radius: 10px;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
        }
        .chart-container {
            text-align: center;
            margin-top: 20px;
        }
        canvas {
            max-width: 400px;
            margin: 0 auto;
            display: block;
        }
        .charts {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        /* Estilos personalizados para las tarjetas y gráficos */
        .bg-custom-danger {
            background-color: #FFD54F; /* Amarillo */
        }
        .bg-custom-info {
            background-color: #FF9999; /* Rojo */
        }
        .bg-custom-success {
            background-color: #A5D6A7; /* Verde */
        }
        .bg-custom-primary {
            background-color: #f7cae7; /* Rosa */
        }
        /* Estilos personalizados para los títulos */
        h2.display-4 {
            font-size: 2.5rem; /* Tamaño grande para títulos */
            font-family: 'Roboto', sans-serif; /* Fuente Roboto */
            font-weight: bold; /* Negrita */
            margin-bottom: 20px; /* Espacio inferior */
            text-align: left; /* Centrado */
            color: #333; /* Color de texto */
        }
        .container {
            max-width: 1200px; /* Ancho máximo del contenedor */
            margin: 20px auto; /* Margen superior e inferior de 20px y centrado horizontal */
            padding: 20px; /* Relleno interno de 20px */
            background-color: #f5f5f5; /* Fondo blanco */
            border-radius: 10px; /* Bordes redondeados */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra ligera */
        }
        h1.display-4 {
            font-size: 2.5rem; /* Tamaño grande para títulos */
            font-family: 'Roboto', sans-serif; /* Fuente Roboto */
            font-weight: bold; /* Negrita */
            margin-bottom: 20px; /* Espacio inferior */
            text-align: center; /* Centrado de texto */
            color: #333; /* Color de texto */
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="text-center mb-4">
            <h1 class="display-4">DASHBOARD DE CONTROL</h1>
        </header>
        <h2 class="display-4">Tarjetas de Detalle</h2>
        <div class="row">
            <!-- Columna de Tareas -->
            <div class="col-md-4">
                <div class="card card-custom bg-custom-info mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Tareas</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalTareasPendientes > 0) {
                                while($row = $result_tareas_pendientes->fetch_assoc()) {
                                    echo "<li>" . $row["Descripcion"] . " - Estado: " . $row["Estado_tarea"] . "</li>";
                                }
                            } else {
                                echo "<li>No hay tareas pendientes</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="card card-custom bg-custom-danger mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Tareas en Proceso</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalTareasEnProceso > 0) {
                                while($row = $result_tareas_en_proceso->fetch_assoc()) {
                                    echo "<li>" . $row["Descripcion"] . " - Estado: " . $row["Estado_tarea"] . "</li>";
                                }
                            } else {
                                echo "<li>No hay tareas en proceso</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="card card-custom bg-custom-success mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Tareas Completadas</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalTareasCompletadas > 0) {
                                while($row = $result_tareas_completadas->fetch_assoc()) {
                                    echo "<li>" . $row["Descripcion"] . " - Estado: " . $row["Estado_tarea"] . "</li>";
                                }
                            } else {
                                echo "<li>No hay tareas completadas</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Columna de Proyectos -->
            <div class="col-md-4">
                <div class="card card-custom bg-custom-info mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Proyectos Pendientes</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalProyectosPendientes > 0) {
                                while($row = $result_proyectos_pendientes->fetch_assoc()) {
                                    echo "<li>" . $row["Descripcion"] . " - Estado: " . $row["Estado"] . "</li>";
                                }
                            } else {
                                echo "<li>No hay proyectos pendientes</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="card card-custom bg-custom-danger mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Proyectos en Proceso</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalProyectosEnProceso > 0) {
                                while($row = $result_proyectos_en_proceso->fetch_assoc()) {
                                    echo "<li>" . $row["Descripcion"] . " - Estado: " . $row["Estado"] . "</li>";
                                }
                            } else {
                                echo "<li>No hay proyectos en proceso</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="card card-custom bg-custom-success mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Proyectos Completados</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalProyectosFinalizados > 0) {
                                while($row = $result_proyectos_finalizados->fetch_assoc()) {
                                    echo "<li>" . $row["Descripcion"] . " - Estado: " . $row["Estado"] . "</li>";
                                }
                            } else {
                                echo "<li>No hay proyectos completados</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="card card-custom bg-custom-primary mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Proyectos Cancelados</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalProyectosCancelados > 0) {
                                while($row = $result_proyectos_cancelados->fetch_assoc()) {
                                    echo "<li>" . $row["Descripcion"] . " - Estado: " . $row["Estado"] . "</li>";
                                }
                            } else {
                                echo "<li>No hay proyectos cancelados</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Columna de Requerimientos -->
            <div class="col-md-4">
                <div class="card card-custom bg-custom-info mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Requerimientos Vencidos</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalRequerimientosVencidos > 0) {
                                while($row = $result_requerimientos_vencidos->fetch_assoc()) {
                                    $fechaVencimiento = isset($row['Fecha_vencimiento']) ? $row['Fecha_vencimiento'] : 'Fecha no disponible';
                                    echo "<li>" . $row["Titulo"] . " - Vencido el: " . $fechaVencimiento . "</li>";
                                }
                            } else {
                                echo "<li>No hay requerimientos vencidos</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="card card-custom bg-custom-success mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Requerimientos Aceptados</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($totalRequerimientosAceptados > 0) {
                                while($row = $result_requerimientos_aceptados->fetch_assoc()) {
                                    $fechaCreacion = isset($row['Fecha_creacion']) ? $row['Fecha_creacion'] : 'Fecha no disponible';
                                    echo "<li>" . $row["Titulo"] . " - Aceptado el: " . $fechaCreacion . "</li>";
                                }
                            } else {
                                echo "<li>No hay requerimientos aceptados</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
                            
   
<!-- Sección de Gráficos -->
<h2 class="display-4">Gráficos</h2>
<div class="charts">
    
    <div class="chart-container">
        <h3>Distribución de Estados de Proyectos</h3>
        <canvas id="proyectosChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Distribución de Estados de Tareas</h3>
        <canvas id="tareasChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Distribución de Estados de Requerimientos</h3>
        <canvas id="requerimientosChart"></canvas>
    </div>
</div>

<!-- Botón de Volver -->
<a href="auditor.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
</div> <!-- Cierre del div container -->

   
    <script>
        // Datos para el gráfico de proyectos
        const proyectosData = {
            labels: ['Pendientes', 'En Progreso', 'Completados', 'Cancelados'],
            datasets: [{
                label: 'Proyectos',
                data: [<?php echo $totalProyectosPendientes; ?>, <?php echo $totalProyectosEnProceso; ?>, <?php echo $totalProyectosFinalizados; ?>, <?php echo $totalProyectosCancelados; ?>],
                backgroundColor: ['#FF9999', '#FDFD96', '#C9E2AE', '#FFB6C1'],
                borderColor: ['#FF9999', '#FDFD96', '#C9E2AE', '#FFB6C1'],
                borderWidth: 1
            }]
        };

        // Datos para el gráfico de tareas
        const tareasData = {
            labels: ['Pendientes', 'En proceso', 'Completadas'],
            datasets: [{
                label: 'Tareas',
                data: [<?php echo $totalTareasPendientes; ?>, <?php echo $totalTareasEnProceso; ?>, <?php echo $totalTareasCompletadas; ?>],
                backgroundColor: ['#FF9999', '#FDFD96', '#C9E2AE'],
                borderColor: ['#FF9999', '#FDFD96', '#C9E2AE'],
                borderWidth: 1
            }]
        };

        // Datos para el gráfico de requerimientos
        const requerimientosData = {
            labels: ['Vencidos', 'Aceptados'],
            datasets: [{
                label: 'Requerimientos',
                data: [<?php echo $totalRequerimientosVencidos; ?>, <?php echo $totalRequerimientosAceptados; ?>],
                backgroundColor: ['#FF9999', '#C9E2AE'],
                borderColor: ['#FF9999', '#C9E2AE'],
                borderWidth: 1
            }]
        };

        // Configuración del gráfico de proyectos
        const proyectosConfig = {
            type: 'pie',
            data: proyectosData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        
                    }
                }
            }
        };

        // Configuración del gráfico de tareas
        const tareasConfig = {
            type: 'pie',
            data: tareasData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        
                    }
                }
            }
        };

        // Configuración del gráfico de requerimientos
        const requerimientosConfig = {
            type: 'pie',
            data: requerimientosData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        
                    }
                }
            }
        };

        // Inicialización de los gráficos
        window.onload = function() {
            const ctxProyectos = document.getElementById('proyectosChart').getContext('2d');
            new Chart(ctxProyectos, proyectosConfig);

            const ctxTareas = document.getElementById('tareasChart').getContext('2d');
            new Chart(ctxTareas, tareasConfig);

            const ctxRequerimientos = document.getElementById('requerimientosChart').getContext('2d');
            new Chart(ctxRequerimientos, requerimientosConfig);
        };
    </script>
</body>
</html>

