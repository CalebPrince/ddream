<?php
declare(strict_types=1);

/** The Virtual tours page header. Wording lives in Page contents > Virtual tours. */

component('page-hero', [
    'crumbs'   => [['label' => 'Virtual tours']],
    'eyebrow'  => content('eyebrow'),
    'heading'  => content_html('heading'),
    'lead'     => content('lead'),
    'image'    => content('image'),
    'imageAlt' => content('image_alt'),
    'facts'    => content_items('facts'),
]);
