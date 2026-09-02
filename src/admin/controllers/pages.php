<?php
declare(strict_types=1);

/**
 * Page contents.
 *
 * A page is a title and description plus an ordered list of bands. What each
 * band is made of is described in src/content-schema.php, which also holds the
 * wording the site shipped with, so the form opens with the live copy in it and
 * only stores what an editor actually changes.
 */

require_once dirname(__DIR__, 2) . '/content.php';

function index(): void
{
    require_login();
    require_can('pages.edit');

    admin_view('pages-index', [
        'title' => 'Page contents',
        'rows'  => db_all(
            'SELECT p.*, COUNT(s.id) section_count, SUM(s.enabled = 1) enabled_count
               FROM pages p
          LEFT JOIN page_sections s ON s.page_id = p.id
           GROUP BY p.id
           ORDER BY p.sort, p.name'
        ),
    ]);
}

function edit(string $id): void
{
    require_login();
    require_can('pages.edit');

    $page = db_one('SELECT * FROM pages WHERE id = ?', [(int) $id]);

    if (!$page) {
        http_response_code(404);
        admin_view('error', [
            'code'    => 404,
            'title'   => 'Page not found',
            'message' => 'That page does not exist.',
        ]);

        return;
    }

    $sections = db_all(
        'SELECT * FROM page_sections WHERE page_id = ? ORDER BY sort, id',
        [(int) $id]
    );

    // Give each band its field definitions and the values to show in them.
    foreach ($sections as $i => $section) {
        $key = $section['section_key'];

        $sections[$i]['fields'] = content_fields($key);
        $sections[$i]['values'] = content_all($key, $page['slug']);
        $sections[$i]['locked'] = content_locked($key);
    }

    admin_view('pages-form', [
        'title'    => 'Edit ' . $page['name'],
        'page'     => $page,
        'sections' => $sections,
        'media'    => db_all('SELECT id, path, alt FROM media ORDER BY created_at DESC'),
    ]);
}

function update(string $id): never
{
    require_login();
    require_can('pages.edit');

    $page = db_one('SELECT * FROM pages WHERE id = ?', [(int) $id]);

    if (!$page) {
        flash('error', 'Page not found.');
        redirect(admin_url('pages'));
    }

    $meta = (string) input('meta_description', '');

    if (mb_strlen($meta) > 255) {
        flash('error', 'Meta description must be 255 characters or fewer.');
        redirect(admin_url('pages/' . $id));
    }

    $user = current_user();

    db_update('pages', (int) $id, [
        'title'            => (string) input('title', ''),
        'meta_description' => $meta,
        'og_image_id'      => (int) input('og_image_id', '0') ?: null,
        'updated_at'       => now(),
        'updated_by'       => $user['id'],
    ]);

    $submitted = is_array($_POST['section'] ?? null) ? $_POST['section'] : [];

    foreach (db_all('SELECT id, section_key FROM page_sections WHERE page_id = ?', [(int) $id]) as $section) {
        $key   = $section['section_key'];
        $known = content_fields($key) !== [];

        $values = [
            'enabled'    => (content_locked($key) || isset($_POST['section_enabled'][$section['id']])) ? 1 : 0,
            'updated_at' => now(),
            'updated_by' => $user['id'],
        ];

        // A band with no field definitions yet keeps whatever it already holds
        // rather than being emptied by a form that could not show it.
        if ($known) {
            $stored = content_collect($key, $submitted[$section['id']] ?? []);
            $values['data'] = json_encode($stored, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        db_update('page_sections', (int) $section['id'], $values);
    }

    log_activity('updated', 'page', $id, 'Updated ' . $page['name']);
    flash('success', 'Page content saved.');
    redirect(admin_url('pages/' . $id));
}
