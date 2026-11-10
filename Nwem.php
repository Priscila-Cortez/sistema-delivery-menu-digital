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
    
    // ===================================================================
    // DETECCIÓN INTELIGENTE Y ROBUSTA DEL ÁREA / GRUPO DEL USUARIO
    // ===================================================================
    $ca_grupo_iId = 1; // Valor por defecto general de respaldo

    if (isset($conexion)) {
        // PASO 1: Intentar buscar el área mediante la relación estándar de tablas
        $sqlGrupo = "SELECT g.ca_grupo_iId 
                     FROM contrato_areas_grupo g 
                     JOIN contrato_areas a ON g.conarea_iId = a.conarea_iId 
                     JOIN usuario u ON u.usutip_iId = a.con_iId 
                     WHERE u.usu_iId = ? LIMIT 1";
                     
        $stmtG = mysqli_prepare($conexion, $sqlGrupo);
        if ($stmtG) {
            mysqli_stmt_bind_param($stmtG, "i", $idUser);
            mysqli_stmt_execute($stmtG);
            $resG = mysqli_stmt_get_result($stmtG);
            if ($rowG = mysqli_fetch_assoc($resG)) {
                $ca_grupo_iId = $rowG['ca_grupo_iId'];
            }
        }

        // PASO 2: RESPALDO INTELIGENTE POR ID O NOMBRE DE USUARIO
        // Si la tabla no lo relacionó y se quedó en 1, forzamos su área correcta según corresponda
        if ($ca_grupo_iId == 1 || $idUser == 47) { 
            $sqlUserCheck = "SELECT usu_vcUser FROM usuario WHERE usu_iId = ? LIMIT 1";
            $stmtU = mysqli_prepare($conexion, $sqlUserCheck);
            if ($stmtU) {
                mysqli_stmt_bind_param($stmtU, "i", $idUser);
                mysqli_stmt_execute($stmtU);
                $resU = mysqli_stmt_get_result($stmtU);
                if ($rowU = mysqli_fetch_assoc($resU)) {
                    $username = strtoupper(trim($rowU['usu_vcUser']));

                    // Asignación directa según el usuario
                    if ($username === 'RAVILA' || $idUser == 47) {
                        $ca_grupo_iId = 10; // 10 = SISTEMAS
                    }
                    // Puedes agregar más "elseif" aquí para otros jefes si lo necesitas
                }
            }
        }
    }
    // ===================================================================

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





<?php
session_start();
include "../conexion.php"; // Ajusta la ruta de tu conexión según corresponda

if (!isset($conexion) && isset($conn)) {
    $conexion = $conn;
}

date_default_timezone_set('America/Lima');

// Validar que el trabajador haya iniciado sesión
if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit();
}

$idUser = $_SESSION['idUser'];

// ===================================================================
// DETECCIÓN INTELIGENTE Y ROBUSTA DEL ÁREA / GRUPO DEL USUARIO
// ===================================================================
$ca_grupo_iId = 1; 
$nombreArea = "Área General";

if (isset($conexion)) {
    // PASO 1: Intentar buscar por la relación estándar de tablas
    $sqlAreaUser = "SELECT g.ca_grupo_iId, g.ca_grupo_vcNombre 
                    FROM contrato_areas_grupo g 
                    JOIN contrato_areas a ON g.conarea_iId = a.conarea_iId 
                    JOIN usuario u ON u.usutip_iId = a.con_iId 
                    WHERE u.usu_iId = ? 
                    LIMIT 1";
                    
    $stmtA = mysqli_prepare($conexion, $sqlAreaUser);
    if ($stmtA) {
        mysqli_stmt_bind_param($stmtA, "i", $idUser);
        mysqli_stmt_execute($stmtA);
        $resA = mysqli_stmt_get_result($stmtA);
        if ($rowA = mysqli_fetch_assoc($resA)) {
            $ca_grupo_iId = $rowA['ca_grupo_iId'];
            $nombreArea = $rowA['ca_grupo_vcNombre'];
        }
    }

    // PASO 2: RESPALDO INTELIGENTE POR ID O NOMBRE DE USUARIO
    if ($ca_grupo_iId == 1 || $idUser == 47) {
        $sqlUserCheck = "SELECT usu_vcUser FROM usuario WHERE usu_iId = ? LIMIT 1";
        $stmtU = mysqli_prepare($conexion, $sqlUserCheck);
        if ($stmtU) {
            mysqli_stmt_bind_param($stmtU, "i", $idUser);
            mysqli_stmt_execute($stmtU);
            $resU = mysqli_stmt_get_result($stmtU);
            if ($rowU = mysqli_fetch_assoc($resU)) {
                $username = strtoupper(trim($rowU['usu_vcUser']));

                if ($username === 'RAVILA' || $idUser == 47) {
                    $ca_grupo_iId = 10; // 10 = SISTEMAS
                    $nombreArea = "SISTEMAS";
                }
            }
        }
    }
}
// ===================================================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos de mi Área</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body style="background-color: #f8f9fa;">

<div class="container-fluid px-4 py-4" style="background-color: #eaebec; min-height: 90vh;">

    <!-- Cabecera limpia con la línea corporativa -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-1 bg-white border-start border-primary border-4" style="border-left-color: #8527c4 !important;">
        <div class="card-body">
            <h1 class="fw-bold mb-2" style="color: #8527c4;">Documentos del Área</h1>
            <p class="text-muted mb-0">Área asignada: <span class="fw-semibold text-dark"><?php echo htmlspecialchars($nombreArea); ?></span></p>
        </div>
    </div>

    <!-- Tabla de Archivos -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha de Subida</th>
                            <th>Nombre del Archivo</th>
                            <th>Descripción / Comentarios</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($conexion)) {
                            // Filtrar estrictamente por el grupo/área correspondiente del usuario
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
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_subida'])); ?></td>
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
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>



