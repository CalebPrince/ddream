# DDREAM

Marketing site for **Domestic, Diaspora Real Estate Management Ltd. (DDREAM)**, a
Ghanaian real estate and property management company operating on a *No Client
Commission* basis.

Live domain: **diasporadomesticrem.com**

PHP 8 templates + Tailwind CSS v4. No framework, no build step for the PHP side.

## The admin

Staff manage the site from `/admin`. See [BACKEND.md](BACKEND.md) for the full plan.

### First run

```bash
cp .env.example .env          # then fill in the database credentials
php db/migrate.php            # create the tables
php db/seed.php "Your Name" you@example.com "a-long-password"
```

```bash
php db/import-content.php     # move src/data/*.php into the database
php db/backfill-slugs.php     # give every listing a URL address
```

`db/seed.php` creates the first Superadmin and is safe to re-run; an existing email
is updated rather than duplicated. `db/import-content.php` is also idempotent: rows are
matched on their natural key (listing ref, location slug, media path) and updated rather
than duplicated, so it can be re-run after editing a source file. `php db/migrate.php --status` shows what has run,
`--fresh` drops everything and reapplies (development only).

**On XAMPP locally**, start MySQL first, then create the database:

```bash
C:/xampp/mysql/bin/mysql.exe -u root -e "CREATE DATABASE ddream CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

### Host requirements

PHP 8.2+ with `pdo_mysql`, `mbstring`, `openssl`, plus `gd` and `fileinfo` for the
media library. MySQL 5.7+ or MariaDB 10.3+.

## Running locally

```bash
npm install
```

Build the stylesheet (once), then start the PHP dev server:

```bash
npm run build && npm run serve
```

Open <http://localhost:8080>. While working on styles, run the watcher instead:

```bash
npm run dev
```

## Layout

```
public/            document root. Point the vhost here, never at the project root
  index.php        front controller: every request routes through here
  .htaccess        Apache rewrite to the front controller + asset caching
  assets/css/      built stylesheet (generated, do not edit)
  assets/js/       site.js: nav drawer, hero slideshow, search tabs, reveals
  images/          brand mark, property renders, hero slideshow frames
resources/css/     app.css, Tailwind source and all design tokens
db/
  migrations/      numbered .sql files, applied in order
  migrate.php      the runner
  seed.php         creates the first Superadmin
src/
  admin/           the staff admin, reached at /admin
    kernel.php     admin front controller: session, headers, routing, CSRF
    routes.php     "METHOD /path" => [controller, function]
    auth.php       sessions, sign in, throttling
    capabilities.php  roles as a capability map
    support.php    CSRF, flashes, activity log, view rendering
    controllers/   one file per section
    views/         layout.php is the shell; one view per screen
  routes.php       exact URL path to page template, title and meta description
  routes-dynamic.php  pattern routes: /property/{id} and /blog/{slug}
  config.php       site metadata, navigation tree, footer columns
  helpers.php      e() · config() · asset() · money() · icon() · section() · component()
  repositories/    listings.php: the database queries behind the public pages
  data/            import source for the content tables, and the page copy that
                   does not have a table yet (about, slideshow, areas, insights)
  layout/          document.php (the HTML shell), header.php, footer.php
  pages/           one file per route: home.php, about.php, contact.php,
                   listings.php (serves all nine Selling, Rentals and Airbnb
                   routes), property.php, blog.php, post.php, virtual-tours.php,
                   careers.php, not-found.php
  sections/        one file per page band
  components/      page-hero.php, property-card.php
DESIGN.md          the visual source of truth. Read before changing any styling
```

### Adding a page

1. Add a path to [src/routes.php](src/routes.php) with `page`, `title` and `desc`.
2. Create `src/pages/<page>.php`. It calls `component('page-hero', [...])` then a
   series of `section('name')` calls.
3. Build each band as `src/sections/<name>.php`; put its copy in `src/data/` if it is
   client-supplied text.

Unmatched paths return a 404 through `src/pages/not-found.php`. The nav active state
comes from the route key, or from an explicit `nav` key when a route should highlight a
different top-level item.

## Content

All copy comes from the client's *DDREAM website outline*. Datasets in `src/data/` are
plain PHP arrays shaped for a later PDO/CMS migration. The field names in
`properties.php` are already the intended column names.

## Asset provenance

| Asset | Source | Status |
|---|---|---|
| `images/brand/ddream-logo.png` | Client supplied | Cleared. Whitespace trimmed; artwork untouched. |
| `images/properties/*.jpg` | Client supplied (*Images of buildings.docx*) | Cleared |
| `images/front-desk.png`, `images/hero-city.png` | Client supplied | Cleared |
| `images/slideshow/gc-prime-*.jpg` | Grace City Prime Homes gallery on royalkingdomestate.com, per the client outline | **Third-party. Confirm written permission or a listing agreement before go-live.** |

## Still to build

Every menu item in the client outline is now built: Home, About, Selling (5 routes),
Rentals (4), Airbnb (3), Virtual tours, Blogs, Careers and Contact, plus the property
detail and article templates behind them. 20 routes in total.

### Listings

**Listings now come from the database.** `search_listings()` in `helpers.php` delegates
to `find_listings()` in `src/repositories/listings.php`, which filters and sorts in SQL
and returns rows in exactly the shape the templates already expected. No template
changed when the site moved off the flat file. `/selling/houses?beds=4&max=200000&sort=price-asc`
is still a real, linkable result set that works with JavaScript switched off.

`src/data/listings.php` is retained only as the import source. Editing it changes
nothing until `php db/import-content.php` is run.

`src/pages/listings.php` serves every Selling, Rentals and Airbnb route. What differs between
them comes entirely from the route entry: `basis` (For sale / To rent), `root`,
which `categories` apply (Rentals has no Land) and the `price_bands` for the filter
(sale bands run to $1m, rental bands to $10,000 a month, short-stay bands to $1,000 a
night). Adding another section is three route entries plus stock with a matching
`basis`. Only `/search` and `/saved` are still unbuilt, and neither appears in the client
outline: `/search` is the hero search form's target, `/saved` is the shortlist behind
the header heart. Both fall through to the 404 page.

### Maintenance mode

Settings > Maintenance closes the public site. Visitors get the maintenance page
with a 503 and a `Retry-After` header, so search engines treat it as temporary and
do not drop the pages. The heading, message and an optional "expected back" line
are all editable, and the page keeps the phone number, WhatsApp and email working
because reaching a person still has to work when the site does not.

Signed-in **Admins and Superadmins pass straight through** and see the real site.
The gate runs after the `/admin` handoff in the front controller, so the sign-in
page is always reachable, and the maintenance page carries a staff sign-in link.

The check is on the role, not merely on being signed in, so a future read-only role
would be held at the door with everyone else. It costs an anonymous visitor nothing:
with no session cookie present, `current_staff()` returns before starting a session.

### Media

`/admin/media` is the shared image library. Upload once and reuse anywhere: the
listing editor has a "Use an image already in the library" picker that attaches
existing files rather than uploading a second copy.

Two rules the library enforces. **Alt text is required**, because the whole public
site depends on it for screen readers and search engines; the library has a "Needs a
description" filter to find anything missing one. **An image in use cannot be
deleted**; the detail screen lists everywhere it appears and the delete button stays
disabled until those references are removed, so no page is ever left with a missing
picture.

### Property URLs

Properties are addressed by a slug built from the title, not the reference:
`/property/the-ddream-tower-residences`. The slug is generated on create, is
editable in the admin, and gets a numeric suffix if two properties share a title.

It is deliberately **not** regenerated when a title is edited, because a published
URL must not move under a visitor's feet. Change it by editing the address field
itself. Old reference URLs such as `/property/dd-1042` still resolve and issue a
301 to the slug, so only one address is ever indexed.

### Dynamic routes

`src/routes-dynamic.php` holds pattern routes, tried after the exact matches.
`{name}` placeholders arrive as `params`, and a `resolve` callback turns the parameter
into a record, filling in the title and meta, or returns null to fall through to the
404. That is how `/property/dd-1042` and `/blog/accra-rental-yields` resolve, and how
`/property/dd-9999` correctly 404s.

Three forms are markup only and need handlers: the hero property search, the footer
newsletter, and the contact enquiry form (`POST /contact`). The contact form needs
server-side validation, a spam check and a mail transport before launch; the browser
`required` attributes are a convenience, not a defence.

**Placeholder data still to be replaced by the client:** the telephone and WhatsApp
numbers and the street address in `src/config.php`, and the entire inventory in
`src/data/listings.php` (25 constructed listings: 11 for sale, 8 to rent and 6 short
stays, built from the supplied renders so the results pages are real and testable). `info@diasporadomesticrem.com` is derived from the real
domain but the mailbox name is still a guess.

**Waiting on the client:** staff names, roles and headshots. The About page deliberately
has no team section, because inventing people would be a lie in the one place the page is
arguing for trustworthiness. `#office` covers that ground with the real front-desk
photograph instead; drop a team band in beside it when the content arrives.
