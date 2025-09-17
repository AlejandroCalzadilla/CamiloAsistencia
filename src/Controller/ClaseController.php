<?php
require_once __DIR__ . '/../Model/ClaseModel.php';
require_once __DIR__ . '/../View/ClaseView.php';
class ClaseController
{
    private $model;
    private $view;
    public function __construct(ClaseModel $model, ClaseView $view)
    {
        $this->model = $model;
        $this->view = $view;
    }
    // funcion para manejar la solicitud
    public function handleRequest($grupo_id)
    {
        // Procesar solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {

            $evento = $_POST['evento'];

            switch ($evento) {

                case 'ver_clases':
                    $this->verClases();
                    break;
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        }
        // Mostrar la vista de login
        $this->view->render($grupo_id);
    }


    public function verClases()
    {
        header('Location: clase.php');
    }

}