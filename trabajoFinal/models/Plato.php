<?php
require_once __DIR__ . "/../config/conexion.php";

class Plato {
    
    public function obtenerTodos() {
        global $base;
        try {
            $sql = "SELECT * FROM platos_menu ORDER BY id DESC";
            $stmt = $base->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al listar platos: " . $e->getMessage());
        }
    }

    public function obtenerPorId($id) {
        global $base;
        try {
            $sql = "SELECT * FROM platos_menu WHERE id = :id";
            $stmt = $base->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al buscar el plato: " . $e->getMessage());
        }
    }

    public function insertar($codigo, $nombre, $categoria, $precio, $tiempo_preparacion, $estado) {
        global $base;
        try {
            $sql = "INSERT INTO platos_menu (codigo, nombre, categoria, precio, tiempo_preparacion, estado) 
                    VALUES (:codigo, :nombre, :categoria, :precio, :tiempo, :estado)";
            $stmt = $base->prepare($sql);
            return $stmt->execute([
                ':codigo' => $codigo,
                ':nombre' => $nombre,
                ':categoria' => $categoria,
                ':precio' => $precio,
                ':tiempo' => $tiempo_preparacion,
                ':estado' => $estado
            ]);
        } catch (PDOException $e) {
            die("Error al insertar el plato: " . $e->getMessage());
        }
    }

    public function actualizar($id, $codigo, $nombre, $categoria, $precio, $tiempo_preparacion, $estado) {
        global $base;
        try {
            $sql = "UPDATE platos_menu SET codigo = :codigo, nombre = :nombre, categoria = :categoria, 
                    precio = :precio, tiempo_preparacion = :tiempo, estado = :estado WHERE id = :id";
            $stmt = $base->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':codigo' => $codigo,
                ':nombre' => $nombre,
                ':categoria' => $categoria,
                ':precio' => $precio,
                ':tiempo' => $tiempo_preparacion,
                ':estado' => $estado
            ]);
        } catch (PDOException $e) {
            die("Error al actualizar el plato: " . $e->getMessage());
        }
    }

    public function eliminar($id) {
        global $base;
        try {
            $sql = "DELETE FROM platos_menu WHERE id = :id";
            $stmt = $base->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            die("Error al eliminar el plato: " . $e->getMessage());
        }
    }

    public function registrarPedido($cliente, $direccion, $telefono, $total, $encargado) {
        global $base;
        try {
            $sql = "INSERT INTO pedidos_delivery (cliente, direccion, telephone_placeholder, total_pagar, fecha_pedido) 
                    VALUES (:cliente, :direccion, :telefono, :total, NOW())";
            
            $sql = "INSERT INTO pedidos_delivery (cliente, direccion, telefono, total_pagar, fecha_pedido) 
                    VALUES (:cliente, :direccion, :telefono, :total, NOW())";

            $stmt = $base->prepare($sql);
            $stmt->execute([
                ':cliente' => $cliente,
                ':direccion' => $direccion,
                ':telefono' => $telefono,
                ':total' => $total
            ]);
            return $base->lastInsertId();
        } catch (PDOException $e) {
            die("Error al guardar el pedido: " . $e->getMessage());
        }
    }
}
?>