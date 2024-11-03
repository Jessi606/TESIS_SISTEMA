<?php
session_start();
include('conexion.php');

// Conectar a la base de datos
$conn = conectarDB();

// Verificar si se solicitan detalles de un evento específico
if (isset($_GET['start']) && isset($_GET['end'])) {
    $start = $_GET['start'];
    $end = $_GET['end'];

    // Consulta para obtener eventos dentro del rango de fechas seleccionado
    $sql = "SELECT ID_evento, Nombre, Fecha_evento, Fecha_vencimiento, Color FROM eventos WHERE Fecha_evento >= '$start' AND Fecha_evento <= '$end'";
} else {
    // Consultar todos los eventos si no se especifica un rango de fechas
    $sql = "SELECT ID_evento, Nombre, Fecha_evento, Fecha_vencimiento, Color FROM eventos";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error al obtener eventos: " . mysqli_error($conn));
}

// Array para almacenar los eventos en formato JSON
$events = array();

// Iterar sobre los resultados y construir el array de eventos
while ($row = mysqli_fetch_assoc($result)) {
    $event = array();
    $event['id'] = $row['ID_evento'];
    $event['title'] = $row['Nombre'];
    $event['start'] = $row['Fecha_evento']; // Fecha de inicio del evento
    $event['end'] = $row['Fecha_vencimiento']; // Fecha de fin del evento
    $event['color'] = $row['Color'];
    $events[] = $event;
}

// Devolver eventos en formato JSON
echo json_encode($events);

mysqli_close($conn);
?>
