<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?accion=listar");
    exit();
}

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../models/Plato.php";

$id = $_GET['id'] ?? null;

if ($id) {
    $modeloPlato = new Plato();
    $modeloPlato->eliminar($id); 
}

header("Location: ../index.php?accion=listar");
exit();
?>