<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/upload.php';

const MEDIA_PER_PAGE = 36;

/* ------------------------------------------------------------------ index */

function index(): void
{
    require_login();
    require_can('media.upload');

    $search = input('q', '') ?: '';
    $filter = input('filter', '') ?: '';

    $where  = ['1 = 1'];
    $params = [];

    if ($search !== '') {
        $where[]  = '(m.alt LIKE ? OR m.path LIKE ? OR m.title LIKE ?)';
        $term     = '%' . $search . '%';
        array_push($params, $term, $term, $term);
    }

    if ($filter === 'unused') {
        $where[] = media_usage_clause() . ' = 0';
    } elseif ($filter === 'no-alt') {
        $where[] = "(m.alt IS NULL OR m.alt = '')";
    }

    $clause = 'WHERE ' . implode(' AND ', $where);
    $total  = (int) db_value("SELECT COUNT(*) FROM media m {$clause}", $params);

    $page   = max(1, (int) (input('page', '1') ?: 1));
    $pages  = max(1, (int) ceil($total / MEDIA_PER_PAGE));
    $page   = min($page, $pages);
    $offset = ($page - 1) * MEDIA_PER_PAGE;

    $rows = db_all(
        "SELECT m.*, " . media_usage_clause() . " AS uses
         FROM media m
         {$clause}
         ORDER BY m.created_at DESC, m.id DESC
         LIMIT " . MEDIA_PER_PAGE . " OFFSET {$offset}",
        $params
    );

    admin_view('media-index', [
        'title'   => 'Media',
        'rows'    => $rows,
        'total'   => $total,
        'page'    => $page,
        'pages'   => $pages,
        'search'  => $search,
        'filter'  => $filter,
        'counts'  => [
            'all'    => (int) db_value('SELECT COUNT(*) FROM media m'),
            'unused' => (int) db_value('SELECT COUNT(*) FROM media m WHERE ' . media_usage_clause() . ' = 0'),
            'noAlt'  => (int) db_value("SELECT COUNT(*) FROM media m WHERE m.alt IS NULL OR m.alt = ''"),
        ],
    ]);
}

/* -------------------------------------------------------------- one image */

function show(string $id): void
{
    require_login();
    require_can('media.upload');

    $media = db_one('SELECT * FROM media WHERE id = ?', [(int) $id]);

    if ($media === null) {
        flash('error', 'That image is no longer in the library.');
        redirect(admin_url('media'));
    }

    admin_view('media-edit', [
        'title' => 'Image details',
        'media' => $media,
        'usage' => media_usage((int) $id),
    ]);
}

function update(string $id): void
{
    require_login();
    require_can('media.upload');

    $id    = (int) $id;
    $media = db_one('SELECT * FROM media WHERE id = ?', [$id]);

    if ($media === null) {
        flash('error', 'That image is no longer in the library.');
        redirect(admin_url('media'));
    }

    $alt = trim((string) input('alt', ''));

    if ($alt === '') {
        flash('error', 'Describe the image. Screen readers and search engines both need it.');
        redirect(admin_url('media/' . $id));
    }

    db_update('media', $id, [
        'alt'   => mb_substr($alt, 0, 255),
        'title' => mb_substr((string) input('title', ''), 0, 190) ?: null,
    ]);

    log_activity('updated', 'media', (string) $id, 'Edited image description');
    flash('success', 'Saved.');
    redirect(admin_url('media/' . $id));
}

/* ----------------------------------------------------------------- upload */

function upload(): void
{
    require_login();
    require_can('media.upload');

    if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'])) {
        flash('error', 'No file was chosen.');
        redirect(admin_url('media'));
    }

    $added    = 0;
    $problems = [];

    foreach ($_FILES['images']['name'] as $i => $name) {
        if (($_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $result = store_upload([
            'name'     => $name,
            'type'     => $_FILES['images']['type'][$i],
            'tmp_name' => $_FILES['images']['tmp_name'][$i],
            'error'    => $_FILES['images']['error'][$i],
            'size'     => $_FILES['images']['size'][$i],
        ], (string) input('alt', ''));

        if (!$result['ok']) {
            $problems[] = $name . ': ' . $result['error'];
            continue;
        }

        $added++;

        if (!empty($result['warning'])) {
            $problems[] = $result['warning'];
        }
    }

    if ($added > 0) {
        log_activity('uploaded', 'media', null, $added . ' image' . ($added === 1 ? '' : 's') . ' added');
        flash('success', $added . ' image' . ($added === 1 ? '' : 's')
            . ' added. Give each one a description so it works for everyone.');
    }

    if ($problems !== []) {
        flash('error', implode(' ', $problems));
    }

    redirect(admin_url('media' . ($added > 0 ? '?filter=no-alt' : '')));
}

/* ----------------------------------------------------------------- delete */

function destroy(string $id): void
{
    require_login();
    require_can('media.delete');

    $id    = (int) $id;
    $media = db_one('SELECT * FROM media WHERE id = ?', [$id]);

    if ($media === null) {
        flash('error', 'That image is no longer in the library.');
        redirect(admin_url('media'));
    }

    $usage = media_usage($id);

    // Refuse rather than silently leaving a page with a missing picture.
    if ($usage !== []) {
        flash('error', 'That image is still used by ' . count($usage) . ' item'
            . (count($usage) === 1 ? '' : 's') . '. Remove it there first.');
        redirect(admin_url('media/' . $id));
    }

    delete_media($id);
    log_activity('deleted', 'media', (string) $id, 'Deleted ' . $media['path']);
    flash('success', 'Image deleted.');
    redirect(admin_url('media'));
}

/* ---------------------------------------------------------------- helpers */

/** A correlated count of everywhere a media row is referenced. */
function media_usage_clause(): string
{
    return '('
        . '(SELECT COUNT(*) FROM listing_images li WHERE li.media_id = m.id)'
        . ' + (SELECT COUNT(*) FROM listings l WHERE l.cover_id = m.id)'
        . ' + (SELECT COUNT(*) FROM posts p WHERE p.cover_id = m.id)'
        . ' + (SELECT COUNT(*) FROM pages pg WHERE pg.og_image_id = m.id)'
        . ')';
}

/**
 * Where an image is actually used, for the detail screen.
 *
 * @return array<int, array{label: string, href: string, context: string}>
 */
function media_usage(int $id): array
{
    $usage = [];

    foreach (db_all(
        'SELECT DISTINCT l.id, l.ref, l.title
         FROM listings l
         LEFT JOIN listing_images li ON li.listing_id = l.id
         WHERE li.media_id = ? OR l.cover_id = ?',
        [$id, $id]
    ) as $row) {
        $usage[] = [
            'label'   => $row['title'],
            'href'    => admin_url('listings/' . $row['id']),
            'context' => 'Property ' . $row['ref'],
        ];
    }

    foreach (db_all('SELECT id, title FROM posts WHERE cover_id = ?', [$id]) as $row) {
        $usage[] = [
            'label'   => $row['title'],
            'href'    => admin_url('blog/' . $row['id']),
            'context' => 'Article cover',
        ];
    }

    foreach (db_all('SELECT id, name FROM pages WHERE og_image_id = ?', [$id]) as $row) {
        $usage[] = [
            'label'   => $row['name'],
            'href'    => admin_url('pages/' . $row['id']),
            'context' => 'Page sharing image',
        ];
    }

    return $usage;
}

/** Recent library images, for the picker inside the listing editor. */
function recent_media(int $limit = 24, array $excludeIds = []): array
{
    if ($excludeIds === []) {
        return db_all('SELECT * FROM media ORDER BY created_at DESC, id DESC LIMIT ' . (int) $limit);
    }

    $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));

    return db_all(
        "SELECT * FROM media WHERE id NOT IN ($placeholders)
         ORDER BY created_at DESC, id DESC LIMIT " . (int) $limit,
        $excludeIds
    );
}
