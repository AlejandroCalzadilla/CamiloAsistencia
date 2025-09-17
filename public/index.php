<?php
// Sistema de Asistencia Web - Página Principal con Login MVC
require_once __DIR__ . '/../src/Model/UsuarioModel.php';
require_once __DIR__ . '/../src/View/LoginView.php';
require_once __DIR__ . '/../src/Controller/UsuarioController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';

// Implementación del patrón MVC para Usuario/Login
// 1. Crear el Modelo Usuario (instancia vacía para login)
$db = Conexion::getInstance();
$usuario = new UsuarioModel($db);
// 2. Crear la Vista de Usuario (sin dependencias inicialmente)
$userView = new LoginView($usuario);
// 3. Crear el Controlador e inyectar tanto el Modelo como la Vista
$usuarioController = new UsuarioController($usuario, $userView);

// 4. El Controlador maneja las solicitudes y coordina Modelo y Vista
// Si hay un evento de login exitoso, automáticamente mostrará la vista de grupo
$usuarioController->handleRequest();
