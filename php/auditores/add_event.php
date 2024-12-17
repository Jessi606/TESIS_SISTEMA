<?php
session_start();
include('conexion.php');

// Conectar a la base de datos
$conn = conectarDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['Nombre']) ? $_POST['Nombre'] : '';
    $fecha_evento = isset($_POST['Fecha_evento']) ? date("Y-m-d H:i:s", strtotime($_POST['Fecha_evento'])) : '';
    $descripcion = isset($_POST['Descripcion']) ? $_POST['Descripcion'] : '';
    $lugar = isset($_POST['Lugar']) ? $_POST['Lugar'] : '';
    $fecha_vencimiento = isset($_POST['Fecha_vencimiento']) ? date("Y-m-d H:i:s", strtotime($_POST['Fecha_vencimiento'])) : '';
    $id_tipoevento = isset($_POST['Id_tipoevento']) ? $_POST['Id_tipoevento'] : '';
    $color = isset($_POST['Color']) ? $_POST['Color'] : '';
    $id_proyecto = isset($_POST['Idproyecto']) ? $_POST['Idproyecto'] : '';

    if (!empty($nombre)) {
        $sql_insert_evento = "INSERT INTO eventos (Nombre, Fecha_evento, Descripcion, Lugar, Fecha_vencimiento, Id_tipoevento, Color, Idproyecto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql_insert_evento);
        mysqli_stmt_bind_param($stmt, "sssssssi", $nombre, $fecha_evento, $descripcion, $lugar, $fecha_vencimiento, $id_tipoevento, $color, $id_proyecto);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Evento agregado exitosamente.";
        } else {
            $_SESSION['error_message'] = "Error al agregar el evento: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error_message'] = "El nombre del evento es obligatorio.";
    }
} else {
    $_SESSION['error_message'] = "Método de solicitud no válido.";
}

mysqli_close($conn);
header("Location: calendario.php");
exit;
?>
