<?php
include 'conexion.php';

function obtenerTiposEventos() {
    $conn = conectarDB();
    $sql = "SELECT * FROM tipo_evento";
    $result = $conn->query($sql);
    $tiposEventos = [];

    while ($row = $result->fetch_assoc()) {
        $tiposEventos[] = $row;
    }

    $conn->close();
    return $tiposEventos;
}
?>
