<?php
require_once __DIR__ . '/../src/Conexion/Conexion.php';

// Procesar ejecución del script SQL
if (isset($_POST['ejecutar_script'])) {
    try {
        $db = Conexion::getInstance();
        $sqlFile = __DIR__ . '/../src/Conexion/schema.sql';
        
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            
            // Ejecutar el script SQL
            $db->getConnection()->exec($sql);
            
            $mensaje_exito = "✅ Script SQL ejecutado correctamente. Tablas y datos creados.";
        } else {
            $mensaje_error = "❌ No se encontró el archivo schema.sql";
        }
    } catch (Exception $e) {
        $mensaje_error = "❌ Error al ejecutar script: " . $e->getMessage();
    }
}

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Test Conexión PostgreSQL Local</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }";
echo ".success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo ".error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo ".info { background: #e3f2fd; padding: 10px; border-radius: 5px; margin: 10px 0; }";
echo ".warning { background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; border: 1px solid #ffeaa7; }";
echo "table { border-collapse: collapse; width: 100%; margin: 10px 0; }";
echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }";
echo "th { background-color: #f2f2f2; }";
echo ".btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }";
echo ".btn:hover { background: #0056b3; }";
echo ".btn-danger { background: #dc3545; }";
echo ".btn-danger:hover { background: #c82333; }";
echo "</style>";
echo "</head><body>";

echo "<h1>🔗 Test Conexión PostgreSQL Local</h1>";

// Mostrar mensajes de resultado
if (isset($mensaje_exito)) {
    echo "<div class='success'><h3>$mensaje_exito</h3></div>";
}
if (isset($mensaje_error)) {
    echo "<div class='error'><h3>$mensaje_error</h3></div>";
}

try {
    // Probar conexión
    $db = Conexion::getInstance();
    
    echo "<div class='success'>";
    echo "<h3>✅ Conexión Exitosa</h3>";
    $info = $db->getConnectionInfo();
    echo "<p><strong>Host:</strong> {$info['host']}</p>";
    echo "<p><strong>Puerto:</strong> {$info['port']}</p>";
    echo "<p><strong>Base de datos:</strong> {$info['dbname']}</p>";
    echo "<p><strong>Usuario:</strong> {$info['username']}</p>";
    echo "</div>";
    
    // Verificar versión
    $version = $db->fetch("SELECT version() as version");
    echo "<div class='info'>";
    echo "<h3>📊 Información del Servidor</h3>";
    echo "<p><strong>Versión:</strong> " . substr($version['version'], 0, 50) . "...</p>";
    echo "</div>";
    
    // Verificar tablas existentes
    $tablas = $db->fetchAll("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    
    echo "<h3>📋 Tablas en la Base de Datos</h3>";
    if (empty($tablas)) {
        echo "<div class='warning'>";
        echo "<p>⚠️ No hay tablas creadas. Ejecuta el script SQL para crear la estructura.</p>";
        echo "<form method='post' style='display: inline;'>";
        echo "<button type='submit' name='ejecutar_script' class='btn' onclick='return confirm(\"¿Estás seguro de ejecutar el script SQL? Esto creará todas las tablas y datos de prueba.\")'>🚀 Ejecutar Script SQL</button>";
        echo "</form>";
        echo "</div>";
    } else {
        echo "<table>";
        echo "<tr><th>Tabla</th><th>Registros</th></tr>";
        foreach ($tablas as $tabla) {
            $nombre = $tabla['table_name'];
            try {
                $count = $db->fetch("SELECT COUNT(*) as total FROM {$nombre}");
                echo "<tr><td>{$nombre}</td><td>{$count['total']}</td></tr>";
            } catch (Exception $e) {
                echo "<tr><td>{$nombre}</td><td>Error</td></tr>";
            }
        }
        echo "</table>";
        
        echo "<div class='warning'>";
        echo "<p>⚠️ Si necesitas recrear las tablas:</p>";
        echo "<form method='post' style='display: inline;'>";
        echo "<button type='submit' name='ejecutar_script' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro? Esto ELIMINARÁ todos los datos existentes y recreará las tablas.\")'>🔄 Recrear Tablas</button>";
        echo "</form>";
        echo "</div>";
    }
    
    // Verificar usuario admin
    if (in_array('usuario', array_column($tablas, 'table_name'))) {
        $usuarios = $db->fetchAll("SELECT id, nombre, creado_en FROM usuario LIMIT 5");
        echo "<h3>👤 Usuarios en la Base</h3>";
        if (empty($usuarios)) {
            echo "<div class='error'>";
            echo "<p>❌ No hay usuarios. Ejecuta el script SQL para insertar datos de prueba.</p>";
            echo "</div>";
        } else {
            echo "<table>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Creado</th></tr>";
            foreach ($usuarios as $user) {
                echo "<tr><td>{$user['id']}</td><td>{$user['nombre']}</td><td>{$user['creado_en']}</td></tr>";
            }
            echo "</table>";
            
            echo "<div class='success'>";
            echo "<h4>🎯 Credenciales de prueba:</h4>";
            echo "<p><strong>Usuario:</strong> admin | <strong>Contraseña:</strong> 123456</p>";
            echo "<p><a href='index.php' class='btn'>🔐 Ir al Login</a></p>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Error de Conexión</h3>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<h4>💡 Soluciones:</h4>";
    echo "<ul>";
    echo "<li>Verificar que PostgreSQL esté instalado y ejecutándose</li>";
    echo "<li>Verificar credenciales en .env</li>";
    echo "<li>Crear la base de datos: <code>createdb asistenciadb</code></li>";
    echo "<li>Para Docker: <code>docker run --name postgres-local -e POSTGRES_DB=asistenciadb -e POSTGRES_PASSWORD=ale12345678 -p 5432:5432 -d postgres:15</code></li>";
    echo "</ul>";
    echo "</div>";
}

echo "</body></html>";
?>