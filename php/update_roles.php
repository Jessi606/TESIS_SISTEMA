<?php
// Incluir el archivo de conexión
include('conexion.php');

// Conectar a la base de datos
$con = conectarDB();

// Verificar si Idrol está presente en la URL
if (isset($_GET['Idrol'])) {
    $id = $_GET['Idrol'];

    // Consulta SQL para obtener detalles del rol específico
    $sql = "SELECT * FROM roles WHERE Idrol=?";
    
    // Preparar la declaración SQL
    $stmt = mysqli_prepare($con, $sql);
    
    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt) {
        // Vincular parámetros y ejecutar la consulta
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $query = mysqli_stmt_execute($stmt);
        
        // Verificar si se ejecutó la consulta correctamente
        if ($query) {
            // Obtener el resultado de la consulta
            $result = mysqli_stmt_get_result($stmt);
            
            // Verificar si se encontró el rol
            if ($result && mysqli_num_rows($result) > 0) {
                // Obtener los datos del rol como array asociativo
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            } else {
                // Manejar el caso donde no se encuentra el rol
                echo "Rol no encontrado.";
                exit; // Opcional: detener la ejecución si el rol no se encuentra
            }
        } else {
            // Manejar errores de ejecución de la consulta
            echo "Error al ejecutar la consulta.";
            exit; // Opcional: detener la ejecución en caso de error
        }
    } else {
        // Manejar errores de preparación de consulta
        echo "Error al preparar la consulta.";
        exit; // Opcional: detener la ejecución en caso de error
    }
}

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se proporcionó una descripción válida
    if (!empty($_POST['Descripcion'])) {
        // Obtener la descripción del formulario
        $descripcion = $_POST['Descripcion'];
        
        // Actualizar la descripción del rol en la base de datos
        $sql_update = "UPDATE roles SET Descripcion=? WHERE Idrol=?";
        $stmt_update = mysqli_prepare($con, $sql_update);
        
        // Verificar si la preparación de la consulta de actualización fue exitosa
        if ($stmt_update) {
            // Vincular parámetros y ejecutar la consulta de actualización
            mysqli_stmt_bind_param($stmt_update, 'si', $descripcion, $id);
            $query_update = mysqli_stmt_execute($stmt_update);
            
            // Verificar si la actualización fue exitosa
            if ($query_update) {
                // Redireccionar a la página principal después de la actualización
                header("Location: roles.php");
                exit; // Detener la ejecución después de la redirección
            } else {
                // Manejar errores de actualización
                echo "Error al actualizar el rol.";
            }
        } else {
            // Manejar errores de preparación de consulta de actualización
            echo "Error al preparar la consulta de actualización.";
        }
    } else {
        // Manejar el caso donde la descripción está vacía
        echo "La descripción del rol no puede estar vacía.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Rol</title>
    <!-- Integra Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Integra Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #a6bbd7;
            color: #333;
        }
         /* Estilo para el encabezado de la tarjeta */
         .card-header {
            background-color: #343a40; /* Color de fondo gris oscuro */
            color: #fff; /* Texto blanco */
            border-bottom: 1px solid #444; /* Borde inferior más oscuro */
        }
        /* Estilo para el cuerpo de la tarjeta */
        .card-body {
            background-color: #fff; /* Fondo blanco */
        }
        /* Estilo para el botón de enviar */
        .btn-primary {
            background-color: #007bff; /* Color azul */
            border-color: #007bff; /* Borde azul */
        }
        /* Estilo para el botón de enviar al pasar el mouse */
        .btn-primary:hover {
            background-color: #0056b3; /* Color azul más oscuro al pasar el mouse */
            border-color: #0056b3; /* Borde azul más oscuro al pasar el mouse */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1 class="mb-0">Editar Roles</h1>
            </div>
            <div class="card-body">
                <form action="" method="POST"> <!-- Modificado -->
                    <input type="hidden" name="Idrol" value="<?= htmlspecialchars($row['Idrol']) ?>">
                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <input type="text" id="descripcion" name="Descripcion" class="form-control" value="<?= htmlspecialchars($row['Descripcion']) ?>" placeholder="Ingrese la descripción">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
                    <a href="roles.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a> <!-- Botón de cancelar -->
                </form>
            </div>
        </div>
    </div>

    <!-- Integra Bootstrap JS (opcional, si es necesario) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Integra Font Awesome (opcional, si es necesario) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
</body>
</html>
