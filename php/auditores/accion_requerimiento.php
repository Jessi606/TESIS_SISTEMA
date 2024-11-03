<?php
// Incluir el archivo de conexión
include 'conexion.php';

session_start();

// Función para registrar auditoría
function registrarAuditoria($conn, $usuario_id, $Idrequerimiento, $accion, $detalles) {
    try {
        // Preparar la consulta para insertar auditoría
        $sql_auditoria = "INSERT INTO auditoria_requerimientos (IDusuario, Idrequerimiento, Accion, Detalles, FechaHora) VALUES (?, ?, ?, ?, NOW())";
        $stmt_auditoria = $conn->prepare($sql_auditoria);
        
        if (!$stmt_auditoria) {
            throw new Exception("Error al preparar la consulta de auditoría: " . $conn->error);
        }
        
        $stmt_auditoria->bind_param("iiss", $usuario_id, $Idrequerimiento, $accion, $detalles);

        // Ejecutar la consulta preparada
        if ($stmt_auditoria->execute()) {
            return true;
        } else {
            throw new Exception("Error al ejecutar la consulta de auditoría: " . $stmt_auditoria->error);
        }
    } catch (Exception $e) {
        echo "Error al registrar auditoría: " . $e->getMessage();
        return false;
    }
}

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
            $accion = "Aceptar requerimiento";
            $detalles = "Requerimiento aceptado con ID: $Idrequerimiento";
            break;
        case 'regresar':
            // Actualizar el estado del requerimiento a 'Regresado'
            $sql = "UPDATE requerimientos SET Estado_requerimiento = 'Regresado' WHERE Idrequerimiento = $Idrequerimiento";
            $accion = "Regresar requerimiento";
            $detalles = "Requerimiento regresado con ID: $Idrequerimiento";
            break;
        default:
            // Acción no válida
            header("Location: requerimiento.php?error=AccionInvalida");
            exit;
    }

    // Ejecutar la consulta SQL
    if (mysqli_query($con, $sql)) {
        // Registrar acción de auditoría
        registrarAuditoria($con, $_SESSION['usuario_id'], $Idrequerimiento, $accion, $detalles);

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
