<?php
session_start();
include 'conexion.php';

$con = conectarDB();

if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Función para registrar acciones en el log de auditoría de tareas
function registrarAuditoriaTarea($con, $idTarea, $accion, $detalles, $idUsuario) {
    $sql = "INSERT INTO auditoria_tarea (IdAuditoriatarea, Idtarea, Accion, Detalles, FechaHora, IDusuario) 
            VALUES (NULL, ?, ?, ?, current_timestamp(), ?)";
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        die("Error en prepare: " . $con->error);
    }
    $stmt->bind_param('isss', $idTarea, $accion, $detalles, $idUsuario);
    if (!$stmt->execute()) {
        die("Error en execute: " . $stmt->error);
    }
}

// Obtener ID de usuario de la sesión (simulado)
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1; // ID de usuario simulado
}
$idUsuario = $_SESSION['usuario_id'];

// Manejo del formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTarea = $_POST['idTarea'];
    $accion = $_POST['accion'];
    $detalles = $_POST['detalles'];
    registrarAuditoriaTarea($con, $idTarea, $accion, $detalles, $idUsuario);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Consulta SQL para seleccionar todos los registros de auditoría de tareas con el nombre del usuario
$sql = "SELECT at.IdAuditoriatarea, at.Idtarea, at.Accion, at.Detalles, at.FechaHora, u.Nombre as NombreUsuario 
        FROM auditoria_tarea at 
        JOIN usuarios u ON at.IDusuario = u.IDusuario 
        ORDER BY at.FechaHora DESC";
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
    <title>Registro de Auditoría de Tareas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #a6bbd7; color: #333; }
        .container { max-width: 1200px; margin: auto; margin-top: 50px; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1); }
        h1, h2 { color: #343a40; text-align: center; margin-bottom: 30px; }
        .table th { background-color: #343a40; color: #fff; }
        .table td { vertical-align: middle; }
        .table-striped tbody tr:nth-of-type(odd) td { background-color: #f8f9fa; }
        .table-striped tbody tr:nth-of-type(even) td { background-color: #e9ecef; }
        form { max-width: 600px; margin: auto; }
        .form-group { margin-bottom: 20px; }
        .form-group label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro de Auditoría de Tareas</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Registro</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Detalles</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['IdAuditoriatarea']) ?></td>
                            <td><?= htmlspecialchars($row['NombreUsuario']) ?></td>
                            <td><?= htmlspecialchars($row['Accion']) ?></td>
                            <td><?= htmlspecialchars($row['Detalles']) ?></td>
                            <td><?= htmlspecialchars($row['FechaHora']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="tarea.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a Tareas</a>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>