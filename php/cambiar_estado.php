<?php
include 'conexion.php';

$con = conectarDB();
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

if (isset($_GET['IDusuario']) && isset($_GET['estado'])) {
    $IDusuario = $_GET['IDusuario'];
    $nuevoEstado = $_GET['estado'];

    // Cambiar el estado del usuario
    $sql = "UPDATE usuarios SET Estado = ? WHERE IDusuario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $nuevoEstado, $IDusuario);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: usuarios.php?success=1");
        exit();
    } else {
        echo "Error al cambiar el estado del usuario: " . mysqli_error($con);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($con);
?>
