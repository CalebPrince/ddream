<?php
declare(strict_types=1);

/** @var array $rows @var int $total @var int $page @var int $pages @var string $search @var string $filter @var array $counts */

$tabs = [
    ''       => ['Everything', $counts['all']],
    'no-alt' => ['Needs a description', $counts['noAlt']],
    'unused' => ['Not used anywhere', $counts['unused']],
];

$link = static function (array $overrides) use ($search, $filter): string {
    $query = array_filter([
        'filter' => $overrides['filter'] ?? $filter,
        'q'      => $overrides['q']      ?? $search,
        'page'   => $overrides['page']   ?? null,
    ], static fn ($v): bool => $v !== '' && $v !== null);

    return admin_url('media') . ($query === [] ? '' : '?' . http_build_query($query));
};
?>

<!-- Upload -->
<form method="post" action="<?= e(admin_url('media/upload')) ?>" enctype="multipart/form-data"
      class="card p-5">
  <?= csrf_field() ?>
  <div class="flex flex-wrap items-end gap-4">
    <div class="min-w-[16rem] flex-1">
      <label class="field-label" for="images">Add images</label>
      <input class="field h-auto py-2.5" id="images" name="images[]" type="file"
             accept="image/jpeg,image/png,image/webp" multiple required>
    </div>
    <div class="min-w-[16rem] flex-1">
      <label class="field-label" for="alt">Description</label>
      <input class="field" id="alt" name="alt" maxlength="255"
             placeholder="What is in the picture">
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
  </div>
  <p class="mt-3 t-meta text-muted">
    JPG, PNG or WebP, up to 8MB each.
    <?= gd_available()
        ? 'Anything wider than 2400px is resized automatically.'
        : 'This server cannot resize images, so upload them already sized.' ?>
    The description is used by screen readers and search engines, so it is worth writing.
  </p>
</form>

<!-- Filters -->
<div class="mt-6 rail -mx-5 overflow-x-auto px-5 lg:mx-0 lg:px-0">
  <ul class="flex min-w-max items-center gap-1 border-b border-hairline">
    <?php foreach ($tabs as $key => [$label, $count]): ?>
      <?php $on = $filter === $key; ?>
      <li>
        <a href="<?= e($link(['filter' => $key, 'page' => null])) ?>"
           class="relative flex items-center gap-2 px-4 py-3 text-[0.875rem] font-semibold transition-colors <?= $on ? 'text-navy-700' : 'text-muted hover:text-navy-600' ?>"
           <?= $on ? 'aria-current="page"' : '' ?>>
          <?= e($label) ?>
          <span class="tabular rounded-[2px] px-1.5 py-0.5 text-[0.6875rem] font-semibold <?= $key === 'no-alt' && $count > 0 ? 'bg-signal-600 text-white' : 'bg-navy-100 text-navy-700' ?>">
            <?= (int) $count ?>
          </span>
          <?php if ($on): ?><span class="absolute inset-x-3 -bottom-px h-0.5 bg-gold-500"></span><?php endif; ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<form method="get" action="<?= e(admin_url('media')) ?>" class="mt-5 flex flex-wrap items-end gap-3">
  <input type="hidden" name="filter" value="<?= e($filter) ?>">
  <div class="min-w-[16rem] flex-1">
    <label class="field-label" for="q">Search</label>
    <input class="field" id="q" name="q" type="search" value="<?= e($search) ?>"
           placeholder="Description or filename">
  </div>
  <button type="submit" class="btn btn-primary">Search</button>
  <?php if ($search !== ''): ?>
    <a href="<?= e($link(['q' => '', 'page' => null])) ?>" class="btn btn-outline">Clear</a>
  <?php endif; ?>
</form>

<!-- Grid -->
<?php if ($rows === []): ?>
  <div class="card mt-6 p-12 text-center">
    <span class="inline-flex h-12 w-12 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700">
      <?= icon('camera', 'h-5 w-5') ?>
    </span>
    <h2 class="t-h3 mt-4"><?= $search !== '' || $filter !== '' ? 'Nothing matches' : 'The library is empty' ?></h2>
    <p class="mx-auto mt-2 max-w-sm text-[0.9375rem] text-muted">
      <?= $search !== '' || $filter !== ''
          ? 'Try a different search, or clear the filter.'
          : 'Upload an image above and it becomes available to every property and article.' ?>
    </p>
  </div>
<?php else: ?>
  <ul class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6">
    <?php foreach ($rows as $row): ?>
      <li>
        <a href="<?= e(admin_url('media/' . $row['id'])) ?>"
           class="card card-interactive group block overflow-hidden">
          <span class="relative block aspect-square overflow-hidden bg-navy-100">
            <img src="<?= e($row['path']) ?>" alt="<?= e($row['alt']) ?>" loading="lazy"
                 class="card-zoom h-full w-full object-cover">
            <?php if (($row['alt'] ?? '') === ''): ?>
              <span class="badge absolute left-2 top-2 bg-signal-600 text-white">No description</span>
            <?php elseif ((int) $row['uses'] === 0): ?>
              <span class="badge absolute left-2 top-2 border border-hairline bg-surface/95 text-muted">Unused</span>
            <?php endif; ?>
          </span>
          <span class="block p-3">
            <span class="block truncate text-[0.8125rem] font-medium text-navy-700">
              <?= e($row['alt'] !== '' ? $row['alt'] : basename($row['path'])) ?>
            </span>
            <span class="mt-0.5 flex items-center justify-between gap-2 t-meta text-muted">
              <span class="tabular"><?= (int) $row['width'] ?>&times;<?= (int) $row['height'] ?></span>
              <span><?= (int) $row['uses'] ?> use<?= (int) $row['uses'] === 1 ? '' : 's' ?></span>
            </span>
          </span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if ($pages > 1): ?>
    <nav class="mt-6 flex items-center justify-between gap-4" aria-label="Pages">
      <p class="t-meta text-muted">Page <?= (int) $page ?> of <?= (int) $pages ?>, <?= (int) $total ?> images</p>
      <div class="flex gap-2">
        <?php if ($page > 1): ?>
          <a href="<?= e($link(['page' => (string) ($page - 1)])) ?>" class="btn btn-outline h-10 px-3 text-[0.8125rem]">Previous</a>
        <?php endif; ?>
        <?php if ($page < $pages): ?>
          <a href="<?= e($link(['page' => (string) ($page + 1)])) ?>" class="btn btn-outline h-10 px-3 text-[0.8125rem]">Next</a>
        <?php endif; ?>
      </div>
    </nav>
  <?php endif; ?>
<?php endif; ?>
