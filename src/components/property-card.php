<?php
declare(strict_types=1);

/** @var array $property */
$period = $property['period'] ?? null;
$isLand = ($property['category'] ?? null) === 'land';
$added  = is_int($property['added'] ?? null)
    ? added_label($property['added'])
    : (string) ($property['added'] ?? '');

// What we verify differs by basis: the title on a sale, the landlord on a let,
// the property itself on a short stay.
$verifiedLabel = match ($property['basis']) {
    'To rent'    => 'Landlord verified',
    'Short stay' => 'Inspected by us',
    default      => 'Title verified',
};
?>
<article class="card card-interactive group relative flex h-full flex-col overflow-hidden">
  <div class="relative aspect-[4/3] overflow-hidden bg-navy-100">
    <img src="<?= e(asset($property['image'])) ?>"
         alt="<?= e($property['title']) ?>, <?= e($property['address']) ?>"
         width="1440" height="1080" loading="lazy"
         class="card-zoom h-full w-full object-cover">

    <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
      <span class="badge bg-navy-700 text-white"><?= e($property['basis']) ?></span>
      <span class="badge bg-surface/95 text-navy-700 backdrop-blur-sm"><?= e($property['status']) ?></span>
    </div>

    <button type="button"
            class="absolute right-3 top-3 z-20 inline-flex h-9 w-9 items-center justify-center rounded-[4px] border border-white/40 bg-navy-900/40 text-white backdrop-blur-sm transition-colors hover:bg-white hover:text-signal-600 data-[saved=true]:bg-white data-[saved=true]:text-signal-600"
            data-save aria-pressed="false"
            aria-label="Save <?= e($property['title']) ?> to your shortlist">
      <?= icon('heart', 'h-[18px] w-[18px]') ?>
    </button>

    <?php if (!empty($property['verified'])): ?>
      <span class="absolute bottom-3 left-3 badge bg-verified text-white">
        <?= icon('badge-check', 'h-3.5 w-3.5') ?><?= e($verifiedLabel) ?>
      </span>
    <?php endif; ?>
  </div>

  <div class="flex flex-1 flex-col p-5">
    <div class="flex items-baseline justify-between gap-3">
      <p class="tabular font-display text-2xl font-semibold text-navy-700">
        <?= e(money($property['price'], $property['currency'])) ?><?php if ($period): ?><span class="font-sans text-sm font-normal text-muted"> <?= e($period) ?></span><?php endif; ?>
      </p>
      <span class="t-meta tabular text-muted"><?= e($property['id']) ?></span>
    </div>

    <h3 class="t-h3 mt-2 text-[1.1875rem]">
      <a href="/property/<?= e($property['slug']) ?>" class="after:absolute after:inset-0 after:content-['']">
        <?= e($property['title']) ?>
      </a>
    </h3>

    <p class="mt-1.5 flex items-center gap-1.5 t-meta text-muted">
      <?= icon('map-pin', 'h-4 w-4 shrink-0 text-gold-600') ?><?= e($property['address']) ?>
    </p>

    <p class="mt-3 text-[0.9375rem] leading-relaxed text-muted"><?= e($property['summary']) ?></p>

    <ul class="mt-4 flex flex-wrap gap-1.5">
      <?php foreach ($property['tags'] as $tag): ?>
        <li class="badge border border-gold-200 bg-gold-100 text-gold-600 normal-case tracking-normal text-[0.75rem] font-medium"><?= e($tag) ?></li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-auto flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-t border-hairline pt-4">
      <ul class="flex items-center gap-4 t-meta text-navy-700">
        <?php if (!empty($property['beds'])): ?>
          <li class="flex items-center gap-1.5" title="Bedrooms">
            <?= icon('bed', 'h-[18px] w-[18px] text-muted') ?>
            <span class="tabular font-semibold"><?= (int) $property['beds'] ?></span>
            <span class="sr-only">bedrooms</span>
          </li>
        <?php endif; ?>
        <?php if (!empty($property['baths'])): ?>
          <li class="flex items-center gap-1.5" title="Bathrooms">
            <?= icon('bath', 'h-[18px] w-[18px] text-muted') ?>
            <span class="tabular font-semibold"><?= (int) $property['baths'] ?></span>
            <span class="sr-only">bathrooms</span>
          </li>
        <?php endif; ?>
        <li class="flex items-center gap-1.5" title="<?= $isLand ? 'Plot size' : 'Internal area' ?>">
          <?= icon($isLand ? 'landmark' : 'ruler', 'h-[18px] w-[18px] text-muted') ?>
          <span class="tabular whitespace-nowrap font-semibold"><?= (int) $property['area'] ?> m²</span>
          <?php if ($isLand): ?><span class="text-muted">plot</span><?php endif; ?>
        </li>
      </ul>
      <span class="t-meta whitespace-nowrap text-muted"><?= e($added) ?></span>
    </div>
  </div>
</article>
