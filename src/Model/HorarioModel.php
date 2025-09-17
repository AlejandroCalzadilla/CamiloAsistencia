<?php
class Horario {

 
    private $id;

    private $hora_inicio;

    private $hora_fin;

    private $dia;

    private $grupo_id;

    private $db; 

 
     public function __construct(Conexion $db){
         $this->db=$db;
     }


   public function obtener(){}


   public function obtenerPorGrupo($grupo_id){


   }



   public function crear(){



   }

   public function editar(){


   }


   public function eliminar(){



   }



   public function validar(){


    
   }


}