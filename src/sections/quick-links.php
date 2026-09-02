<?php
declare(strict_types=1);

$tiles = [
    ['icon' => 'key',      'title' => 'Buy a property',    'body' => 'Houses, apartments, commercial space and land across Ghana.',       'href' => '/selling'],
    ['icon' => 'building', 'title' => 'Rent or let',       'body' => 'Furnished and unfurnished homes, offices and retail units.',         'href' => '/rentals'],
    ['icon' => 'calendar', 'title' => 'Airbnb short stays','body' => 'Nightly and monthly whole-home stays, fully managed.',               'href' => '/airbnb'],
    ['icon' => 'camera',   'title' => 'Virtual tours',     'body' => 'Walk a property from anywhere before you fly in.',                   'href' => '/virtual-tours'],
    ['icon' => 'hard-hat', 'title' => 'Build and supervise','body' => 'Weekly construction reports while you are overseas.',               'href' => '/about#service-supervision'],
    ['icon' => 'chart',    'title' => 'Value my property', 'body' => 'Coordinated valuation and investment advisory.',                     'href' => '/about#service-valuation'],
];
?>
<section class="bg-canvas py-14 lg:py-16" aria-labelledby="quicklinks-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <div>
        <p class="eyebrow">Where would you like to start?</p>
        <h2 id="quicklinks-heading" class="t-h2 mt-3">Six ways we work with you</h2>
      </div>
      <a href="/about#services" class="inline-flex items-center gap-2 text-[0.9375rem] font-semibold text-navy-700 transition-colors hover:text-gold-600">
        All fifteen services <?= icon('arrow-right', 'h-4 w-4') ?>
      </a>
    </div>

    <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($tiles as $tile): ?>
        <li class="reveal">
          <a href="<?= e($tile['href']) ?>"
             class="card card-interactive group flex h-full items-start gap-4 p-5">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-[4px] bg-navy-700 text-gold-400 transition-colors group-hover:bg-navy-600">
              <?= icon($tile['icon'], 'h-5 w-5') ?>
            </span>
            <span class="min-w-0 flex-1">
              <span class="flex items-center gap-1.5 font-sans text-[1.0625rem] font-semibold text-navy-700">
                <?= e($tile['title']) ?>
                <?= icon('arrow-up-right', 'h-4 w-4 text-gold-600 opacity-0 transition-opacity group-hover:opacity-100') ?>
              </span>
              <span class="mt-1 block t-meta text-muted"><?= e($tile['body']) ?></span>
            </span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
