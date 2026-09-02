<?php
declare(strict_types=1);

/**
 * URL path to page template.
 *
 * `page`   template in src/pages/
 * `nav`    which primary nav item shows as current (defaults to the path)
 * `title`  <title>, with the site name appended by the layout
 * `desc`   meta description, falls back to the site description
 */

return [
    '/' => [
        'page'  => 'home',
        'title' => 'Real estate for Ghana and the diaspora',
        'desc'  => null,
        'bare'  => true, // home sets its own <title> without the site-name suffix
    ],

    '/about' => [
        'page'  => 'about',
        'title' => 'About us',
        'desc'  => 'A Ghanaian real estate solutions company bridging property owners, '
            . 'investors and home seekers in Ghana with Ghanaians across the diaspora. '
            . 'Our mission, vision, objectives and the no client commission promise.',
    ],


    '/selling' => [
        'page'        => 'listings',
        'section'     => 'Selling',
        'basis'       => 'For sale',
        'root'        => '/selling',
        'path'        => '/selling',
        'all_label'   => 'All property',
        'categories'  => ['houses', 'apartments', 'commercial', 'land'],
        'price_bands' => ['min' => [25000, 50000, 100000, 200000, 300000], 'max' => [100000, 200000, 300000, 500000, 1000000]],
        'eyebrow'     => 'Properties for sale',
        
        
        'title'       => 'Property for sale in Ghana',
        'h1'          => 'Property for sale<br class="hidden sm:block"> across Ghana',
        'lead'        => 'Houses, apartments, commercial space and land. Every listing is inspected by our team and title-checked at the Lands Commission before it appears here.',
        'image'       => '/images/properties/tower-residences.jpg',
        'desc'        => 'Houses, apartments, commercial space and land for sale in Accra, Kumasi, Tema and the Eastern Region. Title-checked listings and no client commission.',
    ],

    '/selling/houses' => [
        'page'        => 'listings',
        'section'     => 'Selling',
        'basis'       => 'For sale',
        'root'        => '/selling',
        'path'        => '/selling/houses',
        'all_label'   => 'All property',
        'categories'  => ['houses', 'apartments', 'commercial', 'land'],
        'price_bands' => ['min' => [25000, 50000, 100000, 200000, 300000], 'max' => [100000, 200000, 300000, 500000, 1000000]],
        'eyebrow'     => 'Properties for sale',
        'nav'      => '/selling',
        'category' => 'houses',
        'title'       => 'Houses for sale in Ghana',
        'h1'          => 'Houses for sale',
        'lead'        => 'Detached, semi-detached and townhouses, from entry-level storey homes to hillside villas.',
        'image'       => '/images/properties/townhouse-terrace.jpg',
        'desc'        => 'Detached, semi-detached and townhouses for sale in Ghana, all title-checked, with no client commission.',
    ],

    '/selling/apartments' => [
        'page'        => 'listings',
        'section'     => 'Selling',
        'basis'       => 'For sale',
        'root'        => '/selling',
        'path'        => '/selling/apartments',
        'all_label'   => 'All property',
        'categories'  => ['houses', 'apartments', 'commercial', 'land'],
        'price_bands' => ['min' => [25000, 50000, 100000, 200000, 300000], 'max' => [100000, 200000, 300000, 500000, 1000000]],
        'eyebrow'     => 'Properties for sale',
        'nav'      => '/selling',
        'category' => 'apartments',
        'title'       => 'Apartments for sale in Ghana',
        'h1'          => 'Apartments for sale',
        'lead'        => 'Studios through to penthouses in the prime residential belts of Accra, serviced and unserviced.',
        'image'       => '/images/properties/tower-residences.jpg',
        'desc'        => 'Apartments for sale in Cantonments, East Legon, Airport Residential and across Accra.',
    ],

    '/selling/commercial' => [
        'page'        => 'listings',
        'section'     => 'Selling',
        'basis'       => 'For sale',
        'root'        => '/selling',
        'path'        => '/selling/commercial',
        'all_label'   => 'All property',
        'categories'  => ['houses', 'apartments', 'commercial', 'land'],
        'price_bands' => ['min' => [25000, 50000, 100000, 200000, 300000], 'max' => [100000, 200000, 300000, 500000, 1000000]],
        'eyebrow'     => 'Properties for sale',
        'nav'      => '/selling',
        'category' => 'commercial',
        'title'       => 'Commercial space for sale in Ghana',
        'h1'          => 'Commercial space<br class="hidden sm:block"> for sale',
        'lead'        => 'Office suites, retail units and warehousing, with the lease and title position established up front.',
        'image'       => '/images/properties/the-pelican.jpg',
        'desc'        => 'Office, retail and warehouse space for sale in Accra and across Ghana, with full due diligence.',
    ],

    '/selling/land' => [
        'page'        => 'listings',
        'section'     => 'Selling',
        'basis'       => 'For sale',
        'root'        => '/selling',
        'path'        => '/selling/land',
        'all_label'   => 'All property',
        'categories'  => ['houses', 'apartments', 'commercial', 'land'],
        'price_bands' => ['min' => [25000, 50000, 100000, 200000, 300000], 'max' => [100000, 200000, 300000, 500000, 1000000]],
        'eyebrow'     => 'Properties for sale',
        'nav'      => '/selling',
        'category' => 'land',
        'title'       => 'Land for sale in Ghana',
        'h1'          => 'Land and plots for sale',
        'lead'        => 'Titled, litigation-free plots only. Every parcel comes with a completed Lands Commission search before we will list it.',
        'image'       => '/images/slideshow/gc-prime-34.jpg',
        'desc'        => 'Titled, litigation-free land and serviced plots for sale in Ghana, with a completed Lands Commission search on every parcel.',
    ],

    '/rentals' => [
        'page'        => 'listings',
        'section'     => 'Rentals',
        'basis'       => 'To rent',
        'root'        => '/rentals',
        'path'        => '/rentals',
        'all_label'   => 'All rentals',
        'categories'  => ['houses', 'apartments', 'commercial'],
        'price_bands' => ['min' => [500, 1000, 1500, 2500, 4000], 'max' => [1500, 2500, 4000, 6000, 10000]],
        'eyebrow'     => 'Properties to rent',
        
        
        'title'       => 'Property to rent in Ghana',
        'h1'          => 'Property to rent<br class="hidden sm:block"> across Ghana',
        'lead'        => 'Houses, apartments and commercial space to let. Furnished or unfurnished, short lease or long, all managed by the same team that lets them.',
        'image'       => '/images/properties/the-pelican.jpg',
        'desc'        => 'Houses, apartments and commercial space to rent in Accra and across Ghana. Direct landlord access, vetted tenancies and no client commission.',
    ],

    '/rentals/houses' => [
        'page'        => 'listings',
        'section'     => 'Rentals',
        'basis'       => 'To rent',
        'root'        => '/rentals',
        'path'        => '/rentals/houses',
        'all_label'   => 'All rentals',
        'categories'  => ['houses', 'apartments', 'commercial'],
        'price_bands' => ['min' => [500, 1000, 1500, 2500, 4000], 'max' => [1500, 2500, 4000, 6000, 10000]],
        'eyebrow'     => 'Properties to rent',
        'nav'      => '/rentals',
        'category' => 'houses',
        'title'       => 'Houses to rent in Ghana',
        'h1'          => 'Houses to rent',
        'lead'        => 'Family homes on one and two year leases, in gated communities and established residential streets.',
        'image'       => '/images/properties/townhouse-terrace.jpg',
        'desc'        => 'Family houses to rent in Accra, Aburi and across Ghana, on one and two year leases.',
    ],

    '/rentals/apartments' => [
        'page'        => 'listings',
        'section'     => 'Rentals',
        'basis'       => 'To rent',
        'root'        => '/rentals',
        'path'        => '/rentals/apartments',
        'all_label'   => 'All rentals',
        'categories'  => ['houses', 'apartments', 'commercial'],
        'price_bands' => ['min' => [500, 1000, 1500, 2500, 4000], 'max' => [1500, 2500, 4000, 6000, 10000]],
        'eyebrow'     => 'Properties to rent',
        'nav'      => '/rentals',
        'category' => 'apartments',
        'title'       => 'Apartments to rent in Ghana',
        'h1'          => 'Apartments to rent',
        'lead'        => 'Furnished and unfurnished apartments, from short lets to long leases, with backup power and water as standard.',
        'image'       => '/images/properties/the-pelican.jpg',
        'desc'        => 'Furnished and unfurnished apartments to rent in East Legon, Cantonments and Airport Residential.',
    ],

    '/rentals/commercial' => [
        'page'        => 'listings',
        'section'     => 'Rentals',
        'basis'       => 'To rent',
        'root'        => '/rentals',
        'path'        => '/rentals/commercial',
        'all_label'   => 'All rentals',
        'categories'  => ['houses', 'apartments', 'commercial'],
        'price_bands' => ['min' => [500, 1000, 1500, 2500, 4000], 'max' => [1500, 2500, 4000, 6000, 10000]],
        'eyebrow'     => 'Properties to rent',
        'nav'      => '/rentals',
        'category' => 'commercial',
        'title'       => 'Commercial space to let in Ghana',
        'h1'          => 'Commercial space<br class="hidden sm:block"> to let',
        'lead'        => 'Serviced offices, retail units and shops, with the service charge and the lease terms set out before you view.',
        'image'       => '/images/properties/tower-residences.jpg',
        'desc'        => 'Serviced offices, retail units and shops to let in Accra, with transparent service charges.',
    ],

    '/airbnb' => [
        'page'        => 'listings',
        'section'     => 'Airbnb',
        'basis'       => 'Short stay',
        'root'        => '/airbnb',
        'path'        => '/airbnb',
        'all_label'   => 'All short stays',
        'categories'  => ['houses', 'apartments'],
        'price_bands' => ['min' => [50, 100, 150, 250, 400], 'max' => [100, 200, 300, 500, 1000]],
        'eyebrow'     => 'Short stays',
        
        
        'title'       => 'Airbnb short stays in Ghana',
        'h1'          => 'Short stays,<br class="hidden sm:block"> fully managed',
        'lead'        => 'Whole homes and serviced apartments by the night, cleaned and keyed by our own team rather than a remote host.',
        'image'       => '/images/slideshow/gc-prime-05.jpg',
        'desc'        => 'Airbnb short stays in Accra and the Eastern Region. Whole houses and serviced apartments, nightly and monthly, managed by DDREAM.',
    ],

    '/airbnb/houses' => [
        'page'        => 'listings',
        'section'     => 'Airbnb',
        'basis'       => 'Short stay',
        'root'        => '/airbnb',
        'path'        => '/airbnb/houses',
        'all_label'   => 'All short stays',
        'categories'  => ['houses', 'apartments'],
        'price_bands' => ['min' => [50, 100, 150, 250, 400], 'max' => [100, 200, 300, 500, 1000]],
        'eyebrow'     => 'Short stays',
        'nav'      => '/airbnb',
        'category' => 'houses',
        'title'       => 'Whole houses for short stays in Ghana',
        'h1'          => 'Whole-home short stays',
        'lead'        => 'The entire house to yourself, from a hillside villa with a pool to a family home minutes from the embassies.',
        'image'       => '/images/slideshow/gc-prime-01.jpg',
        'desc'        => 'Whole houses to book by the night in Accra and Aburi, with housekeeping and security included.',
    ],

    '/airbnb/apartments' => [
        'page'        => 'listings',
        'section'     => 'Airbnb',
        'basis'       => 'Short stay',
        'root'        => '/airbnb',
        'path'        => '/airbnb/apartments',
        'all_label'   => 'All short stays',
        'categories'  => ['houses', 'apartments'],
        'price_bands' => ['min' => [50, 100, 150, 250, 400], 'max' => [100, 200, 300, 500, 1000]],
        'eyebrow'     => 'Short stays',
        'nav'      => '/airbnb',
        'category' => 'apartments',
        'title'       => 'Serviced apartments for short stays in Ghana',
        'h1'          => 'Serviced apartment<br class="hidden sm:block"> short stays',
        'lead'        => 'One and two bed apartments with backup power, fibre and self check-in. Nightly, weekly and monthly rates.',
        'image'       => '/images/slideshow/gc-prime-23.jpg',
        'desc'        => 'Serviced apartments to book by the night in East Legon, Airport Residential and Cantonments.',
    ],

    '/virtual-tours' => [
        'page'  => 'virtual-tours',
        'title' => 'Virtual property tours in Ghana',
        'desc'  => 'Live video walkthroughs of Ghanaian property, led by you, with a '
            . 'DDREAM adviser on site holding the camera. Free, booked within 48 hours.',
        'image' => '/images/slideshow/gc-prime-23.jpg',
    ],

    '/blog' => [
        'page'  => 'blog',
        'title' => 'Blogs and guides',
        'desc'  => 'Guides to buying land, letting property and building in Ghana from '
            . 'abroad, plus market intelligence on Accra yields and payment plans.',
    ],

    '/careers' => [
        'page'  => 'careers',
        'title' => 'Careers',
        'desc'  => 'Work at DDREAM in Accra: property advisers, property managers, '
            . 'construction supervisors and client liaison for our diaspora desk.',
        'image' => '/images/front-desk.png',
    ],

    '/contact' => [
        'page'  => 'contact',
        'title' => 'Contact us',
        'desc'  => 'Talk to a DDREAM adviser about buying, renting, letting, building or '
            . 'managing property in Ghana. Office in Accra, replies within one working day, '
            . 'and no client commission.',
        'image' => '/images/front-desk.png',
    ],
];
