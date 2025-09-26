<?php

class ProfesorView
{

  private $model;
  private $message = '';
  private $messageType = '';
  public function __construct()
  {
    $this->model = new ProfesorModel();
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

  public function actualizar()
  {
    $datos = $this->model->obtener();
    $this->render($datos);
  }


  public function render($datos)
  {
    $profesores = $datos['profesores'];
    $usuariosLibres = $datos['usuarios_libres'];

    echo "<!DOCTYPE html>";
    echo "<html lang='es'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Gestión de Profesores</title>";
    $this->renderCSS();
    echo "</head>";
    echo "<body>";

    echo "<div class='container'>";
    echo "<div class='header'>";
    echo "<h2>👨‍🏫 Gestión de Profesores</h2>";
    echo "</div>";

    echo "<div class='content'>";

    // Mostrar mensajes
    if ($this->message) {
      $class = $this->messageType === 'success' ? 'message-success' : 'message-error';
      echo "<div class='$class'>{$this->message}</div>";
    }
    echo "<div style='margin: 20px 0;'>";
    echo "<form method='POST' style='display: inline;'>";
    echo "<input type='hidden' name='evento' value='volver_grupos'>";
    echo "<button type='submit' class='btn btn-secondary'>🔙 Volver a Grupos</button>";
    echo "</form>";
    echo "</div>";
    // Formulario de creación
    echo "<div class='form-section'>";
    echo "<h3>Agregar Nuevo Profesor</h3>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='evento' value='crear'>";
    echo "<div class='form-row'>";
    echo "<input type='text' name='codigo' placeholder='Código' required class='form-control'>";
    echo "<input type='text' name='nombres' placeholder='Nombres' required class='form-control'>";
    echo "<input type='text' name='apellidos' placeholder='Apellidos' required class='form-control'>";
    echo "<select name='genero' required class='form-control'>";
    echo "<option value=''>-- Selecciona género --</option>";
    echo "<option value='m'>Masculino</option>";
    echo "<option value='f'>Femenino</option>";
    echo "</select>";
    echo "<select name='usuario_id' required class='form-control'>";
    echo "<option value=''>-- Selecciona usuario --</option>";
    foreach ($usuariosLibres as $u) {
      echo "<option value='{$u['id']}'>{$u['nombre']}</option>";
    }
    echo "</select>";
    echo "<button type='submit' class='btn-create'>Crear</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";

    // Tabla de profesores
    echo "<div class='table-section'>";
    echo "<h3>Lista de Profesores</h3>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>CÓDIGO</th>";
    echo "<th>NOMBRES</th>";
    echo "<th>APELLIDOS</th>";
    echo "<th>GÉNERO</th>";
    echo "<th>USUARIO</th>";
    echo "<th>ACCIONES</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($profesores as $p) {
      echo "<tr>";
      echo "<form method='POST'>";
      echo "<td><input type='text' name='codigo' value='{$p['codigo']}' readonly class='form-control-sm'></td>";
      echo "<td><input type='text' name='nombres' value='{$p['nombres']}' class='form-control-sm'></td>";
      echo "<td><input type='text' name='apellidos' value='{$p['apellidos']}' class='form-control-sm'></td>";
      echo "<td><select name='genero' required class='form-control-sm'>";
      echo "<option value='m'" . ($p['genero'] === 'm' ? ' selected' : '') . ">Masculino</option>";
      echo "<option value='f'" . ($p['genero'] === 'f' ? ' selected' : '') . ">Femenino</option>";
      echo "</select></td>";
      echo "<td><select name='usuario_id' required class='form-control-sm'>";
      echo "<option value='{$p['usuario_id']}' selected>Usuario actual ({$p['usuario_nombre']})</option>";
      foreach ($usuariosLibres as $u) {
        if ($u['id'] != $p['usuario_id']) {
          echo "<option value='{$u['id']}'>{$u['nombre']}</option>";
        }
      }
      echo "</select></td>";
      echo "<td class='actions-cell'>";
      echo "<input type='hidden' name='evento' value='editar'>";
      echo "<button type='submit' class='btn-edit'>Editar</button>";
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
    echo ".container { max-width: 1200px; margin: 0 auto; background: white; border: 1px solid #ddd; border-radius: 8px; }";
    echo ".header { background: white; border-bottom: 2px solid #000; padding: 25px; text-align: center; }";
    echo ".header h2 { margin: 0; color: #000; font-size: 24px; font-weight: 600; }";
    echo ".content { padding: 30px; }";

    // Mensajes
    echo ".message-success { background: #f8f9fa; color: #000; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; }";
    echo ".message-error { background: #f8f9fa; color: #000; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; }";

    // Formulario
    echo ".form-section { background: white; border: 1px solid #ddd; padding: 25px; border-radius: 6px; margin-bottom: 30px; }";
    echo ".form-section h3 { margin: 0 0 20px 0; color: #000; font-size: 18px; font-weight: 600; }";
    echo ".form-row { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }";
    echo ".form-control { padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; background: white; color: #000; }";
    echo ".form-control:focus { outline: none; border-color: #000; }";
    echo ".form-control-sm { padding: 6px 8px; border: 1px solid #ddd; border-radius: 3px; font-size: 13px; background: white; color: #000; width: 100%; }";
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
    echo "td { padding: 8px; border-bottom: 1px solid #eee; vertical-align: middle; background: white; }";
    echo "tr:hover { background: #fafafa; }";
    echo ".actions-cell { text-align: center; white-space: nowrap; }";

    // Responsive
    echo "@media (max-width: 768px) {";
    echo "  .container { margin: 10px; border-radius: 6px; }";
    echo "  .header { padding: 15px; }";
    echo "  .content { padding: 15px; }";
    echo "  .form-row { flex-direction: column; }";
    echo "  .form-control { width: 100%; margin-bottom: 10px; }";
    echo "  table { font-size: 12px; }";
    echo "  th, td { padding: 6px 4px; }";
    echo "}";

    echo "</style>";
  }


}