<?php
include('conexion.php');

$conn = conectarDB();

if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Mensajes de éxito o error
$mensaje = '';
if (isset($_GET['success']) && $_GET['success'] === 'anulado') {
    $mensaje = "<div class='alert alert-success' id='success-message'>Auditor anulado exitosamente.</div>";
} elseif (isset($_GET['error']) && $_GET['error'] == 1) {
    $mensaje = "<div class='alert alert-danger' id='error-message'>Error al anular el auditor.</div>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Auditores</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }
        .container {
            max-width: 1200px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            margin: auto;
            margin-top: 50px;
        }
        h1, h2 {
            color: #000;
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .table td {
            background-color: #f8f9fa;
        }
        /* Estilos para deshabilitar filas anuladas */
        .disabled-row {
            background-color: #e9ecef !important;
            color: #6c757d;
            pointer-events: none; /* Desactiva eventos en la fila */
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Registro de Auditores</h1>

    <!-- Mostrar mensaje de éxito o error -->
    <?= $mensaje ?>

    <div class="d-flex justify-content-start mb-3">
        <a href="agregar_auditor.php" class="btn btn-primary mr-2"><i class="fas fa-user-plus"></i> Agregar</a>
        <a href="/TESIS_SISTEMA/Manuales de usuario/Gestión de Usuarios_auditores_actualizado.pdf" target="_blank" class="btn btn-secondary"><i class="fas fa-question-circle"></i> Ayuda</a>
     </div>

    <h2 class="mt-3">Auditores Registrados</h2>
    <table class="table table-striped mt-3">
        <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Fecha de Nacimiento</th>
            <th>Nivel de Experiencia</th>
            <th>Usuario Asignado</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Consulta para obtener todos los auditores con su estado
        $sql = "SELECT auditores.Idauditor, auditores.Nombre, auditores.Apellido, auditores.Telefono, auditores.Email, 
                auditores.FechaNacimiento, auditores.NivelExperiencia, usuarios.Nombre AS Usuario, auditores.estado
                FROM auditores
                LEFT JOIN usuarios ON auditores.IDusuario = usuarios.IDusuario
                ORDER BY FIELD(auditores.NivelExperiencia, 'Gerente', 'Senior', 'Junior')";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row_class = $row['estado'] === 'anulado' ? 'disabled-row' : '';
                $acciones = $row['estado'] === 'activo' ? "
                    <a href='editar_auditor.php?id=" . htmlspecialchars($row['Idauditor']) . "' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Editar</a>
                    <a href='anular_auditor.php?id=" . htmlspecialchars($row['Idauditor']) . "' class='btn btn-sm btn-danger' onclick='return confirmAnular();'>
                        <i class='fas fa-ban'></i> Anular
                    </a>" : "<span class='text-muted'>No disponible</span>";

                echo "<tr class='$row_class'>
                        <td>" . htmlspecialchars($row['Idauditor']) . "</td>
                        <td>" . htmlspecialchars($row['Nombre']) . "</td>
                        <td>" . htmlspecialchars($row['Apellido']) . "</td>
                        <td>" . htmlspecialchars($row['Telefono']) . "</td>
                        <td>" . htmlspecialchars($row['Email']) . "</td>
                        <td>" . htmlspecialchars($row['FechaNacimiento']) . "</td>
                        <td>" . htmlspecialchars($row['NivelExperiencia']) . "</td>
                        <td>" . htmlspecialchars($row['Usuario']) . "</td>
                        <td>" . htmlspecialchars($row['estado']) . "</td>
                        <td>$acciones</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='10' class='text-center'>No hay auditores registrados.</td></tr>";
        }
        ?>  
        </tbody>
    </table>
    <a href="auditor.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function confirmAnular() {
        return confirm('¿Estás seguro de que deseas anular este auditor?');
    }

    // Ocultar mensaje de éxito o error después de 5 segundos
    setTimeout(function() {
        var successMessage = document.getElementById("success-message");
        var errorMessage = document.getElementById("error-message");
        if (successMessage) {
            successMessage.style.display = "none";
        }
        if (errorMessage) {
            errorMessage.style.display = "none";
        }
    }, 5000);
</script>
</body>
</html>

<?php
$conn->close();
?>
