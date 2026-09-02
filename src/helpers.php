<?php
declare(strict_types=1);

/**
 * Template helpers. Deliberately tiny: no framework, no autoloader.
 */

if (!function_exists('e')) {
    /** Escape for HTML text/attribute context. */
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('config')) {
    /** Dot-path read from src/config.php, cached for the request. */
    function config(?string $key = null, mixed $default = null): mixed
    {
        static $config = null;
        $config ??= require __DIR__ . '/config.php';

        if ($key === null) {
            return $config;
        }

        $value = $config;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('data_set')) {
    /** Load a dataset from src/data, cached for the request. */
    function data_set(string $name): array
    {
        static $cache = [];
        return $cache[$name] ??= require __DIR__ . '/data/' . $name . '.php';
    }
}

if (!function_exists('asset')) {
    /** Cache-busted asset URL. */
    function asset(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $file = dirname(__DIR__) . '/public' . $path;

        return is_file($file) ? $path . '?v=' . filemtime($file) : $path;
    }
}

if (!function_exists('section')) {
    /** Render a section template with scoped data. */
    function section(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/sections/' . $name . '.php';
    }
}

if (!function_exists('component')) {
    /** Render a component template with scoped data. */
    function component(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/components/' . $name . '.php';
    }
}

if (!function_exists('slugify')) {
    /**
     * Turn a title into a URL segment: lowercase, words joined by hyphens,
     * accents folded, everything else dropped.
     */
    function slugify(string $text, int $maxLength = 80): string
    {
        // Fold accented characters where the host has iconv.
        if (function_exists('iconv')) {
            $folded = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            if ($folded !== false) {
                $text = $folded;
            }
        }

        $slug = strtolower($text);
        $slug = (string) preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        if (strlen($slug) > $maxLength) {
            // Cut on a word boundary rather than mid-word.
            $slug = substr($slug, 0, $maxLength);
            $slug = (string) preg_replace('/-[^-]*$/', '', $slug);
        }

        return $slug === '' ? 'property' : $slug;
    }
}

if (!function_exists('money')) {
    /** Format a listing price. Ghana lists in both USD and GHS. */
    function money(int|float $amount, string $currency = 'USD'): string
    {
        $symbol = match ($currency) {
            'GHS'   => 'GH₵',
            'GBP'   => '£',
            'EUR'   => '€',
            default => '$',
        };

        return $symbol . number_format((float) $amount, 0, '.', ',');
    }
}

if (!function_exists('match_dynamic_route')) {
    /**
     * Match a path against the pattern routes in src/routes-dynamic.php.
     * Patterns use {name} placeholders, which arrive in the route as `params`.
     * Returns null when nothing matches, so the caller can fall through to 404.
     */
    function match_dynamic_route(string $path, array $dynamic): ?array
    {
        foreach ($dynamic as $pattern => $route) {
            // preg_quote escapes the braces, so unescape them before substituting.
            $quoted = str_replace(['\{', '\}'], ['{', '}'], preg_quote($pattern, '#'));
            $regex  = '#^' . preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[A-Za-z0-9_-]+)', $quoted) . '$#';

            if (!preg_match($regex, $path, $matches)) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $resolved = $route;
            $resolved['params'] = $params;
            $resolved['path']   = $path;

            // A resolver can reject the match (unknown id) or fill in title and meta.
            if (isset($route['resolve']) && is_callable($route['resolve'])) {
                $resolved = ($route['resolve'])($resolved);
                if ($resolved === null) {
                    return null;
                }
            }

            return $resolved;
        }

        return null;
    }
}

if (!function_exists('added_label')) {
    /** Turn a "days since listed" integer into display text. */
    function added_label(int $days): string
    {
        return match (true) {
            $days <= 0  => 'Added today',
            $days === 1 => 'Added yesterday',
            $days < 14  => 'Added ' . $days . ' days ago',
            $days < 60  => 'Added ' . (int) round($days / 7) . ' weeks ago',
            default     => 'Added ' . (int) round($days / 30) . ' months ago',
        };
    }
}

if (!function_exists('search_listings')) {
    /**
     * Filter and sort the inventory. Everything runs server side, so the Selling
     * pages work with JavaScript switched off and every result set is linkable.
     *
     * @param array $criteria basis, category, min, max, beds, location, sort
     */
    function search_listings(array $criteria = []): array
    {
        require_once __DIR__ . '/repositories/listings.php';

        // The repository does the filtering and sorting in SQL. The signature and
        // the returned shape are unchanged, so every caller is untouched.
        return find_listings($criteria);
    }
}

if (!function_exists('search_listings_from_file')) {
    /** Kept as a reference implementation of the filter rules. Not called. */
    function search_listings_from_file(array $criteria = []): array
    {
        $results = data_set('listings');

        $basis    = $criteria['basis']    ?? null;
        $category = $criteria['category'] ?? null;
        $min      = isset($criteria['min']) && $criteria['min'] !== '' ? (int) $criteria['min'] : null;
        $max      = isset($criteria['max']) && $criteria['max'] !== '' ? (int) $criteria['max'] : null;
        $beds     = isset($criteria['beds']) && $criteria['beds'] !== '' ? (int) $criteria['beds'] : null;
        $location = trim((string) ($criteria['location'] ?? ''));

        $results = array_values(array_filter($results, static function (array $item) use (
            $basis, $category, $min, $max, $beds, $location
        ): bool {
            if ($basis !== null && $item['basis'] !== $basis) {
                return false;
            }
            if ($category !== null && $item['category'] !== $category) {
                return false;
            }
            if ($min !== null && $item['price'] < $min) {
                return false;
            }
            if ($max !== null && $item['price'] > $max) {
                return false;
            }
            if ($beds !== null && (int) ($item['beds'] ?? 0) < $beds) {
                return false;
            }
            if ($location !== '' && stripos($item['address'] . ' ' . $item['title'], $location) === false) {
                return false;
            }

            return true;
        }));

        usort($results, static function (array $a, array $b) use ($criteria): int {
            return match ($criteria['sort'] ?? 'newest') {
                'price-asc'  => $a['price'] <=> $b['price'],
                'price-desc' => $b['price'] <=> $a['price'],
                'beds'       => ($b['beds'] ?? 0) <=> ($a['beds'] ?? 0),
                default      => $a['added'] <=> $b['added'],
            };
        });

        return $results;
    }
}

if (!function_exists('query_string')) {
    /** Current query string with overrides applied. Used by sort and filter links. */
    function query_string(array $overrides = []): string
    {
        $params = array_merge($_GET, $overrides);
        $params = array_filter(
            $params,
            static fn ($value): bool => $value !== '' && $value !== null
        );

        return $params === [] ? '' : '?' . http_build_query($params);
    }
}

if (!function_exists('icon')) {
    /**
     * Inline an icon by name.
     *
     * Interface icons are Lucide (ISC licence) at 1.5px stroke; brand marks are
     * Simple Icons (CC0) and are filled rather than stroked. See DESIGN.md §8.
     */
    function icon(string $name, string $class = 'h-5 w-5', array $attributes = []): string
    {
        static $stroke = [
            'search'      => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            'map-pin'     => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
            'bed'         => '<path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/>',
            'bath'        => '<path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-3 3L6 9"/><path d="M2 12h20"/><path d="M4 12v3a4 4 0 0 0 4 4h8a4 4 0 0 0 4-4v-3"/><path d="M7 19v2"/><path d="M17 19v2"/>',
            'ruler'       => '<path d="M3 8h18v8H3z"/><path d="M7 8v3"/><path d="M11 8v3"/><path d="M15 8v3"/><path d="M19 8v3"/>',
            'heart'       => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
            'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
            'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
            'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
            'arrow-right' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
            'arrow-up-right' => '<path d="M7 17 17 7"/><path d="M7 7h10v10"/>',
            'phone'       => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
            'mail'        => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
            'menu'        => '<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/>',
            'x'           => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
            'shield-check'=> '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1Z"/><path d="m9 12 2 2 4-4"/>',
            'globe'       => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>',
            'key'         => '<path d="m15.5 7.5 3 3L22 7l-3-3"/><path d="m21 2-9.6 9.6"/><circle cx="7.5" cy="15.5" r="5.5"/>',
            'building'    => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/>',
            'hard-hat'    => '<path d="M2 18a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1Z"/><path d="M10 10V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"/><path d="M4 15a8 8 0 0 1 16 0"/>',
            'camera'      => '<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3Z"/><circle cx="12" cy="13" r="3"/>',
            'file-check'  => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v5h6"/><path d="m9 15 2 2 4-4"/>',
            'plane'       => '<path d="M17.8 19.8 16 14l-4 1 .5 5.5-2 1.5-1.5-5-5-1.5 1.5-2L11 14l1-4-5.8-1.8 1.6-2.3 7.2 1.6 3-3a2 2 0 0 1 3 3l-3 3 1.6 7.2Z"/>',
            'users'       => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'scale'       => '<path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>',
            'sofa'        => '<path d="M20 9V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v3"/><path d="M2 11a2 2 0 0 1 2-2 2 2 0 0 1 2 2v3h12v-3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2Z"/><path d="M4 18v2"/><path d="M20 18v2"/>',
            'chart'       => '<path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="m19 9-5 5-4-4-3 3"/>',
            'wrench'      => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76Z"/>',
            'landmark'    => '<path d="M3 22h18"/><path d="M6 18v-7"/><path d="M10 18v-7"/><path d="M14 18v-7"/><path d="M18 18v-7"/><path d="m2 9 10-6 10 6Z"/>',
            'check'       => '<path d="M20 6 9 17l-5-5"/>',
            'lock'        => '<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
            'wallet'      => '<path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-2a2 2 0 0 1 0-4h3"/><path d="M3 5v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4"/>',
            'target'      => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
            'eye'         => '<path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/><circle cx="12" cy="12" r="3"/>',
            'badge-check' => '<path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/>',
            'clock'       => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
            'play'        => '<circle cx="12" cy="12" r="10"/><path d="M10 8.5 16 12l-6 3.5Z"/>',
            'quote'       => '<path d="M10 11H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v7a4 4 0 0 1-4 4"/><path d="M20 11h-4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v7a4 4 0 0 1-4 4"/>',
            'calendar'    => '<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>',
            'compass'     => '<circle cx="12" cy="12" r="10"/><path d="m16.24 7.76-2.12 6.36-6.36 2.12 2.12-6.36Z"/>',
            'layers'      => '<path d="m12 2 9 5-9 5-9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/>',
            'pause'       => '<rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/>',
            'play-solid'  => '<path d="M7 4.5v15l12-7.5Z"/>',
        ];

        static $brand = [
            'facebook'  => '<path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.25h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07Z"/>',
            'instagram' => '<path d="M12 0C8.74 0 8.33.02 7.05.07 5.77.13 4.9.33 4.14.63a5.9 5.9 0 0 0-2.13 1.38A5.9 5.9 0 0 0 .63 4.14C.33 4.9.13 5.77.07 7.05.02 8.33 0 8.74 0 12s.02 3.67.07 4.95c.06 1.28.26 2.15.56 2.91.3.79.71 1.46 1.38 2.13a5.9 5.9 0 0 0 2.13 1.38c.76.3 1.63.5 2.91.56C8.33 23.98 8.74 24 12 24s3.67-.02 4.95-.07c1.28-.06 2.15-.26 2.91-.56a5.9 5.9 0 0 0 2.13-1.38 5.9 5.9 0 0 0 1.38-2.13c.3-.76.5-1.63.56-2.91.05-1.28.07-1.69.07-4.95s-.02-3.67-.07-4.95c-.06-1.28-.26-2.15-.56-2.91a5.9 5.9 0 0 0-1.38-2.13A5.9 5.9 0 0 0 19.86.63c-.76-.3-1.63-.5-2.91-.56C15.67.02 15.26 0 12 0Zm0 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41 1.27-.06 1.65-.07 4.85-.07Zm0 3.68a6.16 6.16 0 1 0 0 12.32 6.16 6.16 0 0 0 0-12.32Zm0 10.16a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm7.85-10.41a1.44 1.44 0 1 1-2.88 0 1.44 1.44 0 0 1 2.88 0Z"/>',
            'linkedin'  => '<path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05a3.74 3.74 0 0 1 3.37-1.85c3.6 0 4.27 2.37 4.27 5.46v6.28ZM5.34 7.43a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14Zm1.78 13.02H3.55V9h3.57v11.45ZM22.22 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.72V1.72C24 .77 23.2 0 22.22 0Z"/>',
            'youtube'   => '<path d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.08 0 12 0 12s0 3.92.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.92 24 12 24 12s0-3.92-.5-5.81ZM9.55 15.57V8.43L15.82 12l-6.27 3.57Z"/>',
            'whatsapp'  => '<path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.14-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.2-.24-.58-.48-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.01-1.04 2.48s1.07 2.87 1.22 3.07c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.63.71.23 1.36.19 1.87.12.57-.09 1.76-.72 2-1.41.25-.7.25-1.29.18-1.41-.08-.13-.28-.2-.57-.35ZM12.05 21.8h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.64-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.9-9.88a9.82 9.82 0 0 1 9.88 9.89c0 5.45-4.44 9.88-9.89 9.88ZM20.52 3.49A11.8 11.8 0 0 0 12.05 0C5.5 0 .17 5.33.17 11.88c0 2.1.55 4.14 1.6 5.95L.07 24l6.33-1.66a11.86 11.86 0 0 0 5.65 1.44h.01c6.55 0 11.88-5.33 11.88-11.88a11.8 11.8 0 0 0-3.42-8.41Z"/>',
        ];

        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= ' ' . $key . '="' . e((string) $value) . '"';
        }

        if (isset($brand[$name])) {
            return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="currentColor" '
                . 'aria-hidden="true"' . $attrString . '>' . $brand[$name] . '</svg>';
        }

        $path = $stroke[$name] ?? $stroke['check'];

        return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" '
            . 'stroke="currentColor" stroke-width="1.5" stroke-linecap="round" '
            . 'stroke-linejoin="round" aria-hidden="true"' . $attrString . '>' . $path . '</svg>';
    }
}
