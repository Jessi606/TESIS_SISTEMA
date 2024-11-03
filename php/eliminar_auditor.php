<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se ha pasado el ID del auditor y que sea un número válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Obtener el ID del auditor a eliminar
    $Idauditor = (int) $_GET['id'];

    // Preparar la consulta SQL para eliminar el auditor
    $sql = "DELETE FROM auditores WHERE Idauditor = ?";

    // Preparar la declaración
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param('i', $Idauditor);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Si la eliminación es exitosa, redirigir con el parámetro success
            header("Location: auditores.php?success=1");
            exit(); // Detener la ejecución del script
        } else {
            // Si hay un error, redirigir con el parámetro error
            header("Location: auditores.php?error=1");
            exit(); // Detener la ejecución del script
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        // Si hay un error al preparar la declaración, redirigir con el parámetro error
        header("Location: auditores.php?error=1");
        exit(); // Detener la ejecución del script
    }
} else {
    // Si el ID no está definido o no es válido, redirigir con el parámetro error
    header("Location: auditores.php?error=1");
    exit(); // Detener la ejecución del script
}

// Cerrar la conexión
$conn->close();
?>
