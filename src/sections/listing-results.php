<?php
declare(strict_types=1);

/**
 * Filter panel plus results grid. Every control is a plain GET form, so filtering
 * and sorting work without JavaScript and every result set has its own URL.
 *
 * Categories sit inside the filter panel as links rather than form controls,
 * because each one is its own route and stays crawlable and linkable.
 *
 * @var array       $results
 * @var array       $criteria
 * @var string      $basePath   the current category path
 * @var array       $categories the category list in the filter panel
 * @var array       $priceBands ['min' => [...], 'max' => [...]] for this basis
 * @var string      $basisLabel "for sale", "to rent" or "available for short stays"
 */

$showBeds = ($criteria['category'] ?? null) !== 'land';
$areas    = data_set('areas');
$sorts    = [
    'newest'     => 'Newest first',
    'price-asc'  => 'Price, low to high',
    'price-desc' => 'Price, high to low',
    'beds'       => 'Most bedrooms',
];
$active   = array_filter([
    'location' => $criteria['location'] ?? '',
    'min'      => $criteria['min'] ?? '',
    'max'      => $criteria['max'] ?? '',
    'beds'     => $criteria['beds'] ?? '',
], static fn ($v): bool => $v !== '' && $v !== null);
?>
<section class="bg-canvas py-10 lg:py-14" aria-labelledby="results-heading">
  <div class="shell">
    <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">

      <!-- Filter panel -->
      <aside class="lg:col-span-3">
        <!-- Collapsed on small screens so the results are not pushed off the page.
             Open in the markup, so it still works with JavaScript switched off;
             site.js closes it below the lg breakpoint. -->
        <details id="filterPanel" class="card overflow-hidden lg:sticky lg:top-28" open>
          <summary class="flex cursor-pointer list-none items-center justify-between gap-3 border-b border-hairline p-5 lg:hidden">
            <span class="flex items-center gap-2.5 font-sans text-[0.9375rem] font-semibold text-navy-700">
              <?= icon('search', 'h-[18px] w-[18px] text-gold-600') ?>
              Filters and property type
            </span>
            <?= icon('chevron-down', 'h-4 w-4 shrink-0 text-muted transition-transform group-open:rotate-180') ?>
          </summary>

          <nav class="border-b border-hairline p-5" aria-label="Property categories">
            <h2 class="t-meta font-semibold uppercase tracking-[0.14em] text-muted">
              Property type
            </h2>
            <ul class="mt-4 space-y-0.5">
              <?php foreach ($categories as $cat): ?>
                <?php $isOn = $cat['href'] === $basePath; ?>
                <li>
                  <a href="<?= e($cat['href']) ?>"
                     class="flex items-center justify-between gap-3 rounded-[4px] px-3 py-2.5 text-[0.9375rem] transition-colors <?= $isOn ? 'bg-navy-700 font-semibold text-white' : 'font-medium text-navy-700 hover:bg-navy-100' ?>"
                     <?= $isOn ? 'aria-current="page"' : '' ?>>
                    <span class="flex items-center gap-2.5">
                      <?= icon($cat['icon'], 'h-[18px] w-[18px] ' . ($isOn ? 'text-gold-400' : 'text-gold-600')) ?>
                      <?= e($cat['label']) ?>
                    </span>
                    <span class="tabular rounded-[2px] px-1.5 py-0.5 text-[0.6875rem] font-semibold <?= $isOn ? 'bg-white/15 text-white' : 'bg-navy-100 text-navy-700' ?>">
                      <?= (int) $cat['count'] ?>
                    </span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </nav>

          <form action="<?= e($basePath) ?>" method="get" class="p-5">
            <div class="flex items-center justify-between gap-3">
              <h2 class="t-meta font-semibold uppercase tracking-[0.14em] text-muted">Refine</h2>
              <?php if ($active): ?>
                <a href="<?= e($basePath) ?>" class="t-meta font-semibold text-gold-600 underline underline-offset-4">Clear all</a>
              <?php endif; ?>
            </div>

            <div class="mt-4 space-y-5">
              <div>
                <label class="field-label" for="f-location">Location</label>
                <input class="field" id="f-location" name="location" type="text" list="areaSuggestionsList"
                       value="<?= e((string) ($criteria['location'] ?? '')) ?>"
                       placeholder="Any location">
                <datalist id="areaSuggestionsList">
                  <?php foreach ($areas['suggestions'] as $suggestion): ?>
                    <option value="<?= e($suggestion) ?>"></option>
                  <?php endforeach; ?>
                </datalist>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="field-label" for="f-min">Min price</label>
                  <select class="field" id="f-min" name="min">
                    <option value="">No min</option>
                    <?php foreach ($priceBands['min'] as $v): ?>
                      <option value="<?= $v ?>" <?= (string) ($criteria['min'] ?? '') === (string) $v ? 'selected' : '' ?>>
                        <?= e(money($v)) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                  <label class="field-label" for="f-max">Max price</label>
                  <select class="field" id="f-max" name="max">
                    <option value="">No max</option>
                    <?php foreach ($priceBands['max'] as $v): ?>
                      <option value="<?= $v ?>" <?= (string) ($criteria['max'] ?? '') === (string) $v ? 'selected' : '' ?>>
                        <?= e(money($v)) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <?php if ($showBeds): ?>
                <div>
                  <label class="field-label" for="f-beds">Minimum bedrooms</label>
                  <select class="field" id="f-beds" name="beds">
                    <option value="">Any</option>
                    <?php foreach ([1, 2, 3, 4, 5] as $v): ?>
                      <option value="<?= $v ?>" <?= (string) ($criteria['beds'] ?? '') === (string) $v ? 'selected' : '' ?>>
                        <?= $v ?>+
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php endif; ?>

              <?php if (!empty($criteria['sort'])): ?>
                <input type="hidden" name="sort" value="<?= e((string) $criteria['sort']) ?>">
              <?php endif; ?>

              <button type="submit" class="btn btn-primary w-full">
                <?= icon('search', 'h-4 w-4') ?>Apply filters
              </button>
            </div>

            <div class="mt-6 border-t border-hairline pt-5">
              <p class="t-meta text-muted">
                Cannot see it here? We source off-market properties every week.
              </p>
              <a href="/contact" class="btn btn-outline mt-3 w-full">Send us your brief</a>
            </div>
          </form>
        </details>
      </aside>

      <!-- Results -->
      <div class="lg:col-span-9">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-hairline pb-4">
          <h2 id="results-heading" class="font-sans text-[0.9375rem] text-muted">
            <strong class="tabular font-semibold text-navy-700"><?= count($results) ?></strong>
            <?= count($results) === 1 ? 'property' : 'properties' ?>
            <?= $active ? 'matching your filters' : e($basisLabel) ?>
          </h2>

          <form action="<?= e($basePath) ?>" method="get" class="flex items-center gap-2">
            <?php foreach ($active as $key => $value): ?>
              <input type="hidden" name="<?= e($key) ?>" value="<?= e((string) $value) ?>">
            <?php endforeach; ?>
            <label class="t-meta whitespace-nowrap text-muted" for="f-sort">Sort by</label>
            <select class="field h-10 w-auto py-0 text-[0.875rem]" id="f-sort" name="sort"
                    onchange="this.form.submit()">
              <?php foreach ($sorts as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= ($criteria['sort'] ?? 'newest') === $key ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <noscript><button type="submit" class="btn btn-outline h-10 px-3 text-[0.8125rem]">Go</button></noscript>
          </form>
        </div>

        <?php if ($results): ?>
          <ul class="mt-6 grid gap-6 sm:grid-cols-2 2xl:grid-cols-3">
            <?php foreach ($results as $property): ?>
              <li class="reveal"><?php component('property-card', ['property' => $property]); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <div class="mt-6 rounded-[10px] border border-dashed border-hairline bg-surface p-10 text-center">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700">
              <?= icon('search', 'h-5 w-5') ?>
            </span>
            <h3 class="t-h3 mt-4">Nothing matches those filters yet</h3>
            <p class="mx-auto mt-2 max-w-md text-[0.9375rem] leading-relaxed text-muted">
              Our books turn over quickly and we source off-market. Tell us what you are
              after and we will come back within one working day.
            </p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
              <a href="<?= e($basePath) ?>" class="btn btn-outline">Clear filters</a>
              <a href="/contact" class="btn btn-primary">Send us your brief</a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
