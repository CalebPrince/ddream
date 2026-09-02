<?php
declare(strict_types=1);

/**
 * Sessions, sign in, sign out, and login throttling.
 *
 * Passwords use Argon2id where the host supports it and bcrypt otherwise;
 * password_verify() reads the algorithm from the stored hash, so a host that
 * gains Argon2 support later keeps working with existing accounts.
 */

const LOGIN_MAX_ATTEMPTS = 5;      // per email and per IP
const LOGIN_WINDOW       = 900;    // 15 minutes
const SESSION_IDLE       = 7200;   // 2 hours

if (!function_exists('password_algo')) {
    function password_algo(): string
    {
        return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
    }
}

if (!function_exists('clear_legacy_session_cookie')) {
    /**
     * Earlier builds scoped the session cookie to /admin. A browser that still
     * holds one sends both it and the new site-wide cookie under the same name,
     * and whichever arrives first wins, which can leave a signed-in user stuck
     * at the login screen. Expire the old one explicitly.
     */
    function clear_legacy_session_cookie(): void
    {
        if (headers_sent()) {
            return;
        }

        setcookie('ddream_admin', '', [
            'expires'  => time() - 42000,
            'path'     => '/admin',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

if (!function_exists('start_admin_session')) {
    function start_admin_session(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $https = (($_SERVER['HTTPS'] ?? '') !== '' && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

        session_set_cookie_params([
            'lifetime' => 0,
            // Site-wide rather than /admin, so the maintenance gate on the public
            // side can recognise a signed-in staff member.
            'path'     => '/',
            'httponly' => true,
            'secure'   => $https,
            'samesite' => 'Lax',
        ]);
        session_name('ddream_admin');
        session_start();

        // Idle timeout.
        if (isset($_SESSION['last_seen']) && (time() - (int) $_SESSION['last_seen']) > SESSION_IDLE) {
            sign_out();
            redirect('/admin/login?timeout=1');
        }
        $_SESSION['last_seen'] = time();
    }
}

if (!function_exists('current_user')) {
    /** @return array<string, mixed>|null */
    function current_user(): ?array
    {
        static $user = null;
        static $looked = false;

        if ($looked) {
            return $user;
        }
        $looked = true;

        $id = $_SESSION['user_id'] ?? null;
        if (!$id) {
            return null;
        }

        $user = db_one(
            'SELECT id, name, email, role, status, last_login_at FROM users WHERE id = ?',
            [$id]
        );

        // An account suspended mid-session loses access on the next request.
        if ($user === null || $user['status'] !== 'active') {
            sign_out();
            $user = null;
        }

        return $user;
    }
}

if (!function_exists('require_login')) {
    function require_login(): array
    {
        $user = current_user();

        if ($user === null) {
            $target = $_SERVER['REQUEST_URI'] ?? '/admin';
            redirect('/admin/login?next=' . urlencode($target));
        }

        return $user;
    }
}

if (!function_exists('login_locked')) {
    /**
     * How many seconds the caller must wait, or 0 if they may try now.
     * Counted per email and per IP so one does not mask the other.
     */
    function login_locked(string $email, string $ip): int
    {
        $since = (new DateTimeImmutable('-' . LOGIN_WINDOW . ' seconds'))->format('Y-m-d H:i:s');

        $failures = (int) db_value(
            'SELECT COUNT(*) FROM login_attempts
             WHERE succeeded = 0 AND attempted_at > ? AND (email = ? OR ip = ?)',
            [$since, $email, $ip]
        );

        if ($failures < LOGIN_MAX_ATTEMPTS) {
            return 0;
        }

        $last = db_value(
            'SELECT attempted_at FROM login_attempts
             WHERE succeeded = 0 AND (email = ? OR ip = ?)
             ORDER BY attempted_at DESC LIMIT 1',
            [$email, $ip]
        );

        $remaining = LOGIN_WINDOW - (time() - strtotime((string) $last));

        return max($remaining, 0);
    }
}

if (!function_exists('record_attempt')) {
    function record_attempt(string $email, string $ip, bool $succeeded): void
    {
        db_insert('login_attempts', [
            'email'        => mb_substr($email, 0, 190),
            'ip'           => $ip,
            'succeeded'    => $succeeded ? 1 : 0,
            'attempted_at' => now(),
        ]);

        // Keep the table small rather than growing it forever.
        db_run('DELETE FROM login_attempts WHERE attempted_at < ?', [
            (new DateTimeImmutable('-7 days'))->format('Y-m-d H:i:s'),
        ]);
    }
}

if (!function_exists('attempt_login')) {
    /**
     * Verify credentials and open a session.
     *
     * @return array{ok: bool, error?: string, wait?: int}
     */
    function attempt_login(string $email, string $password): array
    {
        $email = trim(mb_strtolower($email));
        $ip    = client_ip();

        $wait = login_locked($email, $ip);
        if ($wait > 0) {
            return [
                'ok'    => false,
                'wait'  => $wait,
                'error' => 'Too many failed attempts. Try again in '
                    . max(1, (int) ceil($wait / 60)) . ' minutes.',
            ];
        }

        $user = db_one('SELECT * FROM users WHERE email = ?', [$email]);

        // Always run a hash comparison so a missing account and a wrong password
        // take the same time and cannot be told apart.
        $hash = $user['password_hash'] ?? '$2y$12$invalidinvalidinvalidinvalidinvalidinvalidinvalidinvalidinv';

        if (!password_verify($password, $hash) || $user === null) {
            record_attempt($email, $ip, false);

            return ['ok' => false, 'error' => 'That email and password do not match.'];
        }

        if ($user['status'] !== 'active') {
            record_attempt($email, $ip, false);

            return ['ok' => false, 'error' => 'That account has been suspended.'];
        }

        // Upgrade the stored hash if the host's default has since improved.
        if (password_needs_rehash($hash, password_algo())) {
            db_update('users', (int) $user['id'], [
                'password_hash' => password_hash($password, password_algo()),
                'updated_at'    => now(),
            ]);
        }

        record_attempt($email, $ip, true);

        session_regenerate_id(true);
        clear_legacy_session_cookie();
        $_SESSION['user_id']   = (int) $user['id'];
        $_SESSION['last_seen'] = time();

        db_update('users', (int) $user['id'], ['last_login_at' => now()]);

        log_activity('signed in', 'user', (string) $user['id'], $user['name'] . ' signed in', [], $user);

        return ['ok' => true];
    }
}

if (!function_exists('sign_out')) {
    function sign_out(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'httponly' => true,
                'secure'   => $params['secure'],
                'samesite' => 'Lax',
            ]);
        }

        clear_legacy_session_cookie();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}

if (!function_exists('client_ip')) {
    function client_ip(): string
    {
        return mb_substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
    }
}
