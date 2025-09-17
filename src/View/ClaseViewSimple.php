<?php
class ClaseViewSimple {
    private $claseModel;
    private $message = '';
    private $messageType = '';

    public function __construct(ClaseModel $claseModel) {
        $this->claseModel = $claseModel;
    }

    public function showSuccessMessage($message) {
        $this->message = $message;
        $this->messageType = 'success';
    }

    public function showErrorMessage($message) {
        $this->message = $message;
        $this->messageType = 'error';
    }

    public function render($grupo_id) {
        $data = $this->claseModel->mostrar($grupo_id);
     
        echo "<!DOCTYPE html>";
        echo "<html lang='es'><head><title>Clases - Sistema de Asistencia</title>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        
        // CSS
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
        echo ".btn { background: #007bff; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; margin: 2px; }";
        echo ".btn:hover { background: #0056b3; }";
        echo ".btn-success { background: #28a745; }";
        echo ".btn-success:hover { background: #1e7e34; }";
        echo ".btn-warning { background: #ffc107; color: #212529; }";
        echo ".btn-warning:hover { background: #e0a800; }";
        echo ".btn-secondary { background: #6c757d; }";
        echo ".btn-secondary:hover { background: #545b62; }";
        
        // Estilos para imagen preview
        echo ".preview-image { max-width: 200px; max-height: 200px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }";
        echo ".file-input { margin: 10px 0; }";
        echo ".qr-methods { display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start; }";
        echo ".qr-method { flex: 1; min-width: 300px; }";
        echo "</style>";
        
        // JavaScript libraries y funciones
        echo "<script src='https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js'></script>";
        echo "<script src='qr-reader.js'></script>";
        
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

        // Información del grupo
        echo "<div class='grupo-info'>";
        echo "<h3>📋 Información del Grupo</h3>";
        echo "<p><strong>Nombre:</strong> {$data['grupo']['nombre']}</p>";
        echo "<p><strong>Descripción:</strong> {$data['grupo']['descripcion']}</p>";
        echo "<p><strong>Usuario:</strong> {$data['usuario']['nombre']} ({$data['rol']})</p>";
        echo "</div>";

        // Sección de clases
        echo "<div class='clases-section'>";
        echo "<h3>📅 Clases Programadas</h3>";

        if (empty($data['clases'])) {
            echo "<div class='no-clases'>";
            echo "<p>📭 No hay clases programadas para este grupo.</p>";
            if ($data['rol'] === 'profesor') {
                echo "<p>Puedes crear nuevas clases desde el panel de administración.</p>";
            }
            echo "</div>";
        } else {
            foreach ($data['clases'] as $clase) {
                $asistenciaClass = '';
                $asistenciaBadge = '';
                
                if ($data['rol'] === 'estudiante' && isset($clase['asistencia'])) {
                    if ($clase['asistencia'] === 'presente') {
                        $asistenciaClass = 'asistencia-presente';
                        $asistenciaBadge = '<span class="asistencia-badge badge-presente">✅ Presente</span>';
                    } else {
                        $asistenciaClass = 'asistencia-ausente';
                        $asistenciaBadge = '<span class="asistencia-badge badge-ausente">❌ Ausente</span>';
                    }
                }

                echo "<div class='clase-card $asistenciaClass'>";
                echo "<h4>📖 {$clase['titulo']} $asistenciaBadge</h4>";
                echo "<div class='clase-meta'>📅 Fecha: {$clase['fecha_clase']}</div>";
                echo "<div class='clase-meta'>⏰ Hora: {$clase['hora_inicio']} - {$clase['hora_fin']}</div>";
                echo "<div class='clase-meta'>📍 Lugar: {$clase['lugar']}</div>";
                echo "<div class='clase-meta'>👨‍🏫 Profesor: {$clase['profesor_nombre']}</div>";
                
                if (!empty($clase['descripcion'])) {
                    echo "<div class='clase-meta'>📝 {$clase['descripcion']}</div>";
                }

                // Formulario de QR solo para estudiantes y si aún no han registrado asistencia
                if ($data['rol'] === 'estudiante' && (!isset($clase['asistencia']) || $clase['asistencia'] !== 'presente')) {
                    echo "<div class='qr-form'>";
                    echo "<h5>📱 Registrar Asistencia con Código QR</h5>";
                    
                    echo "<div class='qr-methods'>";
                    
                    // Método 1: Texto QR
                    echo "<div class='qr-method'>";
                    echo "<h6>✍️ Ingresar código manualmente:</h6>";
                    echo "<input type='text' id='qr_texto_{$clase['id']}' class='qr-input' placeholder='Código QR'>";
                    echo "<button class='btn btn-success' onclick='registrarAsistencia({$clase['id']}, document.getElementById(\"qr_texto_{$clase['id']}\").value)'>✅ Registrar</button>";
                    echo "</div>";
                    
                    // Método 2: Imagen QR
                    echo "<div class='qr-method'>";
                    echo "<h6>📷 Subir imagen con QR:</h6>";
                    echo "<input type='file' id='qr_imagen_{$clase['id']}' accept='image/*' class='file-input' onchange='previsualizarImagen(this, {$clase['id']})'>";
                    echo "<br><button class='btn btn-success' onclick='procesarQRConJavaScript({$clase['id']}, document.getElementById(\"qr_imagen_{$clase['id']}\"))'>🔍 Leer QR y Registrar</button>";
                    echo "</div>";
                    
                    echo "</div>";
                    echo "</div>";
                }

                echo "</div>";
            }
        }
        echo "</div>";

        // Botones de navegación
        echo "<div class='actions' style='text-align: center; margin-top: 30px;'>";
        echo "<a href='grupo.php' class='btn btn-secondary'>🔙 Volver a Grupos</a>";
        echo "</div>";

        echo "</div></body></html>";
    }
}
?>
