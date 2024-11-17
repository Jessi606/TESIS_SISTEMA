<?php
// Incluir el archivo de conexión
include('conexion.php');

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener el ID de la ciudad desde la URL
$id_ciudad = isset($_GET['Idciudad']) ? $_GET['Idciudad'] : '';

if (!empty($id_ciudad)) {
    // Preparar la consulta para eliminar la ciudad
    $sql = "DELETE FROM ciudades WHERE Idciudad = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_ciudad);

    if ($stmt->execute()) {
        // Redirigir a la página de ciudades con el parámetro de éxito
        header("Location: /TESIS_SISTEMA/php/ciudad.php?status=eliminado");
        exit();
    } else {
        echo "Error al eliminar la ciudad: " . $con->error;
    }

    $stmt->close();
}

$con->close();
?>
