<?php
declare(strict_types=1);

/**
 * Front controller. Every request lands here; see src/routes.php for the map.
 */

// The PHP built-in server has no rewrite rules, so serve real files directly.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . urldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (is_file($file)) {
        return false;
    }
}

$path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$path = rtrim($path, '/');
$path = $path === '' ? '/' : $path;

// Anything under /admin is handled by the admin front controller, which has its
// own routing, session and authorisation.
if ($path === '/admin' || str_starts_with($path, '/admin/')) {
    require __DIR__ . '/../src/admin/kernel.php';
    exit;
}

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/settings.php';

// Maintenance gate. Runs after the /admin handoff above, so staff can always
// reach the sign-in page, and before routing, so nothing public is served.
if (setting_bool('maintenance_enabled')) {
    require __DIR__ . '/../src/staff.php';

    if (!staff_can_bypass_maintenance()) {
        http_response_code(503);
        header('Retry-After: 3600');
        // Never let a proxy or browser cache the closed state.
        header('Cache-Control: no-store, must-revalidate');
        require __DIR__ . '/../src/layout/maintenance.php';
        exit;
    }
}

$routes = require __DIR__ . '/../src/routes.php';

$dynamic = require __DIR__ . '/../src/routes-dynamic.php';

if (isset($routes[$path])) {
    $route = $routes[$path];
} elseif (($matched = match_dynamic_route($path, $dynamic)) !== null) {
    $route = $matched;
} else {
    http_response_code(404);
    $route = [
        'page'  => 'not-found',
        'title' => 'Page not found',
        'desc'  => 'That page does not exist yet. Search our listings or talk to an adviser.',
    ];
}

// Two different things, and conflating them put the wrong canonical URL on every
// page whose nav highlight differs from its address.
$currentPath = $route['nav'] ?? $path;   // which nav item lights up
$requestPath = $path;                    // the actual address, for canonical and og:url

require __DIR__ . '/../src/layout/document.php';
