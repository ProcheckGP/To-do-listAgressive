<?php
class Database
{
    private static $instance = null;
    private $connection = null;

    protected function __construct()
    {
        $config = require __DIR__ . '/config.php';

        $this->connection = new PDO(
            $config['dsn'],
            $config['username'],
            $config['password'],
            $config['options']
        );
    }

    protected function __clone() {}

    public function __wakeup() {}

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function connection(): \PDO
    {
        return self::getInstance()->connection;
    }

    public static function prepare($statement): \PDOStatement
    {
        return self::connection()->prepare($statement);
    }

    public static function execute($statement, $params = []): \PDOStatement
    {
        $stmt = self::prepare($statement);
        $stmt->execute($params);
        return $stmt;
    }
}
