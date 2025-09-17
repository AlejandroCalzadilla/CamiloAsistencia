<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/ProfesorModel.php';
require_once __DIR__ . '/../src/View/ProfesorView.php';
require_once __DIR__ . '/../src/Controller/ProfesorController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';


$db =Conexion::getInstance();
$profesormodelo= new ProfesorModel($db);
$view = new ProfesorView($profesormodelo);
$controller = new ProfesorController($profesormodelo, $view);
$controller->handleRequest();
