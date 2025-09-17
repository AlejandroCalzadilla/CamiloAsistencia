<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/../View/LoginView.php';
require_once __DIR__ . '/../View/UsuarioView.php';



class UsuarioController {
    private $model;
    private $view;
    public function __construct(UsuarioModel $model, View $view) {
        $this->model = $model;
        $this->view = $view;
        
    }

    // funcion para manejar la solicitud
    public function handleRequest() {
        // Procesar solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            $evento = $_POST['evento'];
            switch ($evento) {
                case 'login':
                    $this->procesarLogin();
                    break;
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        }
        
        // Mostrar la vista de login
        $this->view->render();
    }

    private function procesarLogin() {
        if (isset($_POST['nombre']) && isset($_POST['contrasena'])) {
            $nombre = trim($_POST['nombre']);
            $contrasena = trim($_POST['contrasena']);
            $usuario = $this->model->validarLogin($nombre, $contrasena);
            if ($usuario) {
                header('Location: grupo.php?login=success');
                exit();
            } else {
                $this->view->showErrorMessage("Credenciales incorrectas");
            }
        } else {
            $this->view->showErrorMessage("Por favor complete todos los campos");
        }
    }

   
   
}
