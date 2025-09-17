<?php
require_once __DIR__ . '/../Conexion/Conexion.php';

class ClaseModel {
    private $id;
    private $dia;
    private $fecha;
    private $qr;
    private $grupo_id;
    private $db;

    public function __construct(Conexion $db) {
        $this->db = $db;
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
    private function obtenerClasesGrupo($grupo_id) {
        $sql = "SELECT 
                    c.id,
                    c.dia,
                    c.fecha,
                    c.qr,
                    c.grupo_id,
                    COUNT(a.estudiante_codigo) as asistencias_registradas
                FROM clases c
                LEFT JOIN asistencia a ON c.id = a.clases_id
                WHERE c.grupo_id = ?
                GROUP BY c.id, c.dia, c.fecha, c.qr, c.grupo_id
                ORDER BY c.fecha DESC, c.dia DESC";
        
        return $this->db->fetchAll($sql, [$grupo_id]);
    }

    // Obtener información del grupo
    private function obtenerInfoGrupo($grupo_id) {
        $sql = "SELECT 
                    g.id,
                    g.nombre as grupo_nombre,
                    g.capacidad,
                    m.nombre as materia_nombre,
                    p.nombres as profesor_nombres,
                    p.apellidos as profesor_apellidos,
                    COUNT(i.estudiante_codigo) as estudiantes_inscritos
                FROM grupo g
                INNER JOIN materia m ON g.materia_id = m.id
                INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                LEFT JOIN inscribe i ON g.id = i.grupo_id
                WHERE g.id = ?
                GROUP BY g.id, g.nombre, g.capacidad, m.nombre, p.nombres, p.apellidos";
        
        return $this->db->fetch($sql, [$grupo_id]);
    }

    // Método para crear una nueva clase
    public function crearClase($dia, $grupo_id, $qr = null) {
        try {
            $sql = "INSERT INTO clases (dia, fecha, qr, grupo_id) 
                    VALUES (?, NOW(), ?, ?)";
            
            return $this->db->query($sql, [$dia, $qr, $grupo_id]);
        } catch (Exception $e) {
            error_log("Error al crear clase: " . $e->getMessage());
            return false;
        }
    }

    // Verificar QR y registrar asistencia
    public function registrarAsistenciaConQR($qr_codigo, $usuarioId) {
        try {
            // Obtener datos del estudiante
            $estudiante = $this->obtenerDatosEstudiante($usuarioId);
            if (!$estudiante) {
                return ['success' => false, 'mensaje' => 'Estudiante no encontrado'];
            }

            // Verificar que el QR existe y obtener la clase
            $sql = "SELECT id, grupo_id, qr FROM clases WHERE qr = ?";
            $clase = $this->db->fetch($sql, [$qr_codigo]);
            
            if (!$clase) {
                return ['success' => false, 'mensaje' => 'Código QR inválido'];
            }

            // Verificar que el estudiante está inscrito en el grupo
            $sql = "SELECT 1 FROM inscribe WHERE estudiante_codigo = ? AND grupo_id = ?";
            $inscrito = $this->db->fetch($sql, [$estudiante['codigo'], $clase['grupo_id']]);
            
            if (!$inscrito) {
                return ['success' => false, 'mensaje' => 'No estás inscrito en este grupo'];
            }

            // Verificar si ya marcó asistencia
            $sql = "SELECT 1 FROM asistencia WHERE clases_id = ? AND estudiante_codigo = ?";
            $yaAsistio = $this->db->fetch($sql, [$clase['id'], $estudiante['codigo']]);
            
            if ($yaAsistio) {
                return ['success' => false, 'mensaje' => 'Ya has marcado asistencia para esta clase'];
            }

            // Registrar asistencia
            $sql = "INSERT INTO asistencia (clases_id, estudiante_codigo, fecha_registro) VALUES (?, ?, NOW())";
            $resultado = $this->db->query($sql, [$clase['id'], $estudiante['codigo']]);
            
            if ($resultado) {
                return ['success' => true, 'mensaje' => 'Asistencia registrada correctamente'];
            } else {
                return ['success' => false, 'mensaje' => 'Error al registrar asistencia'];
            }
            
        } catch (Exception $e) {
            error_log("Error al registrar asistencia: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error interno del sistema'];
        }
    }

    // Obtener datos del estudiante
    private function obtenerDatosEstudiante($usuarioId) {
        $sql = "SELECT codigo, nombres, apellidos FROM estudiante WHERE usuario_id = ?";
        return $this->db->fetch($sql, [$usuarioId]);
    }

    // Getters
   
}