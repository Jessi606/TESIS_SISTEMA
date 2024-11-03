<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel</title>
    <link rel="stylesheet" href="estiloauditor.css">
    <!-- Font Awesome Cdn Link -->
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
    </div>
</body>
</html>
