<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
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
    $usuario_duplicado_sql = "SELECT COUNT(*) as total FROM clientes WHERE IDusuario = '$idusuario'";
    $usuario_duplicado_result = $conn->query($usuario_duplicado_sql);
    $usuario_duplicado = $usuario_duplicado_result->fetch_assoc();

    if ($usuario_duplicado['total'] > 0) {
        echo "<script>alert('El usuario seleccionado ya está asignado a otro cliente. Por favor, seleccione otro usuario.');</script>";
    } else {
        $sql = "INSERT INTO clientes (Nombre, Direccion, Telefono, Email, Idciudad, IDusuario, Persona_contacto_designada) 
                VALUES ('$nombre', '$direccion', '$telefono', '$email', '$idciudad', '$idusuario', '$persona_contacto_designada')";

        if ($conn->query($sql) === TRUE) {
            // Redireccionar a la página de agregar cliente con el parámetro de éxito
            header("Location: agregar_cliente.php?success=1");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
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
            background-color:  #a6bbd7;
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
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center; /* Centrar el mensaje */
        }
        .alert-success i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Agregar Cliente</h1>
    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
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
                        echo "<option value='{$usuario['IDusuario']}'>{$usuario['Nombre']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay usuarios disponibles</option>";
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
<!-- Scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>