<?php
class EstudianteModel
{
    private $codigo;
    private $nombres;
    private $apellidos;
    private $estado;
    private $creado_en;
    private $actualizado_en;
    private $usuario_id;
    private $db;

    public function __construct()
    {
        $this->db = new Conexion();
    }

    public function obtenerTodos()
    {
        try {
            $sql = "SELECT e.*, u.nombre as usuario_nombre 
                    FROM estudiante e 
                    LEFT JOIN usuario u ON e.usuario_id = u.id 
                    ORDER BY e.creado_en DESC";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            error_log("Error al obtener estudiantes: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorCodigo($codigo)
    {
        try {
            $sql = "SELECT e.*, u.nombre as usuario_nombre 
                    FROM estudiante e 
                    LEFT JOIN usuario u ON e.usuario_id = u.id 
                    WHERE e.codigo = ?";
            return $this->db->fetch($sql, [$codigo]);
        } catch (Exception $e) {
            error_log("Error al obtener estudiante: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerUsuariosLibres()
    {
        $sql = "SELECT id, nombre FROM usuario 
                    WHERE id NOT IN (
                        SELECT usuario_id FROM estudiante WHERE usuario_id IS NOT NULL
                        UNION
                        SELECT usuario_id FROM profesor WHERE usuario_id IS NOT NULL
                    )
                    ORDER BY nombre ASC";
        return $this->db->fetchAll($sql);
    }

    public function crear($data)
    {

      
        $existeCodigo = $this->db->fetch("SELECT codigo FROM estudiante WHERE codigo = ?", [$data['codigo']]);
        if ($existeCodigo) {
            return [
                'success' => false,
                'mensaje' => 'Ya existe un estudiante con ese código'
            ];
        }
        // Verificar que el CI no exista
        $existeCI = $this->db->fetch("SELECT ci FROM estudiante WHERE ci = ?", [$data['ci']]);
        if ($existeCI) {
            return [
                'success' => false,
                'mensaje' => 'Ya existe un estudiante con ese CI'
            ];
        }
        // Verificar que el usuario no esté asignado
        $usuarioAsignado = $this->db->fetch(
            "SELECT codigo FROM estudiante WHERE usuario_id = ? 
                 UNION 
                 SELECT codigo FROM profesor WHERE usuario_id = ?",
            [$data['usuario_id'], $data['usuario_id']]
        );
        if ($usuarioAsignado) {
            return [
                'success' => false,
                'mensaje' => 'El usuario ya está asignado a otro estudiante o profesor'
            ];
        }
        $sql = "INSERT INTO estudiante (codigo, ci, nombres, apellidos, estado, genero, usuario_id, creado_en, actualizado_en) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $this->db->query($sql, [
            trim($data['codigo']),
            trim($data['ci']),
            trim($data['nombres']),
            trim($data['apellidos']),
            $data['estado'],
            $data['genero'] ?? '',
            $data['usuario_id']
        ]);

        return [
            'success' => true,
            'mensaje' => 'Estudiante creado correctamente'
        ];
    }

    public function actualizar($data)
    {
        // Validaciones
        if (
            empty($data['codigo']) || empty($data['ci']) || empty($data['nombres']) ||
            empty($data['apellidos']) || empty($data['estado']) || empty($data['usuario_id'])
        ) {
            return [
                'success' => false,
                'mensaje' => 'Todos los campos obligatorios deben ser completados'
            ];
        }

        // Verificar que el estudiante existe
        $estudiante = $this->obtenerPorCodigo($data['codigo']);
        if (!$estudiante) {
            return [
                'success' => false,
                'mensaje' => 'El estudiante no existe'
            ];
        }

        // Verificar que el CI no exista en otro estudiante
        $existeCI = $this->db->fetch("SELECT codigo FROM estudiante WHERE ci = ? AND codigo != ?", [$data['ci'], $data['codigo']]);
        if ($existeCI) {
            return [
                'success' => false,
                'mensaje' => 'Ya existe otro estudiante con ese CI'
            ];
        }
        // Verificar que el usuario no esté asignado a otro
        $usuarioAsignado = $this->db->fetch(
            "SELECT codigo FROM estudiante WHERE usuario_id = ? AND codigo != ?
                 UNION 
                 SELECT codigo FROM profesor WHERE usuario_id = ?",
            [$data['usuario_id'], $data['codigo'], $data['usuario_id']]
        );
        if ($usuarioAsignado) {
            return [
                'success' => false,
                'mensaje' => 'El usuario ya está asignado a otro estudiante o profesor'
            ];
        }
        $sql = "UPDATE estudiante SET ci = ?, nombres = ?, apellidos = ?, estado = ?, genero = ?, usuario_id = ?, actualizado_en = NOW() 
                    WHERE codigo = ?";
        $resultado = $this->db->query($sql, [
            trim($data['ci']),
            trim($data['nombres']),
            trim($data['apellidos']),
            $data['estado'],
            $data['genero'] ?? '',
            $data['usuario_id'],
            $data['codigo']
        ]);
        return [
            'success' => true,
            'mensaje' => 'Estudiante actualizado correctamente'
        ];


    }

    public function eliminar($codigo)
    {
        if (empty($codigo)) {
            return [
                'success' => false,
                'mensaje' => 'Código de estudiante requerido'
            ];
        }
        $referencias = $this->verificarReferenciasEstudiante($codigo);
        if (!empty($referencias)) {
            return [
                'success' => false,
                'mensaje' => 'No se puede eliminar el estudiante porque tiene referencias en: ' . implode(', ', $referencias)
            ];
        }
        $sql = "DELETE FROM estudiante WHERE codigo = ?";
        $resultado = $this->db->query($sql, [$codigo]);

        return [
            'success' => true,
            'mensaje' => 'Estudiante eliminado correctamente'
        ];
    }

    // Método auxiliar para verificar referencias
    private function verificarReferenciasEstudiante($codigo)
    {
        $referencias = [];
        $sqlAsistencias = "SELECT COUNT(*) as count FROM asistencia WHERE estudiante_codigo = ?";
        $asistencias = $this->db->fetch($sqlAsistencias, [$codigo]);
        if ($asistencias && $asistencias['count'] > 0) {
            $referencias[] = "Asistencias ({$asistencias['count']})";
        }
        $sqlInscripciones = "SELECT COUNT(*) as count FROM inscribe WHERE estudiante_codigo = ?";
        $inscripciones = $this->db->fetch($sqlInscripciones, [$codigo]);
        if ($inscripciones && $inscripciones['count'] > 0) {
            $referencias[] = "Inscripciones ({$inscripciones['count']})";
        }
        return $referencias;
    }

}