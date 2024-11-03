<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $nivel_experiencia = isset($_POST['nivel_experiencia']) ? $_POST['nivel_experiencia'] : '';
    $id_usuario = $_POST['id_usuario'];

    // Verificar que nivel_experiencia no esté vacío
    if (empty($nivel_experiencia)) {
        $errorMessage = "El nivel de experiencia es obligatorio.";
    } else {
        // Verificar si el usuario ya ha sido asignado como auditor
        $sql_verificar = "SELECT IDusuario FROM auditores WHERE IDusuario = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("i", $id_usuario);
        $stmt_verificar->execute();
        $stmt_verificar->store_result();
        if ($stmt_verificar->num_rows > 0) {
            $errorMessage = "Error al agregar el auditor: El usuario ya ha sido asignado como auditor.";
        } else {
            // Preparar la consulta SQL para insertar el auditor en la base de datos
            $sql = "INSERT INTO auditores (Nombre, Apellido, Telefono, Email, FechaNacimiento, NivelExperiencia, IDusuario) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $nombre, $apellido, $telefono, $email, $fecha_nacimiento, $nivel_experiencia, $id_usuario);

            // Ejecutar la consulta SQL
            if ($stmt->execute()) {
                // Redireccionar a la página de lista de auditores con el parámetro de éxito
                header("Location: agregar_auditor.php?success=1");
                exit();
            } else {
                // Si hay un error, mostrar el mensaje de error general
                $errorMessage = "Error al agregar el auditor: " . $stmt->error;
            }
        }
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Agregar Auditor</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
         body {
            background-color: #a6bbd7;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Resultado de Agregar Auditor</h1>
    <?php if (isset($errorMessage)) : ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>
    <a href="agregar_auditor.php" class="btn btn-primary">Agregar otro auditor</a>
    <a href="auditores.php" class="btn btn-secondary">Volver a la lista de auditores</a>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
