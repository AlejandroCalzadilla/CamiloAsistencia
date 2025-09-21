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
                        a.fecha as fecha_asistencia,
                        a.hora_inicio,
                        a.hora_fin,
                        a.tipo,
                        c.id as clase_id,
                        c.dia as dia_clase,
                        c.fecha as fecha_creacion_clase,
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

                $sql .= " ORDER BY c.fecha DESC, a.fecha DESC, e.apellidos, e.nombres";

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
                        a.fecha as fecha_asistencia,
                        a.hora_inicio,
                        a.hora_fin,
                        a.tipo,
                        c.id as clase_id,
                        c.dia as dia_clase,
                        c.fecha as fecha_creacion_clase,
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

                $sql .= " ORDER BY c.fecha DESC, a.fecha DESC";

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

            date_default_timezone_set('America/La_Paz'); // Para Bolivia
            $sqlEstudiante = "SELECT codigo FROM estudiante WHERE usuario_id = ?";
            $estudiante = $this->db->fetch($sqlEstudiante, [$usuario_id]);

            if (!$estudiante) {
                return [
                    'success' => false,
                    'mensaje' => 'No se encontró información del estudiante'
                ];
            }

            $estudiante_codigo = $estudiante['codigo'];

            // 2. Verificar que el código QR sea válido para esta clase
            $sqlClase = "SELECT c.id, c.qr, c.grupo_id, 
                        a.hora_inicio, a.hora_fin, a.tipo, a.id as asistencia_id
                 FROM clases c
                 INNER JOIN asistencia a ON c.id = a.clases_id
                 WHERE c.id = ? AND c.qr = ? AND a.estudiante_codigo = ?";

            $resultado = $this->db->fetch($sqlClase, [$clase_id, $codigo_verificacion, $estudiante_codigo]);

            if (!$resultado) {
                return [
                    'success' => false,
                    'mensaje' => 'Código QR inválido o no tienes asistencia registrada para esta clase'
                ];
            }

            // 3. Verificar si ya marcó asistencia
            if ($resultado['tipo'] !== 'ausente') {
                return [
                    'success' => false,
                    'mensaje' => 'Ya has marcado tu asistencia para esta clase como: ' . $resultado['tipo']
                ];
            }

            // 4. Obtener hora actual
            $hora_actual = date('H:i:s');
            $hora_inicio_clase = $resultado['hora_inicio'];
            $hora_fin_clase = $resultado['hora_fin'];

            // 5. Convertir horas a timestamp para comparación
            $timestamp_actual = strtotime($hora_actual);
            $timestamp_inicio = strtotime($hora_inicio_clase);
            $timestamp_fin = strtotime($hora_fin_clase);

            // 6. Calcular límite de retraso (5 minutos después del inicio)
            $timestamp_limite_retraso = $timestamp_inicio + (5 * 60); // 5 minutos en segundos

            // 7. Determinar el tipo de asistencia
            $nuevo_tipo = 'ausente'; // Por defecto permanece ausente

            if ($timestamp_actual >= $timestamp_inicio && $timestamp_actual <= $timestamp_fin) {
                // Está dentro del horario de clase
                $nuevo_tipo = 'presente';
            } elseif ($timestamp_actual > $timestamp_inicio && $timestamp_actual <= $timestamp_limite_retraso) {
                // Llegó tarde pero dentro del margen de 5 minutos
                $nuevo_tipo = 'retraso';
            } elseif ($timestamp_actual < $timestamp_inicio) {
                // Llegó antes de tiempo (se considera presente)
                $nuevo_tipo = 'presente';
            }
            // Si llega más de 5 minutos tarde o después del fin de clase, permanece ausente

            // 8. Solo actualizar si no es ausente
            if ($nuevo_tipo !== 'ausente') {
                // Solo actualizar el tipo, no las horas
                $sqlUpdate = "UPDATE asistencia 
                             SET tipo = ?
                             WHERE id = ?";

                $filasAfectadas = $this->db->update($sqlUpdate, [
                    $nuevo_tipo,
                    $resultado['asistencia_id']
                ]);

                if ($filasAfectadas > 0) {
                    $mensaje_tipo = $nuevo_tipo === 'presente' ? 'presente' : 'presente con retraso';
                    return [
                        'success' => true,
                        'mensaje' => "Asistencia marcada como: $mensaje_tipo",
                        'tipo' => $nuevo_tipo,
                        'hora_marcada' => $hora_actual, // Solo para referencia, no se guarda
                        'estudiante_codigo' => $estudiante_codigo
                    ];
                } else {
                    return [
                        'success' => false,
                        'mensaje' => 'Error al actualizar la asistencia'
                    ];
                }
            } else {
                // Llegó muy tarde, no se actualiza
                $minutos_retraso = round(($timestamp_actual - $timestamp_inicio) / 60);
                return [
                    'success' => false,
                    'mensaje' => "Llegaste demasiado tarde ($minutos_retraso minutos de retraso). Solo se acepta hasta 5 minutos de retraso.",
                    'tipo' => 'ausente'
                ];
            }

        } catch (Exception $e) {
            error_log("Error al marcar presente: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error interno del sistema'
            ];
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

    public function crearAsistenciasParaClase($clase_id, $grupo_id, $hora_inicio, $hora_fin)
    {
        try {
            // Obtener todos los estudiantes inscritos en el grupo
            $sql = "SELECT estudiante_codigo FROM inscribe WHERE grupo_id = ?";
            $estudiantes = $this->db->fetchAll($sql, [$grupo_id]);
            foreach ($estudiantes as $estudiante) {
                // Crear registro de asistencia con estado 'ausente' por defecto
                $sqlInsert = "INSERT INTO asistencia (fecha, hora_inicio, hora_fin, tipo, estudiante_codigo, clases_id) 
                             VALUES (CURRENT_DATE, ?, ?, 'ausente', ?, ?)";
                $this->db->query($sqlInsert, [$hora_inicio, $hora_fin, $estudiante['estudiante_codigo'], $clase_id]);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al crear asistencias: " . $e->getMessage());
            return false;
        }
    }

}