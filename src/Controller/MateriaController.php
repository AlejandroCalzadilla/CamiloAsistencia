<?php
class MateriaController {
    private $model;
    private $view;

    public function __construct(MateriaModel $model, MateriaView $view) {
        $this->model = $model;
        $this->view = $view;
    }

    public function handleRequest() {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            switch ($_POST['evento']) {
                case 'crear':
                    if (!empty($_POST['nombre'])) {
                        $this->model->crear($_POST['nombre']);
                        $message = 'Materia creada';
                    }
                    break;
                case 'editar':
                    if (!empty($_POST['nombre']) && !empty($_POST['id'])) {
                        $this->model->editar($_POST['nombre'], $_POST['id']);
                        $message = 'Materia editada';
                    }
                    break;
                case 'eliminar':
                    if (!empty($_POST['id'])) {
                        $this->model->eliminar($_POST['id']);
                        $message = 'Materia eliminada';
                    }
                    break;
            }
        }
        $materias = $this->model->obtener();
        $this->view->render($materias, $message);
    }
}