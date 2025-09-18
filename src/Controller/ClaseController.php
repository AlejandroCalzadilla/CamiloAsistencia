<?php
require_once __DIR__ . '/../Model/ClaseModel.php';
require_once __DIR__ . '/../View/ClaseView.php';
class ClaseController
{
    private $model;
    private $view;
    private $asistenciaModel;

    public function __construct(ClaseModel $model, AsistenciaModel $asistenciaModel, ClaseView $view)
    {
        $this->model = $model;
        $this->asistenciaModel = $asistenciaModel;
        $this->view = $view;
    }

    public function handleRequest($grupo_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            $evento = $_POST['evento'];
            switch ($evento) {
                case 'crear_clase':
                    $this->crearClase($grupo_id);
                    exit();
                case 'registrarAsistencia':
                    $this->registrarAsistencia();
                    exit();
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        }

        $this->view->render($grupo_id);
    }

    public function crearClase($grupo_id)
    {
        header('Content-Type: application/json');
        date_default_timezone_set('America/La_Paz'); // Para Bolivia
        session_start();
        $usuarioData = $_SESSION['usuario_logueado'];
        if (!$usuarioData || $usuarioData['rol'] !== 'profesor') {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Solo los profesores pueden crear clases'
            ]);
            return;
        }
        $dia = $_POST['dia'] ?? date('Y-m-d');
        $hora_inicio = $_POST['hora_inicio'] ?? date('H:i:s');
        $hora_fin = $_POST['hora_fin'] ?? date('H:i:s');
        $resultado = $this->model->crearClase($dia, $grupo_id);
        if ($resultado['success']) {
            $clase_id = $resultado['clase_id'];
            $this->asistenciaModel->crearAsistenciasParaClase($clase_id, $grupo_id, $hora_inicio, $hora_fin);
        }
        echo json_encode($resultado);
    }


    public function registrarAsistencia()
    {
        header('Content-Type: application/json');
        session_start();
        $usuarioData = $_SESSION['usuario_logueado'];
        if (!$usuarioData || $usuarioData['rol'] !== 'estudiante') {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Solo los estudiantes pueden registrar asistencia'
            ]);
            return;
        }
        $usuario_id = $usuarioData['id'] ?? null;
        $clase_id = $_POST['clase_id'] ?? null;
        $codigo_verificacion = $_POST['qr_codigo'] ?? null;
        if (!$usuario_id || !$clase_id || !$codigo_verificacion) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Faltan datos para registrar la asistencia'
            ]);
            return;
        }
        $resultado = $this->asistenciaModel->marcarPresente($usuario_id, $clase_id, $codigo_verificacion);
        echo json_encode($resultado);
    }
}


// Debug: Verificar variables extraídas
//error_log("DEBUG: usuario_id: " . ($usuario_id ?? 'NULL'));
//error_log("DEBUG: clase_id: " . ($clase_id ?? 'NULL'));
//error_log("DEBUG: codigo_verificacion: " . ($codigo_verificacion ?? 'NULL'));