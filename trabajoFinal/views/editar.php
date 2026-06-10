<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Plato - El Buen Sabor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container" style="max-width: 600px;">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark text-center">
            <h4 class="mb-0 fw-bold">Editar Registro del Plato</h4>
        </div>
        <div class="card-body p-4">
            <form action="index.php?accion=actualizar" method="POST">
                
                <input type="hidden" name="id" value="<?php echo $plato['id']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Código:</label>
                    <input type="text" name="codigo" class="form-control" value="<?php echo htmlspecialchars($plato['codigo']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Nombre:</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($plato['nombre']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Categoría:</label>
                    <input type="text" name="categoria" class="form-control" value="<?php echo htmlspecialchars($plato['categoria']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Precio (S/.):</label>
                    <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo htmlspecialchars($plato['precio']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Tiempo Preparación (min):</label>
                    <input type="number" name="tiempo_preparacion" class="form-control" value="<?php echo htmlspecialchars($plato['tiempo_preparacion']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="Disponible" <?php echo ($plato['estado'] == 'Disponible') ? 'selected' : ''; ?>>Disponible</option>
                        <option value="Agotado" <?php echo ($plato['estado'] == 'Agotado') ? 'selected' : ''; ?>>Agotado</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between border-top pt-3 mt-4">
                    <a href="index.php?accion=listar" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" name="actualizar" class="btn btn-warning fw-bold">Actualizar Plato</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>