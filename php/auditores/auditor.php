<?php
session_start();

// Verifica si la sesión está activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /TESIS_SISTEMA/index.php");
    exit();
}

$idUsuario = $_SESSION['usuario_id'];

include_once 'conexion.php';
$conn = conectarDB();

// Consulta para obtener el nombre del auditor
$queryUsuario = "SELECT Nombre FROM usuarios WHERE IDusuario = $idUsuario";
$resultUsuario = $conn->query($queryUsuario);

if ($resultUsuario->num_rows > 0) {
    $usuario = $resultUsuario->fetch_assoc();
    $nombreUsuario = $usuario['Nombre'];
} else {
    $nombreUsuario = "Usuario Desconocido";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel</title>
    <link rel="stylesheet" href="estiloauditor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <nav>
            <ul>
                <li class="logo">
                    <img src="images/logo.jpg">
                </li>
                <li>
                    <span class="nav-title">Panel</span>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-users"></i>
                        <span class="nav-item">Gestión de Usuarios</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="auditores.php">Auditores</a></li>
                        <li>
                            <a href="#">Clientes</a>
                            <ul class="submenu">
                                <li><a href="clientes.php">+ Clientes</a></li>
                                <li><a href="ciudad.php">+ Ciudades</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="nav-item">Gestión de Auditoría</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="requerimiento.php">Requerimientos</a></li>
                        <li><a href="tarea.php">Tareas</a></li>
                        <li><a href="proyectos_auditoria.php">Proyectos de Auditoría</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-item">Gestión de Agenda</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="calendario.php">Calendario de Eventos</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-item">Control de Auditoría</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="control.php">Control</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-file-alt"></i>
                        <span class="nav-item">Informes</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="informes_auditoria.php">Informes de Auditoría</a></li>
                    </ul>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-item">Cerrar sesión</span>
                    </a>
                </li>
            </ul>
        </nav>
        <section class="main-background">
            <div class="main-container">
                <div class="main-top">
                    <div class="title-container">
                        <h1>Bienvenido/a, <?php echo htmlspecialchars($nombreUsuario); ?></h1>
                        <a href="/TESIS_SISTEMA/Manuales de usuario/Manual de Usuario General.pdf" class="btn-help" title="Ayuda">
                            <i class="fas fa-question-circle help-icon"></i>
                            <span>Ayuda</span>
                        </a>
                    </div>
                </div>
                <! <div class="main-skills">
                    <h3>Próximos Eventos</h3>
                    <div class="events-container">
                        <?php
                        // Configurar la localización para fechas en español
                        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain');

                        // Consulta de eventos
                        $queryEventos = "SELECT Nombre AS titulo, Fecha_evento FROM eventos WHERE Fecha_evento > NOW() ORDER BY Fecha_evento ASC LIMIT 5";
                        $resultEventos = $conn->query($queryEventos);

                        if ($resultEventos->num_rows > 0) {
                            while ($evento = $resultEventos->fetch_assoc()) {
                                // Obtener el mes y la fecha en español
                                $mes = strtoupper(strftime("%B", strtotime($evento['Fecha_evento'])));
                                $fechaCompleta = strftime("%d de %B de %Y, %H:%M", strtotime($evento['Fecha_evento']));
                                $titulo = $evento['titulo'];
                                ?>
                                <div class="events-card">
                                    <div class="events-header"><?php echo $mes; ?></div>
                                    <div class="events-content">
                                        <h4 class="event-title"><?php echo $titulo; ?></h4>
                                        <div class="event-date">
                                            <i class="fas fa-clock"></i> <?php echo $fechaCompleta; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p class='no-events'><i class='fas fa-info-circle'></i> No hay próximos eventos registrados.</p>";
                        }
                        ?>
                    </div>

                    <div class="calendar-button">
                        <a href="calendario.php" class="btn-calendar">
                            <i class="fas fa-calendar-alt"></i> Ir al Calendario
                        </a>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </div>
</body>
</html>
