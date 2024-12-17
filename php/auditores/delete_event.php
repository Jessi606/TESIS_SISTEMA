<?php
session_start();
include('conexion.php');

// Verificar si se recibió el id_evento
if (isset($_POST['id_evento'])) {
    // Obtener el id_evento enviado por AJAX
    $id_evento = $_POST['id_evento'];

    // Conectar a la base de datos
    $conn = conectarDB();

    // Preparar la consulta para eliminar el evento
    $sql_delete_evento = "DELETE FROM eventos WHERE Id_evento = ?";
    $stmt_delete_evento = $conn->prepare($sql_delete_evento);
    $stmt_delete_evento->bind_param("i", $id_evento);

    // Ejecutar la consulta
    if ($stmt_delete_evento->execute()) {
        echo "Evento eliminado correctamente";
    } else {
        echo "Error al eliminar el evento: " . $conn->error;
    }

    // Cerrar la conexión
    $stmt_delete_evento->close();
    $conn->close();
} else {
    echo "ID de evento no proporcionado";
}
?>
