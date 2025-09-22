<?php
require_once __DIR__ . '/../Model/GrupoModel.php';
require_once __DIR__ . '/../View/GrupoView.php';


class GrupoController
{
    private GrupoModel $model;
    private GrupoView $view;
    private InscripcionModel $inscripcionModel;

    public function __construct(GrupoModel $model, GrupoView $view, InscripcionModel $inscripcionModel)
    {
        $this->model = $model;
        $this->view = $view;
        $this->inscripcionModel = $inscripcionModel;
    }
    public function handleRequest()
    {
        // Procesar solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento'])) {
            error_log("DEBUG - Evento recibido: " . $_POST['evento']);
            echo "<script>console.log('Evento recibido: " . $_POST['evento'] . "');</script>";

            $evento = $_POST['evento'];

            switch ($evento) {

                case 'ver_clases':
                    $this->verClases();
                    break;
                case 'crear_grupo':
                    $this->crearGrupo();
                    break;
                case 'agregar_inscripcion':
                    $this->agregarInscripcion();
                    break;    
                case 'actualizar_grupo':
                    $this->actualizarGrupo();
                    break;
                case 'eliminar_grupo':
                    $this->eliminarGrupo();
                    break;
                case 'eliminar_inscripcion':
                    $this->eliminarInscripcion();
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
                 case 'Usuarios':
                    $this->Usuarios();
                case 'InscripcionClicked':
                    $this->showInscripcion();
                    break;
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
        }
        else {
             $this->view->render();
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
        return $this->view->render();
    }

    public function actualizarGrupo()
    {
        if (!isset($_POST['id']) || !isset($_POST['nombre']) || !isset($_POST['materia_id']) || !isset($_POST['profesor_codigo'])) {
            $this->view->showErrorMessage('Datos incompletos para actualizar el grupo');
        }
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
        return $this->view->render();
    }

    public function eliminarGrupo()
    {
        if (!isset($_POST['id'])) {
            $this->view->showErrorMessage('ID del grupo no especificado');
        }
        $id = intval($_POST['id']);
        $resultado = $this->model->eliminar($id);
        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['message']);
        } else {
            $this->view->showErrorMessage($resultado['message']);
        }
        return $this->view->render();
    }

    public function agregarInscripcion()
    {
        if (!isset($_POST['estudiante_codigo']) || !isset($_POST['grupo_id'])) {
            $this->view->showErrorMessage('Datos incompletos para agregar inscripción');
            return;
        }
        $estudiante_codigo = $_POST['estudiante_codigo'];
        $grupo_id = intval($_POST['grupo_id']);
        $resultado = $this->inscripcionModel->crear($estudiante_codigo, $grupo_id);
        if ($resultado['success']) {
            $this->view->showSuccessMessage($resultado['mensaje']);
        } else {
            $this->view->showErrorMessage($resultado['mensaje']);
        }
        return $this->view->render();
    }

    public function eliminarInscripcion()
    {
        if (!isset($_POST['estudiante_codigo']) || !isset($_POST['grupo_id'])) {
            $this->view->showErrorMessage('Datos incompletos para eliminar inscripción');
        }
        $estudiante_codigo = $_POST['estudiante_codigo'];
        $grupo_id = intval($_POST['grupo_id']);
        $resultado = $this->inscripcionModel->eliminar($estudiante_codigo, $grupo_id);
            if ($resultado['success']) {
                $this->view->showSuccessMessage($resultado['mensaje']);
            } else {
                $this->view->showErrorMessage($resultado['mensaje']);
            }
        return $this->view->render();
    }

    public function mostrarFormularioCrear()
    {
        $this->view->setMostrarFormulario(true, 'crear');
        return $this->view->render();
    }

    public function mostrarFormularioEditar()
    {
        $grupo_id = intval($_POST['grupo_id']);
        $this->view->setMostrarFormulario(true, 'editar', $grupo_id);
        return $this->view->render();   
    }
 
    public function cancelarFormulario()
    {
        $this->view->setMostrarFormulario(false);
    }

    public function verClases()
    {
        if (isset($_POST['grupo_id'])) {
            $grupo_id = intval($_POST['grupo_id']);
            header("Location: clase.php?grupo_id={$grupo_id}");
            exit();
        } else {
            $this->view->showErrorMessage("No se especificó el grupo");
        }
         $this->view->render();
    }
    public function Usuarios()
    {
        header("Location: usuarios.php");
    }

    public function showInscripcion()
    {
        header('Location: inscripcion.php');
        exit();
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