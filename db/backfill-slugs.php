<?php
declare(strict_types=1);

/**
 * Give every listing a slug built from its title. CLI only.
 * Safe to re-run: rows that already have one are left alone.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only.\n");
}

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/repositories/listings.php';

$rows = db_all("SELECT id, ref, title, slug FROM listings WHERE slug IS NULL OR slug = ''");

foreach ($rows as $row) {
    $slug = unique_listing_slug((string) $row['title'], (int) $row['id']);
    db_update('listings', (int) $row['id'], ['slug' => $slug]);
    printf("  %-10s %s\n", $row['ref'], $slug);
}

echo count($rows) === 0
    ? "Every listing already has an address.\n"
    : 'Set ' . count($rows) . " addresses.\n";
