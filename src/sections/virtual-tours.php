<?php
declare(strict_types=1);

/**
 * The Virtual tours page body: how it works, the three formats, and the
 * properties ready to walk through. Wording lives in Page contents.
 */

$contact  = config('contact', []);
$steps    = content_items('steps');
$formats  = content_items('formats');

// Any listing can be toured; these are the ones with footage already on file.
$featured = array_slice(search_listings(['sort' => 'newest']), 0, 6);
?>
<!-- How it works -->
<section class="bg-surface py-16 lg:py-20" aria-labelledby="how-heading">
  <div class="shell">
    <div class="max-w-2xl">
      <p class="eyebrow"><?= e(content('steps_eyebrow')) ?></p>
      <h2 id="how-heading" class="t-h2 mt-4"><?= e(content('steps_heading')) ?></h2>
    </div>

    <ol class="mt-10 grid gap-px overflow-hidden rounded-[10px] border border-hairline bg-hairline sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($steps as $i => $step): ?>
        <li class="reveal bg-surface p-7">
          <span class="tabular inline-flex h-9 w-9 items-center justify-center rounded-full border border-gold-500/50 font-display text-[0.9375rem] font-semibold text-gold-600">
            <?= $i + 1 ?>
          </span>
          <h3 class="t-h3 mt-5 text-[1.0625rem] leading-snug"><?= e($step['title']) ?></h3>
          <p class="mt-2.5 text-[0.9375rem] leading-relaxed text-muted"><?= e($step['body']) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- Formats -->
<section class="relative overflow-hidden bg-navy-800 py-16 text-navy-200 lg:py-20" aria-labelledby="formats-heading">
  <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.06]" aria-hidden="true"></div>
  <div class="shell relative">
    <div class="max-w-2xl">
      <p class="eyebrow eyebrow-light"><?= e(content('formats_eyebrow')) ?></p>
      <h2 id="formats-heading" class="t-h2 mt-4 text-white"><?= e(content('formats_heading')) ?></h2>
    </div>

    <ul class="mt-10 grid gap-6 lg:grid-cols-3">
      <?php foreach ($formats as $format): ?>
        <li class="reveal rounded-[10px] border border-hairline-dark bg-navy-900/60 p-7">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-gold-500/40 text-gold-400">
            <?= icon($format['icon'], 'h-5 w-5') ?>
          </span>
          <h3 class="t-h3 mt-5 text-white"><?= e($format['title']) ?></h3>
          <p class="mt-1.5 t-meta font-semibold uppercase tracking-[0.12em] text-gold-400"><?= e($format['meta']) ?></p>
          <p class="mt-3 text-[0.9375rem] leading-relaxed text-navy-200/80"><?= e($format['body']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-10 flex flex-wrap gap-3">
      <a href="<?= e(content('primary_href')) ?>" class="btn btn-accent"><?= e(content('primary_label')) ?></a>
      <a href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string) $contact['whatsapp'])) ?>"
         target="_blank" rel="noopener noreferrer" class="btn btn-outline-light">
        <?= icon('whatsapp', 'h-4 w-4') ?><?= e(content('whatsapp_label')) ?>
      </a>
    </div>
  </div>
</section>

<!-- Available to tour -->
<section class="bg-canvas py-16 lg:py-20" aria-labelledby="tourable-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <div class="max-w-2xl">
        <p class="eyebrow"><?= e(content('listings_eyebrow')) ?></p>
        <h2 id="tourable-heading" class="t-h2 mt-3"><?= e(content('listings_heading')) ?></h2>
        <p class="t-lead mt-3 text-muted"><?= e(content('listings_lead')) ?></p>
      </div>
      <a href="<?= e(content('listings_link_href')) ?>" class="btn btn-outline"><?= e(content('listings_link_label')) ?> <?= icon('arrow-right', 'h-4 w-4') ?></a>
    </div>

    <ul class="mt-9 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($featured as $property): ?>
        <li class="reveal"><?php component('property-card', ['property' => $property]); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
