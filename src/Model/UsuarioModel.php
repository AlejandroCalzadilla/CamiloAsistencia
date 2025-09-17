<?php
require_once __DIR__ . '/../Conexion/Conexion.php';

class UsuarioModel {
    private $id;
    private $nombre;
    private $contrasena;

    private $creado_en;
    private $actualizado_en;
    private $db;

    public function __construct(Conexion $db) {
        $this->db = $db;
    }

    // Getters

    public function obtenerTodos() {
        $sql = "SELECT id, nombre, creado_en, actualizado_en FROM usuario ORDER BY nombre ASC";
        return $this->db->fetchAll($sql);
    }

    // Crear usuario
    public function crear($nombre, $contrasena) {
        $sql = "INSERT INTO usuario (nombre, contrasena, creado_en, actualizado_en) VALUES (?, ?, NOW(), NOW())";
        return $this->db->query($sql, [$nombre, $contrasena]);
    }

    // Editar usuario
    public function editar($id, $nombre, $contrasena = null) {
        if ($contrasena) {
            $sql = "UPDATE usuario SET nombre = ?, contrasena = ?, actualizado_en = NOW() WHERE id = ?";
            return $this->db->query($sql, [$nombre, $contrasena, $id]);
        } else {
            $sql = "UPDATE usuario SET nombre = ?, actualizado_en = NOW() WHERE id = ?";
            return $this->db->query($sql, [$nombre, $id]);
        }
    }

    // Eliminar usuario
    public function eliminar($id) {
        $sql = "DELETE FROM usuario WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
   
    // Método para validar login con consulta a la base de datos
    public function validarLogin($nombre, $contrasena) {
        try {
            // Buscar usuario en la base de datos
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
        } catch (Exception $e) {
            // En caso de error de conexión, log el error y retornar false
            error_log("Error en validarLogin: " . $e->getMessage());
            return null;
        }
    }



    private function verificarContrasena($contrasena, $hashAlmacenado) {
        return $contrasena === $hashAlmacenado;
    }

    // Método para obtener todos los datos (sin contraseña)
    public function mostrar() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'creado_en' => $this->creado_en,
            'actualizado_en' => $this->actualizado_en
        ];
    }



    private   function determinarRolUsuario($usuarioId)
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