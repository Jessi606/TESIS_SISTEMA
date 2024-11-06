<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container-reset {
            max-width: 400px;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .message {
            font-size: 1rem;
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 8px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <?php
    // Incluir el archivo de conexión
    include 'php/conexion.php';

    // Definir mensajes
    $error_message = '';
    $success_message = '';
    $show_reset_form = false;
    $id_usuario = null;
    $current_password = '';

    // Paso 1: Verificar el nombre de usuario
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_user'])) {
        $nombre = $_POST['nombre'];

        // Conectar a la base de datos
        $conn = conectarDB();

        // Buscar el usuario en la base de datos usando el campo Nombre
        $sql = "SELECT IDusuario, Password FROM usuarios WHERE Nombre = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->bind_result($id_usuario, $current_password);
        
        if ($stmt->fetch()) {
            // Usuario encontrado, mostrar formulario de restablecimiento de contraseña
            $show_reset_form = true;
        } else {
            // Usuario no encontrado
            $error_message = 'Nombre de usuario no encontrado.';
        }
        
        $stmt->close();
        $conn->close();
    }

    // Paso 2: Restablecer la contraseña si el nombre de usuario es válido
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password']) && !empty($_POST['id_usuario'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $id_usuario = $_POST['id_usuario'];

        // Conectar a la base de datos
        $conn = conectarDB();

        // Obtener la contraseña actual del usuario
        $sql = "SELECT Password FROM usuarios WHERE IDusuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->bind_result($current_password);
        $stmt->fetch();
        $stmt->close();

        if ($new_password !== $confirm_password) {
            $error_message = 'Las contraseñas no coinciden.';
        } elseif ($new_password === $current_password) {
            $error_message = 'La nueva contraseña no puede ser la misma que la contraseña actual. Por favor, elige una diferente.';
        } else {
            // Actualizar la contraseña en la base de datos sin encriptación
            $sql = "UPDATE usuarios SET Password = ? WHERE IDusuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password, $id_usuario);

            if ($stmt->execute()) {
                $success_message = '¡Contraseña restablecida con éxito!';
            } else {
                $error_message = 'Error al actualizar la contraseña.';
            }

            $stmt->close();
        }
        $conn->close();
    }
    ?>

    <div class="container-reset">
        <h2>Restablecer Contraseña</h2>

        <?php if (!empty($error_message)) : ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)) : ?>
            <div class="message success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!$show_reset_form) : ?>
            <!-- Formulario para ingresar el nombre de usuario -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de usuario" required>
                </div>
                <button type="submit" name="check_user" class="btn btn-primary">Verificar Usuario</button>
                <a href="index.php" class="btn btn-primary mt-4"><i class="fas fa-arrow-left"></i> Volver a la página anterior</a>
            </form>
        <?php else : ?>
            <!-- Formulario para restablecer la contraseña -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>">
                <div class="form-group">
                    <input type="password" name="new_password" class="form-control" placeholder="Nueva Contraseña" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirmar Contraseña" required>
                </div>
                <button type="submit" name="reset_password" class="btn btn-primary">Restablecer Contraseña</button>
                <a href="index.php" class="btn btn-primary mt-4"><i class="fas fa-arrow-left"></i> Volver a la página anterior</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
