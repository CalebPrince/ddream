<?php
declare(strict_types=1);

$slides      = data_set('slideshow');
$areas       = data_set('areas');
$development = $slides[0]['development'];
?>
<section class="relative overflow-hidden bg-canvas" aria-label="Search for property in Ghana">
  <!-- Slideshow, full-bleed to the right edge on large screens -->
  <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-[45%] xl:w-[46%]">
    <div id="heroSlideshow" class="group relative h-[21rem] overflow-hidden bg-navy-800 sm:h-[26rem] lg:h-full lg:rounded-bl-[10px]"
         data-interval="6000" aria-roledescription="carousel" aria-label="Grace City Prime Homes gallery">

      <?php foreach ($slides as $i => $slide): ?>
        <figure class="absolute inset-0 transition-opacity duration-[900ms] ease-[cubic-bezier(.16,1,.3,1)] <?= $i === 0 ? 'opacity-100' : 'opacity-0' ?>"
                data-slide="<?= $i ?>" data-kicker="<?= e($slide['kicker']) ?>"
                data-caption="<?= e($slide['caption']) ?>"
                role="group" aria-roledescription="slide"
                aria-label="<?= $i + 1 ?> of <?= count($slides) ?>: <?= e($slide['caption']) ?>"
                <?= $i === 0 ? '' : 'aria-hidden="true"' ?>>
          <img src="<?= e(asset($slide['src'])) ?>"
               alt="<?= e($slide['caption']) ?> at <?= e($development['name']) ?>"
               width="1920" height="1080"
               class="h-full w-full object-cover"
               <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
          <figcaption class="sr-only"><?= e($slide['caption']) ?></figcaption>
        </figure>
      <?php endforeach; ?>

      <!-- Legibility scrims -->
      <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-900/90 via-navy-900/35 to-navy-900/30 lg:via-navy-900/15" aria-hidden="true"></div>
      <div class="pointer-events-none absolute inset-y-0 left-0 hidden w-16 bg-gradient-to-r from-canvas/70 to-transparent lg:block" aria-hidden="true"></div>

      <!-- Development badge -->
      <div class="absolute left-5 top-5 sm:left-7 sm:top-7">
        <div class="inline-flex items-center gap-2 rounded-[2px] bg-navy-900/70 px-3 py-2 backdrop-blur-sm">
          <?= icon('layers', 'h-4 w-4 text-gold-400') ?>
          <span class="text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-white"><?= e(content('badge')) ?></span>
        </div>
      </div>

      <!-- Caption + controls -->
      <div class="absolute inset-x-0 bottom-0 p-5 sm:p-7">
        <div class="flex items-end justify-between gap-4">
          <div class="min-w-0">
            <p class="text-[0.6875rem] font-semibold uppercase tracking-[0.14em] text-gold-400"
               data-slide-kicker><?= e($slides[0]['kicker']) ?></p>
            <h2 class="mt-1.5 font-display text-lg font-semibold text-white sm:text-2xl">
              <?= e($development['name']) ?>
            </h2>
            <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[0.875rem] text-white/75">
              <span class="inline-flex items-center gap-1.5">
                <?= icon('map-pin', 'h-4 w-4 shrink-0 text-gold-400') ?><?= e($development['location']) ?>
              </span>
              <span class="tabular whitespace-nowrap">from <?= e(money($development['price'], $development['currency'])) ?></span>
            </p>
            <p class="mt-1 truncate text-[0.8125rem] text-white/60" data-slide-caption><?= e($slides[0]['caption']) ?></p>
          </div>

          <div class="flex shrink-0 items-center gap-1.5">
            <button type="button" data-slide-toggle
                    class="inline-flex h-10 w-10 items-center justify-center rounded-[4px] border border-white/25 bg-navy-900/40 text-white backdrop-blur-sm transition-colors hover:bg-white hover:text-navy-700"
                    aria-label="Pause slideshow" aria-pressed="false">
              <span data-icon-pause><?= icon('pause', 'h-4 w-4') ?></span>
              <span data-icon-play class="hidden"><?= icon('play-solid', 'h-4 w-4') ?></span>
            </button>
            <button type="button" data-slide-prev
                    class="inline-flex h-10 w-10 items-center justify-center rounded-[4px] border border-white/25 bg-navy-900/40 text-white backdrop-blur-sm transition-colors hover:bg-white hover:text-navy-700"
                    aria-label="Previous image"><?= icon('chevron-left', 'h-4 w-4') ?></button>
            <button type="button" data-slide-next
                    class="inline-flex h-10 w-10 items-center justify-center rounded-[4px] border border-white/25 bg-navy-900/40 text-white backdrop-blur-sm transition-colors hover:bg-white hover:text-navy-700"
                    aria-label="Next image"><?= icon('chevron-right', 'h-4 w-4') ?></button>
          </div>
        </div>

        <!-- Progress dots -->
        <div class="mt-4 flex items-center gap-1.5" role="tablist" aria-label="Choose image">
          <?php foreach ($slides as $i => $slide): ?>
            <button type="button" role="tab" data-slide-dot="<?= $i ?>"
                    aria-label="Image <?= $i + 1 ?>: <?= e($slide['caption']) ?>"
                    aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                    class="h-1 flex-1 max-w-14 rounded-full transition-colors duration-300 <?= $i === 0 ? 'bg-gold-500' : 'bg-white/30 hover:bg-white/60' ?>"></button>
          <?php endforeach; ?>
        </div>
      </div>

      <a href="/virtual-tours" class="absolute right-5 top-5 inline-flex items-center gap-2 rounded-[2px] border border-white/25 bg-navy-900/50 px-3 py-2 text-[0.75rem] font-semibold text-white backdrop-blur-sm transition-colors hover:bg-white hover:text-navy-700 sm:right-7 sm:top-7">
        <?= icon('play', 'h-4 w-4') ?><?= e(content('tour_label')) ?>
      </a>
    </div>
  </div>

  <!-- Copy + search -->
  <div class="shell relative">
    <div class="lg:w-[55%] lg:pr-10 xl:w-[54%]">
      <!-- Extra top padding on desktop clears the hanging brand plaque. -->
      <div class="py-12 lg:pb-14 lg:pt-16 xl:pb-16 xl:pt-[4.5rem]">

        <p class="eyebrow"><?= e(content('eyebrow')) ?></p>

        <h1 class="t-display mt-5 text-navy-700 lg:mt-4 lg:text-[3.25rem]">
          <?= e(content('heading')) ?><br class="hidden sm:block">
          <span class="italic text-gold-600"><?= e(content('heading_accent')) ?></span>
        </h1>

        <p class="t-lead prose-inline mt-5 max-w-xl text-muted lg:mt-4">
          <?= content_html('lead') ?>
        </p>

        <!-- Search panel -->
        <form class="mt-8 rounded-[10px] lg:mt-6 border border-hairline bg-surface shadow-panel" action="/search" method="get" role="search">
          <div class="flex flex-wrap border-b border-hairline px-2 pt-2" role="tablist" aria-label="What are you looking for?">
            <?php
            $tabs = [
                ['id' => 'buy',    'label' => 'Buy',    'cta' => 'Search properties for sale'],
                ['id' => 'rent',   'label' => 'Rent',   'cta' => 'Search properties to rent'],
                ['id' => 'airbnb', 'label' => 'Airbnb', 'cta' => 'Search short stays'],
                ['id' => 'land',   'label' => 'Land',   'cta' => 'Search land and plots'],
            ];
            foreach ($tabs as $i => $tab): ?>
              <button type="button" role="tab" data-search-tab="<?= e($tab['id']) ?>"
                      data-cta="<?= e($tab['cta']) ?>"
                      aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                      class="relative -mb-px px-4 py-3 text-[0.9375rem] font-semibold transition-colors <?= $i === 0 ? 'text-navy-700' : 'text-muted hover:text-navy-600' ?>">
                <?= e($tab['label']) ?>
                <span class="absolute inset-x-3 -bottom-px h-0.5 rounded-full transition-colors <?= $i === 0 ? 'bg-gold-500' : 'bg-transparent' ?>" data-tab-underline></span>
              </button>
            <?php endforeach; ?>
            <input type="hidden" name="intent" id="searchIntent" value="buy">
          </div>

          <div class="p-4 sm:p-5 lg:px-5 lg:py-4">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 xl:gap-3">
              <div class="sm:col-span-2 xl:col-span-4">
                <label class="field-label" for="searchLocation">Location</label>
                <div class="relative">
                  <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-muted"><?= icon('search', 'h-[18px] w-[18px]') ?></span>
                  <input id="searchLocation" name="location" type="text" list="areaSuggestions"
                         class="field pl-11" autocomplete="off"
                         placeholder="e.g. East Legon, Cantonments, Kumasi">
                </div>
                <datalist id="areaSuggestions">
                  <?php foreach ($areas['suggestions'] as $suggestion): ?>
                    <option value="<?= e($suggestion) ?>"></option>
                  <?php endforeach; ?>
                </datalist>
              </div>

              <div>
                <label class="field-label" for="searchType">Property type</label>
                <select id="searchType" name="type" class="field">
                  <option value="">Any type</option>
                  <option>House</option>
                  <option>Apartment</option>
                  <option>Townhouse</option>
                  <option>Commercial space</option>
                  <option>Land / plot</option>
                </select>
              </div>

              <div>
                <label class="field-label" for="searchBeds">Bedrooms</label>
                <select id="searchBeds" name="beds" class="field">
                  <option value="">Any</option>
                  <option value="1">1+</option>
                  <option value="2">2+</option>
                  <option value="3">3+</option>
                  <option value="4">4+</option>
                  <option value="5">5+</option>
                </select>
              </div>

              <div>
                <label class="field-label" for="searchMin">Min price ($)</label>
                <select id="searchMin" name="min" class="field">
                  <option value="">No min</option>
                  <option value="25000">$25,000</option>
                  <option value="50000">$50,000</option>
                  <option value="100000">$100,000</option>
                  <option value="250000">$250,000</option>
                  <option value="500000">$500,000</option>
                </select>
              </div>

              <div>
                <label class="field-label" for="searchMax">Max price ($)</label>
                <select id="searchMax" name="max" class="field">
                  <option value="">No max</option>
                  <option value="100000">$100,000</option>
                  <option value="250000">$250,000</option>
                  <option value="500000">$500,000</option>
                  <option value="1000000">$1,000,000</option>
                  <option value="2000000">$2,000,000+</option>
                </select>
              </div>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center lg:mt-4">
              <button type="submit" class="btn btn-primary btn-lg flex-1" id="searchSubmit">
                <?= icon('search', 'h-[18px] w-[18px]') ?><span data-cta-label>Search properties for sale</span>
              </button>
              <a href="/contact" class="btn btn-outline btn-lg sm:w-auto">Talk to an adviser</a>
            </div>

            <p class="mt-4 flex items-start gap-2 t-meta text-muted lg:mt-3">
              <?= icon('shield-check', 'h-4 w-4 shrink-0 translate-y-0.5 text-verified') ?>
              <span><?= e(content('search_note')) ?></span>
            </p>
          </div>
        </form>

        <!-- Trust markers -->
        <?php $facts = content_items('facts'); ?>
        <dl class="mt-9 grid max-w-xl grid-cols-3 divide-x divide-hairline border-t border-hairline pt-6 lg:mt-6 lg:pt-5">
          <?php foreach ($facts as $i => $fact): ?>
            <div class="<?= $i === 0 ? 'pr-4' : ($i === count($facts) - 1 ? 'pl-4' : 'px-4') ?>">
              <dt class="t-meta text-muted"><?= e($fact['label']) ?></dt>
              <dd class="tabular mt-1 font-display text-2xl font-semibold <?= strtolower($fact['value']) === 'none' ? 'text-signal-600' : 'text-navy-700' ?>">
                <?= e($fact['value']) ?>
              </dd>
            </div>
          <?php endforeach; ?>
        </dl>
      </div>
    </div>
  </div>
</section>
