<?php
// Incluir el archivo de conexión
include('conexion.php');

// Conectar a la base de datos
$con = conectarDB();

if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Consulta SQL para seleccionar todos los roles
$sql = "SELECT * FROM roles";
$query = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles</title>
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
            margin: auto;
            margin-top: 50px;
        }
        h1, .titulo-roles {
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
        /* Estilo para las filas de roles predeterminados y anulados */
        .system-role, .disabled-role {
            background-color: #e9ecef;
            color: #6c757d;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <form action="insert_rol.php" method="POST" class="mb-4">
            <h1 class="text-center">Registrar Roles</h1>
            <div class="form-group">
                <input type="text" name="Descripcion" class="form-control" placeholder="Descripción">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar</button>
            <a href="/TESIS_SISTEMA/Manuales de usuario/Gestión de Usuarios_roles_actualizado.pdf" target="_blank" class="btn btn-secondary"><i class="fas fa-question-circle"></i> Ayuda</a>
        </form>

        <!-- Mostrar el mensaje de éxito si el rol fue anulado -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success" id="success-message">
                El rol ha sido anulado exitosamente.
            </div>
        <?php endif; ?>

        <div>
            <h2 class="titulo-roles">Roles Registrados</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <?php
                        // Verificar si el rol es predeterminado o está anulado
                        $isSystemRole = $row['Idrol'] <= 3;
                        $isDisabledRole = $row['estado'] === 'anulado';
                        ?>
                        <tr class="<?= $isSystemRole ? 'system-role' : ($isDisabledRole ? 'disabled-role' : '') ?>">
                            <td><?= htmlspecialchars($row['Idrol']) ?></td>
                            <td><?= htmlspecialchars($row['Descripcion']) ?></td>
                            <td><?= htmlspecialchars($row['estado']) ?></td>
                            <td>
                                <?php if (!$isSystemRole && !$isDisabledRole): ?>
                                    <a href="update_roles.php?Idrol=<?= urlencode($row['Idrol']) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Modificar</a>
                                    <a href="anular_rol.php?Idrol=<?= urlencode($row['Idrol']) ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Estás seguro de que deseas anular este rol?');"><i class="fas fa-ban"></i> Anular</a>
                                <?php else: ?>
                                    <span class="text-muted"><?= $isSystemRole ? 'Predeterminado del sistema' : 'Anulado' ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
    </div>

    <!-- Integra Bootstrap JS (opcional, si es necesario) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Función para ocultar el mensaje de éxito después de unos segundos -->
    <script>
        setTimeout(function() {
            var successMessage = document.getElementById("success-message");
            if (successMessage) {
                successMessage.style.display = "none";
            }
        }, 5000); // 5000 ms = 5 segundos
    </script>
</body>
</html>
