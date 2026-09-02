<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/settings.php';

function index(): void
{
    require_login();

    // Settings is a set of tabs, each behind its own capability. Land the user
    // on the first one they can actually open.
    $tabs = settings_tabs();

    if ($tabs === []) {
        require_can('settings.company');
    }

    $current = (string) input('tab', '');
    if (!array_key_exists($current, $tabs)) {
        $current = (string) array_key_first($tabs);
    }

    require_can($tabs[$current]['cap']);

    admin_view('settings', [
        'title'   => 'Settings',
        'tabs'    => $tabs,
        'current' => $current,
        'rows'    => settings_group($current),
    ]);
}

function save(): void
{
    require_login();

    $tabs    = settings_tabs();
    $current = (string) input('tab', '');

    if (!array_key_exists($current, $tabs)) {
        flash('error', 'Unknown settings section.');
        redirect(admin_url('settings'));
    }

    require_can($tabs[$current]['cap']);

    $user    = current_user();
    $changed = [];

    foreach (settings_group($current) as $row) {
        $key = $row['setting_key'];
        $old = (string) ($row['value'] ?? '');

        // An unchecked checkbox sends nothing, which is the "off" state.
        $new = $row['type'] === 'bool'
            ? (isset($_POST[$key]) ? '1' : '0')
            : trim((string) ($_POST[$key] ?? ''));

        if ($new === $old) {
            continue;
        }

        set_setting($key, $new, $user['id'] ?? null);
        $changed[$key] = ['from' => $old, 'to' => $new];
    }

    if ($changed === []) {
        flash('success', 'Nothing to change.');
        redirect(admin_url('settings?tab=' . $current));
    }

    // Opening or closing the site is worth its own log line, not one buried in
    // a list of edited fields.
    if (isset($changed['maintenance_enabled'])) {
        $on = $changed['maintenance_enabled']['to'] === '1';
        log_activity(
            $on ? 'closed site' : 'opened site',
            'settings',
            'maintenance',
            $on
                ? 'Turned maintenance mode ON. The public site is closed to visitors.'
                : 'Turned maintenance mode OFF. The public site is live again.'
        );
        flash('success', $on
            ? 'Maintenance mode is on. Visitors now see the maintenance page; you and other staff still see the site.'
            : 'Maintenance mode is off. The site is live again.');
    } else {
        log_activity('updated', 'settings', $current, 'Edited ' . $tabs[$current]['label'] . ' settings', $changed);
        flash('success', 'Saved.');
    }

    redirect(admin_url('settings?tab=' . $current));
}

/**
 * The tabs this user may open. Keyed by settings group_name.
 *
 * @return array<string, array{label: string, cap: string, blurb: string}>
 */
function settings_tabs(): array
{
    $all = [
        'maintenance' => [
            'label' => 'Maintenance',
            'cap'   => 'settings.maintenance',
            'blurb' => 'Close the public site while you work on it. Signed-in staff keep full access.',
        ],
    ];

    return array_filter($all, static fn (array $tab): bool => can($tab['cap']));
}
