<?php
// Incluir el archivo de conexión
include('conexion.php');

// Verificar si se proporcionó un ID de rol
if (isset($_GET['Idrol'])) {
    $idRol = intval($_GET['Idrol']);

    // Conectar a la base de datos
    $con = conectarDB();

    // Verificar la conexión
    if (!$con) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }

    // Verificar si el rol es uno de los predeterminados del sistema (ID <= 3)
    if ($idRol <= 3) {
        // Redirigir con un mensaje de error si se intenta anular un rol predeterminado
        header("Location: roles.php?error=predeterminado");
        exit();
    }

    // Actualizar el estado del rol a "anulado"
    $sql = "UPDATE roles SET estado = 'anulado' WHERE Idrol = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $idRol);

    if ($stmt->execute()) {
        // Redirigir a la página de roles con un mensaje de éxito
        header("Location: roles.php?success=1");
    } else {
        // Redirigir con un mensaje de error si la anulación falla
        header("Location: roles.php?error=1");
    }

    $stmt->close();
    $con->close();
} else {
    // Redirigir si no se proporciona un ID de rol
    header("Location: roles.php");
    exit();
}
