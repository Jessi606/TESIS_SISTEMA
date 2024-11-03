<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$idciudad = $_POST['ciudad'];

// Preparar y ejecutar la consulta de inserción
$sql = "INSERT INTO clientes (nombre, direccion, telefono, email, idciudad) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $nombre, $direccion, $telefono, $email, $idciudad);

// Ejecutar la consulta SQL
if ($stmt->execute()) {
    // Redireccionar a la página de lista de auditores con el parámetro de éxito
    header("Location: agregar_auditor.php?success=1");
    exit();
} else {
    // Si hay un error, verificar si es debido a una entrada duplicada en la clave primaria
    if ($stmt->errno == 1062) { // 1062 es el código de error para una entrada duplicada
        $errorMessage = "Error al agregar el auditor: Ya existe un auditor con el mismo ID de usuario.";
    } else {
        // Si el error no es debido a una entrada duplicada, mostrar el mensaje de error general
        $errorMessage = "Error al agregar el auditor: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Agregar Cliente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Resultado de Agregar Cliente</h1>
    <div class="alert <?php echo $alertClass; ?> mt-3" role="alert">
        <?php echo $message; ?>
    </div>
    <a href="add_cliente.php" class="btn btn-primary">Agregar otro cliente</a>
    <a href="index.php" class="btn btn-secondary">Volver a la lista de clientes</a>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
