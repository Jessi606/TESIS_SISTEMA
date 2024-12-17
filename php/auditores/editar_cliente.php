<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener el ID del cliente desde la URL si está presente
$id_cliente = isset($_GET['id']) ? $_GET['id'] : '';

// Si se proporciona un ID de cliente, obtener los datos del cliente de la base de datos
if ($id_cliente != '') {
    // Consulta SQL para obtener los datos del cliente
    $cliente_sql = "SELECT * FROM clientes WHERE IDcliente = '$id_cliente'";
    $cliente_result = $conn->query($cliente_sql);

    // Verificar si se encontraron datos del cliente
    if ($cliente_result->num_rows > 0) {
        $cliente = $cliente_result->fetch_assoc();
    } else {
        echo "Cliente no encontrado";
        exit();
    }
}

// Obtener la lista de ciudades
$ciudades_sql = "SELECT idciudad, nombre FROM ciudades";
$ciudades_result = $conn->query($ciudades_sql);

// Obtener la lista de usuarios
$usuarios_sql = "SELECT IDusuario, Nombre FROM usuarios WHERE IDrol = 3"; // Filtrar solo los usuarios con el ID de rol de cliente (en este caso, 3)
$usuarios_result = $conn->query($usuarios_sql);

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $idciudad = $_POST['idciudad'];
    $idusuario = $_POST['idusuario'];
    $persona_contacto_designada = $_POST['persona_contacto_designada'];

    // Validar si el usuario seleccionado ya está asignado
    $usuario_duplicado_sql = "SELECT COUNT(*) as total FROM clientes WHERE IDusuario = '$idusuario' AND IDcliente != '$id_cliente'";
    $usuario_duplicado_result = $conn->query($usuario_duplicado_sql);
    $usuario_duplicado = $usuario_duplicado_result->fetch_assoc();

    if ($usuario_duplicado['total'] > 0) {
        echo "<script>alert('El usuario seleccionado ya está asignado a otro cliente. Por favor, seleccione otro usuario.');</script>";
    } else {
        $sql = "UPDATE clientes 
                SET Nombre = '$nombre', Direccion = '$direccion', Telefono = '$telefono', 
                Email = '$email', Idciudad = '$idciudad', IDusuario = '$idusuario', 
                Persona_contacto_designada = '$persona_contacto_designada' 
                WHERE IDcliente = '$id_cliente'";

        if ($conn->query($sql) === TRUE) {
            // Redirigir con un parámetro específico para edición
            header("Location: clientes.php?action=edit&success=1");
            exit();
        } else {
            echo "Error al actualizar cliente: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }

        .container {
            max-width: 800px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
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

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Editar Cliente</h1>
    <form action="editar_cliente.php?id=<?php echo $id_cliente; ?>" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['Nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente['Direccion']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['Telefono']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($cliente['Email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="idciudad">Ciudad</label>
            <select class="form-control" id="idciudad" name="idciudad" required>
                <?php
                if ($ciudades_result->num_rows > 0) {
                    while($ciudad = $ciudades_result->fetch_assoc()) {
                        $selected = $ciudad['idciudad'] == $cliente['Idciudad'] ? 'selected' : '';
                        echo "<option value='{$ciudad['idciudad']}' $selected>{$ciudad['nombre']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay ciudades disponibles</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="idusuario">Usuario Asignado</label>
            <select class="form-control" id="idusuario" name="idusuario" required>
                <?php
                if ($usuarios_result->num_rows > 0) {
                    while($usuario = $usuarios_result->fetch_assoc()) {
                        $selected = $usuario['IDusuario'] == $cliente['IDusuario'] ? 'selected' : '';
                        echo "<option value='{$usuario['IDusuario']}' $selected>{$usuario['Nombre']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay usuarios disponibles</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="persona_contacto_designada">Persona de Contacto Designada</label>
            <input type="text" class="form-control" id="persona_contacto_designada" name="persona_contacto_designada" value="<?php echo htmlspecialchars($cliente['Persona_contacto_designada']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
        <a href="clientes.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
    </form>
</div>
</body>
</html>
