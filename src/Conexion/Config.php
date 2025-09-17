<?php
/**
 * Archivo de configuración para PostgreSQL Local
 */

class Config {
    
    /**
     * Configuración de la base de datos
     */
    public static function getDatabaseConfig() {
        return [
            'host' => self::getEnv('DB_HOST', 'localhost'),
            'port' => self::getEnv('DB_PORT', '5432'),
            'dbname' => self::getEnv('DB_NAME', 'asistencia_db'),
            'username' => self::getEnv('DB_USER', 'postgres'),
            'password' => self::getEnv('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ]
        ];
    }

    /**
     * Configuración de la aplicación
     */
    public static function getAppConfig() {
        return [
            'env' => self::getEnv('APP_ENV', 'development'),
            'debug' => self::getEnv('APP_DEBUG', 'true') === 'true',
            'timezone' => 'America/Bogota',
            'charset' => 'UTF-8'
        ];
    }

    /**
     * Obtener variable de entorno con valor por defecto
     */
    private static function getEnv($key, $default = null) {
        // Cargar .env si no está cargado
        self::loadEnv();
        
        return $_ENV[$key] ?? $default;
    }

    /**
     * Cargar archivo .env
     */
    private static function loadEnv() {
        static $loaded = false;
        
        if ($loaded) {
            return;
        }
        
        $envFile = __DIR__ . '/../../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $_ENV[trim($name)] = trim($value);
                }
            }
        }
        
        $loaded = true;
    }
}
