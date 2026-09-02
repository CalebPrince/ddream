<?php
declare(strict_types=1);

$services = data_set('services');
$total    = count($services['primary']) + count($services['secondary']);
?>
<section id="services" class="border-t border-hairline bg-surface py-16 lg:py-24" aria-labelledby="services-heading">
  <div class="shell">

    <div class="flex flex-wrap items-end justify-between gap-5">
      <div class="max-w-2xl">
        <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
        <h2 id="services-heading" class="t-h2 mt-4"><?= e(content('heading')) ?></h2>
        <p class="t-lead mt-4 text-muted"><?= e(content('lead', ['{count}' => (string) $total])) ?></p>
      </div>
      <a href="<?= e(content('link_href')) ?>" class="btn btn-outline"><?= e(content('link_label')) ?></a>
    </div>

    <!-- The six we are most often engaged for -->
    <ul class="mt-10 grid gap-px overflow-hidden rounded-[10px] border border-hairline bg-hairline sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($services['primary'] as $service): ?>
        <li id="service-<?= e($service['slug']) ?>" class="reveal group scroll-mt-28 bg-surface p-7 transition-colors hover:bg-canvas">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700 transition-colors group-hover:bg-navy-700 group-hover:text-gold-400">
            <?= icon($service['icon'], 'h-5 w-5') ?>
          </span>
          <h3 class="t-h3 mt-5 text-[1.125rem] leading-snug"><?= e($service['title']) ?></h3>
          <?php if (!empty($service['note'])): ?>
            <p class="mt-1 t-meta text-gold-600"><?= e($service['note']) ?></p>
          <?php endif; ?>
          <p class="mt-2.5 text-[0.9375rem] leading-relaxed text-muted"><?= e($service['body']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- The rest -->
    <ul class="mt-6 grid gap-px overflow-hidden rounded-[10px] border border-hairline bg-hairline sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($services['secondary'] as $service): ?>
        <li id="service-<?= e($service['slug']) ?>" class="reveal flex scroll-mt-28 items-start gap-3.5 bg-canvas p-5">
          <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[4px] border border-gold-200 bg-gold-100 text-gold-600">
            <?= icon($service['icon'], 'h-[18px] w-[18px]') ?>
          </span>
          <div>
            <h3 class="font-sans text-[0.9375rem] font-semibold leading-snug text-navy-700"><?= e($service['title']) ?></h3>
            <p class="mt-0.5 t-meta text-muted"><?= e($service['note']) ?></p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <p class="prose-inline mt-7 flex items-start gap-2 t-meta text-muted">
      <?= icon('shield-check', 'h-4 w-4 shrink-0 translate-y-0.5 text-verified') ?>
      <span><?= content_html('footnote') ?></span>
    </p>
  </div>
</section>
