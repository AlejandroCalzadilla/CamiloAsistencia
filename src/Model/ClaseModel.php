<?php
require_once __DIR__ . '/../Conexion/Conexion.php';

class ClaseModel {
    private $id;
    private $dia;
    private $fecha;
    private $qr;
    private $grupo_id;
    private $db;

    public function __construct() {
        $this->db = new Conexion();
    }

    // Método principal para obtener clases de un grupo específico
    public function mostrar($grupo_id) {
        session_start();
        $usuarioData = $_SESSION['usuario_logueado'];
        
        if (isset($usuarioData)) {
            $usuarioId = $usuarioData['id'];
            $rol = $usuarioData['rol'] ?? 'sin_rol';
            
            try {
                // Obtener clases del grupo
                $clases = $this->obtenerClasesGrupo($grupo_id);
                
                // Obtener información del grupo
                $grupoInfo = $this->obtenerInfoGrupo($grupo_id);
                
                return [
                    'clases' => $clases,
                    'grupo' => $grupoInfo,
                    'rol' => $rol,
                    'usuario_id' => $usuarioId
                ];
                
            } catch (Exception $e) {
                error_log("Error en ClaseModel::mostrar: " . $e->getMessage());
                return [
                    'clases' => [],
                    'grupo' => null,
                    'rol' => 'error',
                    'mensaje' => 'Error al consultar clases'
                ];
            }
        }
        
        return [
            'clases' => [],
            'grupo' => null,
            'rol' => 'no_logueado',
            'mensaje' => 'Usuario no logueado'
        ];
    }

    // Obtener clases de un grupo específico
   // Obtener clases de un grupo específico
    private function obtenerClasesGrupo($grupo_id) {
        $sql = "SELECT 
                    c.id,
                    c.dia,
                    c.hora_inicio,
                    c.hora_fin,
                    c.qr,
                    c.grupo_id,
                    COUNT(a.estudiante_codigo) as asistencias_registradas
                FROM clases c
                LEFT JOIN asistencia a ON c.id = a.clases_id
                WHERE c.grupo_id = ?
                GROUP BY c.id, c.dia, c.hora_inicio, c.hora_fin, c.qr, c.grupo_id
                ORDER BY c.dia DESC, c.hora_inicio DESC";
        
        return $this->db->fetchAll($sql, [$grupo_id]);
    }

    // Obtener información del grupo
    private function obtenerInfoGrupo($grupo_id) {
        $sql = "SELECT 
                    g.id,
                    g.nombre as grupo_nombre,
                    g.capacidad_maxima as capacidad,
                    m.nombre as materia_nombre,
                    p.nombres as profesor_nombres,
                    p.apellidos as profesor_apellidos,
                    COUNT(i.estudiante_codigo) as estudiantes_inscritos
                FROM grupo g
                INNER JOIN materia m ON g.materia_id = m.id
                INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                LEFT JOIN inscribe i ON g.id = i.grupo_id
                WHERE g.id = ?
                GROUP BY g.id, g.nombre, g.capacidad_maxima, m.nombre, p.nombres, p.apellidos";
        
        return $this->db->fetch($sql, [$grupo_id]);
    }

    
    public function crearClase($dia, $grupo_id, $hora_inicio, $hora_fin, $qr = null) {
        try {
            // Configurar zona horaria
            date_default_timezone_set('America/La_Paz');
            
            // Generar código QR único si no se proporciona
            if (!$qr) {
                $qr = $this->generarCodigo($grupo_id);
            }
            
            // Insertar en la nueva estructura
            $sql = "INSERT INTO clases (dia, hora_inicio, hora_fin, qr, grupo_id) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $claseId = $this->db->insert($sql, [$dia, $hora_inicio, $hora_fin, $qr, $grupo_id]);
            
            if ($claseId) {
                return [
                    'success' => true,
                    'mensaje' => 'Clase creada exitosamente',
                    'clase_id' => $claseId,
                    'codigo' => $qr,
                    'hora_inicio' => $hora_inicio,
                    'hora_fin' => $hora_fin
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => 'Error al crear la clase'
                ];
            }
        } catch (Exception $e) {
            error_log("Error al crear clase: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al crear la clase: ' . $e->getMessage()
            ];
        }
    }

    public function eliminar($clase_id) {
        try {
            $sql = "DELETE FROM clases WHERE id = ?";
            $resultado = $this->db->delete($sql, [$clase_id]);
            
            if ($resultado > 0) {
                return [
                    'success' => true,
                    'mensaje' => 'Clase eliminada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => 'No se pudo eliminar la clase'
                ];
            }
        } catch (Exception $e) {
            error_log("Error al eliminar clase: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al eliminar la clase: ' . $e->getMessage()
            ];
        }
    }

    // Generar código QR único
    private function generarCodigo($grupo_id) {
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        return   $grupo_id . '_' . $timestamp . '_' . $random;
    }


   
}