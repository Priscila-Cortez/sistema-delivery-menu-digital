<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Digital - El Buen Sabor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">El Buen Sabor</span>
        <div>
            <?php if(isset($_SESSION['usuario'])): ?>
                <span class="text-white me-3">Encargado(a): <strong><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? $_SESSION['usuario']); ?></strong></span>
                <a href="index.php?accion=logout" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            <?php else: ?>
                <span class="text-white-50 me-3">Rol: <strong>Cliente Invitado</strong></span>
                <a href="index.php?accion=logout" class="btn btn-outline-light btn-sm">Volver al Inicio</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        
        <div class="<?php echo isset($_SESSION['usuario']) ? 'col-12' : 'col-md-8'; ?>">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Carta del Día</h5>
                    <?php if(isset($_SESSION['usuario'])): ?>
                        <a href="index.php?accion=crear" class="btn btn-success btn-sm"> Agregar Nuevo Plato</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-striped align-middle" style="font-size: 14px;">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($platos)): ?>
                                <?php foreach($platos as $plato): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($plato['codigo']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($plato['nombre']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($plato['categoria']); ?></span></td>
                                    <td>S/. <?php echo number_format($plato['precio'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($plato['estado'] == 'Disponible') ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $plato['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(!isset($_SESSION['usuario'])): ?>
                                            <?php if($plato['estado'] == 'Disponible'): ?>
                                                <a href="index.php?accion=agregar&id=<?php echo $plato['id']; ?>" class="btn btn-primary btn-sm">Añadir al Pedido</a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>Agotado</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if(isset($_SESSION['usuario'])): ?>
                                            <a href="index.php?accion=editar&id=<?php echo $plato['id']; ?>" class="btn btn-warning btn-sm fw-bold">Editar</a>
                                            <a href="views/eliminar.php?id=<?php echo $plato['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este plato?')" class="btn btn-danger btn-sm"> Eliminar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center text-muted">No hay platos en el menú.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if(!isset($_SESSION['usuario'])): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tu Pedido Actual</h5>
                    <a href="index.php?accion=limpiar" class="btn btn-light btn-sm fw-bold" style="font-size: 11px;">Vaciar</a>
                </div>
                <div class="card-body">
                    <?php if(!empty($_SESSION['carrito'])): ?>
                        <ul class="list-group mb-3">
                            <?php 
                            $totalPedido = 0;
                            foreach($_SESSION['carrito'] as $item): 
                                $sub = $item['precio'] * $item['cantidad'];
                                $totalPedido += $sub;
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center" style="font-size: 13px;">
                                    <div>
                                        <span class="badge bg-primary rounded-pill me-1"><?php echo $item['cantidad']; ?></span> 
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                    </div>
                                    <span class="text-dark fw-bold">S/. <?php echo number_format($sub, 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span><strong>Total a Pagar:</strong></span>
                            <span class="text-danger h5 mb-0"><strong>S/. <?php echo number_format($totalPedido, 2); ?></strong></span>
                        </div>
                        
                        <form action="index.php?accion=procesar_pedido" method="POST" class="mt-2">
                            <input type="hidden" name="total_pagar" value="<?php echo $totalPedido; ?>">
                            
                            <div class="mb-2">
                                <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 12px;">Nombre y Apellidos:</label>
                                <input type="text" name="cliente" class="form-control form-control-sm" required placeholder="Ej. Juan Pérez">
                            </div>
                            <div class="mb-2">
                                <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 12px;">Dirección de Entrega:</label>
                                <input type="text" name="direccion" class="form-control form-control-sm" required placeholder="Ej. Av. Larco 456, Trujillo">
                            </div>
                            <div class="mb-3">
                                <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 12px;">Número Telefónico:</label>
                                <input type="tel" name="telefono" class="form-control form-control-sm" required placeholder="Ej. 987654321">
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm"> Confirmar Pedido y Ver Boleta</button>
                        </form>
                    <?php else: ?>
                        <div class="text-center my-4 py-3 text-muted">
                            <p style="font-size: 13px;">Tu carrito está vacío.<br>Selecciona platos de la izquierda.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
</body>
</html>