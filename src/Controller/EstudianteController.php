<?php
class EstudianteController
{
    public EstudianteModel $model;
    public EstudianteView $view;

    public function __construct()
    {
        $this->model = new EstudianteModel();
        $this->view = new EstudianteView();
    }

    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            switch ($_POST['evento']) {
                case 'crear':
                    $this->crearEstudiante();
                    break;
                case 'actualizar':
                    $this->editarEstudiante($_POST['codigo'] ?? '');
                    break;
                case 'eliminar':
                    $this->eliminarEstudiante($_POST['codigo'] ?? '');
                    break;
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        } else {
            $this->view->actualizar();
        }

    }

    public function crearEstudiante()
    {
        if (!empty($_POST['codigo']) && !empty($_POST['ci']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['estado']) && !empty($_POST['usuario_id'])) {
            // llamar al modelo para crear el estudiante
            $resultado = $this->model->crear(
                [
                    'codigo' => $_POST['codigo'],
                    'ci' => $_POST['ci'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'estado' => $_POST['estado'],
                    'genero' => $_POST['genero'] ?? '',
                    'usuario_id' => $_POST['usuario_id']
                ]
            );
            if ($resultado['success']) {
                $this->view->showSuccessMessage($resultado['mensaje']);

            } else {
                $this->view->showErrorMessage($resultado['mensaje']);
            }
        } else {
            $this->view->showErrorMessage("Datos incompletos para crear estudiante");
        }
        return $this->view->actualizar();
    }


    public function editarEstudiante($codigo)
    {
        if (!empty($codigo) && !empty($_POST['ci']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['estado']) && !empty($_POST['usuario_id'])) {
            $resultado = $this->model->actualizar(
                [
                    'codigo' => $codigo,
                    'ci' => $_POST['ci'],
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'estado' => $_POST['estado'],
                    'genero' => $_POST['genero'] ?? '',
                    'usuario_id' => $_POST['usuario_id']
                ]
            );
            if ($resultado['success']) {
                $this->view->showSuccessMessage($resultado['mensaje']);

            } else {
                $this->view->showErrorMessage($resultado['mensaje']);
            }
        } else {
            $this->view->showErrorMessage("Datos incompletos para actualizar estudiante");
        }
        return $this->view->actualizar();
    }


    public function eliminarEstudiante($codigo)
    {
        if (!empty($codigo)) {
            $respuesta = $this->model->eliminar($codigo);
            if (!$respuesta['success']) {
                $this->view->showErrorMessage($respuesta['mensaje']);
            } else {
                $this->view->showSuccessMessage($respuesta['mensaje']);
            }
        } else {
            $this->view->showErrorMessage("Código de estudiante requerido para eliminar");
        }
        return $this->view->actualizar();
    }
}