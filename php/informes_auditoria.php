  <!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear Informe de Auditoría</title>
<link rel="stylesheet" href="style_informe.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.4.6/jscolor.min.js"></script>
</head>
<body>
<div class="container">
  <h1>Crear Informe de Auditoría</h1>
  <div class="format-toolbar">
    <button type="button" onclick="toggleBold()"><b>B</b></button>
    <button type="button" onclick="toggleItalic()"><i>I</i></button>
    <button type="button" onclick="toggleUnderline()"><u>U</u></button>
    <button type="button" onclick="document.execCommand('strikeThrough', false, null)"><s>S</s></button>
    <input type="color" onchange="changeFontColor(this.value)">
    <select id="fontSizeSelect" onchange="changeFontSize()">
      <option value="10">10</option>
      <option value="12">12</option>
      <option value="14">14</option>
      <option value="16">16</option>
      <option value="18">18</option>
      <option value="20">20</option>
      <option value="24">24</option>
      <option value="28">28</option>
      <option value="32">32</option>
    </select>
    <button type="button" onclick="alignText('left')"><i class="fas fa-align-left"></i></button>
    <button type="button" onclick="alignText('center')"><i class="fas fa-align-center"></i></button>
    <button type="button" onclick="alignText('right')"><i class="fas fa-align-right"></i></button>
    <button type="button" onclick="alignText('justify')"><i class="fas fa-align-justify"></i></button>
    <button type="button" onclick="document.execCommand('insertOrderedList', false, null)"><i class="fas fa-list-ol"></i></button>
    <button type="button" onclick="document.execCommand('insertUnorderedList', false, null)"><i class="fas fa-list-ul"></i></button>
    <select onchange="changeFont(this.value)">
      <option value="Arial">Arial</option>
      <option value="Verdana">Verdana</option>
      <option value="Times New Roman">Times New Roman</option>
      <option value="Courier New">Courier New</option>
      <option value="Georgia">Georgia</option>
    </select>
  </div>
  <form action="generar_informe.php" method="post" onsubmit="fillHiddenFields()">
    <label for="fecha_creacion">Fecha de Creación:</label>
    <input type="date" id="fecha_creacion" name="fecha_creacion" required><br>
    <label for="fecha_entrega">Fecha de Entrega:</label>
    <input type="date" id="fecha_entrega" name="fecha_entrega" required><br>
    <label for="autor">Autor:</label>
    <input type="text" id="autor" name="autor" required><br>
    <label for="tipo_informe">Tipo de Informe:</label>
    <select id="tipo_informe" name="tipo_informe">
      <option value="1">Informe A</option>
      <option value="2">Informe B</option>
      <option value="3">Informe C</option>
    </select><br>
    <label for="descripcion">Descripción:</label>
    <div contenteditable="true" class="editable" id="descripcion"></div>
    <input type="hidden" id="hidden_descripcion" name="descripcion"><br>
    <label for="observaciones">Observaciones:</label>
    <div id="observaciones-container">
      <div contenteditable="true" class="editable"></div>
      <button type="button" onclick="addObservation()"><i class="fas fa-plus"></i> Agregar Observación</button>
      <button type="button" onclick="cancelLastObservation()"><i class="fas fa-times"></i> Cancelar</button>
    </div><br>
    <label for="recomendaciones">Recomendaciones:</label>
    <div id="recomendaciones-container">
      <div contenteditable="true" class="editable"></div>
      <button type="button" onclick="addRecommendation()"><i class="fas fa-plus"></i> Agregar Recomendación</button>
      <button type="button" onclick="cancelLastRecommendation()"><i class="fas fa-times"></i> Cancelar</button>
    </div><br>
     <button type="submit" class="custom-button"><i class="fas fa-file-alt"></i> Generar Informe</button>
    <a href="admin.php" style="display: inline-block; padding: 8px 8px; background-color: #007bff; color: #ffffff; text-decoration: none; border: none; border-radius: 5px; cursor: pointer;"><i class="fas fa-arrow-left"></i> Volver a la página principal</a>
  </form>
</div>
<script>
  function fillHiddenFields() {
    document.getElementById('hidden_descripcion').value = document.getElementById('descripcion').innerHTML;
    
    const observations = document.querySelectorAll('#observaciones-container .editable');
    observations.forEach((obs, index) => {
      let hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = 'observaciones[]';
      hiddenField.value = obs.innerHTML;
      document.querySelector('form').appendChild(hiddenField);
    });

    const recommendations = document.querySelectorAll('#recomendaciones-container .editable');
    recommendations.forEach((rec, index) => {
      let hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = 'recomendaciones[]';
      hiddenField.value = rec.innerHTML;
      document.querySelector('form').appendChild(hiddenField);
    });
  }

  function alignText(align) {
    document.getElementById('descripcion').style.textAlign = align;
  }

  function changeFontColor(color) {
    document.getElementById('descripcion').style.color = color;
  }

  function changeFontSize() {
    var size = document.getElementById('fontSizeSelect').value;
    document.getElementById('descripcion').style.fontSize = size + 'px';
  }

  function toggleUnderline() {
    var textarea = document.getElementById('descripcion');
    textarea.style.textDecoration = textarea.style.textDecoration === 'underline' ? 'none' : 'underline';
  }

  function toggleBold() {
    var textarea = document.getElementById('descripcion');
    textarea.style.fontWeight = textarea.style.fontWeight === 'bold' ? 'normal' : 'bold';
  }

  function toggleItalic() {
    var textarea = document.getElementById('descripcion');
    textarea.style.fontStyle = textarea.style.fontStyle === 'italic' ? 'normal' : 'italic';
  }

  function changeFont(font) {
    document.getElementById('descripcion').style.fontFamily = font;
  }

  function addObservation() {
    const container = document.getElementById('observaciones-container');
    const newObservation = document.createElement('div');
    newObservation.contentEditable = true;
    newObservation.classList.add('editable');
    container.appendChild(newObservation);
  }

  function cancelLastObservation() {
    const container = document.getElementById('observaciones-container');
    const observations = container.querySelectorAll('.editable');
    if (observations.length > 1) {
      observations[observations.length - 1].remove();
    }
  }

  function addRecommendation() {
    const container = document.getElementById('recomendaciones-container');
    const newRecommendation = document.createElement('div');
    newRecommendation.contentEditable = true;
    newRecommendation.classList.add('editable');
    container.appendChild(newRecommendation);
  }

  function cancelLastRecommendation() {
    const container = document.getElementById('recomendaciones-container');
    const recommendations = container.querySelectorAll('.editable');
    if (recommendations.length > 1) {
      recommendations[recommendations.length - 1].remove();
    }
  }
</script>
</body>
</html>