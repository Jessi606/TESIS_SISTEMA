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
            .btn-danger {
                background-color: #dc3545;
                border-color: #dc3545;
            }
            .btn-danger:hover {
                background-color: #c82333;
                border-color: #bd2130;
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

        // Verificar si se ha recibido un ID de proyecto
        if (isset($_GET['id'])) {
            $id_proyecto = intval($_GET['id']);
            $usuario_id = $_SESSION['usuario_id']; // Asegúrate de que tienes el ID del usuario en la sesión

            // Consultar el proyecto a eliminar
            $sql = "SELECT Descripcion FROM proyecto_auditoria WHERE Idproyecto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id_proyecto);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $proyecto = $result->fetch_assoc();
                $descripcion_proyecto = $proyecto['Descripcion'];

                // Eliminar el proyecto
                $sql_delete = "DELETE FROM proyecto_auditoria WHERE Idproyecto = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param('i', $id_proyecto);

                if ($stmt_delete->execute()) {
                    // Registrar acción en el log de auditoría
                    registrarAuditoriaProyectoEliminado($conn, $id_proyecto, $usuario_id, $descripcion_proyecto);
                    echo "<div class='alert alert-success'>El proyecto '{$descripcion_proyecto}' ha sido eliminado exitosamente.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al eliminar el proyecto. Por favor, inténtelo de nuevo.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>No se encontró el proyecto.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>ID de proyecto no especificado.</div>";
        }

        // Función para registrar la acción en el log de auditoría
        function registrarAuditoriaProyectoEliminado($conn, $id_proyecto, $usuario_id, $descripcion_proyecto) {
            $detalles_auditoria = "El proyecto '{$descripcion_proyecto}' con Idproyecto {$id_proyecto} ha sido eliminado.";

            $sql_insert_auditoria = "INSERT INTO auditoria_proyectos (Idproyecto, Detalles, FechaHora, IDusuario) VALUES (?, ?, NOW(), ?)";
            $stmt = $conn->prepare($sql_insert_auditoria);
            $stmt->bind_param('isi', $id_proyecto, $detalles_auditoria, $usuario_id);

            if (!$stmt->execute()) {
                echo "Error al registrar la acción en la auditoría: " . $stmt->error;
            }
        }
        ?>
        <a href="proyectos.php" class="btn btn-primary mt-3">Volver a la lista de proyectos</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
