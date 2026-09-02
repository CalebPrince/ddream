<?php
declare(strict_types=1);

$contact = config('contact', []);

component('page-hero', [
    'crumbs'   => [['label' => 'Contact']],
    'eyebrow'  => 'Contact DDREAM',
    'heading'  => 'Talk to someone<br class="hidden sm:block"> who answers',
    'lead'     => 'A named adviser, a reply within one working day, and a call scheduled '
        . 'for your timezone rather than ours.',
    'image'    => '/images/front-desk.png',
    'imageAlt' => 'The DDREAM reception at the Airport Residential office in Accra',
    'facts'    => [
        ['label' => 'We reply within', 'value' => '1 day'],
        ['label' => 'Client commission', 'value' => 'None', 'accent' => 'text-signal-600'],
        ['label' => 'Flat admin fee',  'value' => config('admin_fee')],
    ],
]);

section('contact-details');
section('contact-form');
