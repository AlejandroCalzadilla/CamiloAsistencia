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

    public function __construct()
    {
        $this->db = new Conexion();
    }

    public function mostrar()
    {
        session_start();
        $usuarioData = $_SESSION['usuario_logueado'];

        if (!isset($usuarioData)) {
            return [
                'grupos' => [],
                'profesores' => [],
                'materias' => [],
                'estudiantes' => [],
                'todos_grupos' => [],
                'rol' => 'no_logueado',
                'mensaje' => 'Usuario no logueado'
            ];
        }

        $usuarioId = $usuarioData['id'];
        $rol = $usuarioData['rol'] ?? 'sin_rol';
        $resultado = [
            'rol' => $rol,
            'usuario_id' => $usuarioId,
            'grupos' => [],
            'profesores' => [],
            'materias' => [],
            'estudiantes' => [],
            'todos_grupos' => [],
            'inscripciones' => []
        ];

        // 1. PROFESORES (siempre necesarios para formularios)
        $sqlProfesores = "SELECT codigo, nombres, apellidos, usuario_id FROM profesor ORDER BY nombres, apellidos";
        $resultado['profesores'] = $this->db->fetchAll($sqlProfesores);

        // 2. MATERIAS (siempre necesarias para formularios)
        $sqlMaterias = "SELECT id, nombre FROM materia ORDER BY nombre";
        $resultado['materias'] = $this->db->fetchAll($sqlMaterias);

        // 3. ESTUDIANTES (siempre necesarios para admin e inscripciones)
        $sqlEstudiantes = "SELECT codigo, nombres, apellidos, ci, genero, estado, usuario_id FROM estudiante ORDER BY apellidos, nombres";
        $resultado['estudiantes'] = $this->db->fetchAll($sqlEstudiantes);

        // 4. TODOS LOS GRUPOS CON INFORMACIÓN COMPLETA
        $sqlTodosGrupos = "SELECT 
                g.id,
                g.nombre as grupo_nombre,
                g.capacidad_maxima,
                g.capacidad_actual, 
                g.profesor_codigo,  -- ← ESTE CAMPO FALTABA
                m.nombre as materia_nombre,
                p.nombres as profesor_nombres,
                COUNT(i.estudiante_codigo) as estudiantes_inscritos
            FROM grupo g
            INNER JOIN materia m ON g.materia_id = m.id
            INNER JOIN profesor p ON g.profesor_codigo = p.codigo
            LEFT JOIN inscribe i ON g.id = i.grupo_id
            GROUP BY g.id, g.nombre, g.capacidad_maxima, g.capacidad_actual, g.materia_id,
                     m.nombre, p.nombres, g.profesor_codigo  
            ORDER BY g.nombre";
        $todosLosGrupos = $this->db->fetchAll($sqlTodosGrupos);

        $resultado['todos_grupos'] = $todosLosGrupos;

        $sqlInscripciones = "SELECT 
                i.grupo_id,
                i.estudiante_codigo,
                i.fecha_inscripcion,
                e.nombres as estudiante_nombres,
                e.apellidos as estudiante_apellidos,
                e.ci as estudiante_ci,
                e.estado as estudiante_estado
            FROM inscribe i
            INNER JOIN estudiante e ON i.estudiante_codigo = e.codigo
            ORDER BY i.grupo_id, e.apellidos, e.nombres";

        $resultado['inscripciones'] = $this->db->fetchAll($sqlInscripciones);


        if ($rol === 'admin') {
            // Admin ve todos los grupos
            $resultado['grupos'] = $todosLosGrupos;

            // Obtener datos adicionales del admin si existe
            $sqlAdmin = "SELECT nombre FROM usuario WHERE id = ?";
            $adminData = $this->db->fetch($sqlAdmin, [$usuarioId]);
            if ($adminData) {
                $resultado = array_merge($resultado, $adminData);
            }

        } elseif ($rol === 'profesor') {
          
            $sqlProfesor = "SELECT codigo, nombres, apellidos FROM profesor WHERE usuario_id = ?";
            $datosProfesor = $this->db->fetch($sqlProfesor, [$usuarioId]);
            if ($datosProfesor) {
                $resultado = array_merge($resultado, $datosProfesor);
                // Filtrar solo grupos del profesor
                $resultado['grupos'] = array_filter($todosLosGrupos, function ($grupo) use ($datosProfesor) {
                    return $grupo['profesor_codigo'] === $datosProfesor['codigo'];
                });
               
            }

        } elseif ($rol === 'estudiante') {
            // Obtener datos del estudiante
            $sqlEstudiante = "SELECT codigo, nombres, apellidos, ci FROM estudiante WHERE usuario_id = ?";
            $datosEstudiante = $this->db->fetch($sqlEstudiante, [$usuarioId]);
            if ($datosEstudiante) {
                $resultado = array_merge($resultado, $datosEstudiante);
                $gruposEstudiante = [];
                foreach ($todosLosGrupos as $grupo) {
                    // Verificar si el estudiante está inscrito en este grupo
                    $inscrito = array_filter($resultado['inscripciones'], function ($inscripcion) use ($grupo, $datosEstudiante) {
                        return $inscripcion['grupo_id'] == $grupo['id'] &&
                            $inscripcion['estudiante_codigo'] === $datosEstudiante['codigo'];
                    });
                    if (!empty($inscrito)) {
                        $gruposEstudiante[] = $grupo;
                    }
                }
                $resultado['grupos'] = $gruposEstudiante;
            }
        }
        return $resultado;
    }

    public function crear($data)
    {
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


}