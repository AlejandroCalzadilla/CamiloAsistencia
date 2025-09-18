<?php
require_once __DIR__ . '/../Model/GrupoModel.php';
require_once __DIR__ . '/../View/GrupoView.php';


class GrupoController
{
    private $model;
    private $view;
    private $inscripcionModel;

    public function __construct(GrupoModel $model, GrupoView $view, InscripcionModel $inscripcionModel)
    {
        $this->model = $model;
        $this->view = $view;
        $this->inscripcionModel = $inscripcionModel;
    }

    // funcion para manejar la solicitud
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
                    exit(); // Terminar aquí para AJAX
                case 'actualizar_grupo':
                    $this->actualizarGrupo();
                    exit(); // Terminar aquí para AJAX
                case 'eliminar_grupo':
                    $this->eliminarGrupo();
                    exit(); // Terminar aquí para AJAX
                case 'obtener_grupo':
                    $this->obtenerGrupo();
                    exit(); // Terminar aquí para AJAX
                case 'obtener_datos_formulario':
                    $this->obtenerDatosFormulario();
                    exit(); // Terminar aquí para AJAX
                case 'listar_todos':
                    $this->listarTodos();
                    exit(); // Terminar aquí para AJAX
                case 'Usuarios':
                     $this->Usuarios();
                case 'InscripcionClicked':
                    // Lógica para manejar la inscripción
                    $this->showInscripcion();
                    break;
                case 'ProfesoresClicked':
                    // Lógica para manejar la inscripción
                    $this->showProfesores();
                    break;
                case 'MateriasClicked':
                    // Lógica para manejar la inscripción
                    $this->showMaterias();
                    break;
                case 'UsuariosClicked':
                    $this->showUsuarios();
                    break;    
                case  'EstudiantesClicked':   
                    $this->showEstudiantes();
                    break;
                default:
                    $this->view->showErrorMessage("Evento no soportado");
                    break;
            }
        }

        // Mostrar la vista de login solo si no es una petición AJAX
        if (!isset($_POST['evento']) || !in_array($_POST['evento'], [
            'crear_grupo', 'actualizar_grupo', 'eliminar_grupo', 'obtener_grupo',
            'obtener_datos_formulario', 'listar_todos', 'buscar_grupos'
        ])) {
            $this->view->render();
        }
    }


   public function verClases()
    {
        // Verificar que se recibió el grupo_id
        if (isset($_POST['grupo_id'])) {
            $grupo_id = intval($_POST['grupo_id']);
            
            // Opción 1: Redirigir con GET
            header("Location: clase.php?grupo_id={$grupo_id}");
            exit();
            
        } else {
            $this->view->showErrorMessage("No se especificó el grupo");
        }
    }


    public function Usuarios(){
   
        header("Location: usuarios.php");   

    }

    public function showInscripcion() {
        header('Location: inscripcion.php');
        exit();
    }  
    public function showUsuarios() {
        header('Location: usuarios.php');
        exit();
    }
    public function showProfesores() {
        header('Location: profesores.php');
        exit();
    }
    public function showMaterias() {
         header('Location: materia.php');
        exit();
    }


    public function showEstudiantes() {
         header('Location: estudiante.php');
        exit();
    }

    // Método para crear un grupo
    public function crearGrupo()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['nombre']) || !isset($_POST['materia_id']) || !isset($_POST['profesor_codigo'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
            return;
        }
        $data = [
            'nombre' => $_POST['nombre'],
            'capacidad_maxima' => $_POST['capacidad_maxima'] ?? 100,
            'capacidad_actual' => 0,
            'materia_id' => $_POST['materia_id'],
            'profesor_codigo' => $_POST['profesor_codigo']
        ];

        // Validar datos
        $errores = $this->model->validar($data);
        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode(', ', $errores)
            ]);
            return;
        }
        
        $resultado = $this->model->crear($data);
        
        // Si el grupo se creó exitosamente y hay inscripciones que crear
        if ($resultado['success'] && isset($_POST['inscripciones']) && !empty($_POST['inscripciones'])) {
            $grupo_id = $resultado['id'];
            $this->inscripcionModel = $_POST['inscripciones']; // Array de códigos de estudiantes

            foreach ($this->inscripcionModel as $estudiante_codigo) {
                $this->inscripcionModel->crear($estudiante_codigo, $grupo_id);
            }
        }
        
        echo json_encode($resultado);
        exit();
    }

    // Método para actualizar un grupo
    public function actualizarGrupo()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['id']) || !isset($_POST['nombre']) || !isset($_POST['materia_id']) || !isset($_POST['profesor_codigo'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
            return;
        }

        $id = intval($_POST['id']);
        $data = [
            'nombre' => $_POST['nombre'],
            'capacidad_maxima' => $_POST['capacidad_maxima'],
            'capacidad_actual' => $_POST['capacidad_actual'],
            'materia_id' => $_POST['materia_id'],
            'profesor_codigo' => $_POST['profesor_codigo']
        ];

        // Validar datos
        $errores = $this->model->validar($data);
        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode(', ', $errores)
            ]);
            return;
        }

        $resultado = $this->model->actualizar($id, $data);
        
        // Si el grupo se actualizó exitosamente y hay nuevas inscripciones que crear
        if ($resultado['success'] && isset($_POST['nuevas_inscripciones']) && !empty($_POST['nuevas_inscripciones'])) {
            require_once __DIR__ . '/../Model/InscripcionModel.php';
            require_once __DIR__ . '/../Conexion/Conexion.php';
            $conexion = Conexion::getInstance();
            $inscripcionModel = new InscripcionModel($conexion);
            
            $nuevas_inscripciones = $_POST['nuevas_inscripciones']; // Array de códigos de estudiantes
            
            foreach ($nuevas_inscripciones as $estudiante_codigo) {
                $inscripcionModel->crear($estudiante_codigo, $id);
            }
        }
        
        echo json_encode($resultado);
        exit();
    }

    // Método para eliminar un grupo
    public function eliminarGrupo()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID del grupo no especificado'
            ]);
            return;
        }

        $id = intval($_POST['id']);
        $resultado = $this->model->eliminar($id);
        echo json_encode($resultado);
        exit();
    }

    // Método para obtener un grupo por ID
    public function obtenerGrupo()
    {
        header('Content-Type: application/json');
        
        if (!isset($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID del grupo no especificado'
            ]);
            return;
        }

        $id = intval($_POST['id']);
        $grupo = $this->model->obtenerPorId($id);
        
        if ($grupo) {
            echo json_encode([
                'success' => true,
                'data' => $grupo
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Grupo no encontrado'
            ]);
        }
        exit();
    }

    // Método para obtener datos para formularios (profesores y materias)
    public function obtenerDatosFormulario()
    {
        header('Content-Type: application/json');
        
        try {
            $profesores = $this->model->obtenerProfesores();
            $materias = $this->model->obtenerMaterias();
            
            echo json_encode([
                'success' => true,
                'profesores' => $profesores,
                'materias' => $materias
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerDatosFormulario: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al cargar datos del formulario'
            ]);
        }
        exit();
    }

    // Método para listar todos los grupos (para admin)
    public function listarTodos()
    {
        header('Content-Type: application/json');
        
        try {
            $grupos = $this->model->listarTodos();
            echo json_encode([
                'success' => true,
                'data' => $grupos
            ]);
        } catch (Exception $e) {
            error_log("Error en listarTodos: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al cargar grupos: ' . $e->getMessage()
            ]);
        }
        exit();
    }
}