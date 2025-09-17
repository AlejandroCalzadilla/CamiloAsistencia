<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/Estudiante.php';
require_once __DIR__ . '/../src/View/EstudianteView.php';
require_once __DIR__ . '/../src/Controller/EstudianteController.php';


$db =Conexion::getInstance();
$estudianteModel= new Estudiante($db);
$view = new EstudianteView($estudianteModel);
$controller = new EstudianteController($estudiante, $view);
$controller->handleRequest();
