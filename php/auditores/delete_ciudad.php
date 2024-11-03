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

    // Preparar la consulta SQL para eliminar la ciudad
    $sql = "DELETE FROM ciudades WHERE Idciudad = '$id_ciudad'";

    // Ejecutar la consulta
    if (mysqli_query($con, $sql)) {
        // Redirigir a ciudad.php
        header("Location: ciudad.php");
        exit;
    } else {
        echo "Error al eliminar la ciudad: " . mysqli_error($con);
    }

    // Cerrar la conexión
    mysqli_close($con);
} else {
    echo "ID de la ciudad no proporcionado.";
}
?>
