<?php
declare(strict_types=1);

/**
 * DDREAM global site configuration.
 * Everything the templates need that isn't a listing lives here.
 */

return [
    'name'      => 'DDREAM',
    'url'       => 'https://diasporadomesticrem.com',
    'legal'     => 'Domestic, Diaspora Real Estate Management Ltd.',
    'tagline'   => 'No Client Commission',
    'strapline' => 'Our building blocks are culturally curated. One client at a time.',

    'description' => 'DDREAM is a Ghanaian real estate management company connecting '
        . 'domestic and diaspora clients with verified property to buy, rent, build and '
        . 'manage, on a no client commission basis.',

    'contact' => [
        'phone'      => '+233 (0) 30 000 0000',
        'phone_href' => '+2333000000000',
        'whatsapp'   => '+233 (0) 55 000 0000',
        'email'      => 'info@diasporadomesticrem.com',
        'address'    => 'Airport Residential Area, Accra, Ghana',
        'hours'      => 'Mon–Fri 08:30–17:30 GMT · Sat 09:00–13:00',
    ],

    'admin_fee'    => 'GHS 1,000',
    'founded_note' => 'Registered with the Office of the Registrar of Companies, Ghana',

    /** Diaspora corridors we actively serve. Used in the hero rail and footer. */
    'corridors' => [
        'London', 'Amsterdam', 'Berlin', 'New York', 'Toronto', 'Johannesburg', 'Accra', 'Kumasi',
    ],

    'social' => [
        ['label' => 'Facebook',  'icon' => 'facebook',  'href' => '#'],
        ['label' => 'Instagram', 'icon' => 'instagram', 'href' => '#'],
        ['label' => 'LinkedIn',  'icon' => 'linkedin',  'href' => '#'],
        ['label' => 'YouTube',   'icon' => 'youtube',   'href' => '#'],
        ['label' => 'WhatsApp',  'icon' => 'whatsapp',  'href' => '#'],
    ],

    /**
     * Primary navigation. Mirrors the client outline:
     * HOME · ABOUT · SELLING · RENTALS · AIRBNB · VIRTUAL TOURS · BLOGS · CAREERS · CONTACT
     */
    'nav' => [
        ['label' => 'Home',    'href' => '/'],
        ['label' => 'About',   'href' => '/about', 'children' => [
            ['label' => 'About us',        'href' => '/about',            'note' => 'Who we are and who we serve'],
            ['label' => 'Our Objectives',  'href' => '/about#objectives', 'note' => 'The nine we measure ourselves on'],
            ['label' => 'What Sets Us Apart?', 'href' => '/about#apart',  'note' => 'The No Client Commission promise'],
            ['label' => 'Our Services',    'href' => '/about#services',   'note' => 'All fifteen, end to end'],
        ]],
        ['label' => 'Selling', 'href' => '/selling', 'children' => [
            ['label' => 'Houses',           'href' => '/selling/houses',           'note' => 'Detached, semi and townhouses'],
            ['label' => 'Apartments',       'href' => '/selling/apartments',       'note' => 'Studios to penthouses'],
            ['label' => 'Commercial space', 'href' => '/selling/commercial',       'note' => 'Office, retail and warehousing'],
            ['label' => 'Land',             'href' => '/selling/land',             'note' => 'Titled and litigation-free plots'],
        ]],
        ['label' => 'Rentals', 'href' => '/rentals', 'children' => [
            ['label' => 'Houses',           'href' => '/rentals/houses',     'note' => 'Family homes, 1–2 year leases'],
            ['label' => 'Apartments',       'href' => '/rentals/apartments', 'note' => 'Furnished and unfurnished'],
            ['label' => 'Commercial space', 'href' => '/rentals/commercial', 'note' => 'Serviced offices and shops'],
        ]],
        ['label' => 'Airbnb',  'href' => '/airbnb', 'children' => [
            ['label' => 'Houses',     'href' => '/airbnb/houses',     'note' => 'Whole-home short stays'],
            ['label' => 'Apartments', 'href' => '/airbnb/apartments', 'note' => 'Nightly and monthly rates'],
        ]],
        ['label' => 'Virtual tours', 'href' => '/virtual-tours'],
        ['label' => 'Blogs',         'href' => '/blog'],
        ['label' => 'Careers',       'href' => '/careers'],
        ['label' => 'Contact',       'href' => '/contact'],
    ],

    'footer_columns' => [
        'Buy' => [
            ['label' => 'Houses for sale',      'href' => '/selling/houses'],
            ['label' => 'Apartments for sale',  'href' => '/selling/apartments'],
            ['label' => 'Commercial space',     'href' => '/selling/commercial'],
            ['label' => 'Land and plots',       'href' => '/selling/land'],
            ['label' => 'New developments',     'href' => '/selling/new-developments'],
        ],
        'Rent & stay' => [
            ['label' => 'Houses to rent',       'href' => '/rentals/houses'],
            ['label' => 'Apartments to rent',   'href' => '/rentals/apartments'],
            ['label' => 'Commercial to let',    'href' => '/rentals/commercial'],
            ['label' => 'Airbnb short stays',   'href' => '/airbnb'],
            ['label' => 'Temporary accommodation', 'href' => '/about#service-accommodation'],
        ],
        'Diaspora services' => [
            ['label' => 'Investment support',   'href' => '/about#service-diaspora'],
            ['label' => 'Project supervision',  'href' => '/about#service-supervision'],
            ['label' => 'Due diligence',        'href' => '/about#service-due-diligence'],
            ['label' => 'Relocation and pickup','href' => '/about#service-relocation'],
            ['label' => 'Holiday packages',     'href' => '/about#service-holidays'],
        ],
        'Company' => [
            ['label' => 'About DDREAM',         'href' => '/about'],
            ['label' => 'Property management',  'href' => '/about#service-management'],
            ['label' => 'Virtual tours',        'href' => '/virtual-tours'],
            ['label' => 'Blogs and guides',     'href' => '/blog'],
            ['label' => 'Careers',              'href' => '/careers'],
            ['label' => 'Contact us',           'href' => '/contact'],
        ],
    ],
];
