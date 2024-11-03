<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $descripcion = $_POST['descripcion'];
    $lugar = $_POST['lugar'];
    $id_tipoevento = $_POST['id_tipoevento'];
    $recordatorio = isset($_POST['recordatorio']) ? 1 : 0;
    $color = $_POST['color'];

    $conn = conectarDB();
    $stmt = $conn->prepare("INSERT INTO eventos (titulo, fecha_inicio, fecha_fin, descripcion, lugar, id_tipoevento, recordatorio, color) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiis", $titulo, $fecha_inicio, $fecha_fin, $descripcion, $lugar, $id_tipoevento, $recordatorio, $color);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
