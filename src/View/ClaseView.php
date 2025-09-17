<?php
class ClaseView {
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
        echo "</style>";
        
        // JavaScript para manejo de QR
        echo "<script>";
        echo "function registrarAsistencia(claseId, qrCode) {";
        echo "  if (!qrCode.trim()) {";
        echo "    alert('Por favor ingresa el código QR');";
        echo "    return;";
        echo "  }";
        echo "  ";
        echo "  fetch('procesar-qr.php', {";
        echo "    method: 'POST',";
        echo "    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },";
        echo "    body: 'qr_codigo=' + encodeURIComponent(qrCode) + '&clase_id=' + claseId";
        echo "  })";
        echo "  .then(response => response.json())";
        echo "  .then(data => {";
        echo "    if (data.success) {";
        echo "      alert(data.mensaje);";
        echo "      location.reload();";
        echo "    } else {";
        echo "      alert('Error: ' + data.mensaje);";
        echo "    }";
        echo "  })";
        echo "  .catch(error => {";
        echo "    alert('Error de conexión');";
        echo "  });";
        echo "}";
        echo "";
        echo "function subirImagenQR(claseId, fileInput) {";
        echo "  if (!fileInput.files || fileInput.files.length === 0) {";
        echo "    alert('Por favor selecciona una imagen');";
        echo "    return;";
        echo "  }";
        echo "  ";
        echo "  const file = fileInput.files[0];";
        echo "  if (!file.type.startsWith('image/')) {";
        echo "    alert('Por favor selecciona solo archivos de imagen');";
        echo "    return;";
        echo "  }";
        echo "  ";
        echo "  const formData = new FormData();";
        echo "  formData.append('qr_imagen', file);";
        echo "  formData.append('clase_id', claseId);";
        echo "  ";
        echo "  const uploadBtn = document.getElementById('upload_btn_' + claseId);";
        echo "  const originalText = uploadBtn.textContent;";
        echo "  uploadBtn.textContent = '⏳ Procesando...';";
        echo "  uploadBtn.disabled = true;";
        echo "  ";
        echo "  fetch('procesar-qr.php', {";
        echo "    method: 'POST',";
        echo "    body: formData";
        echo "  })";
        echo "  .then(response => response.json())";
        echo "  .then(data => {";
        echo "    if (data.success) {";
        echo "      alert(data.mensaje);";
        echo "      location.reload();";
        echo "    } else {";
        echo "      alert('Error: ' + data.mensaje);";
        echo "    }";
        echo "  })";
        echo "  .catch(error => {";
        echo "    alert('Error de conexión');";
        echo "  })";
        echo "  .finally(() => {";
        echo "    uploadBtn.textContent = originalText;";
        echo "    uploadBtn.disabled = false;";
        echo "  });";
        echo "}";
        echo "";
        echo "function previsualizarImagen(input, claseId) {";
        echo "  if (input.files && input.files[0]) {";
        echo "    const reader = new FileReader();";
        echo "    reader.onload = function(e) {";
        echo "      let preview = document.getElementById('preview_' + claseId);";
        echo "      if (!preview) {";
        echo "        preview = document.createElement('img');";
        echo "        preview.id = 'preview_' + claseId;";
        echo "        preview.className = 'preview-image';";
        echo "        input.parentNode.appendChild(preview);";
        echo "      }";
        echo "      preview.src = e.target.result;";
        echo "    };";
        echo "    reader.readAsDataURL(input.files[0]);";
        echo "  }";
        echo "}";
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
            echo "<p><strong>Capacidad:</strong> {$data['grupo']['capacidad']} estudiantes</p>";
            echo "<p><strong>Inscritos:</strong> {$data['grupo']['estudiantes_inscritos']} estudiantes</p>";
            
            $rol = $data['rol'];
            $roleClass = "role-" . $rol;
            $roleText = ucfirst($rol);
            echo "<p><strong>Tu rol:</strong> <span class='role-badge $roleClass'>$roleText</span></p>";
            echo "</div>";
        }

        // Mostrar clases
        echo "<div class='clases-section'>";
        
        if (empty($data['clases'])) {
            echo "<div class='no-clases'>";
            echo "<h3>📅 Sin clases registradas</h3>";
            if ($data['rol'] === 'profesor') {
                echo "<p>No hay clases creadas para este grupo.</p>";
                echo "<a href='crear-clase.php?grupo_id={$grupo_id}' class='btn btn-success'>➕ Crear Primera Clase</a>";
            } else {
                echo "<p>El profesor aún no ha creado clases para este grupo.</p>";
            }
            echo "</div>";
            
        } else {
            // Mostrar lista de clases
            echo "<h3>📅 Clases Registradas (" . count($data['clases']) . ")</h3>";
            
            foreach ($data['clases'] as $clase) {
                // Determinar el estilo de la tarjeta según la asistencia
                $cardClass = "clase-card";
                $badgeClass = "";
                $badgeText = "";
                
                if ($data['rol'] === 'estudiante') {
                    if ($clase['mi_asistencia'] == 1) {
                        $cardClass .= " asistencia-presente";
                        $badgeClass = "badge-presente";
                        $badgeText = "✅ Presente";
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
                echo "<p><strong>Asistencias registradas:</strong> {$clase['asistencias_registradas']} estudiantes</p>";
                
                // Mostrar código QR si existe
                if ($clase['qr']) {
                    echo "<div class='qr-code'>";
                    echo "<p><strong>🔗 Código QR:</strong> {$clase['qr']}</p>";
                    if ($data['rol'] === 'profesor') {
                        echo "<small>Los estudiantes pueden usar este código para registrar asistencia</small>";
                    }
                    echo "</div>";
                }
                
                echo "</div>";
                
                // Botones según el rol
                echo "<div style='margin-top: 15px;'>";
                
                if ($data['rol'] === 'profesor') {
                    // Botones para profesores
                    echo "<a href='ver-asistencias.php?clase_id={$clase['id']}' class='btn'>👥 Ver Asistencias</a>";
                    echo "<a href='tomar-asistencia.php?clase_id={$clase['id']}' class='btn btn-success'>📋 Tomar Asistencia</a>";
                    echo "<a href='generar-qr.php?clase_id={$clase['id']}' class='btn btn-warning'>🔗 Generar QR</a>";
                    echo "<a href='editar-clase.php?clase_id={$clase['id']}' class='btn btn-warning'>✏️ Editar</a>";
                    echo "<a href='eliminar-clase.php?clase_id={$clase['id']}' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de eliminar esta clase?\")'>🗑️ Eliminar</a>";
                    
                } elseif ($data['rol'] === 'estudiante') {
                    // Botones para estudiantes
                    echo "<a href='ver-mi-asistencia.php?clase_id={$clase['id']}' class='btn'>📊 Mi Asistencia</a>";
                    
                    // Solo mostrar opción de registrar asistencia si no ha asistido y hay QR
                    if ($clase['mi_asistencia'] == 0 && $clase['qr']) {
                        echo "<div class='qr-form'>";
                        echo "<h5>📱 Registrar Asistencia con QR</h5>";
                        
                        // Opción 1: Ingresar código manualmente
                        echo "<div style='margin-bottom: 15px;'>";
                        echo "<label><strong>Opción 1: Escribir código QR</strong></label>";
                        echo "<div>";
                        echo "<input type='text' id='qr_{$clase['id']}' class='qr-input' placeholder='Ingresa código QR' maxlength='100'>";
                        echo "<button class='btn btn-success' onclick='registrarAsistencia({$clase['id']}, document.getElementById(\"qr_{$clase['id']}\").value)'>✅ Marcar Asistencia</button>";
                        echo "</div>";
                        echo "</div>";
                        
                        // Opción 2: Subir imagen con QR
                        echo "<div class='qr-upload'>";
                        echo "<label><strong>Opción 2: Subir imagen con código QR</strong></label>";
                        echo "<div class='file-input'>";
                        echo "<input type='file' id='qr_file_{$clase['id']}' accept='image/*' onchange='previsualizarImagen(this, {$clase['id']})'>";
                        echo "<button id='upload_btn_{$clase['id']}' class='upload-btn' onclick='subirImagenQR({$clase['id']}, document.getElementById(\"qr_file_{$clase['id']}\"))'>� Subir y Procesar</button>";
                        echo "</div>";
                        echo "<small style='color: #666;'>Formatos soportados: PNG, JPG, JPEG</small>";
                        echo "</div>";
                        
                        echo "</div>";
                    } elseif ($clase['mi_asistencia'] == 1) {
                        echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; color: #155724;'>";
                        echo "✅ <strong>Asistencia registrada correctamente</strong>";
                        echo "</div>";
                    }
                }
                
                echo "</div>";
                echo "</div>";
            }
        }
        
        echo "</div>";

        // Acciones globales según el rol
        echo "<div class='actions'>";
        
        if ($data['rol'] === 'profesor') {
            echo "<a href='crear-clase.php?grupo_id={$grupo_id}' class='btn btn-success'>➕ Crear Nueva Clase</a>";
            echo "<a href='reporte-asistencias.php?grupo_id={$grupo_id}' class='btn btn-warning'>📊 Reporte General</a>";
        } elseif ($data['rol'] === 'estudiante') {
            echo "<a href='mis-asistencias-grupo.php?grupo_id={$grupo_id}' class='btn btn-success'>📊 Mis Asistencias</a>";
        }
        
        echo "<a href='grupo.php' class='btn btn-secondary'>🔙 Volver a Grupos</a>";
        echo "</div>";
        
        echo "</div>";
        echo "</body></html>";
    }
}