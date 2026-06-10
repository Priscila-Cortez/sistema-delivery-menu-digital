<?php
require_once __DIR__ . "/../models/Plato.php";

class PlatoController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Plato();
    }

    public function index() {
        $platos = $this->modelo->obtenerTodos();
        require_once __DIR__ . "/../views/listar.php";
    }

    public function crear() {
        require_once __DIR__ . "/../views/insertar.php";
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->modelo->insertar(
                $_POST['codigo'],
                $_POST['nombre'],
                $_POST['categoria'],
                $_POST['precio'],
                $_POST['tiempo_preparacion'],
                $_POST['estado']
            );
            header("Location: index.php?accion=listar");
            exit();
        }
    }

    public function editar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $plato = $this->modelo->obtenerPorId($id);
            require_once __DIR__ . "/../views/editar.php";
        } else {
            header("Location: index.php?accion=listar");
            exit();
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->modelo->actualizar(
                $_POST['id'],
                $_POST['codigo'],
                $_POST['nombre'],
                $_POST['categoria'],
                $_POST['precio'],
                $_POST['tiempo_preparacion'],
                $_POST['estado']
            );
            header("Location: index.php?accion=listar");
            exit();
        }
    }

    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->modelo->eliminar($id);
        }
        header("Location: index.php?accion=listar");
        exit();
    }

    public function agregarAlCarrito() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $id = $_GET['id'] ?? null;
        if ($id) {
            $plato = $this->modelo->obtenerPorId($id);
            if ($plato && $plato['estado'] == 'Disponible') {
                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }
                
                if (isset($_SESSION['carrito'][$id])) {
                    $_SESSION['carrito'][$id]['cantidad']++;
                } else {
                    $_SESSION['carrito'][$id] = [
                        'id' => $plato['id'],
                        'nombre' => $plato['nombre'],
                        'precio' => $plato['precio'],
                        'cantidad' => 1
                    ];
                }
            }
        }
        header("Location: index.php?accion=listar");
        exit();
    }

    public function limpiarCarrito() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['carrito'])) {
            unset($_SESSION['carrito']);
        }
        if (isset($_SESSION['datos_cliente'])) {
            unset($_SESSION['datos_cliente']);
        }
        header("Location: index.php?accion=listar");
        exit();
    }

    public function procesarPedido() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SESSION['carrito'])) {
            $cliente = $_POST['cliente'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $total = $_POST['total_pagar'];
            $encargado = $_SESSION['usuario'] ?? 'Sistema Web';

            $this->modelo->registrarPedido($cliente, $direccion, $telefono, $total, $encargado);

            $_SESSION['datos_cliente'] = [
                'nombre' => $cliente,
                'direccion' => $direccion,
                'telefono' => $telefono,
                'encargado' => $encargado
            ];

            header("Location: views/reporte.php");
            exit();
        } else {
            header("Location: index.php?accion=listar");
            exit();
        }
    }
}
?>