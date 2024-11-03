<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center; /* Centra horizontalmente */
            align-items: center; /* Centra verticalmente */
            height: 100vh;
            margin: 0;
            padding: 0;
        }
        .container-reset {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
        }
        .message {
            font-size: 1rem;
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 8px;
        }
        .error-message {
            background-color: #f8d7da; /* Rojo */
            border-color: #f5c6cb;
            color: #721c24;
        }
        .success-message {
            background-color: #d4edda; /* Verde */
            border-color: #c3e6cb;
            color: #155724;
        }
        .btn-group {
            display: flex;
            gap: 10px; /* Espacio entre botones */
        }
    </style>
</head>
<body>
    <?php
    // Incluir el archivo de conexión
    include 'php/conexion.php';

    // Definir variables para mensajes
    $error_message = '';
    $success_message = '';

    // Verificar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener datos del formulario
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Conectar a la base de datos
        $conn = conectarDB();

        // Obtener el ID del usuario (debes ajustar esta parte según tu aplicación)
        $id_usuario = 1; // Aquí deberías obtener el ID de usuario correspondiente

        // Obtener la contraseña actual del usuario
        $sql = "SELECT Password FROM usuarios WHERE IDusuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->bind_result($current_password);
        $stmt->fetch();
        $stmt->close();

        // Verificar que las contraseñas coincidan
        if ($new_password !== $confirm_password) {
            $error_message = 'Las contraseñas no coinciden.';
        } elseif ($new_password === $current_password) {
            $error_message = 'La nueva contraseña no puede ser la misma que la contraseña actual.';
        } else {
            // Actualizar la contraseña en la base de datos
            $sql = "UPDATE usuarios SET Password = ? WHERE IDusuario = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die('Error al preparar la consulta: ' . $conn->error);
            }

            $stmt->bind_param("si", $new_password, $id_usuario);

            if ($stmt->execute()) {
                $success_message = '¡Contraseña reseteada con éxito!';
                echo "<script>
                        setTimeout(function(){
                            window.location.href = 'index.php';
                        }, 3000);
                      </script>";
            } else {
                $error_message = 'Error al actualizar la contraseña: ' . $conn->error;
            }

            $stmt->close();
            $conn->close();
        }
    }
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="container-reset">
                    <h2 class="h4 font-weight-bold text-theme mb-4 text-center">Restablecer Contraseña</h2>
                    <?php if (!empty($error_message)) : ?>
                        <div class="message error-message">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success_message)) : ?>
                        <div class="message success-message">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    <!-- Formulario de reseteo de contraseña -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return validateForm()">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                </div>
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Nueva Contraseña" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                </div>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirmar Contraseña" required>
                            </div>
                            <span id="error_message" class="error-message"></span>
                        </div>
                        <div class="form-group btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Restablecer
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    function validateForm() {
        var new_password = document.getElementById('new_password').value;
        var confirm_password = document.getElementById('confirm_password').value;
        var error_message = document.getElementById('error_message');
        var current_password = "password_actual";  // Reemplaza con la contraseña actual obtenida

        if (new_password !== confirm_password) {
            error_message.textContent = 'Las contraseñas no coinciden.';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
            setTimeout(function() {
                error_message.textContent = '';
            }, 3000); // Limpia el mensaje después de 3 segundos
            return false;
        } else if (new_password === current_password) {
            error_message.textContent = 'La nueva contraseña no puede ser la misma que la contraseña actual.';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
            setTimeout(function() {
                error_message.textContent = '';
            }, 3000); // Limpia el mensaje después de 3 segundos
            return false;
        } else {
            error_message.textContent = '';
            return true; // Permitir que el formulario se envíe si las contraseñas coinciden
        }
    }
    </script>
</body>
</html>
