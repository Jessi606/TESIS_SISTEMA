<?php
include 'conexion.php';

// Función para obtener tipos de eventos desde la base de datos
function obtenerTiposEventos() {
    $conn = conectarDB();
    $sql = "SELECT Id_tipoevento, Descripcion FROM tipo_evento";
    $result = $conn->query($sql);
    $tiposEventos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tiposEventos[] = $row;
        }
    }
    $conn->close();
    return $tiposEventos;
}

// Función para obtener proyectos desde la base de datos
function obtenerProyectos() {
    $conn = conectarDB();
    $sql = "SELECT Id_proyecto, Descripcion FROM proyectos";
    $result = $conn->query($sql);
    $proyectos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $proyectos[] = $row;
        }
    }
    $conn->close();
    return $proyectos;
}

// Función para obtener eventos desde la base de datos
function obtenerEventos($month, $year) {
    $conn = conectarDB();
    $sql = "SELECT Id_evento, Titulo, Descripcion, Fecha_inicio, Fecha_fin, Lugar, Color FROM eventos WHERE MONTH(Fecha_inicio) = ? AND YEAR(Fecha_inicio) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $eventos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $eventos[] = $row;
        }
    }
    $stmt->close();
    $conn->close();
    return $eventos;
}

// Función para eliminar evento por ID
function eliminarEvento($idEvento) {
    $conn = conectarDB();
    $sql = "DELETE FROM eventos WHERE Id_evento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idEvento);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
