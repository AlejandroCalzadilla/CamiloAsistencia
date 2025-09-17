<?php
class MateriaModel {
    private $id;
    private $nombre;
    private $db;

    public function __construct(Conexion $db) {
        $this->db = $db;
    }

    public function obtener() {
        $sql = "SELECT id, nombre FROM materia ORDER BY nombre ASC";
        return $this->db->fetchAll($sql);
    }

    public function crear($nombre) {
        $sql = "INSERT INTO materia (nombre) VALUES (?)";
        return $this->db->query($sql, [$nombre]);
    }

    public function editar($nombre, $id) {
        $sql = "UPDATE materia SET nombre = ? WHERE id = ?";
        return $this->db->query($sql, [$nombre, $id]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM materia WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
}