<?php
declare(strict_types=1);

$apart     = content_items('items');
$corridors = config('corridors', []);
?>
<section id="apart" class="scroll-mt-24 bg-canvas py-16 lg:py-24" aria-labelledby="apart-heading">
  <div class="shell">

    <div class="grid items-start gap-10 lg:grid-cols-12 lg:gap-14">
      <!-- Sticky so the commission panel stays beside the six cards rather than
           leaving a void once the shorter column runs out. -->
      <div class="lg:col-span-5 lg:sticky lg:top-28 lg:self-start">
        <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
        <h2 id="apart-heading" class="t-h2 mt-4"><?= e(content('heading')) ?></h2>

        <div class="mt-7 rounded-[10px] border border-hairline bg-surface p-6 shadow-panel">
          <p class="flex items-baseline gap-2 font-display text-3xl font-semibold leading-none">
            <span class="text-signal-600">No</span>
            <span class="text-navy-700">Client Commission</span>
          </p>
          <div class="mt-5 h-px bg-hairline"></div>
          <dl class="mt-5 space-y-4">
            <div class="flex items-start justify-between gap-6">
              <dt class="text-[0.9375rem] text-muted">Paid by the seller or landlord</dt>
              <dd class="shrink-0 font-sans text-[0.9375rem] font-semibold text-navy-700">The commission</dd>
            </div>
            <div class="flex items-start justify-between gap-6 border-t border-hairline pt-4">
              <dt class="text-[0.9375rem] text-muted">Paid by you, the client</dt>
              <dd class="tabular shrink-0 font-sans text-[0.9375rem] font-semibold text-navy-700"><?= e(config('admin_fee')) ?> flat</dd>
            </div>
          </dl>
          <p class="mt-5 t-meta text-muted"><?= e(content('panel_note')) ?></p>
        </div>
      </div>

      <div class="lg:col-span-7">
        <ul class="grid gap-5 sm:grid-cols-2">
          <?php foreach ($apart as $item): ?>
            <li class="reveal card flex h-full flex-col p-6">
              <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-hairline bg-canvas text-navy-700">
                <?= icon($item['icon'], 'h-5 w-5') ?>
              </span>
              <h3 class="t-h3 mt-4 text-[1.0625rem] leading-snug"><?= e($item['title']) ?></h3>
              <p class="mt-2 text-[0.9375rem] leading-relaxed text-muted"><?= e($item['body']) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- Reach -->
    <div class="reveal mt-12 overflow-hidden rounded-[10px] border border-hairline bg-surface lg:mt-16">
      <div class="grid gap-8 p-7 lg:grid-cols-12 lg:items-center lg:gap-10 lg:p-9">
        <div class="lg:col-span-5">
          <h3 class="t-h3"><?= e(content('reach_heading')) ?></h3>
          <p class="mt-3 text-[0.9375rem] leading-relaxed text-muted"><?= e(content('reach')) ?></p>
        </div>
        <ul class="flex flex-wrap gap-2 lg:col-span-7 lg:justify-end">
          <?php foreach ($corridors as $city): ?>
            <li class="badge border border-hairline bg-canvas text-navy-700 normal-case tracking-normal text-[0.8125rem] font-medium">
              <?= icon('map-pin', 'h-3.5 w-3.5 text-gold-600') ?><?= e($city) ?>
            </li>
          <?php endforeach; ?>
          <li class="badge border border-dashed border-gold-500/50 bg-gold-100/50 text-gold-600 normal-case tracking-normal text-[0.8125rem] font-medium">
            and anywhere else
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>
