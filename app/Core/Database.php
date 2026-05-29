<?php
namespace App\Core;
use PDO;
class Database
{
    private static $instance = null;
    private $pdo;
    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    public static function getInstance()
    {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }
    public function getConnection() { return $this->pdo; }
    public static function query($sql, $params = [])
    {
        $stmt = self::getInstance()->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    public static function fetchAll($sql, $params = []) { return self::query($sql, $params)->fetchAll(); }
    public static function fetchOne($sql, $params = []) { return self::query($sql, $params)->fetch(); }
    public static function insert($sql, $params = [])
    {
        $db = self::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $db->lastInsertId();
    }
    public static function execute($sql, $params = []) { return self::query($sql, $params)->rowCount(); }
    public static function beginTransaction() { self::getInstance()->getConnection()->beginTransaction(); }
    public static function commit() { self::getInstance()->getConnection()->commit(); }
    public static function rollback()
    {
        if (self::getInstance()->getConnection()->inTransaction())
            self::getInstance()->getConnection()->rollBack();
    }
}
