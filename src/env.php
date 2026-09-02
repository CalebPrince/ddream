<?php
declare(strict_types=1);

/**
 * Minimal .env reader. The file lives above the web root and is never committed.
 * No dependency on any dotenv package, because cPanel hosting has no Composer.
 */

if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string
    {
        static $values = null;

        if ($values === null) {
            $values = [];
            $file   = dirname(__DIR__) . '/.env';

            if (is_readable($file)) {
                foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                        continue;
                    }
                    [$k, $v] = explode('=', $line, 2);
                    $v = trim($v);
                    // Strip surrounding quotes if present.
                    if (strlen($v) > 1 && ($v[0] === '"' || $v[0] === "'") && $v[-1] === $v[0]) {
                        $v = substr($v, 1, -1);
                    }
                    $values[trim($k)] = $v;
                }
            }
        }

        $value = $values[$key] ?? getenv($key);

        return ($value === false || $value === '') ? $default : (string) $value;
    }
}
