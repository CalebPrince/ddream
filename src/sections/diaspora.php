<?php
declare(strict_types=1);

$steps = content_items('steps');
?>
<section class="relative overflow-hidden bg-navy-800 text-navy-200" aria-labelledby="diaspora-heading">
  <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.06]" aria-hidden="true"></div>

  <div class="shell relative grid gap-12 py-16 lg:grid-cols-12 lg:gap-14 lg:py-24">
    <div class="lg:col-span-6 xl:col-span-6">
      <p class="eyebrow eyebrow-light"><?= e(content('eyebrow')) ?></p>
      <h2 id="diaspora-heading" class="t-h2 mt-4 text-white">
        <?= e(content('heading')) ?>
      </h2>
      <p class="t-lead mt-4 max-w-xl text-navy-200/85"><?= e(content('lead')) ?></p>

      <ol class="mt-9 space-y-7">
        <?php foreach ($steps as $i => $step): ?>
          <li class="reveal relative flex gap-5 pl-1">
            <span class="relative flex flex-col items-center">
              <span class="tabular inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-gold-500/50 bg-navy-900 font-display text-[0.9375rem] font-semibold text-gold-400">
                <?= $i + 1 ?>
              </span>
              <?php if ($i < count($steps) - 1): ?>
                <span class="mt-2 w-px flex-1 bg-gradient-to-b from-gold-500/40 to-transparent" aria-hidden="true"></span>
              <?php endif; ?>
            </span>
            <div class="pb-1">
              <h3 class="font-sans text-[1.0625rem] font-semibold text-white"><?= e($step['title']) ?></h3>
              <p class="mt-1.5 text-[0.9375rem] leading-relaxed text-navy-200/80"><?= e($step['body']) ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>

      <div class="mt-10 flex flex-wrap gap-3">
        <a href="<?= e(content('primary_href')) ?>" class="btn btn-accent"><?= e(content('primary_label')) ?></a>
        <a href="<?= e(content('secondary_href')) ?>" class="btn btn-outline-light"><?= e(content('secondary_label')) ?></a>
      </div>
    </div>

    <div class="lg:col-span-6">
      <figure class="relative">
        <div class="overflow-hidden rounded-[10px] border border-hairline-dark">
          <img src="<?= e(asset(content('image'))) ?>"
               alt="<?= e(content('image_alt')) ?>"
               width="1536" height="1024" loading="lazy"
               class="w-full object-cover">
        </div>

        <figcaption class="mt-4 flex items-start gap-2 t-meta text-navy-200/70">
          <?= icon('map-pin', 'h-4 w-4 shrink-0 translate-y-0.5 text-gold-500') ?>
          <span><?= e(content('caption')) ?></span>
        </figcaption>

        <!-- Floating proof card -->
        <div class="mt-6 grid gap-px overflow-hidden rounded-[10px] border border-hairline-dark bg-hairline-dark sm:grid-cols-3">
          <?php foreach (content_items('stats') as $stat): ?>
            <div class="bg-navy-900 px-5 py-5">
              <p class="tabular font-display text-3xl font-semibold text-gold-400"><?= e($stat['value']) ?></p>
              <p class="mt-1 t-meta text-navy-200/70"><?= e($stat['label']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </figure>
    </div>
  </div>
</section>
