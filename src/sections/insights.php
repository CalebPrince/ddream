<?php
declare(strict_types=1);

$insights = data_set('insights');
?>
<section class="border-t border-hairline bg-surface py-16 lg:py-20" aria-labelledby="insights-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <div class="max-w-xl">
        <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
        <h2 id="insights-heading" class="t-h2 mt-3"><?= e(content('heading')) ?></h2>
      </div>
      <a href="<?= e(content('link_href')) ?>" class="btn btn-outline"><?= e(content('link_label')) ?> <?= icon('arrow-right', 'h-4 w-4') ?></a>
    </div>

    <ul class="mt-9 grid gap-6 md:grid-cols-3">
      <?php foreach ($insights as $post): ?>
        <li class="reveal">
          <article class="card card-interactive group relative flex h-full flex-col overflow-hidden">
            <div class="aspect-[16/10] overflow-hidden bg-navy-100">
              <img src="<?= e(asset($post['image'])) ?>" alt="" width="1600" height="1000" loading="lazy"
                   class="card-zoom h-full w-full object-cover">
            </div>
            <div class="flex flex-1 flex-col p-5">
              <div class="flex items-center gap-2 t-meta">
                <span class="badge border border-hairline bg-canvas text-gold-600"><?= e($post['category']) ?></span>
                <span class="text-muted"><?= e($post['read']) ?></span>
              </div>
              <h3 class="t-h3 mt-3 text-[1.125rem] leading-snug">
                <a href="<?= e($post['href']) ?>" class="after:absolute after:inset-0 after:content-['']"><?= e($post['title']) ?></a>
              </h3>
              <p class="mt-2.5 flex-1 text-[0.9375rem] leading-relaxed text-muted"><?= e($post['excerpt']) ?></p>
              <span class="mt-4 inline-flex items-center gap-2 text-[0.875rem] font-semibold text-navy-700 transition-colors group-hover:text-gold-600">
                Read the guide <?= icon('arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-1') ?>
              </span>
            </div>
          </article>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
