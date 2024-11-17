<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Consulta SQL para seleccionar todos los usuarios con sus roles
$sql = "SELECT u.IDusuario, u.Nombre, u.Password, r.Descripcion AS Rol, u.Estado
        FROM usuarios u
        LEFT JOIN roles r ON u.Idrol = r.Idrol";
$query = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            margin: auto;
            margin-top: 50px;
        }
        h1, h2 {
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
        .alert-success {
            margin-bottom: 20px;
        }
        /* Estilos para usuarios inactivos */
        .usuario-inactivo {
            background-color: #e0e0e0; /* Gris claro */
            color: #999; /* Texto gris oscuro */
        }
        .usuario-inactivo .btn-warning {
            pointer-events: none; /* Deshabilita el botón */
            opacity: 0.5; /* Hacer el botón semitransparente */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Registrar Usuarios</h1>
        <form action="insert_usuario.php" method="POST" class="mb-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar</button>
            <a href="/TESIS_SISTEMA/manuales_usuario/Gestion de Usuarios_usuarios.pdf" target="_blank" class="btn btn-secondary"><i class="fas fa-question-circle"></i> Ayuda</a>
        </form>

        <div>
            <h2>Usuarios Registrados</h2>
            <table class="table table-bordered table-striped">
                <thead> 
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Contraseña</th>
                        <th>Roles</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <tr class="<?= $row['Estado'] ? '' : 'usuario-inactivo' ?>">
                            <td><?= $row['IDusuario'] ?></td>
                            <td><?= $row['Nombre'] ?></td>
                            <td><?= $row['Password'] ?></td>
                            <td><?= $row['Rol'] ?></td>
                            <td><?= $row['Estado'] ? 'Activo' : 'Inactivo' ?></td>
                            <td>
                                <a href="update_usuario.php?IDusuario=<?= $row['IDusuario'] ?>" 
                                   class="btn btn-sm btn-warning <?= $row['Estado'] ? '' : 'disabled' ?>">
                                   <i class="fas fa-edit"></i> Modificar</a>
                                <?php if ($row['Estado']): ?>
                                    <a href="cambiar_estado.php?IDusuario=<?= $row['IDusuario'] ?>&estado=0" class="btn btn-sm btn-danger"><i class="fas fa-ban"></i> Desactivar</a>
                                <?php else: ?>
                                    <a href="cambiar_estado.php?IDusuario=<?= $row['IDusuario'] ?>&estado=1" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Activar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
