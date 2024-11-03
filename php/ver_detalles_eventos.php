<?php
// Incluir archivo de conexión a la base de datos
include('conexion.php');
$conn = conectarDB();

// Función para obtener el nombre del tipo de evento
function obtenerNombreTipoEvento($conn, $id_tipoevento) {
    $sql_tipoevento = "SELECT Descripcion FROM tipo_evento WHERE ID_tipoevento = '$id_tipoevento'";
    $result_tipoevento = $conn->query($sql_tipoevento);

    if ($result_tipoevento && $result_tipoevento->num_rows > 0) {
        $row_tipoevento = $result_tipoevento->fetch_assoc();
        return $row_tipoevento['Descripcion'];
    } else {
        return "Desconocido";
    }
}

// Consulta para obtener todos los eventos
$sql_eventos = "SELECT * FROM eventos";
$result_eventos = $conn->query($sql_eventos);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Eventos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #556e83;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 32px;
            color: #FFFFFF;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }
        .card {
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
        }
        .card-header {
            background-color: #ededaf;
            color: #333;
            font-weight: bold;
        }
        .card-body {
            padding: 1.25rem;
        }
        .evidencia-link {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Detalles de Eventos</h1>

        <?php if ($result_eventos && $result_eventos->num_rows > 0): ?>
            <?php while ($row_evento = $result_eventos->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <?php echo $row_evento['Nombre']; ?>
                    </div>
                    <div class="card-body">
                        <p><strong>Fecha del Evento:</strong> <?php echo $row_evento['Fecha_evento']; ?></p>
                        <p><strong>Descripción:</strong> <?php echo $row_evento['Descripcion']; ?></p>
                        <p><strong>Lugar:</strong> <?php echo $row_evento['Lugar']; ?></p>
                        <p><strong>Fecha de Vencimiento:</strong> <?php echo $row_evento['Fecha_vencimiento']; ?></p>
                        <p><strong>Tipo de Evento:</strong> <?php echo obtenerNombreTipoEvento($conn, $row_evento['Id_tipoevento']); ?></p>
                        <p><strong>Color:</strong> <?php echo $row_evento['Color']; ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                No se encontraron eventos.
            </div>
        <?php endif; ?>

        <a href="calendario.php" class="btn btn-primary mt-4"><i class="fas fa-arrow-left"></i> Volver al calendario</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos al finalizar
$conn->close();
?>
