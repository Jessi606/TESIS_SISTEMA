<?php
// Incluir el archivo de conexión
include('conexion.php');

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Consulta SQL para seleccionar todas las ciudades
$sql = "SELECT * FROM ciudades";
$query = mysqli_query($con, $sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciudades</title>
    <!-- Integra Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Integra Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Estilos personalizados -->
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
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
            margin: auto; /* Para centrar en la página */
            margin-top: 50px;
        }
        h1, .titulo-ciudades {
            color: #000;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .table td {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <form action="insert_ciudad.php" method="POST" class="mb-4">
            <h1 class="text-center">Registrar Ciudades</h1>
            <div class="form-group">
                <input type="text" name="Nombre" class="form-control" placeholder="Nombre de la ciudad">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar</button>
        </form>

        <div>
            <h2 class="titulo-ciudades">Ciudades Registradas</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Ciudad</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <tr>
                            <td><?= $row['Idciudad'] ?></td>
                            <td><?= $row['Nombre'] ?></td>
                            <td>    
                                <a href="delete_ciudad.php?Idciudad=<?= $row['Idciudad'] ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="auditor.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
    </div>

    <!-- Integra Bootstrap JS (opcional, si es necesario) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
