<?php
declare(strict_types=1);

$contact = config('contact', []);

$expect = content_items('expect');
?>
<section id="office" class="border-t border-hairline bg-surface py-16 lg:py-24" aria-labelledby="office-heading">
  <div class="shell grid gap-12 lg:grid-cols-12 lg:items-center lg:gap-14">

    <div class="lg:col-span-6">
      <figure>
        <div class="overflow-hidden rounded-[10px] border border-hairline">
          <img src="<?= e(asset(content('image'))) ?>"
               alt="<?= e(content('image_alt')) ?>"
               width="1536" height="1024" loading="lazy"
               class="w-full object-cover">
        </div>
        <figcaption class="mt-3 flex items-start gap-2 t-meta text-muted">
          <?= icon('map-pin', 'h-4 w-4 shrink-0 translate-y-0.5 text-gold-600') ?>
          <?= e($contact['address']) ?>
        </figcaption>
      </figure>
    </div>

    <div class="lg:col-span-6">
      <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
      <h2 id="office-heading" class="t-h2 mt-4"><?= e(content('heading')) ?></h2>
      <p class="t-lead mt-4 text-muted"><?= e(content('lead')) ?></p>

      <ul class="mt-8 space-y-5">
        <?php foreach ($expect as $item): ?>
          <li class="reveal flex gap-4">
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-[4px] bg-navy-700 text-gold-400">
              <?= icon($item['icon'], 'h-[18px] w-[18px]') ?>
            </span>
            <div>
              <h3 class="font-sans text-[1rem] font-semibold text-navy-700"><?= e($item['title']) ?></h3>
              <p class="mt-1 text-[0.9375rem] leading-relaxed text-muted"><?= e($item['body']) ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>

      <dl class="mt-8 grid gap-4 border-t border-hairline pt-6 sm:grid-cols-2">
        <div>
          <dt class="t-meta text-muted"><?= e(content('hours_label')) ?></dt>
          <dd class="mt-1 text-[0.9375rem] font-medium text-navy-700"><?= e($contact['hours']) ?></dd>
        </div>
        <div>
          <dt class="t-meta text-muted"><?= e(content('phone_label')) ?></dt>
          <dd class="mt-1 text-[0.9375rem] font-medium text-navy-700">
            <a class="tabular transition-colors hover:text-gold-600" href="tel:<?= e($contact['phone_href']) ?>"><?= e($contact['phone']) ?></a>
          </dd>
        </div>
      </dl>

      <div class="mt-7 flex flex-wrap gap-3">
        <a href="<?= e(content('primary_href')) ?>" class="btn btn-primary"><?= e(content('primary_label')) ?></a>
        <a href="<?= e(content('secondary_href')) ?>" class="btn btn-outline"><?= e(content('secondary_label')) ?></a>
      </div>
    </div>
  </div>
</section>
