<?php
// Sistema de Asistencia Web - Página Principal con Login MVC
require_once __DIR__ . '/../src/Model/UsuarioModel.php';
require_once __DIR__ . '/../src/View/LoginView.php';
require_once __DIR__ . '/../src/Controller/UsuarioController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';

$userView = new LoginView();
$usuarioController = new UsuarioController($userView);
$usuarioController->handleRequest();
