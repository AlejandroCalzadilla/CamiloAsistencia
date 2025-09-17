<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/../View/LoginView.php';
require_once __DIR__ . '/../View/UsuarioView.php';



class UsuarioController
{
    private $model;
    private $view;
    public function __construct(UsuarioModel $model, View $view)
    {
        $this->model = $model;
        $this->view = $view;

    }

    // funcion para manejar la solicitud
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            switch ($_POST['evento']) {
                case 'crear':
                    if (!empty($_POST['nombre']) && !empty($_POST['contrasena'])) {
                        $this->model->crear($_POST['nombre'], $_POST['contrasena']);  
                    }
                    break;
                case 'editar':
                    if (!empty($_POST['id']) && !empty($_POST['nombre'])) {
                        $contrasena = !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;
                        $this->model->editar($_POST['id'], $_POST['nombre'], $contrasena);
                        //$this->view->showMessage('Usuario editado');
                    }
                    break;
                case 'eliminar':
                    if (!empty($_POST['id'])) {
                        $this->model->eliminar($_POST['id']);
                       // $this->view->showMessage('Usuario eliminado');
                    }
                    break;
                case 'login':
                    $this->procesarLogin();
                    break;
                default:
                   // $this->view->showMessage("Evento no soportado");
                    break;
            }
        }
        $this->view->render();
    }

    private function procesarLogin()
    {
        if (isset($_POST['nombre']) && isset($_POST['contrasena'])) {
            $nombre = trim($_POST['nombre']);
            $contrasena = trim($_POST['contrasena']);
            $usuario = $this->model->validarLogin($nombre, $contrasena);
            if ($usuario) {
                header('Location: grupo.php?login=success');
                exit();
            } else {
                //$this->view->showErrorMessage("Credenciales incorrectas");
            }
        } else {
            //$this->view->showErrorMessage("Por favor complete todos los campos");
        }
    }


    public function obtenerTodos()
    {
        $this->model->crear($_POST['nombre'], $_POST['contrasena']);
        //$this->view->showMessage('Usuario creado');

    }


    public function editar()
    {
        $this->model->editar($_POST['id'], $_POST['nombre'], $_POST['contrasena']);
        //$this->view->showMessage('Usuario editado');

    }

    public function eliminar()
    {
        $this->model->eliminar($_POST['id']);
        //$this->view->showMessage('Usuario eliminado');

    }


}
