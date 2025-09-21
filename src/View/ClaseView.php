<?php
class ClaseView
{
    private $claseModel;
    private $message = '';
    private $messageType = '';

    private $asistenciaModel;



    public function __construct(ClaseModel $claseModel, AsistenciaModel $asistenciaModel)
    {
        $this->claseModel = $claseModel;
        $this->asistenciaModel = $asistenciaModel;
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

    public function render($grupo_id)
    {
        $data = $this->claseModel->mostrar($grupo_id);
        $asistenciaData = $this->asistenciaModel->obtener($grupo_id);
        echo "<!DOCTYPE html>";
        echo "<html lang='es'><head><title>Clases - Sistema de Asistencia</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: #f5f5f5; }";
        echo ".container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo ".header { text-align: center; color: #2c3e50; margin-bottom: 30px; }";
        echo ".success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".grupo-info { background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2196f3; }";
        echo ".clases-section { margin: 30px 0; }";

        // Estilos de las tarjetas de clase
        echo ".clase-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 15px 0; transition: all 0.3s ease; }";
        echo ".clase-card.asistencia-presente { background: #d4edda; border-color: #c3e6cb; border-left: 5px solid #28a745; }";
        echo ".clase-card.asistencia-ausente { background: #f8d7da; border-color: #f5c6cb; border-left: 5px solid #dc3545; }";
        echo ".clase-card h4 { color: #495057; margin: 0 0 10px 0; }";
        echo ".clase-meta { color: #6c757d; font-size: 0.9em; margin: 5px 0; }";

        // Indicador de asistencia
        echo ".asistencia-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; font-weight: bold; margin-left: 10px; }";
        echo ".badge-presente { background: #d4edda; color: #155724; }";
        echo ".badge-ausente { background: #f8d7da; color: #721c24; }";

        // Formulario de QR
        echo ".qr-form { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 10px 0; }";
        echo ".qr-input { width: 200px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-right: 10px; }";
        echo ".qr-scanner { background: #17a2b8; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px; }";
        echo ".qr-scanner:hover { background: #138496; }";
        echo ".qr-upload { background: #e3f2fd; border: 1px solid #bbdefb; border-radius: 8px; padding: 15px; margin: 10px 0; }";
        echo ".file-input { margin: 10px 0; }";
        echo ".upload-btn { background: #2196f3; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; }";
        echo ".upload-btn:hover { background: #1976d2; }";
        echo ".preview-image { max-width: 200px; max-height: 200px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }";

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
        echo ".no-clases { text-align: center; color: #6c757d; padding: 40px; background: #f8f9fa; border-radius: 8px; }";
        echo ".access-denied { text-align: center; color: #dc3545; padding: 40px; background: #f8d7da; border-radius: 8px; border: 1px solid #f5c6cb; }";
        echo ".role-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; font-weight: bold; }";
        echo ".role-profesor { background: #d4edda; color: #155724; }";
        echo ".role-estudiante { background: #cce7ff; color: #004085; }";
        echo ".role-admin { background: #f8d7da; color: #721c24; }";
        echo ".qr-code { background: #fff; border: 2px dashed #007bff; padding: 10px; border-radius: 8px; text-align: center; margin: 10px 0; }";

        // Estilos para crear clase
        echo ".crear-clase-form { background: #e3f2fd; border: 1px solid #bbdefb; border-radius: 8px; padding: 20px; margin: 20px 0; display: none; }";
        echo ".form-group { margin-bottom: 15px; }";
        echo ".form-control { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }";
        echo ".btn-crear-clase { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }";
        echo ".btn-crear-clase:hover { background: #218838; }";
        echo ".qr-display { background: #fff; border: 2px solid #28a745; padding: 15px; border-radius: 8px; text-align: center; margin: 10px 0; }";
        echo ".qr-codigo { font-family: monospace; font-size: 14px; background: #f8f9fa; padding: 8px; border-radius: 4px; margin: 5px 0; }";
        echo "</style>";


        echo "<script>";
        echo "
        // Función para mostrar/ocultar formulario de crear clase
        function mostrarFormularioCrearClase() {
            console.log('Ejecutando mostrarFormularioCrearClase');
            const form = document.getElementById('crear-clase-form');
            if (form) {
                form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
            } else {
                console.error('No se encontró el elemento crear-clase-form');
            }
        }

        // Función para crear clase
        function crearClase() {
            console.log('Ejecutando crearClase');
            const dia = document.getElementById('dia-clase').value;
            const horaInicio = document.getElementById('hora-inicio').value;
            const horaFin = document.getElementById('hora-fin').value;
            
            // Validaciones
            if (!dia) {
                alert('Por favor selecciona una fecha');
                return;
            }
            
            if (!horaInicio) {
                alert('Por favor selecciona la hora de inicio');
                return;
            }
            
            if (!horaFin) {
                alert('Por favor selecciona la hora de fin');
                return;
            }
            
            // Validar que la hora de fin sea posterior a la hora de inicio
            if (horaFin <= horaInicio) {
                alert('La hora de fin debe ser posterior a la hora de inicio');
                return;
            }
            
            const btn = document.getElementById('btn-crear-clase');
            const originalText = btn.textContent;
            btn.textContent = '⏳ Creando...';
            btn.disabled = true;
            
            // Agregar las horas al body del request
            fetch(window.location.pathname + window.location.search, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'evento=crear_clase&dia=' + encodeURIComponent(dia) + 
                      '&hora_inicio=' + encodeURIComponent(horaInicio) +
                      '&hora_fin=' + encodeURIComponent(horaFin)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Clase creada exitosamente. Código QR: ' + data.qr_codigo);
                    location.reload();
                } else {
                    alert('Error: ' + data.mensaje);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            })
            .finally(() => {
                btn.textContent = originalText;
                btn.disabled = false;
            });
        }

        // Función para registrar asistencia
         function registrarAsistencia(claseId, qrCode) {
            if (!qrCode.trim()) {
                alert('Por favor ingresa el código QR');
                return;
            }
            console.log('Registrando asistencia para claseId:', claseId, 'con QR:', qrCode);
            
            fetch(window.location.pathname + window.location.search, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'evento=registrarAsistencia&qr_codigo=' + encodeURIComponent(qrCode) + '&clase_id=' + claseId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.mensaje);
                    location.reload();
                } else {
                    alert('Error: ' + data.mensaje);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            });
        }
        // Función para ver asistencias
        function verAsistencias(claseId) {
            fetch(window.location.pathname + window.location.search, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'evento=obtener_asistencias&clase_id=' + claseId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarModalAsistencias(data.asistencias, data.clase);
                } else {
                    alert('Error: ' + data.mensaje);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            });
        }

        // Función para cerrar modal
        function cerrarModal() {
            const modal = document.querySelector('[style*=\"position: fixed\"]');
            if (modal) modal.remove();
        }
            // Función para mostrar/ocultar detalle de asistencias
function toggleDetalleAsistencias(claseId) {
    const detalle = document.getElementById('detalle-' + claseId);
    if (detalle) {
        detalle.style.display = detalle.style.display === 'none' ? 'block' : 'none';
    }
}

        // Verificar que el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado, funciones disponibles:', {
                mostrarFormularioCrearClase: typeof mostrarFormularioCrearClase,
                crearClase: typeof crearClase,
                registrarAsistencia: typeof registrarAsistencia
            });
        });
        
        ";


        echo "</script>";

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
            echo "<a href='grupo.php' class='btn btn-secondary'>🔙 Volver</a>";
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

        // Formulario para crear clase (solo para profesores)
        if ($data['rol'] === 'profesor') {
            echo "<div id='crear-clase-form' class='crear-clase-form'>";
            echo "<h4>➕ Crear Nueva Clase</h4>";

            echo "<div class='form-group'>";
            echo "<label for='dia-clase'>Fecha de la clase:</label>";
            echo "<input type='date' id='dia-clase' class='form-control' value='" . date('Y-m-d') . "'>";
            echo "</div>";

            echo "<div class='form-group'>";
            echo "<label for='hora-inicio'>Hora de inicio:</label>";
            echo "<input type='time' id='hora-inicio' class='form-control' value='" . date('H:i') . "'>";
            echo "</div>";

            echo "<div class='form-group'>";
            echo "<label for='hora-fin'>Hora de fin:</label>";
            echo "<input type='time' id='hora-fin' class='form-control' value='" . date('H:i', strtotime('+2 hours')) . "'>";
            echo "</div>";

            echo "<button id='btn-crear-clase' class='btn-crear-clase' onclick='crearClase()'>🎓 Crear Clase</button>";
            echo "<button class='btn btn-secondary' onclick='mostrarFormularioCrearClase()' style='margin-left: 10px;'>❌ Cancelar</button>";
            echo "</div>";
        }

        // Mostrar clases
        echo "<div class='clases-section'>";

        if (empty($data['clases'])) {
            echo "<div class='no-clases'>";
            echo "<h3>📅 Sin clases registradas</h3>";
            if ($data['rol'] === 'profesor') {
                echo "<p>No hay clases creadas para este grupo.</p>";
                echo "<button onclick='mostrarFormularioCrearClase()' class='btn btn-success'>➕ Crear Primera Clase</button>";
            } else {
                echo "<p>El profesor aún no ha creado clases para este grupo.</p>";
            }
            echo "</div>";

        } else {
            // Mostrar lista de clases
            echo "<h3>📅 Clases Registradas (" . count($data['clases']) . ")</h3>";




            // Botón para crear nueva clase (solo para profesores)
            if ($data['rol'] === 'profesor') {
                echo "<div style='margin-bottom: 20px;'>";
                echo "<button onclick='mostrarFormularioCrearClase()' class='btn btn-success'>➕ Crear Nueva Clase</button>";
                echo "</div>";
            }


            $asistenciasPorClase = [];
            $miAsistenciaPorClase = [];
            if (is_array($asistenciaData)) {
                $rolfor = $data['rol'];
                foreach ($asistenciaData as $asistencia) {
                    $clase_id = $asistencia['clase_id'];
                    echo "<script>";
                    echo "console.log('clase_id', " . json_encode($clase_id) . ");";
                    echo "</script>";
                    if ($rolfor=== 'profesor') {

                        if (!isset($asistenciasPorClase[$clase_id])) {
                            $asistenciasPorClase[$clase_id] = [];
                        }

                        $asistenciasPorClase[$clase_id][] = $asistencia;
                    } elseif ($rolfor === 'estudiante') {
                        // Para estudiante: solo su asistencia por clase
                        $miAsistenciaPorClase[$clase_id] = $asistencia;
                    }
                }
            }
            echo "<script>";
            echo "console.log('miasistenciaporclase', " . json_encode($asistenciaData) . ");";
            echo "</script>";

            foreach ($data['clases'] as $clase) {
                // Determinar el estilo de la tarjeta según la asistencia
                echo "<script>";
                echo "console.log('Renderizando clase ID: {$clase['id']} para rol: {$data['rol']}');";
                echo "</script>";
                $cardClass = "clase-card";
                $badgeClass = "";
                $badgeText = "";

                if ($data['rol'] === 'estudiante') {
                    // Usar datos de asistencia reales
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

                // Mostrar badge de asistencia para estudiantes
                if ($data['rol'] === 'estudiante') {
                    echo "<span class='asistencia-badge $badgeClass'>$badgeText</span>";
                }

                echo "</h4>";
                echo "<div class='clase-meta'>";
                echo "<p><strong>Fecha y hora:</strong> " . date('d/m/Y H:i:s', strtotime($clase['fecha'])) . "</p>";
                echo "<script>";
                echo "console.log('llega aca'); rol = '{$data['rol']}'; asistenciasPorClase = " . json_encode($asistenciasPorClase) . ";";
                echo "</script>";
                // Para profesor: mostrar lista de asistencias
                if ($data['rol'] === 'profesor' && isset($asistenciasPorClase[$clase['id']])) {

                    $asistencias = $asistenciasPorClase[$clase['id']];
                    $totalEstudiantes = count($asistencias);
                    $presentes = count(array_filter($asistencias, fn($a) => $a['tipo'] === 'presente'));
                    $retrasos = count(array_filter($asistencias, fn($a) => $a['tipo'] === 'retraso'));
                    $ausentes = count(array_filter($asistencias, fn($a) => $a['tipo'] === 'ausente'));

                    echo "<div class='asistencias-resumen' style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                    echo "<h5>📊 Resumen de Asistencias</h5>";
                    echo "<p><strong>Total estudiantes:</strong> $totalEstudiantes</p>";
                    echo "<p><strong>Presentes:</strong> <span style='color: #28a745;'>$presentes</span> | ";
                    echo "<strong>Retrasos:</strong> <span style='color: #ffc107;'>$retrasos</span> | ";
                    echo "<strong>Ausentes:</strong> <span style='color: #dc3545;'>$ausentes</span></p>";



                    // Botón para ver detalle
                    echo "<button onclick='toggleDetalleAsistencias({$clase['id']})' class='btn btn-sm' style='font-size: 0.8em;'>👁️ Ver Detalle</button>";

                    // Lista detallada (inicialmente oculta)
                    echo "<div id='detalle-{$clase['id']}' style='display: none; margin-top: 10px;'>";
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
                    echo "</div>";
                }

                // Mostrar código QR si existe
                if ($clase['qr'] && $data['rol'] === 'profesor') {
                    echo "<div class='qr-code'>";
                    echo "<p><strong>🔗 Código QR:</strong> {$clase['qr']}</p>";
                    echo "<small>Los estudiantes pueden usar este código para registrar asistencia</small>";
                    echo "</div>";
                }

                echo "</div>";

                // Botones según el rol
                echo "<div style='margin-top: 15px;'>";

                if ($data['rol'] === 'profesor') {
                    // Botones para profesores
                    echo "<a href='editar-clase.php?clase_id={$clase['id']}' class='btn btn-warning'>✏️ Editar</a>";
                    echo "<a href='eliminar-clase.php?clase_id={$clase['id']}' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de eliminar esta clase?\")'>🗑️ Eliminar</a>";

                } elseif ($data['rol'] === 'estudiante') {
                    // Para estudiante: verificar si puede marcar asistencia
                    $miAsistencia = $miAsistenciaPorClase[$clase['id']] ?? null;
                    $puedeMarcar = !$miAsistencia || $miAsistencia['tipo'] === 'ausente';

                    if ($puedeMarcar && $clase['qr']) {
                        echo "<div class='qr-form'>";
                        echo "<h5>📱 Registrar Asistencia con Código</h5>";
                        echo "<div style='margin-bottom: 15px;'>";
                        echo "<label><strong>Escribir código:</strong></label>";
                        echo "<div>";
                        echo "<input type='text' id='qr_{$clase['id']}' class='qr-input' placeholder='Ingresa código' maxlength='100'>";
                        echo "<button class='btn btn-success' onclick='registrarAsistencia({$clase['id']}, document.getElementById(\"qr_{$clase['id']}\").value)'>✅ Marcar Asistencia</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    } elseif ($miAsistencia && $miAsistencia['tipo'] !== 'ausente') {
                        echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; color: #155724;'>";
                        echo "✅ <strong>Asistencia registrada como: {$miAsistencia['tipo']}</strong>";
                        if ($miAsistencia['hora_inicio']) {
                            echo "<br><small>Hora: {$miAsistencia['hora_inicio']}</small>";
                        }
                        echo "</div>";
                    }
                }

                echo "</div>";
                echo "</div>";
            }



        }
        echo "</div>";

        echo "</div>";

        echo "</div>";
        echo "</body></html>";
    }
}