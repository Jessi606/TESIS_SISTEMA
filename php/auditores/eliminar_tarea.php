<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php"); // Redireccionar a la página de inicio de sesión si no hay sesión activa
    exit();
}

// Función para registrar auditoría
function registrarAuditoria($conn, $usuario_id, $accion, $detalles) {
    if ($usuario_id !== null) {
        $sql_auditoria = "INSERT INTO auditoria (IDusuario, Accion, Detalles, FechaHora) VALUES (?, ?, ?, NOW())";
        $stmt_auditoria = $conn->prepare($sql_auditoria);
        $stmt_auditoria->bind_param("iss", $usuario_id, $accion, $detalles);
        $stmt_auditoria->execute();
    } else {
        echo "Error: ID de usuario es nulo";
    }
}

// Incluir el archivo de conexión a la base de datos
require_once("conexion.php");
$conn = conectarDB();

// Obtener el ID del usuario que ha iniciado sesión
$user_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

// Verificar que $user_id no sea null antes de usarlo
if ($user_id !== null) {
    // Manejar la acción de eliminar tarea si se ha proporcionado un ID de tarea
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Eliminar']) && isset($_POST['id'])) {
        $id_tarea = $_POST['id'];

        // Obtener detalles de la tarea antes de eliminarla para registrar en la auditoría
        $sqlDetalles = "SELECT * FROM tareas WHERE Idtarea = ?";
        $stmtDetalles = $conn->prepare($sqlDetalles);
        $stmtDetalles->bind_param("i", $id_tarea);
        $stmtDetalles->execute();
        $resultDetalles = $stmtDetalles->get_result();

        if ($resultDetalles->num_rows > 0) {
            $tarea = $resultDetalles->fetch_assoc();
            
            // Construir los detalles de la tarea eliminada
            $detallesTarea = "ID: " . $tarea['Idtarea'] . 
                             ", Descripción: " . $tarea['Descripcion'] . 
                             ", Fecha de inicio: " . $tarea['Fecha_inicio'] . 
                             ", Fecha de fin: " . $tarea['Fecha_fin'] . 
                             ", Prioridad: " . $tarea['Prioridad'] . 
                             ", Estado: " . $tarea['Estado_tarea'] . 
                             ", Creador: " . $tarea['Creador_tarea'];

            // Utilizar consulta preparada para eliminar la tarea
            $sql_delete = "DELETE FROM tareas WHERE Idtarea = ?";
            $stmt = $conn->prepare($sql_delete);
            $stmt->bind_param("i", $id_tarea);

            if ($stmt->execute()) {
                // Registrar acción de auditoría
                registrarAuditoria($conn, $user_id, 'Eliminar', "Detalles completos de la tarea eliminada: [$detallesTarea]");

                // Redireccionar a la página de tareas después de eliminar la tarea
                header("Location: tarea.php");
                exit;
            } else {
                echo "Error al eliminar la tarea: " . $stmt->error;
            }
        } else {
            echo "La tarea con ID $id_tarea no existe.";
        }

        $stmtDetalles->close();
    }

    // Consulta SQL para obtener los datos de las tareas con la descripción del proyecto y el nombre del responsable
    $sql = "SELECT t.*, u.Nombre AS NombreResponsable, p.Descripcion AS DescripcionProyecto
            FROM tareas t
            INNER JOIN usuarios u ON t.Responsable = u.IDusuario
            LEFT JOIN proyecto_auditoria p ON t.IdProyecto = p.Idproyecto";
    $result = $conn->query($sql);
} else {
    echo "Error: Usuario no identificado o sesión expirada.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Tarea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            padding-top: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        .form-group {
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
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Eliminar Tarea</h2>
        <div class="alert alert-danger" role="alert">
            ¿Estás seguro de que deseas eliminar esta tarea?
        </div>
        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
            <button type="submit" name="Eliminar" class="btn btn-danger">Eliminar</button>
            <a href="tarea.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
        </form>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
