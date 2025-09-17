<?php
class EstudianteModel{
    private $codigo;
    private $nombres;
    private $apellidos;
    private $estado;
    private $creado_en;
    private $actualizado_en;

    private $usuario_id;

    private $db;

    public function __construct(Conexion $db) {
        $this->db = $db;
    }

    // Mostrar todos los estudiantes
    public function obtenerTodos() {
        $sql = "SELECT * FROM estudiante ORDER BY creado_en DESC";
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

    // Mostrar un estudiante por código
    public function mostrar($codigo = null) {
        if ($codigo === null) $codigo = $this->codigo;
        $sql = "SELECT * FROM estudiante WHERE codigo = ?";
        return $this->db->fetch($sql, [$codigo]);
    }

    // Crear estudiante
  public function crear($data) {
        $sql = "INSERT INTO estudiante (codigo, ci, nombres, apellidos, estado, genero, usuario_id, creado_en, actualizado_en) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        return $this->db->query($sql, [
            $data['codigo'],
            $data['ci'],
            $data['nombres'],
            $data['apellidos'],
            $data['estado'],
            $data['genero'],
            $data['usuario_id']
        ]);
    }

    // Actualizar estudiante
    public function actualizar($data) {
        $sql = "UPDATE estudiante SET ci = ?, nombres = ?, apellidos = ?, estado = ?, genero = ?, usuario_id = ?, actualizado_en = NOW() WHERE codigo = ?";
        return $this->db->query($sql, [
            $data['ci'],
            $data['nombres'],
            $data['apellidos'],
            $data['estado'],
            $data['genero'],
            $data['usuario_id'],
            $data['codigo']
        ]);
    }
    // Eliminar estudiante
    public function eliminar($codigo) {
        $sql = "DELETE FROM estudiante WHERE codigo = ?";
        return $this->db->query($sql, [$codigo]);
    }
}