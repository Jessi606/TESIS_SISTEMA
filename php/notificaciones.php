<?php
include('conexion.php');
$conn = conectarDB();

$fecha_actual = date("Y-m-d");

$sql = "SELECT * FROM Eventos WHERE Recordatorio = 1 AND Fecha_vencimiento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $fecha_actual);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Emitir notificación (puedes personalizar esto según tus necesidades)
    echo "Recordatorio: Tienes un evento hoy: " . $row['Nombre'] . " en " . $row['Lugar'] . "<br>";
}

$stmt->close();
$conn->close();
?>
