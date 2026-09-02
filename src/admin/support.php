<?php
declare(strict_types=1);

/**
 * Small pieces the admin leans on everywhere: CSRF, flash messages, redirects,
 * activity logging and view rendering.
 */

// ---------------------------------------------------------------- redirects

if (!function_exists('redirect')) {
    function redirect(string $path, int $status = 302): never
    {
        header('Location: ' . $path, true, $status);
        exit;
    }
}

// --------------------------------------------------------------------- CSRF

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf')) {
    /** Called on every POST before anything is written. */
    function verify_csrf(): void
    {
        $sent = $_POST['_token'] ?? '';

        if (is_string($sent) && $sent !== '' && hash_equals(csrf_token(), $sent)) {
            return;
        }

        http_response_code(419);
        admin_view('error', [
            'code'    => 419,
            'title'   => 'That form expired',
            'message' => 'You were away long enough for the page to go stale. '
                . 'Go back, reload, and submit it again.',
        ]);
        exit;
    }
}

// ------------------------------------------------------------------ flashes

if (!function_exists('flash')) {
    /** Queue a one-shot message for the next page load. */
    function flash(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('take_flashes')) {
    /** @return array<int, array{type: string, message: string}> */
    function take_flashes(): array
    {
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return $flashes;
    }
}

// ------------------------------------------------------------------- inputs

if (!function_exists('input')) {
    function input(string $key, ?string $default = null): ?string
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }
}

if (!function_exists('old')) {
    /** Repopulate a form after a validation failure. */
    function old(string $key, ?string $fallback = ''): string
    {
        return (string) ($_SESSION['old'][$key] ?? $fallback);
    }
}

if (!function_exists('remember_old')) {
    function remember_old(array $data, array $except = ['password', '_token']): void
    {
        foreach ($except as $key) {
            unset($data[$key]);
        }
        $_SESSION['old'] = $data;
    }
}

if (!function_exists('forget_old')) {
    function forget_old(): void
    {
        unset($_SESSION['old']);
    }
}

// -------------------------------------------------------------- activity log

if (!function_exists('log_activity')) {
    /**
     * Record a write. Called after the change succeeds, never before.
     *
     * @param array<string, mixed> $changes  before/after pairs, already filtered
     * @param array<string, mixed>|null $as  act as this user (used during sign in)
     */
    function log_activity(
        string $action,
        string $entity,
        ?string $entityId = null,
        ?string $summary = null,
        array $changes = [],
        ?array $as = null
    ): void {
        $user = $as ?? current_user();

        db_insert('activity_log', [
            'user_id'    => $user['id']   ?? null,
            'user_name'  => $user['name'] ?? null,
            'action'     => mb_substr($action, 0, 32),
            'entity'     => mb_substr($entity, 0, 48),
            'entity_id'  => $entityId !== null ? mb_substr($entityId, 0, 64) : null,
            'summary'    => $summary !== null ? mb_substr($summary, 0, 255) : null,
            'changes'    => $changes === [] ? null : json_encode($changes, JSON_UNESCAPED_SLASHES),
            'ip'         => client_ip(),
            'created_at' => now(),
        ]);
    }
}

// ------------------------------------------------------------------- views

if (!function_exists('admin_view')) {
    /**
     * Render an admin view inside the shell.
     * Pass `bare => true` for pages with no sidebar, such as sign in.
     */
    function admin_view(string $view, array $data = []): void
    {
        $data['view'] = $view;
        extract($data, EXTR_SKIP);

        $viewFile = __DIR__ . '/views/' . $view . '.php';

        if (!empty($data['bare'])) {
            require $viewFile;

            return;
        }

        // The shell requires $viewFile itself, so the sidebar wraps the page.
        require __DIR__ . '/views/layout.php';
    }
}

if (!function_exists('admin_url')) {
    function admin_url(string $path = ''): string
    {
        return '/admin' . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }
}

if (!function_exists('is_current')) {
    /** Sidebar highlighting. Matches the section, not the exact URL. */
    function is_current(string $section): bool
    {
        $path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);

        return $section === ''
            ? rtrim($path, '/') === '/admin'
            : str_starts_with($path, '/admin/' . $section);
    }
}

if (!function_exists('nice_date')) {
    function nice_date(?string $datetime, string $format = 'j M Y, H:i'): string
    {
        if ($datetime === null || $datetime === '') {
            return '';
        }

        return date($format, strtotime($datetime));
    }
}

if (!function_exists('time_ago')) {
    function time_ago(?string $datetime): string
    {
        if ($datetime === null || $datetime === '') {
            return '';
        }

        $seconds = time() - strtotime($datetime);

        return match (true) {
            $seconds < 60      => 'just now',
            $seconds < 3600    => (int) floor($seconds / 60) . ' min ago',
            $seconds < 86400   => (int) floor($seconds / 3600) . ' hr ago',
            $seconds < 604800  => (int) floor($seconds / 86400) . ' days ago',
            default            => nice_date($datetime, 'j M Y'),
        };
    }
}
