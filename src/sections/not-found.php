<?php
declare(strict_types=1);

/** The 404 page body. Wording lives in Page contents > Not found. */

$links = content_items('links');
?>
<section class="bg-canvas">
  <div class="shell max-w-2xl py-20 lg:py-28">
    <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
    <h1 class="t-h1 mt-4"><?= e(content('heading')) ?></h1>
    <p class="t-lead mt-4 text-muted"><?= e(content('lead')) ?></p>

    <ul class="mt-9 grid gap-3 sm:grid-cols-2">
      <?php foreach ($links as $link): ?>
        <li>
          <a href="<?= e($link['href']) ?>" class="card card-interactive group flex items-center gap-3 px-5 py-4">
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-[4px] bg-navy-700 text-gold-400">
              <?= icon($link['icon'], 'h-[18px] w-[18px]') ?>
            </span>
            <span class="font-sans text-[0.9375rem] font-semibold text-navy-700"><?= e($link['label']) ?></span>
            <?= icon('arrow-right', 'ml-auto h-4 w-4 text-muted transition-transform group-hover:translate-x-0.5') ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
