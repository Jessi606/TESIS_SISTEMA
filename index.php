<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Sistema de Auditoría</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Tus estilos personalizados -->
    <style>
        body {
            background-color: #a6bbd7; /* Color de fondo más oscuro */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .container-login {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row; /* Alineación horizontal */
            width: 100%;
            max-width: 1000px; /* Ancho máximo del contenedor */
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: auto; /* Centrado horizontal */
        }

        .form-container {
            width: 50%; /* Mitad del contenedor para el formulario */
            padding-right: 2rem; /* Espacio entre formulario e imagen */
        }

        .form__group {
            position: relative;
            margin-bottom: 2rem;
        }

        .form__input {
            width: 100%;
            padding: 1.2rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease-in-out;
            padding-left: 3rem; /* Espacio para el icono */
        }

        .form__input:focus {
            outline: none;
            border-color: #008080;
            box-shadow: 0 0 10px rgba(0, 128, 128, 0.3);
        }

        .form__icon {
            position: absolute;
            left: 1rem; /* Alineación más a la izquierda */
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1.2rem;
        }

        .btn {
            width: 100%;
            max-width: 200px;
            border-radius: 25px;
            color: #fff;
            font-weight: 600;
            background-color: #008080;
            border: 1px solid #008080;
            padding: 1.2rem;
            transition: all 0.3s;
        }

        .btn:hover, .btn:focus {
            background-color: #005353;
            border-color: #005353;
        }

        .company__logo-container {
            width: 100%;
            max-width: 200px;
            height: auto;
            margin-top: 1rem;
        }

        .company__logo {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .forgot-link {
            margin-top: 1rem;
            font-size: 0.9rem;
            text-align: left; /* Alineación del enlace a la izquierda */
            color: #008080;
            cursor: pointer; /* Cambia el cursor a pointer para indicar que es un enlace */
        }

        .bg-image {
            width: 55%; /* Aumentar el tamaño de la imagen */
            height: auto;
            overflow: hidden;
            border-radius: 15px;
        }

        .bg-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        .form-title {
            text-align: center; /* Centrado del título */
            margin-bottom: 2rem; /* Espacio inferior para separación */
        }

        @media (max-width: 992px) {
            .container-login {
                flex-direction: column; /* Cambia a apilado en dispositivos pequeños */
                align-items: center;
                padding: 1rem;
            }
            .form-container {
                width: 100%; /* Ancho completo en dispositivos pequeños */
                padding-right: 0; /* Sin espacio adicional en dispositivos pequeños */
                margin-bottom: 1rem; /* Espacio entre elementos apilados */
            }
            .bg-image {
                width: 100%; /* Ancho completo en dispositivos pequeños */
                margin-top: 1rem;
            }
            .forgot-link {
                text-align: left; /* Alineación del enlace a la izquierda en dispositivos pequeños */
                margin-top: 0.5rem; /* Ajuste de margen superior */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Contenedor del formulario y la imagen -->
                <div class="container-login">
                    <div class="form-container">
                        <h2 class="form-title h4 font-weight-bold text-theme mb-4">Inicio de Sesión</h2>
                        <form action="php/login.php" method="POST">
                            <div class="form__group">
                                <i class="fa fa-user form__icon"></i>
                                <input type="text" name="username" id="username" class="form__input" placeholder="Nombre de Usuario" required>
                            </div>
                            <div class="form__group">
                                <i class="fa fa-lock form__icon"></i>
                                <input type="password" name="password" id="password" class="form__input" placeholder="Contraseña" required>
                            </div>
                            <div class="form__group">
                                <button type="submit" class="btn">Ingresar</button>
                            </div>
                        </form>
                        <div class="forgot-link" onclick="window.location.href='reset_password.php';">¿Olvidaste tu contraseña?</div>
                    </div>
                    <div class="bg-image">
                        <img src="images/fon.jpg" alt="Background Image">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
