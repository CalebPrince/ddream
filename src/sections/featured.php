<?php
declare(strict_types=1);

// One inventory source for the whole site; the home page shows the flagged three.
$properties = search_listings(['featured' => true]);
?>
<section class="border-t border-hairline bg-surface py-16 lg:py-20" aria-labelledby="featured-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-5">
      <div class="max-w-2xl">
        <p class="eyebrow">Currently on our books</p>
        <h2 id="featured-heading" class="t-h2 mt-3">Featured properties</h2>
        <p class="t-lead mt-3 text-muted">
          A small, deliberately curated list. Every property below has been inspected by
          our team and title-checked at the Lands Commission.
        </p>
      </div>
      <a href="/selling" class="btn btn-outline">View all listings <?= icon('arrow-right', 'h-4 w-4') ?></a>
    </div>

    <ul class="mt-9 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($properties as $property): ?>
        <li class="reveal"><?php component('property-card', ['property' => $property]); ?></li>
      <?php endforeach; ?>
    </ul>

    <p class="mt-7 flex flex-wrap items-center gap-2 t-meta text-muted">
      <?= icon('clock', 'h-4 w-4 text-gold-600') ?>
      Looking for something specific?
      <a href="/contact" class="font-semibold text-navy-700 underline underline-offset-4 hover:text-gold-600">
        Send us your brief
      </a>
      and we will source it. We find off-market properties for clients every week.
    </p>
  </div>
</section>
