<?php
class Estudiante {
    private $codigo;
    private $nombres;
    private $apellidos;
    private $estado;
    private $creado_en;
    private $actualizado_en;

    private $db;

    public function __construct(Conexion $db)
    {
      $this->db=$db;
    }

    

    // Método para validar datos
    public function validar() {
        $errores = [];
        
        if (empty($this->codigo)) {
            $errores[] = 'El código es obligatorio';
        }
        
        if (empty($this->nombres)) {
            $errores[] = 'Los nombres son obligatorios';
        }
        
        if (empty($this->apellidos)) {
            $errores[] = 'Los apellidos son obligatorios';
        }
        
        $estadosValidos = ['activo', 'inactivo'];
        if (!in_array($this->estado, $estadosValidos)) {
            $errores[] = 'El estado debe ser "activo" o "inactivo"';
        }
        
        return $errores;
    }
}
