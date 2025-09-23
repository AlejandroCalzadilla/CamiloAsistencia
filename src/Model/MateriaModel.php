<?php
class MateriaModel
{
    private $id;
    private $nombre;
    private $db;

    public function __construct()
    {
        $this->db = new Conexion();
    }

    public function obtener()
    {
        $sql = "SELECT id, nombre FROM materia ORDER BY nombre ASC";
        return $this->db->fetchAll($sql);
    }

    public function crear($nombre)
    {
        $sql = "INSERT INTO materia (nombre) VALUES (?)";
        return $this->db->query($sql, [$nombre]);
    }

    public function editar($nombre, $id)
    {
        $sql = "UPDATE materia SET nombre = ? WHERE id = ?";
        return $this->db->query($sql, [$nombre, $id]);
    }

    public function eliminar($id)
    {
            if (empty($id)) {
                return [
                    'success' => false,
                    'mensaje' => 'ID de materia requerido'
                ];
            }

            // Verificar si la materia tiene referencias
            $referencias = $this->verificarReferenciasMateria($id);
            if (!empty($referencias)) {
                return [
                    'success' => false,
                    'mensaje' => 'No se puede eliminar la materia porque tiene referencias en: ' . implode(', ', $referencias)
                ];
            }

            $sql = "DELETE FROM materia WHERE id = ?";
            $resultado = $this->db->query($sql, [$id]);

            return [
                'success' => true,
                'mensaje' => 'Materia eliminada correctamente'
            ];

        
    }

    // Método auxiliar para verificar referencias
    private function verificarReferenciasMateria($materiaId)
    {
        $referencias = [];
        // Verificar si tiene grupos
        $sqlGrupos = "SELECT COUNT(*) as count FROM grupo WHERE materia_id = ?";
        $grupos = $this->db->fetch($sqlGrupos, [$materiaId]);
        if ($grupos && $grupos['count'] > 0) {
            $referencias[] = "Grupos ({$grupos['count']})";
        }
        return $referencias;
    }
}