<?php

require_once __DIR__ . '/../View/interfaces/View.php';
class UsuarioView implements View
{
    private $model;
    private $message = '';

    public function __construct(UsuarioModel $model)
    {
        $this->model = $model;
    }

    public function showMessage($msg) {
        $this->message = $msg;
    }

    public function render()
    {
        $usuarios = $this->model->obtenerTodos();
        echo "<h2>Usuarios</h2>";
        if ($this->message) echo "<p style='color:green;'>{$this->message}</p>";
        echo "<form method='POST'>
                <input type='text' name='nombre' placeholder='Nombre' required>
                <input type='password' name='contrasena' placeholder='Contraseña' required>
                <button type='submit' name='evento' value='crear'>Crear</button>
              </form>";
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Nombre</th><th>Creado</th><th>Actualizado</th><th>Acciones</th></tr>";
        foreach ($usuarios as $u) {
            echo "<tr>
                    <form method='POST'>
                    <td>{$u['id']}</td>
                    <td><input type='text' name='nombre' value='{$u['nombre']}'></td>
                    <td>{$u['creado_en']}</td>
                    <td>{$u['actualizado_en']}</td>
                    <td>
                        <input type='hidden' name='id' value='{$u['id']}'>
                        <input type='password' name='contrasena' placeholder='Nueva contraseña'>
                        <button type='submit' name='evento' value='editar'>Editar</button>
                        <button type='submit' name='evento' value='eliminar' onclick='return confirm(\"¿Eliminar?\")'>Eliminar</button>
                    </td>
                    </form>
                  </tr>";
        }
        echo "</table>";
    }
}