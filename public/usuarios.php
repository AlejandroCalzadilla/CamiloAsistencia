<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/UsuarioModel.php';
require_once __DIR__ . '/../src/View/UsuarioView.php';
require_once __DIR__ . '/../src/Controller/UsuarioController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';


$db = Conexion::getInstance();
$usuarioModel = new UsuarioModel();
$view = new UsuarioView();
$controller = new UsuarioController($view);
$controller->handleRequest();
