<?php

class InscripcionModel{

    private $estudiante_codigo;
    private $grupo_id;
    private $fecha_inscripcion;
    private $db;

    public function __construct(){
        $this->db = new Conexion(); 
    }

    
    
    public function crear($estudiante_codigo, $grupo_id) {

            if ($this->existeInscripcion($estudiante_codigo, $grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'El estudiante ya está inscrito en este grupo'
                ];
            }
            if (!$this->verificarCapacidad($grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'El grupo ha alcanzado su capacidad máxima'
                ];
            }
       
            $sql = "INSERT INTO inscribe (estudiante_codigo, grupo_id, fecha_inscripcion) 
                    VALUES (?, ?, CURRENT_TIMESTAMP)";
            $this->db->query($sql, [$estudiante_codigo, $grupo_id]);
            $this->actualizarCapacidadGrupo($grupo_id);
            return [
                'success' => true,
                'message' => 'Inscripción realizada exitosamente'
            ];
    }

    // Eliminar una inscripción
    public function eliminar($estudiante_codigo, $grupo_id) {
            if (!$this->existeInscripcion($estudiante_codigo, $grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'La inscripción no existe'
                ];
            }
            $sql = "DELETE FROM inscribe WHERE estudiante_codigo = ? AND grupo_id = ?";
            $filasAfectadas = $this->db->delete($sql, [$estudiante_codigo, $grupo_id]);
            if ($filasAfectadas > 0) {
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
    }

    // Obtener todas las inscripciones
    public function mostrar() {
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
            $sql = "UPDATE grupo 
                    SET capacidad_actual = (
                        SELECT COUNT(*) FROM inscribe WHERE grupo_id = ?
                    ) 
                    WHERE id = ?";
            $this->db->update($sql, [$grupo_id, $grupo_id]);
    }   
}