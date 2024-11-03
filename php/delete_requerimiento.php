<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("conexion.php");
$conn = conectarDB();

// Función para registrar auditoría de requerimientos
function registrarAuditoriaRequerimiento($conn, $usuario_id, $accion, $detalles) {
    try {
        // Preparar la consulta para insertar auditoría de requerimiento
        $sql_auditoria = "INSERT INTO auditoria_requerimientos (IDusuario, Accion, Detalles, FechaHora) VALUES (?, ?, ?, NOW())";
        $stmt_auditoria = $conn->prepare($sql_auditoria);
        
        if (!$stmt_auditoria) {
            throw new Exception("Error al preparar la consulta de auditoría de requerimiento: " . $conn->error);
        }
        
        $stmt_auditoria->bind_param("iss", $usuario_id, $accion, $detalles);

        // Ejecutar la consulta preparada
        if ($stmt_auditoria->execute()) {
            return true;
        } else {
            throw new Exception("Error al ejecutar la consulta de auditoría de requerimiento: " . $stmt_auditoria->error);
        }
    } catch (Exception $e) {
        echo "Error al registrar auditoría de requerimiento: " . $e->getMessage();
        return false;
    }
}

// Función para obtener el nombre del solicitante
function obtenerNombreSolicitante($conn, $usuario_id) {
    $sql = "SELECT Nombre FROM usuarios WHERE IDusuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        return $usuario['Nombre'];
    } else {
        return "Desconocido";
    }
}

// Verificar si se recibió el ID del requerimiento a eliminar
if (isset($_GET['Idrequerimiento'])) {
    $id = $_GET['Idrequerimiento'];

    // Preparar la consulta SQL para obtener los detalles del requerimiento antes de eliminarlo
    $sqlDetalles = "SELECT r.*, p.Descripcion AS DescripcionProyecto
                    FROM requerimientos r
                    LEFT JOIN proyecto_auditoria p ON r.Idproyecto = p.Idproyecto
                    WHERE r.Idrequerimiento=?";
    $stmtDetalles = $conn->prepare($sqlDetalles);
    $stmtDetalles->bind_param("i", $id);
    $stmtDetalles->execute();
    $resultDetalles = $stmtDetalles->get_result();

    if ($resultDetalles->num_rows > 0) {
        $requerimiento = $resultDetalles->fetch_assoc();
        
        // Obtener el nombre del solicitante
        $solicitante = obtenerNombreSolicitante($conn, $_SESSION['usuario_id']);
        
        // Construir los detalles del requerimiento eliminado
        $detallesRequerimiento = "ID: " . $requerimiento['Idrequerimiento'] . 
                                 ", Título: " . $requerimiento['Titulo'] . 
                                 ", Descripción: " . $requerimiento['Descripcion'] . 
                                 ", Remitente: " . $requerimiento['Remitente'] . 
                                 ", Fecha de Vencimiento: " . $requerimiento['Fecha_vencimiento'] . 
                                 ", Proyecto: " . $requerimiento['DescripcionProyecto'] .
                                 ", Solicitante: " . $solicitante;

        // Preparar la consulta SQL para eliminar el requerimiento
        $sqlEliminar = "DELETE FROM requerimientos WHERE Idrequerimiento=?";
        $stmtEliminar = $conn->prepare($sqlEliminar);
        $stmtEliminar->bind_param("i", $id);

        // Ejecutar la consulta para eliminar el requerimiento
        if ($stmtEliminar->execute()) {
            // Registrar acción de auditoría de requerimiento eliminado con todos los detalles
            $accion = "Eliminar requerimiento";
            $detalles = "Detalles completos del requerimiento eliminado: [$detallesRequerimiento]";
            registrarAuditoriaRequerimiento($conn, $_SESSION['usuario_id'], $accion, $detalles);

            // Redireccionar a la página de requerimientos después de la eliminación
            header("Location: requerimiento.php");
            exit;
        } else {
            echo "Error al eliminar el requerimiento: " . $stmtEliminar->error;
        }
    } else {
        echo "El requerimiento con ID $id no existe.";
    }

    $stmtDetalles->close();
} else {
    // Manejar el caso donde no se recibió el parámetro Idrequerimiento
    echo "No se proporcionó el parámetro Idrequerimiento para eliminar.";
}


$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Requerimiento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            padding-top: 20px; /* Ajuste para mejorar visualización en dispositivos móviles */
        }
        .container {
            max-width: 800px; /* Reducido para mejor legibilidad y estructura */
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
            margin-bottom: 30px; /* Espacio adicional debajo del título */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Eliminar Requerimiento</h2>
        <div class="alert alert-danger" role="alert">
            ¿Estás seguro de que deseas eliminar este requerimiento?
        </div>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="requerimiento.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
        </form>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
