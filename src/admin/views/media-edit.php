<?php
declare(strict_types=1);

/** @var array $media @var array $usage */

$bytes = (int) $media['bytes'];
$size  = $bytes > 1048576
    ? round($bytes / 1048576, 1) . ' MB'
    : max(1, (int) round($bytes / 1024)) . ' KB';
?>

<a href="<?= e(admin_url('media')) ?>" class="inline-flex items-center gap-2 t-meta font-semibold text-muted transition-colors hover:text-navy-700">
  <?= icon('chevron-left', 'h-4 w-4') ?>All images
</a>

<div class="mt-5 grid gap-6 lg:grid-cols-5">

  <!-- Preview -->
  <div class="lg:col-span-3">
    <div class="card overflow-hidden">
      <div class="bg-navy-100">
        <img src="<?= e($media['path']) ?>" alt="<?= e($media['alt']) ?>"
             class="max-h-[32rem] w-full object-contain">
      </div>
      <div class="flex flex-wrap items-center justify-between gap-3 border-t border-hairline px-5 py-3">
        <code class="t-meta truncate text-muted"><?= e($media['path']) ?></code>
        <a href="<?= e($media['path']) ?>" target="_blank" rel="noopener"
           class="inline-flex shrink-0 items-center gap-1.5 t-meta font-semibold text-navy-700 hover:text-gold-600">
          Open full size <?= icon('arrow-up-right', 'h-3.5 w-3.5') ?>
        </a>
      </div>
    </div>
  </div>

  <!-- Details -->
  <div class="grid content-start gap-6 lg:col-span-2">

    <form method="post" action="<?= e(admin_url('media/' . $media['id'])) ?>" class="card p-6">
      <?= csrf_field() ?>
      <h2 class="t-h3 text-[1.0625rem]">Description</h2>

      <div class="mt-4">
        <label class="field-label" for="alt">
          What is in the picture <span class="text-signal-600">*</span>
        </label>
        <textarea class="field h-auto py-3" id="alt" name="alt" rows="3" required
                  maxlength="255"><?= e($media['alt']) ?></textarea>
        <p class="mt-1.5 t-meta text-muted">
          Read aloud to anyone using a screen reader, and shown if the image fails to
          load. Describe the subject, not the file.
        </p>
      </div>

      <div class="mt-4">
        <label class="field-label" for="title">Internal note</label>
        <input class="field" id="title" name="title" maxlength="190"
               value="<?= e((string) ($media['title'] ?? '')) ?>"
               placeholder="Only staff see this">
      </div>

      <button type="submit" class="btn btn-primary mt-5 w-full">Save</button>
    </form>

    <section class="card p-6">
      <h2 class="t-h3 text-[1.0625rem]">Where it is used</h2>

      <?php if ($usage === []): ?>
        <p class="mt-3 text-[0.9375rem] text-muted">
          Not used anywhere yet. It is available to any property or article from the
          library.
        </p>
      <?php else: ?>
        <ul class="mt-4 divide-y divide-hairline">
          <?php foreach ($usage as $use): ?>
            <li class="py-2.5 first:pt-0 last:pb-0">
              <a href="<?= e($use['href']) ?>" class="group block">
                <span class="block truncate text-[0.9375rem] font-medium text-navy-700 group-hover:text-gold-600">
                  <?= e($use['label']) ?>
                </span>
                <span class="block t-meta text-muted"><?= e($use['context']) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

    <section class="card p-6">
      <h2 class="t-h3 text-[1.0625rem]">File</h2>
      <dl class="mt-4 space-y-2.5 t-meta">
        <div class="flex justify-between gap-3">
          <dt class="text-muted">Dimensions</dt>
          <dd class="tabular text-navy-700"><?= (int) $media['width'] ?> &times; <?= (int) $media['height'] ?> px</dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted">Size</dt>
          <dd class="tabular text-navy-700"><?= e($size) ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted">Type</dt>
          <dd class="text-navy-700"><?= e((string) ($media['mime'] ?? 'unknown')) ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted">Added</dt>
          <dd class="text-navy-700"><?= e(nice_date($media['created_at'], 'j M Y')) ?></dd>
        </div>
      </dl>
    </section>

    <?php if (can('media.delete')): ?>
      <section class="card <?= $usage === [] ? 'border-signal-600/30' : '' ?> p-6">
        <h2 class="t-h3 text-[1.0625rem] <?= $usage === [] ? 'text-signal-600' : '' ?>">Delete</h2>

        <?php if ($usage !== []): ?>
          <p class="mt-2 t-meta text-muted">
            This image is in use. Remove it from the
            <?= count($usage) === 1 ? 'item' : (count($usage) . ' items') ?> above first,
            so nothing is left with a missing picture.
          </p>
          <button type="button" disabled
                  class="btn mt-4 w-full cursor-not-allowed border border-hairline text-muted opacity-60">
            Cannot delete while in use
          </button>
        <?php else: ?>
          <p class="mt-2 t-meta text-muted">
            Removes the record and the file itself. This cannot be undone.
          </p>
          <form method="post" action="<?= e(admin_url('media/' . $media['id'] . '/delete')) ?>" class="mt-4">
            <?= csrf_field() ?>
            <button type="submit"
                    onclick="return confirm('Delete this image permanently?')"
                    class="btn w-full border border-signal-600 text-signal-600 hover:bg-signal-600 hover:text-white">
              Delete image
            </button>
          </form>
        <?php endif; ?>
      </section>
    <?php endif; ?>
  </div>
</div>
