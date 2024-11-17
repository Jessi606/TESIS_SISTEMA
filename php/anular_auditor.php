<?php
include('conexion.php');

$conn = conectarDB();

if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Obtener el ID del auditor desde la URL
$id_auditor = isset($_GET['id']) ? $_GET['id'] : '';

if (!empty($id_auditor)) {
    // Actualizar el estado del auditor a 'anulado'
    $sql = "UPDATE auditores SET estado = 'anulado' WHERE Idauditor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_auditor);

    if ($stmt->execute()) {
        header("Location: auditores.php?success=anulado");
        exit();
    } else {
        header("Location: auditores.php?error=1");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
    