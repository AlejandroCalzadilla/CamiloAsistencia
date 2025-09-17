<?php

require_once __DIR__ . '/../View/interfaces/View.php';

class UsuarioView implements View
{

    private $model;
    public function __construct(UsuarioModel $model)
    {
        $this->model = $model;
    }

    public function render()
    {
        // Aquí va el código HTML para mostrar la vista de usuario
        // Puedes acceder a los datos del modelo usando $this->model
    }

    


}