<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/EstudianteModel.php';
require_once __DIR__ . '/../src/View/EstudianteView.php';
require_once __DIR__ . '/../src/Controller/EstudianteController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';


$db =Conexion::getInstance();
$estudianteModel= new EstudianteModel($db);
$view = new EstudianteView($estudianteModel);
$controller = new EstudianteController($estudianteModel, $view);
$controller->handleRequest();
