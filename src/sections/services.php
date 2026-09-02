<?php
declare(strict_types=1);

/** Home page teaser. The full list lives on the About page at /about#services. */

$services = data_set('services');
$total    = count($services['primary']) + count($services['secondary']);
?>
<section class="border-y border-hairline bg-surface py-16 lg:py-24" aria-labelledby="services-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-5">
      <div class="max-w-2xl">
        <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
        <h2 id="services-heading" class="t-h2 mt-3"><?= e(content('heading')) ?></h2>
        <p class="t-lead mt-3 text-muted"><?= e(content('lead')) ?></p>
      </div>
      <a href="<?= e(content('link_href')) ?>" class="btn btn-outline">
        All <?= $total ?> services <?= icon('arrow-right', 'h-4 w-4') ?>
      </a>
    </div>

    <ul class="mt-10 grid gap-px overflow-hidden rounded-[10px] border border-hairline bg-hairline sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($services['primary'] as $service): ?>
        <li class="reveal group bg-surface transition-colors hover:bg-canvas">
          <a href="/about#service-<?= e($service['slug']) ?>" class="flex h-full flex-col p-7">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700 transition-colors group-hover:bg-navy-700 group-hover:text-gold-400">
              <?= icon($service['icon'], 'h-5 w-5') ?>
            </span>
            <h3 class="t-h3 mt-5 text-[1.125rem] leading-snug"><?= e($service['title']) ?></h3>
            <p class="mt-2.5 flex-1 text-[0.9375rem] leading-relaxed text-muted"><?= e($service['body']) ?></p>
            <span class="mt-5 inline-flex items-center gap-2 text-[0.875rem] font-semibold text-navy-700 transition-colors group-hover:text-gold-600">
              Learn more <?= icon('arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-1') ?>
            </span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-8 rounded-[10px] border border-hairline bg-canvas p-6 sm:p-7">
      <h3 class="t-meta font-semibold uppercase tracking-[0.14em] text-muted"><?= e(content('secondary_heading')) ?></h3>
      <ul class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($services['secondary'] as $item): ?>
          <li>
            <a href="/about#service-<?= e($item['slug']) ?>"
               class="flex items-center gap-2.5 text-[0.9375rem] text-navy-700 transition-colors hover:text-gold-600">
              <?= icon($item['icon'], 'h-[18px] w-[18px] shrink-0 text-gold-600') ?><?= e($item['title']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>
