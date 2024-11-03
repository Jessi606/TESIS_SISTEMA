<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Función para registrar acciones en la auditoría
function registrarAuditoria($con, $idRequerimiento, $accion, $detalles, $idUsuario) {
    $sql = "INSERT INTO auditoria_requerimientos (Idrequerimiento, Accion, Detalles, FechaHora, IDusuario) VALUES (?, ?, ?, current_timestamp(), ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('issi', $idRequerimiento, $accion, $detalles, $idUsuario);
    $stmt->execute();
}

// Obtener ID de usuario de la sesión
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
$idUsuario = $_SESSION['usuario_id'];

// Manejar la acción de Aceptar
if (isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == 'aceptar') {
    $id = $_GET['id'];
    $sql = "UPDATE requerimientos SET Estado_requerimiento = 'Aceptado' WHERE Idrequerimiento = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        registrarAuditoria($con, $id, 'Aceptar', 'El requerimiento fue aceptado.', $idUsuario);
    }
    // Redireccionar a esta misma página después de actualizar el estado
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Manejar la acción de Regresar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comentario']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $comentario = $_POST['comentario'];
    $sql = "UPDATE requerimientos SET Estado_requerimiento = 'Devuelto', Comentario = ? WHERE Idrequerimiento = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('si', $comentario, $id);
    if ($stmt->execute()) {
        registrarAuditoria($con, $id, 'Regresar', "El requerimiento fue devuelto con el comentario: $comentario", $idUsuario);
    }
    // Redireccionar a esta misma página después de actualizar el estado
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Consulta SQL para seleccionar todos los requerimientos con información del proyecto
$sql = "SELECT r.*, p.Descripcion AS NombreProyecto
        FROM requerimientos r
        LEFT JOIN proyecto_auditoria p ON r.Idproyecto = p.Idproyecto";

$query = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requerimientos de Auditoría</title>
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
            max-width: 2100px;
            margin: auto;
            border-radius: 10px; /* Borde redondeado */
            margin-top: 50px; /* Espaciado desde arriba */
            background-color: #fff; /* Fondo blanco */
            padding: 20px; /* Espaciado interno */
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1); /* Sombra */
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
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .table td {
            background-color: #f8f9fa;
        }
        .button-container {
            display: flex;
            gap: 10px; /* Espaciado entre los botones */
        }
        .button-container a {
            flex: 1; /* Distribuye el espacio disponible entre los botones */
        }
    </style>
    <script>
        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este requerimiento?");
        }

        function solicitarComentario(id) {
            var comentario = prompt("Por favor ingrese el comentario para regresar el requerimiento:");
            if (comentario !== null && comentario.trim() !== "") {
                document.getElementById('comentario').value = comentario;
                document.getElementById('id').value = id;
                document.getElementById('form-regresar').submit();
            } else {
                alert("Debe ingresar un comentario.");
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Requerimientos de Auditoría</h1>
        <form action="crear_requerimiento.php" method="GET" class="mb-4 d-flex align-items-start">
            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-plus"></i> Agregar Requerimiento</button>
            <a href="registro_audit_requerimiento.php" class="btn btn-secondary mr-2"><i class="fas fa-file-alt"></i> Ver Registro de Auditoría</a>
            <a href="/TESIS_SISTEMA/manuales_usuario/Gestión de Auditoría-Requerimiento.pdf" target="_blank" class="btn btn-secondary"><i class="fas fa-question-circle"></i> Ayuda</a>
        </form>
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
                        <th>Evidencia</th> <!-- Columna de Evidencia agregada -->
                        <th>Nombre del Proyecto</th> <!-- Nueva columna para el nombre del proyecto -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($query)): ?>
                        <tr>
                            <td><?= $row['Idrequerimiento'] ?></td>
                            <td><?= $row['Titulo'] ?></td>
                            <td><?= $row['Descripcion'] ?></td>
                            <td><?= $row['Solicitante'] ?></td>
                            <td><?= $row['Fecha_creacion'] ?></td>
                            <td><?= $row['Fecha_vencimiento'] ?></td>
                            <td>
                                <?php 
                                    $estado = $row['Estado_requerimiento'];
                                    $badge_class = '';
                                    switch($estado) {
                                        case 'Enviado':
                                            $badge_class = 'badge-primary';
                                            break;
                                        case 'Vencido':
                                            $badge_class = 'badge-danger';
                                            break;
                                        case 'Devuelto':
                                            $badge_class = 'badge-warning';
                                            break;
                                        case 'Aceptado':
                                            $badge_class = 'badge-success';
                                            break;
                                        default:
                                            $badge_class = 'badge-secondary';
                                    }
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= $estado ?></span>
                            </td>
                            <td><?= $row['Remitente'] ?></td>
                            <td><?= $row['Comentario'] ?></td>
                            <td>
                                <div class="button-container">
                                    <!-- Botón de Modificar -->
                                    <a href="update_requerimiento.php?id=<?= $row['Idrequerimiento'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Modificar</a>

                                    <!-- Botón de Eliminar -->
                                    <a href="eliminar_requerimiento.php?id=<?= $row['Idrequerimiento'] ?>" class="btn btn-danger" onclick="return confirmarEliminacion();"><i class="fas fa-trash"></i> Eliminar</a>

                                    <!-- Botón de Aceptar -->
                                    <a href="?action=aceptar&id=<?= $row['Idrequerimiento'] ?>" class="btn btn-success"><i class="fas fa-check"></i> Aceptar</a>

                                    <!-- Botón de Regresar -->
                                    <button type="button" class="btn btn-primary" onclick="solicitarComentario(<?= $row['Idrequerimiento'] ?>)"><i class="fas fa-undo"></i> Regresar</button>
                                </div>
                            </td>
                            <td>
                                    <?php 
                                        // Obtener el nombre del archivo de evidencia
                                        $nombreArchivo = $row['Evidencia'];
                                        
                                        // Ruta completa al archivo de evidencia
                                        $rutaArchivo = '../uploads/' . $nombreArchivo;
                                        
                                        // Verificar si el archivo de evidencia existe
                                        if (file_exists($rutaArchivo)) {
                                            echo '<a href="ver_evidencia.php?idRequerimiento=' . $row['Idrequerimiento'] . '" class="btn btn-sm btn-info"><i class="fas fa-file"></i> Ver Evidencia</a>';
                                        } else {
                                            echo 'No hay evidencia adjunta';
                                        }
                                    ?>
                                </td>
                                <td><?= $row['NombreProyecto'] ?></td> <!-- Mostrar el nombre o descripción del proyecto -->
                            </tr>
                        <?php endwhile; ?> 
                    </tbody>    
            </table>
        </div>
        <!-- Formulario para regresar el requerimiento -->
        <form id="form-regresar" method="POST" action="" style="display: none;">
            <input type="hidden" id="id" name="id">
            <input type="hidden" id="comentario" name="comentario">
        </form>
        <a href="admin.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
    </div>
    <!-- Integrar Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
