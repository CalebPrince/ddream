# DDREAM Design Specification

**Domestic, Diaspora Real Estate Management Ltd.**
Version 1.0 · Landing page (Home) · PHP 8 + Tailwind CSS v4

---

## 1. Strategy

| | |
|---|---|
| **Business model** | Ghanaian real estate agency + property management, operating on a **No Client Commission** basis (commission is paid by seller/landlord; client pays a flat GHS 1,000 admin fee). |
| **Primary audience** | Ghanaians in the diaspora (London, Amsterdam, Berlin, New York, Toronto, Johannesburg) buying, building or managing property back home, plus the diplomatic community and domestic buyers in Accra/Kumasi. |
| **Primary conversion** | **Property search** (Buy / Rent / Airbnb). The portal action, above the fold. |
| **Secondary conversions** | "Book a consultation", "Request a virtual tour", diaspora enquiry form, newsletter. |
| **Core objection to defeat** | *"Can I trust anyone with my money from 5,000 miles away?"* Every section must answer with transparency, registration proof, physical office, no middlemen. |

**Visitor journey (home page order)**

Hero + search → Trust strip → Quick service tiles → Featured properties → Diaspora
programme (the differentiator) → What sets us apart / No Client Commission → Services grid
→ Areas we cover → Insights & guides → Final CTA → Fat footer.

---

## 2. Brand personality

Institutional, but warm. This is not a startup. It is a *registered limited liability
company with an identifiable office*. The visual tone should read closer to a private bank
or a chartered surveyor than to a proptech SaaS: confident serif headlines, disciplined
navy, gold used as a hairline rather than a fill, and photography of real buildings at
dusk.

**Three words:** Trusted · Continental · Considered.

---

## 3. Colour system

Derived directly from the DDREAM logo (navy, gold, signal red).

| Token | Hex | Use |
|---|---|---|
| `--color-navy-900` | `#04101F` | Footer ground, deepest bands |
| `--color-navy-800` | `#071B33` | Dark section ground |
| `--color-navy-700` | `#0A2240` | **Primary.** Header, buttons, headings |
| `--color-navy-600` | `#14355F` | Hover state on primary |
| `--color-navy-100` | `#E8EDF4` | Tinted surfaces, active nav |
| `--color-gold-500` | `#C8A046` | **Accent.** Rules, icons, price emphasis |
| `--color-gold-400` | `#E3C574` | Accent on dark grounds |
| `--color-gold-100` | `#F4EBD6` | Badge fills, gold wash |
| `--color-signal-600` | `#C8102E` | *Reserved.* "No Client Commission" mark only |
| `--color-canvas` | `#FBF8F3` | Page background (warm ivory, never pure white) |
| `--color-surface` | `#FFFFFF` | Cards, search panel |
| `--color-ink` | `#101418` | Body text |
| `--color-muted` | `#5B6672` | Secondary text, meta |
| `--color-hairline` | `#E4DDD1` | 1px borders (warm, not grey) |
| `--color-verified` | `#1F7A55` | "Verified listing" / success |

**Rules**
- Red appears **once per viewport at most**, and only on the No-Client-Commission mark. It
  is a legal/commercial claim, not decoration.
- Gold is a *line* colour and a *small-fill* colour. Never a large flood, never a gradient.
- Backgrounds alternate `canvas` → `surface` → `navy-800` so sections separate without
  shadows.

---

## 4. Typography

| Role | Face | Notes |
|---|---|---|
| Display / headings | **Fraunces** (variable, `opsz` 9–144, `SOFT` 0, `WONK` 0) | Weight 600. Editorial serif that echoes the logo's engraved wordmark without being Playfair. |
| Body / UI | **Public Sans** | 400 / 500 / 600. Legible at small sizes, neutral, government-grade, and deliberately *not* Inter. |
| Numerals | Public Sans, `font-variant-numeric: tabular-nums` | Prices, bed counts, stats. |

**Scale** (clamped, fluid)

```
display   clamp(2.75rem, 1.6rem + 3.6vw, 3.5rem)   /  1.04  / -0.025em
h1        clamp(2.25rem, 1.5rem + 2.4vw, 3.25rem)  /  1.08  / -0.02em
h2        clamp(1.75rem, 1.3rem + 1.5vw, 2.5rem)   /  1.15  / -0.015em
h3        1.375rem                                  /  1.25  / -0.01em
lead      1.0625rem–1.125rem                        /  1.65
body      0.9375rem                                 /  1.7
meta      0.8125rem                                 /  1.5   /  0.01em
eyebrow   0.75rem, 600, uppercase                   /  1     /  0.16em
```

---

## 5. Spacing, grid, radius

- Container: `max-width: 1240px`, gutter `1.25rem` mobile → `2rem` ≥1024px.
- Section rhythm: `py-16` mobile, `py-24` desktop. Dark bands get `py-20 / py-28`.
- Grid: 12 columns desktop, 6 tablet, 4 mobile. Property cards 3-up ≥1024px, 2-up ≥640px.
- **Radius is intentionally small.** This is a portal, not a toy:
  `2px` tags · `4px` inputs & buttons · `10px` cards & panels · `999px` pills only for filter chips.
- **Elevation:** 1px `hairline` borders do the work. One shadow token only,
  `0 1px 2px rgb(4 16 31 / .04), 0 8px 24px -12px rgb(4 16 31 / .12)`, reserved for the
  floating search panel and card hover.

---

## 6. Motifs

1. **The crescent rule.** The logo's gold crescent is abstracted into a 1px gold hairline
   with a 24px gold segment at its left end, sitting under every section eyebrow.
2. **The lattice.** The perforated mashrabiya screen on the DDREAM tower render is
   redrawn as a repeating SVG (`.motif-lattice`) and laid at 4–6% opacity over navy bands.
   This is the visual form of *"our building blocks are culturally curated."*

---

## 7. Components

| Component | Rules |
|---|---|
| **Header** | Single white sticky bar: brand plaque, all 9 nav items always visible from 1024px, dropdowns for About / Selling / Rentals / Airbnb, Saved link, and a navy "Book a Consultation" CTA. Below 1024px the nav collapses to a full-screen drawer. |
| **Brand plaque** | The supplied logo is a tall stacked lockup, so it sits on a white plaque anchored to the top of the bar and hangs ~30px into the hero (rounded bottom corners, soft shadow, no border). On scroll it collapses back inside the bar. |
| **Search panel** | Rightmove-pattern: segmented tabs (Buy · Rent · Airbnb · Land), large location input with datalist, property type + min/max price + bedrooms selects, full-width gold-on-navy submit. Sits half-overlapping the hero image on desktop. |
| **Property card** | 4:3 image, status ribbon top-left, save-heart top-right, price in Fraunces, address, bed/bath/area icon row, agent/estate line, hairline border, hover: border → navy, image scale 1.03, shadow token. |
| **Buttons** | Primary `bg-navy-700 text-white` · Accent `bg-gold-500 text-navy-900` · Outline `border-navy-700 text-navy-700` · Ghost. All `rounded-[4px]`, `h-11`, `font-medium`, 200ms ease-out. |
| **Badges** | `rounded-[2px]`, uppercase, 11px, `tracking-[0.08em]`. |
| **Forms** | 44px min target, 1px hairline, focus: 2px navy ring at 2px offset. |

## 8. Icons & assets

- **Icons:** Lucide, inlined as SVG through the `icon()` helper. 1.5px stroke, `currentColor`,
  20px default. No emoji anywhere in production UI.
- **Logo:** client-supplied PNG (`public/images/brand/ddream-logo.png`), never redrawn or
  recoloured. A mono navy mark is used in the footer at reduced opacity.
- **Photography:** client-supplied dusk architectural renders + the DDREAM front-desk
  photograph. Direction for future assets: dusk/blue-hour, warm interior light, real Accra
  streetscapes, generous negative space top-left for overlay text.

## 9. Copy and tone

- Plain, declarative sentences. State the fact, then stop.
- **No em dashes anywhere in page copy.** Use a full stop, a comma, a colon or a
  parenthesis instead. This applies to headings, body copy, alt text, meta titles,
  datasets in `src/data/` and code comments.
- En dashes are fine for genuine ranges (`Mon–Fri`, `08:30–17:30`, `1–2 year leases`).
- British spelling. Prices in USD for sales, GHS for the admin fee.
- Sentence case everywhere except the CTA "Book a Consultation".

## 10. Motion

- Entrance: `opacity 0 → 1`, `translateY 12px → 0`, `600ms cubic-bezier(.16,1,.3,1)`,
  IntersectionObserver, `once`, 60ms stagger within a group.
- Hover: `200ms ease-out` on colour/border/transform. Image zoom `500ms`.
- All of the above wrapped in `@media (prefers-reduced-motion: reduce)` no-op.

## 11. Accessibility

- Body text ≥ 4.5:1 (`ink` on `canvas` = 15.8:1; `muted` on `canvas` = 5.9:1).
- Gold `#C8A046` is **never** used for text on white. Only on navy grounds or as a rule.
- Visible focus ring on every interactive element; skip-link to `#main`.
- All icon-only controls carry `aria-label`; the save-heart is a real `<button>` with
  `aria-pressed`.
- Landmarks: `header` / `nav` / `main` / `footer`, one `h1`, no heading level skipped.
