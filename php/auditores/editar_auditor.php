<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener el ID del auditor desde la URL si está presente
$id_auditor = isset($_GET['id']) ? $_GET['id'] : '';

// Si se proporciona un ID de auditor, obtener los datos del auditor de la base de datos
if ($id_auditor != '') {
    // Consulta SQL para obtener los datos del auditor
    $auditor_sql = "SELECT * FROM auditores WHERE Idauditor = '$id_auditor'";
    $auditor_result = $conn->query($auditor_sql);

    // Verificar si se encontraron datos del auditor
    if ($auditor_result && $auditor_result->num_rows > 0) {
        // Obtener los datos del auditor
        $auditor = $auditor_result->fetch_assoc();
    } else {
        // Si no se encuentra el auditor, mostrar un mensaje de error
        echo "Auditor no encontrado";
        exit();
    }
}

// Obtener la lista de usuarios
$usuarios_sql = "SELECT IDusuario, Nombre FROM usuarios WHERE IDrol = 2"; // Filtrar solo los usuarios con el ID de rol de auditor (en este caso, 2)
$usuarios_result = $conn->query($usuarios_sql);

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $nivel_experiencia = $_POST['nivel_experiencia'];
    $id_usuario = $_POST['id_usuario'];

    // Validar si el usuario seleccionado ya está asignado
    $usuario_duplicado_sql = "SELECT COUNT(*) as total FROM auditores WHERE IDusuario = '$id_usuario'";
    $usuario_duplicado_result = $conn->query($usuario_duplicado_sql);
    $usuario_duplicado = $usuario_duplicado_result->fetch_assoc();

    if ($usuario_duplicado['total'] > 0 && $id_usuario != $auditor['IDusuario']) {
        echo "<script>alert('El usuario seleccionado ya está asignado a otro auditor. Por favor, seleccione otro usuario.');</script>";
    } else {
        $sql = "UPDATE auditores 
                SET Nombre = '$nombre', Apellido = '$apellido', Telefono = '$telefono', 
                Email = '$email', FechaNacimiento = '$fecha_nacimiento', NivelExperiencia = '$nivel_experiencia', 
                IDusuario = '$id_usuario' 
                WHERE Idauditor = '$id_auditor'";

        if ($conn->query($sql) === TRUE) {
            // Redireccionar a la página de auditores con el parámetro de éxito
            header("Location: auditores.php?success=1");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_editar_auditor.css">
</head>
<body>
<div class="container">
    <h1>Editar Auditor</h1>
    <form action="editar_auditor.php?id=<?php echo $id_auditor; ?>" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo isset($auditor['Nombre']) ? $auditor['Nombre'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo isset($auditor['Apellido']) ? $auditor['Apellido'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo isset($auditor['Telefono']) ? $auditor['Telefono'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($auditor['Email']) ? $auditor['Email'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo isset($auditor['FechaNacimiento']) ? $auditor['FechaNacimiento'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label>Nivel de Experiencia</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nivel_experiencia" id="junior" value="Junior" <?php if (isset($auditor['NivelExperiencia']) && $auditor['NivelExperiencia'] == 'Junior') echo 'checked'; ?>>
                <label class="form-check-label" for="junior">Junior</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nivel_experiencia" id="senior" value="Senior" <?php if (isset($auditor['NivelExperiencia']) && $auditor['NivelExperiencia'] == 'Senior') echo 'checked'; ?>>
                <label class="form-check-label" for="senior">Senior</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nivel_experiencia" id="gerente" value="Gerente" <?php if (isset($auditor['NivelExperiencia']) && $auditor['NivelExperiencia'] == 'Gerente') echo 'checked'; ?>>
                <label class="form-check-label" for="gerente">Gerente</label>
            </div>
        </div>
        <div class="form-group">
            <label for="id_usuario">Usuario Asignado</label>
            <select class="form-control" id="id_usuario" name="id_usuario" required>
                <?php
                if ($usuarios_result->num_rows > 0) {
                    while ($usuario = $usuarios_result->fetch_assoc()) {
                        $selected = isset($auditor['IDusuario']) && $usuario['IDusuario'] == $auditor['IDusuario'] ? 'selected' : '';
                        echo "<option value='{$usuario['IDusuario']}' $selected>{$usuario['Nombre']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay usuarios disponibles</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i> Guardar Cambios</button>
        <a href="auditores.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </form>
</div>
<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
