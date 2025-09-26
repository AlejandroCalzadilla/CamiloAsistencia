<?php

require_once __DIR__ . '/../View/interfaces/View.php';
class UsuarioView implements View
{
    private $model;
    private $message = '';

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    public function showErrorMessage($msg)
    {
        $this->message = $msg;
    }
    public function showMessage($msg)
    {
        $this->message = $msg;
    }

    public function actualizar()
    {

        $usuarios = $this->model->obtenerTodos();
        $this->render($usuarios);

    }


    public function render($usuarios)
    {
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Gestión de Usuarios</title>";
        $this->renderCSS();
        echo "</head>";
        echo "<body>";

        echo "<div class='container'>";
        echo "<div class='header'>";
        echo "<h2>👥 Gestión de Usuarios</h2>";
        echo "</div>";

        echo "<div class='content'>";

        // Mostrar mensajes
        if ($this->message) {
            echo "<div class='message'>{$this->message}</div>";
        }
         echo "<div style='margin: 20px 0;'>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='evento' value='volver_grupos'>";
        echo "<button type='submit' class='btn btn-secondary'>🔙 Volver a Grupos</button>";
        echo "</form>";
        echo "</div>";
        // Formulario de creación
        echo "<div class='form-section'>";
        echo "<h3>➕ Agregar Nuevo Usuario</h3>";
        echo "<form method='POST'>";
        echo "<div class='form-row'>";
        echo "<div class='form-group'>";
        echo "<label>Nombre de Usuario:</label>";
        echo "<input type='text' name='nombre' placeholder='Ingrese nombre' required class='form-control'>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label>Contraseña:</label>";
        echo "<input type='password' name='contrasena' placeholder='Ingrese contraseña' required class='form-control'>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<button type='submit' name='evento' value='crear' class='btn btn-success'>✅ Crear Usuario</button>";
        echo "</div>";
        echo "</div>";
        echo "</form>";
        echo "</div>";

        // Tabla de usuarios
        echo "<div class='table-section'>";
        echo "<div class='table-header'>";
        echo "<h3>📋 Lista de Usuarios</h3>";
        echo "</div>";

        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>NOMBRE</th>";
        echo "<th>CREADO</th>";
        echo "<th>ACTUALIZADO</th>";
        echo "<th>ACCIONES</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($usuarios as $u) {
            echo "<tr>";
            echo "<form method='POST'>";
            echo "<td class='id-cell'>{$u['id']}</td>";
            echo "<td class='name-cell'><input type='text' name='nombre' value='{$u['nombre']}' class='form-control'></td>";
            echo "<td class='date-cell'>{$u['creado_en']}</td>";
            echo "<td class='date-cell'>{$u['actualizado_en']}</td>";
            echo "<td class='actions-cell'>";
            echo "<input type='hidden' name='id' value='{$u['id']}'>";
            echo "<input type='password' name='contrasena' placeholder='Nueva contraseña' class='form-control' style='width: 120px; display: inline-block; margin-right: 5px;'>";
            echo "<button type='submit' name='evento' value='editar' class='btn btn-warning btn-sm'>✏️ Editar</button>";
            echo "<button type='submit' name='evento' value='eliminar' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Eliminar usuario?\")'>🗑️ Eliminar</button>";
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
        echo ".container { max-width: 1200px; margin: 0 auto; background: white; border: 1px solid #ddd; border-radius: 8px; }";
        echo ".header { background: white; border-bottom: 2px solid #000; padding: 25px; text-align: center; }";
        echo ".header h2 { margin: 0; color: #000; font-size: 24px; font-weight: 600; }";
        echo ".content { padding: 30px; }";

        // Mensajes
        echo ".message { background: #f8f9fa; color: #000; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; }";
        echo ".error-message { background: #f8f9fa; color: #000; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; }";

        // Formulario
        echo ".form-section { background: white; border: 1px solid #ddd; padding: 25px; border-radius: 6px; margin-bottom: 30px; }";
        echo ".form-section h3 { margin: 0 0 20px 0; color: #000; font-size: 18px; font-weight: 600; }";
        echo ".form-row { display: flex; gap: 15px; align-items: end; }";
        echo ".form-group { flex: 1; }";
        echo ".form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #000; }";
        echo ".form-control { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; background: white; color: #000; }";
        echo ".form-control:focus { outline: none; border-color: #000; }";

        // Botones
        echo ".btn { padding: 10px 20px; border: none; border-radius: 4px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }";
        echo ".btn-success { background: #000; color: white; }";
        echo ".btn-success:hover { background: #333; }";
        echo ".btn-warning { background: white; color: #000; border: 1px solid #000; }";
        echo ".btn-warning:hover { background: #f8f9fa; }";
        echo ".btn-danger { background: #000; color: white; }";
        echo ".btn-danger:hover { background: #333; }";
        echo ".btn-sm { padding: 6px 12px; font-size: 12px; }";

        // Tabla
        echo ".table-section { background: white; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; }";
        echo ".table-header { background: white; padding: 20px; border-bottom: 1px solid #ddd; }";
        echo ".table-header h3 { margin: 0; color: #000; font-size: 18px; font-weight: 600; }";

        echo "table { width: 100%; border-collapse: collapse; margin: 0; background: white; }";
        echo "th { background: #f8f9fa; color: #000; padding: 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #ddd; }";
        echo "td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; background: white; }";
        echo "tr:hover { background: #fafafa; }";
        echo "tr:last-child td { border-bottom: none; }";

        // Celdas específicas
        echo ".id-cell { font-weight: 600; color: #000; font-family: monospace; }";
        echo ".name-cell input { border: none; background: transparent; font-weight: 500; color: #000; width: 100%; padding: 4px; }";
        echo ".name-cell input:focus { background: white; border: 1px solid #000; }";
        echo ".date-cell { color: #666; font-size: 12px; }";
        echo ".actions-cell { text-align: center; }";
        echo ".actions-cell form { display: inline-block; margin: 0 2px; }";
        echo ".actions-cell input[type='password'] { width: 100px; padding: 4px 6px; border: 1px solid #ddd; border-radius: 3px; font-size: 11px; margin-right: 5px; }";

        // Responsive
        echo "@media (max-width: 768px) {";
        echo "  .container { margin: 10px; border-radius: 6px; }";
        echo "  .header { padding: 15px; }";
        echo "  .content { padding: 15px; }";
        echo "  .form-row { flex-direction: column; }";
        echo "  .form-group { margin-bottom: 10px; }";
        echo "  table { font-size: 12px; }";
        echo "  th, td { padding: 6px 4px; }";
        echo "  .btn { padding: 6px 10px; font-size: 12px; }";
        echo "  .actions-cell input[type='password'] { width: 80px; margin-bottom: 5px; }";
        echo "}";

        echo "</style>";
    }
}