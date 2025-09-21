<?php
require_once __DIR__ . '/../Conexion/Conexion.php';

class UsuarioModel
{
    private $id;
    private $nombre;
    private $contrasena;

    private $creado_en;
    private $actualizado_en;
    private $db;

    public function __construct(Conexion $db)
    {
        $this->db = $db;
    }

    // Getters

    public function obtenerTodos()
    {
        $sql = "SELECT id, nombre, creado_en, actualizado_en FROM usuario ORDER BY nombre ASC";
        return $this->db->fetchAll($sql);
    }

    // Crear usuario
    public function crear($nombre, $contrasena)
    {
        $sql = "INSERT INTO usuario (nombre, contrasena, creado_en, actualizado_en) VALUES (?, ?, NOW(), NOW())";
        return $this->db->query($sql, [$nombre, $contrasena]);
    }

    // Editar usuario
    public function editar($id, $nombre, $contrasena = null)
    {
        if ($contrasena) {
            $sql = "UPDATE usuario SET nombre = ?, contrasena = ?, actualizado_en = NOW() WHERE id = ?";
            return $this->db->query($sql, [$nombre, $contrasena, $id]);
        } else {
            $sql = "UPDATE usuario SET nombre = ?, actualizado_en = NOW() WHERE id = ?";
            return $this->db->query($sql, [$nombre, $id]);
        }
    }


    // Eliminar usuario
    public function eliminar($id)
    {
      
            $referencias = $this->verificarReferenciasUsuario($id);
            if (!empty($referencias)) {
                return [
                    'success' => false,
                    'mensaje' => 'No se puede eliminar el usuario porque tiene referencias en: ' . implode(', ', $referencias)
                ];
            }
            $sql = "DELETE FROM usuario WHERE id = ?";
            $this->db->query($sql, [$id]);
            return [
                'success' => true,
                'mensaje' => 'Usuario eliminado correctamente'
            ];
    }

    // Método auxiliar para verificar referencias
    private function verificarReferenciasUsuario($usuarioId)
    {
        $referencias = [];
        // Verificar si es profesor
        $sqlProfesor = "SELECT COUNT(*) as count FROM profesor WHERE usuario_id = ?";
        $profesor = $this->db->fetch($sqlProfesor, [$usuarioId]);
        if ($profesor && $profesor['count'] > 0) {
            $referencias[] = 'Profesor';
        }

        // Verificar si es estudiante
        $sqlEstudiante = "SELECT COUNT(*) as count FROM estudiante WHERE usuario_id = ?";
        $estudiante = $this->db->fetch($sqlEstudiante, [$usuarioId]);
        if ($estudiante && $estudiante['count'] > 0) {
            $referencias[] = 'Estudiante';
        }

        return $referencias;
    }



    // Método para validar login con consulta a la base de datos
    public function validarLogin($nombre, $contrasena)
    {
        $sql = "SELECT id, nombre, creado_en, actualizado_en 
                    FROM usuario 
                    WHERE nombre = ? AND contrasena = ? 
                    LIMIT 1";

        $usuario = $this->db->fetch($sql, [$nombre, $contrasena]);
        if ($usuario) {
            session_start();
            $rol = $this->determinarRolUsuario($usuario['id']);
            $_SESSION['usuario_logueado'] = $usuario + ['rol' => $rol];
            return $usuario;
        }
        return null;
    }

    private function determinarRolUsuario($usuarioId)
    {
        // Buscar en tabla profesor
        $sqlProfesor = "SELECT codigo, nombres, apellidos, 'profesor' as rol 
                    FROM profesor 
                    WHERE usuario_id = ?";

        $profesor = $this->db->fetch($sqlProfesor, [$usuarioId]);
        if ($profesor) {
            return "profesor";
        }
        // Buscar en tabla estudiante
        $sqlEstudiante = "SELECT codigo, nombres, apellidos, 'estudiante' as rol 
                      FROM estudiante 
                      WHERE usuario_id = ?";

        $estudiante = $this->db->fetch($sqlEstudiante, [$usuarioId]);
        if ($estudiante) {
            return "estudiante";
        }
        return "admin";
    }

}