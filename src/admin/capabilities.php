<?php
declare(strict_types=1);

/**
 * Roles are a capability map, not scattered `if ($role === 'admin')` checks.
 *
 * Adding an Editor or Agent role later is a new entry in ROLES plus the
 * capabilities it should hold. Nothing else in the codebase changes.
 *
 * The guiding split: an Admin can change anything a visitor sees, and nothing
 * that could lock the company out of its own site or break where enquiries are
 * delivered.
 */

const CAPABILITIES = [
    'listings.view'    => 'See the property list',
    'listings.edit'    => 'Create and edit property',
    'listings.publish' => 'Publish and unpublish property',
    'listings.archive' => 'Archive property',
    'listings.delete'  => 'Permanently delete property',
    'pages.edit'       => 'Edit page contents',
    'blog.edit'        => 'Write and publish articles',
    'careers.edit'     => 'Manage vacancies',
    'services.edit'    => 'Manage services',
    'media.upload'     => 'Upload and replace images',
    'media.delete'     => 'Delete images from the library',
    'inbox.view'       => 'Read enquiries',
    'inbox.manage'     => 'Assign, note and close enquiries',
    'settings.company' => 'Edit company details',
    'settings.maintenance' => 'Open and close the public site',
    'settings.email'   => 'Change notification recipients and mail settings',
    'settings.nav'     => 'Change navigation and footer structure',
    'users.manage'     => 'Invite and manage staff accounts',
    'activity.view'    => 'See the activity log',
];

const ROLES = [
    'superadmin' => [
        'label'        => 'Superadmin',
        'description'  => 'Full access, including staff accounts and mail settings.',
        'capabilities' => ['*'],
    ],
    'admin' => [
        'label'       => 'Admin',
        'description' => 'Everything a visitor sees. Cannot manage staff or mail settings.',
        'capabilities' => [
            'listings.view', 'listings.edit', 'listings.publish', 'listings.archive',
            'pages.edit', 'blog.edit', 'careers.edit', 'services.edit',
            'media.upload',
            'inbox.view', 'inbox.manage',
            'settings.company', 'settings.maintenance',
        ],
    ],
];

if (!function_exists('role_label')) {
    function role_label(?string $role): string
    {
        return ROLES[$role ?? '']['label'] ?? 'Unknown';
    }
}

if (!function_exists('role_capabilities')) {
    /** @return array<int, string> */
    function role_capabilities(?string $role): array
    {
        $caps = ROLES[$role ?? '']['capabilities'] ?? [];

        return $caps === ['*'] ? array_keys(CAPABILITIES) : $caps;
    }
}

if (!function_exists('can')) {
    /**
     * The single authorisation question. Called on every admin route handler,
     * not just when deciding whether to draw a menu item.
     */
    function can(string $capability, ?array $user = null): bool
    {
        $user ??= current_user();

        if ($user === null || ($user['status'] ?? '') !== 'active') {
            return false;
        }

        return in_array($capability, role_capabilities($user['role'] ?? null), true);
    }
}

if (!function_exists('require_can')) {
    /** Stop the request unless the current user holds the capability. */
    function require_can(string $capability): void
    {
        if (can($capability)) {
            return;
        }

        http_response_code(403);
        admin_view('error', [
            'code'    => 403,
            'title'   => 'Not your area',
            'message' => 'Your account does not have permission to do that. '
                . 'If you think it should, ask a Superadmin.',
        ]);
        exit;
    }
}
