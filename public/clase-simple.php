<?php
session_start();
require_once '../src/Model/ClaseModel.php';
require_once '../src/View/ClaseViewSimple.php';
require_once '../src/Conexion/Conexion.php';

try {
    $db = Conexion::getInstance();
    $claseModel = new ClaseModel($db);
    $claseView = new ClaseViewSimple($claseModel);
    
    $grupo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if (!$grupo_id) {
        header('Location: grupo.php?error=ID de grupo inválido');
        exit;
    }
    // Procesar QR si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesarQR'])) {
        $qr_codigo = trim($_POST['qr_codigo']);
        $usuario = $_SESSION['usuario_logueado'] ?? null;
        
        if (!$usuario) {
            $claseView->showErrorMessage('Usuario no autenticado');
        } elseif ($usuario['rol'] !== 'estudiante') {
            $claseView->showErrorMessage('Solo los estudiantes pueden registrar asistencia');
        } elseif (empty($qr_codigo)) {
            $claseView->showErrorMessage('Código QR requerido');
        } else {
            $resultado = $claseModel->registrarAsistenciaConQR($qr_codigo, $usuario['id']);
            if ($resultado['success']) {
                $claseView->showSuccessMessage($resultado['mensaje']);
            } else {
                $claseView->showErrorMessage($resultado['mensaje']);
            }
        }
    }
    $claseView->render($grupo_id);
    
} catch (Exception $e) {
    error_log("Error en clase-simple.php: " . $e->getMessage());
    echo "<h3>Error interno del servidor</h3>";
    echo "<p>Se ha producido un error. Por favor, contacta al administrador.</p>";
    echo "<a href='grupo.php'>Volver a grupos</a>";
}
?>
