<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener la lista de usuarios auditores activos
$usuarios_auditores_sql = "SELECT IDusuario, Nombre FROM usuarios WHERE IDrol = 2 AND Estado = 1"; // Filtrar solo los usuarios con el rol de auditor (IDrol = 2) y que estén activos (Estado = 1)
$usuarios_auditores_result = $conn->query($usuarios_auditores_sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Auditor</title>
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
    <h1>Agregar Auditor</h1>
    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> Nuevo auditor agregado con éxito
    </div>
    <?php endif; ?>
    <form action="proceso_agregar_auditor.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" required>
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
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>
        <div class="form-group">
            <label>Nivel de Experiencia</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="gerente" name="nivel_experiencia" value="Gerente">
                <label class="form-check-label" for="gerente">Gerente</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="senior" name="nivel_experiencia" value="Senior">
                <label class="form-check-label" for="senior">Senior</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="junior" name="nivel_experiencia" value="Junior">
                <label class="form-check-label" for="junior">Junior</label>
            </div>
        </div>
        <div class="form-group">
            <label for="id_usuario">Usuario Asignado</label>
            <select class="form-control" id="id_usuario" name="id_usuario" required>
                <?php
                if ($usuarios_auditores_result->num_rows > 0) {
                    while ($usuario = $usuarios_auditores_result->fetch_assoc()) {
                        echo "<option value='{$usuario['IDusuario']}'>{$usuario['Nombre']}</option>";
                    }
                } else {
                    echo "<option value=''>No hay usuarios auditores activos disponibles</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Agregar Auditor</button>
        <a href="auditores.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </form>
</div>
</body>
</html>
