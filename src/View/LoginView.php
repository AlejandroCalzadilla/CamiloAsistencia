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


    public function actualizar()
    {

        $datos = $this->usuarioModel->obtenerTodos();
        $this->render($datos);
    }


    public function render($datos)
    {
        echo "<!DOCTYPE html>";
        echo "<html lang='es'><head><title>Login - Sistema de Asistencia</title>";
        echo "<style>";
        echo "body { font-family: 'Segoe UI', Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; background: white; color: #333; }";
        echo ".container { background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo ".header { text-align: center; color: #000; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }";
        echo ".header h1 { margin: 0 0 10px 0; font-size: 24px; font-weight: 600; }";
        echo ".header h3 { margin: 0; font-size: 16px; font-weight: 400; color: #666; }";
        echo ".success { color: #000; background: #f8f9fa; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; }";
        echo ".error { color: #000; background: #f8f9fa; padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; }";
        echo ".form-group { margin: 20px 0; }";
        echo ".form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #000; font-size: 14px; }";
        echo ".form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 14px; background: white; color: #000; }";
        echo ".form-group input:focus { outline: none; border-color: #000; }";
        echo ".form-group input::placeholder { color: #999; }";
        echo ".btn { width: 100%; background: #000; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600; margin-top: 10px; }";
        echo ".btn:hover { background: #333; }";
        echo ".demo-info { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 20px 0; border: 1px solid #ddd; }";
        echo ".demo-info h4 { margin: 0 0 10px 0; color: #000; font-size: 14px; font-weight: 600; }";
        echo ".demo-info p { margin: 5px 0; color: #666; font-size: 13px; }";

        // Responsive
        echo "@media (max-width: 480px) {";
        echo "  body { margin: 50px auto; padding: 15px; }";
        echo "  .container { padding: 20px; }";
        echo "  .header h1 { font-size: 20px; }";
        echo "  .header h3 { font-size: 14px; }";
        echo "}";

        echo "</style>";
        echo "</head>";
        echo "<body>";

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