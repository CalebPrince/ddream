<?php
declare(strict_types=1);

/** The About page header. Wording lives in Page contents > About > Page header. */

component('page-hero', [
    'crumbs'   => [['label' => 'About']],
    'eyebrow'  => content('eyebrow'),
    'heading'  => content_html('heading'),
    'lead'     => content('lead'),
    'image'    => content('image'),
    'imageAlt' => content('image_alt'),
    'facts'    => content_items('facts'),
]);
