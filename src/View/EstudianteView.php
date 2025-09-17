<?php
class EstudianteView {
    private $estudiantemodel;
    private $message = '';
    private $messageType = '';

     
    public function __construct(Estudiante $estudiante) {
        $this->estudiantemodel = $estudiante;
    }

    public function setModel(Estudiante $model) {
        $this->estudiantemodel = $model;
    }

    public function showSuccessMessage($message) {
        $this->message = $message;
        $this->messageType = 'success';
    }

    public function showErrorMessage($message) {
        $this->message = $message;
        $this->messageType = 'error';
    }

    public function render() {
        if (!$this->estudiantemodel) {
            echo "<p style='color: red;'>Error: No se ha configurado el modelo</p>";
            return;
        }

        $data = $this->estudiantemodel->mostrar();
        
        echo "<!DOCTYPE html>";
        echo "<html><head><title>Gestión de Estudiantes</title>";
        echo "<style>";
        echo ".container { max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; }";
        echo ".success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".form-group { margin: 10px 0; }";
        echo ".form-group label { display: block; margin-bottom: 5px; font-weight: bold; }";
        echo ".form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }";
        echo ".btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }";
        echo ".btn:hover { background: #005a87; }";
        echo ".student-info { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0; }";
        echo "</style></head><body>";
        
        echo "<div class='container'>";
        echo "<h1>Gestión de Estudiantes - Patrón MVC</h1>";

        // Mostrar mensajes
        if ($this->message) {
            $class = $this->messageType === 'success' ? 'success' : 'error';
            echo "<div class='$class'>{$this->message}</div>";
        }

        // Mostrar información del estudiante
        echo "<div class='student-info'>";
        echo "<h2>Información del Estudiante</h2>";
        echo "<p><strong>Código:</strong> {$data['codigo']}</p>";
        echo "<p><strong>Nombres:</strong> {$data['nombres']}</p>";
        echo "<p><strong>Apellidos:</strong> {$data['apellidos']}</p>";
        echo "<p><strong>Estado:</strong> {$data['estado']}</p>";
        echo "<p><strong>Creado en:</strong> {$data['creado_en']}</p>";
        echo "<p><strong>Actualizado en:</strong> {$data['actualizado_en']}</p>";
        echo "</div>";

        // Formulario de actualización
        echo "<h3>Actualizar Estudiante</h3>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='evento' value='actualizar'>";
        
        echo "<div class='form-group'>";
        echo "<label for='nombres'>Nombres:</label>";
        echo "<input type='text' id='nombres' name='nombres' value='{$data['nombres']}' required>";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label for='apellidos'>Apellidos:</label>";
        echo "<input type='text' id='apellidos' name='apellidos' value='{$data['apellidos']}' required>";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label for='estado'>Estado:</label>";
        echo "<select id='estado' name='estado'>";
        echo "<option value='activo'" . ($data['estado'] === 'activo' ? ' selected' : '') . ">Activo</option>";
        echo "<option value='inactivo'" . ($data['estado'] === 'inactivo' ? ' selected' : '') . ">Inactivo</option>";
        echo "</select>";
        echo "</div>";
        
        echo "<button type='submit' class='btn'>Actualizar Estudiante</button>";
        echo "</form>";
        
        echo "</div>";
        echo "</body></html>";
    }
}
