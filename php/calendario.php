<?php
session_start();
include('conexion.php');

// Conectar a la base de datos
$conn = conectarDB();

// Guardar la conexión en la sesión
$_SESSION['conn'] = $conn;

// Obtener tipos de evento
$sql_tipo_evento = "SELECT Id_tipoevento, Descripcion FROM tipo_evento";
$result_tipo_evento = mysqli_query($conn, $sql_tipo_evento);

if (!$result_tipo_evento) {
    die("Error al obtener tipos de evento: " . mysqli_error($conn));
}

$tipos_evento = mysqli_fetch_all($result_tipo_evento, MYSQLI_ASSOC);

// Obtener proyectos
$sql_proyecto = "SELECT Idproyecto, Descripcion FROM proyecto_auditoria";
$result_proyecto = mysqli_query($conn, $sql_proyecto);

if (!$result_proyecto) {
    die("Error al obtener proyectos: " . mysqli_error($conn));
}

$proyectos = mysqli_fetch_all($result_proyecto, MYSQLI_ASSOC);

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar y asignar valores desde $_POST
    $nombre = isset($_POST['Nombre']) ? $_POST['Nombre'] : '';
    $fecha_evento = isset($_POST['Fecha_evento']) ? $_POST['Fecha_evento'] : '';
    $descripcion = isset($_POST['Descripcion']) ? $_POST['Descripcion'] : '';
    $lugar = isset($_POST['Lugar']) ? $_POST['Lugar'] : '';
    $fecha_vencimiento = isset($_POST['Fecha_vencimiento']) ? $_POST['Fecha_vencimiento'] : '';
    $id_tipoevento = isset($_POST['Id_tipoevento']) ? $_POST['Id_tipoevento'] : '';
    $color = isset($_POST['Color']) ? $_POST['Color'] : '';
    $id_proyecto = isset($_POST['Idproyecto']) ? $_POST['Idproyecto'] : '';

    // Validar que el campo Nombre no esté vacío
    if (!empty($nombre)) {
        // Formatear las fechas para MySQL
        $fecha_evento_mysql = date('Y-m-d H:i:s', strtotime($fecha_evento));
        $fecha_vencimiento_mysql = date('Y-m-d H:i:s', strtotime($fecha_vencimiento));
        
        // Insertar el evento en la base de datos
        $sql_insert_evento = "INSERT INTO eventos (Nombre, Fecha_evento, Descripcion, Lugar, Fecha_vencimiento, Id_tipoevento, Color, Idproyecto)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert_evento = $conn->prepare($sql_insert_evento);
        $stmt_insert_evento->bind_param("sssssssi", $nombre, $fecha_evento_mysql, $descripcion, $lugar, $fecha_vencimiento_mysql, $id_tipoevento, $color, $id_proyecto);

        if ($stmt_insert_evento->execute()) {
            // Éxito en la inserción
            $_SESSION['success_message'] = "Nuevo evento creado exitosamente";
            header("Location: eventos.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error al agregar el evento: " . $stmt_insert_evento->error;
        }
    } else {
        $_SESSION['error_message'] = "El campo Nombre no puede estar vacío";
    }
}

// Obtener mensaje de éxito o error de la sesión
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Limpiar mensajes de sesión
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Eventos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color:#a6bbd7; /* Color de fondo para todo el cuerpo */
            padding-top: 20px; /* Espacio superior */
        }
        .container {
            max-width: 900px; /* Ancho máximo del contenedor principal */
            margin: 0 auto; /* Centrar el contenedor en la página */
            background-color: #fff; /* Fondo blanco del contenedor */
            padding: 20px; /* Espacio interno */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Sombra ligera */
        }
        #calendar {
            margin-top: 20px; /* Espacio superior para el calendario */
        }
        .fc-event {
            color: #fff !important; /* Color de texto para los eventos */
            position: relative; /* Necesario para posicionar la X */
        }
        .btn-custom {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .delete-event-btn {
            color: #fff;
            background-color: #dc3545;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            position: absolute;
            top: -20px; /* Colocar la X en la parte superior del evento */
            left: 5px; /* Ajustar el margen derecho */
            font-size: 1rem;
        }
        .delete-event-btn:hover {
            background-color: #c82333;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            font-size: 1.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        /* Botón Primario */
        .btn-custom {
            background-color: #007bff; /* Azul vibrante */
            border-color: #007bff; /* Azul vibrante */
            color: white;
        }

        .btn-custom:hover {
            background-color: #0056b3; /* Azul oscuro */
            border-color: #004085; /* Azul más oscuro */
        }

        /* Botón Secundario */
        .btn-secondary {
            background-color: #6c757d; /* Gris azul */
            border-color: #6c757d; /* Gris azul */
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268; /* Gris más oscuro */
            border-color: #545b62; /* Gris más oscuro */
        }

        /* Botón Custom Info */
        .btn-custom-info {
            background-color: #17a2b8; /* Azul claro */
            border-color: #17a2b8; /* Azul claro */
            color: white;
        }

        .btn-custom-info:hover {
            background-color: #138496; /* Azul más oscuro */
            border-color: #117a8b; /* Azul más oscuro */
        }

        /* Botón Custom Secondary */
        .btn-custom-secondary {
            background-color: #ffc107; /* Amarillo dorado */
            border-color: #ffc107; /* Amarillo dorado */
            color: black;
        }

        .btn-custom-secondary:hover {
            background-color: #e0a800; /* Amarillo más oscuro */
            border-color: #d39e00; /* Amarillo más oscuro */
        }

    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Calendario de Eventos</h1>
        <!-- Mensajes de éxito o error -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
        
        <div id="calendar"></div>
        <br>
        <button class="btn btn-primary btn-custom" data-toggle="modal" data-target="#eventModal">
            <i class="fas fa-plus"></i> Agregar Evento
        </button>
        <a href="admin.php" class="btn btn-secondary mr-2">
            <i class="fas fa-arrow-left"></i> Volver a la página principal
        </a>
        <a href="ver_detalles_eventos.php" class="btn btn-custom-info ml-2">
            <i class="fas fa-info-circle"></i> Ver Detalles de Eventos
        </a>
        <a href="/TESIS_SISTEMA/manuales_usuario/Gestión de Agenda – Calendario de Eventos.pdf" target="_blank" class="btn btn-custom-secondary">
            <i class="fas fa-question-circle"></i> Ayuda
        </a>    
    </div>

    <!-- Modal para agregar evento -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="eventForm" method="POST" action="add_event.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventModalLabel">Agregar Evento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="Nombre">Nombre</label>
                            <input type="text" class="form-control" id="Nombre" name="Nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="Fecha_evento">Fecha y Hora del Evento</label>
                            <input type="datetime-local" class="form-control" id="Fecha_evento" name="Fecha_evento" required>
                        </div>
                        <div class="form-group">
                            <label for="Descripcion">Descripción</label>
                            <textarea class="form-control" id="Descripcion" name="Descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="Lugar">Lugar</label>
                            <input type="text" class="form-control" id="Lugar" name="Lugar" required>
                        </div>
                        <div class="form-group">
                            <label for="Fecha_vencimiento">Fecha y Hora de Vencimiento</label>
                            <input type="datetime-local" class="form-control" id="Fecha_vencimiento" name="Fecha_vencimiento" required>
                        </div>
                        <div class="form-group">
                            <label for="Id_tipoevento">Tipo de Evento</label>
                            <select class="form-control" id="Id_tipoevento" name="Id_tipoevento" required>
                                <?php foreach ($tipos_evento as $tipo_evento): ?>
                                    <option value="<?= $tipo_evento['Id_tipoevento'] ?>"><?= $tipo_evento['Descripcion'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Color">Color</label>
                            <input type="color" class="form-control" id="Color" name="Color" required>
                        </div>
                        <div class="form-group">
                            <label for="Idproyecto">Proyecto</label>
                            <select class="form-control" id="Idproyecto" name="Idproyecto" required>
                                <?php foreach($proyectos as $proyecto): ?>
                                    <option value="<?= $proyecto['Idproyecto'] ?>"><?= $proyecto['Descripcion'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Dentro del modal eventModal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Evento</button>
                        <?php if (!empty($id_evento)): ?>
                            <!-- Mostrar botón para eliminar si existe un evento (solo para edición) -->
                            <button type="button" class="btn btn-danger" id="eliminarEvento">Eliminar Evento</button>
                        <?php endif; ?>
                    </div>
                </form>
               
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
    <script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: '' // Quita las opciones de month y agendaWeek
        },
        events: 'load_events.php',
        editable: true,
        selectable: true,
        selectHelper: true,
        select: function(start, end) {
            // Abre el modal para agregar o editar un evento y preselecciona la fecha
            $('#eventModal').modal('show');
            $('#Fecha_evento').val(start.format('YYYY-MM-DDTHH:mm')); // Formato necesario para input datetime-local
            $('#Fecha_vencimiento').val(end.format('YYYY-MM-DDTHH:mm')); // Formato necesario para input datetime-local

            // Limpia y guarda el ID de evento al agregar uno nuevo o editar uno existente
            $('#id_evento').val('');
        },
        eventRender: function(event, element) {
            element.append("<span class='delete-event-btn'>X</span>");
            element.find(".delete-event-btn").click(function() {
                if (confirm("¿Seguro que deseas eliminar este evento?")) {
                    deleteEvent(event.id);
                    $('#calendar').fullCalendar('removeEvents', event._id); // Elimina el evento del calendario
                }
            });
        }
    });

    // Función para eliminar el evento
    function deleteEvent(eventId) {
        $.ajax({
            url: 'delete_event.php',
            type: "POST",
            data: { id_evento: eventId },
            success: function(response) {
                alert('Evento eliminado exitosamente');
            },
            error: function(xhr, status, error) {
                alert('Error al eliminar el evento: ' + xhr.responseText);
            }
        });
    }

    // Eliminar evento desde el modal de edición
    $('#eliminarEvento').click(function() {
        var eventId = $('#id_evento').val();
        if (eventId !== '') {
            if (confirm("¿Seguro que deseas eliminar este evento?")) {
                deleteEvent(eventId);
                $('#calendar').fullCalendar('removeEvents', eventId); // Elimina el evento del calendario
                $('#eventModal').modal('hide'); // Oculta el modal después de eliminar el evento
            }
        } else {
            alert("No se puede eliminar el evento sin un ID válido.");
        }
    });
});
</script>
</body>
</html>
