<?php 
// Incluir el archivo de conexión
include 'conexion.php';

// Definir una variable para almacenar mensajes de error o éxito
$msg = '';

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = isset($_POST['Nombre']) ? $_POST['Nombre'] : '';
    $password = isset($_POST['Password']) ? $_POST['Password'] : '';
    $id_rol = isset($_POST['Idrol']) ? $_POST['Idrol'] : '';

    // Verificar si se proporcionaron todos los datos necesarios
    if (!empty($nombre) && !empty($password) && !empty($id_rol)) {
        // Conectar a la base de datos
        $conn = conectarDB();

        // Sanitizar los datos recibidos del formulario para prevenir inyección SQL
        $nombre = mysqli_real_escape_string($conn, $nombre);
        $password = mysqli_real_escape_string($conn, $password);
        $id_rol = mysqli_real_escape_string($conn, $id_rol);

        // Consulta SQL para insertar un nuevo usuario con estado activo
        $sql = "INSERT INTO usuarios (Nombre, Password, Idrol, Estado) VALUES ('$nombre', '$password', '$id_rol', 1)";
        
        // Ejecutar la consulta y verificar si se realizó correctamente
        if ($conn->query($sql) === TRUE) {
            $msg = "Usuario agregado correctamente";
            
            // Redireccionar al usuario a la página de usuarios
            header("Location: usuarios.php");
            exit(); // Finalizar el script después de la redirección
        } else {
            $msg = "Error al agregar usuario: " . $conn->error;
        }
        
        // Cerrar la conexión a la base de datos
        $conn->close();
    } else {
        // Manejar el caso donde faltan datos en el formulario
        $msg = "Por favor, complete todos los campos del formulario.";
    }
}

// Obtener todos los roles de la base de datos
$conn = conectarDB();
$sql_roles = "SELECT Idrol, Descripcion FROM roles";
$result_roles = $conn->query($sql_roles);
$roles = [];
if ($result_roles->num_rows > 0) {
    while ($row = $result_roles->fetch_assoc()) {
        $roles[$row["Idrol"]] = $row["Descripcion"];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
    <!-- Enlace a Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Estilos adicionales -->
    <style>
        body {
            background-color:  #a6bbd7;
        }
        .container {
            max-width: 500px;
            margin: auto;
            border-radius: 10px;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
        .alert {
            margin-top: 20px;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Agregar Usuario</h2>
        <?php if (!empty($msg)) { ?>
            <div class="alert alert-<?php echo (strpos($msg, 'Error') !== false) ? 'danger' : 'success'; ?>" role="alert">
                <?php echo $msg; ?>
            </div>
        <?php } ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="Nombre" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="Password" required>
            </div>
            <div class="form-group">
                <label for="id_rol">Rol:</label>
                <select class="form-control" id="id_rol" name="Idrol" required>
                    <?php foreach ($roles as $id => $descripcion) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $descripcion; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Agregar Usuario</button>
            <a href="usuarios.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
        </form>
    </div>
</body>
</html>
