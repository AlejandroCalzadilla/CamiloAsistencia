<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/ClaseModel.php';
require_once __DIR__ . '/../src/Model/AsistenciaModel.php';
require_once __DIR__ . '/../src/View/ClaseView.php';
require_once __DIR__ . '/../src/Controller/ClaseController.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';


if (!isset($_GET['grupo_id']) || empty($_GET['grupo_id'])) {
    header('Location: grupo.php');
    exit();
}
$grupo_id = intval($_GET['grupo_id']);
$db = Conexion::getInstance();
$claseModel = new ClaseModel($db);
$asistenciaModel = new AsistenciaModel($db);
$view = new ClaseView($claseModel,$asistenciaModel);
$controller = new ClaseController($claseModel, $asistenciaModel, $view);
$controller->handleRequest($grupo_id);
