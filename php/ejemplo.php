<span style="font-family: verdana, geneva, sans-serif;"><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard | By Code Info</title>
  <link rel="stylesheet" href="estilo_ejemplo.css" />
  <!-- Font Awesome Cdn Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
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
                        <li><a href="usuarios.php">Usuarios</a></li>
                        <li><a href="roles.php">Roles</a></li>
                        <li><a href="auditores.php">Auditores</a></li>
                        <li><a href="ejemplo.php">Auditores</a></li>
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
        <li><a href="" class="logout">
          <i class="fas fa-sign-out-alt"></i>
          <span class="nav-item">Log out</span>
        </a></li>
      </ul>
    </nav>

    <section class="main">
        <div class="card">
        <div class="main-top">
                <?php
                // Incluir archivo de conexión
                include_once 'conexion.php';

                // Establecer conexión a la base de datos
                $conn = conectarDB();

                // Obtener el nombre de usuario
                $idUsuario = 1; // Aquí debes establecer el ID del usuario actualmente logueado
                $queryUsuario = "SELECT Nombre FROM usuarios WHERE IDusuario = $idUsuario";
                $resultUsuario = $conn->query($queryUsuario);
                
                if ($resultUsuario->num_rows > 0) {
                    $usuario = $resultUsuario->fetch_assoc();
                    $nombreUsuario = $usuario['Nombre'];
                } else {
                    $nombreUsuario = "Usuario Desconocido"; // Manejo de caso por defecto
                }
                ?>
                <h1>Bienvenido/a, <?php echo $nombreUsuario; ?></h1>
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="main-skills">
                <!-- Sección para mostrar los próximos eventos -->
                <div class="card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Próximos Eventos</h3>
                    <ul>
                        <?php
                        // Consulta para obtener los próximos eventos con detalles
                        $queryEventos = "SELECT e.Nombre AS titulo, e.Fecha_evento AS fecha_inicio
                                         FROM eventos e
                                         WHERE e.Fecha_evento > NOW()
                                         ORDER BY e.Fecha_evento ASC
                                         LIMIT 5";

                        $resultEventos = $conn->query($queryEventos);

                        // Verificar si se obtuvieron resultados
                        if ($resultEventos->num_rows > 0) {
                            while ($evento = $resultEventos->fetch_assoc()) {
                                echo "<li>{$evento['titulo']} - {$evento['fecha_inicio']}</li>";
                            }
                        } else {
                            echo "<p>No hay próximos eventos registrados.</p>";
                        }

                        // Cerrar conexión
                        $conn->close();
                        ?>
                    </ul>
                </div>
      </div>  
    </section>
  </div>
</body>
</html></span>