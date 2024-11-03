<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se ha enviado un ID de cliente para eliminar
if(isset($_GET['id']) && !empty($_GET['id'])) {
    // Obtener el ID del cliente a eliminar
    $id_cliente = $_GET['id'];

    // Consulta SQL para eliminar el cliente
    $sql = "DELETE FROM clientes WHERE Idcliente = $id_cliente";

    if ($conn->query($sql) === TRUE) {
        // Redireccionar de vuelta a la página principal con un mensaje de éxito
        header("Location: clientes.php?success=1");
        exit();
    } else {
        echo "Error al eliminar el cliente: " . $conn->error;
    }
} else {
    // Si no se proporciona un ID de cliente, redireccionar a la página principal
    header("Location: clientes.php");
    exit();
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
