<?php
include 'funciones.php'; // Incluye tus funciones de conexión y otras utilidades

// Recibe los datos del formulario
$idEvento = $_POST['idEvento'];
$title = $_POST['title'];
$date = $_POST['date'];
$endDate = $_POST['endDate'];
$description = $_POST['description'];
$location = $_POST['location'];
$eventType = $_POST['eventType'];
$reminder = isset($_POST['reminder']) ? 1 : 0;
$color = $_POST['color'];

// Actualizar evento en la base de datos
$conn = conectarDB();
$sql = "UPDATE eventos SET Titulo=?, Fecha=?, Fecha_vencimiento=?, Descripcion=?, Lugar=?, Id_tipoevento=?, Recordatorio=?, Color=? WHERE Id_evento=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiiii", $title, $date, $endDate, $description, $location, $eventType, $reminder, $color, $idEvento);
if ($stmt->execute()) {
    $response = [
        'success' => true,
        'message' => 'Evento actualizado correctamente.'
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Error al actualizar el evento: ' . $stmt->error
    ];
}
$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
