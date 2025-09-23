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
                    break;
                case 'registrar_asistencia':
                    $this->registrarAsistencia();
                    break;
                case 'eliminar_clase':
                    $this->eliminarClase();
                    break;    
                case 'mostrar_form_crear':
                    $this->mostrarFormularioCrear();
                    break;
                case 'cancelar_formulario':
                    $this->cancelarFormulario();
                    break;
                case 'ver_asistencias':
                    $this->verAsistencias();
                    break;
               
                case 'volver_grupos':
                    $this->volverGrupos();
                    return; // Redirect, no renderizar
                default:
                    $this->view->showErrorMessage("Evento no soportado: " . $evento);
                    break;
            }
        }

        // Siempre renderizar al final
        $this->view->render($grupo_id);
    }

    public function crearClase($grupo_id)
    {
        date_default_timezone_set('America/La_Paz');
        session_start();
        $usuarioData = $_SESSION['usuario_logueado'];
        $dia = $_POST['dia'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        // Validaciones
        if ($hora_fin <= $hora_inicio) {
            $this->view->showErrorMessage('La hora de fin debe ser posterior a la hora de inicio');
            return;
        }
        $resultado = $this->model->crearClase($dia, $grupo_id, $hora_inicio, $hora_fin); 
        if ($resultado['success']) {
            $clase_id = $resultado['clase_id'];
            // Crear asistencias para todos los estudiantes
            $resultadoAsistencias = $this->asistenciaModel->crearAsistenciasParaClase($clase_id, $grupo_id);
            if ($resultadoAsistencias) {
                $this->view->showSuccessMessage('Clase creada exitosamente. Código  ' . ($resultado['codigo'] ?? 'N/A'));
            } else {
                $this->view->showErrorMessage('Clase creada pero hubo problemas al crear las asistencias');
            }
        } else {
            $this->view->showErrorMessage($resultado['mensaje']);
        }
    }
    public function registrarAsistencia()
    {
        session_start();
        $usuarioData = $_SESSION['usuario_logueado'];

        $usuario_id = $usuarioData['id'];
        $clase_id = intval($_POST['clase_id']);
        $codigo_verificacion = $_POST['qr_codigo'];
        if (empty(trim($codigo_verificacion))) {
            $this->view->showErrorMessage('Por favor ingresa el código QR');
            return;
        }
        $resultado = $this->asistenciaModel->marcarPresente($usuario_id, $clase_id, $codigo_verificacion);
        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['mensaje']);
        } else {
            $this->view->showErrorMessage($resultado['mensaje']);
        }
    }

    public function mostrarFormularioCrear()
    {
        $this->view->setMostrarFormulario(true);
    }

    public function cancelarFormulario()
    {
        $this->view->setMostrarFormulario(false);
    }

    public function verAsistencias()
    {
        if (!isset($_POST['clase_id'])) {
            $this->view->showErrorMessage('ID de clase no especificado');
            return;
        }

        $clase_id = intval($_POST['clase_id']);
        $this->view->setMostrarAsistencias(true, $clase_id);
    }

    public function eliminarClase()
    {
        session_start();
        $usuarioData = $_SESSION['usuario_logueado'];
        
        if (!$usuarioData || $usuarioData['rol'] !== 'profesor') {
            $this->view->showErrorMessage('Solo los profesores pueden eliminar clases');
            return;
        }

        if (!isset($_POST['clase_id'])) {
            $this->view->showErrorMessage('ID de clase no especificado');
            return;
        }

        $clase_id = intval($_POST['clase_id']);
        $resultado = $this->model->eliminar($clase_id);
        
        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['mensaje']);
        } else {
            $this->view->showErrorMessage($resultado['mensaje']);
        }
    }

    public function volverGrupos()
    {
        header('Location: grupo.php');
        exit();
    }
}

