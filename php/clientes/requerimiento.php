<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener ID de usuario de la sesión
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
$idUsuario = $_SESSION['usuario_id'];

// Consulta SQL para seleccionar requerimientos asignados al usuario logueado
$sql = "SELECT r.*, p.Descripcion AS NombreProyecto
        FROM requerimientos r
        LEFT JOIN proyecto_auditoria p ON r.Idproyecto = p.Idproyecto
        WHERE r.Remitente = (SELECT Nombre FROM usuarios WHERE IDusuario = ?)";

$stmt = $con->prepare($sql);
$stmt->bind_param('i', $idUsuario);
$stmt->execute();
$query = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requerimientos de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #a6bbd7; color: #333; }
        .container { max-width: 2100px; margin: auto; border-radius: 10px; margin-top: 50px; background-color: #fff; padding: 20px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1); }
        h1, h2 { color: #000; }
        .table th { background-color: #343a40; color: #fff; }
        .table td { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Requerimientos de Auditoría</h1>
        <div>
            <h2>Requerimientos Registrados</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Solicitante</th>
                        <th>Fecha de Creación</th>
                        <th>Fecha de Vencimiento</th>
                        <th>Estado</th>
                        <th>Remitente</th>
                        <th>Comentario</th>
                        <th>Acciones</th>
                        <th>Evidencia</th>
                        <th>Nombre del Proyecto</th>
                    </tr>
                </thead>
                <tbody>
    <?php if ($query->num_rows > 0): ?>
        <?php while ($row = $query->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Idrequerimiento']) ?></td>
                <td><?= htmlspecialchars($row['Titulo']) ?></td>
                <td><?= htmlspecialchars($row['Descripcion']) ?></td>
                <td><?= htmlspecialchars($row['Solicitante']) ?></td>
                <td><?= htmlspecialchars($row['Fecha_creacion']) ?></td>
                <td><?= htmlspecialchars($row['Fecha_vencimiento']) ?></td>
                <td>
                    <span class="badge <?= ($row['Estado_requerimiento'] == 'Enviado') ? 'badge-primary' : 'badge-secondary'; ?>">
                        <?= htmlspecialchars($row['Estado_requerimiento']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($row['Remitente']) ?></td>
                <td><?= htmlspecialchars($row['Comentario']) ?></td>
                <td>
                    <?php if ($row['Estado_requerimiento'] !== 'Aceptado'): ?>
                        <a href="cargar_evidencia_formulario.php?idRequerimiento=<?= $row['Idrequerimiento'] ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Cargar Evidencia
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Acciones no disponibles</span>
                    <?php endif; ?>
                </td>
                <td>
                <a href="ver_evidencia.php?idRequerimiento=<?= $row['Idrequerimiento'] ?>" class="btn btn-sm btn-info"><i class="fas fa-file"></i> Ver Evidencia</a>
                <td><?= $row['NombreProyecto'] ?></td> <!-- Mostrar el nombre o descripción del proyecto -->
            </td>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center">No tiene requerimientos asignados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
                        </table>
        </div>
        <a href="cliente.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
    </div>
</body>
</html>
