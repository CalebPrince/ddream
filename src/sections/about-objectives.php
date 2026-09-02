<?php
declare(strict_types=1);

$objectives = content_lines('objectives');
?>
<section id="objectives" class="scroll-mt-24 bg-canvas py-16 lg:py-24" aria-labelledby="objectives-heading">
  <div class="shell">
    <div class="max-w-2xl">
      <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
      <h2 id="objectives-heading" class="t-h2 mt-4"><?= e(content('heading')) ?></h2>
    </div>

    <ol class="mt-10 grid gap-px overflow-hidden rounded-[10px] border border-hairline bg-hairline md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($objectives as $i => $objective): ?>
        <li class="reveal flex gap-4 bg-surface p-6 transition-colors hover:bg-gold-100/40 lg:p-7">
          <span class="tabular font-display text-2xl font-semibold leading-none text-gold-500">
            <?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?>
          </span>
          <p class="text-[0.9375rem] leading-relaxed text-navy-700"><?= e($objective) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
