<?php
require_once __DIR__ . '/../Model/GrupoModel.php';
require_once __DIR__ . '/../View/GrupoView.php';


class GrupoController
{
    private $model;
    private $view;
    public function __construct(GrupoModel $model, GrupoView $view)
    {
        $this->model = $model;
        $this->view = $view;

    }

    // funcion para manejar la solicitud
    public function handleRequest()
    {
        // Procesar solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {

            $evento = $_POST['evento'];

            switch ($evento) {

                case 'ver_clases':
                    $this->verClases();
                    break;
                case 'Usuarios':
                     $this->Usuarios();    
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        }

        // Mostrar la vista de login
        $this->view->render();
    }


   public function verClases()
    {
        // Verificar que se recibió el grupo_id
        if (isset($_POST['grupo_id'])) {
            $grupo_id = intval($_POST['grupo_id']);
            
            // Opción 1: Redirigir con GET
            header("Location: clase.php?grupo_id={$grupo_id}");
            exit();
            
        } else {
            $this->view->showErrorMessage("No se especificó el grupo");
        }
    }


    public function Usuarios(){
   
        header("Location: usuarios.php");   

    }

}