<?php
declare(strict_types=1);

/**
 * "Home Page Left Side" from the client outline: the kente photograph with the
 * strapline beneath it on the left, and the trust cards on the right.
 */

$points = [
    ['icon' => 'shield-check', 'title' => 'Registered company',  'body' => 'A limited liability company filed with the Registrar of Companies, working from an office you can walk into.'],
    ['icon' => 'users',        'title' => 'No middlemen',         'body' => 'We hold the relationships with landlords, developers and vendors ourselves.'],
    ['icon' => 'globe',        'title' => 'Local and global',     'body' => 'Staff born and bred in Ghana, with several years of working experience abroad.'],
    ['icon' => 'file-check',   'title' => 'Due diligence first',  'body' => 'Title searches and litigation checks before any offer is made on your behalf.'],
];
?>
<section class="border-b border-hairline bg-surface py-14 lg:py-20" aria-labelledby="curated-heading">
  <div class="shell grid items-start gap-10 lg:grid-cols-12 lg:gap-14">

    <!-- Left: the picture, strapline underneath -->
    <div class="lg:col-span-5">
      <figure class="reveal">
        <div class="overflow-hidden rounded-[10px] border border-hairline bg-navy-900">
          <img src="<?= e(asset('/images/kente-cloth.jpg')) ?>"
               alt="Rolls of woven kente cloth in green, gold, red and black"
               width="331" height="220" loading="lazy"
               class="aspect-[3/2] w-full object-cover">
        </div>

        <figcaption class="mt-6">
          <h2 id="curated-heading" class="t-h2 text-[1.75rem] leading-tight lg:text-[2rem]">
            Our Building Blocks are<br class="hidden sm:block"> Culturally Curated.
          </h2>
          <p class="mt-3 flex flex-wrap items-center gap-x-2 font-display text-lg font-semibold text-navy-700">
            One Client at a time.
            <span class="inline-flex items-baseline gap-1.5">
              <span class="text-signal-600">No</span>
              <span>Client Commission</span>
            </span>
          </p>
          <span class="mt-5 block h-px w-16 bg-gold-500" aria-hidden="true"></span>
        </figcaption>
      </figure>
    </div>

    <!-- Right: the cards -->
    <div class="lg:col-span-7">
      <ul class="grid gap-4 sm:grid-cols-2">
        <?php foreach ($points as $point): ?>
          <li class="reveal card flex h-full flex-col p-6">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-gold-200 bg-gold-100 text-gold-600">
              <?= icon($point['icon'], 'h-5 w-5') ?>
            </span>
            <h3 class="t-h3 mt-4 text-[1.0625rem] leading-snug"><?= e($point['title']) ?></h3>
            <p class="mt-2 text-[0.9375rem] leading-relaxed text-muted"><?= e($point['body']) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>

      <p class="mt-5 flex items-start gap-2 t-meta text-muted">
        <?= icon('shield-check', 'h-4 w-4 shrink-0 translate-y-0.5 text-verified') ?>
        The commission falls on the seller or landlord, never on you. You pay a flat
        <span class="tabular font-semibold text-navy-700"><?= e(config('admin_fee')) ?></span>
        administrative fee and nothing else.
      </p>
    </div>
  </div>
</section>
