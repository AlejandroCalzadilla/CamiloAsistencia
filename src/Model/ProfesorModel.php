<?php
class ProfesorModel
{

    private $codigo;
    private $nombres;
    private $apellidos;
    private $genero;
    private $usuario_id;



    private $db;

    public function __construct(Conexion $db)
    {

        $this->db = $db;

    }



    public function obtener(){


    }




    public function crear($codigo, $nombres, $apellidos, $genero ,$usuario_id){
        
    }


    public function editar(){


    }


    public function eliminar(){

    }





}