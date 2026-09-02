<?php
declare(strict_types=1);

/** @var array $tabs @var string $current @var array $rows */

$isMaintenance = $current === 'maintenance';
$live          = !setting_bool('maintenance_enabled');
?>

<?php if (count($tabs) > 1): ?>
  <div class="rail -mx-5 overflow-x-auto px-5 lg:mx-0 lg:px-0">
    <ul class="flex min-w-max items-center gap-1 border-b border-hairline">
      <?php foreach ($tabs as $key => $tab): ?>
        <?php $on = $key === $current; ?>
        <li>
          <a href="<?= e(admin_url('settings?tab=' . $key)) ?>"
             class="relative px-4 py-3 text-[0.875rem] font-semibold transition-colors <?= $on ? 'text-navy-700' : 'text-muted hover:text-navy-600' ?>"
             <?= $on ? 'aria-current="page"' : '' ?>>
            <?= e($tab['label']) ?>
            <?php if ($on): ?><span class="absolute inset-x-3 -bottom-px h-0.5 bg-gold-500"></span><?php endif; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
  <div class="xl:col-span-2">

    <?php if ($isMaintenance): ?>
      <!-- Current state, stated before the controls. -->
      <div class="card flex flex-wrap items-center justify-between gap-4 p-5 <?= $live ? '' : 'border-signal-600/40' ?>">
        <div class="flex items-center gap-4">
          <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-[4px] <?= $live ? 'bg-verified/10 text-verified' : 'bg-signal-600 text-white' ?>">
            <?= icon($live ? 'globe' : 'lock', 'h-5 w-5') ?>
          </span>
          <div>
            <p class="font-sans text-[1rem] font-semibold text-navy-700">
              <?= $live ? 'The site is live' : 'The site is closed to visitors' ?>
            </p>
            <p class="t-meta text-muted">
              <?= $live
                  ? 'Anyone can browse the site normally.'
                  : 'Visitors see the maintenance page. You and other staff see the real site.' ?>
            </p>
          </div>
        </div>
        <a href="/" target="_blank" rel="noopener" class="btn btn-outline">
          View the site <?= icon('arrow-up-right', 'h-4 w-4') ?>
        </a>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= e(admin_url('settings')) ?>" class="card mt-6 p-6">
      <?= csrf_field() ?>
      <input type="hidden" name="tab" value="<?= e($current) ?>">

      <h2 class="t-h3 text-[1.0625rem]"><?= e($tabs[$current]['label']) ?></h2>
      <p class="mt-1.5 text-[0.9375rem] text-muted"><?= e($tabs[$current]['blurb']) ?></p>

      <div class="mt-6 grid gap-5">
        <?php foreach ($rows as $row): ?>
          <?php $key = $row['setting_key']; ?>

          <?php if ($row['type'] === 'bool'): ?>
            <label class="flex cursor-pointer items-start gap-3 rounded-[6px] border border-hairline bg-canvas p-4">
              <input type="checkbox" name="<?= e($key) ?>" value="1"
                     <?= ($row['value'] ?? '0') === '1' ? 'checked' : '' ?>
                     class="mt-0.5 h-4 w-4 shrink-0 rounded-[2px] border-hairline text-navy-700 focus:ring-navy-700">
              <span>
                <span class="block text-[0.9375rem] font-semibold text-navy-700"><?= e($row['label']) ?></span>
                <?php if ($row['hint']): ?>
                  <span class="mt-0.5 block t-meta text-muted"><?= e($row['hint']) ?></span>
                <?php endif; ?>
              </span>
            </label>

          <?php elseif ($row['type'] === 'textarea'): ?>
            <div>
              <label class="field-label" for="<?= e($key) ?>"><?= e($row['label']) ?></label>
              <textarea class="field h-auto py-3" id="<?= e($key) ?>" name="<?= e($key) ?>"
                        rows="4"><?= e((string) ($row['value'] ?? '')) ?></textarea>
              <?php if ($row['hint']): ?>
                <p class="mt-1.5 t-meta text-muted"><?= e($row['hint']) ?></p>
              <?php endif; ?>
            </div>

          <?php else: ?>
            <div>
              <label class="field-label" for="<?= e($key) ?>"><?= e($row['label']) ?></label>
              <input class="field" id="<?= e($key) ?>" name="<?= e($key) ?>" maxlength="255"
                     value="<?= e((string) ($row['value'] ?? '')) ?>">
              <?php if ($row['hint']): ?>
                <p class="mt-1.5 t-meta text-muted"><?= e($row['hint']) ?></p>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <button type="submit" class="btn btn-primary mt-7">Save settings</button>
    </form>
  </div>

  <?php if ($isMaintenance): ?>
    <aside class="grid content-start gap-6">
      <section class="card p-6">
        <h2 class="t-h3 text-[1.0625rem]">What visitors see</h2>
        <p class="mt-2 text-[0.9375rem] leading-relaxed text-muted">
          The maintenance page shows your heading and message, the office phone
          number, WhatsApp and email, and a staff sign-in link. It returns a
          503 so search engines treat it as temporary and do not drop your pages.
        </p>
      </section>

      <section class="card p-6">
        <h2 class="t-h3 text-[1.0625rem]">Who still gets through</h2>
        <ul class="mt-4 space-y-2.5">
          <?php foreach (['Superadmin', 'Admin'] as $role): ?>
            <li class="flex items-center gap-2.5 text-[0.9375rem] text-navy-700">
              <?= icon('check', 'h-4 w-4 shrink-0 text-verified') ?><?= e($role) ?>
            </li>
          <?php endforeach; ?>
          <li class="flex items-start gap-2.5 text-[0.9375rem] text-muted">
            <?= icon('x', 'h-4 w-4 shrink-0 translate-y-1 text-signal-600') ?>
            Everyone else, signed in or not
          </li>
        </ul>
        <p class="mt-4 t-meta text-muted">
          Sign in first, then turn maintenance on. The admin stays reachable
          either way.
        </p>
      </section>
    </aside>
  <?php endif; ?>
</div>
