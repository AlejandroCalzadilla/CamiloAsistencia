<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/UsuarioModel.php';
require_once __DIR__ . '/../src/Model/GrupoModel.php';
require_once __DIR__ . '/../src/View/GrupoView.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';
require_once __DIR__ . '/../src/Controller/GrupoController.php';   
require_once __DIR__ . '/../src/Model/AsignacionModel.php';

session_start();

$grupocontroler = new GrupoController();
$grupocontroler->handleRequest();
?>
