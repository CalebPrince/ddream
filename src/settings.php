<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Site settings, read from the database and cached for the request.
 *
 * Every setting is loaded in one query the first time any of them is asked for,
 * because a page typically wants several and they are all small.
 */

if (!function_exists('settings_all')) {
    /** @return array<string, string> */
    function settings_all(bool $refresh = false): array
    {
        static $cache = null;

        if ($cache === null || $refresh) {
            $cache = [];
            foreach (db_all('SELECT setting_key, value FROM settings') as $row) {
                $cache[$row['setting_key']] = (string) ($row['value'] ?? '');
            }
        }

        return $cache;
    }
}

if (!function_exists('setting')) {
    function setting(string $key, ?string $default = null): ?string
    {
        $value = settings_all()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }
}

if (!function_exists('setting_bool')) {
    function setting_bool(string $key): bool
    {
        return setting($key, '0') === '1';
    }
}

if (!function_exists('settings_group')) {
    /**
     * Full rows for one group, in display order, for rendering a Settings tab.
     *
     * @return array<int, array<string, mixed>>
     */
    function settings_group(string $group): array
    {
        return db_all(
            'SELECT * FROM settings WHERE group_name = ? ORDER BY sort, id',
            [$group]
        );
    }
}

if (!function_exists('set_setting')) {
    /** Write one setting. Unknown keys are ignored rather than created. */
    function set_setting(string $key, ?string $value, ?int $userId = null): bool
    {
        $existing = db_one('SELECT id FROM settings WHERE setting_key = ?', [$key]);

        if ($existing === null) {
            return false;
        }

        db_update('settings', (int) $existing['id'], [
            'value'      => $value,
            'updated_at' => now(),
            'updated_by' => $userId,
        ]);

        settings_all(true);

        return true;
    }
}
