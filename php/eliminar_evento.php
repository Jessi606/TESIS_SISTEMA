<?php
// eliminar_evento.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Aquí deberías incluir tu lógica para conectar a la base de datos y eliminar el evento
    // Ejemplo básico de conexión a la base de datos
    include 'funciones.php'; // Archivo con la función conectarDB()

    $conn = conectarDB();
    
    // Recibir datos del evento a eliminar
    $data = json_decode(file_get_contents('php://input'), true);
    $eventId = $data['eventId'];

    // Ejemplo de consulta SQL para eliminar
    $sql = "DELETE FROM eventos WHERE Id_evento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $eventId);

    $response = array();
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Evento eliminado correctamente';
    } else {
        $response['success'] = false;
        $response['message'] = 'Error al eliminar el evento';
    }

    echo json_encode($response);

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405); // Método no permitido
    echo json_encode(array('message' => 'Método no permitido'));
}
?>
