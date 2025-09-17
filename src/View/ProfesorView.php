<?php

class ProfesorView{


   private $model;



   public function __construct(ProfesorModel $model){

     $this->model=$model;

   }



  public function render($profesores = null, $message = '', $messageType = ''){
    if ($profesores === null) {
      $profesores = $this->model->obtener();
    }
    $usuariosLibres = $this->model->obtenerUsuariosLibres();

    echo "<!DOCTYPE html><html><head><title>Profesores</title></head><body>";
    echo "<h2>Profesores</h2>";
    if ($message) {
      $class = $messageType === 'success' ? 'success' : 'error';
      echo "<div class='$class'>{$message}</div>";
    }
    // Formulario de creación
    echo "<form method='POST'>
        <input type='hidden' name='evento' value='crear'>
        <input type='text' name='codigo' placeholder='Código' required>
        <input type='text' name='nombres' placeholder='Nombres' required>
        <input type='text' name='apellidos' placeholder='Apellidos' required>
        <select name='genero' required>
          <option value=''>-- Selecciona género --</option>
          <option value='m'>Masculino</option>
          <option value='f'>Femenino</option>
        </select>
        <select name='usuario_id' required>
          <option value=''>-- Selecciona usuario --</option>";
    foreach ($usuariosLibres as $u) {
      echo "<option value='{$u['id']}'>{$u['nombre']}</option>";
    }
    echo "</select>
        <button type='submit'>Crear</button>
        </form>";
    // Tabla de profesores
    echo "<table border='1' cellpadding='5'><tr><th>Código</th><th>Nombres</th><th>Apellidos</th><th>Género</th><th>Usuario</th><th>Acciones</th></tr>";
    foreach ($profesores as $p) {
      echo "<tr>
          <form method='POST'>
          <td><input type='text' name='codigo' value='{$p['codigo']}' readonly></td>
          <td><input type='text' name='nombres' value='{$p['nombres']}'></td>
          <td><input type='text' name='apellidos' value='{$p['apellidos']}'></td>
          <td><select name='genero' required>";
      echo "<option value='m'" . ($p['genero'] === 'm' ? ' selected' : '') . ">Masculino</option>";
      echo "<option value='f'" . ($p['genero'] === 'f' ? ' selected' : '') . ">Femenino</option>";
      echo "</select></td>";
      echo "<td><select name='usuario_id' required>";
      // Mostrar el usuario actual
      echo "<option value='{$p['usuario_id']}' selected>Usuario actual ({$p['usuario_id']})</option>";
      // Mostrar los usuarios libres
      foreach ($usuariosLibres as $u) {
        if ($u['id'] != $p['usuario_id']) {
          echo "<option value='{$u['id']}'>{$u['nombre']}</option>";
        }
      }
      echo "</select></td>";
      echo "<td>
            <input type='hidden' name='evento' value='editar'>
            <button type='submit'>Editar</button>
            <button type='submit' name='evento' value='eliminar' onclick='return confirm(\"¿Eliminar?\")'>Eliminar</button>
          </td>
          </form>
          </tr>";
    }
    echo "</table></body></html>";
  }



}