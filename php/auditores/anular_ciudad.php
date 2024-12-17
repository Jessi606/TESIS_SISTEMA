<?php
include('conexion.php');
$con = conectarDB();

if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

$id_ciudad = isset($_GET['Idciudad']) ? $_GET['Idciudad'] : '';

if (!empty($id_ciudad)) {
    $sql = "UPDATE ciudades SET estado = 'anulado' WHERE Idciudad = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_ciudad);

    if ($stmt->execute()) {
        header("Location: ciudad.php?status=anulado");
        exit();
    } else {
        echo "Error al anular la ciudad: " . $con->error;
    }

    $stmt->close();
}

$con->close();
?>
