<?php
/**
 * config/database.php
 * Configuración de conexión a la base de datos MySQL.
 * Utiliza PDO con prepared statements para prevenir inyección SQL.
 *
 * @package  Bartek
 * @version  1.0
 */

// ── Constantes de conexión ────────────────────────────────────────────────────
// En producción estas variables deben cargarse desde variables de entorno
// o un archivo .env excluido del control de versiones.
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')    ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: 'bartek_db');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Class Database
 *
 * Implementa el patrón Singleton para mantener una única conexión
 * PDO durante toda la solicitud HTTP, evitando conexiones redundantes.
 */
class Database
{
    /** @var Database|null Instancia única de la clase */
    private static ?Database $instancia = null;

    /** @var PDO Objeto de conexión PDO */
    private PDO $pdo;

    /**
     * Constructor privado: establece la conexión PDO con opciones seguras.
     * Fuerza emulación de prepared statements desactivada para mayor seguridad.
     */
    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lanza excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Arrays asociativos por defecto
            PDO::ATTR_EMULATE_PREPARES   => false,                   // Prepared statements nativos
            PDO::MYSQL_ATTR_FOUND_ROWS   => true,                   // Rows encontradas vs afectadas
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        } catch (PDOException $e) {
            // Se registra el error real en log pero NO se expone al usuario
            error_log('[Bartek][DB] Error de conexión: ' . $e->getMessage());
            die(json_encode(['error' => 'Error interno del servidor. Intente más tarde.']));
        }
    }

    /**
     * Retorna la instancia única de Database (Singleton).
     *
     * @return Database
     */
    public static function obtenerInstancia(): Database
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    /**
     * Retorna el objeto PDO para ejecutar consultas.
     *
     * @return PDO
     */
    public function obtenerConexion(): PDO
    {
        return $this->pdo;
    }

    /** Evita la clonación del Singleton */
    private function __clone() {}

    /** Evita la deserialización del Singleton */
    public function __wakeup(): void
    {
        throw new \Exception('No se puede deserializar un Singleton.');
    }
}
