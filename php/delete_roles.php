<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener el ID del auditor desde la URL
if (isset($_GET['Idauditor'])) {
    $Idauditor = $_GET['Idauditor'];

    // Consulta SQL para eliminar el auditor
    $sql = "DELETE FROM auditores WHERE Idauditor = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $Idauditor);

    if (mysqli_stmt_execute($stmt)) {
        // Redirigir a la página de auditores con el mensaje de éxito
        header("Location: lista_auditores.php?success=1");
        exit();
    } else {
        echo "Error al eliminar el auditor: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Cerrar la conexión
mysqli_close($conn);
?>
