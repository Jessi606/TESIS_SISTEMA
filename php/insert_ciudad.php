<?php
// Incluir el archivo de conexión
include('conexion.php');

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el nombre de la ciudad del formulario
    $nombre_ciudad = $_POST['Nombre'];

    // Conectar a la base de datos
    $con = conectarDB();

    // Verificar la conexión
    if (!$con) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }

    // Preparar la consulta SQL para insertar la ciudad
    $sql = "INSERT INTO ciudades (Nombre) VALUES ('$nombre_ciudad')";

    // Ejecutar la consulta
    if (mysqli_query($con, $sql)) {
        // Redirigir a ciudad.php
        header("Location: ciudad.php");
        exit;
    } else {
        echo "Error al insertar la ciudad: " . mysqli_error($con);
    }

    // Cerrar la conexión
    mysqli_close($con);
}
?>
    