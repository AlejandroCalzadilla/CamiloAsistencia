<?php
class Conexion {
    private static $instance = null;
    private $connection;
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;

    public function __construct() {
        
        $this->loadEnvironmentVariables();
        $this->connect();
    }

    /**
     * Singleton pattern para obtener una única instancia de la conexión
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Cargar variables de entorno desde el archivo .env
     */
    private function loadEnvironmentVariables() {
        $envFile = __DIR__ . '/../../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue; // Saltar comentarios
                }
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $_ENV[trim($name)] = trim($value);
                }
            }
        }

        // Configurar parámetros de conexión para PostgreSQL local
        if (isset($_ENV['DATABASE_URL']) && !empty($_ENV['DATABASE_URL'])) {
            // Usar DATABASE_URL completa
            $this->parseDatabaseUrl($_ENV['DATABASE_URL']);
        } else {
            // Usar parámetros individuales
            $this->host = $_ENV['DB_HOST'] ?? 'localhost';
            $this->port = $_ENV['DB_PORT'] ?? '5432';
            $this->dbname = $_ENV['DB_NAME'] ?? 'asistencia_db';
            $this->username = $_ENV['DB_USER'] ?? 'postgres';
            $this->password = $_ENV['DB_PASSWORD'] ?? '';
        }
    }

    /**
     * Parsear la URL de la base de datos
     */
    private function parseDatabaseUrl($url) {
        $parsed = parse_url($url);
        $this->host = $parsed['host'];
        $this->port = $parsed['port'] ?? '5432';
        $this->dbname = ltrim($parsed['path'], '/');
        $this->username = $parsed['user'];
        $this->password = $parsed['pass'];
    }

    /**
     * Establecer conexión con PostgreSQL local
     */
    private function connect() {
        try {
            // DSN para PostgreSQL local (sin SSL)
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_TIMEOUT => 10
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
            // Configurar timezone y encoding
            $this->connection->exec("SET timezone = 'America/Bogota'");
            $this->connection->exec("SET client_encoding = 'UTF8'");
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexión a la base de datos local: " . $e->getMessage());
        }
    }

    /**
     * Obtener la conexión PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Ejecutar una consulta preparada
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error al ejecutar consulta: " . $e->getMessage());
        }
    }

    /**
     * Obtener un registro
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Obtener múltiples registros
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insertar registro y devolver ID
     */
    public function insert($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }

    /**
     * Actualizar registros
     */
    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Eliminar registros
     */
    public function delete($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Verificar si la conexión está activa
     */
    public function isConnected() {
        try {
            if ($this->connection === null) {
                return false;
            }
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtener información de la conexión
     */
    public function getConnectionInfo() {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->dbname,
            'username' => $this->username,
            'password_set' => !empty($this->password),
            'connected' => $this->isConnected(),
            'type' => 'PostgreSQL Local'
        ];
    }

    /**
     * Obtener información de la base de datos
     */
    public function getDatabaseInfo() {
        try {
            $version = $this->fetch("SELECT version() as version");
            $currentDb = $this->fetch("SELECT current_database() as database");
            $currentUser = $this->fetch("SELECT current_user as user");
            
            return [
                'version' => $version['version'] ?? 'Desconocida',
                'database' => $currentDb['database'] ?? 'Desconocida',
                'user' => $currentUser['user'] ?? 'Desconocido',
                'host' => $this->host,
                'port' => $this->port
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}