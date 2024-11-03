<?php
// Ajustar la ruta para requerir el autoloader de PHPWord
require_once '../PHPWord-master/src/PhpWord/Autoloader.php';

// Registrar el autoloader de PHPWord
\PhpOffice\PhpWord\Autoloader::register();

// Importar clases necesarias de PHPWord
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// Recibir los datos del formulario
$fecha_creacion = $_POST['fecha_creacion'];
$fecha_entrega = $_POST['fecha_entrega'];
$autor = $_POST['autor'];
$tipo_informe = $_POST['tipo_informe'];
$descripcion = $_POST['descripcion'];
$observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : [];
$recomendaciones = isset($_POST['recomendaciones']) ? $_POST['recomendaciones'] : [];

// Crear un nuevo objeto PHPWord
$phpWord = new PhpWord();
$section = $phpWord->addSection();

// Agregar contenido al documento
$section->addTitle("Informe de Auditoría", 1);
$section->addText("Fecha de Creación: $fecha_creacion");
$section->addText("Fecha de Entrega: $fecha_entrega");
$section->addText("Autor: $autor");
$section->addText("Tipo de Informe: $tipo_informe");
$section->addText("Descripción:");
$section->addText($descripcion, array('name' => 'Arial', 'size' => 12));

// Agregar observaciones y recomendaciones
foreach ($observaciones as $key => $observacion) {
    $section->addText("Observación ".($key+1).": ".$observacion, array('name' => 'Arial', 'size' => 12));

    if(isset($recomendaciones[$key])) {
        $section->addText("Recomendación ".($key+1).": ".$recomendaciones[$key], array('name' => 'Arial', 'size' => 12));
    } else {
        $section->addText("Recomendación ".($key+1).": No se proporcionó una recomendación para esta observación.", array('name' => 'Arial', 'size' => 12));
    }

    // Agregar un salto de línea entre cada observación y recomendación
    $section->addTextBreak(1);
}

// Guardar el archivo en formato Word
$fileName = "Informe_Auditoria_" . date("Y-m-d_H-i-s") . ".docx";
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');

$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save("php://output");
exit;
?>
