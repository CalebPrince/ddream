<?php
declare(strict_types=1);

/**
 * Migration runner. CLI only.
 *
 *   php db/migrate.php            apply anything not yet applied
 *   php db/migrate.php --status   list what has and has not run
 *   php db/migrate.php --fresh    drop every table and re-apply (development)
 *
 * Migrations are plain .sql files in db/migrations, applied in filename order
 * and recorded in a `migrations` table so re-running is safe.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Migrations can only be run from the command line.\n");
}

require __DIR__ . '/../src/db.php';

$dir     = __DIR__ . '/migrations';
$files   = glob($dir . '/*.sql') ?: [];
sort($files);
$options = array_slice($argv, 1);

db_run(
    'CREATE TABLE IF NOT EXISTS migrations (
        id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        filename   VARCHAR(190) NOT NULL,
        applied_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_migration (filename)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$applied = array_column(db_all('SELECT filename FROM migrations'), 'filename');

/** Split a file into statements on semicolons that end a line. */
function statements(string $sql): array
{
    // Strip full-line comments so they cannot confuse the split.
    $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;

    return array_values(array_filter(
        array_map('trim', preg_split('/;\s*$/m', $sql) ?: []),
        static fn (string $s): bool => $s !== ''
    ));
}

if (in_array('--status', $options, true)) {
    echo "Migration status\n";
    foreach ($files as $file) {
        $name = basename($file);
        printf("  %-24s %s\n", $name, in_array($name, $applied, true) ? 'applied' : 'pending');
    }
    exit(0);
}

if (in_array('--fresh', $options, true)) {
    echo "Dropping all tables\n";
    db_run('SET FOREIGN_KEY_CHECKS = 0');
    foreach (db_all('SHOW TABLES') as $row) {
        $table = array_values($row)[0];
        db_run(sprintf('DROP TABLE IF EXISTS `%s`', $table));
        echo "  dropped {$table}\n";
    }
    db_run('SET FOREIGN_KEY_CHECKS = 1');

    db_run(
        'CREATE TABLE migrations (
            id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
            filename   VARCHAR(190) NOT NULL,
            applied_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_migration (filename)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
    $applied = [];
}

$ran = 0;

foreach ($files as $file) {
    $name = basename($file);

    if (in_array($name, $applied, true)) {
        continue;
    }

    echo "Applying {$name}\n";

    try {
        foreach (statements((string) file_get_contents($file)) as $statement) {
            db_run($statement);
        }
        db_run(
            'INSERT INTO migrations (filename, applied_at) VALUES (?, ?)',
            [$name, now()]
        );
        $ran++;
    } catch (Throwable $e) {
        // MySQL cannot roll back DDL, so stop at the first failure and report
        // it rather than leaving the runner to make things worse.
        fwrite(STDERR, "  FAILED: {$e->getMessage()}\n");
        exit(1);
    }
}

echo $ran === 0
    ? "Nothing to apply. Database is up to date.\n"
    : "Applied {$ran} migration" . ($ran === 1 ? "" : "s") . ".\n";
