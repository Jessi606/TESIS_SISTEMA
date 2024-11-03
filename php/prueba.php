<?php
// Incluir el archivo de conexión
include('conexion.php');

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Consulta SQL para seleccionar todas las personas
$sql = "SELECT * FROM persona";
$query = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personas</title>
    <!-- Integra Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 1200px;
            background-color: #fff;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
        }
        h1 {
            color: #2c3e50;
        }
        .btn-custom {
            background-color: #2ecc71;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #27ae60;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        table {
            width: 100%;
        }
        th {
            background-color: #34495e;
            color: white;
        }
        td {
            background-color: #ecf0f1;
        }
        .form-control {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Gestión de Personas</h1>
        
        <form action="insert_persona.php" method="POST" class="mb-4">
            <div class="form-row">
                <div class="col-md-4">
                    <input type="text" name="Tipo_doc" class="form-control" placeholder="Tipo de Documento">
                </div>
                <div class="col-md-4">
                    <input type="text" name="Nro_doc" class="form-control" placeholder="Número de Documento">
                </div>
                <div class="col-md-4">
                    <input type="text" name="Nombre" class="form-control" placeholder="Nombre">
                </div>
                <div class="col-md-4">
                    <input type="text" name="Apellido" class="form-control" placeholder="Apellido">
                </div>
                <div class="col-md-4">
                    <input type="text" name="Direccion" class="form-control" placeholder="Dirección">
                </div>
                <div class="col-md-4">
                    <input type="date" name="Fecha_nac" class="form-control">
                </div>
                <div class="col-md-4">
                    <input type="date" name="Fecha_alta" class="form-control">
                </div>
                <div class="col-md-4">
                    <select name="Estado" class="form-control">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-custom btn-block mt-4">Agregar Persona</button>
                </div>
            </div>
        </form>

        <h2 class="titulo-personas mb-4">Personas Registradas</h2>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo Documento</th>
                    <th>Número Documento</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Dirección</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Fecha de Alta</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($query)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['tipo_doc'] ?></td>
                        <td><?= $row['nro_doc'] ?></td>
                        <td><?= $row['nombre'] ?></td>
                        <td><?= $row['apellido'] ?></td>
                        <td><?= $row['direccion'] ?></td>
                        <td><?= $row['fecha_nac'] ?></td>
                        <td><?= $row['fecha_alta'] ?></td>
                        <td><?= $row['estado'] ?></td>
                        <td>    
                            <a href="delete_persona.php?Id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="admin.php" class="btn btn-custom"><i class="fas fa-arrow-left"></i> Volver al menú principal</a>
    </div>

    <!-- Integra Bootstrap JS (opcional, si es necesario) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
