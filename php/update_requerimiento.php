<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("conexion.php");
$conn = conectarDB();

// Obtener el ID del requerimiento a actualizar
if (!isset($_GET['Idrequerimiento'])) {
    echo "ID de requerimiento no especificado.";
    exit();
}

$idRequerimiento = $_GET['Idrequerimiento'];

// Obtener los datos actuales del requerimiento
$sql = "SELECT * FROM requerimientos WHERE Idrequerimiento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRequerimiento);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "El requerimiento con ID $idRequerimiento no existe.";
    exit();
}

$row = $result->fetch_assoc();

// Obtener los remitentes con rol de cliente
$sqlRemitentes = "SELECT Nombre FROM usuarios WHERE Idrol = 3"; // 3 representa el rol de cliente
$resultRemitentes = $conn->query($sqlRemitentes);

// Obtener la lista de proyectos
$sqlProyectos = "SELECT Idproyecto, Descripcion FROM proyecto_auditoria";
$resultProyectos = $conn->query($sqlProyectos);

// Función para registrar auditoría de requerimientos actualizados
function registrarAuditoriaRequerimientoActualizado($conn, $usuario_id, $accion, $detalles, $idRequerimiento) {
    try {
        // Preparar la consulta para insertar auditoría de requerimiento actualizado
        $sql_auditoria = "INSERT INTO auditoria_requerimientos (IDusuario, Accion, Detalles, FechaHora, IdRequerimiento) VALUES (?, ?, ?, NOW(), ?)";
        $stmt_auditoria = $conn->prepare($sql_auditoria);
        
        if (!$stmt_auditoria) {
            throw new Exception("Error al preparar la consulta de auditoría de requerimiento actualizado: " . $conn->error);
        }
        
        $stmt_auditoria->bind_param("isss", $usuario_id, $accion, $detalles, $idRequerimiento);

        // Ejecutar la consulta preparada
        if ($stmt_auditoria->execute()) {
            return true;
        } else {
            throw new Exception("Error al ejecutar la consulta de auditoría de requerimiento actualizado: " . $stmt_auditoria->error);
        }
    } catch (Exception $e) {
        echo "Error al registrar auditoría de requerimiento actualizado: " . $e->getMessage();
        return false;
    }
}

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $remitente = $_POST['remitente'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $proyecto = $_POST['proyecto'];

    // Validar datos del formulario (ejemplo básico)
    if (empty($titulo) || empty($descripcion) || empty($remitente) || empty($fecha_vencimiento) || empty($proyecto)) {
        echo "Por favor complete todos los campos.";
        exit();
    }

    // Detalles antes de la actualización
    $detallesAntes = "Título: " . $row['Titulo'] . ", Descripción: " . $row['Descripcion'] . ", Remitente: " . $row['Remitente'] . ", Fecha de Vencimiento: " . $row['Fecha_vencimiento'] . ", Proyecto: " . $row['Idproyecto'];

    // Actualizar el requerimiento en la base de datos
    $sqlUpdate = "UPDATE requerimientos SET 
                  Titulo = ?, 
                  Descripcion = ?, 
                  Remitente = ?, 
                  Fecha_vencimiento = ?, 
                  Idproyecto = ? 
                  WHERE Idrequerimiento = ?";
    
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssssii", $titulo, $descripcion, $remitente, $fecha_vencimiento, $proyecto, $idRequerimiento);

    if ($stmtUpdate->execute()) {
        // Detalles después de la actualización
        $detallesDespues = "Título: $titulo, Descripción: $descripcion, Remitente: $remitente, Fecha de Vencimiento: $fecha_vencimiento, Proyecto: $proyecto";

        // Registrar acción de auditoría de requerimiento actualizado
        $accion = "Actualizar requerimiento";
        $detalles = "Antes: [$detallesAntes] | Después: [$detallesDespues]";
        registrarAuditoriaRequerimientoActualizado($conn, $_SESSION['usuario_id'], $accion, $detalles, $idRequerimiento);

        echo "Requerimiento actualizado correctamente.";
        // Redirigir a la página principal después de actualizar
        header("Location: requerimiento.php");
        exit();
    } else {
        echo "Error al actualizar el requerimiento: " . $stmtUpdate->error;
    }

    $stmtUpdate->close();
}

// Cerrar conexiones y liberar recursos
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Requerimiento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            padding-top: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Actualizar Requerimiento</h2>
        <form method="post" action="update_requerimiento.php?Idrequerimiento=<?php echo htmlspecialchars($idRequerimiento); ?>">
            <div class="form-group">
                <label for="titulo">Título del Requerimiento:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($row['Titulo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($row['Descripcion']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="remitente">Remitente:</label>
                <select class="form-control" id="remitente" name="remitente" required>
                    <?php while($rowRemitente = $resultRemitentes->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($rowRemitente['Nombre']); ?>" <?php if ($row['Remitente'] === $rowRemitente['Nombre']) echo 'selected'; ?>><?php echo htmlspecialchars($rowRemitente['Nombre']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="<?php echo htmlspecialchars($row['Fecha_vencimiento']); ?>" required>
            </div>
            <div class="form-group">
                <label for="proyecto">Proyecto:</label>
                <select class="form-control" id="proyecto" name="proyecto" required>
                    <?php while($rowProyecto = $resultProyectos->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($rowProyecto['Idproyecto']); ?>" <?php if ($row['Idproyecto'] == $rowProyecto['Idproyecto']) echo 'selected'; ?>><?php echo htmlspecialchars($rowProyecto['Descripcion']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="requerimiento.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
