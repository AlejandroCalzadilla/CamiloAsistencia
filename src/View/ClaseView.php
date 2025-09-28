<?php
class ClaseView
{
    private $claseModel;
    private $asistenciaModel;
    private $message = '';
    private $messageType = '';
    private $mostrarFormulario = false;
    private $mostrarAsistencias = false;
    private $claseIdAsistencias = null;

    public function __construct()
    {
        $this->claseModel = new ClaseModel();
        $this->asistenciaModel = new AsistenciaModel();
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

    public function setMostrarFormulario($mostrar)
    {
        $this->mostrarFormulario = $mostrar;
    }

    public function setMostrarAsistencias($mostrar, $claseId = null)
    {
        $this->mostrarAsistencias = $mostrar;
        $this->claseIdAsistencias = $claseId;
    }

    public function actualizar($grupo_id)
    {
        $data = $this->claseModel->mostrar($grupo_id);
        $asistenciaResponse = $this->asistenciaModel->obtener($grupo_id);
        $this->render($data, $asistenciaResponse);
    }


    public function render($data, $asistenciaData)
    {

        echo "<!DOCTYPE html>";
        echo "<html lang='es'><head><title>Clases - Sistema de Asistencia</title>";
        $this->renderCSS();
        echo "</head><body>";

        echo "<div class='container'>";
        echo "<div class='header'>";
        echo "<h1>📚 Clases del Grupo</h1>";
        echo "<h3>Sistema de Asistencia Académica</h3>";
        echo "</div>";

        // Mostrar mensajes
        if ($this->message) {
            $class = $this->messageType === 'success' ? 'success' : 'error';
            echo "<div class='$class'>{$this->message}</div>";
        }

        // Botón volver
        echo "<div style='margin: 20px 0;'>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='evento' value='volver_grupos'>";
        echo "<button type='submit' class='btn btn-secondary'>🔙 Volver a Grupos</button>";
        echo "</form>";
        echo "</div>";

        // Verificaciones de acceso
        if ($data['rol'] === 'no_logueado') {
            echo "<div class='access-denied'>";
            echo "<h3>🔒 Acceso Denegado</h3>";
            echo "<p>Debes iniciar sesión para ver las clases.</p>";
            echo "<a href='../public/index.php' class='btn'>🚪 Ir al Login</a>";
            echo "</div>";
            echo "</div></body></html>";
            return;
        }

        if ($data['rol'] === 'admin') {
            echo "<div class='access-denied'>";
            echo "<h3>⛔ Acceso Restringido</h3>";
            echo "<p>Los administradores no pueden ver las clases de los grupos.</p>";
            echo "<div class='actions'>";
            echo "<a href='admin-dashboard.php' class='btn btn-warning'>📊 Panel de Administrador</a>";
            echo "</div>";
            echo "</div>";
            echo "</div></body></html>";
            return;
        }

        // Mostrar información del grupo
        if ($data['grupo']) {
            echo "<div class='grupo-info'>";
            echo "<h3>📖 Información del Grupo</h3>";
            echo "<p><strong>Grupo:</strong> {$data['grupo']['grupo_nombre']}</p>";
            echo "<p><strong>Materia:</strong> {$data['grupo']['materia_nombre']}</p>";
            echo "<p><strong>Profesor:</strong> {$data['grupo']['profesor_nombres']} {$data['grupo']['profesor_apellidos']}</p>";
            echo "</div>";
        }

        // Formulario crear clase (solo profesores)
        if ($data['rol'] === 'profesor') {
            if (!$this->mostrarFormulario) {
                echo "<div style='margin: 20px 0;'>";
                echo "<form method='POST' style='display: inline;'>";
                echo "<input type='hidden' name='evento' value='mostrar_form_crear'>";
                echo "<button type='submit' class='btn btn-success'>➕ Crear Nueva Clase</button>";
                echo "</form>";
                echo "</div>";
            } else {
                $this->renderFormularioCrearClase();
            }
        }

        // Mostrar clases
        $this->renderClases($data, $asistenciaData);

        echo "</div>";
        echo "</body></html>";
    }

    private function renderFormularioCrearClase()
    {
        echo "<div class='form-section'>";
        echo "<h4>➕ Crear Nueva Clase</h4>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='evento' value='crear_clase'>";

        echo "<div class='form-group'>";
        echo "<label>Fecha de la clase:</label>";
        echo "<input type='date' name='dia' value='" . date('Y-m-d') . "' required class='form-control'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label>Hora de inicio Presente:</label>";
        echo "<input type='time' name='hora_inicio' value='" . date('H:i') . "' required class='form-control'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label>Hora de fin Presente:</label>";
        echo "<input type='time' name='hora_fin' value='" . date('H:i', strtotime('+2 hours')) . "' required class='form-control'>";
        echo "</div>";

        echo "<div style='margin-top: 20px;'>";
        echo "<button type='submit' class='btn btn-success'>🎓 Crear Clase</button>";
        echo "</div>";
        echo "</form>";

        // Formulario separado para cancelar
        echo "<div style='margin-top: 10px;'>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='evento' value='cancelar_formulario'>";
        echo "<button type='submit' class='btn btn-secondary'>❌ Cancelar</button>";
        echo "</form>";
        echo "</div>";

        echo "</div>";
    }

    private function renderClases($data, $asistenciaResponse)
    {

       if (is_array($asistenciaResponse)) {
            if (isset($asistenciaResponse['success'])) {
                // Es una respuesta con estructura success/mensaje
                $asistenciaData = $asistenciaResponse['success'] ? ($asistenciaResponse['data'] ?? []) : [];
            } else {
                // Es directamente el array de datos
                $asistenciaData = $asistenciaResponse;
            }
        } else {
            $asistenciaData = [];
        }


        echo "<div class='clases-section'>";

        if (empty($data['clases'])) {
            echo "<div class='no-clases'>";
            echo "<h3>📅 Sin clases registradas</h3>";
            if ($data['rol'] === 'profesor') {
                echo "<p>No hay clases creadas para este grupo.</p>";
            } else {
                echo "<p>El profesor aún no ha creado clases para este grupo.</p>";
            }
            echo "</div>";
        } else {
            echo "<h3>📅 Clases Registradas (" . count($data['clases']) . ")</h3>";

            // Organizar asistencias por clase
            $asistenciasPorClase = [];
            $miAsistenciaPorClase = [];

            if (is_array($asistenciaData)) {
                foreach ($asistenciaData as $asistencia) {
                    $clase_id = $asistencia['clase_id'];

                    if ($data['rol'] === 'profesor') {
                        if (!isset($asistenciasPorClase[$clase_id])) {
                            $asistenciasPorClase[$clase_id] = [];
                        }
                        $asistenciasPorClase[$clase_id][] = $asistencia;
                    } elseif ($data['rol'] === 'estudiante') {
                        $miAsistenciaPorClase[$clase_id] = $asistencia;
                    }
                }
            }

            foreach ($data['clases'] as $clase) {
                $this->renderClaseCard($clase, $data['rol'], $asistenciasPorClase, $miAsistenciaPorClase);
            }
        }

        echo "</div>";
    }

    private function renderClaseCard($clase, $rol, $asistenciasPorClase, $miAsistenciaPorClase)
    {
        // Determinar estilo según asistencia
        $cardClass = "clase-card";
        $badgeClass = "";
        $badgeText = "";

        if ($rol === 'estudiante') {
            $miAsistencia = $miAsistenciaPorClase[$clase['id']] ?? null;

            if ($miAsistencia) {
                switch ($miAsistencia['tipo']) {
                    case 'presente':
                        $cardClass .= " asistencia-presente";
                        $badgeClass = "badge-presente";
                        $badgeText = "✅ Presente";
                        break;
                    case 'retraso':
                        $cardClass .= " asistencia-presente";
                        $badgeClass = "badge-presente";
                        $badgeText = "⏰ Retraso";
                        break;
                    case 'ausente':
                    default:
                        $cardClass .= " asistencia-ausente";
                        $badgeClass = "badge-ausente";
                        $badgeText = "❌ Ausente";
                        break;
                }
            } else {
                $cardClass .= " asistencia-ausente";
                $badgeClass = "badge-ausente";
                $badgeText = "❌ Ausente";
            }
        }

        echo "<div class='$cardClass'>";
        echo "<h4>📖 Clase del " . date('d/m/Y', strtotime($clase['dia']));

        // Badge de asistencia para estudiantes
        if ($rol === 'estudiante') {
            echo "<span class='asistencia-badge $badgeClass'>$badgeText</span>";
        }

        echo "</h4>";
        echo "<div class='clase-meta'>";
        echo "<p><strong>Fecha:</strong> " . date('d/m/Y', strtotime($clase['dia'])) . "</p>";
        echo "<p><strong>Hora inicio presente</strong> {$clase['hora_inicio']} - <strong>Hora fin  presente</strong> - {$clase['hora_fin']}</p>";


        // Para profesor: mostrar resumen de asistencias
        if ($rol === 'profesor' && isset($asistenciasPorClase[$clase['id']])) {
            $asistencias = $asistenciasPorClase[$clase['id']];
            $totalEstudiantes = count($asistencias);
            $presentes = count(array_filter($asistencias, fn($a) => $a['tipo'] === 'presente'));
            $retrasos = count(array_filter($asistencias, fn($a) => $a['tipo'] === 'retraso'));
            $ausentes = count(array_filter($asistencias, fn($a) => $a['tipo'] === 'ausente'));

            echo "<div class='asistencias-resumen'>";
            echo "<h5>📊 Resumen de Asistencias</h5>";
            echo "<p><strong>Total estudiantes:</strong> $totalEstudiantes</p>";
            echo "<p><strong>Presentes:</strong> <span style='color: #28a745;'>$presentes</span> | ";
            echo "<strong>Retrasos:</strong> <span style='color: #ffc107;'>$retrasos</span> | ";
            echo "<strong>Ausentes:</strong> <span style='color: #dc3545;'>$ausentes</span></p>";

            // Formulario para ver asistencias detalladas
            echo "<form method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='evento' value='ver_asistencias'>";
            echo "<input type='hidden' name='clase_id' value='{$clase['id']}'>";
            echo "<button type='submit' class='btn' style='font-size: 0.8em;'>👁️ Ver Detalle</button>";
            echo "</form>";

            // Mostrar detalles si están activados
            if ($this->mostrarAsistencias && $this->claseIdAsistencias == $clase['id']) {
              
                echo "<div style='margin-top: 15px; background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
                echo "<h6>Lista de Estudiantes:</h6>";
                echo "<div style='max-height: 200px; overflow-y: auto;'>";

                foreach ($asistencias as $asistencia) {
                    $iconoTipo = match ($asistencia['tipo']) {
                        'presente' => '✅',
                        'retraso' => '⏰',
                        'ausente' => '❌',
                        default => '❓'
                    };

                    $colorTipo = match ($asistencia['tipo']) {
                        'presente' => '#28a745',
                        'retraso' => '#ffc107',
                        'ausente' => '#dc3545',
                        default => '#6c757d'
                    };

                    echo "<div style='display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #eee;'>";
                    echo "<span>{$asistencia['estudiante_nombres']} {$asistencia['estudiante_apellidos']}</span>";
                    echo "<span style='color: $colorTipo; font-weight: bold;'>$iconoTipo {$asistencia['tipo']}</span>";
                    echo "</div>";
                }

                echo "</div>";
                echo "</div>";
            }

            echo "</div>";
        }

        // Mostrar código  para profesores
        if ($clase['codigo'] && $rol === 'profesor') {
            echo "<div class='qr-code'>";
            echo "<p><strong>🔗 Código :</strong> {$clase['codigo']}</p>";
            echo "<small>Los estudiantes pueden usar este código para registrar asistencia</small>";
            echo "</div>";
        }

        echo "</div>";

        // Botones según el rol
        echo "<div style='margin-top: 15px;'>";

        if ($rol === 'profesor') {
            // Formulario eliminar
            echo "<form method='POST' style='display: inline; margin: 5px;' onsubmit='return confirm(\"¿Estás seguro de eliminar esta clase?\")'>";
            echo "<input type='hidden' name='evento' value='eliminar_clase'>";
            echo "<input type='hidden' name='clase_id' value='{$clase['id']}'>";
            echo "<button type='submit' class='btn btn-danger'>🗑️ Eliminar</button>";
            echo "</form>";

        } 
        elseif ($rol === 'estudiante') {
        // Para estudiante: verificar si puede marcar asistencia
        $miAsistencia = $miAsistenciaPorClase[$clase['id']] ?? null;
        
        // Solo puede marcar si no tiene asistencia O si está marcado como ausente
        $puedeMarcar = !$miAsistencia || $miAsistencia['tipo'] === 'ausente';

        if ($puedeMarcar && $clase['codigo']) {
            echo "<div class='qr-form'>";
            echo "<h5>📱 Registrar Asistencia con Código</h5>";
            echo "<form method='POST'>";
            echo "<input type='hidden' name='evento' value='registrar_asistencia'>";
            echo "<input type='hidden' name='clase_id' value='{$clase['id']}'>";
            echo "<div style='display: flex; gap: 10px; align-items: end; margin: 10px 0;'>";
            echo "<div style='flex: 1;'>";
            echo "<label><strong>Código :</strong></label>";
            echo "<input type='text' name='codigo' placeholder='Ingresa código' maxlength='100' required class='form-control'>";
            echo "</div>";
            echo "<button type='submit' class='btn btn-success'>✅ Marcar Asistencia</button>";
            echo "</div>";
            echo "</form>";
            echo "</div>";

        } elseif ($miAsistencia && ($miAsistencia['tipo'] === 'presente' || $miAsistencia['tipo'] === 'retraso')) {
            // Mostrar mensaje para presente o retraso
            $tipoTexto = $miAsistencia['tipo'] === 'presente' ? 'presente' : 'retraso';
            $icono = $miAsistencia['tipo'] === 'presente' ? '✅' : '⏰';
            
            echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; color: #155724;'>";
            echo "$icono <strong>Asistencia registrada como: $tipoTexto</strong>";
            if (isset($miAsistencia['hora_inicio'])) {
                echo "<br><small>Hora: {$miAsistencia['hora_inicio']}</small>";
            }
            echo "</div>";
        }
    }

        echo "</div>";
        echo "</div>";
    }

    private function renderCSS()
    {
        echo "<style>";
        echo "body { font-family: 'Segoe UI', Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: white; color: #333; }";
        echo ".container { background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd; }";
        echo ".header { text-align: center; color: #000; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }";
        echo ".success { color: #000; background: #f8f9fa; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; }";
        echo ".error { color: #000; background: #f8f9fa; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; }";
        echo ".grupo-info { background: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; border: 1px solid #ddd; }";
        echo ".clases-section { margin: 30px 0; }";
        echo ".clase-card { background: white; border: 1px solid #ddd; border-radius: 6px; padding: 20px; margin: 15px 0; }";
        echo ".clase-card.asistencia-presente { background: #f8f9fa; border: 1px solid #000; border-left: 3px solid #000; }";
        echo ".clase-card.asistencia-ausente { background: #f8f9fa; border: 1px solid #ddd; }";
        echo ".asistencia-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; font-weight: bold; margin-left: 10px; border: 1px solid #ddd; }";
        echo ".badge-presente { background: #f8f9fa; color: #000; }";
        echo ".badge-ausente { background: #f8f9fa; color: #000; }";
        echo ".qr-form { background: #f8f9fa; border: 1px solid #ddd; border-radius: 6px; padding: 15px; margin: 10px 0; }";
        echo ".qr-code { background: white; border: 2px dashed #000; padding: 10px; border-radius: 6px; text-align: center; margin: 10px 0; }";
        echo ".asistencias-resumen { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #ddd; }";
        echo ".form-section { background: white; padding: 20px; border-radius: 6px; margin: 20px 0; border: 1px solid #ddd; }";
        echo ".form-group { margin-bottom: 15px; }";
        echo ".form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #000; }";
        echo ".form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; background: white; color: #000; }";
        echo ".form-control:focus { outline: none; border-color: #000; }";
        echo ".btn { background: #000; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; font-size: 0.9em; }";
        echo ".btn:hover { background: #333; }";
        echo ".btn-success { background: #000; } .btn-success:hover { background: #333; }";
        echo ".btn-warning { background: white; color: #000; border: 1px solid #000; } .btn-warning:hover { background: #f8f9fa; }";
        echo ".btn-danger { background: #000; color: white; } .btn-danger:hover { background: #333; }";
        echo ".btn-secondary { background: white; color: #000; border: 1px solid #000; } .btn-secondary:hover { background: #f8f9fa; }";
        echo ".no-clases { text-align: center; color: #666; padding: 40px; background: #f8f9fa; border-radius: 6px; border: 1px solid #ddd; }";
        echo ".access-denied { text-align: center; color: #000; padding: 40px; background: #f8f9fa; border-radius: 6px; border: 1px solid #ddd; }";
        echo ".actions { text-align: center; margin: 30px 0; }";
        echo ".clase-meta p { margin: 5px 0; color: #333; }";
        
        // Responsive
        echo "@media (max-width: 768px) {";
        echo "  body { padding: 10px; }";
        echo "  .container { padding: 20px; }";
        echo "  .header { padding-bottom: 15px; }";
        echo "  .clase-card { padding: 15px; }";
        echo "  .btn { padding: 6px 12px; font-size: 0.8em; }";
        echo "}";
        
        echo "</style>";
    }
}