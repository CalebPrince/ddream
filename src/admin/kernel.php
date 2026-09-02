<?php
declare(strict_types=1);

/**
 * Admin front controller. Reached from public/index.php for any /admin path.
 *
 * Routes are "METHOD /path" => [controller file, function]. Paths may contain
 * {id} placeholders, which arrive as arguments in order.
 */

require_once dirname(__DIR__) . '/helpers.php';   // e(), config(), icon(), asset()
require_once dirname(__DIR__) . '/db.php';
require_once __DIR__ . '/support.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/capabilities.php';
require_once dirname(__DIR__) . '/settings.php';

start_admin_session();

// The admin must never be indexed.
header('X-Robots-Tag: noindex, nofollow', true);
header('Referrer-Policy: same-origin');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$routes = require __DIR__ . '/routes.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path   = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/admin'), PHP_URL_PATH);
$path   = '/' . trim(substr($path, strlen('/admin')), '/');   // '/admin/inbox' -> '/inbox'

$handler = null;
$args    = [];

foreach ($routes as $definition => $target) {
    [$routeMethod, $routePath] = explode(' ', $definition, 2);

    if ($routeMethod !== $method) {
        continue;
    }

    if ($routePath === $path) {
        $handler = $target;
        break;
    }

    if (str_contains($routePath, '{')) {
        $pattern = '#^' . preg_replace('#\\\{[a-z_]+\\\}#', '([A-Za-z0-9_-]+)', preg_quote($routePath, '#')) . '$#';

        if (preg_match($pattern, $path, $matches)) {
            $handler = $target;
            $args    = array_slice($matches, 1);
            break;
        }
    }
}

if ($handler === null) {
    http_response_code(404);
    admin_view('error', [
        'code'    => 404,
        'title'   => 'No such admin page',
        'message' => 'That address does not exist. Use the sidebar to get back on track.',
    ]);
    exit;
}

[$controller, $function] = $handler;

$controllerFile = __DIR__ . '/controllers/' . $controller . '.php';

// A route can be listed before its controller is written. Say so plainly rather
// than dying with a fatal error.
if (!is_file($controllerFile)) {
    require_login();
    http_response_code(501);
    admin_view('coming-soon', ['title' => ucfirst($controller), 'section' => $controller]);
    exit;
}

require_once $controllerFile;

// Every POST is checked before the handler runs, so no controller can forget.
if ($method === 'POST') {
    verify_csrf();
}

$function(...$args);
