<?php

class AsignacionModel{

    private $estudiante_codigo;
    private $grupo_id;
    private $fecha_asignacion;
    private $db;

    public function __construct(){
        $this->db = new Conexion(); 
    }

    
    
    public function crear($estudiante_codigo, $grupo_id) {

            if ($this->existeAsignacion($estudiante_codigo, $grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'El estudiante ya está asignado en este grupo'
                ];
            }
            if (!$this->verificarCapacidad($grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'El grupo ha alcanzado su capacidad máxima'
                ];
            }
       
            $sql = "INSERT INTO asignacion (estudiante_codigo, grupo_id, fecha_asignacion) 
                    VALUES (?, ?, CURRENT_TIMESTAMP)";
            $this->db->query($sql, [$estudiante_codigo, $grupo_id]);
            $this->actualizarCapacidadGrupo($grupo_id);
            return [
                'success' => true,
                'message' => 'Asignación realizada exitosamente'
            ];
    }

    // Eliminar una asignación
    public function eliminar($estudiante_codigo, $grupo_id) {
            if (!$this->existeAsignacion($estudiante_codigo, $grupo_id)) {
                return [
                    'success' => false,
                    'message' => 'La asignación no existe'
                ];
            }
            $sql = "DELETE FROM asignacion WHERE estudiante_codigo = ? AND grupo_id = ?";
            $filasAfectadas = $this->db->delete($sql, [$estudiante_codigo, $grupo_id]);
            if ($filasAfectadas > 0) {
                $this->actualizarCapacidadGrupo($grupo_id);  
                return [
                    'success' => true,
                    'message' => 'Asignación eliminada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo eliminar la asignación'
                ];
            }
    }

    // Obtener todas las asignaciones
    public function mostrar() {
        try {
            $sql = "SELECT 
                        a.estudiante_codigo,
                        a.grupo_id,
                        a.fecha_asignacion,
                        e.nombres as estudiante_nombres,
                        e.apellidos as estudiante_apellidos,
                        e.ci as estudiante_ci,
                        g.nombre as grupo_nombre,
                        m.nombre as materia_nombre,
                        p.nombres as profesor_nombres,
                        p.apellidos as profesor_apellidos
                    FROM asignacion a
                    INNER JOIN estudiante e ON a.estudiante_codigo = e.codigo
                    INNER JOIN grupo g ON a.grupo_id = g.id
                    INNER JOIN materia m ON g.materia_id = m.id
                    INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                    ORDER BY a.fecha_asignacion DESC";

            return $this->db->fetchAll($sql);

        } catch (Exception $e) {
            error_log("Error en AsignacionModel::obtenerTodas: " . $e->getMessage());
            return [];
        }
    }


    // Verificar si existe una asignación
    private function existeAsignacion($estudiante_codigo, $grupo_id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM asignacion
                    WHERE estudiante_codigo = ? AND grupo_id = ?";
            $resultado = $this->db->fetch($sql, [$estudiante_codigo, $grupo_id]);
            return $resultado['total'] > 0;

        } catch (Exception $e) {
            error_log("Error en AsignacionModel::existeAsignacion: " . $e->getMessage());
            return false;
        }
    }

  
    private function verificarCapacidad($grupo_id) {
        try {
            $sql = "SELECT 
                        g.capacidad_maxima,
                        COUNT(a.estudiante_codigo) as asignados_actuales
                    FROM grupo g
                    LEFT JOIN asignacion a ON g.id = a.grupo_id
                    WHERE g.id = ?
                    GROUP BY g.id, g.capacidad_maxima";
            
            $resultado = $this->db->fetch($sql, [$grupo_id]);
            
            if ($resultado) {
                return $resultado['asignados_actuales'] < $resultado['capacidad_maxima'];
            }
            
            return false;

        } catch (Exception $e) {
            error_log("Error en AsignacionModel::verificarCapacidad: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar la capacidad actual del grupo
    private function actualizarCapacidadGrupo($grupo_id) {
            $sql = "UPDATE grupo 
                    SET capacidad_actual = (
                        SELECT COUNT(*) FROM asignacion WHERE grupo_id = ?
                    ) 
                    WHERE id = ?";
            $this->db->update($sql, [$grupo_id, $grupo_id]);
    }   
}