<?php
declare(strict_types=1);

/**
 * The Careers page header. Wording lives in Page contents > Careers.
 *
 * @var int $openRoles how many vacancies are advertised, for the {count} token
 */

$openRoles ??= 0;

component('page-hero', [
    'crumbs'   => [['label' => 'Careers']],
    'eyebrow'  => content('eyebrow'),
    'heading'  => content_html('heading'),
    'lead'     => content('lead'),
    'image'    => content('image'),
    'imageAlt' => content('image_alt'),
    'facts'    => content_items('facts', ['{count}' => (string) $openRoles]),
]);
