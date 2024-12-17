<?php
// Establecer la zona horaria de Paraguay (Asunción)
date_default_timezone_set('America/Asuncion');

// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Función para registrar acciones en la auditoría de proyectos
function registrarAuditoriaProyecto($con, $idProyecto, $detalles, $idUsuario) {
    $sql = "INSERT INTO auditoria_proyectos (IdAuditoria, Idproyecto, Detalles, FechaHora, IDusuario) VALUES (NULL, ?, ?, current_timestamp(), ?)";
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        die("Error en prepare: " . $con->error);
    }
    $stmt->bind_param('iss', $idProyecto, $detalles, $idUsuario);
    if (!$stmt->execute()) {
        die("Error en execute: " . $stmt->error);
    }
}

// Obtener ID de usuario de la sesión (simulado)
session_start();
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1; // ID de usuario simulado
}
$idUsuario = $_SESSION['usuario_id'];

// Manejo del formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProyecto = $_POST['idProyecto'];
    $detalles = $_POST['detalles'];

    // Llamar a la función para registrar la auditoría
    registrarAuditoriaProyecto($con, $idProyecto, $detalles, $idUsuario);

    // Redirigir o mostrar un mensaje de éxito
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Consulta SQL para seleccionar todos los registros de auditoría de proyectos con el nombre del usuario
$sql = "SELECT ap.IdAuditoria, ap.Idproyecto, ap.Detalles, ap.FechaHora, u.Nombre as NombreUsuario 
        FROM auditoria_proyectos ap 
        JOIN usuarios u ON ap.IDusuario = u.IDusuario 
        ORDER BY ap.FechaHora DESC";
$query = mysqli_query($con, $sql);
if ($query === false) {
    die("Error en consulta: " . $con->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Auditoría de Proyectos</title>
    <!-- Integra Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }
        .container {
            max-width: 1800px;
            margin: auto;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #343a40;
            text-align: center;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .table td {
            vertical-align: middle;
        }
        .table-striped tbody tr:nth-of-type(odd) td {
            background-color: #f8f9fa;
        }
        .table-striped tbody tr:nth-of-type(even) td {
            background-color: #e9ecef;
        }
        form {
            max-width: 600px;
            margin: auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro de Auditoría de Proyectos</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Registro</th>
                        <th>Usuario</th>
                        <th>Detalles</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['IdAuditoria']) ?></td>
                            <td><?= htmlspecialchars($row['NombreUsuario']) ?></td>
                            <td><?= htmlspecialchars($row['Detalles']) ?></td>
                            <td><?= htmlspecialchars($row['FechaHora']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="proyectos_auditoria.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a Proyectos</a>
        </div>
    </div>
    <!-- Integra Bootstrap JS (opcional, si es necesario) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
