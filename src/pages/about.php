<?php
declare(strict_types=1);

component('page-hero', [
    'crumbs'   => [['label' => 'About']],
    'eyebrow'  => 'About DDREAM',
    'heading'  => 'A Ghanaian company<br class="hidden sm:block"> built for distance',
    'lead'     => 'Domestic, Diaspora Real Estate Management Ltd. exists to close the '
        . 'distance between property in Ghana and the people who want to own, build or let '
        . 'it from anywhere in the world.',
    'image'    => '/images/properties/tower-residences.jpg',
    'imageAlt' => 'A DDREAM residential tower at dusk in Cantonments, Accra',
    'facts'    => [
        ['label' => 'Client commission', 'value' => 'None',            'accent' => 'text-signal-600'],
        ['label' => 'Flat admin fee',    'value' => config('admin_fee')],
        ['label' => 'Services',          'value' => '15'],
    ],
]);

// Band order follows the ABOUT section of the client outline exactly:
// About us, Mission, Vision, Objectives, Core Values, Our Services, What Sets Us Apart.
section('about-intro');
section('about-mission');
section('about-objectives');
section('about-values');
section('about-services');
section('about-apart');
section('about-office');
section('cta');
