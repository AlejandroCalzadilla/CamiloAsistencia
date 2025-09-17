<?php
class EstudianteController {
    private $model;
    private $view;

    public function __construct(EstudianteModel $model, EstudianteView $view) {
        $this->model = $model;
        $this->view = $view;
    }

     public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            switch ($_POST['evento']) {
                case 'crear':
                    if (!empty($_POST['codigo']) && !empty($_POST['ci']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['estado']) && !empty($_POST['usuario_id'])) {
                        $this->model->crear([
                            'codigo' => $_POST['codigo'],
                            'ci' => $_POST['ci'],
                            'nombres' => $_POST['nombres'],
                            'apellidos' => $_POST['apellidos'],
                            'estado' => $_POST['estado'],
                            'genero' => $_POST['genero'] ?? '',
                            'usuario_id' => $_POST['usuario_id']
                        ]);
                        $this->view->showSuccessMessage("Estudiante creado correctamente");
                    }
                    break;
                case 'actualizar':
                    if (!empty($_POST['codigo']) && !empty($_POST['ci']) && !empty($_POST['nombres']) && !empty($_POST['apellidos']) && !empty($_POST['estado']) && !empty($_POST['usuario_id'])) {
                        $this->model->actualizar([
                            'codigo' => $_POST['codigo'],
                            'ci' => $_POST['ci'],
                            'nombres' => $_POST['nombres'],
                            'apellidos' => $_POST['apellidos'],
                            'estado' => $_POST['estado'],
                            'genero' => $_POST['genero'] ?? '',
                            'usuario_id' => $_POST['usuario_id']
                        ]);
                        $this->view->showSuccessMessage("Estudiante actualizado correctamente");
                    }
                    break;
                case 'eliminar':
                    if (!empty($_POST['codigo'])) {
                        $this->model->eliminar($_POST['codigo']);
                        $this->view->showSuccessMessage("Estudiante eliminado correctamente");
                    }
                    break;
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        }
        $estudiantes = $this->model->obtenerTodos();
        $this->view->render($estudiantes);
    }
     }