<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #a6bbd7;
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            margin-top: 50px;
            max-width: 400px;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .login-header {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .alert-danger {
            font-size: 1.2rem;
            background-color: #f8d7da;
            color: #721c24;
            margin-bottom: 15px;
        }

        .btn-custom {
            background-color: #007bff;
            border: none;
            width: 100%;
            color: white;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container mx-auto">
            <div class="login-header">Iniciar Sesión</div>

            <?php
            session_start();

            // Redirigir si ya hay una sesión activa
            if (isset($_SESSION['usuario_id'])) {
                switch ($_SESSION['rol']) {
                    case 1:
                        header("Location: admin.php");
                        exit();
                    case 2:
                        header("Location: auditores/auditor.php");
                        exit();
                    case 3:
                        header("Location: clientes/cliente.php");
                        exit();
                }
            }

            include("conexion.php");

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nombre = $_POST['username'] ?? "";
                $password = $_POST['password'] ?? "";

                $conn = conectarDB();
                $sql = "SELECT IDusuario, Nombre, Idrol, Estado FROM usuarios WHERE Nombre=? AND Password=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $nombre, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();

                    // Verificar si el usuario está activo
                    if ($row['Estado'] == 1) {
                        $_SESSION['usuario_id'] = $row['IDusuario'];
                        $_SESSION['nombre'] = $row['Nombre'];
                        $_SESSION['rol'] = $row['Idrol'];

                        // Redirigir según el rol del usuario
                        switch ($row['Idrol']) {
                            case 1:
                                header("Location: admin.php");
                                exit();
                            case 2:
                                header("Location: auditores/auditor.php");
                                exit();
                            case 3:
                                header("Location: clientes/cliente.php");
                                exit();
                        }
                    } else {
                        $error = "Tu cuenta está inactiva. Contacta al administrador.";
                    }
                } else {
                    $error = "Usuario o contraseña incorrectos.";
                }

                $stmt->close();
                $conn->close();
            }
            ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><?= $error ?></div>
                </div>
                <script>
                    setTimeout(function() {
                    window.location.href = "/TESIS_SISTEMA/index.php";
                    }, 3000); // Redirige después de 3 segundos
                </script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
