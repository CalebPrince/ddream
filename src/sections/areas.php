<?php
declare(strict_types=1);

$areas = data_set('areas')['featured'];
?>
<section class="bg-canvas py-16 lg:py-20" aria-labelledby="areas-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <div>
        <p class="eyebrow">Where we operate</p>
        <h2 id="areas-heading" class="t-h2 mt-3">Search by area</h2>
      </div>
      <a href="/areas" class="inline-flex items-center gap-2 text-[0.9375rem] font-semibold text-navy-700 transition-colors hover:text-gold-600">
        Browse every location <?= icon('arrow-right', 'h-4 w-4') ?>
      </a>
    </div>

    <ul class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($areas as $area): ?>
        <li class="reveal">
          <a href="/search?location=<?= urlencode($area['name']) ?>"
             class="card card-interactive group flex items-center justify-between gap-3 px-5 py-4">
            <span>
              <span class="block font-sans text-[1rem] font-semibold text-navy-700"><?= e($area['name']) ?></span>
              <span class="block t-meta text-muted"><?= e($area['city']) ?></span>
            </span>
            <span class="flex shrink-0 items-center gap-2">
              <span class="tabular t-meta font-semibold text-gold-600"><?= (int) $area['count'] ?></span>
              <?= icon('chevron-right', 'h-4 w-4 text-muted transition-transform group-hover:translate-x-0.5') ?>
            </span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
