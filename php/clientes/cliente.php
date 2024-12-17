<?php
session_start();

include_once 'conexion.php';
$conn = conectarDB();

// Verificar si el usuario está logueado y es cliente activo
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 3) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario desde la sesión
$idUsuario = $_SESSION['usuario_id'];

// Consulta para verificar que el usuario está asignado como cliente activo
$queryCliente = "SELECT Nombre FROM usuarios WHERE IDusuario = ? AND idRol = 3 AND Estado = 1";
$stmt = $conn->prepare($queryCliente);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultCliente = $stmt->get_result();

if ($resultCliente && $resultCliente->num_rows > 0) {
    $cliente = $resultCliente->fetch_assoc();
    $nombreCliente = $cliente['Nombre'];
} else {
    header("Location: login.php");
    exit();
}

// Consulta para obtener los requerimientos asignados al cliente
$queryRequerimientos = "SELECT Titulo, Descripcion, Fecha_creacion, Estado_requerimiento 
                        FROM requerimientos 
                        WHERE Remitente = ?";
$stmtRequerimientos = $conn->prepare($queryRequerimientos);
$stmtRequerimientos->bind_param("s", $nombreCliente);
$stmtRequerimientos->execute();
$resultRequerimientos = $stmtRequerimientos->get_result();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Clientes</title>
    <link rel="stylesheet" href="estilocliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Barra de Navegación -->
        <nav>
            <ul>
                <li class="logo">
                    <img src="images/logo.jpg" alt="Logo">
                </li>
                <li>
                    <span class="nav-title">Panel de Cliente</span>
                </li>
                <li>
                    <a href="requerimiento.php">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="nav-item">Gestión de Requerimientos</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-item">Cerrar sesión</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Sección principal -->
        <section class="main-background">
            <div class="main-container">
                <div class="main-top">
            
                    <h1>Bienvenido/a, <?php echo htmlspecialchars($nombreCliente); ?></h1>
                    
                </div>

                <div class="main-content">
                    <h2>Requerimientos que precisan atención</h2>
                    <div class="cards-container">
                        <?php if ($resultRequerimientos->num_rows > 0): ?>
                            <?php while ($requerimiento = $resultRequerimientos->fetch_assoc()): ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h3><?php echo htmlspecialchars($requerimiento['Titulo']); ?></h3>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($requerimiento['Descripcion']); ?></p>
                                        <p><strong>Fecha de Creación:</strong> <?php echo htmlspecialchars($requerimiento['Fecha_creacion']); ?></p>
                                        <p><strong>Estado:</strong> <?php echo htmlspecialchars($requerimiento['Estado_requerimiento']); ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No tienes requerimientos asignados.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
