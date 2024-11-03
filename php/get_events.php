<?php
include('conexion.php');
$conn = conectarDB();

// Obtener eventos desde la base de datos
$sql = "SELECT Nombre AS title, Fecha_evento AS start, Color AS color FROM eventos";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error al obtener eventos: " . mysqli_error($conn));
}

$events = array();

while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

echo json_encode($events);
?>
