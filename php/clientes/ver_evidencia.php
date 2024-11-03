<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Establecer la zona horaria a Asunción, Paraguay
date_default_timezone_set('America/Asuncion');

// Obtener el ID del requerimiento de la URL
$idRequerimiento = isset($_GET['idRequerimiento']) ? intval($_GET['idRequerimiento']) : 0;

// Verificar si el ID del requerimiento es válido
if ($idRequerimiento <= 0) {
    die("El ID del requerimiento proporcionado no es válido. Por favor, verifica el enlace e intenta de nuevo.");
}

// Obtener las evidencias del requerimiento
$sql = "SELECT Descripcion, Comentario, Fecha_recopilacion, Evidencia FROM evidencias WHERE Idrequerimiento = $idRequerimiento";
$resultado = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Evidencias</title>
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
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }
        h2 {
            color: #000;
        }
        table {
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Evidencias del Requerimiento</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Comentario</th>
                    <th>Fecha y Hora de Recopilación</th>
                    <th>Archivo</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['Descripcion']) ?></td>
                        <td><?= htmlspecialchars($fila['Comentario']) ?></td>
                        <td><?= htmlspecialchars($fila['Fecha_recopilacion']) ?></td>
                        <td><a href="../../uploads/<?= htmlspecialchars($fila['Evidencia']) ?>" download><?= htmlspecialchars($fila['Evidencia']) ?></a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="requerimiento.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</body>
</html>

<?php
mysqli_close($con);
?>
