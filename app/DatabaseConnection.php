<?php

/**
 * Database Connection
 *
 * @class DatabaseConnection
 */
class DatabaseConnection
{
    const string HOST = 'mysql';
    const string DB_NAME = 'project';
    const int PORT = 3306;
    const string USER = 'my_user';
    const string PASSWORD = 'my_password';

    /**
     * @var PDO
     */
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=' . self::HOST . ';port=' . self::PORT . ';dbname=' . self::DB_NAME,
            self::USER,
            self::PASSWORD
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param string $sql
     * @return array|false
     */
    public function query(string $sql): array|false
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $results = ['error' => $e->getMessage()];
        }

        return $results;
    }
}
