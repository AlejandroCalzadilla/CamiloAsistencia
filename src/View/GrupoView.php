<?php
class GrupoView
{
    private $grupoModel;
    private $inscripcionModel;
    private $message = '';
    private $messageType = '';
    private $mostrarFormulario = false;
    private $tipoFormulario = 'crear';
    private $grupoIdEditar = null;

    public function __construct()
    {
        $this->grupoModel = new GrupoModel();
        $this->inscripcionModel = new InscripcionModel();
    }


    public function actualizar()
    {
        $data = $this->grupoModel->mostrar();
        $inscripciones = $this->inscripcionModel->mostrar();
        $this->render($data, $inscripciones);
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
    public function setMostrarFormulario($mostrar, $tipo = 'crear', $grupoId = null)
    {
        $this->mostrarFormulario = $mostrar;
        $this->tipoFormulario = $tipo;
        $this->grupoIdEditar = $grupoId;
    }


    public function render($data, $inscripciones)
    {
        $grupo = $data;
        $profesores = $data['profesores'] ?? [];
        $materias = $data['materias'] ?? [];
        $estudiantes = $data['estudiantes'] ?? [];
        $grupos = $data['grupos'] ?? [];

        echo "<!DOCTYPE html>";
        echo "<html lang='es'><head><title>Grupos - Sistema de Asistencia</title>";
        $this->renderCSS();
        echo "</head><body>";
        // Dropdown de usuario simplificado
        $this->renderUserDropdown($grupo);
        echo "<div class='container'>";
        echo "<div class='header'>";
        echo "<h1>🎓 Mis Grupos</h1>";
        echo "<h3>Sistema de Asistencia Académica</h3>";
        echo "</div>";
        // Mostrar mensajes
        if ($this->message) {
            echo "<script>";
            echo "console.log( 'mensaje' , " . json_encode($this->message) . ");";
            echo "</script>";
            $class = $this->messageType === 'success' ? 'success' : 'error';
            echo "<div class='$class'>{$this->message}</div>";
        }
        echo "<div class='groups-section'>";
        if ($grupo['rol'] === 'admin') {
            $this->renderAdminSection($profesores, $materias, $estudiantes, $grupos, $inscripciones);
        } elseif (empty($grupo['grupos'])) {
            $this->renderNoGroups($grupo['rol']);
        } else {
            $this->renderGroupsList($grupo);
        }
        echo "</div>";
        echo "</div>";
        echo "</body></html>";
    }



    private function renderUserDropdown($grupo)
    {
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
            echo "<a href='logout.php' class='dropdown-item' style='color: #dc3545;'>🚪 Cerrar Sesión</a>";
            echo "</div>";
            echo "</div>";
        }
    }

    private function renderAdminSection($profesores = [], $materias = [], $estudiantes = [], $grupos = [], $inscripciones = [])
    {
        echo "<div class='admin-section'>";
        echo "<h3>👑 Panel de Administrador - Gestión de Grupos</h3>";

        // Botón para mostrar formulario de creación
        if (!$this->mostrarFormulario) {
            echo "<div style='margin: 20px 0;'>";
            echo "<form method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='evento' value='mostrar_form_crear'>";
            echo "<button type='submit' class='btn btn-success'>➕ Crear Nuevo Grupo</button>";
            echo "</form>";
            echo "</div>";
        }

        if ($this->mostrarFormulario) {
            $this->renderFormularioGrupo($profesores, $materias, $estudiantes, $inscripciones);
        }

        // Lista de todos los grupos
        $this->renderListaTodosLosGrupos($grupos);

        // Botones de navegación
        $this->renderBotonesNavegacion();

        echo "</div>";
    }



    private function renderFormularioGrupo($profesores = [], $materias = [], $estudiantes = [], $inscripciones = [])
    {
        $esEdicion = $this->tipoFormulario === 'editar';
        $titulo = $esEdicion ? 'Editar Grupo' : 'Crear Nuevo Grupo';

        echo "<script>";
        echo "console.log( 'inscripciones' , " . json_encode($inscripciones) . ");";
        echo "</script>";
        $grupoData = null;
        $inscripcionesporgrupo = [];
        if ($esEdicion && $this->grupoIdEditar) {
            $grupoData = $this->grupoModel->obtenerPorId($this->grupoIdEditar);
            $inscripcionesporgrupo = array_filter($inscripciones, function ($inscripcion) {
                return $inscripcion['grupo_id'] == $this->grupoIdEditar;
            });
            $inscripcionesporgrupo = array_values($inscripcionesporgrupo);
            echo "<script>";
            echo "console.log( 'inscripcionesporid' , " . json_encode($inscripcionesporgrupo) . ");";
            echo "</script>";
        }

        echo "<div class='form-section'>";
        echo "<h4>$titulo</h4>";

        // ============ FORMULARIO PRINCIPAL (crear/actualizar) ============
        echo "<form method='POST'>";
        echo "<input type='hidden' name='evento' value='" . ($esEdicion ? 'actualizar_grupo' : 'crear_grupo') . "'>";

        if ($esEdicion) {
            echo "<input type='hidden' name='id' value='{$this->grupoIdEditar}'>";
            echo "<input type='hidden' name='capacidad_actual' value='" . count($inscripcionesporgrupo) . "'>";
        }

        echo "<div class='form-group'>";
        echo "<label>Nombre del Grupo:</label>";
        $valorNombre = $esEdicion && $grupoData ? $grupoData['nombre'] : '';
        echo "<input type='text' name='nombre' value='$valorNombre' required class='form-control'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label>Materia:</label>";
        echo "<select name='materia_id' required class='form-control'>";
        echo "<option value=''>Selecciona una materia</option>";
        foreach ($materias as $materia) {
            $selected = ($esEdicion && $grupoData && $grupoData['materia_id'] == $materia['id']) ? 'selected' : '';
            echo "<option value='{$materia['id']}' $selected>{$materia['nombre']}</option>";
        }
        echo "</select>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label>Profesor:</label>";
        echo "<select name='profesor_codigo' required class='form-control'>";
        echo "<option value=''>Selecciona un profesor</option>";
        foreach ($profesores as $profesor) {
            $selected = ($esEdicion && $grupoData && $grupoData['profesor_codigo'] == $profesor['codigo']) ? 'selected' : '';
            echo "<option value='{$profesor['codigo']}' $selected>{$profesor['nombres']} {$profesor['apellidos']}</option>";
        }
        echo "</select>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label>Capacidad Máxima:</label>";
        $valorCapacidad = $esEdicion && $grupoData ? $grupoData['capacidad_maxima'] : '100';
        echo "<input type='number' name='capacidad_maxima' value='$valorCapacidad' min='1' required class='form-control'>";
        echo "</div>";

        // BOTONES DEL FORMULARIO PRINCIPAL
        echo "<div style='margin-top: 20px;'>";
        echo "<button type='submit' class='btn btn-success'>💾 " . ($esEdicion ? 'Actualizar' : 'Crear') . "</button>";
        echo "</div>";
        echo "</form>"; // ← CERRAR FORMULARIO PRINCIPAL AQUÍ

        // ============ FORMULARIO SEPARADO PARA CANCELAR ============
        echo "<div style='margin-top: 10px;'>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='evento' value='cancelar_formulario'>";
        echo "<button type='submit' class='btn btn-secondary'>❌ Cancelar</button>";
        echo "</form>";
        echo "</div>";

        // ============ GESTIÓN DE INSCRIPCIONES (SOLO PARA EDICIÓN) ============
        if ($esEdicion) {
            $this->renderGestionInscripcionesSeparadas($inscripcionesporgrupo, $estudiantes);
        }

        echo "</div>";
    }


    private function renderGestionInscripcionesSeparadas($inscripciones, $estudiantes)
    {
        echo "<div style='margin-top: 30px; border-top: 2px solid #dee2e6; padding-top: 20px;'>";
        echo "<h5>👥 Gestión de Asisgnaciones</h5>";

        // ============ MOSTRAR LISTA DE INSCRITOS (SIN FORMULARIOS) ============
        if (!empty($inscripciones)) {
            echo "<div style='margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
            echo "<h6>📋 Estudiantes Asignados (" . count($inscripciones) . "):</h6>";

            foreach ($inscripciones as $inscripcion) {
                echo "<div class='inscripcion-item'>";
                echo "<div>";
                echo "<strong>{$inscripcion['estudiante_nombres']} {$inscripcion['estudiante_apellidos']}</strong><br>";
                echo "<small>CI: {$inscripcion['estudiante_ci']} | Código: {$inscripcion['estudiante_codigo']}</small>";
                echo "</div>";
                echo "<div>";
                echo "✅ Inscrito";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>";
            echo "<p>📝 No hay estudiantes inscritos en este grupo.</p>";
            echo "</div>";
        }

        // ============ FORMULARIO PARA AGREGAR ESTUDIANTE ============
        echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 15px;'>";
        echo "<h6>➕ Agregar Estudiante:</h6>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='evento' value='agregar_inscripcion'>";
        echo "<input type='hidden' name='grupo_id' value='{$this->grupoIdEditar}'>";
        echo "<div style='display: flex; gap: 10px; align-items: end;'>";
        echo "<div style='flex: 1;'>";
        echo "<select name='estudiante_codigo' required class='form-control'>";
        echo "<option value=''>Selecciona un estudiante</option>";

        $codigosInscritos = array_column($inscripciones, 'estudiante_codigo');
        foreach ($estudiantes as $estudiante) {
            if (!in_array($estudiante['codigo'], $codigosInscritos)) {
                echo "<option value='{$estudiante['codigo']}'>{$estudiante['nombres']} {$estudiante['apellidos']} (CI: {$estudiante['ci']})</option>";
            }
        }

        echo "</select>";
        echo "</div>";
        echo "<button type='submit' class='btn btn-success'>➕ Agregar</button>";
        echo "</div>";
        echo "</form>";
        echo "</div>";

        // ============ FORMULARIOS PARA ELIMINAR ESTUDIANTES ============
        if (!empty($inscripciones)) {
            echo "<div style='background: #ffe8e8; padding: 15px; border-radius: 5px;'>";
            echo "<h6>🗑️ Eliminar Estudiantes:</h6>";
            echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";

            foreach ($inscripciones as $inscripcion) {
                echo "<form method='POST' style='display: inline-block;'>";
                echo "<input type='hidden' name='evento' value='eliminar_inscripcion'>";
                echo "<input type='hidden' name='estudiante_codigo' value='{$inscripcion['estudiante_codigo']}'>";
                echo "<input type='hidden' name='grupo_id' value='{$this->grupoIdEditar}'>";
                echo "<button type='submit' class='btn btn-danger' style='padding: 5px 10px; font-size: 0.8em;' onclick='return confirm(\"¿Eliminar inscripción de {$inscripcion['estudiante_nombres']} {$inscripcion['estudiante_apellidos']}?\")'>🗑️ {$inscripcion['estudiante_nombres']}</button>";
                echo "</form>";
            }

            echo "</div>";
            echo "</div>";
        }

        echo "</div>";
    }

    private function renderListaTodosLosGrupos($grupos)
    {


        echo "<div style='margin-top: 30px;'>";
        echo "<h4>📚 Todos los Grupos del Sistema</h4>";


        if (empty($grupos)) {
            echo "<p style='text-align: center; color: #6c757d;'>No hay grupos registrados</p>";
        } else {
            foreach ($grupos as $grupo) {

                echo "<div class='group-card'>";
                echo "<h4>📖 {$grupo['grupo_nombre']}</h4>";
                echo "<div class='group-meta'>";
                echo "<p><strong>Materia:</strong> {$grupo['materia_nombre']}</p>";
                echo "<p><strong>Profesor:</strong> {$grupo['profesor_nombres']} </p>";
                echo "<p><strong>Capacidad:</strong> {$grupo['capacidad_maxima']} estudiantes</p>";
                echo "<p><strong>Inscritos:</strong> {$grupo['estudiantes_inscritos']} estudiantes</p>";
                echo "</div>";
                echo "<div style='margin-top: 15px;'>";
                // Botón Editar
                echo "<form method='POST' style='display: inline; margin: 5px;'>";
                echo "<input type='hidden' name='evento' value='mostrar_form_editar'>";
                echo "<input type='hidden' name='grupo_id' value='{$grupo['id']}'>";
                echo "<button type='submit' class='btn btn-warning'>✏️ Editar</button>";
                echo "</form>";
                // Botón Eliminar
                echo "<form method='POST' style='display: inline; margin: 5px;' onsubmit='return confirm(\"¿Estás seguro de eliminar este grupo?\")'>";
                echo "<input type='hidden' name='evento' value='eliminar_grupo'>";
                echo "<input type='hidden' name='id' value='{$grupo['id']}'>";
                echo "<button type='submit' class='btn btn-danger'>🗑️ Eliminar</button>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
            }
        }
        echo "</div>";
    }

    private function renderBotonesNavegacion()
    {
        echo "<div style='margin-top: 30px; border-top: 1px solid #dee2e6; padding-top: 20px;'>";
        echo "<h4>🧩 Otras Secciones del Sistema</h4>";
        $botones = [
            ['evento' => 'MateriasClicked', 'texto' => '📚 Materias'],
            ['evento' => 'EstudiantesClicked', 'texto' => '👩‍🎓 Estudiantes'],
            ['evento' => 'UsuariosClicked', 'texto' => '👤 Usuarios'],
            ['evento' => 'ProfesoresClicked', 'texto' => '👨‍🏫 Profesores'],

        ];
        foreach ($botones as $boton) {
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='{$boton['evento']}'>";
            echo "<button type='submit' class='btn btn-warning'>{$boton['texto']}</button>";
            echo "</form>";
        }
        echo "</div>";
    }

    private function renderNoGroups($rol)
    {
        echo "<div style='text-align: center; color: #6c757d; padding: 40px; background: #f8f9fa; border-radius: 8px;'>";
        echo "<h3>📚 Sin grupos asignados</h3>";
        if ($rol === 'profesor') {
            echo "<p>No tienes grupos asignados como profesor.</p>";
        } else {
            echo "<p>No estás inscrito en ningún grupo.</p>";
        }
        echo "</div>";
    }

    private function renderGroupsList($grupo)
    {

        echo "<script> "
            . "console.log(" . json_encode($grupo) . ");"
            . "</script>";
        echo "<h3>📚 " . ($grupo['rol'] === 'profesor' ? 'Grupos que Impartes' : 'Grupos Inscritos') . " (" . count($grupo['grupos']) . ")</h3>";

        foreach ($grupo['grupos'] as $grupoItem) {
            echo "<div class='group-card'>";
            echo "<h4>📖 {$grupoItem['grupo_nombre']}</h4>";
            echo "<div class='group-meta'>";
            echo "<p><strong>Materia:</strong> {$grupoItem['materia_nombre']}</p>";
            echo "<p><strong>Capacidad:</strong> {$grupoItem['capacidad_maxima']} estudiantes</p>";
            echo "<p><strong>Inscritos:</strong> {$grupoItem['estudiantes_inscritos']} estudiantes</p>";
            if ($grupo['rol'] === 'estudiante' && isset($grupoItem['profesor_nombres'])) {
                echo "<p><strong>Profesor:</strong> {$grupoItem['profesor_nombres']} {$grupoItem['profesor_apellidos']}</p>";
            }
            echo "</div>";
            echo "<div style='margin-top: 15px;'>";
            // Botón Ver Clases
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='ver_clases'>";
            echo "<input type='hidden' name='grupo_id' value='{$grupoItem['id']}'>";
            echo "<button type='submit' class='btn " . ($grupo['rol'] === 'profesor' ? 'btn-warning' : 'btn-success') . "'>📚 Ver Clases</button>";
            echo "</form>";


            echo "</div>";
            echo "</div>";
        }
    }

    private function getIniciales($data)
    {
        if (isset($data['nombres']) && isset($data['apellidos'])) {
            $nombre = trim($data['nombres']);
            $apellido = trim($data['apellidos']);
            return strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
        }
        $rol = $data['rol'] ?? 'U';
        return strtoupper(substr($rol, 0, 1));
    }

    private function renderCSS()
    {
        echo "<style>";
        echo "body { font-family: 'Segoe UI', Arial, sans-serif; background: white; margin: 0; padding: 20px; color: #333; }";
        echo ".container { max-width: 1200px; margin: 0 auto; background: white; border: 1px solid #ddd; border-radius: 8px; }";
        echo ".header { text-align: center; color: #000; margin-bottom: 30px; background: white; border-bottom: 2px solid #000; padding: 25px; }";
        echo ".success { color: #000; background: #f8f9fa; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; }";
        echo ".error { color: #000; background: #f8f9fa; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; }";
        echo ".group-card { background: white; border: 1px solid #ddd; border-radius: 6px; padding: 20px; margin: 15px 0; }";
        echo ".group-meta { color: #666; font-size: 0.9em; margin: 5px 0; }";
        echo ".btn { background: #000; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; font-size: 0.9em; }";
        echo ".btn:hover { background: #333; }";
        echo ".btn-success { background: #000; } .btn-success:hover { background: #333; }";
        echo ".btn-warning { background: white; color: #000; border: 1px solid #000; } .btn-warning:hover { background: #f8f9fa; }";
        echo ".btn-danger { background: #000; color: white; } .btn-danger:hover { background: #333; }";
        echo ".btn-secondary { background: white; color: #000; border: 1px solid #000; } .btn-secondary:hover { background: #f8f9fa; }";
        echo ".form-section { background: white; padding: 20px; border-radius: 6px; margin: 20px 0; border: 1px solid #ddd; }";
        echo ".form-group { margin-bottom: 15px; }";
        echo ".form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #000; }";
        echo ".form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; background: white; color: #000; }";
        echo ".form-control:focus { outline: none; border-color: #000; }";
        echo ".inscripcion-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; margin: 5px 0; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; }";
        echo ".user-dropdown { position: fixed; top: 20px; left: 20px; z-index: 1000; }";
        echo ".user-avatar { width: 50px; height: 50px; border-radius: 50%; background: #000; border: 3px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.3); cursor: pointer; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px; }";
        echo ".dropdown-content { position: absolute; top: 60px; left: 0; background: white; min-width: 280px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); border-radius: 8px; display: none; border: 1px solid #ddd; }";
        echo ".dropdown-content.show { display: block; }";
        echo ".dropdown-header { background: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd; }";
        echo ".dropdown-item { display: block; padding: 10px 15px; text-decoration: none; color: #000; }";
        echo ".dropdown-item:hover { background: #f8f9fa; }";
        echo ".role-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; font-weight: bold; }";
        echo ".role-admin { background: #f8f9fa; color: #000; border: 1px solid #ddd; }";
        echo ".role-profesor { background: #f8f9fa; color: #000; border: 1px solid #ddd; }";
        echo ".role-estudiante { background: #f8f9fa; color: #000; border: 1px solid #ddd; }";
        echo ".admin-section { background: white; }";
        echo ".groups-section { background: white; padding: 20px; }";

        // Estilos responsive
        echo "@media (max-width: 768px) {";
        echo "  body { padding: 10px; }";
        echo "  .container { margin: 0; border-radius: 6px; }";
        echo "  .header { padding: 15px; }";
        echo "  .form-section { padding: 15px; }";
        echo "  .group-card { padding: 15px; }";
        echo "  .btn { padding: 6px 12px; font-size: 0.8em; }";
        echo "  .user-dropdown { top: 10px; left: 10px; }";
        echo "  .user-avatar { width: 40px; height: 40px; font-size: 16px; }";
        echo "}";

        echo "</style>";
        echo "<script>";
        echo "function toggleUserDropdown() {";
        echo "  var dropdown = document.getElementById('userDropdown');";
        echo "  dropdown.classList.toggle('show');";
        echo "}";
        echo "// Cerrar dropdown al hacer clic fuera";
        echo "document.addEventListener('click', function(event) {";
        echo "  var dropdown = document.getElementById('userDropdown');";
        echo "  var avatar = document.querySelector('.user-avatar');";
        echo "  if (!avatar.contains(event.target) && !dropdown.contains(event.target)) {";
        echo "    dropdown.classList.remove('show');";
        echo "  }";
        echo "});";
        echo "</script>";
    }
}