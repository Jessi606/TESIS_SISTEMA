<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Proyecto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 500px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #000;
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

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Eliminar Proyecto</h1>
    <?php
    // Incluir archivo de conexión
    include('conexion.php');
    
    session_start(); // Iniciar sesión si aún no está iniciada
    $conn = conectarDB();

    // Función para registrar la acción en el log de auditoría
    function registrarAuditoriaProyecto($conn, $id_proyecto, $accion, $detalles, $usuario_id) {
        $descripcion = $detalles['Descripcion'];
        $fecha_inicio = $detalles['Fecha_inicio'];
        $fecha_fin = $detalles['Fecha_fin'];
        $prioridad = $detalles['Prioridad'];
        $fase_proyecto = $detalles['Fase_proyecto'];
        $estado = $detalles['Estado'];
        $creador_proyecto = $detalles['Creador_proyecto'];
        
        $detalles_auditoria = "Descripción: $descripcion, Fecha inicio: $fecha_inicio, Fecha fin: $fecha_fin, Prioridad: $prioridad, Fase proyecto: $fase_proyecto, Estado: $estado, Creador proyecto: $creador_proyecto";
        
        $sql_insert_auditoria = "INSERT INTO auditoria_proyectos (Idproyecto, Accion, Detalles, FechaHora, IDusuario) VALUES ($id_proyecto, '$accion', '$detalles_auditoria', NOW(), $usuario_id)";

        if ($conn->query($sql_insert_auditoria) === TRUE) {
            return true;
        } else {
            echo "Error al registrar la auditoría: " . $conn->error;
            return false;
        }
    }

    // Verificar si se recibió correctamente el ID del proyecto a eliminar
    if (isset($_GET['id'])) {
        $id_proyecto = $_GET['id'];

        // Obtener los detalles del proyecto antes de eliminarlo para el registro de auditoría
        $sql_select_proyecto = "SELECT * FROM proyecto_auditoria WHERE Idproyecto = $id_proyecto";
        $result_proyecto = $conn->query($sql_select_proyecto);

        if ($result_proyecto->num_rows == 1) {
            $proyecto = $result_proyecto->fetch_assoc();
            $detalles_proyecto = [
                'Descripcion' => $proyecto['Descripcion'],
                'Fecha_inicio' => $proyecto['Fecha_inicio'],
                'Fecha_fin' => $proyecto['Fecha_fin'],
                'Prioridad' => $proyecto['Prioridad'],
                'Fase_proyecto' => $proyecto['Fase_proyecto'],
                'Estado' => $proyecto['Estado'],
                'Creador_proyecto' => $proyecto['Creador_proyecto']
            ];

            // Eliminar registros en equipo_trabajo relacionados con el proyecto
            $sql_delete_equipo = "DELETE FROM equipo_trabajo WHERE Idproyecto = $id_proyecto";
            if ($conn->query($sql_delete_equipo) === TRUE) {
                // Ahora eliminar el proyecto de auditoria
                $sql_delete_proyecto = "DELETE FROM proyecto_auditoria WHERE Idproyecto = $id_proyecto";
                if ($conn->query($sql_delete_proyecto) === TRUE) {
                    $accion = "Eliminación de Proyecto";
                    // Llamar a la función para registrar la auditoría del proyecto eliminado
                    if (registrarAuditoriaProyecto($conn, $id_proyecto, $accion, $detalles_proyecto, $_SESSION['usuario_id'])) {
                        echo "<div class='alert alert-success' role='alert'>
                                <strong>¡Proyecto eliminado correctamente!</strong>
                              </div>";
                    } else {
                        echo "<div class='alert alert-danger' role='alert'>
                                Error al registrar la auditoría del proyecto eliminado.
                              </div>";
                    }
                } else {
                    echo "<div class='alert alert-danger' role='alert'>
                            Error al eliminar el proyecto: " . $conn->error . "
                          </div>";
                }
            } else {
                echo "<div class='alert alert-danger' role='alert'>
                        Error al eliminar el equipo de trabajo asociado: " . $conn->error . "
                      </div>";
            }
        } else {
            echo "<div class='alert alert-warning' role='alert'>
                    No se encontró el proyecto a eliminar.
                  </div>";
        }

        $conn->close();
    } else {  
        echo "<div class='alert alert-warning' role='alert'>
                No se recibió correctamente el ID del proyecto a eliminar.
              </div>";
    }
    ?>
    <a href="proyectos_auditoria.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a Proyectos</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
