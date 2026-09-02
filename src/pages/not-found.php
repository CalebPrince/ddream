<?php
declare(strict_types=1);

$links = [
    ['label' => 'Browse properties for sale', 'href' => '/selling',  'icon' => 'key'],
    ['label' => 'Browse properties to rent',  'href' => '/rentals',  'icon' => 'building'],
    ['label' => 'About DDREAM',               'href' => '/about',    'icon' => 'users'],
    ['label' => 'Talk to an adviser',         'href' => '/contact',  'icon' => 'phone'],
];
?>
<section class="bg-canvas">
  <div class="shell max-w-2xl py-20 lg:py-28">
    <p class="eyebrow">Error 404</p>
    <h1 class="t-h1 mt-4">We cannot find that page</h1>
    <p class="t-lead mt-4 text-muted">
      The link may be out of date, or the page may not be built yet. Here is where most
      people go next.
    </p>

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
