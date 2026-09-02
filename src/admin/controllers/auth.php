<?php
declare(strict_types=1);

function show_login(): void
{
    // Already signed in? Nothing to do here.
    if (current_user() !== null) {
        redirect(admin_url());
    }

    admin_view('login', [
        'bare'     => true,
        'error'    => null,
        'timedOut' => isset($_GET['timeout']),
        'next'     => safe_next(input('next', '')),
    ]);
}

function do_login(): void
{
    $email    = (string) input('email', '');
    $password = (string) ($_POST['password'] ?? '');
    $next     = safe_next(input('next', ''));

    $result = attempt_login($email, $password);

    if (!$result['ok']) {
        remember_old(['email' => $email]);

        admin_view('login', [
            'bare'     => true,
            'error'    => $result['error'] ?? 'Sign in failed.',
            'timedOut' => false,
            'next'     => $next,
        ]);

        return;
    }

    forget_old();
    redirect($next !== '' ? $next : admin_url());
}

function do_logout(): void
{
    $user = current_user();

    if ($user !== null) {
        log_activity('signed out', 'user', (string) $user['id'], $user['name'] . ' signed out', [], $user);
    }

    sign_out();
    redirect(admin_url('login'));
}

/**
 * Only ever redirect to a path inside this admin, never to a URL supplied by
 * whoever crafted the link.
 */
function safe_next(?string $next): string
{
    $next = (string) $next;

    if ($next === '' || !str_starts_with($next, '/admin')) {
        return '';
    }

    // Reject protocol-relative and anything with a host in it.
    if (str_starts_with($next, '//') || str_contains($next, '://')) {
        return '';
    }

    return $next;
}
