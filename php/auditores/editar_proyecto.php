<?php
include('conexion.php');
$conn = conectarDB();

// Función para obtener la lista de usuarios por nivel de experiencia desde la tabla auditores
function obtenerUsuariosPorNivel($conn, $nivel) {
    $sql = "SELECT IDusuario, CONCAT(Nombre, ' ', Apellido) AS NombreCompleto
            FROM auditores
            WHERE NivelExperiencia = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $nivel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result;
    } else {
        return null;
    }
}

// Función para registrar acciones en la auditoría de proyectos
function registrarAuditoriaProyecto($conn, $idProyecto, $accion, $detalles, $idUsuario) {
    $sql = "INSERT INTO auditoria_proyectos (IdAuditoriaProy, Idproyecto, Accion, Detalles, FechaHora, IDusuario) VALUES (NULL, ?, ?, ?, current_timestamp(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issi', $idProyecto, $accion, $detalles, $idUsuario);
    $stmt->execute();
}

// Obtener listas de usuarios por nivel específico
$gerentes_result = obtenerUsuariosPorNivel($conn, 'Gerente');
$seniores_result = obtenerUsuariosPorNivel($conn, 'Senior');
$juniors_result = obtenerUsuariosPorNivel($conn, 'Junior');

// Variables para almacenar datos del proyecto a editar
$id_proyecto_editar = null;
$descripcion = "";
$fecha_inicio = "";
$fecha_fin = "";
$prioridad = "";
$gerente_id = "";
$senior_id = "";
$junior_id = "";
$fase_proyecto = "";
$estado = "";
$creador_proyecto = "";

// Verificar si se está editando un proyecto existente
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_proyecto_editar = $_GET['id'];

    // Consultar los datos del proyecto a editar
    $sql_proyecto = "SELECT * FROM proyecto_auditoria WHERE Idproyecto = ?";
    $stmt_proyecto = $conn->prepare($sql_proyecto);
    $stmt_proyecto->bind_param('i', $id_proyecto_editar);
    $stmt_proyecto->execute();
    $result_proyecto = $stmt_proyecto->get_result();

    if ($result_proyecto->num_rows == 1) {
        $proyecto = $result_proyecto->fetch_assoc();
        $descripcion = $proyecto['Descripcion'];
        $fecha_inicio = $proyecto['Fecha_inicio'];
        $fecha_fin = $proyecto['Fecha_fin'];
        $prioridad = $proyecto['Prioridad'];
        $fase_proyecto = $proyecto['Fase_proyecto'];
        $estado = $proyecto['Estado'];
        $creador_proyecto = $proyecto['Creador_proyecto']; // Obtener el creador del proyecto

        // Obtener los IDs de los usuarios asignados al proyecto
        $sql_equipo = "SELECT IDusuario FROM equipo_trabajo WHERE Idproyecto = ?";
        $stmt_equipo = $conn->prepare($sql_equipo);
        $stmt_equipo->bind_param('i', $id_proyecto_editar);
        $stmt_equipo->execute();
        $result_equipo = $stmt_equipo->get_result();

        if ($result_equipo->num_rows > 0) {
            $equipo = $result_equipo->fetch_all(MYSQLI_ASSOC);
            $gerente_id = $equipo[0]['IDusuario'];
            $senior_id = $equipo[1]['IDusuario'];
            $junior_id = $equipo[2]['IDusuario'];
        }
    } else {
        echo "No se encontró el proyecto a editar.";
        exit();
    }
}

// Procesar el formulario de actualización del proyecto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_proyecto = $_POST['id_proyecto'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $prioridad = $_POST['prioridad'];
    $gerente_id = $_POST['gerente'];
    $senior_id = $_POST['senior'];
    $junior_id = $_POST['junior'];
    $fase_proyecto = $_POST['fase_proyecto'];
    $estado = $_POST['estado'];
    $creador_proyecto = $_POST['creador_proyecto']; // Obtener el creador del proyecto desde el formulario

    // Consultar los datos del proyecto antes de la actualización
    $sql_proyecto_anterior = "SELECT Descripcion, Fecha_inicio, Fecha_fin, Prioridad, Fase_proyecto, Estado, Creador_proyecto FROM proyecto_auditoria WHERE Idproyecto = ?";
    $stmt_proyecto_anterior = $conn->prepare($sql_proyecto_anterior);
    $stmt_proyecto_anterior->bind_param('i', $id_proyecto);
    $stmt_proyecto_anterior->execute();
    $result_proyecto_anterior = $stmt_proyecto_anterior->get_result();

    if ($result_proyecto_anterior->num_rows == 1) {
        $proyecto_anterior = $result_proyecto_anterior->fetch_assoc();

        // Generar detalles de los cambios realizados
        $detalles_anterior = "Descripción: {$proyecto_anterior['Descripcion']}, Fecha inicio: {$proyecto_anterior['Fecha_inicio']}, Fecha fin: {$proyecto_anterior['Fecha_fin']}, Prioridad: {$proyecto_anterior['Prioridad']}, Fase proyecto: {$proyecto_anterior['Fase_proyecto']}, Estado: {$proyecto_anterior['Estado']}, Creador proyecto: {$proyecto_anterior['Creador_proyecto']}";
        $detalles_nuevo = "Descripción: $descripcion, Fecha inicio: $fecha_inicio, Fecha fin: $fecha_fin, Prioridad: $prioridad, Fase proyecto: $fase_proyecto, Estado: $estado, Creador proyecto: $creador_proyecto";

        // Actualizar los datos del proyecto en la tabla proyecto_auditoria
        $sql_update_proyecto = "UPDATE proyecto_auditoria SET 
                                Descripcion = ?,
                                Fecha_inicio = ?,
                                Fecha_fin = ?,
                                Prioridad = ?,
                                Fase_proyecto = ?,
                                Estado = ?,
                                Creador_proyecto = ?
                                WHERE Idproyecto = ?";
        $stmt_update_proyecto = $conn->prepare($sql_update_proyecto);
        $stmt_update_proyecto->bind_param('sssssssi', $descripcion, $fecha_inicio, $fecha_fin, $prioridad, $fase_proyecto, $estado, $creador_proyecto, $id_proyecto);

        if ($stmt_update_proyecto->execute()) {
            // Eliminar usuarios actuales asignados al proyecto en equipo_trabajo
            $sql_delete_equipo = "DELETE FROM equipo_trabajo WHERE Idproyecto = ?";
            $stmt_delete_equipo = $conn->prepare($sql_delete_equipo);
            $stmt_delete_equipo->bind_param('i', $id_proyecto);
            $stmt_delete_equipo->execute();

            // Insertar los nuevos usuarios seleccionados como gerente, senior y junior en equipo_trabajo
            $equipo_trabajo = [$gerente_id, $senior_id, $junior_id];
            foreach ($equipo_trabajo as $id_usuario) {
                $sql_equipo = "INSERT INTO equipo_trabajo (Idproyecto, IDusuario) VALUES (?, ?)";
                $stmt_equipo = $conn->prepare($sql_equipo);
                $stmt_equipo->bind_param('ii', $id_proyecto, $id_usuario);
                $stmt_equipo->execute();
            }

            // Registrar la acción en el log de auditoría
            $accion = "Edición de Proyecto";
            $detalles = "Se ha actualizado el proyecto con ID $id_proyecto. Cambios realizados: Antes - $detalles_anterior, Después - $detalles_nuevo";
            // Aquí se asume que $idUsuario contiene el ID del usuario actual (debes ajustarlo según tu lógica de sesión)
            $idUsuario = 1; // Ejemplo, deberías obtener el ID del usuario actualmente logueado
            registrarAuditoriaProyecto($conn, $id_proyecto, $accion, $detalles, $idUsuario);

            // Redireccionar a la página de proyectos después de la actualización
            header("Location: proyectos_auditoria.php");
            exit();
        } else {
            echo "Error al actualizar el proyecto: " . $conn->error;
        }
    } else {
        echo "No se encontró el proyecto a actualizar.";
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
        }
        .container {
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
            margin-top: 50px;
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
        .alert {
            margin-top: 20px;
        }
        h1, h2 {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Editar Proyecto de Auditoría</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="id_proyecto" value="<?php echo $id_proyecto_editar; ?>">
        <div class="form-group">
            <label for="descripcion">Descripción del Proyecto</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo $descripcion; ?></textarea>
        </div>
        <div class="form-group">
            <label for="fecha_inicio">Fecha de Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_fin">Fecha de Fin</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $fecha_fin; ?>" required>
        </div>
        <div class="form-group">
            <label for="prioridad">Prioridad</label>
            <select class="form-control" id="prioridad" name="prioridad" required>
                <option value="Baja" <?php if ($prioridad == 'Baja') echo 'selected'; ?>>Baja</option>
                <option value="Media" <?php if ($prioridad == 'Media') echo 'selected'; ?>>Media</option>
                <option value="Alta" <?php if ($prioridad == 'Alta') echo 'selected'; ?>>Alta</option>
            </select>
        </div>
        <div class="form-group">
            <label for="gerente">Gerente del Proyecto</label>
            <select class="form-control" id="gerente" name="gerente" required>
                <option value="">Seleccionar Gerente</option>
                <?php while ($row = $gerentes_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['IDusuario']; ?>" <?php if ($gerente_id == $row['IDusuario']) echo 'selected'; ?>><?php echo $row['NombreCompleto']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="senior">Senior del Proyecto</label>
            <select class="form-control" id="senior" name="senior" required>
                <option value="">Seleccionar Senior</option>
                <?php while ($row = $seniores_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['IDusuario']; ?>" <?php if ($senior_id == $row['IDusuario']) echo 'selected'; ?>><?php echo $row['NombreCompleto']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="junior">Junior del Proyecto</label>
            <select class="form-control" id="junior" name="junior" required>
                <option value="">Seleccionar Junior</option>
                <?php while ($row = $juniors_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['IDusuario']; ?>" <?php if ($junior_id == $row['IDusuario']) echo 'selected'; ?>><?php echo $row['NombreCompleto']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fase_proyecto">Fase del Proyecto</label>
            <select class="form-control" id="fase_proyecto" name="fase_proyecto" required>
                <option value="Planificación" <?php if ($fase_proyecto == 'Planificación') echo 'selected'; ?>>Planificación</option>
                <option value="Ejecución" <?php if ($fase_proyecto == 'Ejecución') echo 'selected'; ?>>Ejecución</option>
                <option value="Cierre" <?php if ($fase_proyecto == 'Cierre') echo 'selected'; ?>>Cierre</option>
            </select>
        </div>
        <div class="form-group">
            <label for="estado">Estado del Proyecto</label>
            <select class="form-control" id="estado" name="estado" required>
                <option value="Pendiente" <?php if ($estado == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                <option value="En Progreso" <?php if ($estado == 'En Progreso') echo 'selected'; ?>>En Progreso</option>
                <option value="Completado" <?php if ($estado == 'Completado') echo 'selected'; ?>>Completado</option>
                <option value="Cancelado" <?php if ($estado == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="creador_proyecto">Creador del Proyecto</label>
            <input type="text" class="form-control" id="creador_proyecto" name="creador_proyecto" value="<?php echo $creador_proyecto; ?>" readonly>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
        <a href="proyectos_auditoria.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
