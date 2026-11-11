guardar
<?php
session_start();
include "../../conexion.php";

$conexion = isset($conn) ? $conn : (isset($conexion) ? $conexion : null);

date_default_timezone_set('America/Lima');

if (!isset($_SESSION['idUser'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $idUser = $_SESSION['idUser'];
    $descripcion = trim($_POST['description']);
    
    // 1. DETECCIÓN AUTOMÁTICA DEL ÁREA CON RESPALDO INTELIGENTE
    // Intentamos leerla de la sesión; si no está o viene por defecto, la consultamos al vuelo en la BD
    $ca_grupo_iId = isset($_SESSION['ca_grupo_iId']) ? intval($_SESSION['ca_grupo_iId']) : 1;

    if ($conexion) {
        // Consulta de respaldo directa para cualquier usuario logueado
        $sqlAreaCheck = "SELECT g.ca_grupo_iId 
                        FROM contrato_areas_grupo g 
                        JOIN contrato_areas a ON g.conarea_iId = a.conarea_iId 
                        JOIN usuario u ON u.usutip_iId = a.con_iId 
                        WHERE u.usu_iId = ? 
                        LIMIT 1";
        $stmtArea = mysqli_prepare($conexion, $sqlAreaCheck);
        if ($stmtArea) {
            mysqli_stmt_bind_param($stmtArea, "i", $idUser);
            mysqli_stmt_execute($stmtArea);
            $resArea = mysqli_stmt_get_result($stmtArea);
            if ($rowArea = mysqli_fetch_assoc($resArea)) {
                if (!empty($rowArea['ca_grupo_iId'])) {
                    $ca_grupo_iId = intval($rowArea['ca_grupo_iId']);
                }
            }
            mysqli_stmt_close($stmtArea);
        }

        // Auto-crear la tabla 'archivos' si no existe
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
                    
                    if ($conexion) {
                        // Se realiza la inserción asociando el archivo al área detectada automáticamente
                        $sql = "INSERT INTO archivos (id_usuario, nombre_original, ruta, descripcion, ca_grupo_iId, fecha_subida) VALUES (?, ?, ?, ?, ?, NOW())";
                        $stmt = mysqli_prepare($conexion, $sql);
                        
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "isssi", $idUser, $nombreArchivo, $rutaParaBD, $descripcion, $ca_grupo_iId);
                            if (mysqli_stmt_execute($stmt)) {
                                mysqli_stmt_close($stmt);
                                header("Location: ../documentacionSubirArchivos.php?success=1");
                                exit();
                            }
                            mysqli_stmt_close($stmt);
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




sibir
<?php
session_start();
include "../conexion.php"; 
include "includes/headerSub.php"; 

$conexion = isset($conn) ? $conn : (isset($conexion) ? $conexion : null);

date_default_timezone_set('America/Lima');

if (!isset($_SESSION['idUser'])) {
    header("Location: index.php");
    exit();
}

$idUser = $_SESSION['idUser'];

// Detección automática del área con respaldo inteligente
$ca_grupo_iId = isset($_SESSION['ca_grupo_iId']) ? intval($_SESSION['ca_grupo_iId']) : 1; 
$nombreArea = isset($_SESSION['nombreArea']) ? $_SESSION['nombreArea'] : "Área General";

if ($conexion && ($ca_grupo_iId <= 1 || empty($nombreArea))) {
    $sqlAreaCheck = "SELECT g.ca_grupo_iId, g.ca_grupo_vcNombre 
                    FROM contrato_areas_grupo g 
                    JOIN contrato_areas a ON g.conarea_iId = a.conarea_iId 
                    JOIN usuario u ON u.usutip_iId = a.con_iId 
                    WHERE u.usu_iId = ? 
                    LIMIT 1";
    $stmtArea = mysqli_prepare($conexion, $sqlAreaCheck);
    if ($stmtArea) {
        mysqli_stmt_bind_param($stmtArea, "i", $idUser);
        mysqli_stmt_execute($stmtArea);
        $resArea = mysqli_stmt_get_result($stmtArea);
        if ($rowArea = mysqli_fetch_assoc($resArea)) {
            $ca_grupo_iId = intval($rowArea['ca_grupo_iId']);
            $nombreArea = $rowArea['ca_grupo_vcNombre'];
            
            // Actualizamos la sesión para mantenerla sincronizada
            $_SESSION['ca_grupo_iId'] = $ca_grupo_iId;
            $_SESSION['nombreArea'] = $nombreArea;
        }
        mysqli_stmt_close($stmtArea);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Documentos</title>
</head>
<body style="background-color: #eaebec;">

<div class="container-fluid px-4 py-4" style="min-height: 90vh;">

    <div class="card border-0 shadow-sm rounded-4 mb-4 p-1 bg-white border-start border-primary border-4" style="border-left-color: #8527c4 !important;">
        <div class="card-body">
            <h1 class="fw-bold mb-2" style="color: #8527c4;">Subir Nuevo Documento</h1>
            <p class="text-muted mb-0">El archivo se asignará automáticamente a tu área: <span class="fw-semibold text-dark"><?php echo htmlspecialchars($nombreArea); ?></span></p>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-light"></i> Archivo subido y asignado correctamente a tu área.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 text-light"></i> Hubo un error al subir el archivo. Por favor, verifica el formato o tamaño e inténtalo de nuevo.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-14">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="card-body">
                    <form action="procedimientos/subirArchivo_guardar.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3 alert alert-light border rounded-pill px-3 py-2 text-secondary">
                            <i class="bi bi-building me-2"></i> Área de destino detectada: <strong class="text-dark"><?php echo htmlspecialchars($nombreArea); ?></strong>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold text-dark">Selecciona un Archivo:</label>
                            <input type="file" name="file" id="file" class="form-control rounded-pill px-3 py-2" required>
                            <div class="form-text text-muted small mt-1">Tamaño máximo permitido: 15 MB. Formatos permitidos: PDF, Word, Excel, TXT, Imágenes, ZIP.</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-dark">Descripción o Comentarios:</label>
                            <textarea name="description" id="description" class="form-control rounded-4 p-3" rows="3" placeholder="Añade detalles sobre el contenido del documento..."></textarea>
                        </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once "includes/footer.php"; ?>

</body>
</html>




archivos por area
<?php
session_start();
include "../conexion.php"; 
include "includes/headerSub.php"; 

$conexion = isset($conn) ? $conn : (isset($conexion) ? $conexion : null);

date_default_timezone_set('America/Lima');

if (!isset($_SESSION['idUser'])) {
    header("Location: ../index.php");
    exit();
}

$idUser = $_SESSION['idUser'];

// Detección automática del área con respaldo inteligente
$ca_grupo_iId = isset($_SESSION['ca_grupo_iId']) ? intval($_SESSION['ca_grupo_iId']) : 1; 
$nombreArea = isset($_SESSION['nombreArea']) ? $_SESSION['nombreArea'] : "Área General";

if ($conexion && ($ca_grupo_iId <= 1 || empty($nombreArea))) {
    $sqlAreaCheck = "SELECT g.ca_grupo_iId, g.ca_grupo_vcNombre 
                    FROM contrato_areas_grupo g 
                    JOIN contrato_areas a ON g.conarea_iId = a.conarea_iId 
                    JOIN usuario u ON u.usutip_iId = a.con_iId 
                    WHERE u.usu_iId = ? 
                    LIMIT 1";
    $stmtArea = mysqli_prepare($conexion, $sqlAreaCheck);
    if ($stmtArea) {
        mysqli_stmt_bind_param($stmtArea, "i", $idUser);
        mysqli_stmt_execute($stmtArea);
        $resArea = mysqli_stmt_get_result($stmtArea);
        if ($rowArea = mysqli_fetch_assoc($resArea)) {
            $ca_grupo_iId = intval($rowArea['ca_grupo_iId']);
            $nombreArea = $rowArea['ca_grupo_vcNombre'];
            
            // Actualizamos la sesión
            $_SESSION['ca_grupo_iId'] = $ca_grupo_iId;
            $_SESSION['nombreArea'] = $nombreArea;
        }
        mysqli_stmt_close($stmtArea);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos de mi Área</title>
</head>
<body style="background-color: #eaebec;">

<div class="container-fluid px-4 py-4" style="min-height: 90vh;">

    <div class="card border-0 shadow-sm rounded-4 mb-4 p-1 bg-white border-start border-primary border-4" style="border-left-color: #8527c4 !important;">
        <div class="card-body">
            <h2 class="fw-bold mb-1" style="color: #8527c4;">Documentos del Área</h2>
            <p class="text-muted mb-0">Área asignada: <span class="fw-semibold text-dark"><?php echo htmlspecialchars($nombreArea); ?></span></p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center py-3 fw-bold" style="background-color: #000000; color: white; height: 40px; border-radius: 7px 0 0 0px;">Fecha de Subida</th>
                            <th class="text-center py-3 fw-bold" style="background-color: #000000; color: white; height: 40px;">Nombre del Archivo</th>
                            <th class="text-center py-3 fw-bold" style="background-color: #000000; color: white; height: 40px;">Descripción / Comentarios</th>
                            <th class="text-center py-3 fw-bold" style="background-color: #000000; color: white; height: 40px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($conexion) {
                            $query = "SELECT * FROM archivos WHERE ca_grupo_iId = ? ORDER BY fecha_subida DESC";
                            $stmt = mysqli_prepare($conexion, $query);
                            
                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, "i", $ca_grupo_iId);
                                mysqli_stmt_execute($stmt);
                                $resultado = mysqli_stmt_get_result($stmt);

                                if ($resultado && mysqli_num_rows($resultado) > 0) {
                                    while ($row = mysqli_fetch_assoc($resultado)) {
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($row['fecha_subida'])); ?></td>
                                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['nombre_original']); ?></td>
                                            <td>
                                                <?php echo !empty($row['descripcion']) ? htmlspecialchars($row['descripcion']) : '<span class="text-muted fst-italic">Sin descripción</span>'; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="../<?php echo $row['ruta']; ?>" class="btn btn-sm btn-primary rounded-pill px-3" download style="background-color: #8527c4; border-color: #8527c4;">
                                                    <i class="bi bi-download"></i> Descargar
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center text-muted py-4">No hay documentos disponibles para tu área en este momento.</td></tr>';
                                }
                                mysqli_stmt_close($stmt);
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include_once "includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>



después de iiduser
$sqlAreaCheck = "SELECT g.ca_grupo_iId, g.ca_grupo_vcNombre 
                FROM contrato_areas_grupo g 
                JOIN contrato_areas a ON g.conarea_iId = a.conarea_iId 
                JOIN usuario u ON u.usutip_iId = a.con_iId 
                WHERE u.usu_iId = ? 
                LIMIT 1";
