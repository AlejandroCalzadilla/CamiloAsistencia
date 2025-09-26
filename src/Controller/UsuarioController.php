<?php
require_once __DIR__ . '/../Model/UsuarioModel.php';
require_once __DIR__ . '/../View/LoginView.php';
require_once __DIR__ . '/../View/UsuarioView.php';



class UsuarioController
{
    private $model;
    private $view;
    public function __construct(View $view)
    {
        $this->model = new UsuarioModel();
        $this->view = $view;

    }

    // funcion para manejar los eventos
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            switch ($_POST['evento']) {
                case 'crear':
                    $this->crear();
                    break;
                case 'editar':
                    if (!empty($_POST['id']) && !empty($_POST['nombre'])) {
                        $this->editar();
                    }
                    break;
                case 'eliminar':
                    $this->eliminar();
                    break;
                case 'login':
                    $this->procesarLogin();
                    break;
                default:
                    $this->view->showMessage("Evento no soportado");
                    $this->view->actualizar();
                    break;
                case 'volver_grupos':
                    $this->volverGrupos();
                    return;     
            }
        } else {
            $this->view->actualizar();
        }
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
                $this->view->showErrorMessage("Credenciales incorrectas");
            }
        }
        else{
            $this->view->showErrorMessage("Ingrese Crendenciales");
        }

        return $this->view->actualizar();
    }




    public function crear()
    {
        $this->model->crear($_POST['nombre'], $_POST['contrasena']);
        $this->view->showMessage('Usuario creado');
        return $this->view->actualizar();

    }

    public function editar()
    {
        $contrasena = !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;
        $this->model->editar($_POST['id'], $_POST['nombre'], $contrasena);
        $this->view->showMessage('Usuario editado');
        return $this->view->actualizar();
    }

    public function eliminar()
    {
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            $this->view->showErrorMessage('ID de usuario no proporcionado');
            $this->view->actualizar();
            return;
        }
        $resultado = $this->model->eliminar($_POST['id']);
        if ($resultado['success']) {
            $this->view->showMessage($resultado['mensaje']);
        } else {
            $this->view->showErrorMessage($resultado['mensaje']);
        }
        return $this->view->actualizar();
    }


      public function volverGrupos()
    {
        header('Location: grupo.php');
        exit();
    }


}
