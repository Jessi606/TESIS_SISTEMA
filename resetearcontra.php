<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .reset-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Restablecer Contraseña</h2>
        <form id="resetForm">
            <input type="email" id="email" name="email" placeholder="Introduce tu correo electrónico" required>
            <button type="submit">Enviar</button>
        </form>
        <div class="message" id="message"></div>
    </div>

    <script>
        document.getElementById('resetForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const messageDiv = document.getElementById('message');

            // Aquí iría la lógica para manejar el restablecimiento de la contraseña.
            // Esto puede involucrar una llamada a una API, etc.

            messageDiv.textContent = `Se ha enviado un correo de restablecimiento de contraseña a ${email}.`;
        });
    </script>
</body>
</html>
