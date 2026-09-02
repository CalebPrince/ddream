<?php
declare(strict_types=1);

/**
 * One page: its search and sharing details, then a card per band.
 *
 * @var array $page     the pages row
 * @var array $sections page_sections rows, each with `fields`, `values`, `locked`
 * @var array $media    the image library, for the photograph pickers
 */

$imagePaths = array_values(array_unique(array_column($media, 'path')));

/** One field, drawn from its schema definition and current value. */
$renderField = static function (array $field, string $name, mixed $value) use ($imagePaths): void {
    $id    = 'f-' . preg_replace('/[^a-z0-9]+/i', '-', $name);
    $help  = $field['help'] ?? null;
    $label = $field['label'];
    ?>
    <div>
      <label class="field-label" for="<?= e($id) ?>"><?= e($label) ?></label>

      <?php if ($field['type'] === 'text' || $field['type'] === 'rich'): ?>
        <textarea class="field h-auto py-3" id="<?= e($id) ?>" name="<?= e($name) ?>" rows="3"><?= e(is_string($value) ? $value : '') ?></textarea>
        <?php if ($field['type'] === 'rich'): ?>
          <p class="mt-1.5 t-meta text-muted">
            You may use &lt;strong&gt;bold&lt;/strong&gt;, &lt;em&gt;italics&lt;/em&gt;,
            &lt;br&gt; for a line break and a link.
          </p>
        <?php endif; ?>

      <?php elseif ($field['type'] === 'lines'): ?>
        <textarea class="field h-auto py-3" id="<?= e($id) ?>" name="<?= e($name) ?>" rows="6"><?= e(implode("\n", array_map('strval', (array) $value))) ?></textarea>

      <?php elseif ($field['type'] === 'image'): ?>
        <input class="field" id="<?= e($id) ?>" name="<?= e($name) ?>" list="mediaPaths"
               value="<?= e(is_string($value) ? $value : '') ?>" placeholder="/images/...">

      <?php else: ?>
        <input class="field" id="<?= e($id) ?>" name="<?= e($name) ?>" maxlength="500"
               value="<?= e(is_string($value) ? $value : '') ?>">
      <?php endif; ?>

      <?php if ($help): ?>
        <p class="mt-1.5 t-meta text-muted"><?= e($help) ?></p>
      <?php endif; ?>
    </div>
    <?php
};
?>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
  <a class="text-sm font-semibold text-navy-700" href="<?= e(admin_url('pages')) ?>">&larr; Back to pages</a>
  <a class="text-sm font-semibold text-navy-700" target="_blank" rel="noopener"
     href="/<?= e($page['slug'] === 'home' ? '' : $page['slug']) ?>">View this page &nearr;</a>
</div>

<form method="post" action="<?= e(admin_url('pages/' . $page['id'])) ?>" class="space-y-6">
  <?= csrf_field() ?>

  <datalist id="mediaPaths">
    <?php foreach ($imagePaths as $path): ?>
      <option value="<?= e($path) ?>"></option>
    <?php endforeach; ?>
  </datalist>

  <section class="card p-6">
    <h2 class="t-h3 text-[1.0625rem]">Search and sharing</h2>
    <div class="mt-5 grid gap-5">
      <div>
        <label class="field-label" for="page-title">Browser title</label>
        <input class="field" id="page-title" name="title" maxlength="190" value="<?= e($page['title'] ?? '') ?>">
        <p class="mt-1.5 t-meta text-muted">The whole title shown in the browser tab and in search results.</p>
      </div>
      <div>
        <label class="field-label" for="page-meta">Meta description</label>
        <textarea class="field h-auto py-3" id="page-meta" name="meta_description" maxlength="255" rows="3"><?= e($page['meta_description'] ?? '') ?></textarea>
        <p class="mt-1.5 t-meta text-muted">Up to 255 characters. Shown under the title in search results.</p>
      </div>
      <div>
        <label class="field-label" for="page-og">Social image</label>
        <select class="field" id="page-og" name="og_image_id">
          <option value="0">None</option>
          <?php foreach ($media as $image): ?>
            <option value="<?= (int) $image['id'] ?>" <?= (int) $page['og_image_id'] === (int) $image['id'] ? 'selected' : '' ?>>
              <?= e(($image['alt'] !== '' ? $image['alt'] . ' – ' : '') . $image['path']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="mt-1.5 t-meta text-muted">Used when someone shares this page on social media.</p>
      </div>
    </div>
  </section>

  <?php foreach ($sections as $section): ?>
    <?php $prefix = 'section[' . (int) $section['id'] . ']'; ?>
    <section class="card p-6">

      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h2 class="font-sans text-base font-semibold text-navy-700"><?= e($section['name']) ?></h2>
          <p class="t-meta text-muted"><?= e($section['section_key']) ?></p>
        </div>

        <?php if ($section['locked']): ?>
          <span class="t-meta text-muted">Always shown</span>
        <?php else: ?>
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="section_enabled[<?= (int) $section['id'] ?>]"
                   class="h-4 w-4 rounded-[2px] border-hairline text-navy-700 focus:ring-navy-700"
                   <?= $section['enabled'] ? 'checked' : '' ?>>
            Show this section
          </label>
        <?php endif; ?>
      </div>

      <?php if ($section['fields'] === []): ?>
        <p class="mt-5 rounded-[6px] border border-dashed border-hairline bg-canvas p-4 t-meta text-muted">
          This band has no editable fields yet.
        </p>
      <?php endif; ?>

      <div class="mt-5 grid gap-5">
        <?php foreach ($section['fields'] as $field): ?>
          <?php $value = $section['values'][$field['key']] ?? ''; ?>

          <?php if ($field['type'] !== 'list'): ?>
            <?php $renderField($field, $prefix . '[' . $field['key'] . ']', $value); ?>

          <?php else: ?>
            <?php
            $rows = array_values(array_filter((array) $value, 'is_array'));
            $max  = (int) ($field['max'] ?? 0);
            // One spare row unless the list is already at its limit, so a new
            // entry can be typed straight in. Clearing every box removes a row.
            if ($max === 0 || count($rows) < $max) {
                $rows[] = [];
            }
            ?>
            <fieldset class="rounded-[6px] border border-hairline bg-canvas p-4">
              <legend class="field-label px-1"><?= e($field['label']) ?></legend>

              <?php if (!empty($field['help'])): ?>
                <p class="mb-3 t-meta text-muted"><?= e($field['help']) ?></p>
              <?php endif; ?>

              <div class="grid gap-4">
                <?php foreach ($rows as $index => $row): ?>
                  <div class="grid gap-3 rounded-[4px] border border-hairline bg-surface p-3 sm:grid-cols-2">
                    <?php foreach ($field['item'] as $column): ?>
                      <?php
                      $name = $prefix . '[' . $field['key'] . '][' . $index . '][' . $column['key'] . ']';
                      $cell = $row[$column['key']] ?? '';
                      ?>
                      <?php if ($column['type'] === 'icon'): ?>
                        <div>
                          <label class="field-label"><?= e($column['label']) ?></label>
                          <select class="field" name="<?= e($name) ?>">
                            <option value="" <?= $cell === '' ? 'selected' : '' ?>>None</option>
                            <?php foreach (CONTENT_ICONS as $iconName): ?>
                              <option value="<?= e($iconName) ?>" <?= $cell === $iconName ? 'selected' : '' ?>><?= e($iconName) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      <?php else: ?>
                        <div class="<?= $column['type'] === 'text' ? 'sm:col-span-2' : '' ?>">
                          <?php $renderField($column, $name, $cell); ?>
                        </div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                <?php endforeach; ?>
              </div>

              <p class="mt-3 t-meta text-muted">
                The last row is blank so you can add one. Clear every box in a row to remove it.
              </p>
            </fieldset>
          <?php endif; ?>

        <?php endforeach; ?>
      </div>
    </section>
  <?php endforeach; ?>

  <div class="sticky bottom-0 -mx-1 border-t border-hairline bg-canvas/95 px-1 py-4 backdrop-blur">
    <button class="btn btn-primary">Save page</button>
  </div>
</form>
