<?php
$servidor = "localhost";
$usuarioBD = "root";
$passwordBD = "";
$nombreBD = "restaurante"; 

try {
    $base = new PDO("mysql:host=$servidor;dbname=$nombreBD;charset=utf8", $usuarioBD, $passwordBD);
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>
            <h3> Error de Conexión</h3>
            <p>" . $e->getMessage() . "</p>
         </div>");
}
?>