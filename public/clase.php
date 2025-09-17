<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/ClaseModel.php';
require_once __DIR__ . '/../src/View/ClaseView.php';
require_once __DIR__ . '/../src/Controller/ClaseController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';

// Implementación correcta del patrón MVC
if (!isset($_GET['grupo_id']) || empty($_GET['grupo_id'])) {
    // Si no hay grupo_id, redirigir a grupos
    header('Location: grupo.php');
    exit();
}


$grupo_id = intval($_GET['grupo_id']);

// 1. Crear el Modelo (con datos simulados)
$db = Conexion::getInstance();

$claseModel = new ClaseModel($db);

$view = new ClaseView($claseModel);
// 3. Crear el Controlador e inyectar tanto el Modelo como la Vista
$controller = new ClaseController($claseModel, $view);

// 4. El Controlador maneja las solicitudes y coordina Modelo y Vista
$controller->handleRequest($grupo_id);
