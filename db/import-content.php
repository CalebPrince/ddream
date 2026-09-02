<?php
declare(strict_types=1);

/**
 * Move the flat content files into the database. CLI only.
 *
 *   php db/import-content.php
 *
 * Idempotent: rows are matched on their natural key (listing ref, location
 * slug, media path) and updated rather than duplicated, so it is safe to run
 * again after editing a source file.
 *
 * Images already in public/images are registered in the media table by path.
 * Nothing is copied or moved, so the front end keeps working throughout.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only.\n");
}

require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/helpers.php';

$root = dirname(__DIR__);
$time = now();

/** Register an existing file under public/ in the media table, returning its id. */
function register_media(string $path, string $alt = ''): ?int
{
    static $cache = [];

    if (isset($cache[$path])) {
        return $cache[$path];
    }

    $existing = db_one('SELECT id FROM media WHERE path = ?', [$path]);
    if ($existing !== null) {
        return $cache[$path] = (int) $existing['id'];
    }

    $absolute = dirname(__DIR__) . '/public' . $path;
    if (!is_file($absolute)) {
        fwrite(STDERR, "  missing image: {$path}\n");

        return null;
    }

    $probe = @getimagesize($absolute);

    return $cache[$path] = db_insert('media', [
        'path'       => $path,
        'alt'        => mb_substr($alt, 0, 255),
        'mime'       => $probe['mime'] ?? null,
        'width'      => $probe[0] ?? null,
        'height'     => $probe[1] ?? null,
        'bytes'      => (int) filesize($absolute),
        'created_at' => now(),
    ]);
}

// ------------------------------------------------------------------ locations

$areas = require $root . '/src/data/areas.php';
$locationIds = [];
$sort = 0;

foreach ($areas['featured'] as $area) {
    $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $area['name']));
    $slug = trim($slug, '-');

    $existing = db_one('SELECT id FROM locations WHERE slug = ?', [$slug]);

    $data = [
        'name'       => $area['name'],
        'city'       => $area['city'],
        'slug'       => $slug,
        'featured'   => 1,
        'sort'       => $sort++,
        'updated_at' => $time,
    ];

    if ($existing) {
        db_update('locations', (int) $existing['id'], $data);
        $locationIds[$area['name']] = (int) $existing['id'];
    } else {
        $locationIds[$area['name']] = db_insert('locations', $data + ['created_at' => $time]);
    }
}
echo 'Locations: ' . count($locationIds) . "\n";

// ------------------------------------------------------------------- listings

$listings = require $root . '/src/data/listings.php';
$imported = 0;

foreach ($listings as $item) {
    $mediaId = register_media($item['image'], $item['title'] . ', ' . $item['address']);

    // `added` is a day count in the source file; turn it back into a date.
    $publishedAt = (new DateTimeImmutable('-' . (int) $item['added'] . ' days'))->format('Y-m-d H:i:s');

    // Match the address to a known location where we can.
    $locationId = null;
    foreach ($locationIds as $name => $id) {
        if (stripos($item['address'], (string) $name) !== false) {
            $locationId = $id;
            break;
        }
    }

    $data = [
        'basis'        => $item['basis'],
        'category'     => $item['category'],
        'title'        => $item['title'],
        'address'      => $item['address'],
        'location_id'  => $locationId,
        'price'        => $item['price'],
        'currency'     => $item['currency'],
        'period'       => $item['period'] ?? null,
        'status'       => $item['status'],
        'beds'         => $item['beds'] ?? null,
        'baths'        => $item['baths'] ?? null,
        'area'         => $item['area'] ?? null,
        'summary'      => $item['summary'],
        'cover_id'     => $mediaId,
        'verified'     => !empty($item['verified']) ? 1 : 0,
        'featured'     => !empty($item['featured']) ? 1 : 0,
        'published_at' => $publishedAt,
        'updated_at'   => $time,
    ];

    $existing = db_one('SELECT id FROM listings WHERE ref = ?', [$item['id']]);

    if ($existing) {
        $listingId = (int) $existing['id'];
        db_update('listings', $listingId, $data);
    } else {
        $listingId = db_insert('listings', $data + ['ref' => $item['id'], 'created_at' => $time]);
    }

    // Features and images are rebuilt rather than merged, so a re-run matches
    // the source file exactly.
    db_run('DELETE FROM listing_features WHERE listing_id = ?', [$listingId]);
    foreach (array_values($item['tags']) as $i => $label) {
        db_insert('listing_features', [
            'listing_id' => $listingId,
            'label'      => $label,
            'sort'       => $i,
        ]);
    }

    if ($mediaId !== null) {
        db_run('DELETE FROM listing_images WHERE listing_id = ?', [$listingId]);
        db_insert('listing_images', [
            'listing_id' => $listingId,
            'media_id'   => $mediaId,
            'sort'       => 0,
        ]);
    }

    $imported++;
}
echo "Listings: {$imported}\n";

// ------------------------------------------------------------------- services

$services = require $root . '/src/data/services.php';
$sort = 0;
$count = 0;

foreach (['primary' => 1, 'secondary' => 0] as $group => $featured) {
    foreach ($services[$group] as $service) {
        $data = [
            'title'      => $service['title'],
            'note'       => $service['note'] ?? null,
            'body'       => $service['body'] ?? null,
            'icon'       => $service['icon'],
            'featured'   => $featured,
            'sort'       => $sort++,
            'updated_at' => $time,
        ];

        $existing = db_one('SELECT id FROM services WHERE slug = ?', [$service['slug']]);
        $existing
            ? db_update('services', (int) $existing['id'], $data)
            : db_insert('services', $data + ['slug' => $service['slug'], 'created_at' => $time]);
        $count++;
    }
}
echo "Services: {$count}\n";

// ---------------------------------------------------------------------- posts

$posts = require $root . '/src/data/posts.php';
$categoryIds = [];
$count = 0;

foreach ($posts as $post) {
    $categoryName = $post['category'];

    if (!isset($categoryIds[$categoryName])) {
        $slug = trim(strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $categoryName)), '-');
        $existing = db_one('SELECT id FROM post_categories WHERE slug = ?', [$slug]);
        $categoryIds[$categoryName] = $existing
            ? (int) $existing['id']
            : db_insert('post_categories', [
                'name' => $categoryName,
                'slug' => $slug,
                'sort' => count($categoryIds),
            ]);
    }

    $coverId = register_media($post['image'], $post['title']);

    $data = [
        'category_id'  => $categoryIds[$categoryName],
        'title'        => $post['title'],
        'excerpt'      => $post['excerpt'],
        'body'         => json_encode($post['body'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        'cover_id'     => $coverId,
        'read_minutes' => (int) filter_var($post['read'], FILTER_SANITIZE_NUMBER_INT),
        'featured'     => !empty($post['featured']) ? 1 : 0,
        'published_at' => $post['date'] . ' 09:00:00',
        'updated_at'   => $time,
    ];

    $existing = db_one('SELECT id FROM posts WHERE slug = ?', [$post['slug']]);
    $existing
        ? db_update('posts', (int) $existing['id'], $data)
        : db_insert('posts', $data + ['slug' => $post['slug'], 'created_at' => $time]);
    $count++;
}
echo "Posts: {$count} in " . count($categoryIds) . " categories\n";

// ------------------------------------------------------------------ vacancies

$vacancies = require $root . '/src/data/careers.php';
$sort = 0;

foreach ($vacancies as $role) {
    $existing = db_one('SELECT id FROM vacancies WHERE title = ?', [$role['title']]);

    $data = [
        'location'     => $role['location'],
        'type'         => $role['type'],
        'team'         => $role['team'],
        'summary'      => $role['summary'],
        'requirements' => json_encode($role['wants'], JSON_UNESCAPED_SLASHES),
        'sort'         => $sort++,
        'published_at' => $time,
        'updated_at'   => $time,
    ];

    $existing
        ? db_update('vacancies', (int) $existing['id'], $data)
        : db_insert('vacancies', $data + ['title' => $role['title'], 'created_at' => $time]);
}
echo 'Vacancies: ' . count($vacancies) . "\n";

echo "\nDone. The database now mirrors src/data.\n";
