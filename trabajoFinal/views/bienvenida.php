<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido - El Buen Sabor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; display: flex; align-items: center; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>
<div class="container text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-5 bg-white">
                <h1 class="display-5 mb-3 text-dark fw-bold">El Buen Sabor</h1>
                <p class="text-muted mb-5">seleccione su rol de acceso en el sistema</p>
                <div class="d-grid gap-3">
                    <a href="index.php?accion=ser_cliente" class="btn btn-primary btn-lg shadow-sm py-3 fw-bold">Ver Menú / Realizar Pedido</a>
                    <a href="index.php?accion=login" class="btn btn-outline-dark btn-lg py-3 fw-bold">Acceso Personal Autorizado</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>