<?php
session_start();
include('conexion.php');

$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Función para registrar auditoría y mostrar el nombre del responsable en detalles
function registrarAuditoria($conn, $usuario_id, $accion, $detalles, $tarea_id) {
    // Reemplazar el ID del responsable con el nombre en los detalles si es necesario
    if (strpos($detalles, 'Responsable:') !== false) {
        preg_match('/Responsable: (\d+)/', $detalles, $matches);
        if (!empty($matches[1])) {
            $responsable_id = $matches[1];
            $sql_responsable = "SELECT Nombre FROM usuarios WHERE IDusuario = ?";
            $stmt_responsable = $conn->prepare($sql_responsable);
            $stmt_responsable->bind_param("i", $responsable_id);
            $stmt_responsable->execute();
            $result_responsable = $stmt_responsable->get_result();
            if ($result_responsable->num_rows > 0) {
                $row_responsable = $result_responsable->fetch_assoc();
                $responsable_nombre = $row_responsable['Nombre'];
                $detalles = str_replace("Responsable: $responsable_id", "Responsable: $responsable_nombre", $detalles);
            }
        }
    }

    $sql_auditoria = "INSERT INTO auditoria_tarea (IDusuario, Accion, Detalles, FechaHora, Idtarea) 
                      VALUES (?, ?, ?, NOW(), ?)";
    $stmt_auditoria = $conn->prepare($sql_auditoria);
    $stmt_auditoria->bind_param("issi", $usuario_id, $accion, $detalles, $tarea_id);
    $stmt_auditoria->execute();
}

// Verificar si se ha proporcionado un ID de tarea a editar
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_tarea = $_GET['id'];

    // Consultar la información de la tarea y obtener el nombre del responsable actual
    $sql_select = "SELECT t.*, u.Nombre as NombreResponsable FROM tareas t LEFT JOIN usuarios u ON t.Responsable = u.IDusuario WHERE t.Idtarea = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("i", $id_tarea);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "No se encontró la tarea a editar.";
        exit;
    }
}

// Manejar la acción de editar tarea si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_tarea'])) {
    $id_tarea = $_POST['id_tarea'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $prioridad = $_POST['prioridad'];
    $estado_tarea = $_POST['estado_tarea'];
    $responsable = $_POST['responsable'];

    // Actualizar la tarea en la base de datos
    $sql_update = "UPDATE tareas SET Descripcion = ?, Fecha_inicio = ?, Fecha_fin = ?, Prioridad = ?, Estado_tarea = ?, Responsable = ? WHERE Idtarea = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssii", $descripcion, $fecha_inicio, $fecha_fin, $prioridad, $estado_tarea, $responsable, $id_tarea);
    
    if ($stmt_update->execute()) {
        // Registrar la acción de auditoría con el nombre del responsable en lugar de su ID
        $usuario_id = $_SESSION['usuario_id'];
        $accion_auditoria = "MODIFICAR TAREA";
        $sql_responsable_nombre = "SELECT Nombre FROM usuarios WHERE IDusuario = ?";
        $stmt_responsable_nombre = $conn->prepare($sql_responsable_nombre);
        $stmt_responsable_nombre->bind_param("i", $responsable);
        $stmt_responsable_nombre->execute();
        $result_responsable_nombre = $stmt_responsable_nombre->get_result();
        $nombre_responsable = $result_responsable_nombre->fetch_assoc()['Nombre'];
        $detalles_auditoria = "Descripción: $descripcion, Fecha de inicio: $fecha_inicio, Fecha de fin: $fecha_fin, Prioridad: $prioridad, Estado: $estado_tarea, Responsable: $nombre_responsable";
        registrarAuditoria($conn, $usuario_id, $accion_auditoria, $detalles_auditoria, $id_tarea);

        // Redireccionar a la página principal después de editar la tarea
        header("Location: tarea.php");
        exit;
    } else {
        echo "Error al editar la tarea: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #a6bbd7; }
        .container { max-width: 1500px; margin: auto; border-radius: 10px; margin-top: 50px; background-color: #fff; padding: 20px; box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .btn-primary { background-color: #007bff; border-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; border-color: #0056b3; }
        .alert { margin-top: 20px; }
        h2 { text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Editar Tarea de Auditoría</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="hidden" name="id_tarea" value="<?php echo $row['Idtarea']; ?>">
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $row['Descripcion']; ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $row['Fecha_inicio']; ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_fin">Fecha de Fin:</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $row['Fecha_fin']; ?>" required>
        </div>
        <div class="form-group">
            <label for="prioridad">Prioridad:</label>
            <select class="form-control" id="prioridad" name="prioridad" required>
                <option value="Alta" <?php if ($row['Prioridad'] == 'Alta') echo 'selected'; ?>>Alta</option>
                <option value="Media" <?php if ($row['Prioridad'] == 'Media') echo 'selected'; ?>>Media</option>
                <option value="Baja" <?php if ($row['Prioridad'] == 'Baja') echo 'selected'; ?>>Baja</option>
            </select>
        </div>
        <div class="form-group">
            <label for="estado_tarea">Estado:</label>
            <select class="form-control" id="estado_tarea" name="estado_tarea" required>
                <option value="Pendiente" <?php if ($row['Estado_tarea'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                <option value="En Progreso" <?php if ($row['Estado_tarea'] == 'En Progreso') echo 'selected'; ?>>En Progreso</option>
                <option value="Completada" <?php if ($row['Estado_tarea'] == 'Completada') echo 'selected'; ?>>Completada</option>
            </select>
        </div>
        <div class="form-group">
            <label for="responsable">Responsable:</label>
            <select class="form-control" id="responsable" name="responsable" required>
                <?php
                $sql_auditores = "SELECT IDusuario, Nombre FROM usuarios WHERE Idrol = 2";
                $result_auditores = $conn->query($sql_auditores);
                while ($auditor = $result_auditores->fetch_assoc()) {
                    $selected = ($auditor['IDusuario'] == $row['Responsable']) ? 'selected' : '';
                    echo "<option value='{$auditor['IDusuario']}' $selected>{$auditor['Nombre']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="editar_tarea">Guardar Cambios</button>
    </form>
    <a href="tarea.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver a la lista de tareas</a>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
