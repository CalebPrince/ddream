<?php
declare(strict_types=1);

/** @var array $rows @var int $total @var int $page @var int $pages @var array $filters @var array $counts */

$tabs = [
    ''          => ['All',       $counts['all']],
    'published' => ['Published', $counts['published']],
    'draft'     => ['Drafts',    $counts['draft']],
    'archived'  => ['Archived',  $counts['archived']],
];

/** Keep the other filters when one changes. */
$link = static function (array $overrides) use ($filters): string {
    $query = array_filter([
        'state'    => $overrides['state']    ?? $filters['state'],
        'basis'    => $overrides['basis']    ?? $filters['basis'],
        'category' => $overrides['category'] ?? $filters['category'],
        'q'        => $overrides['q']        ?? $filters['search'],
        'page'     => $overrides['page']     ?? null,
    ], static fn ($v): bool => $v !== '' && $v !== null);

    return admin_url('listings') . ($query === [] ? '' : '?' . http_build_query($query));
};
?>

<!-- State tabs -->
<div class="rail -mx-5 overflow-x-auto px-5 lg:mx-0 lg:px-0">
  <ul class="flex min-w-max items-center gap-1 border-b border-hairline">
    <?php foreach ($tabs as $key => [$label, $count]): ?>
      <?php $on = $filters['state'] === $key; ?>
      <li>
        <a href="<?= e($link(['state' => $key, 'page' => null])) ?>"
           class="relative flex items-center gap-2 px-4 py-3 text-[0.875rem] font-semibold transition-colors <?= $on ? 'text-navy-700' : 'text-muted hover:text-navy-600' ?>"
           <?= $on ? 'aria-current="page"' : '' ?>>
          <?= e($label) ?>
          <span class="tabular rounded-[2px] bg-navy-100 px-1.5 py-0.5 text-[0.6875rem] font-semibold text-navy-700"><?= (int) $count ?></span>
          <?php if ($on): ?><span class="absolute inset-x-3 -bottom-px h-0.5 bg-gold-500"></span><?php endif; ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<!-- Filters -->
<form method="get" action="<?= e(admin_url('listings')) ?>" class="mt-5 flex flex-wrap items-end gap-3">
  <input type="hidden" name="state" value="<?= e($filters['state']) ?>">

  <div class="min-w-[14rem] flex-1">
    <label class="field-label" for="q">Search</label>
    <input class="field" id="q" name="q" type="search" value="<?= e($filters['search']) ?>"
           placeholder="Title, address or reference">
  </div>

  <div class="w-[11rem]">
    <label class="field-label" for="basis">Section</label>
    <select class="field" id="basis" name="basis">
      <option value="">All sections</option>
      <?php foreach (LISTING_BASES as $b): ?>
        <option value="<?= e($b) ?>" <?= $filters['basis'] === $b ? 'selected' : '' ?>><?= e($b) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="w-[11rem]">
    <label class="field-label" for="category">Type</label>
    <select class="field" id="category" name="category">
      <option value="">All types</option>
      <?php foreach (LISTING_CATEGORIES as $key => $label): ?>
        <option value="<?= e($key) ?>" <?= $filters['category'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="submit" class="btn btn-primary">Filter</button>
  <?php if (array_filter([$filters['basis'], $filters['category'], $filters['search']])): ?>
    <a href="<?= e($link(['basis' => '', 'category' => '', 'q' => '', 'page' => null])) ?>"
       class="btn btn-outline">Clear</a>
  <?php endif; ?>
</form>

<!-- Table -->
<?php if ($rows === []): ?>
  <div class="card mt-6 p-12 text-center">
    <span class="inline-flex h-12 w-12 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700">
      <?= icon('key', 'h-5 w-5') ?>
    </span>
    <h2 class="t-h3 mt-4">Nothing here</h2>
    <p class="mx-auto mt-2 max-w-sm text-[0.9375rem] text-muted">
      <?= $filters['search'] !== '' || $filters['basis'] !== '' || $filters['category'] !== ''
          ? 'No property matches those filters.'
          : 'No property in this view yet.' ?>
    </p>
    <?php if (can('listings.edit')): ?>
      <a href="<?= e(admin_url('listings/new')) ?>" class="btn btn-primary mt-6">Add property</a>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="card mt-6 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[52rem] text-left">
        <thead>
          <tr class="border-b border-hairline bg-canvas">
            <th class="px-4 py-3 text-[0.6875rem] font-bold uppercase tracking-[0.1em] text-muted">Property</th>
            <th class="px-4 py-3 text-[0.6875rem] font-bold uppercase tracking-[0.1em] text-muted">Section</th>
            <th class="px-4 py-3 text-[0.6875rem] font-bold uppercase tracking-[0.1em] text-muted">Price</th>
            <th class="px-4 py-3 text-[0.6875rem] font-bold uppercase tracking-[0.1em] text-muted">State</th>
            <th class="px-4 py-3 text-[0.6875rem] font-bold uppercase tracking-[0.1em] text-muted">Updated</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-hairline">
          <?php foreach ($rows as $row): ?>
            <?php
            $isLive     = $row['published_at'] !== null && $row['archived_at'] === null;
            $isArchived = $row['archived_at'] !== null;
            ?>
            <tr class="transition-colors hover:bg-canvas">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <span class="h-11 w-14 shrink-0 overflow-hidden rounded-[4px] bg-navy-100">
                    <?php if (!empty($row['cover_path'])): ?>
                      <img src="<?= e($row['cover_path']) ?>" alt="" class="h-full w-full object-cover">
                    <?php endif; ?>
                  </span>
                  <span class="min-w-0">
                    <a href="<?= e(admin_url('listings/' . $row['id'])) ?>"
                       class="block truncate text-[0.9375rem] font-semibold text-navy-700 hover:text-gold-600">
                      <?= e($row['title']) ?>
                    </a>
                    <span class="block truncate t-meta text-muted">
                      <span class="tabular"><?= e($row['ref']) ?></span> &middot; <?= e($row['address']) ?>
                    </span>
                  </span>
                </div>
              </td>
              <td class="px-4 py-3">
                <span class="block text-[0.875rem] text-navy-700"><?= e($row['basis']) ?></span>
                <span class="block t-meta text-muted"><?= e(LISTING_CATEGORIES[$row['category']] ?? $row['category']) ?></span>
              </td>
              <td class="tabular px-4 py-3 text-[0.9375rem] font-semibold text-navy-700">
                <?= e(money((float) $row['price'], $row['currency'])) ?>
                <?php if ($row['period']): ?>
                  <span class="block t-meta font-normal text-muted"><?= e($row['period']) ?></span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3">
                <?php if ($isArchived): ?>
                  <span class="badge border border-hairline bg-canvas text-muted">Archived</span>
                <?php elseif ($isLive): ?>
                  <span class="badge bg-verified text-white">Live</span>
                <?php else: ?>
                  <span class="badge border border-gold-500 bg-gold-100 text-gold-600">Draft</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 t-meta text-muted"><?= e(time_ago($row['updated_at'] ?? $row['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if ($pages > 1): ?>
    <nav class="mt-5 flex items-center justify-between gap-4" aria-label="Pages">
      <p class="t-meta text-muted">
        Page <?= (int) $page ?> of <?= (int) $pages ?>, <?= (int) $total ?> properties
      </p>
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
