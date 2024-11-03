<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Proyectos de Auditoría</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-0XnqybGF87QozmrzJCSFs8fz3tuZPmX9m/ftLk2iVDxKcD6f3tFCCSUe+ZTehId5jY5vby6cfGH7b63Kec7YVA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
       /* Estilos para el informe */
        body {
            font-family: Arial, sans-serif;
            background-color: #a6bbd7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1500px; /* Ajustado para un ancho más manejable en pantalla */
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        .header img {
            max-width: 150px;
            margin-right: 20px; /* Aumentado el espacio entre la imagen y el título */
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-align: center;
            flex-grow: 1;
        }

        .table-container {
            margin-top: 20px; /* Espacio adicional entre la tabla y los botones */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #006064;
            color: #fff;
        }

        .btn-export {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-export button {
            border-radius: 5px;
            transition: background-color 0.3s ease; /* Transición suave para el cambio de color */
            display: inline-flex;
            align-items: center;
            padding: 10px 20px; /* Ajuste del padding para separar más los iconos del texto */
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-export button.pdf {
            background-color: #dc3545; /* Rojo */
            color: white;
        }

        .btn-export button.excel {
            background-color: #28a745; /* Verde */
            color: white;
        }

        .btn-export button.primary {
            background-color: #0056b3; /* Azul */
            color: white;
        }

        .btn-export button:hover {
            filter: brightness(85%);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-export a.btn-primary {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px; /* Ajuste del padding para separar más los iconos del texto */
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #0056b3;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-export a.btn-primary:hover {
            filter: brightness(85%);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo.jpg" alt="Logo de la entidad" id="logo">
            <div class="title">Informe de Proyectos de Auditoría</div>
        </div>

        <div class="table-container">
            <table class="table" id="tableData">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Prioridad</th>
                        <th>Auditores Asignados</th>
                        <th>Fase del Proyecto</th>
                        <th>Estado Actual</th>
                        <th>Fecha de Creación</th>
                        <th>Creado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Conexión a la base de datos y consulta SQL
                    include('conexion.php'); // Archivo que contiene la función conectarDB

                    $conn = conectarDB(); // Intentamos establecer la conexión a la base de datos

                    if ($conn->connect_error) {
                        die("Error en la conexión: " . $conn->connect_error);
                    }

                    $conn->set_charset("utf8"); // Establecer la codificación UTF-8

                    $sql = "SELECT 
                                p.Idproyecto, 
                                p.Descripcion, 
                                p.Fecha_inicio, 
                                p.Fecha_fin, 
                                p.Prioridad, 
                                GROUP_CONCAT(CONCAT('<li>', a.Nombre, ' - ', a.NivelExperiencia, '</li>') SEPARATOR '') as Auditores,
                                p.Fase_proyecto, 
                                p.Estado, 
                                p.Fecha_creacion,
                                p.Creador_proyecto as NombreCreador  /* Renombramos el campo Creador_proyecto */
                            FROM 
                                proyecto_auditoria p
                                LEFT JOIN equipo_trabajo et ON p.Idproyecto = et.Idproyecto
                                LEFT JOIN auditores a ON et.IDusuario = a.IDusuario
                            GROUP BY p.Idproyecto
                            ORDER BY 
                                CASE 
                                    WHEN p.Estado = 'Completado' THEN 1
                                    WHEN p.Estado = 'En Progreso' THEN 2
                                    WHEN p.Estado = 'Planificación' THEN 3
                                    ELSE 4
                                END,
                                p.Fecha_fin"; // Ordenar por Estado y luego por Fecha de Fin

                    $result = $conn->query($sql); // Ejecutamos la consulta SQL

                    if ($result->num_rows > 0) {
                        while ($row_data = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row_data['Descripcion']) . "</td>";
                            echo "<td>" . $row_data['Fecha_inicio'] . "</td>";
                            echo "<td>" . $row_data['Fecha_fin'] . "</td>";
                            echo "<td>" . $row_data['Prioridad'] . "</td>";
                            echo "<td>";
                            $auditores = explode('<li>', $row_data['Auditores']);
                            foreach ($auditores as $auditor) {
                                if (!empty($auditor)) {
                                    echo htmlspecialchars(str_replace('</li>', '', $auditor)) . "<br>";
                                }
                            }
                            echo "</td>";
                            echo "<td>" . $row_data['Fase_proyecto'] . "</td>";
                            echo "<td>" . $row_data['Estado'] . "</td>";
                            echo "<td>" . $row_data['Fecha_creacion'] . "</td>";
                            echo "<td>" . $row_data['NombreCreador'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="9">No hay proyectos registrados.</td></tr>';
                    }

                    // Cerrar la conexión después de ejecutar la consulta
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

        <div class="btn-export mt-3">
            <button class="pdf btn btn-danger me-2" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </button>
            <button class="excel btn btn-success me-2" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </button>
            <a href="admin.php" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Volver a la Página Principal
            </a>
        </div>


    </div>

    <!-- Bibliotecas para los iconos de Font Awesome y otros scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script>
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Agregar el logo
            const logo = document.getElementById('logo');
            const logoDataUrl = logo.src;

            // Dibujar el logo en el PDF
            doc.addImage(logoDataUrl, 'JPEG', 10, 10, 50, 20);

            // Título del documento
            doc.setFontSize(18);
            doc.text("Informe de Proyectos de Auditoría", doc.internal.pageSize.getWidth() / 2, 35, { align: 'center' });

            // Configurar el formato de la tabla
            const options = {
                margin: { top: 5 }, // Ajustado para dejar espacio mínimo entre el título y la tabla
                headStyles: { fillColor: [0, 96, 100], textColor: [255, 255, 255], fontSize: 7}, // Tamaño de letra reducido para el encabezado
                bodyStyles: { fontSize: 7}, // Tamaño de letra reducido para el cuerpo de la tabla
                startY: 40, // Ajuste menor en la posición inicial de la tabla
                styles: {
                    cellPadding: 3,
                    overflow: 'linebreak',
                    fontSize: 8, // Tamaño de letra general para la tabla
                    valign: 'middle'
                },
                columnStyles: {
                    0: { cellWidth: 20 }, // Descripción
                    1: { cellWidth: 20 }, // Inicio
                    2: { cellWidth: 20 }, // Fin
                    3: { cellWidth: 20 }, // Prioridad
                    4: { cellWidth: 30 }, // Auditores
                    5: { cellWidth: 20 }, // Fase del Proyecto
                    6: { cellWidth: 20 }, // Estado Actual
                    7: { cellWidth: 20 }, // Fecha de Creación
                    8: { cellWidth: 20 }  // Creado por
                }
            };

            // Obtener la tabla HTML y generar la tabla en el PDF
            doc.autoTable({
                html: '#tableData',
                ...options
            });

            // Guardar el PDF con un nombre específico
            doc.save('proyectos_auditoria.pdf');
        }

        function exportToExcel() {
            // Obtener la tabla HTML
            var table = document.getElementById("tableData");
            var workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet 1" });
            XLSX.writeFile(workbook, 'proyectos_auditoria.xlsx');
        }
    </script>
</body>
</html>
