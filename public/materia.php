<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/MateriaModel.php';
require_once __DIR__ . '/../src/View/MateriaView.php';  
require_once __DIR__ . '/../src/Conexion/Conexion.php';
require_once __DIR__ . '/../src/Controller/MateriaController.php';

session_start();
// Crear el usuario desde la sesión
$usuarioData = $_SESSION['usuario_logueado'];
$matericontroller = new MateriaController();
$matericontroller->handleRequest();

?>
