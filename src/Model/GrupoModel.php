<?php
class GrupoModel
{
    private $id;
    private $nombre;
    private $capacidad_maxima;
    private $capacidad_actual;
    private $materia_id;
    private $profesor_codigo;
    private $db;

    public function __construct(Conexion $db){
        $this->db = $db;
    }

    public function crear($data)
    {
        try {
            $sql = "INSERT INTO grupo (nombre, capacidad_maxima, capacidad_actual, materia_id, profesor_codigo) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $params = [
                $data['nombre'],
                $data['capacidad_maxima'] ?? 100,
                $data['capacidad_actual'] ?? 0,
                $data['materia_id'],
                $data['profesor_codigo']
            ];

            $id = $this->db->insert($sql, $params);
            
            if ($id) {
                return [
                    'success' => true,
                    'message' => 'Grupo creado exitosamente',
                    'id' => $id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear el grupo'
                ];
            }
        } catch (Exception $e) {
            error_log("Error en GrupoModel::crear: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear el grupo: ' . $e->getMessage()
            ];
        }
    }

    // Método para actualizar datos
    public function actualizar($id, $data)
    {
        try {
            $sql = "UPDATE grupo SET 
                    nombre = ?, 
                    capacidad_maxima = ?, 
                    capacidad_actual = ?, 
                    materia_id = ?, 
                    profesor_codigo = ?
                    WHERE id = ?";
            
            $params = [
                $data['nombre'],
                $data['capacidad_maxima'],
                $data['capacidad_actual'],
                $data['materia_id'],
                $data['profesor_codigo'],
                $id
            ];

            $filasAfectadas = $this->db->update($sql, $params);
            
            if ($filasAfectadas > 0) {
                return [
                    'success' => true,
                    'message' => 'Grupo actualizado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se encontró el grupo o no se realizaron cambios'
                ];
            }
        } catch (Exception $e) {
            error_log("Error en GrupoModel::actualizar: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al actualizar el grupo: ' . $e->getMessage()
            ];
        }
    }

    // Método para eliminar un grupo
    public function eliminar($id)
    {
        try {
            // Verificar si hay inscripciones en el grupo
            $sqlCheck = "SELECT COUNT(*) as total FROM inscribe WHERE grupo_id = ?";
            $inscripciones = $this->db->fetch($sqlCheck, [$id]);
            
            if ($inscripciones['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar el grupo porque tiene estudiantes inscritos'
                ];
            }

            $sql = "DELETE FROM grupo WHERE id = ?";
            $filasAfectadas = $this->db->delete($sql, [$id]);
            
            if ($filasAfectadas > 0) {
                return [
                    'success' => true,
                    'message' => 'Grupo eliminado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se encontró el grupo'
                ];
            }
        } catch (Exception $e) {
            error_log("Error en GrupoModel::eliminar: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al eliminar el grupo: ' . $e->getMessage()
            ];
        }
    }

    // Método para obtener un grupo por ID
    public function obtenerPorId($id)
    {
        try {
            $sql = "SELECT 
                        g.id,
                        g.nombre,
                        g.capacidad_maxima,
                        g.capacidad_actual,
                        g.materia_id,
                        g.profesor_codigo,
                        m.nombre as materia_nombre,
                        p.nombres as profesor_nombres,
                        p.apellidos as profesor_apellidos
                    FROM grupo g
                    INNER JOIN materia m ON g.materia_id = m.id
                    INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                    WHERE g.id = ?";
            
            return $this->db->fetch($sql, [$id]);
        } catch (Exception $e) {
            error_log("Error en GrupoModel::obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    // Método para obtener todos los datos

    // Método para obtener todos los datos
    public function mostrar()
    {
        session_start();
        $usuarioData = $_SESSION['usuario_logueado'];
        if (isset($usuarioData)) {
            $usuarioId = $usuarioData['id'];
            $rol = $usuarioData['rol'] ?? 'sin_rol';
            try {
                $grupos = [];
                $datosAdicionales = [];

                // Si es admin, devolver todos los grupos y estudiantes
                if ($rol === 'admin') {
                    $grupos = $this->obtenerTodosLosGrupos();
                    $estudiantes = $this->obtenerTodosLosEstudiantes();
                    return [
                        'grupos' => $grupos,
                        'estudiantes' => $estudiantes,
                        'rol' => 'admin',
                        'mensaje' => 'Mostrando todos los grupos del sistema',
                        'usuario_id' => $usuarioId
                    ];
                }

                // Buscar grupos según el rol
                if ($rol === 'profesor') {
                    $datosProfesor = $this->obtenerDatosProfesor($usuarioId);
                    if ($datosProfesor) {
                        $grupos = $this->obtenerGruposProfesor($datosProfesor['codigo']);
                        $datosAdicionales = $datosProfesor;
                    }
                } else if ($rol === 'estudiante') {
                    $datosEstudiante = $this->obtenerDatosEstudiante($usuarioId);
                    if ($datosEstudiante) {
                        $grupos = $this->obtenerGruposEstudiante($datosEstudiante['codigo']);
                        $datosAdicionales = $datosEstudiante;
                    }
                }

                return array_merge([
                    'grupos' => $grupos,
                    'rol' => $rol,
                    'usuario_id' => $usuarioId
                ], $datosAdicionales);

            } catch (Exception $e) {
                error_log("Error en GrupoModel::mostrar: " . $e->getMessage());
                return [
                    'grupos' => [],
                    'rol' => 'error',
                    'mensaje' => 'Error al consultar grupos'
                ];
            }
        }
        return [
            'grupos' => [],
            'rol' => 'no_logueado',
            'mensaje' => 'Usuario no logueado'
        ];
    }

    // Método para determinar si el usuario es profesor o estudiante
    private function obtenerDatosProfesor($usuarioId)
    {
        $sql = "SELECT codigo, nombres, apellidos FROM profesor WHERE usuario_id = ?";
        return $this->db->fetch($sql, [$usuarioId]);
    }

    // Método para obtener datos del estudiante
    private function obtenerDatosEstudiante($usuarioId)
    {
        $sql = "SELECT codigo, nombres, apellidos FROM estudiante WHERE usuario_id = ?";
        return $this->db->fetch($sql, [$usuarioId]);
    }

    // Método para obtener grupos de un profesor
    private function obtenerGruposProfesor($profesorCodigo)
    {
        $sql = "SELECT 
                g.id,
                g.nombre as grupo_nombre,
                g.capacidad_maxima,
                g.capacidad_actual,
                m.nombre as materia_nombre,
                COUNT(i.estudiante_codigo) as estudiantes_inscritos
            FROM grupo g
            INNER JOIN materia m ON g.materia_id = m.id
            LEFT JOIN inscribe i ON g.id = i.grupo_id
            WHERE g.profesor_codigo = ?
            GROUP BY g.id, g.nombre, g.capacidad_maxima, g.capacidad_actual, m.nombre
            ORDER BY g.nombre";
        return $this->db->fetchAll($sql, [$profesorCodigo]);
    }

    // Método para obtener grupos de un estudiante
    private function obtenerGruposEstudiante($estudianteCodigo)
    {
        $sql = "SELECT 
                g.id,
                g.nombre as grupo_nombre,
                g.capacidad_maxima,
                g.capacidad_actual,
                m.nombre as materia_nombre,
                p.nombres as profesor_nombres,
                p.apellidos as profesor_apellidos,
                COUNT(i2.estudiante_codigo) as estudiantes_inscritos
            FROM inscribe i
            INNER JOIN grupo g ON i.grupo_id = g.id
            INNER JOIN materia m ON g.materia_id = m.id
            INNER JOIN profesor p ON g.profesor_codigo = p.codigo
            LEFT JOIN inscribe i2 ON g.id = i2.grupo_id
            WHERE i.estudiante_codigo = ?
            GROUP BY g.id, g.nombre, g.capacidad_maxima, g.capacidad_actual, m.nombre, p.nombres, p.apellidos
            ORDER BY g.nombre";

        return $this->db->fetchAll($sql, [$estudianteCodigo]);
    }

    // Método para obtener todos los grupos (para admin)
    private function obtenerTodosLosGrupos()
    {
        $sql = "SELECT 
                    g.id,
                    g.nombre as grupo_nombre,
                    g.capacidad_maxima,
                    g.capacidad_actual,
                    m.nombre as materia_nombre,
                    p.nombres as profesor_nombres,
                    p.apellidos as profesor_apellidos,
                    p.codigo as profesor_codigo,
                    COUNT(i.estudiante_codigo) as estudiantes_inscritos
                FROM grupo g
                INNER JOIN materia m ON g.materia_id = m.id
                INNER JOIN profesor p ON g.profesor_codigo = p.codigo
                LEFT JOIN inscribe i ON g.id = i.grupo_id
                GROUP BY g.id, g.nombre, g.capacidad_maxima, g.capacidad_actual, 
                         m.nombre, p.nombres, p.apellidos, p.codigo
                ORDER BY g.nombre";
        return $this->db->fetchAll($sql);
    }

    // Método para obtener todos los grupos con información completa (para listados)
    public function listarTodos()
    {
        try {
            return $this->obtenerTodosLosGrupos();
        } catch (Exception $e) {
            error_log("Error en GrupoModel::listarTodos: " . $e->getMessage());
            return [];
        }
    }

    // Método para obtener profesores disponibles
    public function obtenerProfesores()
    {
        try {
            $sql = "SELECT codigo, nombres, apellidos FROM profesor ORDER BY nombres, apellidos";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            error_log("Error en GrupoModel::obtenerProfesores: " . $e->getMessage());
            return [];
        }
    }

    // Método para obtener materias disponibles
    public function obtenerMaterias()
    {
        try {
            $sql = "SELECT id, nombre FROM materia ORDER BY nombre";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            error_log("Error en GrupoModel::obtenerMaterias: " . $e->getMessage());
            return [];
        }
    }

    // Método para obtener todos los estudiantes disponibles
    public function obtenerTodosLosEstudiantes()
    {
        try {
            $sql = "SELECT codigo, nombres, apellidos, ci, genero, estado FROM estudiante ORDER BY apellidos, nombres";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            error_log("Error en GrupoModel::obtenerTodosLosEstudiantes: " . $e->getMessage());
            return [];
        }
    }

    // Método para obtener las inscripciones de un grupo específico
    public function obtenerInscripcionesGrupo($grupo_id)
    {
        try {
            $sql = "SELECT 
                        i.estudiante_codigo,
                        i.fecha_inscripcion,
                        e.nombres,
                        e.apellidos,
                        e.ci,
                        e.estado
                    FROM inscribe i
                    INNER JOIN estudiante e ON i.estudiante_codigo = e.codigo
                    WHERE i.grupo_id = ?
                    ORDER BY e.apellidos, e.nombres";
            return $this->db->fetchAll($sql, [$grupo_id]);
        } catch (Exception $e) {
            error_log("Error en GrupoModel::obtenerInscripcionesGrupo: " . $e->getMessage());
            return [];
        }
    }

    // Método para actualizar la capacidad actual del grupo
    public function actualizarCapacidadActual($grupo_id)
    {
        try {
            $sql = "UPDATE grupo SET capacidad_actual = (
                        SELECT COUNT(*) FROM inscribe WHERE grupo_id = ?
                    ) WHERE id = ?";
            return $this->db->update($sql, [$grupo_id, $grupo_id]);
        } catch (Exception $e) {
            error_log("Error en GrupoModel::actualizarCapacidadActual: " . $e->getMessage());
            return false;
        }
    }

    // Método para validar datos
    public function validar($data)
    {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores[] = 'El nombre del grupo es obligatorio';
        }

        if (empty($data['materia_id'])) {
            $errores[] = 'La materia es obligatoria';
        }

        if (empty($data['profesor_codigo'])) {
            $errores[] = 'El profesor es obligatorio';
        }

        if (isset($data['capacidad_maxima']) && $data['capacidad_maxima'] < 1) {
            $errores[] = 'La capacidad máxima debe ser mayor a 0';
        }

        return $errores;
    }
}