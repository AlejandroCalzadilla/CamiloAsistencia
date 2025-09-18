<?php

class InscripcionModel{

    private $estudiante_codigo;
    private $grupo_id;
    private $fecha_inscripcion;
    private $db;

    public function __construct(Conexion $db){
        $this->db = $db; 
    }

    // Setters
    
    // Crear una nueva inscripción
    public function crear($estudiante_codigo, $grupo_id) {
        try {
            // Verificar si ya existe la inscripción
            if ($this->existeInscripcion($estudiante_codigo, $grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'El estudiante ya está inscrito en este grupo'
                ];
            }

            // Verificar capacidad del grupo
            if (!$this->verificarCapacidad($grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'El grupo ha alcanzado su capacidad máxima'
                ];
            }

            // Insertar inscripción
            $sql = "INSERT INTO inscribe (estudiante_codigo, grupo_id, fecha_inscripcion) 
                    VALUES (?, ?, CURRENT_TIMESTAMP)";
            
            $this->db->query($sql, [$estudiante_codigo, $grupo_id]);
            
            // Actualizar capacidad actual del grupo
            $this->actualizarCapacidadGrupo($grupo_id);
            
            return [
                'success' => true,
                'message' => 'Inscripción realizada exitosamente'
            ];

        } catch (Exception $e) {
            error_log("Error en InscripcionModel::crear: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al realizar la inscripción: ' . $e->getMessage()
            ];
        }
    }

    // Eliminar una inscripción
    public function eliminar($estudiante_codigo, $grupo_id) {
        try {
            // Verificar si existe la inscripción
            if (!$this->existeInscripcion($estudiante_codigo, $grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'La inscripción no existe'
                ];
            }
            $sql = "DELETE FROM inscribe WHERE estudiante_codigo = ? AND grupo_id = ?";
            $filasAfectadas = $this->db->delete($sql, [$estudiante_codigo, $grupo_id]);
            if ($filasAfectadas > 0) {
                // Actualizar capacidad actual del grupo
                $this->actualizarCapacidadGrupo($grupo_id);
                
                return [
                    'success' => true,
                    'message' => 'Inscripción eliminada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo eliminar la inscripción'
                ];
            }

        } catch (Exception $e) {
            error_log("Error en InscripcionModel::eliminar: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al eliminar la inscripción: ' . $e->getMessage()
            ];
        }
    }

    // Obtener todas las inscripciones
    public function obtenerTodas() {
        try {
            $sql = "SELECT 
                        i.estudiante_codigo,
                        i.grupo_id,
                        i.fecha_inscripcion,
                        e.nombres as estudiante_nombres,
                        e.apellidos as estudiante_apellidos,
                        e.ci as estudiante_ci,
                        g.nombre as grupo_nombre,
                        m.nombre as materia_nombre,
                        p.nombres as profesor_nombres,
                        p.apellidos as profesor_apellidos
                    FROM inscribe i
                    INNER JOIN estudiante e ON i.estudiante_codigo = e.codigo
                    INNER JOIN grupo g ON i.grupo_id = g.id
                    INNER JOIN materia m ON g.materia_id = m.id
                    INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                    ORDER BY i.fecha_inscripcion DESC";
            
            return $this->db->fetchAll($sql);

        } catch (Exception $e) {
            error_log("Error en InscripcionModel::obtenerTodas: " . $e->getMessage());
            return [];
        }
    }

    // Obtener inscripciones por grupo_id
    public function obtenerPorGrupoId($grupo_id) {
        try {
            $sql = "SELECT 
                        i.estudiante_codigo,
                        i.grupo_id,
                        i.fecha_inscripcion,
                        e.nombres as estudiante_nombres,
                        e.apellidos as estudiante_apellidos,
                        e.ci as estudiante_ci,
                        e.genero as estudiante_genero,
                        e.estado as estudiante_estado
                    FROM inscribe i
                    INNER JOIN estudiante e ON i.estudiante_codigo = e.codigo
                    WHERE i.grupo_id = ?
                    ORDER BY e.apellidos, e.nombres";
            
            return $this->db->fetchAll($sql, [$grupo_id]);

        } catch (Exception $e) {
            error_log("Error en InscripcionModel::obtenerPorGrupoId: " . $e->getMessage());
            return [];
        }
    }

    // Obtener inscripciones por estudiante_codigo
    public function obtenerPorEstudiante($estudiante_codigo) {
        try {
            $sql = "SELECT 
                        i.estudiante_codigo,
                        i.grupo_id,
                        i.fecha_inscripcion,
                        g.nombre as grupo_nombre,
                        g.capacidad_maxima,
                        g.capacidad_actual,
                        m.nombre as materia_nombre,
                        p.nombres as profesor_nombres,
                        p.apellidos as profesor_apellidos
                    FROM inscribe i
                    INNER JOIN grupo g ON i.grupo_id = g.id
                    INNER JOIN materia m ON g.materia_id = m.id
                    INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                    WHERE i.estudiante_codigo = ?
                    ORDER BY g.nombre";
            
            return $this->db->fetchAll($sql, [$estudiante_codigo]);

        } catch (Exception $e) {
            error_log("Error en InscripcionModel::obtenerPorEstudiante: " . $e->getMessage());
            return [];
        }
    }

    

    // Verificar si existe una inscripción
    private function existeInscripcion($estudiante_codigo, $grupo_id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM inscribe 
                    WHERE estudiante_codigo = ? AND grupo_id = ?";
            $resultado = $this->db->fetch($sql, [$estudiante_codigo, $grupo_id]);
            return $resultado['total'] > 0;

        } catch (Exception $e) {
            error_log("Error en InscripcionModel::existeInscripcion: " . $e->getMessage());
            return false;
        }
    }

  
    private function verificarCapacidad($grupo_id) {
        try {
            $sql = "SELECT 
                        g.capacidad_maxima,
                        COUNT(i.estudiante_codigo) as inscritos_actuales
                    FROM grupo g
                    LEFT JOIN inscribe i ON g.id = i.grupo_id
                    WHERE g.id = ?
                    GROUP BY g.id, g.capacidad_maxima";
            
            $resultado = $this->db->fetch($sql, [$grupo_id]);
            
            if ($resultado) {
                return $resultado['inscritos_actuales'] < $resultado['capacidad_maxima'];
            }
            
            return false;

        } catch (Exception $e) {
            error_log("Error en InscripcionModel::verificarCapacidad: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar la capacidad actual del grupo
    private function actualizarCapacidadGrupo($grupo_id) {
        try {
            $sql = "UPDATE grupo 
                    SET capacidad_actual = (
                        SELECT COUNT(*) FROM inscribe WHERE grupo_id = ?
                    ) 
                    WHERE id = ?";
            
            $this->db->update($sql, [$grupo_id, $grupo_id]);

        } catch (Exception $e) {
            error_log("Error en InscripcionModel::actualizarCapacidadGrupo: " . $e->getMessage());
        }
    }


    // Validar datos de inscripción
    public function validar($estudiante_codigo, $grupo_id) {
        $errores = [];

        if (empty($estudiante_codigo)) {
            $errores[] = 'El código del estudiante es obligatorio';
        }

        if (empty($grupo_id)) {
            $errores[] = 'El ID del grupo es obligatorio';
        }

        // Verificar si el estudiante existe
        if (!empty($estudiante_codigo) && !$this->existeEstudiante($estudiante_codigo)) {
            $errores[] = 'El estudiante no existe';
        }

        // Verificar si el grupo existe
        if (!empty($grupo_id) && !$this->existeGrupo($grupo_id)) {
            $errores[] = 'El grupo no existe';
        }

        return $errores;
    }

   
}