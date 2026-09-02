<?php
declare(strict_types=1);

/**
 * Pattern routes, tried after the exact matches in src/routes.php.
 *
 * `{name}` placeholders arrive in the route as `params`. A `resolve` callback may
 * return null to reject the match, which drops the request through to the 404.
 */

return [
    '/property/{slug}' => [
        'page'    => 'property',
        'resolve' => static function (array $route): ?array {
            require_once __DIR__ . '/repositories/listings.php';

            $segment = $route['params']['slug'];
            $listing = find_listing_by_slug($segment);

            // A reference such as dd-1042 still works, but sends the visitor on
            // to the readable address so only one URL is ever indexed.
            if ($listing === null && preg_match('/^dd-\d+$/i', $segment)) {
                $byRef = find_listing_by_ref($segment);

                if ($byRef !== null) {
                    header('Location: /property/' . $byRef['slug'], true, 301);
                    exit;
                }
            }

            if ($listing === null) {
                return null;
            }

            $route['listing'] = $listing;
            $route['nav']     = match ($listing['basis']) {
                'To rent'    => '/rentals',
                'Short stay' => '/airbnb',
                default      => '/selling',
            };
            $route['title'] = $listing['title'] . ', ' . $listing['address'];
            $route['image'] = $listing['image'];
            $route['desc']  = $listing['summary'];

            return $route;
        },
    ],

    '/blog/{slug}' => [
        'page'    => 'post',
        'nav'     => '/blog',
        'resolve' => static function (array $route): ?array {
            $slug = $route['params']['slug'];

            $post = current(array_filter(
                data_set('posts'),
                static fn (array $item): bool => $item['slug'] === $slug
            ));

            if (!$post) {
                return null;
            }

            $route['post']  = $post;
            $route['title'] = $post['title'];
            $route['image'] = $post['image'];
            $route['desc']  = $post['excerpt'];

            return $route;
        },
    ],
];
