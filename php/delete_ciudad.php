<?php
// Incluir el archivo de conexión
include('conexion.php');

// Verificar si se recibió el ID de la ciudad a eliminar
if(isset($_GET['Idciudad'])) {
    // Obtener el ID de la ciudad desde la URL
    $id_ciudad = $_GET['Idciudad'];

    // Conectar a la base de datos
    $con = conectarDB();

    // Verificar la conexión
    if (!$con) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }

    // Preparar la consulta SQL para eliminar la ciudad de forma segura
    $stmt = $con->prepare("DELETE FROM ciudades WHERE Idciudad = ?");
    $stmt->bind_param("i", $id_ciudad); // "i" indica que el parámetro es un entero

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir a ciudad.php con un parámetro que indique éxito
        header("Location: ciudad.php?status=eliminado");
        exit;
    } else {
        echo "Error al eliminar la ciudad: " . $stmt->error;
    }

    // Cerrar la consulta y la conexión
    $stmt->close();
    mysqli_close($con);
} else {
    echo "ID de la ciudad no proporcionado.";
}
?>
