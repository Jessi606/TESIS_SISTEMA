<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se recibió la acción y el ID del requerimiento
if (isset($_GET['action'], $_GET['Idrequerimiento'])) {
    $action = $_GET['action'];
    $Idrequerimiento = $_GET['Idrequerimiento'];

    // Validar los datos recibidos
    $action = mysqli_real_escape_string($con, $action);
    $Idrequerimiento = intval($Idrequerimiento);

    // Realizar la acción correspondiente
    switch ($action) {
        case 'aceptar':
            // Actualizar el estado del requerimiento a 'Aceptado'
            $sql = "UPDATE requerimientos SET Estado_requerimiento = 'Aceptado' WHERE Idrequerimiento = $Idrequerimiento";
            break;
        case 'regresar':
            // Actualizar el estado del requerimiento a 'Regresado'
            $sql = "UPDATE requerimientos SET Estado_requerimiento = 'Regresado' WHERE Idrequerimiento = $Idrequerimiento";
            break;
        default:
            // Acción no válida
            header("Location: requerimiento.php?error=AccionInvalida");
            exit;
    }

    // Ejecutar la consulta SQL
    if (mysqli_query($con, $sql)) {
        // Redirigir de vuelta a la página desde la que se realizó la acción
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    } else {
        // Error al ejecutar la consulta
        header("Location: requerimiento.php?error=ErrorConsulta");
        exit;
    }
} else {
    // Datos insuficientes
    header("Location: requerimiento.php?error=DatosInsuficientes");
    exit;
}
?>
