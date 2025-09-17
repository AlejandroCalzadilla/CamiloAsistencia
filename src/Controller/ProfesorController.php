<?php
class ProfesorController {
    private $model;
    private $view;

    public function __construct(ProfesorModel $model, ProfesorView $view){
        $this->model = $model;
        $this->view = $view;
    }

    public function handleRequest(){
        $message = '';
        $messageType = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            switch ($_POST['evento']) {
                case 'crear':
                    if (!empty($_POST['codigo']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['genero']) && !empty($_POST['usuario_id'])) {
                        $this->model->crear($_POST['codigo'], $_POST['nombres'], $_POST['apellidos'], $_POST['genero'], $_POST['usuario_id']);
                        $message = 'Profesor creado correctamente';
                        $messageType = 'success';
                    }
                    break;
                case 'editar':
                    if (!empty($_POST['codigo']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['genero']) && !empty($_POST['usuario_id'])) {
                        $this->model->editar($_POST['codigo'], $_POST['nombres'], $_POST['apellidos'], $_POST['genero'], $_POST['usuario_id']);
                        $message = 'Profesor editado correctamente';
                        $messageType = 'success';
                    }
                    break;
                case 'eliminar':
                    if (!empty($_POST['codigo'])) {
                        $this->model->eliminar($_POST['codigo']);
                        $message = 'Profesor eliminado correctamente';
                        $messageType = 'success';
                    }
                    break;
                default:
                    $message = 'Evento no soportado';
                    $messageType = 'error';
                    break;
            }
        }
        $profesores = $this->model->obtener();
        $this->view->render($profesores, $message, $messageType);
    }
}