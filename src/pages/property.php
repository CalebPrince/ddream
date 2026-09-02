<?php
declare(strict_types=1);

/** @var array $route */

$p       = $route['listing'];
$isLand  = $p['category'] === 'land';
$section = match ($p['basis']) {
    'To rent'    => ['label' => 'Rentals', 'href' => '/rentals'],
    'Short stay' => ['label' => 'Airbnb',  'href' => '/airbnb'],
    default      => ['label' => 'Selling', 'href' => '/selling'],
};
$contact = config('contact', []);

// Grace City listings have the full gallery behind them; everything else has the
// single supplied render, so the gallery falls back to one frame.
$gallery = str_contains($p['image'], 'gc-prime')
    ? array_map(
        static fn (array $slide): string => $slide['src'],
        array_slice(data_set('slideshow'), 0, 5)
    )
    : [$p['image']];
$gallery = array_values(array_unique(array_merge([$p['image']], $gallery)));
$gallery = array_slice($gallery, 0, 5);

$similar = array_values(array_filter(
    search_listings(['basis' => $p['basis'], 'category' => $p['category']]),
    static fn (array $item): bool => $item['id'] !== $p['id']
));
$similar = array_slice($similar, 0, 3);

$specs = array_values(array_filter([
    !empty($p['beds'])  ? ['icon' => 'bed',   'label' => 'Bedrooms',  'value' => (string) $p['beds']] : null,
    !empty($p['baths']) ? ['icon' => 'bath',  'label' => 'Bathrooms', 'value' => (string) $p['baths']] : null,
    ['icon' => $isLand ? 'landmark' : 'ruler',
     'label' => $isLand ? 'Plot size' : 'Internal area',
     'value' => number_format((int) $p['area']) . ' m²'],
    ['icon' => 'layers', 'label' => 'Status', 'value' => $p['status']],
]));
?>
<article>
  <!-- Gallery -->
  <section class="border-b border-hairline bg-navy-900" aria-label="Property photographs">
    <div class="grid gap-1 sm:grid-cols-4 sm:grid-rows-2">
      <div class="relative sm:col-span-3 sm:row-span-2">
        <img src="<?= e(asset($gallery[0])) ?>"
             alt="<?= e($p['title']) ?>, <?= e($p['address']) ?>"
             width="1920" height="1080" fetchpriority="high"
             class="h-64 w-full object-cover sm:h-[30rem] lg:h-[34rem]">
        <div class="absolute bottom-4 left-4 flex flex-wrap gap-1.5">
          <span class="badge bg-navy-700 text-white"><?= e($p['basis']) ?></span>
          <span class="badge bg-surface/95 text-navy-700 backdrop-blur-sm"><?= e($p['status']) ?></span>
        </div>
      </div>
      <?php foreach (array_slice($gallery, 1, 2) as $i => $src): ?>
        <div class="relative hidden sm:block">
          <img src="<?= e(asset($src)) ?>" alt="" width="960" height="720" loading="lazy"
               class="h-full w-full object-cover">
          <?php if ($i === 1 && count($gallery) > 3): ?>
            <a href="/virtual-tours"
               class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-navy-900/70 text-white backdrop-blur-[2px] transition-colors hover:bg-navy-900/80">
              <?= icon('play', 'h-6 w-6 text-gold-400') ?>
              <span class="text-[0.8125rem] font-semibold">Book a virtual tour</span>
            </a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="bg-canvas py-10 lg:py-14">
    <div class="shell">
      <nav aria-label="Breadcrumb" class="mb-7">
        <ol class="flex flex-wrap items-center gap-1.5 t-meta text-muted">
          <li><a href="/" class="transition-colors hover:text-navy-700">Home</a></li>
          <li aria-hidden="true" class="text-hairline"><?= icon('chevron-right', 'h-3.5 w-3.5') ?></li>
          <li><a href="<?= e($section['href']) ?>" class="transition-colors hover:text-navy-700"><?= e($section['label']) ?></a></li>
          <li aria-hidden="true" class="text-hairline"><?= icon('chevron-right', 'h-3.5 w-3.5') ?></li>
          <li><span class="font-medium text-navy-700" aria-current="page"><?= e($p['id']) ?></span></li>
        </ol>
      </nav>

      <div class="grid items-start gap-10 lg:grid-cols-12 lg:gap-12">

        <!-- Detail -->
        <div class="lg:col-span-8">
          <p class="tabular font-display text-4xl font-semibold text-navy-700">
            <?= e(money($p['price'], $p['currency'])) ?><?php if (!empty($p['period'])): ?><span class="font-sans text-base font-normal text-muted"> <?= e($p['period']) ?></span><?php endif; ?>
          </p>
          <h1 class="t-h2 mt-2"><?= e($p['title']) ?></h1>
          <p class="mt-2 flex items-center gap-2 text-[1rem] text-muted">
            <?= icon('map-pin', 'h-[18px] w-[18px] shrink-0 text-gold-600') ?><?= e($p['address']) ?>
          </p>

          <dl class="mt-8 grid gap-px overflow-hidden rounded-[10px] border border-hairline bg-hairline sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($specs as $spec): ?>
              <div class="bg-surface p-5">
                <dt class="flex items-center gap-2 t-meta text-muted">
                  <?= icon($spec['icon'], 'h-4 w-4 text-gold-600') ?><?= e($spec['label']) ?>
                </dt>
                <dd class="tabular mt-1.5 font-display text-xl font-semibold text-navy-700"><?= e($spec['value']) ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>

          <div class="mt-10">
            <h2 class="t-h3">About this property</h2>
            <p class="t-lead mt-3 text-muted"><?= e($p['summary']) ?></p>
            <p class="mt-4 text-[0.9375rem] leading-relaxed text-muted">
              Every property we list is inspected in person by our own team before it
              reaches this page, and the paperwork is checked at the Lands Commission.
              If you are abroad, we will film a walkthrough and talk you round it on a
              video call at a time that suits your timezone.
            </p>
          </div>

          <div class="mt-9">
            <h2 class="t-h3">Features</h2>
            <ul class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
              <?php foreach ($p['tags'] as $tag): ?>
                <li class="flex items-center gap-2.5 text-[0.9375rem] text-navy-700">
                  <?= icon('check', 'h-4 w-4 shrink-0 text-verified') ?><?= e($tag) ?>
                </li>
              <?php endforeach; ?>
              <li class="flex items-center gap-2.5 text-[0.9375rem] text-navy-700">
                <?= icon('check', 'h-4 w-4 shrink-0 text-verified') ?>Inspected by DDREAM
              </li>
              <li class="flex items-center gap-2.5 text-[0.9375rem] text-navy-700">
                <?= icon('check', 'h-4 w-4 shrink-0 text-verified') ?>Documentation checked
              </li>
            </ul>
          </div>

          <div class="mt-9 rounded-[10px] border border-gold-200 bg-gold-100/60 p-6">
            <p class="flex items-baseline gap-2 font-display text-2xl font-semibold leading-none">
              <span class="text-signal-600">No</span><span class="text-navy-700">Client Commission</span>
            </p>
            <p class="mt-3 text-[0.9375rem] leading-relaxed text-muted">
              The commission on this property falls on the
              <?= $p['basis'] === 'For sale' ? 'seller' : 'landlord' ?>, never on you. You
              pay a single flat <span class="tabular font-semibold text-navy-700"><?= e(config('admin_fee')) ?></span>
              administrative fee, and nothing else.
            </p>
          </div>
        </div>

        <!-- Enquiry -->
        <aside class="lg:col-span-4 lg:sticky lg:top-28 lg:self-start">
          <div class="card p-6">
            <p class="t-meta font-semibold uppercase tracking-[0.14em] text-gold-600">
              Reference <?= e($p['id']) ?>
            </p>
            <h2 class="t-h3 mt-3">Arrange a viewing</h2>
            <p class="mt-2 text-[0.9375rem] leading-relaxed text-muted">
              In person if you are in Ghana, on a filmed walkthrough if you are not.
            </p>

            <div class="mt-5 space-y-3">
              <a href="/contact?ref=<?= e($p['id']) ?>" class="btn btn-primary w-full">
                <?= icon('calendar', 'h-4 w-4') ?>Request a viewing
              </a>
              <a href="tel:<?= e($contact['phone_href']) ?>" class="btn btn-outline w-full">
                <?= icon('phone', 'h-4 w-4') ?><span class="tabular"><?= e($contact['phone']) ?></span>
              </a>
              <a href="/virtual-tours" class="btn btn-outline w-full">
                <?= icon('play', 'h-4 w-4') ?>Book a virtual tour
              </a>
            </div>

            <ul class="mt-6 space-y-2.5 border-t border-hairline pt-5">
              <?php
              $assurances = [
                  'A named adviser, not a call centre',
                  'Due diligence before any payment',
                  'Calls scheduled in your timezone',
              ];
              foreach ($assurances as $item): ?>
                <li class="flex items-start gap-2.5 t-meta text-muted">
                  <?= icon('badge-check', 'h-4 w-4 shrink-0 translate-y-0.5 text-verified') ?><?= e($item) ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <p class="mt-4 t-meta text-muted">
            <?= e(added_label((int) $p['added'])) ?>. Reference <?= e($p['id']) ?> when you
            get in touch.
          </p>
        </aside>
      </div>
    </div>
  </section>

  <?php if ($similar): ?>
    <section class="border-t border-hairline bg-surface py-14 lg:py-16" aria-labelledby="similar-heading">
      <div class="shell">
        <div class="flex flex-wrap items-end justify-between gap-4">
          <h2 id="similar-heading" class="t-h2">Similar properties</h2>
          <a href="<?= e($section['href']) ?>" class="btn btn-outline">
            All <?= e(strtolower($section['label'])) ?> <?= icon('arrow-right', 'h-4 w-4') ?>
          </a>
        </div>
        <ul class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <?php foreach ($similar as $item): ?>
            <li class="reveal"><?php component('property-card', ['property' => $item]); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
  <?php endif; ?>
</article>
