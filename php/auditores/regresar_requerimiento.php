<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Conectar a la base de datos
$con = conectarDB();

// Verificar la conexión
if (!$con) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $comentario = $_POST['comentario'];

    // Escapar las variables para evitar SQL Injection
    $id = mysqli_real_escape_string($con, $id);
    $comentario = mysqli_real_escape_string($con, $comentario);

    // Crear la consulta SQL
    $sql = "UPDATE requerimientos SET Estado_requerimiento = 'Devuelto', Comentario = '$comentario' WHERE Idrequerimiento = $id";

    if (mysqli_query($con, $sql)) {
        // Redirigir si la consulta fue exitosa
        header("Location: requerimiento.php");
        exit;
    } else {
        // Mostrar error si la consulta falla
        echo "Error: " . mysqli_error($con);
    }
}

$id = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regresar Requerimiento</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Regresar Requerimiento</h1>
        <form action="regresar_requerimiento.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-group">
                <label for="comentario">Comentario:</label>
                <textarea name="comentario" id="comentario" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Guardar Comentario</button>
            <a href="requerimiento.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
