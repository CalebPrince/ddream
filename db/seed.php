<?php
declare(strict_types=1);

/**
 * Create the first Superadmin. CLI only.
 *
 *   php db/seed.php "Full Name" email@example.com "password"
 *
 * Safe to re-run: an existing email is updated rather than duplicated.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only.\n");
}

require __DIR__ . '/../src/db.php';

$name     = $argv[1] ?? null;
$email    = isset($argv[2]) ? strtolower(trim($argv[2])) : null;
$password = $argv[3] ?? null;

if (!$name || !$email || !$password) {
    exit("Usage: php db/seed.php \"Full Name\" email@example.com \"password\"\n");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("That is not a valid email address.\n");
}

if (strlen($password) < 12) {
    exit("Choose a password of at least 12 characters.\n");
}

$algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
$hash = password_hash($password, $algo);

$existing = db_one('SELECT id FROM users WHERE email = ?', [$email]);

if ($existing) {
    db_update('users', (int) $existing['id'], [
        'name'          => $name,
        'password_hash' => $hash,
        'role'          => 'superadmin',
        'status'        => 'active',
        'updated_at'    => now(),
    ]);
    echo "Updated existing account {$email} as Superadmin.\n";
} else {
    db_insert('users', [
        'name'          => $name,
        'email'         => $email,
        'password_hash' => $hash,
        'role'          => 'superadmin',
        'status'        => 'active',
        'created_at'    => now(),
    ]);
    echo "Created Superadmin {$email}.\n";
}

echo "Password algorithm: " . ($algo === PASSWORD_BCRYPT ? 'bcrypt' : 'argon2id') . "\n";
