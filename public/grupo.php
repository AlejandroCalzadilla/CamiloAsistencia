<?php
// Incluir las clases necesarias
require_once __DIR__ . '/../src/Model/UsuarioModel.php';
require_once __DIR__ . '/../src/Model/GrupoModel.php';
require_once __DIR__ . '/../src/View/GrupoView.php';
require_once __DIR__ . '/../src/Conexion/Conexion.php';
require_once __DIR__ . '/../src/Controller/GrupoController.php';   

session_start();
// Crear el usuario desde la sesión
$usuarioData = $_SESSION['usuario_logueado'];
$db = Conexion::getInstance();

$grupo = new GrupoModel($db);
$grupoView = new GrupoView($grupo);
$grupocontroler = new GrupoController($grupo, $grupoView);

// Mostrar mensaje de bienvenida si viene del login
if (isset($_GET['login']) && $_GET['login'] === 'success') {
    $grupoView->showSuccessMessage("¡Bienvenido " . $usuarioData['nombre'] . "! Login exitoso.");
}

// El controlador maneja todo (incluyendo el render)
$grupocontroler->handleRequest();

?>
