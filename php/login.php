<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <!-- Vincular Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Vincular Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Estilos personalizados -->
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
            font-weight: bold;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .alert-danger i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #721c24;
        }

        .btn-custom {
            background-color: #007bff;
            border: none;
            font-size: 1rem;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .btn-back {
            margin-top: 10px;
            font-size: 0.9rem;
            background-color: #6c757d;
            border: none;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            color: white;
            transition: background-color 0.3s ease;
            display: block;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="login-container mx-auto">
            <div class="login-header">Iniciar Sesión</div>

            <?php
            session_start();

            // Verificar si ya hay una sesión activa, redirigir al usuario si es así
            if (isset($_SESSION['usuario_id'])) {
                switch ($_SESSION['rol']) {
                    case 1: // Asumimos que 1 es admin
                        header("Location: admin.php");
                        exit();
                    case 2: // Asumimos que 2 es auditor
                        header("Location: auditores/auditor.php");
                        exit();
                    case 3: // Asumimos que 3 es cliente
                        header("Location: clientes/cliente.php");
                        exit();
                    default:
                        header("Location: acceso_denegado.php");
                        exit();
                }
            }

            // Incluir el archivo de conexión a la base de datos
            require_once("conexion.php");

            // Procesar el formulario de inicio de sesión
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Obtener datos del formulario de manera segura
                $nombre = isset($_POST['username']) ? $_POST['username'] : "";
                $password = isset($_POST['password']) ? $_POST['password'] : "";

                // Conectar a la base de datos
                $conn = conectarDB();

                // Consulta SQL para verificar las credenciales
                $sql = "SELECT IDusuario, Nombre, Idrol FROM usuarios WHERE Nombre=? AND Password=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $nombre, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    // Iniciar sesión y redirigir al usuario según su rol
                    $row = $result->fetch_assoc();
                    $_SESSION['usuario_id'] = $row['IDusuario'];
                    $_SESSION['nombre'] = $row['Nombre'];
                    $_SESSION['rol'] = $row['Idrol'];

                    switch ($row['Idrol']) {
                        case 1: // Asumimos que 1 es admin
                            header("Location: admin.php");
                            exit();
                        case 2: // Asumimos que 2 es auditor
                            header("Location: auditores/auditor.php");
                            exit();
                        case 3: // Asumimos que 3 es cliente
                            header("Location: clientes/cliente.php");
                            exit();
                        default:
                            header("Location: acceso_denegado.php");
                            exit();
                    }
                } else {
                    // Mostrar mensaje de error con ícono y redirigir después de 5 segundos
                    echo '<div class="alert alert-danger text-center" role="alert">';
                    echo '<i class="bi bi-exclamation-triangle"></i>';
                    echo '<div>Usuario o contraseña incorrectos.</div>';
                    echo '</div>';
                    echo '<script>
                            setTimeout(function(){
                                window.location.href = "/TESIS_SISTEMA/index.php";
                            }, 5000); // Redirigir después de 5 segundos
                          </script>';
                }

                $stmt->close();
                $conn->close();
            }
            ?>
        </div>
    </div>

</body>

</html>
