<?php
declare(strict_types=1);

/**
 * Home page hero slideshow.
 *
 * Source: Grace City Prime Homes gallery, royalkingdomestate.com/apartment/gc-prime-homes
 * (referenced in the client's website outline). Images live in
 * public/images/slideshow/gc-prime-01.jpg … gc-prime-34.jpg.
 *
 * NOTE FOR THE CLIENT: these renders are the property of Royal Kingdom Estate.
 * Confirm written permission or a listing agreement before going live.
 */

$development = [
    'name'     => 'Grace City Prime Homes',
    'location' => 'Aburi Amanfrom, Eastern Region',
    'price'    => 99000,
    'currency' => 'USD',
    'blurb'    => 'An eco-friendly gated community in the Aburi hills, with 2-bed semi-detached '
        . 'units through to 4-bed residences, each with an extra plot of farmland.',
];

/** Curated hero rotation: exterior → aerial → interior → lifestyle. */
$slides = [
    ['file' => 'gc-prime-01.jpg', 'caption' => '4-bedroom detached residence',        'kicker' => 'Homes from $99,000'],
    ['file' => 'gc-prime-11.jpg', 'caption' => 'The gated community masterplan',      'kicker' => '24/7 secured entrance'],
    ['file' => 'gc-prime-23.jpg', 'caption' => 'Open-plan living and dining',         'kicker' => 'Fully serviced interiors'],
    ['file' => 'gc-prime-29.jpg', 'caption' => 'Life inside the community',           'kicker' => 'Aburi hills, Eastern Region'],
    ['file' => 'gc-prime-15.jpg', 'caption' => '3-bedroom storey homes',              'kicker' => 'Semi-detached and detached'],
    ['file' => 'gc-prime-12.jpg', 'caption' => 'Landscaped avenues and green belts',  'kicker' => 'Parks and jogging trails'],
    ['file' => 'gc-prime-25.jpg', 'caption' => 'Principal bedroom suite',             'kicker' => 'Turn-key finishes'],
    ['file' => 'gc-prime-05.jpg', 'caption' => 'Private pool residence',              'kicker' => 'An extra plot for farming'],
    ['file' => 'gc-prime-31.jpg', 'caption' => 'Community gardens',                   'kicker' => 'Rooted in wellness and culture'],
    ['file' => 'gc-prime-34.jpg', 'caption' => 'Aerial view of the estate',           'kicker' => 'Phase one now selling'],
];

return array_map(
    static fn (array $slide, int $index): array => $slide + [
        'src'         => '/images/slideshow/' . $slide['file'],
        'index'       => $index,
        'development' => $development,
    ],
    $slides,
    array_keys($slides)
);
