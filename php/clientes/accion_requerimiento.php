<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Idrequerimiento = intval($_POST['idRequerimiento']);

    // Validar si el requerimiento existe
    $sqlValidar = "SELECT Estado_requerimiento FROM requerimientos WHERE Idrequerimiento = $Idrequerimiento";
    $resultadoValidar = mysqli_query($con, $sqlValidar);

    if (mysqli_num_rows($resultadoValidar) > 0) {
        // Obtener el estado actual
        $row = mysqli_fetch_assoc($resultadoValidar);
        $estadoActual = $row['Estado_requerimiento'];

        // Cambiar el estado a 'Enviado' si no está aceptado
        if ($estadoActual !== 'Aceptado') {
            $sqlActualizar = "UPDATE requerimientos SET Estado_requerimiento = 'Enviado' WHERE Idrequerimiento = $Idrequerimiento";
            if (!mysqli_query($con, $sqlActualizar)) {
                die("Error al actualizar el estado del requerimiento: " . mysqli_error($con));
            }
        }

        // Subir los archivos de evidencia
        if (isset($_FILES['archivoEvidencia'])) {
            $directorioSubida = '../../uploads/';
            foreach ($_FILES['archivoEvidencia']['tmp_name'] as $key => $tmpName) {
                $nombreArchivo = $_FILES['archivoEvidencia']['name'][$key];
                $rutaArchivo = $directorioSubida . basename($nombreArchivo);

                if (move_uploaded_file($tmpName, $rutaArchivo)) {
                    // Registrar la evidencia en la base de datos
                    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
                    $comentario = mysqli_real_escape_string($con, $_POST['comentario']);
                    $sqlInsertarEvidencia = "
                        INSERT INTO evidencias (Idrequerimiento, Descripcion, Comentario, Evidencia)
                        VALUES ($Idrequerimiento, '$descripcion', '$comentario', '$nombreArchivo')
                    ";
                    if (!mysqli_query($con, $sqlInsertarEvidencia)) {
                        echo "Error al registrar la evidencia: " . mysqli_error($con);
                    }
                } else {
                    echo "Error al mover el archivo: " . $nombreArchivo;
                }
            }
        }

        // Redirigir al listado de requerimientos
        header("Location: requerimiento.php?success=EvidenciaCargada");
        exit;
    } else {
        die("El requerimiento no existe o no es válido.");
    }
}
?>
