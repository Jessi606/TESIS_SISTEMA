<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se ha enviado un ID de auditor para eliminar
if(isset($_GET['id']) && !empty($_GET['id'])) {
    // Obtener el ID del auditor a eliminar
    $id_auditor = $_GET['id'];

    // Consulta SQL para eliminar el auditor
    $sql = "DELETE FROM auditores WHERE Idauditor = $id_auditor";

    if ($conn->query($sql) === TRUE) {
        // Redireccionar de vuelta a la página principal con un mensaje de éxito
        header("Location: auditores.php?success=1");
        exit();
    } else {
        echo "Error al eliminar el auditor: " . $conn->error;
    }
} else {
    // Si no se proporciona un ID de auditor, redireccionar a la página principal
    header("Location: auditores.php");
    exit();
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
