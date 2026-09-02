<?php
declare(strict_types=1);

/**
 * What each editable band on the site is made of.
 *
 * One entry per `page_sections.section_key`. The `default` on every field is the
 * wording the site ships with, so a section that has never been edited still
 * renders, and the Page contents screen opens with the live copy in it rather
 * than an empty box. Only fields an editor actually changes are written to the
 * database; putting a field back to its original wording clears it again.
 *
 * Field types
 *   line   one-line text          text   paragraph
 *   rich   paragraph, keeps <strong> <em> <a> <br>
 *   lines  one item per line, stored as a list of strings
 *   list   repeating rows, `item` describes the columns
 *   link   a URL or path          image  a path under public/
 *   icon   an icon name, inside `item` only
 */

$about = data_set('about');

/** The card shape shared by several bands. */
$iconTitleBody = [
    ['key' => 'icon',  'label' => 'Icon',  'type' => 'icon'],
    ['key' => 'title', 'label' => 'Title', 'type' => 'line'],
    ['key' => 'body',  'label' => 'Body',  'type' => 'text'],
];

/** The interior-page header. Same fields on About, Contact, Tours and Careers. */
$pageHero = static fn (array $defaults): array => [
    'locked' => true,
    'fields' => [
        ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => $defaults['eyebrow']],
        ['key' => 'heading', 'label' => 'Heading', 'type' => 'rich', 'default' => $defaults['heading']],
        ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text', 'default' => $defaults['lead']],
        ['key' => 'image', 'label' => 'Photograph', 'type' => 'image', 'default' => $defaults['image']],
        ['key' => 'image_alt', 'label' => 'Image description', 'type' => 'line',
         'default' => $defaults['image_alt'],
         'help' => 'Read aloud by screen readers. Describe what is in the picture.'],
        ['key' => 'facts', 'label' => 'Figures under the lead', 'type' => 'list', 'max' => 3,
         'item' => [
             ['key' => 'label', 'label' => 'Label', 'type' => 'line'],
             ['key' => 'value', 'label' => 'Value', 'type' => 'line'],
         ],
         'default' => $defaults['facts']],
    ],
];

return [

    // ------------------------------------------------------------------- home

    'hero' => [
        'locked' => true,
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line',
             'default' => 'Domestic & Diaspora Real Estate Management'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line',
             'default' => 'Serving the domestic and'],
            ['key' => 'heading_accent', 'label' => 'Heading, gold italic line', 'type' => 'line',
             'default' => 'diaspora communities.'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'rich',
             'default' => 'With integrity and outstanding professionalism, one client at a time, '
                . 'on a <strong>no client commission</strong> basis.'],
            ['key' => 'badge', 'label' => 'Slideshow badge', 'type' => 'line',
             'default' => 'Featured development'],
            ['key' => 'tour_label', 'label' => 'Slideshow tour link', 'type' => 'line',
             'default' => 'Virtual tour'],
            ['key' => 'search_note', 'label' => 'Note under the search panel', 'type' => 'text',
             'default' => 'Every listing is title-checked before it appears. We deal directly with '
                . 'landlords and developers, never middlemen.'],
            ['key' => 'facts', 'label' => 'Trust markers', 'type' => 'list', 'max' => 3,
             'help' => 'Write {fee} for the flat admin fee.',
             'item' => [
                 ['key' => 'label', 'label' => 'Label', 'type' => 'line'],
                 ['key' => 'value', 'label' => 'Value', 'type' => 'line'],
             ],
             'default' => [
                 ['label' => 'Client commission', 'value' => 'None'],
                 ['label' => 'Flat admin fee',    'value' => '{fee}'],
                 ['label' => 'Middlemen',         'value' => 'Zero'],
             ]],
        ],
    ],

    'culturally-curated' => [
        'fields' => [
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'rich',
             'default' => 'Our Building Blocks are<br> Culturally Curated.'],
            ['key' => 'strapline', 'label' => 'Strapline', 'type' => 'line',
             'default' => 'One Client at a time.',
             'help' => 'The "No Client Commission" line beside it is part of the design.'],
            ['key' => 'image', 'label' => 'Photograph', 'type' => 'image',
             'default' => '/images/kente-cloth.jpg'],
            ['key' => 'image_alt', 'label' => 'Image description', 'type' => 'line',
             'default' => 'Rolls of woven kente cloth in green, gold, red and black'],
            ['key' => 'points', 'label' => 'Trust cards', 'type' => 'list', 'item' => $iconTitleBody,
             'default' => [
                 ['icon' => 'shield-check', 'title' => 'Registered company', 'body' => 'A limited liability company filed with the Registrar of Companies, working from an office you can walk into.'],
                 ['icon' => 'users', 'title' => 'No middlemen', 'body' => 'We hold the relationships with landlords, developers and vendors ourselves.'],
                 ['icon' => 'globe', 'title' => 'Local and global', 'body' => 'Staff born and bred in Ghana, with several years of working experience abroad.'],
                 ['icon' => 'file-check', 'title' => 'Due diligence first', 'body' => 'Title searches and litigation checks before any offer is made on your behalf.'],
             ]],
            ['key' => 'footnote', 'label' => 'Footnote', 'type' => 'text',
             'help' => 'Write {fee} where the flat admin fee from Settings should appear.',
             'default' => 'The commission falls on the seller or landlord, never on you. You pay a '
                . 'flat {fee} administrative fee and nothing else.'],
        ],
    ],

    'quick-links' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Where would you like to start?'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Six ways we work with you'],
            ['key' => 'link_label', 'label' => 'Corner link', 'type' => 'line', 'default' => 'All fifteen services'],
            ['key' => 'link_href', 'label' => 'Corner link address', 'type' => 'link', 'default' => '/about#services'],
            ['key' => 'tiles', 'label' => 'Tiles', 'type' => 'list',
             'item' => [
                 ['key' => 'icon',  'label' => 'Icon',    'type' => 'icon'],
                 ['key' => 'title', 'label' => 'Title',   'type' => 'line'],
                 ['key' => 'body',  'label' => 'Body',    'type' => 'text'],
                 ['key' => 'href',  'label' => 'Address', 'type' => 'link'],
             ],
             'default' => [
                 ['icon' => 'key', 'title' => 'Buy a property', 'body' => 'Houses, apartments, commercial space and land across Ghana.', 'href' => '/selling'],
                 ['icon' => 'building', 'title' => 'Rent or let', 'body' => 'Furnished and unfurnished homes, offices and retail units.', 'href' => '/rentals'],
                 ['icon' => 'calendar', 'title' => 'Airbnb short stays', 'body' => 'Nightly and monthly whole-home stays, fully managed.', 'href' => '/airbnb'],
                 ['icon' => 'camera', 'title' => 'Virtual tours', 'body' => 'Walk a property from anywhere before you fly in.', 'href' => '/virtual-tours'],
                 ['icon' => 'hard-hat', 'title' => 'Build and supervise', 'body' => 'Weekly construction reports while you are overseas.', 'href' => '/about#service-supervision'],
                 ['icon' => 'chart', 'title' => 'Value my property', 'body' => 'Coordinated valuation and investment advisory.', 'href' => '/about#service-valuation'],
             ]],
        ],
    ],

    'featured' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Currently on our books'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Featured properties'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'default' => 'A small, deliberately curated list. Every property below has been '
                . 'inspected by our team and title-checked at the Lands Commission.'],
            ['key' => 'link_label', 'label' => 'Button', 'type' => 'line', 'default' => 'View all listings'],
            ['key' => 'link_href', 'label' => 'Button address', 'type' => 'link', 'default' => '/selling'],
            ['key' => 'footnote', 'label' => 'Footnote', 'type' => 'rich',
             'help' => 'Which properties appear here is set on each listing, under Listings.',
             'default' => 'Looking for something specific? <a href="/contact">Send us your brief</a> '
                . 'and we will source it. We find off-market properties for clients every week.'],
        ],
    ],

    'diaspora' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'For Ghanaians abroad'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line',
             'default' => 'Investing from 5,000 miles away shouldn\'t feel like a gamble'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'default' => 'Transparency, project oversight, documentation, property management: the '
                . 'four things that go wrong when you buy from abroad. Our whole business is built '
                . 'to remove them.'],
            ['key' => 'steps', 'label' => 'Numbered steps', 'type' => 'list',
             'item' => [
                 ['key' => 'title', 'label' => 'Title', 'type' => 'line'],
                 ['key' => 'body',  'label' => 'Body',  'type' => 'text'],
             ],
             'default' => [
                 ['title' => 'Tell us the brief', 'body' => 'A video call at a time that works in your timezone. Budget, location, purpose: investment, family home, or a build.'],
                 ['title' => 'We shortlist and inspect', 'body' => 'We visit in person, film a walkthrough, and send you photographs, measurements and the honest problems as well as the selling points.'],
                 ['title' => 'Due diligence before money moves', 'body' => 'Lands Commission search, indenture review, litigation check and developer background, all reported to you in writing.'],
                 ['title' => 'Close, then hand over to management', 'body' => 'Documentation, registration and keys. Then rent collection, maintenance and statements for as long as you need us.'],
             ]],
            ['key' => 'primary_label', 'label' => 'First button', 'type' => 'line', 'default' => 'Diaspora investment support'],
            ['key' => 'primary_href', 'label' => 'First button address', 'type' => 'link', 'default' => '/services/diaspora'],
            ['key' => 'secondary_label', 'label' => 'Second button', 'type' => 'line', 'default' => 'Book a video consultation'],
            ['key' => 'secondary_href', 'label' => 'Second button address', 'type' => 'link', 'default' => '/contact'],
            ['key' => 'image', 'label' => 'Photograph', 'type' => 'image', 'default' => '/images/front-desk.png'],
            ['key' => 'image_alt', 'label' => 'Image description', 'type' => 'line',
             'default' => 'The DDREAM reception desk at the Airport Residential office in Accra'],
            ['key' => 'caption', 'label' => 'Caption under the photograph', 'type' => 'text',
             'default' => 'Our front desk in Accra. We hold meetings here, and clients are welcome '
                . 'to walk in, meet the team and see the files.'],
            ['key' => 'stats', 'label' => 'Figures', 'type' => 'list', 'max' => 3,
             'item' => [
                 ['key' => 'value', 'label' => 'Value', 'type' => 'line'],
                 ['key' => 'label', 'label' => 'Label', 'type' => 'line'],
             ],
             'default' => [
                 ['value' => '15', 'label' => 'Services under one roof'],
                 ['value' => '8+', 'label' => 'Diaspora corridors served'],
                 ['value' => '100%', 'label' => 'Listings title-checked'],
             ]],
        ],
    ],

    'commission' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'What sets us apart'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'rich',
             'default' => 'The commission never<br> falls on you'],
            ['key' => 'panel_note', 'label' => 'Note inside the fee panel', 'type' => 'text',
             'help' => 'Write {fee} where the flat admin fee from Settings should appear.',
             'default' => 'A single {fee} administrative fee per client covers our running costs. '
                . 'That is the whole of what you pay us. There is no percentage, no success fee '
                . 'and no surprise at closing.'],
            ['key' => 'note', 'label' => 'Paragraph under the panel', 'type' => 'text',
             'default' => 'Whether you are in Accra, Kumasi, London, Amsterdam, Berlin, New York, '
                . 'Toronto, Johannesburg, a member of the diplomatic community or anywhere else in '
                . 'the world, the arithmetic is the same.'],
            ['key' => 'items', 'label' => 'Cards', 'type' => 'list', 'item' => $iconTitleBody,
             'default' => [
                 ['icon' => 'globe', 'title' => 'Local expertise, global perspective', 'body' => 'Our staff were born and bred in Ghana and have spent years working abroad. They know both sides of the conversation.'],
                 ['icon' => 'landmark', 'title' => 'A real, identifiable office', 'body' => 'A registered limited liability company operating from a recognised address. Meetings happen in our offices, face to face.'],
                 ['icon' => 'users', 'title' => 'Direct landlord access', 'body' => 'We hold the relationships ourselves. No agent chains, no inflated asking prices, no phantom listings.'],
                 ['icon' => 'clock', 'title' => 'Swift and bespoke service', 'body' => 'One client at a time. You get a named contact who answers, and a service shaped around your circumstances.'],
             ]],
            ['key' => 'quote', 'label' => 'Pull quote', 'type' => 'text',
             'default' => 'We combine local expertise with a global perspective, so every client '
                . 'enjoys a safe, transparent and rewarding real estate experience.'],
            ['key' => 'quote_source', 'label' => 'Pull quote attribution', 'type' => 'line',
             'default' => 'The DDREAM commitment'],
        ],
    ],

    'services' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Our services'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'End-to-end, under one roof'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'default' => 'Our network of reputable developers, financial institutions, legal '
                . 'professionals, surveyors, architects and contractors sits behind every one of these.'],
            ['key' => 'link_href', 'label' => 'Button address', 'type' => 'link', 'default' => '/about#services'],
            ['key' => 'secondary_heading', 'label' => 'Heading above the shorter list', 'type' => 'line',
             'default' => 'Also available',
             'help' => 'The services themselves are edited under Services.'],
        ],
    ],

    'areas' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Where we operate'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Search by area'],
            ['key' => 'link_label', 'label' => 'Corner link', 'type' => 'line', 'default' => 'Browse every location'],
            ['key' => 'link_href', 'label' => 'Corner link address', 'type' => 'link', 'default' => '/areas'],
        ],
    ],

    'insights' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Blogs and guides'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Market intelligence, written plainly'],
            ['key' => 'link_label', 'label' => 'Button', 'type' => 'line', 'default' => 'Read the blog'],
            ['key' => 'link_href', 'label' => 'Button address', 'type' => 'link', 'default' => '/blog',
             'help' => 'The posts shown here are chosen under Blogs.'],
        ],
    ],

    'cta' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Start the conversation'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line',
             'default' => 'Invest with confidence. Manage with peace of mind.'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'help' => 'Write {fee} where the flat admin fee from Settings should appear.',
             'default' => 'Tell us what you are looking for and we will come back within one working '
                . 'day with a shortlist, an honest view of the market, and a clear fee of {fee}, '
                . 'and nothing else.'],
            ['key' => 'primary_label', 'label' => 'Button', 'type' => 'line', 'default' => 'Book a Consultation'],
            ['key' => 'primary_href', 'label' => 'Button address', 'type' => 'link', 'default' => '/contact'],
            ['key' => 'assurances', 'label' => 'Assurances', 'type' => 'list',
             'item' => [
                 ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon'],
                 ['key' => 'text', 'label' => 'Text', 'type' => 'line'],
             ],
             'default' => [
                 ['icon' => 'badge-check', 'text' => 'A named adviser, not a call centre'],
                 ['icon' => 'shield-check', 'text' => 'Due diligence before any payment'],
                 ['icon' => 'globe', 'text' => 'Calls scheduled in your timezone'],
                 ['icon' => 'file-check', 'text' => 'Everything confirmed in writing'],
             ]],
        ],
    ],

    // ------------------------------------------------------------------ about

    'about-hero' => $pageHero([
        'eyebrow'   => 'About DDREAM',
        'heading'   => 'A Ghanaian company<br> built for distance',
        'lead'      => 'Domestic, Diaspora Real Estate Management Ltd. exists to close the '
            . 'distance between property in Ghana and the people who want to own, build or let '
            . 'it from anywhere in the world.',
        'image'     => '/images/properties/tower-residences.jpg',
        'image_alt' => 'A DDREAM residential tower at dusk in Cantonments, Accra',
        'facts'     => [
            ['label' => 'Client commission', 'value' => 'None'],
            ['label' => 'Flat admin fee',    'value' => '{fee}'],
            ['label' => 'Services',          'value' => '15'],
        ],
    ]),

    'about-intro' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Who we are'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'About us'],
            ['key' => 'paragraphs', 'label' => 'Paragraphs', 'type' => 'lines',
             'help' => 'One paragraph per line. The first is set larger.',
             'default' => $about['intro']],
            ['key' => 'aside_heading', 'label' => 'Panel heading', 'type' => 'line',
             'default' => 'The network behind us'],
            ['key' => 'aside_body', 'label' => 'Panel paragraph', 'type' => 'text',
             'default' => 'Every transaction draws on the same bench of vetted professionals. We '
                . 'coordinate them so you deal with one point of contact.'],
            ['key' => 'network', 'label' => 'Panel list', 'type' => 'lines',
             'default' => ['Reputable developers', 'Financial institutions', 'Legal professionals',
                 'Licensed surveyors', 'Architects', 'Contractors']],
            ['key' => 'aside_link_label', 'label' => 'Panel button', 'type' => 'line', 'default' => 'Visit our office'],
            ['key' => 'aside_link_href', 'label' => 'Panel button address', 'type' => 'link', 'default' => '/contact'],
        ],
    ],

    'about-mission' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'What we are here to do'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Our mission and vision'],
            ['key' => 'mission_heading', 'label' => 'First card heading', 'type' => 'line', 'default' => 'Our Mission'],
            ['key' => 'mission', 'label' => 'Mission', 'type' => 'text', 'default' => $about['mission']],
            ['key' => 'vision_heading', 'label' => 'Second card heading', 'type' => 'line', 'default' => 'Our Vision'],
            ['key' => 'vision', 'label' => 'Vision', 'type' => 'text', 'default' => $about['vision']],
        ],
    ],

    'about-objectives' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'What we set out to do'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Our Objectives'],
            ['key' => 'objectives', 'label' => 'Objectives', 'type' => 'lines',
             'help' => 'One objective per line. They are numbered automatically.',
             'default' => $about['objectives']],
        ],
    ],

    'about-values' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'What we hold to'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Our Core Values'],
            ['key' => 'note', 'label' => 'Note beside the heading', 'type' => 'text',
             'default' => 'These are the values the whole business is judged on, by us and by you.'],
            ['key' => 'values', 'label' => 'Values', 'type' => 'list',
             'item' => [
                 ['key' => 'icon', 'label' => 'Icon', 'type' => 'icon'],
                 ['key' => 'text', 'label' => 'Value', 'type' => 'line'],
             ],
             'default' => $about['values']],
        ],
    ],

    'about-services' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'What we do'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Our Services'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'help' => 'Write {count} where the number of services should appear.',
             'default' => 'All {count} of them, delivered by one team. Our extensive network of '
                . 'reputable developers, financial institutions, legal professionals, surveyors, '
                . 'architects and contractors sits behind every one.'],
            ['key' => 'link_label', 'label' => 'Button', 'type' => 'line', 'default' => 'Discuss your requirement'],
            ['key' => 'link_href', 'label' => 'Button address', 'type' => 'link', 'default' => '/contact'],
            ['key' => 'footnote', 'label' => 'Footnote', 'type' => 'rich',
             'help' => 'Write {fee} where the flat admin fee from Settings should appear. '
                . 'The services themselves are edited under Services.',
             'default' => 'Every one of these is delivered on the same <strong>No Client '
                . 'Commission</strong> basis: the commission falls on the seller or landlord, and '
                . 'you pay a flat {fee} administrative fee.'],
        ],
    ],

    'about-apart' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'The difference'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'What Sets Us Apart?'],
            ['key' => 'panel_note', 'label' => 'Note inside the fee panel', 'type' => 'text',
             'default' => 'No percentage, no success fee, no surprise at closing.'],
            ['key' => 'items', 'label' => 'Cards', 'type' => 'list', 'item' => $iconTitleBody,
             'default' => $about['apart']],
            ['key' => 'reach_heading', 'label' => 'Reach heading', 'type' => 'line',
             'default' => 'Wherever you happen to be'],
            ['key' => 'reach', 'label' => 'Reach paragraph', 'type' => 'text', 'default' => $about['reach'],
             'help' => 'The cities listed beside it come from Settings.'],
        ],
    ],

    'about-office' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Come and see us'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line',
             'default' => 'We hold meetings in our own office'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'default' => 'A registered company at an address you can walk into. Clients are welcome '
                . 'to visit, meet the team and see the files before committing to anything.'],
            ['key' => 'image', 'label' => 'Photograph', 'type' => 'image', 'default' => '/images/front-desk.png'],
            ['key' => 'image_alt', 'label' => 'Image description', 'type' => 'line',
             'default' => 'The DDREAM reception at the Airport Residential office in Accra'],
            ['key' => 'expect', 'label' => 'What to expect', 'type' => 'list', 'item' => $iconTitleBody,
             'default' => [
                 ['icon' => 'users', 'title' => 'A face, not a call centre', 'body' => 'You meet the person who will handle your file, in our office, before anything is signed.'],
                 ['icon' => 'file-check', 'title' => 'Firsthand information', 'body' => 'Company documents, and the full file on every property under consideration.'],
                 ['icon' => 'camera', 'title' => 'A viewing plan', 'body' => 'Filmed walkthroughs if you are abroad, a driver and a schedule if you are flying in.'],
             ]],
            ['key' => 'hours_label', 'label' => 'Opening hours label', 'type' => 'line', 'default' => 'Opening hours'],
            ['key' => 'phone_label', 'label' => 'Telephone label', 'type' => 'line', 'default' => 'Book ahead on'],
            ['key' => 'primary_label', 'label' => 'First button', 'type' => 'line', 'default' => 'Book a Consultation'],
            ['key' => 'primary_href', 'label' => 'First button address', 'type' => 'link', 'default' => '/contact'],
            ['key' => 'secondary_label', 'label' => 'Second button', 'type' => 'line', 'default' => 'See all our services'],
            ['key' => 'secondary_href', 'label' => 'Second button address', 'type' => 'link', 'default' => '#services'],
        ],
    ],

    // ---------------------------------------------------------- virtual tours

    'virtual-tours-hero' => $pageHero([
        'eyebrow'   => 'Virtual tours',
        'heading'   => 'View it properly,<br> from wherever you are',
        'lead'      => 'A live video walkthrough led by you, with one of our advisers standing in '
            . 'the property holding the camera. Not a slideshow, and not an agent reading from a '
            . 'brochure.',
        'image'     => '/images/slideshow/gc-prime-23.jpg',
        'image_alt' => 'Open-plan living space in a DDREAM property',
        'facts'     => [
            ['label' => 'Typical length', 'value' => '30 min'],
            ['label' => 'Cost to you',    'value' => 'Free'],
            ['label' => 'Booked within',  'value' => '48 hrs'],
        ],
    ]),

    'virtual-tours' => [
        'locked' => true,
        'fields' => [
            ['key' => 'steps_eyebrow', 'label' => 'Steps eyebrow', 'type' => 'line', 'default' => 'How it works'],
            ['key' => 'steps_heading', 'label' => 'Steps heading', 'type' => 'line',
             'default' => 'Four steps, no cost, no obligation'],
            ['key' => 'steps', 'label' => 'Steps', 'type' => 'list', 'item' => $iconTitleBody,
             'default' => [
                 ['icon' => 'calendar', 'title' => 'Book a slot in your timezone', 'body' => 'Tell us when you are free. We work Ghana hours, but we hold evening slots for North America and morning slots for Australia.'],
                 ['icon' => 'camera', 'title' => 'We go to the property', 'body' => 'An adviser attends in person with a phone gimbal and a tape measure. You are on the call live, not watching a recording.'],
                 ['icon' => 'users', 'title' => 'You direct the walkthrough', 'body' => 'Ask us to open the cupboards, run the taps, look at the ceiling, check the meter, step outside and film the street.'],
                 ['icon' => 'file-check', 'title' => 'You get the file afterwards', 'body' => 'The recording, the photographs, the measurements, and an honest written note of the problems as well as the selling points.'],
             ]],
            ['key' => 'formats_eyebrow', 'label' => 'Formats eyebrow', 'type' => 'line', 'default' => 'Three ways to view'],
            ['key' => 'formats_heading', 'label' => 'Formats heading', 'type' => 'line',
             'default' => 'Pick the one that fits your week'],
            ['key' => 'formats', 'label' => 'Formats', 'type' => 'list',
             'item' => [
                 ['key' => 'icon',  'label' => 'Icon',  'type' => 'icon'],
                 ['key' => 'title', 'label' => 'Title', 'type' => 'line'],
                 ['key' => 'meta',  'label' => 'Timing', 'type' => 'line'],
                 ['key' => 'body',  'label' => 'Body',  'type' => 'text'],
             ],
             'default' => [
                 ['icon' => 'play', 'title' => 'Live guided tour', 'meta' => '30 to 45 minutes', 'body' => 'A video call from inside the property, led by you. The most useful option before an offer.'],
                 ['icon' => 'camera', 'title' => 'Filmed walkthrough', 'meta' => 'Sent within 48 hours', 'body' => 'A recorded walk through every room, narrated, with measurements called out. Watch it whenever suits.'],
                 ['icon' => 'compass', 'title' => 'Neighbourhood drive', 'meta' => '15 minutes', 'body' => 'The street, the junction, the nearest school and market. What a photograph of the house never shows you.'],
             ]],
            ['key' => 'primary_label', 'label' => 'First button', 'type' => 'line', 'default' => 'Book a virtual tour'],
            ['key' => 'primary_href', 'label' => 'First button address', 'type' => 'link', 'default' => '/contact'],
            ['key' => 'whatsapp_label', 'label' => 'WhatsApp button', 'type' => 'line', 'default' => 'Ask on WhatsApp'],
            ['key' => 'listings_eyebrow', 'label' => 'Listings eyebrow', 'type' => 'line', 'default' => 'Ready to walk through'],
            ['key' => 'listings_heading', 'label' => 'Listings heading', 'type' => 'line', 'default' => 'Available to tour this week'],
            ['key' => 'listings_lead', 'label' => 'Listings lead', 'type' => 'text',
             'default' => 'Any property on our books can be toured. These are the ones with an '
                . 'adviser already scheduled nearby.'],
            ['key' => 'listings_link_label', 'label' => 'Listings button', 'type' => 'line', 'default' => 'Browse everything'],
            ['key' => 'listings_link_href', 'label' => 'Listings button address', 'type' => 'link', 'default' => '/selling'],
        ],
    ],

    // --------------------------------------------------------------- careers

    'careers-hero' => $pageHero([
        'eyebrow'   => 'Careers at DDREAM',
        'heading'   => 'Work somewhere<br> the answer can be no',
        'lead'      => 'We are paid by the seller, never the client, so nobody here is pushed to '
            . 'close a deal that is wrong for the person in front of them. It makes for a better '
            . 'job as well as a better service.',
        'image'     => '/images/front-desk.png',
        'image_alt' => 'The DDREAM front desk in Accra',
        'facts'     => [
            ['label' => 'Open roles', 'value' => '{count}'],
            ['label' => 'Based in',   'value' => 'Accra'],
            ['label' => 'Clients in', 'value' => '8+'],
        ],
    ]),

    'careers' => [
        'locked' => true,
        'fields' => [
            ['key' => 'why_eyebrow', 'label' => 'Why work here eyebrow', 'type' => 'line', 'default' => 'Why work here'],
            ['key' => 'why_heading', 'label' => 'Why work here heading', 'type' => 'line',
             'default' => 'Four things that make this different'],
            ['key' => 'reasons', 'label' => 'Reasons', 'type' => 'list', 'item' => $iconTitleBody,
             'default' => [
                 ['icon' => 'users', 'title' => 'One client at a time', 'body' => 'You are not handed an impossible pipeline. Advisers carry a portfolio small enough to know every client by name.'],
                 ['icon' => 'shield-check', 'title' => 'Nothing to hide', 'body' => 'We are paid the same whatever the client decides, so nobody here is ever asked to oversell a property.'],
                 ['icon' => 'globe', 'title' => 'Local and international', 'body' => 'Our clients are in Accra, London, Toronto and Johannesburg. Ghanaian property, with an international desk around it.'],
                 ['icon' => 'landmark', 'title' => 'A real office', 'body' => 'A registered company with an address, a front desk and colleagues in the room. Not a phone and a WhatsApp group.'],
             ]],
            ['key' => 'roles_eyebrow', 'label' => 'Roles eyebrow', 'type' => 'line', 'default' => 'Open roles'],
            ['key' => 'roles_heading', 'label' => 'Roles heading', 'type' => 'line', 'default' => 'Currently hiring',
             'help' => 'The vacancies themselves are edited under Careers.'],
            ['key' => 'roles_heading_empty', 'label' => 'Roles heading when there are none', 'type' => 'line',
             'default' => 'No vacancies right now'],
            ['key' => 'empty_heading', 'label' => 'Empty state heading', 'type' => 'line',
             'default' => 'Nothing advertised at the moment'],
            ['key' => 'empty_body', 'label' => 'Empty state paragraph', 'type' => 'text',
             'default' => 'We still read every speculative application, and we keep good ones on file.'],
            ['key' => 'spec_eyebrow', 'label' => 'Speculative eyebrow', 'type' => 'line', 'default' => 'Nothing that fits?'],
            ['key' => 'spec_heading', 'label' => 'Speculative heading', 'type' => 'line', 'default' => 'Write to us anyway'],
            ['key' => 'spec_lead', 'label' => 'Speculative paragraph', 'type' => 'text',
             'default' => 'Surveyors, conveyancers, interior designers, facility managers and '
                . 'photographers: much of our work is delivered through a network of professionals '
                . 'rather than by staff alone. Tell us what you do and how you work.'],
            ['key' => 'card_heading', 'label' => 'Application card heading', 'type' => 'line', 'default' => 'Send an application'],
            ['key' => 'card_body', 'label' => 'Application card paragraph', 'type' => 'text',
             'default' => 'A CV and a short note about why. We reply within one working day.'],
            ['key' => 'cv_label', 'label' => 'Email button', 'type' => 'line', 'default' => 'Email your CV'],
            ['key' => 'contact_label', 'label' => 'Contact form button', 'type' => 'line', 'default' => 'Or use the contact form'],
        ],
    ],

    // --------------------------------------------------------------- contact

    'contact-hero' => $pageHero([
        'eyebrow'   => 'Contact DDREAM',
        'heading'   => 'Talk to someone<br> who answers',
        'lead'      => 'A named adviser, a reply within one working day, and a call scheduled for '
            . 'your timezone rather than ours.',
        'image'     => '/images/front-desk.png',
        'image_alt' => 'The DDREAM reception at the Airport Residential office in Accra',
        'facts'     => [
            ['label' => 'We reply within',   'value' => '1 day'],
            ['label' => 'Client commission', 'value' => 'None'],
            ['label' => 'Flat admin fee',    'value' => '{fee}'],
        ],
    ]),

    'contact-details' => [
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Contact details'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Four ways to reach us'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'default' => 'Whichever you choose, you get a named adviser rather than a queue.'],
            ['key' => 'notes', 'label' => 'Notes under each channel', 'type' => 'list', 'max' => 4,
             'help' => 'The numbers and address themselves come from Settings.',
             'item' => [
                 ['key' => 'label', 'label' => 'Channel', 'type' => 'line'],
                 ['key' => 'note',  'label' => 'Note',    'type' => 'line'],
             ],
             'default' => [
                 ['label' => 'Telephone', 'note' => 'Office hours, Ghana time (GMT)'],
                 ['label' => 'WhatsApp',  'note' => 'Best if you are calling from abroad'],
                 ['label' => 'Email',     'note' => 'Replied to within one working day'],
                 ['label' => 'Office',    'note' => ''],
             ]],
            ['key' => 'walk_in', 'label' => 'Walk-in note', 'type' => 'text',
             'default' => 'You are also welcome to walk in. Meetings are held in our own offices.'],
        ],
    ],

    'contact-form' => [
        'locked' => true,
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Send us a brief'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'Tell us what you are looking for'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'default' => 'The more you can tell us, the more useful our first reply will be. '
                . 'Nothing here commits you to anything.'],
            ['key' => 'interests', 'label' => 'Interest choices', 'type' => 'lines',
             'help' => 'One choice per line. These appear in the "I am interested in" menu.',
             'default' => ['Buying a property', 'Renting a property', 'Airbnb / short stay',
                 'Selling my property', 'Letting my property', 'Property management',
                 'Building or project supervision', 'Land acquisition', 'Something else']],
            ['key' => 'consent', 'label' => 'Consent wording', 'type' => 'line',
             'default' => 'I am happy for DDREAM to contact me about this enquiry.'],
            ['key' => 'submit_label', 'label' => 'Send button', 'type' => 'line', 'default' => 'Send my enquiry'],
            ['key' => 'call_label', 'label' => 'Call button', 'type' => 'line', 'default' => 'Call instead'],
            ['key' => 'privacy_note', 'label' => 'Privacy note', 'type' => 'text',
             'default' => 'We use your details to answer this enquiry and nothing else. We never '
                . 'sell or share them.'],
            ['key' => 'next_heading', 'label' => 'Panel heading', 'type' => 'line', 'default' => 'What happens next'],
            ['key' => 'steps', 'label' => 'What happens next', 'type' => 'list', 'item' => $iconTitleBody,
             'default' => [
                 ['icon' => 'mail', 'title' => 'We read it properly', 'body' => 'Your enquiry goes to an adviser, not an autoresponder.'],
                 ['icon' => 'phone', 'title' => 'We come back in a day', 'body' => 'One working day, on the channel and at the time you asked for.'],
                 ['icon' => 'file-check', 'title' => 'You get it in writing', 'body' => 'A shortlist, an honest view of the market, and a clear fee.'],
             ]],
            ['key' => 'panel_note', 'label' => 'Fee panel note', 'type' => 'text',
             'help' => 'Write {fee} where the flat admin fee from Settings should appear.',
             'default' => 'The commission falls on the seller or landlord. You pay a flat {fee} '
                . 'administrative fee, and nothing else.'],
        ],
    ],

    // ------------------------------------------------------------- not found

    'not-found' => [
        'locked' => true,
        'fields' => [
            ['key' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'line', 'default' => 'Error 404'],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'line', 'default' => 'We cannot find that page'],
            ['key' => 'lead', 'label' => 'Lead paragraph', 'type' => 'text',
             'default' => 'The link may be out of date, or the page may not be built yet. Here is '
                . 'where most people go next.'],
            ['key' => 'links', 'label' => 'Suggested links', 'type' => 'list',
             'item' => [
                 ['key' => 'icon',  'label' => 'Icon',    'type' => 'icon'],
                 ['key' => 'label', 'label' => 'Label',   'type' => 'line'],
                 ['key' => 'href',  'label' => 'Address', 'type' => 'link'],
             ],
             'default' => [
                 ['icon' => 'key', 'label' => 'Browse properties for sale', 'href' => '/selling'],
                 ['icon' => 'building', 'label' => 'Browse properties to rent', 'href' => '/rentals'],
                 ['icon' => 'users', 'label' => 'About DDREAM', 'href' => '/about'],
                 ['icon' => 'phone', 'label' => 'Talk to an adviser', 'href' => '/contact'],
             ]],
        ],
    ],
];
