<?php
class EstudianteView
{
    private $estudiantemodel;
    private $message = '';
    private $messageType = '';

    public function __construct()
    {
        $this->estudiantemodel = new EstudianteModel();
    }


    public function actualizar()
    {
        $estudiantes = $this->estudiantemodel->obtenerTodos();
        $usuariosLibres = $this->estudiantemodel->obtenerUsuariosLibres();
        $this->render($estudiantes, $usuariosLibres);
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

    public function render($estudiantes, $usuariosLibres)
    {

        echo "<!DOCTYPE html><html><head><title>Estudiantes</title></head><body>";
        echo "<h2>Estudiantes</h2>";
        if ($this->message) {
            $class = $this->messageType === 'success' ? 'success' : 'error';
            echo "<div class='$class'>{$this->message}</div>";
        }
        // Formulario de creación
        echo "<form method='POST'>
                <input type='hidden' name='evento' value='crear'>
                <input type='text' name='codigo' placeholder='Código' required>
                <input type='text' name='ci' placeholder='CI' required>
                <input type='text' name='nombres' placeholder='Nombres' required>
                <input type='text' name='apellidos' placeholder='Apellidos' required>
                <select name='estado'>
                    <option value='activo'>Activo</option>
                    <option value='inactivo'>Inactivo</option>
                </select>
                <select name='genero' required>
                    <option value=''>-- Selecciona género --</option>
                    <option value='M'>Masculino</option>
                    <option value='F'>Femenino</option>
                </select>
                <select name='usuario_id' required>
                    <option value=''>-- Selecciona usuario --</option>";
        foreach ($usuariosLibres as $u) {
            echo "<option value='{$u['id']}'>{$u['nombre']}</option>";
        }
        echo "</select>
                <button type='submit'>Crear</button>
              </form>";
        // Tabla de estudiantes
        echo "<table border='1' cellpadding='5'><tr><th>Código</th><th>CI</th><th>Nombres</th><th>Apellidos</th><th>Estado</th><th>Género</th><th>Usuario</th><th>Acciones</th></tr>";
        foreach ($estudiantes as $e) {
            echo "<tr>
                    <form method='POST'>
                    <td><input type='text' name='codigo' value='{$e['codigo']}' readonly></td>
                    <td><input type='text' name='ci' value='{$e['ci']}'></td>
                    <td><input type='text' name='nombres' value='{$e['nombres']}'></td>
                    <td><input type='text' name='apellidos' value='{$e['apellidos']}'></td>
                    <td>
                        <select name='estado'>
                            <option value='activo'" . ($e['estado'] === 'activo' ? ' selected' : '') . ">Activo</option>
                            <option value='inactivo'" . ($e['estado'] === 'inactivo' ? ' selected' : '') . ">Inactivo</option>
                        </select>
                    </td>
                    <td><select name='genero' required>
                        <option value='m'" . ($e['genero'] === 'm' ? ' selected' : '') . ">Masculino</option>
                        <option value='f'" . ($e['genero'] === 'f' ? ' selected' : '') . ">Femenino</option>
                    </select></td>
                    <td><select name='usuario_id' required>";
            // Mostrar el usuario actual
            echo "<option value='{$e['usuario_id']}' selected>Usuario actual ({$e['usuario_id']})</option>";
            // Mostrar los usuarios libres
            foreach ($usuariosLibres as $u) {
                if ($u['id'] != $e['usuario_id']) {
                    echo "<option value='{$u['id']}'>{$u['nombre']}</option>";
                }
            }
            echo "</select></td>";
            echo "<td>
                        <input type='hidden' name='evento' value='actualizar'>
                        <button type='submit'>Actualizar</button>
                        <button type='submit' name='evento' value='eliminar' onclick='return confirm(\"¿Eliminar?\")'>Eliminar</button>
                    </td>
                    </form>
                  </tr>";
        }
        echo "</table></body></html>";
    }
}