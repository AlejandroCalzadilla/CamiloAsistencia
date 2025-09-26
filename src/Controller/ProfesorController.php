<?php
class ProfesorController
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new ProfesorModel();
        $this->view = new ProfesorView();
    }

    public function handleRequest()
    {
        $message = '';
        $messageType = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            switch ($_POST['evento']) {
                case 'crear':
                    $this->crear();
                    break;
                case 'editar':
                    $this->editar();
                    break;
                case 'eliminar':
                    $this->eliminar();
                    break;
                case 'volver_grupos':
                    $this->volverGrupos();
                    return; 
                default:
                    $message = 'Evento no soportado';
                    $messageType = 'error';
                    break;
                    
            }
        } else {
            $this->view->actualizar();
        }
    }

    public function crear()
    {
        if (!empty($_POST['codigo']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['genero']) && !empty($_POST['usuario_id'])) {
            $resultado = $this->model->crear($_POST['codigo'], $_POST['nombres'], $_POST['apellidos'], $_POST['genero'], $_POST['usuario_id']);
            if ($resultado) {
                $this->view->showSuccessMessage('Profesor creado correctamente');
            } else {
                $this->view->showErrorMessage('Error al crear profesor');
            }

        } else {
            $this->view->showErrorMessage('Por favor completa todos los campos');

        }
        return $this->view->actualizar();
    }

    public function editar()
    {
        if (!empty($_POST['codigo']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['genero']) && !empty($_POST['usuario_id'])) {
            $resultado = $this->model->editar($_POST['codigo'], $_POST['nombres'], $_POST['apellidos'], $_POST['genero'], $_POST['usuario_id']);
            if ($resultado) {
                $this->view->showSuccessMessage('Profesor editado correctamente');
            } else {
                $this->view->showErrorMessage('Error al editar profesor');
            }

        } else {
            $this->view->showErrorMessage('Por favor completa todos los campos');
        }
        return $this->view->actualizar();


    }

    public function eliminar()
    {
        if (!empty($_POST['codigo'])) {
            $resultado = $this->model->eliminar($_POST['codigo']);
            if ($resultado) {
                $this->view->showSuccessMessage('Profesor eliminado correctamente');
            } else {
                $this->view->showErrorMessage('Error al eliminar profesor');
            }
        }else{
            $this->view->showErrorMessage('Por favor completa todos los campos');
        }
        return $this->view->actualizar();
    }

      public function volverGrupos()
    {
        header('Location: grupo.php');
        exit();
    }

}