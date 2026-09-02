<?php
declare(strict_types=1);

/**
 * Compact interior-page hero. Sits under the hanging brand plaque, so it carries
 * the same desktop top padding as the home hero.
 *
 * @var string      $eyebrow
 * @var string      $heading
 * @var string      $lead
 * @var array       $crumbs   [['label' =>, 'href' => null], ...]
 * @var string|null $image
 * @var string|null $imageAlt
 * @var array       $facts    optional [['value' =>, 'label' =>], ...]
 */

$image    ??= null;
$imageAlt ??= '';
$facts    ??= [];
?>
<section class="relative overflow-hidden border-b border-hairline bg-canvas">
  <?php if ($image): ?>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-[40%]">
      <div class="relative h-56 sm:h-72 lg:h-full">
        <img src="<?= e(asset($image)) ?>" alt="<?= e($imageAlt) ?>"
             width="1440" height="1080" fetchpriority="high"
             class="h-full w-full object-cover">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-900/35 to-transparent lg:hidden" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-y-0 left-0 hidden w-20 bg-gradient-to-r from-canvas to-transparent lg:block" aria-hidden="true"></div>
      </div>
    </div>
  <?php endif; ?>

  <div class="shell relative">
    <div class="<?= $image ? 'lg:w-[60%] lg:pr-12' : 'max-w-3xl' ?>">
      <div class="py-10 lg:pb-16 lg:pt-[5.5rem]">

        <?php if (!empty($crumbs)): ?>
          <nav aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-1.5 t-meta text-muted">
              <li><a href="/" class="transition-colors hover:text-navy-700">Home</a></li>
              <?php foreach ($crumbs as $crumb): ?>
                <li aria-hidden="true" class="text-hairline"><?= icon('chevron-right', 'h-3.5 w-3.5') ?></li>
                <li>
                  <?php if (!empty($crumb['href'])): ?>
                    <a href="<?= e($crumb['href']) ?>" class="transition-colors hover:text-navy-700"><?= e($crumb['label']) ?></a>
                  <?php else: ?>
                    <span class="font-medium text-navy-700" aria-current="page"><?= e($crumb['label']) ?></span>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ol>
          </nav>
        <?php endif; ?>

        <p class="eyebrow mt-6"><?= e($eyebrow) ?></p>
        <h1 class="t-h1 prose-inline mt-4 font-display"><?= $heading ?></h1>
        <p class="t-lead mt-5 max-w-xl text-muted"><?= e($lead) ?></p>

        <?php if ($facts): ?>
          <dl class="mt-8 grid max-w-lg grid-cols-3 divide-x divide-hairline border-t border-hairline pt-5">
            <?php foreach ($facts as $i => $fact): ?>
              <?php
              // "None" and "Free" are the promises worth colouring, whoever
              // typed them into Page contents.
              $accent = $fact['accent']
                  ?? (in_array(strtolower((string) $fact['value']), ['none', 'free'], true)
                      ? 'text-signal-600'
                      : 'text-navy-700');
              ?>
              <div class="<?= $i === 0 ? 'pr-4' : ($i === count($facts) - 1 ? 'pl-4' : 'px-4') ?>">
                <dt class="t-meta text-muted"><?= e($fact['label']) ?></dt>
                <dd class="tabular mt-1 whitespace-nowrap font-display text-lg font-semibold sm:text-2xl <?= $accent ?>">
                  <?= e($fact['value']) ?>
                </dd>
              </div>
            <?php endforeach; ?>
          </dl>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
