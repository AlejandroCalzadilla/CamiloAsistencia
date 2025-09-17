<?php
class GrupoModel
{
    private $id;
    private $nombre;
    private $descripcion;
    private $materia_id;
    private $profesor_id;
    private $creado_en;
    private $actualizado_en;
    private $db;

    public function __construct(Conexion $db){
        $this->db = $db;
    }
    // Método para actualizar datos
    public function actualizar($data)
    {
        
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
                // Si es admin, devolver sin datos
                if ($rol === 'admin') {
                    return [
                        'grupos' => [],
                        'rol' => 'admin',
                        'mensaje' => 'Los administradores no tienen grupos asignados',
                        'usuario_id' => $usuarioId
                    ];
                }
                $grupos = [];
                $datosAdicionales = [];

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
                g.capacidad,
                m.nombre as materia_nombre,
                COUNT(i.estudiante_codigo) as estudiantes_inscritos
            FROM grupo g
            INNER JOIN materia m ON g.materia_id = m.id
            LEFT JOIN inscribe i ON g.id = i.grupo_id
            WHERE g.profesor_codigo = ?
            GROUP BY g.id, g.nombre, g.capacidad, m.nombre
            ORDER BY g.nombre";
        return $this->db->fetchAll($sql, [$profesorCodigo]);
    }

    // Método para obtener grupos de un estudiante
    private function obtenerGruposEstudiante($estudianteCodigo)
    {
        $sql = "SELECT 
                g.id,
                g.nombre as grupo_nombre,
                g.capacidad,
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
            GROUP BY g.id, g.nombre, g.capacidad, m.nombre, p.nombres, p.apellidos
            ORDER BY g.nombre";

        return $this->db->fetchAll($sql, [$estudianteCodigo]);
    }

    // Método privado para actualizar timestamp
    private function updateTimestamp()
    {
        $this->actualizado_en = date('Y-m-d H:i:s');
    }

    // Método para validar datos
    public function validar()
    {
        $errores = [];

        if (empty($this->nombre)) {
            $errores[] = 'El nombre del grupo es obligatorio';
        }

        if (empty($this->usuario_id)) {
            $errores[] = 'El usuario asociado es obligatorio';
        }

        return $errores;
    }
}