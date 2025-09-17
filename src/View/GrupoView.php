<?php
class GrupoView
{
    private $model;
    private $usuario;
    private $message = '';
    private $messageType = '';

    public function __construct(GrupoModel $model = null)
    {
        $this->model = $model;
    }

    public function showSuccessMessage($message)
    {
        $this->message = $message;
        $this->messageType = 'success';
    }

    public function showErrorMessage($message)
    {
        $this->message = $message;
        $this->messageType = 'error';
    }

    public function render()
    {
        if (!$this->model) {
            echo "<p style='color: red;'>Error: No se ha configurado el modelo de grupo</p>";
            return;
        }

        $grupo = $this->model->mostrar();

        echo "<!DOCTYPE html>";
        echo "<html lang='es'><head><title>Grupos - Sistema de Asistencia</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: #f5f5f5; position: relative; }";
        echo ".container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo ".header { text-align: center; color: #2c3e50; margin-bottom: 30px; }";
        echo ".success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".groups-section { margin: 30px 0; }";
        echo ".group-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 15px 0; }";
        echo ".group-card h4 { color: #495057; margin: 0 0 10px 0; }";
        echo ".group-meta { color: #6c757d; font-size: 0.9em; margin: 5px 0; }";
        echo ".btn { background: #007bff; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; font-size: 0.9em; }";
        echo ".btn:hover { background: #0056b3; }";
        echo ".btn-success { background: #28a745; }";
        echo ".btn-success:hover { background: #218838; }";
        echo ".btn-warning { background: #ffc107; color: #212529; }";
        echo ".btn-warning:hover { background: #e0a800; }";
        echo ".btn-danger { background: #dc3545; }";
        echo ".btn-danger:hover { background: #c82333; }";
        echo ".btn-secondary { background: #6c757d; }";
        echo ".btn-secondary:hover { background: #5a6268; }";
        echo ".actions { text-align: center; margin: 30px 0; }";
        echo ".no-groups { text-align: center; color: #6c757d; padding: 40px; background: #f8f9fa; border-radius: 8px; }";
        echo ".role-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; font-weight: bold; }";
        echo ".role-profesor { background: #d4edda; color: #155724; }";
        echo ".role-estudiante { background: #cce7ff; color: #004085; }";
        echo ".role-admin { background: #f8d7da; color: #721c24; }";

        // Estilos del dropdown de usuario
        echo ".user-dropdown { position: fixed; top: 20px; left: 20px; z-index: 1000; }";
        echo ".user-avatar { width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 3px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.3); cursor: pointer; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px; transition: transform 0.2s; }";
        echo ".user-avatar:hover { transform: scale(1.1); }";
        echo ".dropdown-content { position: absolute; top: 60px; left: 0; background: white; min-width: 280px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); border-radius: 8px; display: none; border: 1px solid #ddd; }";
        echo ".dropdown-content.show { display: block; animation: slideDown 0.3s ease; }";
        echo "@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }";
        echo ".dropdown-header { background: #f8f9fa; padding: 15px; border-bottom: 1px solid #dee2e6; border-radius: 8px 8px 0 0; }";
        echo ".dropdown-header h4 { margin: 0; color: #495057; font-size: 16px; }";
        echo ".dropdown-header p { margin: 5px 0 0 0; color: #6c757d; font-size: 14px; }";
        echo ".dropdown-body { padding: 10px; }";
        echo ".dropdown-item { display: block; padding: 10px 15px; text-decoration: none; color: #495057; border-radius: 4px; margin: 2px 0; transition: background 0.2s; }";
        echo ".dropdown-item:hover { background: #f8f9fa; color: #007bff; }";
        echo ".dropdown-item i { margin-right: 8px; width: 16px; }";
        echo ".dropdown-divider { border-top: 1px solid #dee2e6; margin: 8px 0; }";
        echo "</style>";

        // JavaScript para el dropdown
        echo "<script>";
        echo "function toggleUserDropdown() {";
        echo "  var dropdown = document.getElementById('userDropdown');";
        echo "  dropdown.classList.toggle('show');";
        echo "}";
        echo "window.onclick = function(event) {";
        echo "  if (!event.target.matches('.user-avatar')) {";
        echo "    var dropdown = document.getElementById('userDropdown');";
        echo "    if (dropdown.classList.contains('show')) {";
        echo "      dropdown.classList.remove('show');";
        echo "    }";
        echo "  }";
        echo "}";
        echo "</script>";

        echo "</head><body>";


        // Dropdown de usuario
        if ($grupo) {
            $iniciales = $this->getIniciales($grupo);
            $rol = $grupo['rol'] ?? 'sin_rol';
            $nombre = isset($grupo['nombres']) && isset($grupo['apellidos'])
                ? $grupo['nombres'] . ' ' . $grupo['apellidos']
                : 'Usuario';

            echo "<div class='user-dropdown'>";
            echo "<div class='user-avatar' onclick='toggleUserDropdown()'>$iniciales</div>";
            echo "<div id='userDropdown' class='dropdown-content'>";

            echo "<div class='dropdown-header'>";
            echo "<h4>$nombre</h4>";
            echo "<p><span class='role-badge role-$rol'>" . ucfirst($rol) . "</span></p>";
            if (isset($grupo['codigo'])) {
                echo "<p>Código: {$grupo['codigo']}</p>";
            }
            echo "</div>";

            echo "<div class='dropdown-body'>";

            if ($grupo['rol'] === 'profesor') {
                // Solo opciones generales para profesores
                echo "<a href='mi-perfil.php' class='dropdown-item'>👤 Mi Perfil</a>";
                echo "<a href='crear-grupo.php' class='dropdown-item'>➕ Crear Grupo</a>";
                echo "<a href='mis-estudiantes.php' class='dropdown-item'>👥 Mis Estudiantes</a>";
                echo "<a href='reportes.php' class='dropdown-item'>📊 Reportes</a>";

            } elseif ($grupo['rol'] === 'estudiante') {
                // Solo opciones generales para estudiantes
                echo "<a href='mi-perfil.php' class='dropdown-item'>👤 Mi Perfil</a>";
                echo "<a href='buscar-grupos.php' class='dropdown-item'>🔍 Buscar Grupos</a>";
                echo "<a href='mis-asistencias.php' class='dropdown-item'>📊 Mis Asistencias</a>";
                echo "<a href='horarios.php' class='dropdown-item'>🕒 Mis Horarios</a>";

            } elseif ($grupo['rol'] === 'admin') {
                echo "<a href='admin-dashboard.php' class='dropdown-item'>📊 Panel de Control</a>";
                echo "<a href='manage-users.php' class='dropdown-item'>👥 Gestionar Usuarios</a>";
                echo "<a href='manage-groups.php' class='dropdown-item'>📚 Gestionar Grupos</a>";
                echo "<a href='system-reports.php' class='dropdown-item'>📈 Reportes del Sistema</a>";
            }

            echo "<div class='dropdown-divider'></div>";
            echo "<a href='configuracion.php' class='dropdown-item'>⚙️ Configuración</a>";
            echo "<a href='ayuda.php' class='dropdown-item'>❓ Ayuda</a>";
            echo "<div class='dropdown-divider'></div>";
            echo "<a href='logout.php' class='dropdown-item' style='color: #dc3545;'>🚪 Cerrar Sesión</a>";

            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }

        echo "<div class='container'>";
        echo "<div class='header'>";
        echo "<h1>🎓 Mis Grupos</h1>";
        echo "<h3>Sistema de Asistencia Académica</h3>";
        echo "</div>";

        // Mostrar mensajes
        if ($this->message) {
            $class = $this->messageType === 'success' ? 'success' : 'error';
            echo "<div class='$class'>{$this->message}</div>";
        }
        // Mostrar grupos según el rol
        echo "<div class='groups-section'>";

        if ($grupo['rol'] === 'admin') {
            echo "<div class='no-groups'>";
            echo "<h3>👑 Panel de Administrador</h3>";
            echo "<p>{$grupo['mensaje']}</p>";
            echo "<div class='actions'>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='MateriasClicked'>";
            echo "<button type='submit' class='btn btn-success'>📚 Materias</button>";
            echo "</form>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='EstudiantesClicked'>";
            echo "<button type='submit' class='btn btn-success'>📚 Estudiantes</button>";
            echo "</form>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='InscripcionClicked'>";
            echo "<button type='submit' class='btn btn-success'>📚 Inscripciones</button>";
            echo "</form>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='UsuariosClicked'>";
            echo "<button type='submit' class='btn btn-success'>📚 Usuarios</button>";
            echo "</form>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='ProfesoresClicked'>";
            echo "<button type='submit' class='btn btn-success'>📚 Profesores</button>";
            echo "</form>";


            echo "</div>";
            echo "</div>";

        } elseif (empty($grupo['grupos'])) {
            echo "<div class='no-groups'>";
            echo "<h3>📚 Sin grupos asignados</h3>";
            if ($grupo['rol'] === 'profesor') {
                echo "<p>No tienes grupos asignados como profesor.</p>";
            } else {
                echo "<p>No estás inscrito en ningún grupo.</p>";
            }
            echo "</div>";

        } else {
            // Mostrar grupos
            echo "<h3>📚 " . ($grupo['rol'] === 'profesor' ? 'Grupos que Impartes' : 'Grupos Inscritos') . " (" . count($grupo['grupos']) . ")</h3>";

            foreach ($grupo['grupos'] as $index => $grupoItem) {
                echo "<div class='group-card'>";
                echo "<h4>📖 {$grupoItem['grupo_nombre']}</h4>";
                echo "<div class='group-meta'>";
                echo "<p><strong>Materia:</strong> {$grupoItem['materia_nombre']}</p>";
                echo "<p><strong>Capacidad:</strong> {$grupoItem['capacidad']} estudiantes</p>";
                echo "<p><strong>Inscritos:</strong> {$grupoItem['estudiantes_inscritos']} estudiantes</p>";

                // Información específica según el rol
                if ($grupo['rol'] === 'estudiante' && isset($grupoItem['profesor_nombres'])) {
                    echo "<p><strong>Profesor:</strong> {$grupoItem['profesor_nombres']} {$grupoItem['profesor_apellidos']}</p>";
                }

                echo "</div>";

                // Botones según el rol
                echo "<div style='margin-top: 15px;'>";

                if ($grupo['rol'] === 'profesor') {
                    // Botones para profesores
                    echo "<a href='ver-grupo.php?id={$grupoItem['id']}' class='btn'>👁️ Ver Detalles</a>";
                    echo "<a href='lista-asistencia.php?grupo_id={$grupoItem['id']}' class='btn btn-success'>📋 Tomar Asistencia</a>";

                    // Formulario para ir a clases
                    echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
                    echo "<input type='hidden' name='evento' value='ver_clases'>";
                    echo "<input type='hidden' name='grupo_id' value='{$grupoItem['id']}'>";
                    echo "<button type='submit' class='btn btn-warning'>📚 Ver Clases</button>";
                    echo "</form>";

                    echo "<a href='editar-grupo.php?id={$grupoItem['id']}' class='btn btn-warning'>✏️ Editar</a>";
                    echo "<a href='eliminar-grupo.php?id={$grupoItem['id']}' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de eliminar este grupo?\")'>🗑️ Eliminar</a>";

                } elseif ($grupo['rol'] === 'estudiante') {
                    // Botones para estudiantes (solo lectura)


                    // Formulario para ir a clases
                    echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
                    echo "<input type='hidden' name='evento' value='ver_clases'>";
                    echo "<input type='hidden' name='grupo_id' value='{$grupoItem['id']}'>";
                    echo "<button type='submit' class='btn btn-success'>📚 Ver Clases</button>";
                    echo "</form>";

                    echo "<a href='mis-asistencias.php?grupo_id={$grupoItem['id']}' class='btn btn-success'>📊 Mis Asistencias</a>";
                }

                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }

        echo "</div>";

        // Acciones globales según el rol (ahora simplificadas)
        echo "<div class='actions'>";

        if ($grupo['rol'] === 'profesor') {
            echo "<a href='crear-grupo.php' class='btn btn-success'>➕ Crear Nuevo Grupo</a>";
        } elseif ($grupo['rol'] === 'estudiante') {

        }

        echo "</div>";

        echo "</div>";
        echo "</body></html>";
    }

    // Método auxiliar para obtener iniciales del usuario
    private function getIniciales($data)
    {
        if (isset($data['nombres']) && isset($data['apellidos'])) {
            $nombre = trim($data['nombres']);
            $apellido = trim($data['apellidos']);
            return strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
        }

        // Fallback basado en el rol
        $rol = $data['rol'] ?? 'U';
        return strtoupper(substr($rol, 0, 1));
    }
}