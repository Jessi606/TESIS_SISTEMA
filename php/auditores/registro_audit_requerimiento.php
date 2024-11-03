<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("conexion.php");
$conn = conectarDB();

// Verificar la conexión
if (!$conn) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Función para registrar auditoría de requerimientos
function registrarAuditoria($conn, $usuario_id, $accion, $detalles) {
    try {
        // Preparar la consulta para insertar auditoría
        $sql_auditoria = "INSERT INTO auditoria_requerimientos (IDusuario, Accion, Detalles, FechaHora) VALUES (?, ?, ?, NOW())";
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

// Manejar las acciones para los requerimientos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];
    $id_requerimiento = $_POST['id_requerimiento'];
    $detalles = '';

    switch ($accion) {
        case 'agregar':
            // Lógica para agregar requerimiento
            $detalles = "Nuevo requerimiento agregado.";
            break;
        case 'modificar':
            // Lógica para modificar requerimiento
            $sql_select_requerimiento = "SELECT * FROM requerimientos WHERE Idrequerimiento = ?";
            $stmt_select_requerimiento = $conn->prepare($sql_select_requerimiento);
            $stmt_select_requerimiento->bind_param("i", $id_requerimiento);
            if ($stmt_select_requerimiento->execute()) {
                $result_requerimiento = $stmt_select_requerimiento->get_result();
                if ($result_requerimiento->num_rows > 0) {
                    $row = $result_requerimiento->fetch_assoc();
                    // Construir detalles del requerimiento modificado
                    $detalles = "Requerimiento modificado: ";
                    $detalles .= "ID: " . $id_requerimiento . ", ";
                    $detalles .= "Título: " . $row['titulo'] . ", ";
                    $detalles .= "Descripción: " . $row['descripcion'] . ", ";
                    $detalles .= "Fecha de inicio: " . $row['fecha_inicio'] . ", ";
                    $detalles .= "Fecha de fin: " . $row['fecha_fin'] . ", ";
                    $detalles .= "Estado: " . $row['estado'] . ", ";
                    $detalles .= "Proyecto: " . $row['proyecto'];
                } else {
                    echo "No se encontró el requerimiento con ID $id_requerimiento.";
                }
            }
            break;
        case 'eliminar':
            // Lógica para eliminar requerimiento
            $detalles = "Requerimiento eliminado con ID: " . $id_requerimiento;
            break;
        case 'aceptar':
            // Lógica para aceptar requerimiento
            $detalles = "Requerimiento aceptado con ID: " . $id_requerimiento;
            break;
        case 'devolver':
            // Lógica para devolver requerimiento
            $detalles = "Requerimiento devuelto con ID: " . $id_requerimiento;
            break;
        case 'subir_evidencia':
            // Lógica para subir evidencia
            $detalles = "Evidencia subida para el requerimiento con ID: " . $id_requerimiento;
            break;
        default:
            echo "Acción no válida.";
            exit();
    }

    // Registrar la acción en la auditoría
    registrarAuditoria($conn, $_SESSION['usuario_id'], $accion, $detalles);

    // Redirigir a la misma página después de realizar la acción
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Consultar registros de auditoría
$sql_auditoria = "SELECT ar.IdAuditoriaReq, ar.IDusuario, ar.Accion, ar.Detalles, ar.FechaHora, u.Nombre
                  FROM auditoria_requerimientos ar
                  JOIN usuarios u ON ar.IDusuario = u.IDusuario
                  ORDER BY ar.FechaHora DESC";
$result_auditoria = $conn->query($sql_auditoria);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Requerimientos</title>
    <!-- Integra Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }
        .container {
            max-width: 1900px;
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
        .btn-primary, .btn-danger {
            width: 150px;
            margin: 5px;
        }
        .custom-btn {
            width: 150px;
        }
        table {
            width: 100%;
        }
        th {
            background-color: #343a40;
            color: #fff;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 10px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        tbody tr:nth-child(even) {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro de Auditoría de Requerimientos</h1>
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
                    <?php while ($row_auditoria = $result_auditoria->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row_auditoria['IdAuditoriaReq'] ?></td>
                            <td><?= $row_auditoria['Nombre'] ?></td>
                            <td><?= $row_auditoria['Accion'] ?></td>
                            <td><?= $row_auditoria['Detalles'] ?></td>
                            <td><?= $row_auditoria['FechaHora'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="requerimiento.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a Requerimientos</a>
        </div>
    </div>
    <!-- Integra Bootstrap JS (opcional, si es necesario) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
