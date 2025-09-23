<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/EstudianteModel.php';
require_once __DIR__ . '/../src/View/EstudianteView.php';
require_once __DIR__ . '/../src/Controller/EstudianteController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';


$controller = new EstudianteController();
$controller->handleRequest();
