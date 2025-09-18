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
        
        // Estilos adicionales para el CRUD de admin
        echo ".admin-section { margin: 20px 0; }";
        echo ".form-group { margin-bottom: 15px; }";
        echo ".form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #495057; }";
        echo ".form-control { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }";
        echo ".form-control:focus { border-color: #007bff; outline: none; box-shadow: 0 0 0 2px rgba(0,123,255,0.25); }";
        echo ".btn-sm { padding: 5px 10px; font-size: 0.875em; }";
        echo ".table-responsive { overflow-x: auto; }";
        echo ".search-box { margin: 15px 0; }";
        echo ".search-box input { padding: 10px; border: 1px solid #ddd; border-radius: 4px; width: 300px; }";
        echo ".loading { text-align: center; padding: 20px; color: #6c757d; }";
        echo ".empty-state { text-align: center; padding: 40px; color: #6c757d; background: #f8f9fa; border-radius: 8px; }";
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
            echo "<div class='admin-section'>";
            echo "<h3>👑 Panel de Administrador - Gestión de Grupos</h3>";
            echo "<p>{$grupo['mensaje']}</p>";
            
            // Botón para crear nuevo grupo
            echo "<div class='actions' style='margin: 20px 0;'>";
            echo "<button onclick='mostrarFormularioCrear()' class='btn btn-success'>➕ Crear Nuevo Grupo</button>";
          
            echo "</div>";

            // Formulario para crear/editar grupo
            echo "<div id='formulario-grupo' style='display: none; background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
            echo "<h4 id='titulo-formulario'>Crear Nuevo Grupo</h4>";
            echo "<form id='form-grupo' onsubmit='return procesarFormulario(event)'>";
            echo "<input type='hidden' id='grupo-id' name='id'>";
            echo "<div style='margin-bottom: 15px;'>";
            echo "<label>Nombre del Grupo:</label>";
            echo "<input type='text' id='nombre' name='nombre' required style='width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;'>";
            echo "</div>";
            echo "<div style='margin-bottom: 15px;'>";
            echo "<label>Materia:</label>";
            echo "<select id='materia_id' name='materia_id' required style='width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;'>";
            echo "<option value=''>Selecciona una materia</option>";
            echo "</select>";
            echo "</div>";
            echo "<div style='margin-bottom: 15px;'>";
            echo "<label>Profesor:</label>";
            echo "<select id='profesor_codigo' name='profesor_codigo' required style='width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;'>";
            echo "<option value=''>Selecciona un profesor</option>";
            echo "</select>";
            echo "</div>";
            echo "<div style='margin-bottom: 15px;'>";
            echo "<label>Capacidad Máxima:</label>";
            echo "<input type='number' id='capacidad_maxima' name='capacidad_maxima' value='100' min='1' style='width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;'>";
            echo "</div>";
            echo "<div>";
            echo "<button type='submit' class='btn btn-success'>💾 Guardar</button>";
            echo "<button type='button' onclick='cancelarFormulario()' class='btn btn-secondary'>❌ Cancelar</button>";
            echo "</div>";
            echo "</form>";
            echo "</div>";

            // Lista de todos los grupos
            echo "<div id='lista-grupos'>";
            echo "<h4>📚 Todos los Grupos del Sistema</h4>";
            echo "<div id='grupos-container'>";
            echo "<p style='text-align: center; color: #6c757d;'>Cargando grupos...</p>";
            echo "</div>";
            echo "</div>";

            // Botones de navegación a otras secciones
            echo "<div class='actions' style='margin-top: 30px; border-top: 1px solid #dee2e6; padding-top: 20px;'>";
            echo "<h4>🧩 Otras Secciones del Sistema</h4>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='MateriasClicked'>";
            echo "<button type='submit' class='btn btn-warning'>📚 Materias</button>";
            echo "</form>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='EstudiantesClicked'>";
            echo "<button type='submit' class='btn btn-warning'>� Estudiantes</button>";
            echo "</form>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='InscripcionClicked'>";
            echo "<button type='submit' class='btn btn-warning'>� Inscripciones</button>";
            echo "</form>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='UsuariosClicked'>";
            echo "<button type='submit' class='btn btn-warning'>� Usuarios</button>";
            echo "</form>";
            echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
            echo "<input type='hidden' name='evento' value='ProfesoresClicked'>";
            echo "<button type='submit' class='btn btn-warning'>�‍🏫 Profesores</button>";
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
                  
                    echo "<form method='POST' style='display: inline-block; margin: 5px;'>";
                    echo "<input type='hidden' name='evento' value='ver_clases'>";
                    echo "<input type='hidden' name='grupo_id' value='{$grupoItem['id']}'>";
                    echo "<button type='submit' class='btn btn-warning'>📚 Ver Clases</button>";
                   
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

        // JavaScript para funcionalidades CRUD
        if ($grupo['rol'] === 'admin') {
            echo "<script>";
            echo "
            // Variables globales
            let profesores = [];
            let materias = [];
            let modoEdicion = false;
            
            // Cargar datos iniciales
            document.addEventListener('DOMContentLoaded', function() {
                cargarDatosFormulario();
                cargarTodosLosGrupos();
            });
            
            // Función para cargar profesores y materias
            function cargarDatosFormulario() {
                fetch(window.location.pathname, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'evento=obtener_datos_formulario'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.text(); // Primero obtenemos como texto
                })
                .then(text => {
                    console.log('Response text:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            profesores = data.profesores;
                            materias = data.materias;
                            llenarSelectores();
                        } else {
                            console.error('Error en respuesta:', data.message);
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.error('Response was:', text);
                    }
                })
                .catch(error => {
                    console.error('Error en fetch:', error);
                });
            }
            
            // Llenar los selectores de materia y profesor
            function llenarSelectores() {
                const selectMateria = document.getElementById('materia_id');
                const selectProfesor = document.getElementById('profesor_codigo');
                
                // Limpiar selectores
                selectMateria.innerHTML = '<option value=\"\">Selecciona una materia</option>';
                selectProfesor.innerHTML = '<option value=\"\">Selecciona un profesor</option>';
                
                // Llenar materias
                materias.forEach(materia => {
                    const option = document.createElement('option');
                    option.value = materia.id;
                    option.textContent = materia.nombre;
                    selectMateria.appendChild(option);
                });
                
                // Llenar profesores
                profesores.forEach(profesor => {
                    const option = document.createElement('option');
                    option.value = profesor.codigo;
                    option.textContent = profesor.nombres + ' ' + profesor.apellidos;
                    selectProfesor.appendChild(option);
                });
            }
            
            // Mostrar formulario para crear
            function mostrarFormularioCrear() {
                document.getElementById('titulo-formulario').textContent = 'Crear Nuevo Grupo';
                document.getElementById('formulario-grupo').style.display = 'block';
                document.getElementById('form-grupo').reset();
                document.getElementById('grupo-id').value = '';
                modoEdicion = false;
            }
            
            // Mostrar formulario para editar
            function editarGrupo(id) {
                document.getElementById('titulo-formulario').textContent = 'Editar Grupo';
                document.getElementById('formulario-grupo').style.display = 'block';
                modoEdicion = true;
                
                // Cargar datos del grupo
                fetch(window.location.pathname, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'evento=obtener_grupo&id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const grupo = data.data;
                        document.getElementById('grupo-id').value = grupo.id;
                        document.getElementById('nombre').value = grupo.nombre;
                        document.getElementById('materia_id').value = grupo.materia_id;
                        document.getElementById('profesor_codigo').value = grupo.profesor_codigo;
                        document.getElementById('capacidad_maxima').value = grupo.capacidad_maxima;
                    }
                })
                .catch(error => console.error('Error:', error));
            }
            
            // Cancelar formulario
            function cancelarFormulario() {
                document.getElementById('formulario-grupo').style.display = 'none';
                document.getElementById('form-grupo').reset();
                modoEdicion = false;
            }
            
            // Procesar formulario (crear o actualizar)
            function procesarFormulario(event) {
                event.preventDefault();
                
                const formData = new FormData(document.getElementById('form-grupo'));
                const evento = modoEdicion ? 'actualizar_grupo' : 'crear_grupo';
                formData.append('evento', evento);
                
                fetch(window.location.pathname, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        cancelarFormulario();
                        cargarTodosLosGrupos();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
                
                return false;
            }
            
            // Cargar todos los grupos
            function cargarTodosLosGrupos() {
                fetch(window.location.pathname, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'evento=listar_todos'
                })
                .then(response => {
                    console.log('Response status for listar_todos:', response.status);
                    return response.text(); // Primero obtenemos como texto
                })
                .then(text => {
                    console.log('Response text for listar_todos:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            mostrarGrupos(data.data);
                        } else {
                            console.error('Error en respuesta:', data.message);
                            document.getElementById('grupos-container').innerHTML = '<p style=\"color: red;\">Error: ' + data.message + '</p>';
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.error('Response was:', text);
                        document.getElementById('grupos-container').innerHTML = '<p style=\"color: red;\">Error al procesar respuesta del servidor</p>';
                    }
                })
                .catch(error => {
                    console.error('Error en fetch:', error);
                    document.getElementById('grupos-container').innerHTML = '<p style=\"color: red;\">Error de conexión</p>';
                });
            }
            
            // Mostrar lista de grupos
            function mostrarGrupos(grupos) {
                const container = document.getElementById('grupos-container');
                
                if (grupos.length === 0) {
                    container.innerHTML = '<p style=\"text-align: center; color: #6c757d;\">No hay grupos registrados</p>';
                    return;
                }
                
                let html = '';
                grupos.forEach(grupo => {
                    html += '<div class=\"group-card\">';
                    html += '<h4>📖 ' + grupo.grupo_nombre + '</h4>';
                    html += '<div class=\"group-meta\">';
                    html += '<p><strong>Materia:</strong> ' + grupo.materia_nombre + '</p>';
                    html += '<p><strong>Profesor:</strong> ' + grupo.profesor_nombres + ' ' + grupo.profesor_apellidos + '</p>';
                    html += '<p><strong>Capacidad:</strong> ' + grupo.capacidad_maxima + ' estudiantes</p>';
                    html += '<p><strong>Inscritos:</strong> ' + grupo.estudiantes_inscritos + ' estudiantes</p>';
                    html += '</div>';
                    html += '<div style=\"margin-top: 15px;\">';
                    html += '<button onclick=\"editarGrupo(' + grupo.id + ')\" class=\"btn btn-warning\">✏️ Editar</button>';
                    html += '<button onclick=\"eliminarGrupo(' + grupo.id + ')\" class=\"btn btn-danger\">🗑️ Eliminar</button>';
                    html += '<button onclick=\"verClases(' + grupo.id + ')\" class=\"btn\">📚 Ver Clases</button>';
                    html += '</div>';
                    html += '</div>';
                });
                
                container.innerHTML = html;
            }
            
            // Eliminar grupo
            function eliminarGrupo(id) {
                if (!confirm('¿Estás seguro de que deseas eliminar este grupo?')) {
                    return;
                }
                
                fetch(window.location.pathname, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'evento=eliminar_grupo&id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        cargarTodosLosGrupos();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el grupo');
                });
            }
            
            // Ver clases de un grupo
            function verClases(grupoId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.pathname;
                
                const input1 = document.createElement('input');
                input1.type = 'hidden';
                input1.name = 'evento';
                input1.value = 'ver_clases';
                
                const input2 = document.createElement('input');
                input2.type = 'hidden';
                input2.name = 'grupo_id';
                input2.value = grupoId;
                
                form.appendChild(input1);
                form.appendChild(input2);
                document.body.appendChild(form);
                form.submit();
            }
            ";
            echo "</script>";
        }

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