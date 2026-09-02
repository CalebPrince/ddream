<?php
declare(strict_types=1);

?>
<section class="bg-surface py-16 lg:py-24" aria-labelledby="whoweare-heading">
  <div class="shell grid gap-12 lg:grid-cols-12 lg:gap-14">

    <div class="lg:col-span-7">
      <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
      <h2 id="whoweare-heading" class="t-h2 mt-4 max-w-xl"><?= e(content('heading')) ?></h2>

      <div class="mt-7 space-y-5">
        <?php foreach (content_lines('paragraphs') as $i => $paragraph): ?>
          <p class="<?= $i === 0 ? 't-lead text-ink' : 'text-[0.9375rem] leading-relaxed text-muted' ?>">
            <?= e($paragraph) ?>
          </p>
        <?php endforeach; ?>
      </div>
    </div>

    <aside class="lg:col-span-5">
      <div class="card p-6 lg:sticky lg:top-28">
        <h3 class="t-meta font-semibold uppercase tracking-[0.14em] text-gold-600">
          <?= e(content('aside_heading')) ?>
        </h3>
        <p class="mt-3 text-[0.9375rem] leading-relaxed text-muted"><?= e(content('aside_body')) ?></p>

        <ul class="mt-5 grid gap-2.5">
          <?php foreach (content_lines('network') as $item): ?>
            <li class="flex items-center gap-2.5 text-[0.9375rem] text-navy-700">
              <?= icon('check', 'h-4 w-4 shrink-0 text-verified') ?><?= e($item) ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="mt-6 border-t border-hairline pt-5">
          <p class="t-meta text-muted">
            <?= e(config('founded_note')) ?>, operating from a recognised office in
            <?= e(config('contact.address')) ?>.
          </p>
          <a href="<?= e(content('aside_link_href')) ?>" class="btn btn-outline mt-4 w-full"><?= e(content('aside_link_label')) ?></a>
        </div>
      </div>
    </aside>
  </div>
</section>
