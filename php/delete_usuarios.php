<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener el ID del usuario desde la URL
if (isset($_GET['IDusuario'])) {
    $IDusuario = $_GET['IDusuario'];

    // Consulta SQL para eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE IDusuario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $IDusuario);

    if (mysqli_stmt_execute($stmt)) {
        // Redirigir a la página principal con el mensaje de éxito
        header("Location: usuarios.php?success=1");
        exit();
    } else {
        echo "Error al eliminar el usuario: " . mysqli_error($con);
    }

    mysqli_stmt_close($stmt);
}

// Cerrar la conexión
mysqli_close($con);
?>
