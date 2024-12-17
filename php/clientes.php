<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php');

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }

        .container {
            max-width: 1880px;
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
            margin-top: 20px;
        }

        .table th {
            background-color: #343a40;
            color: #fff;
        }

        .anulado {
            background-color: #f0e68c;
            color: #888;
        }

        .btn[disabled] {
            cursor: not-allowed;
            opacity: 0.5;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Registrar Clientes</h1>

    <!-- Mostrar mensaje de éxito o error -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Éxito!</strong> El cliente ha sido anulado correctamente.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Error!</strong> No se pudo realizar la acción solicitada.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="d-flex mb-3">
        <a href="agregar_cliente.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Agregar</a>
        <a href="/TESIS_SISTEMA/Manuales de usuario/Gestión de Usuarios_clientes_actualizado.pdf" target="_blank" class="btn btn-secondary"><i class="fas fa-question-circle"></i> Ayuda</a>
    </div>

    <h2 class="mt-3">Clientes Registrados</h2>
    <table class="table table-striped mt-3">
        <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Ciudad</th>
            <th>Persona de Contacto Designada</th>
            <th>Usuario Asignado</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT clientes.Idcliente, clientes.Nombre, clientes.Direccion, clientes.Telefono, clientes.Email, 
                       ciudades.nombre AS Ciudad, usuarios.Nombre AS Usuario, clientes.Persona_contacto_designada, clientes.estado
                FROM clientes
                LEFT JOIN ciudades ON clientes.Idciudad = ciudades.idciudad
                LEFT JOIN usuarios ON clientes.IDusuario = usuarios.IDusuario";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $estado = htmlspecialchars($row['estado']);
                $claseAnulado = $estado == 'anulado' ? 'anulado' : '';

                echo "<tr class='{$claseAnulado}'>
                        <td>{$row['Idcliente']}</td>
                        <td>{$row['Nombre']}</td>
                        <td>{$row['Direccion']}</td>
                        <td>{$row['Telefono']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['Ciudad']}</td>
                        <td>{$row['Persona_contacto_designada']}</td>
                        <td>{$row['Usuario']}</td>
                        <td>{$estado}</td>
                        <td>";

                if ($estado == 'activo') {
                    echo "<a href='editar_cliente.php?id={$row['Idcliente']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Editar</a>
                          <a href='anular_cliente.php?id={$row['Idcliente']}' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de que deseas anular este cliente?\");'><i class='fas fa-ban'></i> Anular</a>";
                } else {
                    echo "<button class='btn btn-secondary btn-sm' disabled><i class='fas fa-ban'></i> Anulado</button>";
                }

                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No hay clientes registrados.</td></tr>";
        }

        $conn->close();
        ?>
        </tbody>
    </table>
    <a href="admin.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
