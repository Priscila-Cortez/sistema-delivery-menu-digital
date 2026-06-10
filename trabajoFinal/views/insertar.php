<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Plato - El Buen Sabor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container" style="max-width: 600px;">
    <div class="card shadow border-0">
        <div class="card-header bg-success text-white text-center">
            <h4 class="mb-0 fw-bold"> Agregar Nuevo Plato</h4>
        </div>
        <div class="card-body p-4">
            <form action="index.php?accion=guardar" method="POST">

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Código:</label>
                    <input class="form-control" type="text" name="codigo" placeholder="Ej: PL01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Nombre del Plato:</label>
                    <input class="form-control" type="text" name="nombre" placeholder="Ej: Lomo Saltado" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Categoría:</label>
                    <input class="form-control" type="text" name="categoria" placeholder="Ej: Criollo">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Precio (S/.):</label>
                    <input class="form-control" type="number" step="0.01" name="precio" placeholder="0.00" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Tiempo (min):</label>
                    <input class="form-control" type="number" name="tiempo_preparacion" placeholder="Ej: 15">
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">Estado Inicial:</label>
                    <select class="form-select" name="estado">
                        <option value="Disponible">Disponible</option>
                        <option value="Agotado">Agotado</option>
                    </select>
                </div>

                <div class="d-grid gap-2 border-top pt-3">
                    <button type="submit" class="btn btn-success btn-lg fw-bold" name="guardar">Guardar Plato</button>
                    <a href="index.php?accion=listar" class="btn btn-secondary">Volver al Menú</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>