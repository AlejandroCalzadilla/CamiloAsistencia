<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/MateriaModel.php';
require_once __DIR__ . '/../src/View/MateriaView.php';  
require_once __DIR__ . '/../src/Conexion/Conexion.php';
require_once __DIR__ . '/../src/Controller/MateriaController.php';

session_start();
// Crear el usuario desde la sesión
$usuarioData = $_SESSION['usuario_logueado'];
$db = Conexion::getInstance();

$materia = new MateriaModel($db);
$grupoView = new MateriaView($materia);
$matericontroller = new MateriaController($materia, $grupoView);
$matericontroller->handleRequest();

?>
