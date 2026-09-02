<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Editable page content.
 *
 * Every band on a public page is a row in `page_sections`. The row's `data`
 * column holds only the fields an editor has changed; everything else falls
 * back to the wording in src/content-schema.php, which is what the site shipped
 * with. That means the front end renders correctly before anything is edited,
 * the admin opens with the live copy in its fields rather than an empty box,
 * and clearing a field puts the original wording back.
 *
 * Templates read through content(), content_html(), content_items() and
 * content_lines(), all of which act on the section currently being rendered.
 */

/** Icons an editor can pick, in the order they are offered. */
const CONTENT_ICONS = [
    'check', 'shield-check', 'badge-check', 'file-check', 'users', 'globe', 'landmark',
    'building', 'key', 'hard-hat', 'camera', 'play', 'compass', 'calendar', 'clock',
    'chart', 'wallet', 'target', 'eye', 'heart', 'lock', 'wrench', 'scale', 'sofa',
    'plane', 'map-pin', 'phone', 'mail', 'whatsapp', 'quote', 'layers', 'search',
];

/** Inline markup a `rich` field may keep. */
const CONTENT_RICH_TAGS = '<strong><b><em><i><br><a>';

if (!function_exists('content_schema')) {
    /** @return array<string, array{locked?: bool, fields: array}> */
    function content_schema(): array
    {
        static $schema = null;

        return $schema ??= require __DIR__ . '/content-schema.php';
    }
}

if (!function_exists('content_fields')) {
    /** The field definitions for one section key, empty if it has none yet. */
    function content_fields(string $sectionKey): array
    {
        return content_schema()[$sectionKey]['fields'] ?? [];
    }
}

if (!function_exists('content_locked')) {
    /** Sections the page cannot sensibly render without. */
    function content_locked(string $sectionKey): bool
    {
        return (bool) (content_schema()[$sectionKey]['locked'] ?? false);
    }
}

if (!function_exists('content_defaults')) {
    /** @return array<string, mixed> field key => shipped value */
    function content_defaults(string $sectionKey): array
    {
        static $cache = [];

        if (isset($cache[$sectionKey])) {
            return $cache[$sectionKey];
        }

        $defaults = [];
        foreach (content_fields($sectionKey) as $field) {
            $defaults[$field['key']] = $field['default'] ?? ($field['type'] === 'list' || $field['type'] === 'lines' ? [] : '');
        }

        return $cache[$sectionKey] = $defaults;
    }
}

// ---------------------------------------------------------------- what is rendering

if (!function_exists('content_page')) {
    /**
     * The page slug being rendered. Set once by the layout; read by everything
     * else. Pass a slug to set it, nothing to read it.
     */
    function content_page(?string $slug = null): ?string
    {
        static $current = null;

        if ($slug !== null) {
            $current = $slug;
        }

        return $current;
    }
}

if (!function_exists('content_section')) {
    /** The section key being rendered. Returns the previous one when setting. */
    function content_section(?string $key = null, bool $set = false): ?string
    {
        static $current = null;

        if (!$set) {
            return $current;
        }

        $previous = $current;
        $current  = $key;

        return $previous;
    }
}

// ------------------------------------------------------------------------ storage

if (!function_exists('content_rows')) {
    /**
     * Every stored section for a page, keyed by section key. One query per page.
     *
     * A missing table or an unreachable row must never take the public site
     * down, so a failure here simply means "nothing has been edited".
     *
     * @return array<string, array{data: array, enabled: bool}>
     */
    function content_rows(?string $page): array
    {
        static $cache = [];

        if ($page === null || $page === '') {
            return [];
        }

        if (isset($cache[$page])) {
            return $cache[$page];
        }

        $rows = [];

        try {
            $records = db_all(
                'SELECT s.section_key, s.data, s.enabled
                   FROM page_sections s
                   JOIN pages p ON p.id = s.page_id
                  WHERE p.slug = ?',
                [$page]
            );
        } catch (Throwable $e) {
            error_log('DDREAM page content unavailable: ' . $e->getMessage());

            return $cache[$page] = [];
        }

        foreach ($records as $record) {
            $data = json_decode((string) ($record['data'] ?? ''), true);

            $rows[$record['section_key']] = [
                'data'    => is_array($data) ? $data : [],
                'enabled' => (bool) $record['enabled'],
            ];
        }

        return $cache[$page] = $rows;
    }
}

if (!function_exists('content_stored')) {
    /** What an editor has changed on one section. */
    function content_stored(string $sectionKey, ?string $page = null): array
    {
        return content_rows($page ?? content_page())[$sectionKey]['data'] ?? [];
    }
}

if (!function_exists('content_all')) {
    /** Shipped wording with any edits applied over the top. */
    function content_all(string $sectionKey, ?string $page = null): array
    {
        return content_stored($sectionKey, $page) + content_defaults($sectionKey);
    }
}

if (!function_exists('content_enabled')) {
    /**
     * Whether a band should render. Unknown sections and sections the page
     * cannot do without always render.
     */
    function content_enabled(string $sectionKey, ?string $page = null): bool
    {
        $row = content_rows($page ?? content_page())[$sectionKey] ?? null;

        return $row === null || content_locked($sectionKey) || $row['enabled'];
    }
}

// ------------------------------------------------------------------------ reading

if (!function_exists('content_tokens')) {
    /** Replace the placeholders an editor may leave in a field. */
    function content_tokens(string $text, array $extra = []): string
    {
        $tokens = ['{fee}' => (string) config('admin_fee')] + $extra;

        return strtr($text, $tokens);
    }
}

if (!function_exists('content_raw')) {
    /** A field of the section being rendered, exactly as stored. */
    function content_raw(string $name, mixed $fallback = null): mixed
    {
        $section = content_section();

        if ($section === null) {
            return $fallback;
        }

        return content_all($section)[$name] ?? $fallback;
    }
}

if (!function_exists('content')) {
    /** A plain-text field, tokens applied. Escape it at the point of output. */
    function content(string $name, array $tokens = []): string
    {
        $value = content_raw($name, '');

        return content_tokens(is_scalar($value) ? (string) $value : '', $tokens);
    }
}

if (!function_exists('content_clean_html')) {
    /**
     * Keep the small set of inline tags a `rich` field allows, drop the rest.
     *
     * strip_tags() keeps the attributes of the tags it allows, so an editor
     * account could otherwise put `onmouseover` on a `<strong>`. Every attribute
     * is therefore removed here, and an anchor is rebuilt from its href alone.
     */
    function content_clean_html(string $html): string
    {
        $html = strip_tags($html, CONTENT_RICH_TAGS);

        return (string) preg_replace_callback(
            '#<(/?)([a-z]+)\b([^>]*)>#i',
            static function (array $match): string {
                [, $slash, $tag, $attributes] = $match;
                $tag = strtolower($tag);

                if ($slash !== '' || $tag !== 'a') {
                    // One spelling of a line break, so stored copy compares cleanly.
                    return '<' . $slash . $tag . '>';
                }

                preg_match('/href\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attributes, $href);
                $target = trim(html_entity_decode($href[2] ?? $href[3] ?? $href[4] ?? '', ENT_QUOTES, 'UTF-8'));

                if (!preg_match('#^(/|\#|https?://|mailto:|tel:)#i', $target)) {
                    return '<a>';
                }

                return '<a href="' . e($target) . '">';
            },
            $html
        );
    }
}

if (!function_exists('content_html')) {
    /**
     * A `rich` field, sanitised and ready to echo without e().
     *
     * A line break an editor typed behaves like every designed one: it splits
     * the line on a wide screen and disappears on a phone, where forcing the
     * break would leave a short orphan line.
     */
    function content_html(string $name, array $tokens = []): string
    {
        return str_replace(
            '<br>',
            '<br class="hidden sm:block">',
            content_clean_html(content($name, $tokens))
        );
    }
}

if (!function_exists('content_items')) {
    /** A `list` field: rows of columns, tokens applied to every column. */
    function content_items(string $name, array $tokens = []): array
    {
        $value = content_raw($name, []);

        if (!is_array($value)) {
            return [];
        }

        $rows = [];
        foreach ($value as $row) {
            if (!is_array($row)) {
                continue;
            }

            $rows[] = array_map(
                static fn ($cell) => is_string($cell) ? content_tokens($cell, $tokens) : $cell,
                $row
            );
        }

        return $rows;
    }
}

if (!function_exists('content_lines')) {
    /** A `lines` field: a list of strings. */
    function content_lines(string $name): array
    {
        $value = content_raw($name, []);

        if (is_string($value)) {
            $value = preg_split('/\R+/', $value) ?: [];
        }

        return array_values(array_filter(
            array_map(static fn ($line): string => trim((string) $line), (array) $value),
            static fn (string $line): bool => $line !== ''
        ));
    }
}

// ------------------------------------------------------------------------- saving

if (!function_exists('content_clean_value')) {
    /**
     * Normalise one submitted field to the shape its type stores.
     *
     * @param array $field the schema definition
     * @param mixed $raw   whatever arrived in $_POST
     */
    function content_clean_value(array $field, mixed $raw): mixed
    {
        switch ($field['type']) {
            case 'lines':
                $lines = is_array($raw) ? $raw : (preg_split('/\R/', (string) $raw) ?: []);

                return array_values(array_filter(
                    array_map(static fn ($line): string => trim((string) $line), $lines),
                    static fn (string $line): bool => $line !== ''
                ));

            case 'list':
                $rows    = is_array($raw) ? $raw : [];
                $columns = $field['item'] ?? [];
                $clean   = [];

                foreach ($rows as $row) {
                    if (!is_array($row)) {
                        continue;
                    }

                    $entry = [];
                    foreach ($columns as $column) {
                        $entry[$column['key']] = content_clean_value($column, $row[$column['key']] ?? '');
                    }

                    // A row is dropped once every column is empty, which is how
                    // an editor deletes one.
                    if (implode('', array_map('strval', $entry)) !== '') {
                        $clean[] = $entry;
                    }
                }

                if (isset($field['max'])) {
                    $clean = array_slice($clean, 0, (int) $field['max']);
                }

                return $clean;

            case 'icon':
                $icon = trim((string) $raw);

                // Empty stays empty, so a blank row still counts as blank and
                // gets dropped. Anything unrecognised falls back to the tick.
                if ($icon === '') {
                    return '';
                }

                return in_array($icon, CONTENT_ICONS, true) ? $icon : 'check';

            case 'rich':
                return content_clean_html(trim(str_replace("\r\n", "\n", (string) $raw)));

            default:
                return trim(str_replace("\r\n", "\n", (string) $raw));
        }
    }
}

if (!function_exists('content_collect')) {
    /**
     * Turn one section's submitted fields into what should be stored.
     *
     * Anything still identical to the shipped wording is left out, so the row
     * records edits rather than a copy of the site.
     *
     * @param array $submitted field key => posted value
     */
    function content_collect(string $sectionKey, array $submitted): array
    {
        $defaults = content_defaults($sectionKey);
        $stored   = [];

        foreach (content_fields($sectionKey) as $field) {
            $key   = $field['key'];
            $value = content_clean_value($field, $submitted[$key] ?? '');

            if ($value !== ($defaults[$key] ?? null)) {
                $stored[$key] = $value;
            }
        }

        return $stored;
    }
}

// -------------------------------------------------------------- page title and meta

if (!function_exists('content_page_meta')) {
    /**
     * The title, description and social image an editor set for a page.
     *
     * @return array{title: ?string, description: ?string, image: ?string}
     */
    function content_page_meta(?string $page): array
    {
        static $cache = [];

        $empty = ['title' => null, 'description' => null, 'image' => null];

        if ($page === null || $page === '') {
            return $empty;
        }

        if (isset($cache[$page])) {
            return $cache[$page];
        }

        try {
            $row = db_one(
                'SELECT p.title, p.meta_description, m.path
                   FROM pages p
              LEFT JOIN media m ON m.id = p.og_image_id
                  WHERE p.slug = ?',
                [$page]
            );
        } catch (Throwable $e) {
            error_log('DDREAM page meta unavailable: ' . $e->getMessage());

            return $cache[$page] = $empty;
        }

        if ($row === null) {
            return $cache[$page] = $empty;
        }

        return $cache[$page] = [
            'title'       => ($row['title'] ?? '') !== '' ? (string) $row['title'] : null,
            'description' => ($row['meta_description'] ?? '') !== '' ? (string) $row['meta_description'] : null,
            'image'       => ($row['path'] ?? '') !== '' ? (string) $row['path'] : null,
        ];
    }
}
