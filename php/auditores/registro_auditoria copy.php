<?php
// Iniciar o reanudar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Si no ha iniciado sesión, redirigir al inicio de sesión
    header("Location: index.php");
    exit();
}

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// Establecer la conexión a la base de datos
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Función para registrar auditoría
function registrarAuditoria($conn, $usuario_id, $accion, $detalles) {
    try {
        // Preparar la consulta para insertar auditoría
        $sql_auditoria = "INSERT INTO auditoria (IDUsuario, Accion, Detalles, FechaHora) VALUES (?, ?, ?, NOW())";
        $stmt_auditoria = $conn->prepare($sql_auditoria);
        
        if (!$stmt_auditoria) {
            throw new Exception("Error al preparar la consulta de auditoría: " . $conn->error);
        }
        
        $stmt_auditoria->bind_param("iss", $usuario_id, $accion, $detalles);

        // Ejecutar la consulta preparada
        if ($stmt_auditoria->execute()) {
            return true;
        } else {
            throw new Exception("Error al ejecutar la consulta de auditoría: " . $stmt_auditoria->error);
        }
    } catch (Exception $e) {
        echo "Error al registrar auditoría: " . $e->getMessage();
        return false;
    }
}

// Manejar la acción de eliminar tarea si se ha proporcionado un ID de tarea
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['eliminar']) && isset($_GET['id'])) {
    $id_tarea = $_GET['id'];

    try {
        // Utilizar consulta preparada para eliminar la tarea
        $sql_delete = "DELETE FROM tareas WHERE Idtarea = ?";
        $stmt_delete = $conn->prepare($sql_delete);

        if (!$stmt_delete) {
            throw new Exception("Error al preparar la consulta de eliminación: " . $conn->error);
        }

        $stmt_delete->bind_param("i", $id_tarea);

        if ($stmt_delete->execute()) {
            // Obtener el número de filas afectadas por la eliminación
            $filas_afectadas = $stmt_delete->affected_rows;

            // Verificar si se eliminó correctamente al menos una fila
            if ($filas_afectadas > 0) {
                // Registrar acción de auditoría
                if (registrarAuditoria($conn, $_SESSION['usuario_id'], 'Eliminar', "Tarea ID: $id_tarea eliminada")) {
                    echo '<div class="alert alert-success" role="alert">La tarea ha sido eliminada correctamente.</div>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Error al registrar la auditoría de eliminación.</div>';
                }
            } else {
                echo '<div class="alert alert-warning" role="alert">No se encontró ninguna tarea con ID ' . $id_tarea . ' para eliminar.</div>';
            }
        } else {
            throw new Exception("Error al ejecutar la consulta de eliminación: " . $stmt_delete->error);
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger" role="alert">Error: ' . $e->getMessage() . '</div>';
    }
}

// Consulta SQL para obtener los datos de auditoría, incluyendo todas las acciones relacionadas con tareas
$sql = "SELECT a.IDAuditoria, a.IDUsuario, a.Accion, a.Detalles, a.FechaHora, u.Nombre AS NombreUsuario 
        FROM auditoria a
        INNER JOIN usuarios u ON a.IDUsuario = u.IDUsuario
        WHERE a.Accion IN ('Agregar tarea de auditoría', 'Editar tarea de auditoría', 'Eliminar')
        ORDER BY a.FechaHora DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Registro de Auditoría de Tareas</h1>

        <?php
        // Mostrar mensajes de éxito, advertencia o error según las acciones realizadas
        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['eliminar']) && isset($_GET['id'])) {
            // Manejo de mensajes ya integrado en el código PHP anteriormente
        }
        ?>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID Registro</th>
                    <th>Usuario</th>
                    <th>Acción Realizada</th>
                    <th>Detalles</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar si hay resultados y mostrar los datos en la tabla
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['IDAuditoria']}</td>";
                        echo "<td>{$row['NombreUsuario']}</td>";
                        echo "<td>{$row['Accion']}</td>";
                        echo "<td>{$row['Detalles']}</td>";
                        echo "<td>{$row['FechaHora']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay registros de auditoría</td></tr>";
                }

                // Cerrar la conexión a la base de datos
                $conn->close();
                ?>
            </tbody>
        </table>
        <a href="tarea.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a Tareas</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
