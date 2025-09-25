<?php

require_once __DIR__ . '/../View/interfaces/View.php';

class LoginView implements View
{

    private $message = '';
    private $messageType = '';

    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    

    public function showSuccessMessage($message)
    {
        $this->message = $message;
        $this->messageType = 'success';
    }


    public function showMessage($message)
    {
        $this->message = $message;
        $this->messageType = 'success';
    }

    public function showErrorMessage($message)
    {
        $this->message = $message;
        $this->messageType = 'error';
    }

     
    public function actualizar(){

        $datos=$this->usuarioModel->obtenerTodos();
        $this->render($datos);
    }

    
    public function render($datos)
    {
        echo "<!DOCTYPE html>";
        echo "<html lang='es'><head><title>Login - Sistema de Asistencia</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; background: #f5f5f5; }";
        echo ".container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo ".header { text-align: center; color: #2c3e50; margin-bottom: 30px; }";
        echo ".success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
        echo ".form-group { margin: 15px 0; }";
        echo ".form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }";
        echo ".form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }";
        echo ".btn { width: 100%; background: #3498db; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }";
        echo ".btn:hover { background: #2980b9; }";
        echo ".demo-info { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #ffeaa7; }";
        echo ".demo-info h4 { margin: 0 0 10px 0; color: #856404; }";
        echo "</style></head><body>";

        echo "<div class='container'>";
        echo "<div class='header'>";
        echo "<h1>🔐 Sistema de Login</h1>";
        echo "<h3>Acceso al Sistema de Asistencia</h3>";
        echo "</div>";

        // Verificar si viene de logout
        if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
            echo "<div class='success'>✅ Sesión cerrada correctamente. ¡Hasta pronto!</div>";
        }

        // Mostrar mensajes
        if ($this->message) {
            $class = $this->messageType === 'success' ? 'success' : 'error';
            echo "<div class='$class'>{$this->message}</div>";
        }

        // Formulario de login
        echo "<form method='post'>";
        echo "<input type='hidden' name='evento' value='login'>";

        echo "<div class='form-group'>";
        echo "<label for='nombre'>👤 Usuario:</label>";
        echo "<input type='text' id='nombre' name='nombre' required placeholder='Ingrese su usuario'>";
        echo "</div>";

        echo "<div class='form-group'>";
        echo "<label for='contrasena'>🔑 Contraseña:</label>";
        echo "<input type='password' id='contrasena' name='contrasena' required placeholder='Ingrese su contraseña'>";
        echo "</div>";

        echo "<button type='submit' class='btn'>🚀 Iniciar Sesión</button>";
        echo "</form>";

      

        echo "</div>";
        echo "</body></html>";
    }
}