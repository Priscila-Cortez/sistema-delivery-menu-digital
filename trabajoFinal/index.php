<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "config/conexion.php";
require_once "controllers/PlatoController.php";

$controlador = new PlatoController();
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'bienvenida';

$acciones_privadas = ['crear', 'guardar', 'editar', 'actualizar', 'eliminar'];
if (in_array($accion, $acciones_privadas) && !isset($_SESSION['usuario'])) {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>
            <h3>Acceso denegado</h3>
            <p>Solo personal autorizado puede realizar esto.</p>
            <a href='index.php?accion=bienvenida'>Volver al Inicio</a>
         </div>");
}

switch ($accion) {
    case 'bienvenida':
        require_once "views/bienvenida.php";
        break;

    case 'ser_cliente':
        unset($_SESSION['usuario']);
        unset($_SESSION['nombre_completo']);
        
        $_SESSION['rol_cliente'] = true;
        header("Location: index.php?accion=listar");
        exit();
        break;

    case 'login':
        header("Location: views/login.php");
        exit();
        break;

    case 'logout':
        header("Location: logout.php");
        exit();
        break;

    case 'listar': $controlador->index(); break;
    case 'crear': $controlador->crear(); break;
    case 'guardar': $controlador->guardar(); break;
    case 'editar': $controlador->editar(); break;
    case 'actualizar': $controlador->actualizar(); break;
    case 'eliminar': $controlador->eliminar(); break;
    case 'agregar': $controlador->agregarAlCarrito(); break;
    case 'limpiar': $controlador->limpiarCarrito(); break;
    case 'procesar_pedido': $controlador->procesarPedido(); break;

    default:
        header("Location: index.php?accion=bienvenida");
        exit();
        break;
}
?>