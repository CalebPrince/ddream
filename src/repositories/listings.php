<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/db.php';

/**
 * Listings repository.
 *
 * Rows come back shaped exactly like the arrays the front end templates already
 * expect, so swapping data_set('listings') for these functions changed no
 * template. `added` stays a day count, `tags` an array, `image` a path.
 */

if (!function_exists('listing_row_to_array')) {
    /** Turn a database row into the shape the templates use. */
    function listing_row_to_array(array $row, array $features = [], ?string $cover = null): array
    {
        return [
            'id'       => $row['ref'],
            'db_id'    => (int) $row['id'],
            // The public URL segment. Falls back to the reference for any row
            // that predates slugs, so a link is never broken.
            'slug'     => $row['slug'] ?: strtolower($row['ref']),
            'category' => $row['category'],
            'title'    => $row['title'],
            'address'  => $row['address'],
            'price'    => (float) $row['price'],
            'currency' => $row['currency'],
            'basis'    => $row['basis'],
            'period'   => $row['period'] ?: null,
            'status'   => $row['status'],
            'beds'     => $row['beds'] !== null ? (int) $row['beds'] : null,
            'baths'    => $row['baths'] !== null ? (int) $row['baths'] : null,
            'area'     => (int) $row['area'],
            'image'    => $cover ?? '/images/properties/tower-residences.jpg',
            'summary'  => (string) $row['summary'],
            'tags'     => $features,
            'verified' => (bool) $row['verified'],
            'featured' => (bool) $row['featured'],
            'added'    => $row['published_at'] !== null
                ? max(0, (int) floor((time() - strtotime((string) $row['published_at'])) / 86400))
                : 0,
        ];
    }
}

if (!function_exists('hydrate_listings')) {
    /**
     * Attach features and cover images to a set of rows in two extra queries
     * rather than two per listing.
     */
    function hydrate_listings(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $ids          = array_column($rows, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $features = [];
        foreach (db_all(
            "SELECT listing_id, label FROM listing_features
             WHERE listing_id IN ($placeholders) ORDER BY sort, id",
            $ids
        ) as $feature) {
            $features[(int) $feature['listing_id']][] = $feature['label'];
        }

        // The cover image, falling back to the first attached image.
        $covers = [];
        foreach (db_all(
            "SELECT li.listing_id, m.path
             FROM listing_images li
             JOIN media m ON m.id = li.media_id
             WHERE li.listing_id IN ($placeholders)
             ORDER BY li.listing_id, li.sort, li.id",
            $ids
        ) as $image) {
            $covers[(int) $image['listing_id']] ??= $image['path'];
        }

        foreach ($rows as $row) {
            $id = (int) $row['id'];
            // An explicit cover wins over the first attachment.
            $cover = $row['cover_path'] ?? $covers[$id] ?? null;
            $out[] = listing_row_to_array($row, $features[$id] ?? [], $cover);
        }

        return $out ?? [];
    }
}

if (!function_exists('find_listings')) {
    /**
     * Public-facing search. Only published, unarchived listings.
     *
     * @param array $criteria basis, category, min, max, beds, location, sort, limit
     */
    function find_listings(array $criteria = []): array
    {
        $where  = ['l.published_at IS NOT NULL', 'l.archived_at IS NULL'];
        $params = [];

        if (!empty($criteria['basis'])) {
            $where[]  = 'l.basis = ?';
            $params[] = $criteria['basis'];
        }
        if (!empty($criteria['category'])) {
            $where[]  = 'l.category = ?';
            $params[] = $criteria['category'];
        }
        if (!empty($criteria['featured'])) {
            $where[] = 'l.featured = 1';
        }
        if (isset($criteria['min']) && $criteria['min'] !== '') {
            $where[]  = 'l.price >= ?';
            $params[] = (float) $criteria['min'];
        }
        if (isset($criteria['max']) && $criteria['max'] !== '') {
            $where[]  = 'l.price <= ?';
            $params[] = (float) $criteria['max'];
        }
        if (isset($criteria['beds']) && $criteria['beds'] !== '') {
            $where[]  = 'l.beds >= ?';
            $params[] = (int) $criteria['beds'];
        }
        if (!empty($criteria['location'])) {
            $where[]  = '(l.address LIKE ? OR l.title LIKE ?)';
            $term     = '%' . $criteria['location'] . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $order = match ($criteria['sort'] ?? 'newest') {
            'price-asc'  => 'l.price ASC',
            'price-desc' => 'l.price DESC',
            'beds'       => 'l.beds DESC',
            default      => 'l.published_at DESC',
        };

        $limit = isset($criteria['limit']) ? ' LIMIT ' . (int) $criteria['limit'] : '';

        $rows = db_all(
            'SELECT l.*, m.path AS cover_path
             FROM listings l
             LEFT JOIN media m ON m.id = l.cover_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY ' . $order . $limit,
            $params
        );

        return hydrate_listings($rows);
    }
}

if (!function_exists('find_listing_by_ref')) {
    function find_listing_by_ref(string $ref): ?array
    {
        $row = db_one(
            'SELECT l.*, m.path AS cover_path
             FROM listings l
             LEFT JOIN media m ON m.id = l.cover_id
             WHERE UPPER(l.ref) = UPPER(?) AND l.published_at IS NOT NULL AND l.archived_at IS NULL',
            [$ref]
        );

        if ($row === null) {
            return null;
        }

        return hydrate_listings([$row])[0] ?? null;
    }
}

if (!function_exists('find_listing_by_slug')) {
    function find_listing_by_slug(string $slug): ?array
    {
        $row = db_one(
            'SELECT l.*, m.path AS cover_path
             FROM listings l
             LEFT JOIN media m ON m.id = l.cover_id
             WHERE l.slug = ? AND l.published_at IS NOT NULL AND l.archived_at IS NULL',
            [$slug]
        );

        if ($row === null) {
            return null;
        }

        return hydrate_listings([$row])[0] ?? null;
    }
}

if (!function_exists('unique_listing_slug')) {
    /**
     * A slug that no other listing is using. Collisions get a numeric suffix
     * rather than the reference, which would defeat the point of a readable URL.
     */
    function unique_listing_slug(string $title, ?int $excludeId = null): string
    {
        $base = slugify($title);
        $slug = $base;
        $n    = 1;

        while (true) {
            $existing = db_one(
                'SELECT id FROM listings WHERE slug = ?' . ($excludeId ? ' AND id <> ?' : ''),
                $excludeId ? [$slug, $excludeId] : [$slug]
            );

            if ($existing === null) {
                return $slug;
            }

            $slug = $base . '-' . (++$n);
        }
    }
}

if (!function_exists('listing_gallery')) {
    /** @return array<int, string> image paths in display order */
    function listing_gallery(int $listingId): array
    {
        return array_column(
            db_all(
                'SELECT m.path FROM listing_images li
                 JOIN media m ON m.id = li.media_id
                 WHERE li.listing_id = ? ORDER BY li.sort, li.id',
                [$listingId]
            ),
            'path'
        );
    }
}

if (!function_exists('count_listings')) {
    function count_listings(array $criteria = []): int
    {
        return count(find_listings($criteria));
    }
}

if (!function_exists('next_listing_ref')) {
    /** DD-1042 style reference, continuing from the highest already used. */
    function next_listing_ref(): string
    {
        $highest = (int) db_value(
            "SELECT MAX(CAST(SUBSTRING(ref, 4) AS UNSIGNED)) FROM listings WHERE ref LIKE 'DD-%'"
        );

        return 'DD-' . max($highest + 1, 1001);
    }
}
