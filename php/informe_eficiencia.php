<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Función para obtener las tareas de auditoría completadas
function obtenerTareasCompletadas() {
    $conn = conectarDB();
    
    // Consulta SQL para obtener las tareas de auditoría completadas
    $sql = "SELECT t.Idtarea, 
                   t.Descripcion, 
                   t.Fecha_inicio, 
                   t.Fecha_fin, 
                   t.Estado_tarea, 
                   c.Nombre AS CreadorTarea, 
                   u.nombre AS NombreResponsable, 
                   p.Descripcion AS NombreProyecto
            FROM tareas t
            LEFT JOIN proyecto_auditoria p ON t.Idproyecto = p.Idproyecto
            LEFT JOIN usuarios u ON t.Responsable = u.IDusuario
            LEFT JOIN usuarios c ON t.Creador_tarea = c.IDusuario
            WHERE t.Estado_tarea = 'Completado'"; // Filtrar por tareas completadas
    
    $result = $conn->query($sql);
    
    // Arreglo para almacenar las tareas completadas
    $tareasCompletadas = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Calcular la duración en días
            $fechaInicio = new DateTime($row['Fecha_inicio']);
            $fechaFin = new DateTime($row['Fecha_fin']);
            $duracion = $fechaInicio->diff($fechaFin);
            
            // Guardar solo los días en el formato requerido
            $row['Duracion'] = $duracion->days; // Obtener solo los días
            
            $tareasCompletadas[] = $row;
        }
    }
    
    $conn->close();
    
    return $tareasCompletadas;
}

// Obtener las tareas completadas
$tareasCompletadas = obtenerTareasCompletadas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Ejecución de Auditorías</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos para el informe */
        body {
            font-family: Arial, sans-serif;
            background-color: #a6bbd7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1800px; 
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
            margin-right: 20px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-align: center;
            flex-grow: 1;
        }

        .table-container {
            margin-top: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <img src="images/logo.jpg" alt="Logo de la entidad" id="logo">
        <h1 class="title">Informe de Ejecución de Auditorías</h1>
        
        <?php if (!empty($tareasCompletadas)): ?>
        <h2>Detalles de las Tareas Completadas</h2>
        <div class="table-container">
        <table id="table-id" class="table table-bordered">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Duración (días)</th>
                    <th>Creador de Tarea</th>
                    <th>Responsable</th>
                    <th>Proyecto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tareasCompletadas as $tarea): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tarea['Descripcion']); ?></td>
                    <td><?php echo $tarea['Fecha_inicio']; ?></td>
                    <td><?php echo $tarea['Fecha_fin']; ?></td>
                    <td><?php echo $tarea['Duracion']; ?></td>
                    <td><?php echo htmlspecialchars($tarea['CreadorTarea']); ?></td> <!-- Nombre del creador -->
                    <td><?php echo htmlspecialchars($tarea['NombreResponsable']); ?></td>
                    <td><?php echo htmlspecialchars($tarea['NombreProyecto']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <p>No se encontraron tareas de auditoría completadas.</p>
        <?php endif; ?>

        <div class="btn-export">
            <button class="pdf btn btn-danger" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </button>
            <button class="excel btn btn-success" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </button>
            <a href="admin.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Volver a la Página Principal
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script>
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const logo = document.getElementById('logo').src;

            doc.addImage(logo, 'JPEG', 10, 10, 50, 20);
            doc.setFontSize(18);
            doc.text("Informe de Ejecución de Auditorías", doc.internal.pageSize.getWidth() / 2, 35, { align: 'center' });

            doc.autoTable({
                html: '#table-id',
                startY: 50,
                headStyles: { fillColor: [0, 96, 100], textColor: [255, 255, 255] },
                styles: { fontSize: 8 }
            });

            doc.save('informe_ejecucion_auditorias.pdf');
        }

        function exportToExcel() {
            const table = document.getElementById("table-id");
            const workbook = XLSX.utils.table_to_book(table, { sheet: "Tareas" });
            XLSX.writeFile(workbook, 'informe_ejecucion_auditorias.xlsx');
        }
    </script>
</body>
</html>
