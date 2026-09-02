<?php
declare(strict_types=1);

/**
 * "Home Page Left Side" from the client outline: the kente photograph with the
 * strapline beneath it on the left, and the trust cards on the right.
 */

$points = content_items('points');
?>
<section class="border-b border-hairline bg-surface py-14 lg:py-20" aria-labelledby="curated-heading">
  <div class="shell grid items-start gap-10 lg:grid-cols-12 lg:gap-14">

    <!-- Left: the picture, strapline underneath -->
    <div class="lg:col-span-5">
      <figure class="reveal">
        <div class="overflow-hidden rounded-[10px] border border-hairline bg-navy-900">
          <img src="<?= e(asset(content('image'))) ?>"
               alt="<?= e(content('image_alt')) ?>"
               width="331" height="220" loading="lazy"
               class="aspect-[3/2] w-full object-cover">
        </div>

        <figcaption class="mt-6">
          <h2 id="curated-heading" class="t-h2 prose-inline text-[1.75rem] leading-tight lg:text-[2rem]">
            <?= content_html('heading') ?>
          </h2>
          <p class="mt-3 flex flex-wrap items-center gap-x-2 font-display text-lg font-semibold text-navy-700">
            <?= e(content('strapline')) ?>
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
        <span><?= e(content('footnote')) ?></span>
      </p>
    </div>
  </div>
</section>
