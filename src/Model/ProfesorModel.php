<?php
class ProfesorModel{

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
    // Método principal que obtiene profesores con usuarios y usuarios libres
    public function obtener() {
        try {
            $resultado = [];
            
            // 1. Obtener profesores con sus usuarios asociados
            $sqlProfesores = "SELECT p.codigo, p.nombres, p.apellidos, p.genero, p.usuario_id, 
                                     p.creado_en, p.actualizado_en,
                                     u.nombre as usuario_nombre 
                              FROM profesor p 
                              INNER JOIN usuario u ON p.usuario_id = u.id 
                              ORDER BY p.creado_en DESC";
            
            $profesores = $this->db->fetchAll($sqlProfesores);
            
            // 2. Obtener usuarios libres (no asignados a estudiante ni profesor)
            $sqlUsuariosLibres = "SELECT u.id, u.nombre 
                                  FROM usuario u
                                  WHERE u.id NOT IN (
                                      SELECT usuario_id FROM estudiante WHERE usuario_id IS NOT NULL
                                      UNION
                                      SELECT usuario_id FROM profesor WHERE usuario_id IS NOT NULL
                                  )
                                  ORDER BY u.nombre ASC";
            
            $usuariosLibres = $this->db->fetchAll($sqlUsuariosLibres);
            
            // 3. Retornar ambos conjuntos de datos
            $resultado = [
                'profesores' => $profesores,
                'usuarios_libres' => $usuariosLibres
            ];
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error al obtener profesores: " . $e->getMessage());
            return [
                'profesores' => [],
                'usuarios_libres' => []
            ];
        }
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