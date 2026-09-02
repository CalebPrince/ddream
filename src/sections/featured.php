<?php
declare(strict_types=1);

// One inventory source for the whole site; the home page shows the flagged three.
$properties = search_listings(['featured' => true]);
?>
<section class="border-t border-hairline bg-surface py-16 lg:py-20" aria-labelledby="featured-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-5">
      <div class="max-w-2xl">
        <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
        <h2 id="featured-heading" class="t-h2 mt-3"><?= e(content('heading')) ?></h2>
        <p class="t-lead mt-3 text-muted"><?= e(content('lead')) ?></p>
      </div>
      <a href="<?= e(content('link_href')) ?>" class="btn btn-outline"><?= e(content('link_label')) ?> <?= icon('arrow-right', 'h-4 w-4') ?></a>
    </div>

    <ul class="mt-9 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($properties as $property): ?>
        <li class="reveal"><?php component('property-card', ['property' => $property]); ?></li>
      <?php endforeach; ?>
    </ul>

    <p class="prose-inline mt-7 flex flex-wrap items-center gap-2 t-meta text-muted">
      <?= icon('clock', 'h-4 w-4 text-gold-600') ?>
      <span><?= content_html('footnote') ?></span>
    </p>
  </div>
</section>
