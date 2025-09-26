<?php
class MateriaController
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new MateriaModel();
        $this->view = new MateriaView();
    }

    public function handleRequest()
    {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            switch ($_POST['evento']) {
                case 'crear':
                    $this->crearMateria($_POST['nombre']);
                    break;
                case 'editar':
                    if (!empty($_POST['nombre']) && !empty($_POST['id'])) {
                        $this->editarMateria($_POST['id'], $_POST['nombre']);
                    }
                    break;
                case 'eliminar':
                    if (!empty($_POST['id'])) {
                        $this->eliminarMateria($_POST['id']);
                    }
                    break;
                case 'volver_grupos':
                    $this->volverGrupos();
                    return; 
            }
        } else {
            $this->view->actualizar();
        }
    }



    public function crearMateria($nombre)
    {
        if (!empty($nombre)) {
            $this->model->crear($nombre);
        }
        $this->view->showMessage('Materia creada');
        return $this->view->actualizar();
    }


    public function editarMateria($id, $nombre)
    {
        if (!empty($id) && !empty($nombre)) {
            $this->model->editar($nombre, $id);
        }
        $this->view->showMessage('Materia editada');
        return $this->view->actualizar();
    }


    public function eliminarMateria($id)
    {
        $resultado = $this->model->eliminar($id);
        if (!$resultado['success']) {
            $this->view->showMessage($resultado['mensaje']);
            return $this->view->actualizar();
        } else {
            $this->view->showMessage('Materia eliminada');
            return $this->view->actualizar();
        }
    }


    public function volverGrupos()
    {
        header('Location: grupo.php');
        exit();
    }


}