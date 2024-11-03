<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Establecer la zona horaria a Asunción, Paraguay
date_default_timezone_set('America/Asuncion');

// Verificar si el formulario se ha enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conectar a la base de datos
    $con = conectarDB();

    // Obtener los datos del formulario
    $idRequerimiento = intval($_POST['idRequerimiento']);
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcionEvidencia']);
    $comentario = mysqli_real_escape_string($con, $_POST['comentarioEvidencia']);
    $fechaRecopilacion = mysqli_real_escape_string($con, $_POST['fechaRecopilacion']); // Fecha y hora de recopilación

    // Verificar si se han subido archivos
if (isset($_FILES['archivoEvidencia']) && count($_FILES['archivoEvidencia']['name']) > 0) {
    // Directorio donde se guardarán los archivos
    $directorioSubida = '../../uploads/'; // Ruta relativa para acceder a la carpeta uploads desde la carpeta php

    // Verificar el estado actual del requerimiento
    $sqlEstado = "SELECT Estado_requerimiento FROM requerimientos WHERE Idrequerimiento = $idRequerimiento";
    $resultadoEstado = mysqli_query($con, $sqlEstado);
    $rowEstado = mysqli_fetch_assoc($resultadoEstado);
    $estadoActual = $rowEstado['Estado_requerimiento'];

    // Si el estado actual es "Devuelto", actualizarlo a "Enviado"
    if ($estadoActual == 'Devuelto') {
        $sqlActualizarEstado = "UPDATE requerimientos SET Estado_requerimiento = 'Enviado' WHERE Idrequerimiento = $idRequerimiento";
        mysqli_query($con, $sqlActualizarEstado);
    }

    // Iterar sobre cada archivo
    for ($i = 0; $i < count($_FILES['archivoEvidencia']['name']); $i++) {
        $archivoEvidencia = $_FILES['archivoEvidencia']['name'][$i];
        $rutaArchivo = $directorioSubida . basename($archivoEvidencia);

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($_FILES['archivoEvidencia']['tmp_name'][$i], $rutaArchivo)) {
            // Insertar los datos en la base de datos
            $sql = "INSERT INTO evidencias (Descripcion, Comentario, Fecha_recopilacion, Idrequerimiento, Evidencia) VALUES ('$descripcion', '$comentario', '$fechaRecopilacion', $idRequerimiento, '$archivoEvidencia')";

            if (!mysqli_query($con, $sql)) {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            echo "Error al subir el archivo: " . $_FILES['archivoEvidencia']['name'][$i];
        }
    }

    // Redireccionar a la página de éxito
    header('Location: exito.php');
    exit;
} else {
    echo "No se han subido archivos.";
}

} else {
    // Obtener el ID del requerimiento de la URL
    $idRequerimiento = isset($_GET['idRequerimiento']) ? intval($_GET['idRequerimiento']) : 0;

    // Verificar si el ID del requerimiento es válido
    if ($idRequerimiento <= 0) {
        die("El ID del requerimiento proporcionado no es válido. Por favor, verifica el enlace e intenta de nuevo.");
    }
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cargar Evidencia</title>
        <!-- Integra Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome Cdn Link -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            body {
                background-color: #a6bbd7;
                color: #333;
            }
            .container {
                max-width: 800px;
                margin: auto;
                border-radius: 10px;
                margin-top: 50px;
                background-color: #fff;
                padding: 20px;
                box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
            }
            h2 {
                color: #000;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Cargar Evidencia</h2>
            <form action="cargar_evidencia_formulario.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="idRequerimiento" value="<?= $idRequerimiento ?>">
                <div class="form-group">
                    <label for="descripcionEvidencia">Descripción</label>
                    <input type="text" class="form-control" id="descripcionEvidencia" name="descripcionEvidencia" required>
                </div>
                <div class="form-group">
                    <label for="comentarioEvidencia">Comentario</label>
                    <textarea class="form-control" id="comentarioEvidencia" name="comentarioEvidencia" required></textarea>
                </div>
                <div class="form-group">
                    <label for="fechaRecopilacion">Fecha y Hora de Recopilación</label>
                    <input type="text" class="form-control" id="fechaRecopilacion" name="fechaRecopilacion" value="<?= date('Y-m-d H:i:s') ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="archivoEvidencia">Archivos Adjuntos</label>
                    <input type="file" class="form-control-file" id="archivoEvidencia" name="archivoEvidencia[]" multiple required>
                </div>
                <button type="submit" class="btn btn-primary">Cargar Evidencia</button>
            </form>
            <a href="requerimiento.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
    </body>
    </html>

<?php
}
?>
