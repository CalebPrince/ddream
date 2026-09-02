<?php
declare(strict_types=1);

function index(): void
{
    require_login();
    require_can('activity.view');

    $page = max(1, (int) input('page', '1'));
    $perPage = 30;
    $total = (int) db_value('SELECT COUNT(*) FROM activity_log');
    $offset = ($page - 1) * $perPage;
    $rows = db_all("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}");

    admin_view('activity-index', [
        'title' => 'Activity', 'rows' => $rows, 'page' => $page,
        'pages' => max(1, (int) ceil($total / $perPage)), 'total' => $total,
    ]);
}
