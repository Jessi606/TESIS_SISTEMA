<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si se ha proporcionado el ID del auditor en la URL
$id_auditor = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id_auditor)) {
    die("Error: No se ha especificado el ID del auditor.");
}

// Preparar y ejecutar la consulta para obtener los datos del auditor
$auditor_sql = "SELECT * FROM auditores WHERE Idauditor = ?";
$stmt = $conn->prepare($auditor_sql);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $id_auditor);

if (!$stmt->execute()) {
    die("Error al ejecutar la consulta: " . $stmt->error);
}

$auditor_result = $stmt->get_result();

// Verificar si se encontraron datos del auditor
if ($auditor_result && $auditor_result->num_rows > 0) {
    $auditor = $auditor_result->fetch_assoc();
} else {
    die("Error: No se encontró el auditor con ID: " . htmlspecialchars($id_auditor));
}

$stmt->close();

// Obtener la lista de usuarios
$usuarios_sql = "SELECT IDusuario, Nombre FROM usuarios WHERE IDrol = 2";
$usuarios_result = $conn->query($usuarios_sql);

if (!$usuarios_result) {
    die("Error al obtener la lista de usuarios: " . $conn->error);
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $nivel_experiencia = $_POST['nivel_experiencia'];
    $id_usuario = $_POST['id_usuario'];

    // Validar si el usuario seleccionado ya está asignado a otro auditor
    $usuario_duplicado_sql = "SELECT COUNT(*) as total FROM auditores WHERE IDusuario = ? AND Idauditor != ?";
    $stmt = $conn->prepare($usuario_duplicado_sql);
    $stmt->bind_param("ii", $id_usuario, $id_auditor);
    $stmt->execute();
    $usuario_duplicado_result = $stmt->get_result();
    $usuario_duplicado = $usuario_duplicado_result->fetch_assoc();
    $stmt->close();

    if ($usuario_duplicado['total'] > 0) {
        echo "<script>alert('El usuario seleccionado ya está asignado a otro auditor. Por favor, seleccione otro usuario.');</script>";
    } else {
        // Actualizar los datos del auditor
        $sql = "UPDATE auditores 
                SET Nombre = ?, Apellido = ?, Telefono = ?, Email = ?, FechaNacimiento = ?, NivelExperiencia = ?, IDusuario = ? 
                WHERE Idauditor = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta de actualización: " . $conn->error);
        }

        $stmt->bind_param("ssssssii", $nombre, $apellido, $telefono, $email, $fecha_nacimiento, $nivel_experiencia, $id_usuario, $id_auditor);

        if ($stmt->execute()) {
            // Redireccionar a la página de auditores sin ningún mensaje
            header("Location: auditores.php");
            exit();
        } else {
            die("Error al actualizar el auditor: " . $stmt->error);
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Auditor</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }

        .container {
            max-width: 600px;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            margin: auto;
            margin-top: 50px;
        }

        h1 {
            color: #000;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 20px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            border-radius: 20px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Editar Auditor</h1>
    <form action="editar_auditor.php?id=<?php echo htmlspecialchars($id_auditor); ?>" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($auditor['Nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($auditor['Apellido']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($auditor['Telefono']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($auditor['Email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($auditor['FechaNacimiento']); ?>" required>
        </div>
        <div class="form-group">
            <label>Nivel de Experiencia</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nivel_experiencia" id="junior" value="Junior" <?php if ($auditor['NivelExperiencia'] == 'Junior') echo 'checked'; ?>>
                <label class="form-check-label" for="junior">Junior</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nivel_experiencia" id="senior" value="Senior" <?php if ($auditor['NivelExperiencia'] == 'Senior') echo 'checked'; ?>>
                <label class="form-check-label" for="senior">Senior</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nivel_experiencia" id="gerente" value="Gerente" <?php if ($auditor['NivelExperiencia'] == 'Gerente') echo 'checked'; ?>>
                <label class="form-check-label" for="gerente">Gerente</label>
            </div>
        </div>
        <div class="form-group">
            <label for="id_usuario">Usuario Asignado</label>
            <select class="form-control" id="id_usuario" name="id_usuario" required>
                <?php
                while ($usuario = $usuarios_result->fetch_assoc()) {
                    $selected = ($auditor['IDusuario'] == $usuario['IDusuario']) ? 'selected' : '';
                    echo "<option value='{$usuario['IDusuario']}' $selected>{$usuario['Nombre']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i> Guardar Cambios</button>
        <a href="auditores.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </form>
</div>
</body>
</html>
