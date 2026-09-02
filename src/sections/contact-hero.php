<?php
declare(strict_types=1);

/** The Contact page header. Wording lives in Page contents > Contact. */

component('page-hero', [
    'crumbs'   => [['label' => 'Contact']],
    'eyebrow'  => content('eyebrow'),
    'heading'  => content_html('heading'),
    'lead'     => content('lead'),
    'image'    => content('image'),
    'imageAlt' => content('image_alt'),
    'facts'    => content_items('facts'),
]);
