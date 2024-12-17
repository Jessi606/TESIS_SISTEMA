<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener la lista de ciudades activas
$ciudades_sql = "SELECT idciudad, nombre FROM ciudades WHERE estado = 'activo'";
$ciudades_result = $conn->query($ciudades_sql);

// Obtener la lista de usuarios activos con rol de cliente (Idrol = 3 y Estado = 1)
$usuarios_sql = "SELECT IDusuario, Nombre FROM usuarios WHERE IDrol = 3 AND Estado = 1";
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
    $usuario_duplicado_sql = "SELECT COUNT(*) as total FROM clientes WHERE IDusuario = ?";
    $stmt_check = $conn->prepare($usuario_duplicado_sql);
    $stmt_check->bind_param("i", $idusuario);
    $stmt_check->execute();
    $usuario_duplicado_result = $stmt_check->get_result();
    $usuario_duplicado = $usuario_duplicado_result->fetch_assoc();

    if ($usuario_duplicado['total'] > 0) {
        echo "<script>alert('El usuario seleccionado ya está asignado a otro cliente. Por favor, seleccione otro usuario.');</script>";
    } else {
        $sql = "INSERT INTO clientes (Nombre, Direccion, Telefono, Email, Idciudad, IDusuario, Persona_contacto_designada) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql);
        $stmt_insert->bind_param("ssssiss", $nombre, $direccion, $telefono, $email, $idciudad, $idusuario, $persona_contacto_designada);

        if ($stmt_insert->execute()) {
            // Redireccionar a la página de agregar cliente con el parámetro de éxito
            header("Location: agregar_cliente.php?success=1");
            exit();
        } else {
            echo "Error: " . $stmt_insert->error;
        }
    }

    $stmt_check->close();
    $stmt_insert->close();
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #a6bbd7;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
            margin: auto;
            margin-top: 50px;
        }
        h1 {
            color: #000000;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-control {
            border-radius: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Agregar Cliente</h1>
    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success" role="alert">
        <i class="fas fa-check-circle"></i> Nuevo cliente agregado con éxito
    </div>
    <?php endif; ?>
    <form action="agregar_cliente.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="idciudad">Ciudad</label>
            <select class="form-control" id="idciudad" name="idciudad" required>
                <?php
                if ($ciudades_result->num_rows > 0) {
                    while($ciudad = $ciudades_result->fetch_assoc()) {
                        echo "<option value='{$ciudad['idciudad']}'>{$ciudad['nombre']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay ciudades activas disponibles</option>";
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
                        echo "<option value='{$usuario['IDusuario']}'>{$usuario['Nombre']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay usuarios activos disponibles</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="persona_contacto_designada">Persona de Contacto Designada</label>
            <input type="text" class="form-control" id="persona_contacto_designada" name="persona_contacto_designada" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Agregar Cliente</button>
        <a href="clientes.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </form>
</div>
</body>
</html>
