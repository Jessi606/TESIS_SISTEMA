<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Definir una variable para almacenar mensajes de error o éxito
$msg = '';

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // Conectar a la base de datos
    $conn = conectarDB();

    // Sanitizar los datos recibidos del formulario para prevenir inyección SQL
    $id_usuario = mysqli_real_escape_string($conn, $id_usuario);
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $password = mysqli_real_escape_string($conn, $password);
    $rol = mysqli_real_escape_string($conn, $rol);

    // Consulta SQL para actualizar los datos del usuario
    $sql = "UPDATE usuarios SET Nombre = '$nombre', Password = '$password', Idrol = '$rol' WHERE IDusuario = $id_usuario";
    
    // Ejecutar la consulta y verificar si se realizó correctamente
    if ($conn->query($sql) === TRUE) {
        $msg = "Usuario actualizado correctamente";
    } else {
        $msg = "Error al actualizar usuario: " . $conn->error;
    }
    
    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <!-- Enlace a Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Estilos adicionales -->
    <style>
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Usuario</h2>
        <?php if (!empty($msg)) { ?>
            <div class="alert alert-<?php echo (strpos($msg, 'Error') !== false) ? 'danger' : 'success'; ?>" role="alert">
                <?php echo $msg; ?>
            </div>
        <?php } ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="id_usuario">ID Usuario:</label>
                <input type="text" class="form-control" id="id_usuario" name="id_usuario">
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre">
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="rol">ID Rol:</label>
                <input type="text" class="form-control" id="rol" name="rol">
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</body>
</html>