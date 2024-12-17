<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se recibe el ID del cliente para anular
if (isset($_GET['id'])) {
    $idCliente = $_GET['id'];

    // Actualizar el estado del cliente a 'anulado'
    $sql = "UPDATE clientes SET estado = 'anulado' WHERE Idcliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idCliente);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: clientes.php?success=1");
        exit();
    } else {
        // Redirigir con mensaje de error
        header("Location: clientes.php?error=1");
        exit();
    }

    $stmt->close();
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
