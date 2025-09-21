<?php
class MateriaView {
    private $model;

    public function __construct(MateriaModel $model) {
        $this->model = $model;
    }

    public function showMessage($message) {
        if ($message) {
            echo "<p style='color: green;'>$message</p>";
        }
    }

    public function render() {
       

        //actuializa la vista con los datos del modelo s
        $materias = $this->model->obtener();
        
        echo "<h2>Materias</h2>";
       
        echo "<form method='POST'>
                <input type='text' name='nombre' placeholder='Nueva materia' required>
                <button type='submit' name='evento' value='crear'>Crear</button>
              </form>";
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr>";
        foreach ($materias as $m) {
            echo "<tr>
                    <form method='POST'>
                    <td>{$m['id']}</td>
                    <td><input type='text' name='nombre' value='{$m['nombre']}'></td>
                    <td>
                        <input type='hidden' name='id' value='{$m['id']}'>
                        <button type='submit' name='evento' value='editar'>Editar</button>
                        <button type='submit' name='evento' value='eliminar' onclick='return confirm(\"¿Eliminar?\")'>Eliminar</button>
                    </td>
                    </form>
                  </tr>";
        }
        echo "</table>";
    }
}