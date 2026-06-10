<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['carrito']) || empty($_SESSION['datos_cliente'])) {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>
            <h3> No se encontraron datos para procesar la boleta.</h3>

            <a href='../index.php?accion=listar'>Volver al Menú</a>
         </div>");
}

$productos = $_SESSION['carrito'];
$cliente = $_SESSION['datos_cliente'];
$total = 0;

foreach ($productos as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

$subtotal = $total / 1.18;
$igv = $total - $subtotal;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Venta Electrónica</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 20px auto; color: #000; font-size: 13px; line-height: 1.4; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .lineas { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding-bottom: 5px; }
        .total-box { font-weight: bold; font-size: 14px; }
        
        .no-print-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px; font-family: sans-serif; }
        .btn-print { background: #28a745; color: white; padding: 8px 12px; text-decoration: none; font-weight: bold; border-radius: 4px; display: inline-block; margin-bottom: 10px; font-size: 12px; }
        .btn-back { color: #6c757d; text-decoration: none; font-size: 12px; display: block; }
        
        @media print { 
            .no-print-box { display: none !important; } 
            body { margin: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="no-print-box">
        <p style="margin-top:0; font-weight:bold; color:#333;">¡Pedido Generado!</p>
        <a href="#" onclick="window.print(); return false;" class="btn-print"> Imprimir / Guardar en PDF</a>
        <a href="../index.php?accion=limpiar" class="btn-back"> Volver al Menú Principal</a>
    </div>

    <div class="text-center">
        <strong>RESTAURANTE "EL BUEN SABOR"</strong><br>
        SABOR  S.A.C.<br>
        Av. Las Lomas 553<br>
        Paijan, La Libertad<br>
        RUC: 20458796321<br>
        <div class="lineas"></div>
        <strong>BOLETA ELECTRÓNICA</strong><br>
        B001 - <?php echo str_pad(rand(1, 99999), 6, "0", STR_PAD_LEFT); ?><br>
    </div>
    
    <div class="lineas"></div>
    
    <div>
        <strong>Fecha:</strong> <?php echo date("d/m/Y H:i:s"); ?><br>
        <strong>Cliente:</strong> <?php echo htmlspecialchars($cliente['nombre']); ?><br>
        <strong>Dirección:</strong> <?php echo htmlspecialchars($cliente['direccion']); ?><br>
        <strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['telefono']); ?><br>
    </div>
    
    <div class="lineas"></div>
    
    <table>
        <thead>
            <tr>
                <th>DESCRIPCIÓN</th>
                <th class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $prod): ?>
            <tr>
                <td style="padding: 3px 0;">
                    <?php echo $prod['cantidad']; ?>x <?php echo htmlspecialchars($prod['nombre']); ?>
                </td>
                <td class="text-right" style="vertical-align: top;">
                    S/. <?php echo number_format($prod['precio'] * $prod['cantidad'], 2); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="lineas"></div>
    
    <table>
        <tr>
            <td>Op. Gravada (Subtotal):</td>
            <td class="text-right">S/. <?php echo number_format($subtotal, 2); ?></td>
        </tr>
        <tr>
            <td>I.G.V. (18%):</td>
            <td class="text-right">S/. <?php echo number_format($igv, 2); ?></td>
        </tr>
        <tr class="total-box">
            <td>TOTAL COMPRA:</td>
            <td class="text-right">S/. <?php echo number_format($total, 2); ?></td>
        </tr>
    </table>
    
    <div class="lineas"></div>
    
    <div class="text-center" style="font-size: 11px; margin-top: 15px;">
        ¡Muchas gracias por su preferencia!<br>
        Su pedido llegará pronto a su Casa.
    </div>

</body>
</html>