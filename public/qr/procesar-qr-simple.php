<?php
session_start();
require_once '../src/Model/ClaseModel.php';
require_once '../src/Conexion/Conexion.php';

header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_logueado'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Verificar que sea un estudiante
$usuarioData = $_SESSION['usuario_logueado'];
if ($usuarioData['rol'] !== 'estudiante') {
    echo json_encode(['success' => false, 'message' => 'Solo los estudiantes pueden registrar asistencia']);
    exit;
}

try {
    $db = Conexion::getInstance();
    $claseModel = new ClaseModel($db);
    
    // Obtener datos del POST
    $clase_id = isset($_POST['clase_id']) ? intval($_POST['clase_id']) : 0;
    $qr_texto = isset($_POST['qr_texto']) ? trim($_POST['qr_texto']) : '';
    
    if (!$clase_id || !$qr_texto) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
        exit;
    }
    
    // Registrar asistencia
    $resultado = $claseModel->registrarAsistenciaConQR($qr_texto, $usuarioData['id']);
    
    if ($resultado['success']) {
        echo json_encode(['success' => true, 'message' => 'Asistencia registrada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => $resultado['mensaje']]);
    }
    
} catch (Exception $e) {
    error_log("Error en procesar-qr.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
