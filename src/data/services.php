<?php
declare(strict_types=1);

/**
 * The fifteen services listed under ABOUT > Our Services in the client outline.
 *
 * `title` is the client's own wording, verbatim. Where the outline appended a
 * description after a colon or in brackets, that tail becomes `note` so it can be
 * typeset underneath, but no word is changed.
 *
 * The six with a `body` render as detailed cards; `body` is our expansion and is the
 * only copy here that is not the client's. Every entry has a `slug`, so anything can
 * deep-link to its card at /about#service-<slug>.
 */

return [
    'primary' => [
        [
            'slug'  => 'diaspora',
            'icon'  => 'globe',
            'title' => 'Diaspora Property Investment Support',
            'body'  => 'End-to-end support for buying from abroad: sourcing, negotiation, '
                . 'payment structuring and handover, with you on video call throughout.',
        ],
        [
            'slug'  => 'sales',
            'icon'  => 'key',
            'title' => 'Property Sales and Purchases',
            'body'  => 'Houses, apartments, commercial space and land. Direct access to '
                . 'landlords and developers. We never work through middlemen.',
        ],
        [
            'slug'  => 'management',
            'icon'  => 'building',
            'title' => 'Residential and Commercial Property Management',
            'body'  => 'Tenant vetting, rent collection, maintenance, statements and '
                . 'facility management carried out on your behalf.',
        ],
        [
            'slug'  => 'supervision',
            'icon'  => 'hard-hat',
            'title' => 'Construction and Project Supervision',
            'body'  => 'Weekly site reports, photographic evidence and contractor oversight '
                . 'for clients who are unable to be physically present.',
        ],
        [
            'slug'  => 'due-diligence',
            'icon'  => 'file-check',
            'title' => 'Documentation and Due Diligence Support',
            'body'  => 'Title searches at the Lands Commission, indenture review, litigation '
                . 'checks and registration, all before a cedi changes hands.',
        ],
        [
            'slug'  => 'relocation',
            'icon'  => 'plane',
            'title' => 'Relocation Services',
            'note'  => 'Airport pickup, short term transportation and accommodation',
            'body'  => 'Airport pickup, short term transportation and accommodation, '
                . 'arranged before you land.',
        ],
    ],

    'secondary' => [
        ['slug' => 'holidays',      'icon' => 'compass',  'title' => 'Holiday packages',               'note' => 'Limited or unlimited cross-country site seeing'],
        ['slug' => 'rentals',       'icon' => 'users',    'title' => 'Property Rentals and Leasing',   'note' => 'Residential and commercial, furnished or unfurnished'],
        ['slug' => 'marketing',     'icon' => 'camera',   'title' => 'Property Marketing',             'note' => 'Photography, listings and virtual tours'],
        ['slug' => 'valuation',     'icon' => 'chart',    'title' => 'Property Valuation Coordination','note' => 'Independent valuers, instructed and managed by us'],
        ['slug' => 'land',          'icon' => 'landmark', 'title' => 'Land Acquisition Assistance',    'note' => 'Titled, litigation-free plots only'],
        ['slug' => 'facility',      'icon' => 'wrench',   'title' => 'Facility Management',            'note' => 'Servicing, repairs and vendor management'],
        ['slug' => 'accommodation', 'icon' => 'clock',    'title' => 'Temporary Accommodation',        'note' => 'Somewhere to stay while you view or move in'],
        ['slug' => 'legal',         'icon' => 'scale',    'title' => 'Legal assistance',               'note' => 'Conveyancing and contract review through our network'],
        ['slug' => 'interior',      'icon' => 'sofa',     'title' => 'Interior Design',                'note' => 'Furnishing and fit-out for homes and lettings'],
    ],
];
