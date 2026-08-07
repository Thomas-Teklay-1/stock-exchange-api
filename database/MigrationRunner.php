<?php

declare(strict_types=1);

namespace Database;

use App\Config\Database;
use PDO;
use RuntimeException;

class MigrationRunner
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public function migrate(): void
    {
        $this->createMigrationTable();

        $executed = $this->executedMigrations();

        $files = glob(__DIR__ . '/migrations/*.sql');

        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);

            if (in_array($filename, $executed, true)) {
                echo "Skipping {$filename}\n";
                continue;
            }

            echo "Running {$filename}...\n";

            $sql = file_get_contents($file);

            if ($sql === false) {
                throw new RuntimeException(
                    "Unable to read {$filename}"
                );
            }

            try {

                $this->connection->exec($sql);

                $this->recordMigration($filename);

                echo "Completed {$filename}\n";

            } catch (\Throwable $exception) {

                throw $exception;
            }
        }

        echo "\nDatabase is up to date.\n";
    }

    private function createMigrationTable(): void
    {
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                migration_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function executedMigrations(): array
    {
        $statement = $this->connection->query(
            "SELECT migration FROM migrations"
        );

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    private function recordMigration(string $migration): void
    {
        $statement = $this->connection->prepare("
            INSERT INTO migrations (migration)
            VALUES (:migration)
        ");

        $statement->execute([
            'migration' => $migration
        ]);
    }
}