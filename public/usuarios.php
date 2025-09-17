<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/ClaseModel.php';
require_once __DIR__ . '/../src/View/ClaseView.php';
require_once __DIR__ . '/../src/Controller/ClaseController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';


$db = Conexion::getInstance();
$usuarioModel = new UsuarioModel($db);
$view = new UsuarioView($usuarioModel);
$controller = new UsuarioController($usuarioModel,$view);
$controller->handleRequest();
