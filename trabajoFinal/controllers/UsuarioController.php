<?php
require_once __DIR__ . "/../models/Usuario.php";

class UsuarioController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Usuario();
    }

    public function login($usuario, $password) {
        return $this->modelo->login($usuario, $password);
    }
}
?>