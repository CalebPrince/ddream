<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';

/**
 * One PDO connection per request, plus thin query helpers.
 *
 * Everything goes through prepared statements. There is no query builder and no
 * place where a value is concatenated into SQL.
 */

if (!function_exists('db')) {
    function db(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            env('DB_HOST', '127.0.0.1'),
            env('DB_PORT', '3306'),
            env('DB_NAME', 'ddream')
        );

        try {
            $pdo = new PDO($dsn, env('DB_USER', 'root'), env('DB_PASS', ''), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]);
        } catch (PDOException $e) {
            // Never leak credentials or the DSN to the browser.
            error_log('DDREAM database connection failed: ' . $e->getMessage());
            http_response_code(503);
            exit('The site is temporarily unavailable. Please try again shortly.');
        }

        return $pdo;
    }
}

if (!function_exists('db_run')) {
    /** Run a prepared statement and return it. */
    function db_run(string $sql, array $params = []): PDOStatement
    {
        $statement = db()->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}

if (!function_exists('db_all')) {
    /** @return array<int, array<string, mixed>> */
    function db_all(string $sql, array $params = []): array
    {
        return db_run($sql, $params)->fetchAll();
    }
}

if (!function_exists('db_one')) {
    /** @return array<string, mixed>|null */
    function db_one(string $sql, array $params = []): ?array
    {
        $row = db_run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }
}

if (!function_exists('db_value')) {
    /** First column of the first row, or null. */
    function db_value(string $sql, array $params = []): mixed
    {
        $value = db_run($sql, $params)->fetchColumn();

        return $value === false ? null : $value;
    }
}

if (!function_exists('db_insert')) {
    /** Insert an associative array and return the new id. */
    function db_insert(string $table, array $data): int
    {
        $columns      = array_keys($data);
        $placeholders = array_map(static fn (string $c): string => ':' . $c, $columns);

        db_run(
            sprintf(
                'INSERT INTO `%s` (`%s`) VALUES (%s)',
                $table,
                implode('`, `', $columns),
                implode(', ', $placeholders)
            ),
            $data
        );

        return (int) db()->lastInsertId();
    }
}

if (!function_exists('db_update')) {
    /** Update one row by primary key. Returns rows affected. */
    function db_update(string $table, int $id, array $data): int
    {
        if ($data === []) {
            return 0;
        }

        $assignments = implode(
            ', ',
            array_map(static fn (string $c): string => sprintf('`%s` = :%s', $c, $c), array_keys($data))
        );

        return db_run(
            sprintf('UPDATE `%s` SET %s WHERE `id` = :__id', $table, $assignments),
            $data + ['__id' => $id]
        )->rowCount();
    }
}

if (!function_exists('now')) {
    /** MySQL DATETIME for the current moment. */
    function now(): string
    {
        return (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
    }
}
