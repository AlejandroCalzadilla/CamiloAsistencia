<?php
class ProfesorModel
    // Obtener todos los profesores
   
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



    public function obtener() {
        $sql = "SELECT * FROM profesor ORDER BY creado_en DESC";
        return $this->db->fetchAll($sql);
    }

    // Obtener usuarios no asociados a estudiante ni profesor
    public function obtenerUsuariosLibres() {
        $sql = "SELECT id, nombre FROM usuario \n
                WHERE id NOT IN (\n
                    SELECT usuario_id FROM estudiante\n
                    UNION\n
                    SELECT usuario_id FROM profesor\n
                )\n
                ORDER BY nombre ASC";
        return $this->db->fetchAll($sql);
    }

    // Crear profesor
    public function crear($codigo, $nombres, $apellidos, $genero, $usuario_id) {
        $sql = "INSERT INTO profesor (codigo, nombres, apellidos, genero, usuario_id, creado_en, actualizado_en) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        return $this->db->query($sql, [$codigo, $nombres, $apellidos, $genero, $usuario_id]);
    }

    // Editar profesor
    public function editar($codigo, $nombres, $apellidos, $genero, $usuario_id) {
        $sql = "UPDATE profesor SET nombres = ?, apellidos = ?, genero = ?, usuario_id = ?, actualizado_en = NOW() WHERE codigo = ?";
        return $this->db->query($sql, [$nombres, $apellidos, $genero, $usuario_id, $codigo]);
    }

    // Eliminar profesor
    public function eliminar($codigo) {
        $sql = "DELETE FROM profesor WHERE codigo = ?";
        return $this->db->query($sql, [$codigo]);
    }




}