<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar si se ha enviado un archivo
if ($_FILES['archivoEvidencia']['error'] === UPLOAD_ERR_OK) {
    // Directorio donde se almacenarán los archivos cargados
    $directorioDestino = '../uploads/';

    // Obtener el nombre del archivo y su ubicación temporal
    $nombreArchivo = basename($_FILES['archivoEvidencia']['name']);
    $ubicacionTemporal = $_FILES['archivoEvidencia']['tmp_name'];

    // Mover el archivo a la ubicación deseada
    if (move_uploaded_file($ubicacionTemporal, $directorioDestino . $nombreArchivo)) {
        // Archivo cargado con éxito, ahora guardamos el nombre en la base de datos
        $descripcion = $_POST['descripcionEvidencia'];
        $comentario = $_POST['comentarioEvidencia'];
        $fechaRecopilacion = $_POST['fechaRecopilacion'];
        $idRequerimiento = $_POST['idRequerimiento'];
        $estadoEvidencia = $_POST['estadoEvidencia'];

        // Consulta SQL para insertar la evidencia en la base de datos
        $sql = "INSERT INTO evidencia (Nombre_archivo, Descripcion, Comentario, Fecha_recopilacion, Id_requerimiento, Estado_evidencia) VALUES ('$nombreArchivo', '$descripcion', '$comentario', '$fechaRecopilacion', '$idRequerimiento', '$estadoEvidencia')";

        if (mysqli_query($con, $sql)) {
            // Redireccionar a la página de éxito
            header('Location: exito.php');
            exit;
        } else {
            // Error al insertar en la base de datos
            echo "Error: " . mysqli_error($con);
        }
    } else {
        // Error al mover el archivo
        echo "Error: no se pudo mover el archivo a la ubicación deseada.";
    }
} else {
    // Error al cargar el archivo
    echo "Error: " . $_FILES['archivoEvidencia']['error'];
}
?>