<?php
session_start();
include('conexion.php');

// Establecer la conexión a la base de datos
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

// Función para registrar auditoría
function registrarAuditoria($conn, $usuario_id, $accion, $detalles, $tarea_id) {
    $sql_auditoria = "INSERT INTO auditoria_tarea (IDusuario, Accion, Detalles, FechaHora, Idtarea) 
                      VALUES (?, ?, ?, NOW(), ?)";
    $stmt_auditoria = $conn->prepare($sql_auditoria);
    $stmt_auditoria->bind_param("issi", $usuario_id, $accion, $detalles, $tarea_id);
    $stmt_auditoria->execute();
}

// Verificar si se ha proporcionado un ID de tarea a editar
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_tarea = $_GET['id'];

    // Consultar la información de la tarea a editar
    $sql_select = "SELECT * FROM tareas WHERE Idtarea = $id_tarea";
    $result = mysqli_query($conn, $sql_select);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
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
    $sql_update = "UPDATE tareas SET Descripcion = '$descripcion', Fecha_inicio = '$fecha_inicio', 
                    Fecha_fin = '$fecha_fin', Prioridad = '$prioridad', Estado_tarea = '$estado_tarea', 
                    Responsable = '$responsable' 
                    WHERE Idtarea = $id_tarea";
    if (mysqli_query($conn, $sql_update)) {
        // Registrar la acción de auditoría
        $usuario_id = $_SESSION['usuario_id']; // Asegurarse de que $_SESSION esté definida
        $accion_auditoria = "MODIFICAR TAREA";
        $detalles_auditoria = "Descripción: $descripcion, Fecha de inicio: $fecha_inicio, Fecha de fin: $fecha_fin, Prioridad: $prioridad, Estado: $estado_tarea, Responsable: $responsable";
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
                <option value="En proceso" <?php if ($row['Estado_tarea'] == 'En proceso') echo 'selected'; ?>>En proceso</option>
                <option value="Completada" <?php if ($row['Estado_tarea'] == 'Completada') echo 'selected'; ?>>Completada</option>
            </select>
        </div>
        <div class="form-group">
            <label for="responsable">Responsable:</label>
            <select class="form-control" id="responsable" name="responsable" required>
                <?php
                // Consulta SQL para obtener solo los auditores como opciones de responsables
                $sql_auditores = "SELECT IDusuario, Nombre FROM usuarios WHERE Idrol = 2";
                $result_auditores = mysqli_query($conn, $sql_auditores);
                // Iterar sobre los resultados y mostrar cada auditor como una opción en el select
                while ($auditor = mysqli_fetch_assoc($result_auditores)) {
                    echo "<option value='{$auditor['Nombre']}' ";
                    if ($auditor['Nombre'] == $row['Responsable']) {
                        echo 'selected';
                    }
                    echo ">{$auditor['Nombre']}</option>";
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
