# DDREAM backend plan

Phase two: put a database and a staff-managed admin behind the finished public site.

The formatted version of this document, for sharing with the client, is published at
<https://claude.ai/code/artifact/6441ee39-eee5-4015-9dc3-0130b24efd60>.

## Starting point

The public site is complete: 20 routes, 25 listings, three inert forms. All content lives
in `src/data/*.php` as structured arrays whose field names were chosen to be the eventual
database columns, so this is a migration rather than a rewrite.

## Stack

**Recommended: plain PHP 8.2 + MySQL**, matching the front end. Runs on ordinary cPanel
shared hosting with no SSH, Composer or build step. Cost: auth, validation and file
handling are hand written, which is why the security checklist below is not optional.

**Alternative: Laravel + Filament.** Halves the admin build and is safer out of the box,
but needs a VPS, a deploy process, and someone who knows Laravel available long term.
Pick this if DDREAM will keep a developer on retainer.

## Roles

Two roles now. Permissions are a capability map per role, not `if role === 'admin'`
scattered through the code, so adding Editor or Agent later is configuration.

| Capability | Superadmin | Admin |
|---|---|---|
| `listings.*` | yes | yes |
| `listings.delete` | yes | archive only |
| `pages.edit`, `blog.*`, `careers.*`, `services.*`, `media.*`, `inbox.*` | yes | yes |
| `settings.company` | yes | yes |
| `settings.email`, `settings.nav` | yes | no |
| `users.*`, `activity.view` | yes | no |

Principle: an Admin can change anything a visitor sees, and nothing that could lock the
company out or break where enquiries are delivered. Every capability is checked on the
route handler, not just by hiding a menu item.

## Admin sidebar

```
Dashboard          what needs attention today
Inbox              all enquiries + notifications
                   All · Unassigned · Mine · Consultations · Viewings · Closed · Newsletter
Listings           For sale · To rent · Short stays · Locations · Archived
Page contents      Home · About · Virtual tours · Careers · Contact · Not found
Blogs              All posts · Categories · Drafts
Careers            vacancies
Services           the fifteen
Media              image library, alt text required
Settings           Company · Brand · Navigation · SEO · Email
Users              Superadmin only, plus activity log
```

Each public page is a list of named `page_sections` in the order they appear, so editors
change wording and images without touching layout.

### How a page section is edited

`src/content-schema.php` describes every band: its fields, their type, and the wording the
site shipped with. That one file is what the Page contents form draws and what the section
template reads, so the two can never drift apart.

`page_sections.data` holds only what an editor has actually changed. A field put back to
its original wording is dropped again, which means an unedited site renders from the
schema, the form always opens with the live copy in it rather than an empty box, and
improvements to the shipped wording still reach pages nobody has touched.

| Type | Field | Stored as |
|---|---|---|
| `line` | one-line text | string |
| `text` | paragraph | string |
| `rich` | paragraph keeping `<strong> <em> <a> <br>` | sanitised string |
| `lines` | one item per line | list of strings |
| `list` | repeating rows described by `item` | list of objects |
| `link` | URL or path | string |
| `image` | path under `public/` | string |
| `icon` | an icon name, inside `item` only | string |

Templates read their own section through `content()`, `content_html()`, `content_items()`
and `content_lines()`. `{fee}` in any field is replaced with the flat admin fee.

Bands marked `locked` in the schema are the ones a page cannot render without; the rest
carry a "Show this section" toggle that `section()` honours. Band order is the order in
the page template, not the `sort` column, so the form does not offer to reorder them.

Adding an editable field is one entry in the schema plus one `content()` call in the
template. Adding a whole band is a section template, a schema entry, and a row in
`page_sections`.

## Schema

Every content table carries `created_at`, `updated_at`, `updated_by`.

| Table | Key columns |
|---|---|
| `users` | name, email, password_hash, role, status, last_login_at |
| `login_attempts` | email, ip, attempted_at, succeeded |
| `password_resets` | user_id, token_hash, expires_at, used_at |
| `listings` | ref, basis, category, title, address, location_id, price, currency, period, status, beds, baths, area, summary, description, featured, published_at |
| `listing_images` | listing_id, media_id, sort, is_cover |
| `listing_features` | listing_id, label, sort |
| `locations` | name, city, slug, sort, featured |
| `pages` | slug, title, meta_title, meta_description, og_image_id |
| `page_sections` | page_id, key, type, sort, data (JSON), enabled |
| `posts` | slug, category_id, title, excerpt, body, cover_id, read_minutes, status, published_at |
| `post_categories` | name, slug, sort |
| `vacancies` | title, location, type, team, summary, requirements (JSON), status |
| `services` | title, note, body, icon, sort, featured |
| `media` | path, alt, width, height, bytes, mime, uploaded_by |
| `settings` | group, key, value, type |
| `enquiries` | type, name, email, phone, country, interest, budget, timeline, method, best_time, message, listing_id, source_url, status, assigned_to |
| `enquiry_notes` | enquiry_id, user_id, body, is_reply |
| `subscribers` | email, confirmed_at, unsubscribed_at |
| `activity_log` | user_id, action, entity, entity_id, before (JSON), after (JSON) |

## Migration map

The front end reads content through `data_set()`. Swap that one function for a repository
and every template keeps working.

| Today | Becomes | Edited from |
|---|---|---|
| `data/listings.php` | `listings` + `listing_images` + `listing_features` | Listings |
| `data/areas.php` | `locations` | Listings > Locations |
| `data/slideshow.php` | `page_sections` (home hero) | Page contents > Home |
| `data/about.php` | `page_sections` (about), **done** | Page contents > About |
| `data/services.php` | `services` | Services |
| `data/posts.php` | `posts` + `post_categories` | Blogs |
| `data/careers.php` | `vacancies` | Careers |
| `data/insights.php` | `posts` (featured flag) | Blogs |
| `config.php` | `settings` + nav + footer | Settings |

## Inbox

| Source | `type` | Carries |
|---|---|---|
| Contact form | `contact` | interest, location, budget, timeline, channel, best time |
| Book a Consultation | `consultation` | same, flagged as a consultation |
| Request a viewing (property page) | `viewing` | same plus the property reference |
| Book a virtual tour | `tour` | preferred format and timezone |
| Footer signup | `newsletter` | goes to `subscribers`, not the Inbox |

Status flow: `new -> assigned -> replied -> closed`, with internal notes the enquirer never
sees. On arrival: in-app notification plus email to a configurable recipient list. The one
working day promise the site makes depends on that email arriving, so use a real SMTP
service, not PHP `mail()`.

## Security checklist

- Passwords hashed with `password_hash()` / Argon2id. Never stored, logged or emailed.
- Session cookies `HttpOnly`, `Secure`, `SameSite=Lax`; id regenerated on login and
  privilege change; idle timeout.
- CSRF token per session on every state-changing form, verified server side.
- Login throttling: progressive delay then temporary lock, per email and per IP.
- Authorisation checked on the route handler. Hiding a menu item is not protection.
- Uploads: extension and MIME allowlist, size cap, images re-encoded to strip embedded
  content, stored where scripts cannot execute.
- PDO prepared statements throughout; no interpolation into SQL.
- The existing `e()` helper on every rendered value, including staff-entered content. The
  one exception is a `rich` page-section field, which keeps a fixed handful of inline tags:
  it is stripped of everything else on save, and an anchor keeps only an `href` that starts
  with `/`, `#`, `http://`, `https://`, `mailto:` or `tel:`.
- Public forms: honeypot, minimum time-to-submit, per-IP rate limit.
- HTTPS enforced, HSTS, admin excluded from search engines.
- Credentials in a file outside the web root, never committed.
- Nightly database dump plus uploads, retained off-server, restore tested once.

## Build order

1. **Foundations.** Migrations, accounts, login, capability map, admin shell, activity
   log. **Built.**
2. **Listings and media.** The listing editor and image library; front end switched to the
   database. **Built.**
3. **Inbox and live forms.** Wire contact, consultation, viewing and tour forms with
   validation, spam defences and notification. Nothing before this captures a lead.
4. **Page contents and settings.** Section editors **built**; company details, navigation,
   SEO still to do.
5. **Blogs, careers, services.** The last of the flat files retired.
6. **Refinements.** `/search`, `/saved`, enquiry export, 2FA, scheduled publishing.

## Decisions needed

| Question | Default if unanswered |
|---|---|
| ~~Shared cPanel hosting or a VPS?~~ | **Decided: cPanel + MySQL, plain PHP.** |
| ~~Reply from inside the admin, or notify only?~~ | **Decided: notify only.** Staff reply from their own email. |
| Does content need approving before publish? | No approval step; draft and publish only |
| How many staff accounts, who is Superadmin? | One Superadmin, Admins on request |
| Prices in USD, GHS, or both? | One currency per listing, shown as entered |

## Blocker to resolve before launch

Roughly 20 of the 34 Grace City photographs carry a visible **Royal Kingdom Estate**
watermark, and they currently appear on the home hero, listings, property pages and blog.
Either obtain unwatermarked originals under a listing agreement, or replace them with
DDREAM's own photography. See the asset provenance table in [README.md](README.md).
