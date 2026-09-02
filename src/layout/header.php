<?php
declare(strict_types=1);

/** @var string $currentPath */
$currentPath ??= '/';
$nav     = config('nav', []);
$contact = config('contact', []);
?>
<a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded-[4px] focus:bg-navy-700 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
  Skip to content
</a>

<!-- Main navigation -->
<header id="siteHeader" class="sticky top-0 z-50 border-b border-hairline bg-surface/95 backdrop-blur-md transition-shadow duration-300">
  <div class="shell flex h-[4.5rem] items-center justify-between gap-3 lg:h-20 xl:gap-4">

    <!-- The supplied logo is a tall stacked lockup, so on desktop it hangs out of the
         header on a plaque and collapses back into the bar once the page scrolls. -->
    <a href="/" class="brand relative flex shrink-0 items-center lg:w-[10.5rem] lg:self-stretch" aria-label="DDREAM home">
      <span class="brand-plaque">
        <img src="<?= e(asset('/images/brand/ddream-logo.png')) ?>"
             alt="DDREAM. Domestic, Diaspora Real Estate Management. No client commission."
             width="300" height="200" class="w-auto">
      </span>
    </a>

    <nav class="hidden min-w-0 flex-1 items-center justify-end lg:flex xl:justify-center" aria-label="Primary">
      <ul class="flex items-center">
        <?php foreach ($nav as $item): ?>
          <?php $active = $item['href'] === $currentPath; ?>
          <li class="nav-item relative">
            <a href="<?= e($item['href']) ?>"
               class="flex h-[4.5rem] items-center gap-1 whitespace-nowrap px-1.5 text-[0.6875rem] font-semibold uppercase tracking-[0.05em] transition-colors lg:h-20 xl:px-1.5 xl:text-[0.75rem] 2xl:px-2 2xl:text-[0.8125rem] <?= $active ? 'text-navy-700' : 'text-ink hover:text-navy-600' ?>"
               <?= $active ? 'aria-current="page"' : '' ?>>
              <?= e($item['label']) ?>
              <?php if (!empty($item['children'])): ?>
                <?= icon('chevron-down', 'h-3 w-3 text-muted xl:h-3.5 xl:w-3.5') ?>
              <?php endif; ?>
            </a>
            <?php if ($active): ?>
              <span class="absolute inset-x-2 bottom-0 h-0.5 bg-gold-500 xl:inset-x-3" aria-hidden="true"></span>
            <?php endif; ?>

            <?php if (!empty($item['children'])): ?>
              <div class="nav-panel absolute left-0 top-full w-[19rem] border border-hairline bg-surface p-2 shadow-panel rounded-[10px]">
                <?php foreach ($item['children'] as $child): ?>
                  <a href="<?= e($child['href']) ?>"
                     class="group flex items-start gap-3 rounded-[6px] px-3 py-2.5 transition-colors hover:bg-navy-100">
                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>
                    <span>
                      <span class="block text-[0.9375rem] font-semibold text-navy-700"><?= e($child['label']) ?></span>
                      <span class="block t-meta text-muted"><?= e($child['note']) ?></span>
                    </span>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="flex shrink-0 items-center gap-2 xl:gap-3">
      <a href="/saved" aria-label="Saved properties"
         class="hidden h-11 items-center gap-1.5 whitespace-nowrap rounded-[4px] px-2 text-[0.875rem] font-medium text-ink transition-colors hover:text-navy-600 lg:inline-flex">
        <?= icon('heart', 'h-[18px] w-[18px] text-gold-600') ?>
      </a>
      <a href="/admin" title="Staff sign in" aria-label="Staff sign in"
         class="hidden h-11 w-9 items-center justify-center rounded-[4px] text-muted transition-colors hover:text-navy-700 lg:inline-flex">
        <?= icon('lock', 'h-[18px] w-[18px]') ?>
      </a>
      <a href="/contact" class="btn btn-primary hidden whitespace-nowrap px-3 text-[0.8125rem] sm:inline-flex lg:hidden xl:inline-flex xl:px-5 xl:text-sm">
        Book a Consultation
      </a>
      <button type="button" id="navToggle"
              class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-hairline text-navy-700 lg:hidden"
              aria-expanded="false" aria-controls="mobileNav" aria-label="Open menu">
        <?= icon('menu', 'h-5 w-5') ?>
      </button>
    </div>
  </div>
</header>

<!-- Mobile drawer -->
<div id="mobileNav" class="fixed inset-0 z-[60] hidden lg:hidden" role="dialog" aria-modal="true" aria-label="Site menu">
  <div class="absolute inset-0 bg-navy-900/60 backdrop-blur-sm" data-nav-close></div>
  <div class="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col bg-surface shadow-2xl">
    <div class="flex h-[4.5rem] shrink-0 items-center justify-between border-b border-hairline px-5">
      <img src="<?= e(asset('/images/brand/ddream-logo.png')) ?>" alt="DDREAM" class="h-12 w-auto">
      <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-hairline text-navy-700"
              data-nav-close aria-label="Close menu"><?= icon('x') ?></button>
    </div>

    <nav class="flex-1 overflow-y-auto px-5 py-4" aria-label="Mobile">
      <ul class="divide-y divide-hairline">
        <?php foreach ($nav as $item): ?>
          <li class="py-1">
            <?php if (empty($item['children'])): ?>
              <a href="<?= e($item['href']) ?>" class="block py-3 text-sm font-semibold uppercase tracking-[0.08em] text-navy-700"><?= e($item['label']) ?></a>
            <?php else: ?>
              <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between py-3 text-sm font-semibold uppercase tracking-[0.08em] text-navy-700">
                  <?= e($item['label']) ?>
                  <?= icon('chevron-down', 'h-4 w-4 text-muted transition-transform group-open:rotate-180') ?>
                </summary>
                <ul class="pb-2 pl-1">
                  <?php foreach ($item['children'] as $child): ?>
                    <li><a href="<?= e($child['href']) ?>" class="block py-2 text-[0.9375rem] text-muted"><?= e($child['label']) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </details>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
        <li class="py-1">
          <a href="/saved" class="flex items-center gap-2 py-3 text-sm font-semibold uppercase tracking-[0.08em] text-navy-700">
            <?= icon('heart', 'h-[18px] w-[18px] text-gold-600') ?>Saved properties
          </a>
        </li>
      </ul>
    </nav>

    <div class="shrink-0 space-y-3 border-t border-hairline p-5">
      <a href="/contact" class="btn btn-primary w-full">Book a Consultation</a>
      <a href="tel:<?= e($contact['phone_href']) ?>" class="btn btn-outline w-full">
        <?= icon('phone', 'h-4 w-4') ?><?= e($contact['phone']) ?>
      </a>
      <a href="/admin" class="flex items-center justify-center gap-2 py-1 t-meta font-semibold text-muted transition-colors hover:text-navy-700">
        <?= icon('lock', 'h-4 w-4') ?>Staff sign in
      </a>
    </div>
  </div>
</div>
