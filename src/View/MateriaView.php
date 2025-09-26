<?php
class MateriaView
{
    private MateriaModel $model;

    private ?string $message = null;
    private ?string $messageType = null; // 'success' o 'error' 

    public function __construct()
    {
        $this->model = new MateriaModel();
    }

    public function showMessage($message)
    {
        if ($message) {
            echo "<p style='color: green;'>$message</p>";
        }
    }

    public function actualizar()
    {
        $materias = $this->model->obtener();
        $this->render($materias);
    }

   public function render($materias)
    {
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Gestión de Materias</title>";
        $this->renderCSS();
        echo "</head>";
        echo "<body>";

        echo "<div class='container'>";
        echo "<div class='header'>";
        echo "<h2>📚 Gestión de Materias</h2>";
        echo "</div>";

        echo "<div class='content'>";

        // Mostrar mensajes
        if ($this->message) {
            $class = $this->messageType === 'success' ? 'message-success' : 'message-error';
            echo "<div class='$class'>{$this->message}</div>";
        }

        // Formulario de creación
        echo "<div style='margin: 20px 0;'>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='evento' value='volver_grupos'>";
        echo "<button type='submit' class='btn btn-secondary'>🔙 Volver a Grupos</button>";
        echo "</form>";
        echo "</div>";
        echo "<div class='form-section'>";
        echo "<h3>Agregar Nueva Materia</h3>";
        echo "<form method='POST'>";
        echo "<div class='form-row'>";
        echo "<input type='text' name='nombre' placeholder='Nombre de la materia' required class='form-control'>";
        echo "<button type='submit' name='evento' value='crear' class='btn-create'>Crear</button>";
        echo "</div>";
        echo "</form>";
        echo "</div>";

        // Tabla de materias
        echo "<div class='table-section'>";
        echo "<h3>Lista de Materias</h3>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>NOMBRE</th>";
        echo "<th>ACCIONES</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($materias as $m) {
            echo "<tr>";
            echo "<form method='POST'>";
            echo "<td class='id-cell'>{$m['id']}</td>";
            echo "<td><input type='text' name='nombre' value='{$m['nombre']}' class='form-control-sm'></td>";
            echo "<td class='actions-cell'>";
            echo "<input type='hidden' name='id' value='{$m['id']}'>";
            echo "<button type='submit' name='evento' value='editar' class='btn-edit'>Editar</button>";
            echo "<button type='submit' name='evento' value='eliminar' onclick='return confirm(\"¿Eliminar?\")' class='btn-delete'>Eliminar</button>";
            echo "</td>";
            echo "</form>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "</div>"; // content
        echo "</div>"; // container
        echo "</body>";
        echo "</html>";
    }

    private function renderCSS()
    {
        echo "<style>";
        echo "body { font-family: 'Segoe UI', Arial, sans-serif; background: white; margin: 0; padding: 20px; color: #333; }";
        echo ".container { max-width: 900px; margin: 0 auto; background: white; border: 1px solid #ddd; border-radius: 8px; }";
        echo ".header { background: white; border-bottom: 2px solid #000; padding: 25px; text-align: center; }";
        echo ".header h2 { margin: 0; color: #000; font-size: 24px; font-weight: 600; }";
        echo ".content { padding: 30px; }";
        
        // Mensajes
        echo ".message-success { background: #f8f9fa; color: #000; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; }";
        echo ".message-error { background: #f8f9fa; color: #000; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; }";
        
        // Formulario
        echo ".form-section { background: white; border: 1px solid #ddd; padding: 25px; border-radius: 6px; margin-bottom: 30px; }";
        echo ".form-section h3 { margin: 0 0 20px 0; color: #000; font-size: 18px; font-weight: 600; }";
        echo ".form-row { display: flex; gap: 15px; align-items: center; }";
        echo ".form-control { flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; background: white; color: #000; }";
        echo ".form-control:focus { outline: none; border-color: #000; }";
        echo ".form-control-sm { padding: 8px 10px; border: 1px solid #ddd; border-radius: 3px; font-size: 14px; background: white; color: #000; width: 100%; }";
        echo ".form-control-sm:focus { outline: none; border-color: #000; }";
        
        // Botones
        echo ".btn-create { background: #000; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; }";
        echo ".btn-create:hover { background: #333; }";
        echo ".btn-edit { background: white; color: #000; border: 1px solid #000; padding: 6px 12px; border-radius: 3px; cursor: pointer; font-size: 12px; margin-right: 5px; }";
        echo ".btn-edit:hover { background: #f8f9fa; }";
        echo ".btn-delete { background: #000; color: white; border: 1px solid #000; padding: 6px 12px; border-radius: 3px; cursor: pointer; font-size: 12px; }";
        echo ".btn-delete:hover { background: #333; }";
        
        // Tabla
        echo ".table-section { background: white; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; }";
        echo ".table-section h3 { margin: 0; padding: 20px; color: #000; font-size: 18px; font-weight: 600; border-bottom: 1px solid #ddd; background: white; }";
        echo "table { width: 100%; border-collapse: collapse; background: white; }";
        echo "th { background: #f8f9fa; color: #000; padding: 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #ddd; }";
        echo "td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; background: white; }";
        echo "tr:hover { background: #fafafa; }";
        echo ".id-cell { font-weight: 600; color: #000; font-family: monospace; width: 80px; }";
        echo ".actions-cell { text-align: center; white-space: nowrap; width: 200px; }";
        
        // Responsive
        echo "@media (max-width: 768px) {";
        echo "  .container { margin: 10px; border-radius: 6px; }";
        echo "  .header { padding: 15px; }";
        echo "  .content { padding: 15px; }";
        echo "  .form-row { flex-direction: column; }";
        echo "  .form-control { width: 100%; margin-bottom: 10px; }";
        echo "  table { font-size: 12px; }";
        echo "  th, td { padding: 8px 6px; }";
        echo "  .btn-edit, .btn-delete { padding: 4px 8px; font-size: 10px; }";
        echo "}";
        
        echo "</style>";
    }
}