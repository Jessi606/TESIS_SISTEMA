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
