<?php
declare(strict_types=1);

function show_dashboard(): void
{
    require_login();

    $stats = [
        'new_enquiries' => (int) db_value("SELECT COUNT(*) FROM enquiries WHERE status = 'new'"),
        'open_enquiries'=> (int) db_value("SELECT COUNT(*) FROM enquiries WHERE closed_at IS NULL"),
        'published'     => (int) db_value('SELECT COUNT(*) FROM listings WHERE published_at IS NOT NULL AND archived_at IS NULL'),
        'drafts'        => (int) db_value('SELECT COUNT(*) FROM listings WHERE published_at IS NULL AND archived_at IS NULL'),
    ];

    $byBasis = db_all(
        'SELECT basis, COUNT(*) AS total FROM listings
         WHERE archived_at IS NULL GROUP BY basis ORDER BY total DESC'
    );

    $recentEnquiries = can('inbox.view')
        ? db_all('SELECT id, type, name, email, status, created_at FROM enquiries ORDER BY created_at DESC LIMIT 6')
        : [];

    $recentActivity = db_all('SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 8');

    admin_view('dashboard', [
        'title'           => 'Dashboard',
        'stats'           => $stats,
        'byBasis'         => $byBasis,
        'recentEnquiries' => $recentEnquiries,
        'recentActivity'  => $recentActivity,
    ]);
}
