<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/upload.php';
require_once dirname(__DIR__, 2) . '/repositories/listings.php';

/** Recent library images for the picker, excluding ones already attached. */
function library_for_picker(array $attachedIds): array
{
    if ($attachedIds === []) {
        return db_all('SELECT * FROM media ORDER BY created_at DESC, id DESC LIMIT 24');
    }

    $placeholders = implode(',', array_fill(0, count($attachedIds), '?'));

    return db_all(
        "SELECT * FROM media WHERE id NOT IN ($placeholders)
         ORDER BY created_at DESC, id DESC LIMIT 24",
        $attachedIds
    );
}

const LISTING_BASES     = ['For sale', 'To rent', 'Short stay'];
const LISTING_CATEGORIES = [
    'houses'     => 'Houses',
    'apartments' => 'Apartments',
    'commercial' => 'Commercial space',
    'land'       => 'Land',
];
const LISTINGS_PER_PAGE = 20;

/* ------------------------------------------------------------------ index */

function index(): void
{
    require_login();
    require_can('listings.view');

    $where  = [];
    $params = [];

    $state    = input('state', '') ?: '';
    $basis    = input('basis', '') ?: '';
    $category = input('category', '') ?: '';
    $search   = input('q', '') ?: '';

    match ($state) {
        'published' => $where[] = 'l.published_at IS NOT NULL AND l.archived_at IS NULL',
        'draft'     => $where[] = 'l.published_at IS NULL AND l.archived_at IS NULL',
        'archived'  => $where[] = 'l.archived_at IS NOT NULL',
        default     => $where[] = 'l.archived_at IS NULL',
    };

    if ($basis !== '') {
        $where[]  = 'l.basis = ?';
        $params[] = $basis;
    }
    if ($category !== '') {
        $where[]  = 'l.category = ?';
        $params[] = $category;
    }
    if ($search !== '') {
        $where[]  = '(l.title LIKE ? OR l.address LIKE ? OR l.ref LIKE ?)';
        $term     = '%' . $search . '%';
        array_push($params, $term, $term, $term);
    }

    $clause = 'WHERE ' . implode(' AND ', $where);
    $total  = (int) db_value("SELECT COUNT(*) FROM listings l {$clause}", $params);

    $page   = max(1, (int) (input('page', '1') ?: 1));
    $pages  = max(1, (int) ceil($total / LISTINGS_PER_PAGE));
    $page   = min($page, $pages);
    $offset = ($page - 1) * LISTINGS_PER_PAGE;

    $rows = db_all(
        "SELECT l.*, m.path AS cover_path
         FROM listings l
         LEFT JOIN media m ON m.id = l.cover_id
         {$clause}
         ORDER BY l.updated_at DESC, l.id DESC
         LIMIT " . LISTINGS_PER_PAGE . " OFFSET {$offset}",
        $params
    );

    admin_view('listings-index', [
        'title'   => 'Listings',
        'rows'    => $rows,
        'total'   => $total,
        'page'    => $page,
        'pages'   => $pages,
        'filters' => compact('state', 'basis', 'category', 'search'),
        'counts'  => [
            'all'       => (int) db_value('SELECT COUNT(*) FROM listings WHERE archived_at IS NULL'),
            'published' => (int) db_value('SELECT COUNT(*) FROM listings WHERE published_at IS NOT NULL AND archived_at IS NULL'),
            'draft'     => (int) db_value('SELECT COUNT(*) FROM listings WHERE published_at IS NULL AND archived_at IS NULL'),
            'archived'  => (int) db_value('SELECT COUNT(*) FROM listings WHERE archived_at IS NOT NULL'),
        ],
        'headerAction' => can('listings.edit')
            ? '<a href="' . admin_url('listings/new') . '" class="btn btn-primary">Add property</a>'
            : '',
    ]);
}

/* ------------------------------------------------------------- create/edit */

function create(): void
{
    require_login();
    require_can('listings.edit');

    admin_view('listings-form', [
        'title'     => 'Add property',
        'listing'   => null,
        'features'  => [],
        'images'    => [],
        'locations' => all_locations(),
        'nextRef'   => next_listing_ref(),
        'library'   => [],
    ]);
}

function edit(string $id): void
{
    require_login();
    require_can('listings.view');

    $listing = db_one('SELECT * FROM listings WHERE id = ?', [(int) $id]);

    if ($listing === null) {
        flash('error', 'That property no longer exists.');
        redirect(admin_url('listings'));
    }

    admin_view('listings-form', [
        'title'     => $listing['title'],
        'listing'   => $listing,
        'features'  => db_all('SELECT * FROM listing_features WHERE listing_id = ? ORDER BY sort, id', [$listing['id']]),
        'images'    => db_all(
            'SELECT li.id AS link_id, li.sort, m.*
             FROM listing_images li JOIN media m ON m.id = li.media_id
             WHERE li.listing_id = ? ORDER BY li.sort, li.id',
            [$listing['id']]
        ),
        'locations' => all_locations(),
        'nextRef'   => $listing['ref'],
        'library'   => library_for_picker(array_map(
            'intval',
            array_column(
                db_all('SELECT media_id FROM listing_images WHERE listing_id = ?', [$listing['id']]),
                'media_id'
            )
        )),
    ]);
}

/* ------------------------------------------------------------ store/update */

function store(): void
{
    require_login();
    require_can('listings.edit');

    $data = collect_listing_input();

    if ($data['errors'] !== []) {
        remember_old($_POST);
        flash('error', implode(' ', $data['errors']));
        redirect(admin_url('listings/new'));
    }

    $ref = next_listing_ref();
    $id  = db_insert('listings', $data['fields'] + [
        'ref'        => $ref,
        'slug'       => unique_listing_slug(
            input('slug', '') !== '' ? (string) input('slug') : $data['fields']['title']
        ),
        'created_at' => now(),
        'updated_at' => now(),
        'updated_by' => current_user()['id'],
    ]);

    save_features($id, (string) input('features', ''));
    $upload = save_uploaded_images($id);

    forget_old();
    log_activity('created', 'listing', $ref, 'Added ' . $data['fields']['title']);
    flash('success', $ref . ' created. It is a draft until you publish it.');

    if ($upload !== null) {
        flash('error', $upload);
    }

    redirect(admin_url('listings/' . $id));
}

function update(string $id): void
{
    require_login();
    require_can('listings.edit');

    $id      = (int) $id;
    $listing = db_one('SELECT * FROM listings WHERE id = ?', [$id]);

    if ($listing === null) {
        flash('error', 'That property no longer exists.');
        redirect(admin_url('listings'));
    }

    $data = collect_listing_input();

    if ($data['errors'] !== []) {
        remember_old($_POST);
        flash('error', implode(' ', $data['errors']));
        redirect(admin_url('listings/' . $id));
    }

    // The address is only regenerated when it is blank or the editor changed it.
    // Live URLs must not move because someone corrected a typo in the title.
    $typedSlug = (string) input('slug', '');
    if ($typedSlug === '') {
        $data['fields']['slug'] = $listing['slug'] ?: unique_listing_slug($data['fields']['title'], $id);
    } elseif ($typedSlug !== $listing['slug']) {
        $data['fields']['slug'] = unique_listing_slug($typedSlug, $id);
    }

    // Record only what actually changed, so the activity log stays readable.
    $changed = [];
    foreach ($data['fields'] as $key => $value) {
        if ((string) ($listing[$key] ?? '') !== (string) $value) {
            $changed[$key] = ['from' => $listing[$key] ?? null, 'to' => $value];
        }
    }

    db_update('listings', $id, $data['fields'] + [
        'updated_at' => now(),
        'updated_by' => current_user()['id'],
    ]);

    save_features($id, (string) input('features', ''));
    $upload = save_uploaded_images($id);

    forget_old();

    if ($changed !== []) {
        log_activity('updated', 'listing', $listing['ref'], 'Edited ' . $data['fields']['title'], $changed);
    }

    flash('success', 'Saved.');

    if ($upload !== null) {
        flash('error', $upload);
    }

    redirect(admin_url('listings/' . $id));
}

/* ------------------------------------------------------------------ state */

function set_state(string $id): void
{
    require_login();

    $id      = (int) $id;
    $listing = db_one('SELECT * FROM listings WHERE id = ?', [$id]);

    if ($listing === null) {
        flash('error', 'That property no longer exists.');
        redirect(admin_url('listings'));
    }

    $action = (string) input('action', '');

    switch ($action) {
        case 'publish':
            require_can('listings.publish');
            db_update('listings', $id, ['published_at' => now(), 'archived_at' => null, 'updated_at' => now()]);
            log_activity('published', 'listing', $listing['ref'], 'Published ' . $listing['title']);
            flash('success', $listing['ref'] . ' is now live on the site.');
            break;

        case 'unpublish':
            require_can('listings.publish');
            db_update('listings', $id, ['published_at' => null, 'updated_at' => now()]);
            log_activity('unpublished', 'listing', $listing['ref'], 'Unpublished ' . $listing['title']);
            flash('success', $listing['ref'] . ' has been taken off the site.');
            break;

        case 'archive':
            require_can('listings.archive');
            db_update('listings', $id, ['archived_at' => now(), 'published_at' => null, 'updated_at' => now()]);
            log_activity('archived', 'listing', $listing['ref'], 'Archived ' . $listing['title']);
            flash('success', $listing['ref'] . ' archived. Nothing is lost; you can restore it.');
            redirect(admin_url('listings'));

        case 'restore':
            require_can('listings.archive');
            db_update('listings', $id, ['archived_at' => null, 'updated_at' => now()]);
            log_activity('restored', 'listing', $listing['ref'], 'Restored ' . $listing['title']);
            flash('success', $listing['ref'] . ' restored as a draft.');
            break;

        case 'delete':
            require_can('listings.delete');
            db_run('DELETE FROM listings WHERE id = ?', [$id]);
            log_activity('deleted', 'listing', $listing['ref'], 'Deleted ' . $listing['title']);
            flash('success', $listing['ref'] . ' permanently deleted.');
            redirect(admin_url('listings'));

        default:
            flash('error', 'Unknown action.');
    }

    redirect(admin_url('listings/' . $id));
}

/* ----------------------------------------------------------------- images */

function set_cover(string $id, string $mediaId): void
{
    require_login();
    require_can('listings.edit');

    db_update('listings', (int) $id, ['cover_id' => (int) $mediaId, 'updated_at' => now()]);
    flash('success', 'Cover image set.');
    redirect(admin_url('listings/' . (int) $id));
}

function attach_images(string $id): void
{
    require_login();
    require_can('listings.edit');

    $id  = (int) $id;
    $ids = array_filter(array_map('intval', (array) ($_POST['media_ids'] ?? [])));

    if ($ids === []) {
        flash('error', 'Choose at least one image.');
        redirect(admin_url('listings/' . $id));
    }

    $sort  = (int) db_value('SELECT COALESCE(MAX(sort), -1) + 1 FROM listing_images WHERE listing_id = ?', [$id]);
    $added = 0;

    foreach ($ids as $mediaId) {
        if (db_one('SELECT id FROM media WHERE id = ?', [$mediaId]) === null) {
            continue;
        }

        // The unique key already prevents duplicates; check first so a repeat
        // selection is a no-op rather than an error.
        $already = db_one(
            'SELECT id FROM listing_images WHERE listing_id = ? AND media_id = ?',
            [$id, $mediaId]
        );

        if ($already !== null) {
            continue;
        }

        db_insert('listing_images', [
            'listing_id' => $id,
            'media_id'   => $mediaId,
            'sort'       => $sort++,
        ]);
        $added++;
    }

    if ($added > 0 && db_value('SELECT cover_id FROM listings WHERE id = ?', [$id]) === null) {
        db_update('listings', $id, ['cover_id' => reset($ids)]);
    }

    flash(
        $added > 0 ? 'success' : 'error',
        $added > 0
            ? $added . ' image' . ($added === 1 ? '' : 's') . ' added from the library.'
            : 'Those images are already on this property.'
    );

    redirect(admin_url('listings/' . $id));
}

function remove_image(string $id, string $mediaId): void
{
    require_login();
    require_can('listings.edit');

    $id      = (int) $id;
    $mediaId = (int) $mediaId;

    db_run('DELETE FROM listing_images WHERE listing_id = ? AND media_id = ?', [$id, $mediaId]);

    // Never leave a listing pointing at a cover it no longer holds.
    $listing = db_one('SELECT cover_id FROM listings WHERE id = ?', [$id]);
    if ($listing !== null && (int) $listing['cover_id'] === $mediaId) {
        $next = db_value(
            'SELECT media_id FROM listing_images WHERE listing_id = ? ORDER BY sort, id LIMIT 1',
            [$id]
        );
        db_update('listings', $id, ['cover_id' => $next !== null ? (int) $next : null]);
    }

    flash('success', 'Image removed from this property. It is still in the media library.');
    redirect(admin_url('listings/' . $id));
}

/* ---------------------------------------------------------------- helpers */

function all_locations(): array
{
    return db_all('SELECT * FROM locations ORDER BY city, name');
}

/**
 * Read and validate the form.
 *
 * @return array{fields: array<string, mixed>, errors: array<int, string>}
 */
function collect_listing_input(): array
{
    $errors = [];

    $title    = (string) input('title', '');
    $address  = (string) input('address', '');
    $basis    = (string) input('basis', '');
    $category = (string) input('category', '');
    $price    = (string) input('price', '');

    if ($title === '')   { $errors[] = 'Give the property a title.'; }
    if ($address === '') { $errors[] = 'Add an address.'; }
    if (!in_array($basis, LISTING_BASES, true)) { $errors[] = 'Choose whether it is for sale, to rent or a short stay.'; }
    if (!array_key_exists($category, LISTING_CATEGORIES)) { $errors[] = 'Choose a property type.'; }
    if ($price === '' || !is_numeric($price) || (float) $price < 0) { $errors[] = 'Enter a price as a number.'; }

    $locationId = input('location_id', '');
    $beds       = input('beds', '');
    $baths      = input('baths', '');
    $area       = input('area', '');

    return [
        'errors' => $errors,
        'fields' => [
            'basis'       => $basis,
            'category'    => $category,
            'title'       => mb_substr($title, 0, 190),
            'address'     => mb_substr($address, 0, 190),
            'location_id' => $locationId !== '' ? (int) $locationId : null,
            'price'       => $price === '' ? 0 : (float) $price,
            'currency'    => in_array(input('currency', 'USD'), ['USD', 'GHS', 'GBP', 'EUR'], true)
                ? (string) input('currency', 'USD') : 'USD',
            'period'      => input('period', '') ?: null,
            'status'      => mb_substr((string) input('status', ''), 0, 32),
            'beds'        => $beds !== '' ? (int) $beds : null,
            'baths'       => $baths !== '' ? (int) $baths : null,
            'area'        => $area !== '' ? (int) $area : null,
            'summary'     => (string) input('summary', ''),
            'description' => (string) input('description', ''),
            'verified'    => input('verified') !== null ? 1 : 0,
            'featured'    => input('featured') !== null ? 1 : 0,
        ],
    ];
}

/** Features arrive as one per line, which is far easier for staff than a repeater. */
function save_features(int $listingId, string $raw): void
{
    db_run('DELETE FROM listing_features WHERE listing_id = ?', [$listingId]);

    $lines = array_values(array_filter(
        array_map('trim', preg_split('/\r\n|\r|\n/', $raw) ?: []),
        static fn (string $line): bool => $line !== ''
    ));

    foreach (array_slice($lines, 0, 12) as $i => $label) {
        db_insert('listing_features', [
            'listing_id' => $listingId,
            'label'      => mb_substr($label, 0, 120),
            'sort'       => $i,
        ]);
    }
}

/** @return string|null an error or warning worth surfacing, or null */
function save_uploaded_images(int $listingId): ?string
{
    if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'])) {
        return null;
    }

    require_can('media.upload');

    $problems = [];
    $sort     = (int) db_value('SELECT COALESCE(MAX(sort), -1) + 1 FROM listing_images WHERE listing_id = ?', [$listingId]);

    foreach ($_FILES['images']['name'] as $i => $name) {
        if (($_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $file = [
            'name'     => $name,
            'type'     => $_FILES['images']['type'][$i],
            'tmp_name' => $_FILES['images']['tmp_name'][$i],
            'error'    => $_FILES['images']['error'][$i],
            'size'     => $_FILES['images']['size'][$i],
        ];

        $listing = db_one('SELECT title, address FROM listings WHERE id = ?', [$listingId]);
        $result  = store_upload($file, ($listing['title'] ?? '') . ', ' . ($listing['address'] ?? ''));

        if (!$result['ok']) {
            $problems[] = $name . ': ' . $result['error'];
            continue;
        }

        db_insert('listing_images', [
            'listing_id' => $listingId,
            'media_id'   => $result['id'],
            'sort'       => $sort++,
        ]);

        // First image uploaded becomes the cover if there is not one already.
        $current = db_value('SELECT cover_id FROM listings WHERE id = ?', [$listingId]);
        if ($current === null) {
            db_update('listings', $listingId, ['cover_id' => $result['id']]);
        }

        if (!empty($result['warning'])) {
            $problems[] = $result['warning'];
        }
    }

    return $problems === [] ? null : implode(' ', $problems);
}
