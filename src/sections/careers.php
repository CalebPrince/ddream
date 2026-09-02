<?php
declare(strict_types=1);

/**
 * The Careers page body: why work here, the open roles, and the speculative
 * application panel. Wording lives in Page contents; the vacancies themselves
 * are edited under Careers.
 */

$roles   = data_set('careers');
$contact = config('contact', []);
$reasons = content_items('reasons');
?>
<section class="bg-surface py-16 lg:py-20" aria-labelledby="why-heading">
  <div class="shell">
    <div class="max-w-2xl">
      <p class="eyebrow"><?= e(content('why_eyebrow')) ?></p>
      <h2 id="why-heading" class="t-h2 mt-4"><?= e(content('why_heading')) ?></h2>
    </div>
    <ul class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($reasons as $reason): ?>
        <li class="reveal card flex h-full flex-col p-6">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-gold-200 bg-gold-100 text-gold-600">
            <?= icon($reason['icon'], 'h-5 w-5') ?>
          </span>
          <h3 class="t-h3 mt-4 text-[1.0625rem] leading-snug"><?= e($reason['title']) ?></h3>
          <p class="mt-2 text-[0.9375rem] leading-relaxed text-muted"><?= e($reason['body']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section id="roles" class="scroll-mt-24 bg-canvas py-16 lg:py-20" aria-labelledby="roles-heading">
  <div class="shell">
    <div class="max-w-2xl">
      <p class="eyebrow"><?= e(content('roles_eyebrow')) ?></p>
      <h2 id="roles-heading" class="t-h2 mt-4">
        <?= e($roles ? content('roles_heading') : content('roles_heading_empty')) ?>
      </h2>
    </div>

    <?php if ($roles): ?>
      <ul class="mt-9 space-y-4">
        <?php foreach ($roles as $role): ?>
          <li class="reveal">
            <details class="card group overflow-hidden">
              <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-4 p-6">
                <div class="min-w-0">
                  <h3 class="t-h3 text-[1.125rem] leading-snug"><?= e($role['title']) ?></h3>
                  <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 t-meta text-muted">
                    <span class="inline-flex items-center gap-1.5"><?= icon('map-pin', 'h-4 w-4 text-gold-600') ?><?= e($role['location']) ?></span>
                    <span class="inline-flex items-center gap-1.5"><?= icon('clock', 'h-4 w-4 text-gold-600') ?><?= e($role['type']) ?></span>
                    <span class="inline-flex items-center gap-1.5"><?= icon('users', 'h-4 w-4 text-gold-600') ?><?= e($role['team']) ?></span>
                  </p>
                </div>
                <span class="inline-flex shrink-0 items-center gap-2 text-[0.875rem] font-semibold text-navy-700">
                  Details <?= icon('chevron-down', 'h-4 w-4 transition-transform group-open:rotate-180') ?>
                </span>
              </summary>

              <div class="border-t border-hairline p-6">
                <p class="text-[0.9375rem] leading-relaxed text-muted"><?= e($role['summary']) ?></p>
                <h4 class="t-meta mt-6 font-semibold uppercase tracking-[0.14em] text-muted">What we are looking for</h4>
                <ul class="mt-3 space-y-2">
                  <?php foreach ($role['wants'] as $want): ?>
                    <li class="flex items-start gap-2.5 text-[0.9375rem] text-navy-700">
                      <?= icon('check', 'h-4 w-4 shrink-0 translate-y-1 text-verified') ?><?= e($want) ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
                <a href="mailto:<?= e($contact['email']) ?>?subject=<?= rawurlencode('Application: ' . $role['title']) ?>"
                   class="btn btn-primary mt-6">
                  <?= icon('mail', 'h-4 w-4') ?>Apply for this role
                </a>
              </div>
            </details>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="mt-9 rounded-[10px] border border-dashed border-hairline bg-surface p-10 text-center">
        <span class="inline-flex h-12 w-12 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700">
          <?= icon('users', 'h-5 w-5') ?>
        </span>
        <h3 class="t-h3 mt-4"><?= e(content('empty_heading')) ?></h3>
        <p class="mx-auto mt-2 max-w-md text-[0.9375rem] leading-relaxed text-muted"><?= e(content('empty_body')) ?></p>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="relative overflow-hidden bg-navy-800 py-16 text-navy-200 lg:py-20" aria-labelledby="spec-heading">
  <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.06]" aria-hidden="true"></div>
  <div class="shell relative grid gap-10 lg:grid-cols-12 lg:items-center">
    <div class="lg:col-span-7">
      <p class="eyebrow eyebrow-light"><?= e(content('spec_eyebrow')) ?></p>
      <h2 id="spec-heading" class="t-h2 mt-4 max-w-xl text-white"><?= e(content('spec_heading')) ?></h2>
      <p class="t-lead mt-4 max-w-xl text-navy-200/85"><?= e(content('spec_lead')) ?></p>
    </div>
    <div class="lg:col-span-5 lg:justify-self-end">
      <div class="rounded-[10px] border border-hairline-dark bg-navy-900/60 p-6 lg:w-[22rem]">
        <h3 class="t-h3 text-white"><?= e(content('card_heading')) ?></h3>
        <p class="mt-2 t-meta text-navy-200/75"><?= e(content('card_body')) ?></p>
        <a href="mailto:<?= e($contact['email']) ?>?subject=<?= rawurlencode('Speculative application') ?>"
           class="btn btn-accent mt-5 w-full">
          <?= icon('mail', 'h-4 w-4') ?><?= e(content('cv_label')) ?>
        </a>
        <a href="/contact" class="btn btn-outline-light mt-3 w-full"><?= e(content('contact_label')) ?></a>
      </div>
    </div>
  </div>
</section>
