<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Recognise a signed-in staff member from the public side of the site.
 *
 * Used only by the maintenance gate, so that staff can browse the live site
 * while it is closed to visitors. Deliberately cheap: if there is no session
 * cookie the function returns immediately without starting a session, so an
 * anonymous visitor never pays for this and never gets a session cookie.
 */

if (!function_exists('current_staff')) {
    /** @return array<string, mixed>|null */
    function current_staff(): ?array
    {
        static $staff  = null;
        static $looked = false;

        if ($looked) {
            return $staff;
        }
        $looked = true;

        if (session_status() !== PHP_SESSION_ACTIVE) {
            // No cookie means no session to read. Do not create one.
            if (empty($_COOKIE['ddream_admin'])) {
                return null;
            }

            session_name('ddream_admin');
            session_start(['read_and_close' => true]);
        }

        $id = $_SESSION['user_id'] ?? null;

        if (!$id) {
            return null;
        }

        $user = db_one(
            "SELECT id, name, email, role, status FROM users
             WHERE id = ? AND status = 'active'",
            [$id]
        );

        return $staff = $user;
    }
}

if (!function_exists('staff_can_bypass_maintenance')) {
    /**
     * Only Admins and Superadmins pass through a closed site. The check is on
     * the role rather than merely being signed in, so a future read-only role
     * would be held at the door with everyone else.
     */
    function staff_can_bypass_maintenance(): bool
    {
        $user = current_staff();

        return $user !== null
            && in_array($user['role'], ['admin', 'superadmin'], true);
    }
}
