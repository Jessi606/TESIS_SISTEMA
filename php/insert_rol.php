<?php
// Incluir el archivo de conexión
include('conexion.php');

// Conectar a la base de datos
$con = conectarDB();

// Obtener la descripción del formulario
$descripcion = isset($_POST['Descripcion']) ? $_POST['Descripcion'] : '';

// Inicializar la bandera de error
$error = false;

// Verificar si se proporcionó una descripción válida
if (!empty($descripcion)) {
    // Preparar la consulta SQL utilizando una consulta preparada
    $sql = "INSERT INTO roles (Descripcion) VALUES (?)";
    
    // Preparar la declaración SQL
    $stmt = mysqli_prepare($con, $sql);
    
    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt) {
        // Vincular parámetros y ejecutar la consulta
        mysqli_stmt_bind_param($stmt, 's', $descripcion);
        $query = mysqli_stmt_execute($stmt);
        
        // Verificar si la inserción fue exitosa
        if ($query) {
            // Redireccionar a index.php después de la inserción
            header("Location: roles.php");
            exit; // Detener la ejecución después de la redirección
        } else {
            // Manejar errores de inserción
            echo "Error al insertar el rol.";
            $error = true;
        }
    } else {
        // Manejar errores de preparación de consulta
        echo "Error al preparar la consulta.";
        $error = true;
    }
} else {
    // Establecer la bandera de error si la descripción está vacía
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mensaje de Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #a6bbd7;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #f44336;
        }
        p {
            color: #333;
            margin-bottom: 20px;
        }
        a {
            text-decoration: none;
            color: #fff;
            background-color: #4caf50;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Error</h1>
        <?php if ($error): ?>
            <p>La descripción del rol no puede estar vacía.</p>
        <?php endif; ?>
        <a href="javascript:history.go(-1)">Volver</a>
    </div>
</body>
</html>
