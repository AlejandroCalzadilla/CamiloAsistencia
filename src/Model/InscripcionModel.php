<?php

class InscripcionModel{


    private $estudiante_id;
    
    private $grupo_id;

    private $db;

    public function __construct(Conexion $db){
        $this->db =$db; 
    }



    public function crear($estudiante_id,$grupo_id){

    } 


    public function eliminar(){

    }

    public function validar(){


    }

}