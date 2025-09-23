<?php
class AsistenciaModel
{
    private $id;

    private $hora_inicio;

    private $hora_fin;

    private $tipo;

    private $clase_id;

    private $db;


    public function __construct(Conexion $db)
    {
        $this->db = $db;
    }



    public function obtener( $grupo_id)
    {
  
        $usuarioData = $_SESSION['usuario_logueado'];
        if (isset($usuarioData)) {
            $usuarioId = $usuarioData['id'];
            $rol = $usuarioData['rol'] ?? 'sin_rol';
        }

        try {
            if ($rol === 'profesor') {
                // Consulta para profesor: obtener asistencias de todas las clases de sus grupos
                $sql = "SELECT 
                        a.id as asistencia_id,
                        a.tipo,
                        c.id as clase_id,
                        c.dia as dia_clase,
                      
                        c.qr,
                        g.id as grupo_id,
                        g.nombre as grupo_nombre,
                        m.nombre as materia_nombre,
                        e.codigo as estudiante_codigo,
                        e.nombres as estudiante_nombres,
                        e.apellidos as estudiante_apellidos,
                        COUNT(*) OVER (PARTITION BY c.id) as total_estudiantes_clase,
                        SUM(CASE WHEN a.tipo = 'presente' THEN 1 ELSE 0 END) OVER (PARTITION BY c.id) as presentes,
                        SUM(CASE WHEN a.tipo = 'retraso' THEN 1 ELSE 0 END) OVER (PARTITION BY c.id) as retrasos,
                        SUM(CASE WHEN a.tipo = 'ausente' THEN 1 ELSE 0 END) OVER (PARTITION BY c.id) as ausentes
                    FROM asistencia a
                    INNER JOIN clases c ON a.clases_id = c.id
                    INNER JOIN grupo g ON c.grupo_id = g.id
                    INNER JOIN materia m ON g.materia_id = m.id
                    INNER JOIN estudiante e ON a.estudiante_codigo = e.codigo
                    INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                    WHERE p.usuario_id = ?";

                $params = [$usuarioId];

                // Si se especifica un grupo, filtrar por ese grupo
                if ($grupo_id) {
                    $sql .= " AND g.id = ?";
                    $params[] = $grupo_id;
                }

                $sql .= " ORDER BY c.id DESC, a.id DESC, e.apellidos, e.nombres";

                return $this->db->fetchAll($sql, $params);

            } elseif ($rol === 'estudiante') {
                // Consulta para estudiante: obtener sus propias asistencias
                $sqlEstudiante = "SELECT codigo FROM estudiante WHERE usuario_id = ?";
                $estudiante = $this->db->fetch($sqlEstudiante, [$usuarioId]);

                if (!$estudiante) {
                    return [
                        'success' => false,
                        'mensaje' => 'No se encontró información del estudiante'
                    ];
                }

                $estudiante_codigo = $estudiante['codigo'];

                $sql = "SELECT 
                        a.id as asistencia_id,
                        a.tipo,
                        c.id as clase_id,
                        c.dia as dia_clase,
                       
                        c.qr,
                        g.id as grupo_id,
                        g.nombre as grupo_nombre,
                        m.nombre as materia_nombre,
                        p.nombres as profesor_nombres,
                        p.apellidos as profesor_apellidos,
                        -- Estadísticas del estudiante
                        (SELECT COUNT(*) 
                         FROM asistencia a2 
                         INNER JOIN clases c2 ON a2.clases_id = c2.id 
                         WHERE a2.estudiante_codigo = ? AND c2.grupo_id = g.id) as total_clases_grupo,
                        (SELECT COUNT(*) 
                         FROM asistencia a2 
                         INNER JOIN clases c2 ON a2.clases_id = c2.id 
                         WHERE a2.estudiante_codigo = ? AND a2.tipo = 'presente' AND c2.grupo_id = g.id) as clases_presente,
                        (SELECT COUNT(*) 
                         FROM asistencia a2 
                         INNER JOIN clases c2 ON a2.clases_id = c2.id 
                         WHERE a2.estudiante_codigo = ? AND a2.tipo = 'retraso' AND c2.grupo_id = g.id) as clases_retraso
                    FROM asistencia a
                    INNER JOIN clases c ON a.clases_id = c.id
                    INNER JOIN grupo g ON c.grupo_id = g.id
                    INNER JOIN materia m ON g.materia_id = m.id
                    INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                    WHERE a.estudiante_codigo = ?";

                $params = [$estudiante_codigo, $estudiante_codigo, $estudiante_codigo, $estudiante_codigo];

                // Si se especifica un grupo, filtrar por ese grupo
                if ($grupo_id) {
                    $sql .= " AND g.id = ?";
                    $params[] = $grupo_id;
                }

              

                return $this->db->fetchAll($sql, $params);

            } else {
                return [
                    'success' => false,
                    'mensaje' => 'Rol de usuario no válido'
                ];
            }

        } catch (Exception $e) {
            error_log("Error al obtener asistencias: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al consultar asistencias'
            ];
        }
    }




   public function marcarPresente($usuario_id, $clase_id, $codigo_verificacion) 
    {
        try {
            // Configurar zona horaria
            date_default_timezone_set('America/La_Paz');
            
            // Obtener datos del estudiante
            $estudiante = $this->obtenerDatosEstudiante($usuario_id);
            if (!$estudiante) {
                return ['success' => false, 'mensaje' => 'Estudiante no encontrado'];
            }

            // Verificar que el QR existe y obtener la clase
            $sql = "SELECT c.id, c.grupo_id, c.qr, c.dia, c.hora_inicio, c.hora_fin 
                    FROM clases c 
                    WHERE c.qr = ?";
            $clase = $this->db->fetch($sql, [$codigo_verificacion]);
            
            if (!$clase) {
                return ['success' => false, 'mensaje' => 'Código QR inválido'];
            }

            // Verificar que el estudiante está inscrito en el grupo
            $sql = "SELECT 1 FROM inscribe WHERE estudiante_codigo = ? AND grupo_id = ?";
            $inscrito = $this->db->fetch($sql, [$estudiante['codigo'], $clase['grupo_id']]);
            
            if (!$inscrito) {
                return ['success' => false, 'mensaje' => 'No estás inscrito en este grupo'];
            }

            // Verificar si ya existe el registro de asistencia
            $sql = "SELECT id, tipo FROM asistencia WHERE clases_id = ? AND estudiante_codigo = ?";
            $asistencia = $this->db->fetch($sql, [$clase['id'], $estudiante['codigo']]);
            
            if (!$asistencia) {
                return ['success' => false, 'mensaje' => 'No se encontró registro de asistencia para esta clase'];
            }
            
            if ($asistencia['tipo'] === 'presente' || $asistencia['tipo'] === 'retraso') {
                return ['success' => false, 'mensaje' => 'Ya has marcado asistencia para esta clase'];
            }

            // Determinar si es presente o retraso basado en la hora
            $hora_actual = date('H:i:s');
            $tipo_asistencia = 'ausente';
            if ($clase['hora_inicio'] && $hora_actual < $clase['hora_fin']) {
            $tipo_asistencia = 'presente';
            }else {
                $hora_fin_mas_5 = date("H:i:s", strtotime($clase['hora_fin'] . " +5 minutes"));
                if (($clase['hora_inicio'] && $hora_actual <= $hora_fin_mas_5)) {
                    // Si llega después de la hora de inicio, es retraso
                    $tipo_asistencia = 'retraso';
                }
            }
            // Actualizar asistencia
            $sql = "UPDATE asistencia SET tipo = ? WHERE clases_id = ? AND estudiante_codigo = ?";
            $resultado = $this->db->update($sql, [$tipo_asistencia, $clase['id'], $estudiante['codigo']]);
            if ($resultado > 0) {
                $mensaje = $tipo_asistencia === 'presente' 
                    ? "Asistencia registrada correctamente a las $hora_actual"
                    : "Asistencia registrada como RETRASO a las $hora_actual";
                    
                return [
                    'success' => true, 
                    'mensaje' => $mensaje
                ];
            } else {
                return ['success' => false, 'mensaje' => 'Error al registrar asistencia'];
            }
            
        } catch (Exception $e) {
            error_log("Error al registrar asistencia: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error interno del sistema'];
        }
    }
    public function obtenerPorClase($clase_id)
    {
        try {
            $sql = "SELECT * FROM asistencia WHERE clases_id = ?";
            $asistencias = $this->db->fetchAll($sql, [$clase_id]);
            return $asistencias;
        } catch (Exception $e) {
            error_log("Error al obtener asistencias: " . $e->getMessage());
            return [];
        }
    }

    private function obtenerDatosEstudiante($usuarioId) 
    {
        $sql = "SELECT codigo, nombres, apellidos FROM estudiante WHERE usuario_id = ?";
        return $this->db->fetch($sql, [$usuarioId]);
    }
    public function crearAsistenciasParaClase($clase_id, $grupo_id)
    {
        try {
            // Obtener todos los estudiantes inscritos en el grupo
            $sql = "SELECT estudiante_codigo FROM inscribe WHERE grupo_id = ?";
            $estudiantes = $this->db->fetchAll($sql, [$grupo_id]);
            foreach ($estudiantes as $estudiante) {
                // Crear registro de asistencia con estado 'ausente' por defecto
                $sqlInsert = "INSERT INTO asistencia ( tipo, estudiante_codigo, clases_id) 
                             VALUES ('ausente', ?, ?)";
                $this->db->query($sqlInsert, [ $estudiante['estudiante_codigo'], $clase_id]);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al crear asistencias: " . $e->getMessage());
            return false;
        }
    }

}