<?php
include 'conexion.php';

$month = $_GET['month'];
$year = $_GET['year'];

$conn = conectarDB();
$sql = "SELECT id, titulo, descripcion, fecha_inicio, color FROM eventos WHERE MONTH(fecha_inicio) = ? AND YEAR(fecha_inicio) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['titulo'],
        'description' => $row['descripcion'],
        'date' => $row['fecha_inicio'],
        'color' => $row['color']
    ];
}

echo json_encode(['success' => true, 'events' => $events]);

$stmt->close();
$conn->close();
?>
