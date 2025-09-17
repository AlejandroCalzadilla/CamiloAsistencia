<?php
require_once __DIR__ . '/../Model/Estudiante.php';
require_once __DIR__ . '/../View/EstudianteView.php';

class EstudianteController {
    private $model;
    private $view;

    public function __construct(Estudiante $model, EstudianteView $view) {
        $this->model = $model;
        $this->view = $view;
        // Inyectar el modelo en la vista
       
    }

    public function handleRequest() {
        // Procesar solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            $evento = $_POST['evento'];
            $data = [];
            
            switch ($evento) {
                case 'actualizar':
                    if (isset($_POST['nombres'])) {
                        $data['nombres'] = $_POST['nombres'];
                    }
                    if (isset($_POST['apellidos'])) {
                        $data['apellidos'] = $_POST['apellidos'];
                    }
                    if (isset($_POST['estado'])) {
                        $data['estado'] = $_POST['estado'];
                    }
                    
                    $this->model->actualizar($data);
                    $this->view->showSuccessMessage("Estudiante actualizado correctamente");
                    break;
                
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        }
        
        // Mostrar la vista
        $this->view->render();
    }

    public function getModel() {
        return $this->model;
    }

    public function getView() {
        return $this->view;
    }
}
