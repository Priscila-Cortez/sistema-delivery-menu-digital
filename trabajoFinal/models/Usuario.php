<?php
require_once __DIR__ . "/../config/conexion.php";

class Usuario {

    public function login($usuario, $password) {
        global $base;
        try {
            $sql = "SELECT * FROM usuarios WHERE usuario = :usuario AND password = :password";
            $stmt = $base->prepare($sql);
            $stmt->execute([
                ':usuario' => $usuario,
                ':password' => $password
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error en la autenticación del usuario: " . $e->getMessage());
        }
    }
}
?>