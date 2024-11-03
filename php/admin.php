<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo</title>
    <link rel="stylesheet" href="estiloadmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Menú de navegación -->
         <!-- Menú de navegación -->
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
                        <li><a href="usuarios.php">Usuarios</a></li>
                        <li><a href="roles.php">Roles</a></li>
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
                        <li><a href="informe_proyectos.php">Informes de Proyectos</a></li>
                        <li><a href="informe_tareas.php">Informes de Tareas</a></li>
                        <li><a href="informe_requerimientos.php">Informes de Requerimientos</a></li>
                        <li><a href="informe_eficiencia.php">Informes de Ejecucón de Auditoría</a></li>
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
        <section class="main">
            <div class="card">
                <div class="main-top">
                    <?php
                    include_once 'conexion.php';
                    $conn = conectarDB();
                    $idUsuario = 1;
                    $queryUsuario = "SELECT Nombre FROM usuarios WHERE IDusuario = $idUsuario";
                    $resultUsuario = $conn->query($queryUsuario);

                    if ($resultUsuario->num_rows > 0) {
                        $usuario = $resultUsuario->fetch_assoc();
                        $nombreUsuario = $usuario['Nombre'];
                    } else {
                        $nombreUsuario = "Usuario Desconocido";
                    }
                    ?>
                    <h1>Bienvenido/a, <?php echo $nombreUsuario; ?></h1>
                    <div class="top-icons">
                        <i class="fas fa-user-cog user-icon" title="Configuración de Usuario"></i>
                        <a href="ayuda.php" class="btn-help" title="Ayuda">
                            <i class="fas fa-question-circle help-icon"></i>
                        </a>
                    </div>
                </div>

                <div class="main-skills">
                    <!-- Sección de eventos -->
                    <div class="events-card">
                        <div class="events-header">
                            <h3>Próximos Eventos</h3>
                        </div>
                        <div class="events-container">
                            <ul class="events-list">
                                <?php
                                $queryEventos = "SELECT Nombre AS titulo, Fecha_evento FROM eventos WHERE Fecha_evento > NOW() ORDER BY Fecha_evento ASC LIMIT 5";
                                $resultEventos = $conn->query($queryEventos);

                                if ($resultEventos->num_rows > 0) {
                                    while ($evento = $resultEventos->fetch_assoc()) {
                                        $fecha = date("d M", strtotime($evento['Fecha_evento']));
                                        $titulo = $evento['titulo'];

                                        echo "
                                        <li class='event-item'>
                                            <span class='event-date'>$fecha</span>
                                            <span class='event-title'>$titulo</span>
                                        </li>";
                                    }
                                } else {
                                    echo "<p class='no-events'><i class='fas fa-info-circle'></i> No hay próximos eventos registrados.</p>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Botón para ir al calendario -->
                    <div class="calendar-button">
                        <a href="calendario.php" class="btn-calendar">
                            <i class="fas fa-calendar-alt"></i> Ir al Calendario
                        </a>  
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
