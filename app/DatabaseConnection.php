<?php

/**
 * Database Connection
 *
 * @class DatabaseConnection
 */
class DatabaseConnection
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $env = $this->loadEnv('.env');

        $this->pdo = new PDO(
            'mysql:host=' . $env['DB_HOST'] . ';port=' . $env['DB_PORT'] . ';dbname=' . $env['DB_NAME'],
            $env['DB_USER'],
            $env['DB_PASSWORD']
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

    /**
     * @param string $file
     * @return array
     * @throws Exception
     */
    function loadEnv(string $file): array
    {
        $env = [];
        if (file_exists($file)) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with($line, '#')) {
                    continue;
                }
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        } else {
            throw new Exception('File .env not found.');
        }

        return $env;
    }
}
