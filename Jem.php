<?php
session_start();
include "../conexion.php"; // Ajusta la ruta de tu conexión según corresponda

if (!isset($conexion) && isset($conn)) {
    $conexion = $conn;
}

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Documentos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body style="background-color: #f8f9fa;">

<div class="container-fluid px-4 py-4" style="background-color: #eaebec; min-height: 90vh;">

    <!-- Cabecera -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-1 bg-white border-start border-primary border-4" style="border-left-color: #8527c4 !important;">
        <div class="card-body">
            <h1 class="fw-bold mb-2" style="color: #8527c4;">Subir Nuevo Documento</h1>
            <p class="text-muted mb-0">Selecciona el área de destino, adjunta el archivo y añade una descripción.</p>
        </div>
    </div>

    <!-- Alertas de éxito o error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> ¡Archivo subido y asignado correctamente al área seleccionada!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Hubo un error al subir el archivo. Por favor, verifica el formato o tamaño e inténtalo de nuevo.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario de Subida -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="card-body">
                    <form action="procesos/subirArchivo_guardar.php" method="POST" enctype="multipart/form-data">
                        
                        <!-- SELECTOR MANUAL DE ÁREAS -->
                        <div class="mb-3">
                            <label for="ca_grupo_iId" class="form-label fw-semibold text-dark">Área / Departamento de Destino:</label>
                            <select name="ca_grupo_iId" id="ca_grupo_iId" class="form-select rounded-pill px-3 py-2" required>
                                <option value="" disabled selected>-- Seleccione el área correspondiente --</option>
                                <?php
                                if (isset($conexion)) {
                                    $sqlSelect = "SELECT ca_grupo_iId, ca_grupo_vcNombre FROM contrato_areas_grupo WHERE ca_grupo_iId > 0 ORDER BY ca_grupo_vcNombre ASC";
                                    $resSelect = mysqli_query($conexion, $sqlSelect);
                                    if ($resSelect && mysqli_num_rows($resSelect) > 0) {
                                        while($rowArea = mysqli_fetch_assoc($resSelect)) {
                                            echo '<option value="'.$rowArea['ca_grupo_iId'].'">'.htmlspecialchars($rowArea['ca_grupo_vcNombre']).'</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- ARCHIVO -->
                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold text-dark">Archivo (PDF, Word, Excel, etc.):</label>
                            <input type="file" name="file" id="file" class="form-control rounded-pill px-3 py-2" required>
                            <div class="form-text text-muted small mt-1">Tamaño máximo permitido: 15 MB.</div>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-dark">Descripción o Comentarios:</label>
                            <textarea name="description" id="description" class="form-control rounded-4 p-3" rows="3" placeholder="Añade detalles sobre el contenido del documento..."></textarea>
                        </div>

                        <!-- BOTÓN DE ENVÍO -->
                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary rounded-pill py-2 fw-semibold shadow-sm" style="background-color: #8527c4; border-color: #8527c4;">
                                <i class="bi bi-cloud-upload me-2"></i> Subir Documento
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>





<?php
session_start();
include "../../conexion.php";

if (!isset($conexion) && isset($conn)) {
    $conexion = $conn;
}

// Validar sesión del usuario
if (!isset($_SESSION['idUser'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $idUser = $_SESSION['idUser'];
    $descripcion = trim($_POST['description']);
    
    // Capturar el área seleccionada manualmente desde el <select> del formulario
    // Si por alguna razón no viene, se asigna 1 por seguridad
    $ca_grupo_iId = isset($_POST['ca_grupo_iId']) ? intval($_POST['ca_grupo_iId']) : 1;
    
    // Auto-crear la tabla 'archivos' si no existe
    if (isset($conexion)) {
        $sqlCrearTabla = "CREATE TABLE IF NOT EXISTS archivos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            nombre_original VARCHAR(255) NOT NULL,
            ruta VARCHAR(255) NOT NULL,
            descripcion TEXT DEFAULT NULL,
            ca_grupo_iId INT NOT NULL,
            fecha_subida DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        mysqli_query($conexion, $sqlCrearTabla);
    }

    // Procesar archivo físico
    $nombreArchivo = $_FILES['file']['name'];
    $archivoTmp = $_FILES['file']['tmp_name'];
    $tamanioArchivo = $_FILES['file']['size'];
    $errorArchivo = $_FILES['file']['error'];

    if ($errorArchivo === 0) {
        $carpetaDestino = "../../uploads/";
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreUnico = uniqid('file_', true) . '.' . $extension;
        $rutaFinal = $carpetaDestino . $nombreUnico;
        $rutaParaBD = "uploads/" . $nombreUnico; 

        $extensionesPermitidas = array('pdf', 'docx', 'xlsx', 'txt', 'png', 'jpg', 'jpeg', 'zip');

        if (in_array(strtolower($extension), $extensionesPermitidas)) {
            if ($tamanioArchivo < 15000000) { // 15 MB máximo
                if (move_uploaded_file($archivoTmp, $rutaFinal)) {
                    
                    if (isset($conexion)) {
                        $sql = "INSERT INTO archivos (id_usuario, nombre_original, ruta, descripcion, ca_grupo_iId, fecha_subida) VALUES (?, ?, ?, ?, ?, NOW())";
                        $stmt = mysqli_prepare($conexion, $sql);
                        
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "isssi", $idUser, $nombreArchivo, $rutaParaBD, $descripcion, $ca_grupo_iId);
                            if (mysqli_stmt_execute($stmt)) {
                                header("Location: ../documentacionSubirArchivos.php?success=1");
                                exit();
                            }
                        }
                    }
                    header("Location: ../documentacionSubirArchivos.php?error=db");
                    exit();
                } else {
                    header("Location: ../documentacionSubirArchivos.php?error=move");
                    exit();
                }
            } else {
                header("Location: ../documentacionSubirArchivos.php?error=size");
                exit();
            }
        } else {
            header("Location: ../documentacionSubirArchivos.php?error=extension");
            exit();
        }
    } else {
        header("Location: ../documentacionSubirArchivos.php?error=upload");
        exit();
    }
} else {
    header("Location: ../documentacionSubirArchivos.php");
    exit();
}
?>

