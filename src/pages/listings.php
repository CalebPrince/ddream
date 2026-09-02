<?php
declare(strict_types=1);

/**
 * Shared results page for Selling and Rentals. Everything that differs between
 * the two comes from the route: the basis, the root path and which categories
 * apply. See src/routes.php.
 *
 * @var array $route
 */

$basis    = $route['basis'];
$root     = $route['root'];
$category = $route['category'] ?? null;
$basePath = $route['path'];

$criteria = [
    'basis'    => $basis,
    'category' => $category,
    'location' => $_GET['location'] ?? '',
    'min'      => $_GET['min']      ?? '',
    'max'      => $_GET['max']      ?? '',
    'beds'     => $_GET['beds']     ?? '',
    'sort'     => $_GET['sort']     ?? 'newest',
];

$results = search_listings($criteria);

// Category counts ignore the refine filters, so the numbers stay stable while browsing.
$countFor = static fn (?string $cat): int => count(search_listings([
    'basis'    => $basis,
    'category' => $cat,
]));

$labels = [
    'houses'     => ['label' => 'Houses',           'icon' => 'key'],
    'apartments' => ['label' => 'Apartments',       'icon' => 'building'],
    'commercial' => ['label' => 'Commercial space', 'icon' => 'landmark'],
    'land'       => ['label' => 'Land',             'icon' => 'compass'],
];

$categories = [[
    'label' => $route['all_label'],
    'icon'  => 'layers',
    'href'  => $root,
    'count' => $countFor(null),
]];
foreach ($route['categories'] as $slug) {
    $categories[] = [
        'label' => $labels[$slug]['label'],
        'icon'  => $labels[$slug]['icon'],
        'href'  => $root . '/' . $slug,
        'count' => $countFor($slug),
    ];
}

$crumbs = [['label' => $route['section'], 'href' => $category ? $root : null]];
if ($category) {
    $crumbs[] = ['label' => $labels[$category]['label']];
}

component('page-hero', [
    'crumbs'   => $crumbs,
    'eyebrow'  => $route['eyebrow'],
    'heading'  => $route['h1'],
    'lead'     => $route['lead'],
    'image'    => $route['image'] ?? '/images/properties/tower-residences.jpg',
    'imageAlt' => 'A DDREAM residential development at dusk in Accra',
    'facts'    => [
        ['label' => 'On our books',      'value' => (string) $countFor(null)],
        ['label' => match ($basis) {
            'To rent'    => 'Landlord checked',
            'Short stay' => 'Inspected by us',
            default      => 'Title checked',
        }, 'value' => '100%'],
        ['label' => 'Client commission', 'value' => 'None', 'accent' => 'text-signal-600'],
    ],
]);

section('listing-results', [
    'results'    => $results,
    'criteria'   => $criteria,
    'basePath'   => $basePath,
    'categories' => $categories,
    'priceBands' => $route['price_bands'],
    'basisLabel' => match ($basis) {
        'To rent'    => 'to rent',
        'Short stay' => 'available for short stays',
        default      => 'for sale',
    },
]);

section('cta');
