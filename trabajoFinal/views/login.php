<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../controllers/UsuarioController.php";

$error = "";

if (isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $controladorUsuario = new UsuarioController();
    $datos = $controladorUsuario->login($usuario, $password);

    if ($datos) {
        $_SESSION['usuario'] = $datos['usuario']; 
        $_SESSION['nombre_completo'] = $datos['nombre_completo'];
        $_SESSION['rol'] = $datos['rol'];

        if (isset($_SESSION['rol_cliente'])) {
            unset($_SESSION['rol_cliente']);
        }

        header("Location: ../index.php?accion=listar");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - El Buen Sabor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #343a40; min-height: 100vh; display: flex; align-items: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card p-4 shadow border-0">
                <div class="text-center mb-4">
                    <h3 class="mt-2 fw-bold text-dark">Acceso Personal</h3>
                    <p class="text-muted small">Sistema El Buen Sabor</p>
                </div>

                <?php if(!empty($error)): ?>
                    <div class='alert alert-danger p-2 small text-center'><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-bold">Usuario</label>
                        <input type="text" name="usuario" class="form-control" required placeholder="Ej: admin_user">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-bold">Contraseña</label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                    <button class="btn btn-dark w-100 py-2 mb-3 fw-bold" name="login">Ingresar</button>
                    <div class="text-center">
                        <a href="../index.php?accion=bienvenida" class="text-decoration-none small text-muted">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>