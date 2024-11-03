    <?php
    // Configuración de la base de datos
    $host = "localhost";
    $user = "root";
    $pass = ""; // Si tu contraseña está vacía, usa una cadena vacía
    $database = "bdpretesis";

    // Función para conectar a la base de datos
    function conectarDB() {
        // Usar las variables globales
        global $host, $user, $pass, $database;
        
        // Crear una nueva conexión
        $conn = new mysqli($host, $user, $pass, $database);
        
        // Verificar la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }
        
        return $conn;
    }
    ?>
