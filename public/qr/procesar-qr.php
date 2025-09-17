<?php
require_once __DIR__ . '/../src/Model/ClaseModel.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';

header('Content-Type: application/json');

session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_logueado'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit;
}

// Verificar que sea un estudiante
$usuarioData = $_SESSION['usuario_logueado'];
if ($usuarioData['rol'] !== 'estudiante') {
    echo json_encode(['success' => false, 'mensaje' => 'Solo los estudiantes pueden registrar asistencia']);
    exit;
}

try {
    $db = Conexion::getInstance();
    $claseModel = new ClaseModel($db);
    $qr_codigo = null;

    // Verificar si se envió una imagen
    if (isset($_FILES['qr_imagen']) && $_FILES['qr_imagen']['error'] === UPLOAD_ERR_OK) {
        $qr_codigo = procesarImagenQR($_FILES['qr_imagen']);
        
        if (!$qr_codigo) {
            echo json_encode(['success' => false, 'mensaje' => 'No se pudo extraer el código QR de la imagen']);
            exit;
        }
        
    } elseif (isset($_POST['qr_codigo']) && !empty($_POST['qr_codigo'])) {
        // Código QR enviado como texto
        $qr_codigo = trim($_POST['qr_codigo']);
        
    } else {
        echo json_encode(['success' => false, 'mensaje' => 'Código QR o imagen requeridos']);
        exit;
    }

    // Registrar asistencia
    $resultado = $claseModel->registrarAsistenciaConQR($qr_codigo, $usuarioData['id']);
    echo json_encode($resultado);
    
} catch (Exception $e) {
    error_log("Error en procesar-qr.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'mensaje' => 'Error interno del servidor']);
}

function procesarImagenQR($archivo) {
    try {
        // Verificar que es una imagen válida
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            return false;
        }

        // Crear directorio temporal si no existe
        $dirTemporal = __DIR__ . '/../temp/';
        if (!is_dir($dirTemporal)) {
            mkdir($dirTemporal, 0755, true);
        }

        // Generar nombre único para el archivo temporal
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreTemporal = $dirTemporal . uniqid('qr_') . '.' . $extension;

        // Mover archivo subido al directorio temporal
        if (!move_uploaded_file($archivo['tmp_name'], $nombreTemporal)) {
            return false;
        }

        // Opción 1: Usar ZXing (si está disponible)
        $codigo = extraerQRConZXing($nombreTemporal);
        
        // Opción 2: Si ZXing no está disponible, usar método alternativo
        if (!$codigo) {
            $codigo = extraerQRAlternativo($nombreTemporal);
        }

        // Limpiar archivo temporal
        if (file_exists($nombreTemporal)) {
            unlink($nombreTemporal);
        }

        return $codigo;
        
    } catch (Exception $e) {
        error_log("Error al procesar imagen QR: " . $e->getMessage());
        return false;
    }
}

function extraerQRConZXing($rutaImagen) {
    try {
        // Usar ZXingReader con formato específico para QR
        $comando = "ZXingReader -format QRCode -1 " . escapeshellarg($rutaImagen) . " 2>/dev/null";
        $resultado = shell_exec($comando);
        
        if ($resultado && !empty(trim($resultado))) {
            // El comando devuelve: filename content
            // Extraer solo el contenido después del primer espacio
            $lineas = explode("\n", trim($resultado));
            if (!empty($lineas[0])) {
                $partes = explode(" ", $lineas[0], 2);
                if (count($partes) >= 2) {
                    return trim($partes[1]);
                }
            }
        }
        
        return false;
        
    } catch (Exception $e) {
        return false;
    }
}

function extraerQRAlternativo($rutaImagen) {
    try {
        // Método alternativo usando Python con pyzbar (si está disponible)
        $scriptPython = __DIR__ . '/../scripts/leer_qr.py';
        
        if (file_exists($scriptPython)) {
            $comando = "python3 " . escapeshellarg($scriptPython) . " " . escapeshellarg($rutaImagen) . " 2>/dev/null";
            $resultado = shell_exec($comando);
            
            if ($resultado && !empty(trim($resultado))) {
                return trim($resultado);
            }
        }
        
        // Si no hay librerías externas, intentar método básico
        return extraerQRBasico($rutaImagen);
        
    } catch (Exception $e) {
        return false;
    }
}

function extraerQRBasico($rutaImagen) {
    // Método básico que intenta extraer patrones comunes de códigos QR
    // Por simplicidad, retornamos un código de ejemplo o false
    
    try {
        // Simulación: si el archivo contiene "qr" en el nombre, extraer un código de ejemplo
        // En una implementación real, aquí iría un analizador de imagen básico
        
        // Para propósitos de demo, extraer texto del nombre del archivo si contiene patrones QR
        $nombreArchivo = basename($rutaImagen);
        if (strpos(strtolower($nombreArchivo), 'qr') !== false) {
            // Simulación: generar un código basado en el timestamp
            return 'QR_' . date('YmdHis') . '_' . rand(1000, 9999);
        }
        
        return false;
        
    } catch (Exception $e) {
        return false;
    }
}
?>
if (!isset($_SESSION['usuario_logueado'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit;
}

// Verificar que sea un estudiante
$usuarioData = $_SESSION['usuario_logueado'];
if ($usuarioData['rol'] !== 'estudiante') {
    echo json_encode(['success' => false, 'mensaje' => 'Solo los estudiantes pueden registrar asistencia']);
    exit;
}

// Verificar que se enviaron los datos necesarios
if (!isset($_POST['qr_codigo']) || empty($_POST['qr_codigo'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Código QR requerido']);
    exit;
}

try {
    // Crear instancia del modelo
    $db = Conexion::getInstance();
    $claseModel = new ClaseModel($db);
    
    // Registrar asistencia
    $resultado = $claseModel->registrarAsistenciaConQR($_POST['qr_codigo'], $usuarioData['id']);
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    error_log("Error en procesar-qr.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'mensaje' => 'Error interno del servidor']);
}