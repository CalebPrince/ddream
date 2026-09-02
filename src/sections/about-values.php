<?php
declare(strict_types=1);

$values = content_items('values');
?>
<section id="values" class="scroll-mt-24 border-y border-hairline bg-surface py-16 lg:py-24" aria-labelledby="values-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-5">
      <div class="max-w-2xl">
        <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
        <h2 id="values-heading" class="t-h2 mt-4"><?= e(content('heading')) ?></h2>
      </div>
      <p class="t-meta max-w-xs text-muted"><?= e(content('note')) ?></p>
    </div>

    <ul class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($values as $value): ?>
        <li class="reveal card flex h-full items-start gap-4 p-5">
          <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-[4px] border border-gold-200 bg-gold-100 text-gold-600">
            <?= icon($value['icon'], 'h-5 w-5') ?>
          </span>
          <p class="self-center text-[1rem] font-medium leading-snug text-navy-700">
            <?= e($value['text']) ?>
          </p>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
