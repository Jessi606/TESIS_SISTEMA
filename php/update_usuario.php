<?php
include('conexion.php');
$con = conectarDB();

// Verificar si IDusuario está presente en la URL
if (isset($_GET['IDusuario'])) {
    $id = $_GET['IDusuario'];

    // Consulta SQL para obtener detalles del usuario específico
    $sql = "SELECT * FROM usuarios WHERE IDusuario='$id'";
    $query = mysqli_query($con, $sql);

    // Verificar si se encontró el usuario
    if ($query && mysqli_num_rows($query) > 0) {
        // Obtener los datos del usuario como array asociativo
        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
    } else {
        // Manejar el caso donde no se encuentra el usuario
        echo "Usuario no encontrado.";
        exit; // Opcional: detener la ejecución si el usuario no se encuentra
    }
} else {
    // Manejar el caso donde IDusuario no está presente en la URL
    echo "Parámetro IDusuario no definido.";
    exit; // Opcional: detener la ejecución si IDusuario no está presente
}

// Verificar si se envió el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = isset($_POST['Nombre']) ? $_POST['Nombre'] : '';
    $password = isset($_POST['Password']) ? $_POST['Password'] : '';
    $id_rol = isset($_POST['Idrol']) ? $_POST['Idrol'] : '';

    // Verificar si se proporcionaron todos los datos necesarios
    if (!empty($nombre) && !empty($password) && !empty($id_rol)) {
        // Sanitizar los datos recibidos del formulario para prevenir inyección SQL
        $nombre = mysqli_real_escape_string($con, $nombre);
        $password = mysqli_real_escape_string($con, $password);
        $id_rol = mysqli_real_escape_string($con, $id_rol);
        $id = mysqli_real_escape_string($con, $id);

        // Consulta SQL para actualizar los datos del usuario
        $sql = "UPDATE usuarios SET Nombre = '$nombre', Password = '$password', Idrol = '$id_rol' WHERE IDusuario = '$id'";
        
        // Ejecutar la consulta y verificar si se realizó correctamente
        if ($con->query($sql) === TRUE) {
            echo "Usuario actualizado correctamente";
            // Redireccionar al usuario a la página de usuarios
            header("Location: usuarios.php");
            exit(); // Finalizar el script después de la redirección
        } else {
            echo "Error al actualizar el usuario: " . $con->error;
        }
        
        // Cerrar la conexión a la base de datos
        $con->close();
    } else {
        // Manejar el caso donde faltan datos en el formulario
        echo "Por favor, complete todos los campos del formulario.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <!-- Integra Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Integra FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }
         /* Estilo para el encabezado de la tarjeta */
         .card-header {
            background-color: #343a40; /* Color de fondo gris oscuro */
            color: #fff; /* Texto blanco */
            border-bottom: 1px solid #444; /* Borde inferior más oscuro */
        }
        /* Estilo para el cuerpo de la tarjeta */
        .card-body {
            background-color: #fff; /* Fondo blanco */
        }
        /* Estilo para el botón de enviar */
        .btn-primary {
            background-color: #007bff; /* Color azul */
            border-color: #007bff; /* Borde azul */
        }
        /* Estilo para el botón de enviar al pasar el mouse */
        .btn-primary:hover {
            background-color: #0056b3; /* Color azul más oscuro al pasar el mouse */
            border-color: #0056b3; /* Borde azul más oscuro al pasar el mouse */
        }
        /* Estilo para el botón de cancelar */
        .btn-secondary {
            background-color: #6c757d; /* Color gris */
            border-color: #6c757d; /* Borde gris */
        }
        /* Estilo para el botón de cancelar al pasar el mouse */
        .btn-secondary:hover {
            background-color: #5a6268; /* Color gris más oscuro al pasar el mouse */
            border-color: #5a6268; /* Borde gris más oscuro al pasar el mouse */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1 class="mb-0">Editar Usuario</h1>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?IDusuario=' . $id); ?>" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="Nombre" class="form-control" value="<?= htmlspecialchars($row['Nombre']) ?>" placeholder="Ingrese el nombre">
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="Password" class="form-control" value="<?= htmlspecialchars($row['Password']) ?>" placeholder="Ingrese la contraseña">
                    </div>
                    <div class="form-group">
                        <label for="id_rol">Rol:</label>
                        <select class="form-control" id="id_rol" name="Idrol">
                            <?php 
                            $sql_roles = "SELECT Idrol, Descripcion FROM roles";
                            $result_roles = $con->query($sql_roles);
                            if ($result_roles->num_rows > 0) {
                                while ($row_role = $result_roles->fetch_assoc()) { 
                                    $selected = ($row_role['Idrol'] == $row['Idrol']) ? 'selected' : '';
                                    echo "<option value='".$row_role['Idrol']."' $selected>".$row_role['Descripcion']."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
                    <a href="usuarios.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Integra Bootstrap JS (opcional, si es necesario) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Integra FontAwesome (opcional, si es necesario) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>
