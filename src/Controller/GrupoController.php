<?php
require_once __DIR__ . '/../Model/GrupoModel.php';
require_once __DIR__ . '/../View/GrupoView.php';


class GrupoController
{
    private GrupoModel $model;
    private GrupoView $view;
    private AsignacionModel $asignacionModel;

    public function __construct()
    {
        $this->model = new GrupoModel();
        $this->view = new GrupoView();
        $this->asignacionModel = new AsignacionModel();
    }
    public function handleRequest()
    {
        // Procesar solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {


            $evento = $_POST['evento'];

            switch ($evento) {

                case 'ver_clases':
                    $this->verClases();
                    break;
                case 'crear_grupo':
                    $this->crearGrupo();
                    break;
                case 'agregar_asignacion':
                    $this->agregarAsignacion();
                    break;
                case 'actualizar_grupo':
                    $this->actualizarGrupo();
                    break;
                case 'eliminar_grupo':
                    $this->eliminarGrupo();
                    break;
                case 'eliminar_asignacion':
                    $this->eliminarAsignacion();
                    break;
                case 'mostrar_form_crear':
                    $this->mostrarFormularioCrear();
                    break;
                case 'mostrar_form_editar':
                    $this->mostrarFormularioEditar();
                    break;
                case 'cancelar_formulario':
                    $this->cancelarFormulario();
                    break;



                //rutas

                
                case 'ProfesoresClicked':
                    $this->showProfesores();
                    break;
                case 'MateriasClicked':
                    $this->showMaterias();
                    break;
                case 'UsuariosClicked':
                    $this->showUsuarios();
                    break;
                case 'EstudiantesClicked':
                    $this->showEstudiantes();
                    break;
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        } else {
            $this->view->actualizar();
        }

    }



    public function crearGrupo()
    {
        if (!isset($_POST['nombre']) || !isset($_POST['materia_id']) || !isset($_POST['profesor_codigo'])) {
            $this->view->showErrorMessage('Datos incompletos para crear el grupo');
        }
        $data = [
            'nombre' => $_POST['nombre'],
            'capacidad_maxima' => $_POST['capacidad_maxima'] ?? 100,
            'capacidad_actual' => 0,
            'materia_id' => $_POST['materia_id'],
            'profesor_codigo' => $_POST['profesor_codigo']
        ];
        $resultado = $this->model->crear($data);
        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['message']);
        } else {
            $this->view->showErrorMessage($resultado['message']);
        }
        return $this->view->actualizar();
    }

    public function actualizarGrupo()
    {
        $id = intval($_POST['id']);
        $data = [
            'nombre' => $_POST['nombre'],
            'capacidad_maxima' => $_POST['capacidad_maxima'],
            'capacidad_actual' => $_POST['capacidad_actual'] ?? 0,
            'materia_id' => $_POST['materia_id'],
            'profesor_codigo' => $_POST['profesor_codigo']
        ];


        $resultado = $this->model->actualizar($id, $data);


        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['message']);
        } else {
            $this->view->showErrorMessage($resultado['message']);
        }
        return $this->view->actualizar();
    }

    public function eliminarGrupo()
    {

        $id = intval($_POST['id']);
        $resultado = $this->model->eliminar($id);

        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['message']);
        } else {
            $this->view->showErrorMessage($resultado['message']);
        }
        return $this->view->actualizar();
    }

    public function agregarAsignacion()
    {
        $estudiante_codigo = $_POST['estudiante_codigo'];
        $grupo_id = intval($_POST['grupo_id']);


        $resultado = $this->asignacionModel->crear($estudiante_codigo, $grupo_id);

        error_log("DEBUG - Resultado de agregarAsignacion: " . json_encode($resultado));
        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['message']);
        } else {
            $this->view->showErrorMessage($resultado['message']);
        }
        return $this->view->actualizar();
    }

    public function eliminarAsignacion()
    {
        $estudiante_codigo = $_POST['estudiante_codigo'];
        $grupo_id = intval($_POST['grupo_id']);


        $resultado = $this->asignacionModel->eliminar($estudiante_codigo, $grupo_id);


        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['mensaje']);
        } else {
            $this->view->showErrorMessage($resultado['mensaje']);
        }
        return $this->view->actualizar();
    }

    public function mostrarFormularioCrear()
    {
        $this->view->setMostrarFormulario(true, 'crear');
        return $this->view->actualizar();
    }

    public function mostrarFormularioEditar()
    {
        $grupo_id = intval($_POST['grupo_id']);
        $this->view->setMostrarFormulario(true, 'editar', $grupo_id);
        return $this->view->actualizar();
    }

    public function cancelarFormulario()
    {
        $this->view->setMostrarFormulario(false);
        return $this->view->actualizar();
    }

    public function verClases()
    {
        if (isset($_POST['grupo_id'])) {
            $grupo_id = intval($_POST['grupo_id']);
            header("Location: clase.php?grupo_id={$grupo_id}");
            exit();
        } else {
            $this->view->showErrorMessage("No se especificó el grupo");
            $this->view->actualizar();
        }

    }



  
    public function showUsuarios()
    {
        header('Location: usuarios.php');
        exit();
    }
    public function showProfesores()
    {
        header('Location: profesores.php');
        exit();
    }
    public function showMaterias()
    {
        header('Location: materia.php');
        exit();
    }
    public function showEstudiantes()
    {
        header('Location: estudiante.php');
        exit();
    }
}