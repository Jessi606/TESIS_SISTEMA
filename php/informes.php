<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crear Informe de Auditoría</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Crear Informe de Auditoría</h1>
    <form action="crear_informe.php" method="POST">
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="fecha_entrega">Fecha de Entrega</label>
            <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
        </div>
        <div class="form-group">
            <label for="autor">Autor</label>
            <input type="text" class="form-control" id="autor" name="autor" required>
        </div>
        <div class="form-group">
            <label for="plantilla">Plantilla Predefinida</label>
            <select class="form-control" id="plantilla" name="plantilla">
                <option value="1">Plantilla 1</option>
                <option value="2">Plantilla 2</option>
                <option value="3">Plantilla 3</option>
            </select>
        </div>
        <div class="form-group">
            <label for="observaciones">Observaciones</label>
            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="recomendaciones">Recomendaciones</label>
            <textarea class="form-control" id="recomendaciones" name="recomendaciones" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Crear Informe</button>
    </form>
</div>
</body>
</html>
