<?php
include('conexion.php');
$conn = conectarDB();
$sql = "SELECT 
            p.Idproyecto, 
            p.Descripcion, 
            p.Fecha_inicio, 
            p.Fecha_fin, 
            p.Prioridad, 
            GROUP_CONCAT(CONCAT(a.Nombre, ' (', a.NivelExperiencia, ')') SEPARATOR '<br>') as AuditoresConCargos,
            p.Fase_proyecto, 
            p.Estado, 
            p.Fecha_creacion,
            p.Creador_proyecto  
        FROM 
            proyecto_auditoria p
            LEFT JOIN equipo_trabajo et ON p.Idproyecto = et.Idproyecto
            LEFT JOIN auditores a ON et.IDusuario = a.IDusuario
        GROUP BY p.Idproyecto";
$result = $conn->query($sql);

// Función para registrar en la auditoría
function registrarAuditoriaProyecto($conn, $idProyecto, $descripcionProyecto, $detalles, $idUsuario) {
    $detalles = "El proyecto '$descripcionProyecto' con ID $idProyecto ha sido $detalles";
    $sql = "INSERT INTO auditoria_proyectos (Idproyecto, Detalles, FechaHora, IDusuario) VALUES (?, ?, current_timestamp(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isi', $idProyecto, $detalles, $idUsuario);
    $stmt->execute();
}

// Anular proyecto
if (isset($_GET['anular'])) {
    $idProyecto = $_GET['anular'];
    $estadoAnulado = "Anulado";

    // Obtener el nombre del proyecto antes de anularlo
    $stmtNombre = $conn->prepare("SELECT Descripcion FROM proyecto_auditoria WHERE Idproyecto = ?");
    $stmtNombre->bind_param('i', $idProyecto);
    $stmtNombre->execute();
    $resultNombre = $stmtNombre->get_result();
    $descripcionProyecto = $resultNombre->fetch_assoc()['Descripcion'];

    // Actualizar el estado del proyecto a "Anulado"
    $stmt = $conn->prepare("UPDATE proyecto_auditoria SET Estado = ? WHERE Idproyecto = ?");
    $stmt->bind_param('si', $estadoAnulado, $idProyecto);
    if ($stmt->execute()) {
        // Registrar en la auditoría con el nombre del proyecto
        $idUsuario = 1; // Cambia por el ID del usuario actual
        registrarAuditoriaProyecto($conn, $idProyecto, $descripcionProyecto, "anulado", $idUsuario);
        
        header("Location: proyectos_auditoria.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Proyectos de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }

        .container {
            max-width: 1880px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            margin: auto;
            margin-top: 50px;
        }

        h1 {
            color: #000;
            text-align: center;
            margin-top: 20px;
        }

        .table th {
            background-color: #343a40;
            color: #fff;
        }

        .table td {
            background-color: #f8f9fa;
        }

        .row-disabled {
            background-color: #c4c8cc !important;
            color: #5a5c5e;
        }

        .table .enabled-row {
            color: #1b1e21;
        }

        .btn-anular {
            background-color: #f8d7da;
            color: #dc3545;
            border: 1px solid #f5c6cb;
        }

        .btn-anular:disabled {
            background-color: #d6d8db;
            color: #6c757d;
            border: 1px solid #c4c8cc;
            cursor: not-allowed;
        }

        .btn-anular i {
            color: #dc3545;
        }
    </style>
    <script>
        function confirmAnular() {
            return confirm("¿Estás seguro de que deseas anular este proyecto?");
        }
    </script>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Proyectos de Auditoría</h1>
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex">
            <a href="agregar_proyecto.php" class="btn btn-primary mr-2"><i class="fas fa-plus"></i> Agregar</a>
            <a href="auditoria_proyectos.php" class="btn btn-info mr-2"><i class="fas fa-file-alt"></i> Ver Registro de Auditoría</a>
            <a href="/TESIS_SISTEMA/manuales_usuario/Gestión de Auditoría-Proyecto Auditoria.pdf" target="_blank" class="btn btn-secondary"><i class="fas fa-question-circle"></i> Ayuda</a>
        </div>
    </div>
    <h2 class="mt-3">Proyectos Registrados</h2>
    <table class="table table-striped mt-3">
        <thead>
        <tr>
            <th>Código</th>
            <th>Descripción</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Prioridad</th>
            <th>Auditores asignados</th>
            <th>Fase del Proyecto</th>
            <th>Estado</th>
            <th>Fecha de Creación</th>
            <th>Creador</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $isAnulado = $row['Estado'] === "Anulado";
                $rowClass = $isAnulado ? "row-disabled" : "enabled-row";

                // Botón de anular completamente deshabilitado si el estado es "Anulado"
                $botonAnular = $isAnulado 
                    ? "<button class='btn btn-anular btn-sm' disabled><i class='fas fa-ban'></i> Anulado</button>"
                    : "<a href='?anular={$row['Idproyecto']}' class='btn btn-anular btn-sm' onclick='return confirmAnular();'><i class='fas fa-ban'></i> Anular</a>";

                // Botones de acción
                $acciones = $isAnulado ? $botonAnular : "
                    <a href='editar_proyecto.php?id={$row['Idproyecto']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Editar</a>
                    $botonAnular
                    <a href='ver_proyecto.php?id={$row['Idproyecto']}' class='btn btn-info btn-sm'><i class='fas fa-eye'></i> Ver Detalles</a>
                ";

                echo "<tr class='$rowClass'>
                        <td>{$row['Idproyecto']}</td>
                        <td>{$row['Descripcion']}</td>
                        <td>{$row['Fecha_inicio']}</td>
                        <td>{$row['Fecha_fin']}</td>
                        <td>{$row['Prioridad']}</td>
                        <td>{$row['AuditoresConCargos']}</td>
                        <td>{$row['Fase_proyecto']}</td>
                        <td>{$row['Estado']}</td>
                        <td>{$row['Fecha_creacion']}</td>
                        <td>{$row['Creador_proyecto']}</td>
                        <td>$acciones</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='11'>No hay proyectos registrados.</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <a href="admin.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
