<?php
declare(strict_types=1);

/** @var array|null $listing @var array $features @var array $images @var array $locations @var string $nextRef @var array $library */
$library ??= [];

$isNew      = $listing === null;
$action     = $isNew ? admin_url('listings') : admin_url('listings/' . $listing['id']);
$isLive     = !$isNew && $listing['published_at'] !== null && $listing['archived_at'] === null;
$isArchived = !$isNew && $listing['archived_at'] !== null;

/** Prefer what the user just typed, then the stored row, then a default. */
$val = static function (string $key, $fallback = '') use ($listing): string {
    $old = $_SESSION['old'][$key] ?? null;
    if ($old !== null) {
        return (string) $old;
    }

    return (string) ($listing[$key] ?? $fallback);
};

$featureText = $_SESSION['old']['features']
    ?? implode("\n", array_column($features, 'label'));
?>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-3">
  <?= csrf_field() ?>

  <!-- Main -->
  <div class="grid gap-6 xl:col-span-2">

    <section class="card p-6">
      <h2 class="t-h3 text-[1.0625rem]">The basics</h2>

      <div class="mt-5 grid gap-5">
        <div>
          <label class="field-label" for="title">Title <span class="text-signal-600">*</span></label>
          <input class="field" id="title" name="title" required maxlength="190"
                 value="<?= e($val('title')) ?>" placeholder="Four-bed Family House, East Legon">
        </div>

        <div>
          <label class="field-label" for="address">Address <span class="text-signal-600">*</span></label>
          <input class="field" id="address" name="address" required maxlength="190"
                 value="<?= e($val('address')) ?>" placeholder="East Legon, Accra">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
          <div>
            <label class="field-label" for="basis">Section <span class="text-signal-600">*</span></label>
            <select class="field" id="basis" name="basis" required>
              <?php foreach (LISTING_BASES as $b): ?>
                <option value="<?= e($b) ?>" <?= $val('basis', 'For sale') === $b ? 'selected' : '' ?>><?= e($b) ?></option>
              <?php endforeach; ?>
            </select>
            <p class="mt-1.5 t-meta text-muted">Which part of the site it appears in.</p>
          </div>

          <div>
            <label class="field-label" for="category">Property type <span class="text-signal-600">*</span></label>
            <select class="field" id="category" name="category" required>
              <?php foreach (LISTING_CATEGORIES as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= $val('category', 'houses') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
            <p class="mt-1.5 t-meta text-muted">Rentals have no Land; Airbnb is houses and apartments only.</p>
          </div>
        </div>

        <div>
          <label class="field-label" for="location_id">Area</label>
          <select class="field" id="location_id" name="location_id">
            <option value="">Not set</option>
            <?php foreach ($locations as $location): ?>
              <option value="<?= (int) $location['id'] ?>" <?= $val('location_id') === (string) $location['id'] ? 'selected' : '' ?>>
                <?= e($location['name']) ?>, <?= e($location['city']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </section>

    <section class="card p-6">
      <h2 class="t-h3 text-[1.0625rem]">Price and size</h2>

      <div class="mt-5 grid gap-5 sm:grid-cols-3">
        <div>
          <label class="field-label" for="price">Price <span class="text-signal-600">*</span></label>
          <input class="field tabular" id="price" name="price" type="number" min="0" step="1" required
                 value="<?= e($val('price')) ?>">
        </div>
        <div>
          <label class="field-label" for="currency">Currency</label>
          <select class="field" id="currency" name="currency">
            <?php foreach (['USD' => 'US dollars', 'GHS' => 'Cedis', 'GBP' => 'Pounds', 'EUR' => 'Euros'] as $code => $label): ?>
              <option value="<?= e($code) ?>" <?= $val('currency', 'USD') === $code ? 'selected' : '' ?>><?= e($code) ?> &middot; <?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="field-label" for="period">Period</label>
          <select class="field" id="period" name="period">
            <option value="" <?= $val('period') === '' ? 'selected' : '' ?>>One-off price</option>
            <option value="per month" <?= $val('period') === 'per month' ? 'selected' : '' ?>>per month</option>
            <option value="per night" <?= $val('period') === 'per night' ? 'selected' : '' ?>>per night</option>
          </select>
          <p class="mt-1.5 t-meta text-muted">Leave as one-off for sales.</p>
        </div>
      </div>

      <div class="mt-5 grid gap-5 sm:grid-cols-4">
        <div>
          <label class="field-label" for="beds">Bedrooms</label>
          <input class="field tabular" id="beds" name="beds" type="number" min="0" max="20" value="<?= e($val('beds')) ?>">
        </div>
        <div>
          <label class="field-label" for="baths">Bathrooms</label>
          <input class="field tabular" id="baths" name="baths" type="number" min="0" max="20" value="<?= e($val('baths')) ?>">
        </div>
        <div>
          <label class="field-label" for="area">Area, m&sup2;</label>
          <input class="field tabular" id="area" name="area" type="number" min="0" value="<?= e($val('area')) ?>">
        </div>
        <div>
          <label class="field-label" for="status">Status label</label>
          <input class="field" id="status" name="status" maxlength="32" list="statusOptions"
                 value="<?= e($val('status')) ?>" placeholder="New build">
          <datalist id="statusOptions">
            <?php foreach (['New build', 'Off-plan', 'Resale', 'Furnished', 'Unfurnished', 'Serviced', 'Titled', 'Whole home', 'Shell', 'Fitted'] as $s): ?>
              <option value="<?= e($s) ?>"></option>
            <?php endforeach; ?>
          </datalist>
        </div>
      </div>
      <p class="mt-2 t-meta text-muted">Leave bedrooms and bathrooms blank for land.</p>
    </section>

    <section class="card p-6">
      <h2 class="t-h3 text-[1.0625rem]">Description</h2>

      <div class="mt-5 grid gap-5">
        <div>
          <label class="field-label" for="summary">Card summary</label>
          <textarea class="field h-auto py-3" id="summary" name="summary" rows="3"
                    placeholder="Two or three sentences. This is what appears on the search results card."><?= e($val('summary')) ?></textarea>
        </div>

        <div>
          <label class="field-label" for="features">Features</label>
          <textarea class="field h-auto py-3" id="features" name="features" rows="5"
                    placeholder="One per line&#10;Standby generator&#10;Staff quarters"><?= e($featureText) ?></textarea>
          <p class="mt-1.5 t-meta text-muted">One per line, up to twelve. These become the gold tags on the card.</p>
        </div>
      </div>
    </section>

    <!-- Images -->
    <section class="card p-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="t-h3 text-[1.0625rem]">Photographs</h2>
        <?php if (!gd_available()): ?>
          <span class="badge border border-gold-500 bg-gold-100 text-gold-600">Resizing unavailable</span>
        <?php endif; ?>
      </div>

      <?php if ($images !== []): ?>
        <ul class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
          <?php foreach ($images as $image): ?>
            <?php $isCover = !$isNew && (int) $listing['cover_id'] === (int) $image['id']; ?>
            <li class="group relative overflow-hidden rounded-[6px] border <?= $isCover ? 'border-gold-500' : 'border-hairline' ?>">
              <img src="<?= e($image['path']) ?>" alt="<?= e($image['alt']) ?>"
                   class="aspect-[4/3] w-full object-cover">
              <?php if ($isCover): ?>
                <span class="badge absolute left-2 top-2 bg-gold-500 text-navy-900">Cover</span>
              <?php endif; ?>
              <div class="flex items-center justify-between gap-1 border-t border-hairline bg-surface p-2">
                <?php if (!$isCover): ?>
                  <button type="submit" formaction="<?= e(admin_url('listings/' . $listing['id'] . '/images/' . $image['id'] . '/cover')) ?>" formnovalidate
                          class="t-meta font-semibold text-navy-700 hover:text-gold-600">Make cover</button>
                <?php else: ?>
                  <span class="t-meta text-muted">Shown on cards</span>
                <?php endif; ?>
                <button type="submit" formaction="<?= e(admin_url('listings/' . $listing['id'] . '/images/' . $image['id'] . '/remove')) ?>" formnovalidate
                        class="t-meta font-semibold text-signal-600 hover:underline">Remove</button>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <?php if (!$isNew && $library !== []): ?>
        <details class="group mt-5 rounded-[6px] border border-hairline">
          <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3">
            <span class="flex items-center gap-2.5 text-[0.9375rem] font-semibold text-navy-700">
              <?= icon('layers', 'h-[18px] w-[18px] text-gold-600') ?>
              Use an image already in the library
            </span>
            <?= icon('chevron-down', 'h-4 w-4 shrink-0 text-muted transition-transform group-open:rotate-180') ?>
          </summary>

          <div class="border-t border-hairline p-4">
            <p class="t-meta text-muted">
              Pick one or more, then add them. The same file is reused rather than
              uploaded again.
            </p>

            <ul class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-4 lg:grid-cols-6">
              <?php foreach ($library as $item): ?>
                <li>
                  <label class="group/pick relative block cursor-pointer overflow-hidden rounded-[4px] border border-hairline">
                    <input type="checkbox" name="media_ids[]" value="<?= (int) $item['id'] ?>"
                           form="attachForm" class="peer sr-only">
                    <img src="<?= e($item['path']) ?>" alt="<?= e($item['alt']) ?>" loading="lazy"
                         class="aspect-square w-full object-cover transition-opacity peer-checked:opacity-100 opacity-80 group-hover/pick:opacity-100">
                    <span class="pointer-events-none absolute inset-0 border-2 border-transparent peer-checked:border-gold-500"></span>
                    <span class="pointer-events-none absolute right-1.5 top-1.5 hidden h-5 w-5 items-center justify-center rounded-full bg-gold-500 text-navy-900 peer-checked:flex">
                      <?= icon('check', 'h-3 w-3') ?>
                    </span>
                  </label>
                </li>
              <?php endforeach; ?>
            </ul>

            <div class="mt-4 flex flex-wrap items-center gap-3">
              <button type="submit" form="attachForm" class="btn btn-outline">Add selected</button>
              <a href="<?= e(admin_url('media')) ?>" class="t-meta font-semibold text-gold-600 hover:underline">
                Open the full library
              </a>
            </div>
          </div>
        </details>
      <?php endif; ?>

      <div class="mt-5">
        <label class="field-label" for="images">Upload new photographs</label>
        <input class="field h-auto py-2.5" id="images" name="images[]" type="file"
               accept="image/jpeg,image/png,image/webp" multiple>
        <p class="mt-1.5 t-meta text-muted">
          JPG, PNG or WebP, up to 8MB each.
          <?= gd_available()
              ? 'Anything wider than 2400px is resized automatically.'
              : 'This server cannot resize images, so upload them already sized.' ?>
        </p>
      </div>
    </section>
  </div>

  <!-- Sidebar -->
  <div class="grid content-start gap-6">

    <section class="card p-6">
      <div class="flex items-center justify-between gap-3">
        <h2 class="t-h3 text-[1.0625rem]">Publishing</h2>
        <?php if ($isArchived): ?>
          <span class="badge border border-hairline bg-canvas text-muted">Archived</span>
        <?php elseif ($isLive): ?>
          <span class="badge bg-verified text-white">Live</span>
        <?php else: ?>
          <span class="badge border border-gold-500 bg-gold-100 text-gold-600">Draft</span>
        <?php endif; ?>
      </div>

      <dl class="mt-4 space-y-2 t-meta">
        <div class="flex justify-between gap-3">
          <dt class="text-muted">Reference</dt>
          <dd class="tabular font-semibold text-navy-700"><?= e($nextRef) ?></dd>
        </div>
        <?php if (!$isNew): ?>
          <div class="flex justify-between gap-3">
            <dt class="text-muted">Last updated</dt>
            <dd class="text-navy-700"><?= e(time_ago($listing['updated_at'] ?? $listing['created_at'])) ?></dd>
          </div>
        <?php endif; ?>
      </dl>

      <div class="mt-5">
        <label class="field-label" for="slug">Address on the site</label>
        <div class="flex items-center gap-1 rounded-[4px] border border-hairline bg-canvas px-2">
          <span class="shrink-0 t-meta text-muted">/property/</span>
          <input class="field border-0 bg-transparent px-1 focus:shadow-none" id="slug" name="slug"
                 value="<?= e($val('slug')) ?>" maxlength="200"
                 placeholder="<?= e($isNew ? 'made from the title' : '') ?>">
        </div>
        <p class="mt-1.5 t-meta text-muted">
          <?= $isNew
              ? 'Leave blank and we will build it from the title.'
              : 'Changing this changes the public address. Anyone with the old link will still arrive, but only if it is a reference.' ?>
        </p>
      </div>

      <label class="mt-5 flex items-start gap-3 text-[0.9375rem] text-navy-700">
        <input type="checkbox" name="featured" value="1" <?= $val('featured', '0') === '1' ? 'checked' : '' ?>
               class="mt-1 h-4 w-4 shrink-0 rounded-[2px] border-hairline text-navy-700 focus:ring-navy-700">
        <span>Feature on the home page
          <span class="block t-meta text-muted">The home page shows three.</span>
        </span>
      </label>

      <label class="mt-3 flex items-start gap-3 text-[0.9375rem] text-navy-700">
        <input type="checkbox" name="verified" value="1" <?= $val('verified', '1') === '1' ? 'checked' : '' ?>
               class="mt-1 h-4 w-4 shrink-0 rounded-[2px] border-hairline text-navy-700 focus:ring-navy-700">
        <span>Checks complete
          <span class="block t-meta text-muted">Shows the green verified badge.</span>
        </span>
      </label>

      <button type="submit" class="btn btn-primary mt-6 w-full">
        <?= $isNew ? 'Create property' : 'Save changes' ?>
      </button>

      <?php if (!$isNew): ?>
        <div class="mt-3 space-y-2">
          <?php if (!$isArchived && can('listings.publish')): ?>
            <button type="submit" formaction="<?= e(admin_url('listings/' . $listing['id'] . '/state')) ?>"
                    formnovalidate name="action" value="<?= $isLive ? 'unpublish' : 'publish' ?>"
                    class="btn <?= $isLive ? 'btn-outline' : 'btn-accent' ?> w-full">
              <?= $isLive ? 'Take off the site' : 'Publish to the site' ?>
            </button>
          <?php endif; ?>

          <?php if ($isArchived && can('listings.archive')): ?>
            <button type="submit" formaction="<?= e(admin_url('listings/' . $listing['id'] . '/state')) ?>"
                    formnovalidate name="action" value="restore" class="btn btn-outline w-full">Restore</button>
          <?php endif; ?>

          <?php if ($isLive): ?>
            <a href="/property/<?= e($listing['slug'] ?: strtolower($listing['ref'])) ?>" target="_blank" rel="noopener"
               class="btn btn-outline w-full">View on the site <?= icon('arrow-up-right', 'h-4 w-4') ?></a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </section>

    <?php if (!$isNew && !$isArchived && can('listings.archive')): ?>
      <section class="card border-hairline p-6">
        <h2 class="t-h3 text-[1.0625rem]">Archive</h2>
        <p class="mt-2 t-meta text-muted">
          Takes it off the site and out of the main list. Nothing is lost and you can
          restore it at any time.
        </p>
        <button type="submit" formaction="<?= e(admin_url('listings/' . $listing['id'] . '/state')) ?>"
                formnovalidate name="action" value="archive"
                class="btn btn-outline mt-4 w-full">Archive this property</button>
      </section>
    <?php endif; ?>

    <?php if (!$isNew && can('listings.delete')): ?>
      <section class="card border-signal-600/30 p-6">
        <h2 class="t-h3 text-[1.0625rem] text-signal-600">Delete permanently</h2>
        <p class="mt-2 t-meta text-muted">
          Removes the record and its photograph links for good. Archiving is almost
          always the better option.
        </p>
        <button type="submit" formaction="<?= e(admin_url('listings/' . $listing['id'] . '/state')) ?>"
                formnovalidate name="action" value="delete"
                onclick="return confirm('Permanently delete <?= e($listing['ref']) ?>? This cannot be undone.')"
                class="btn mt-4 w-full border border-signal-600 text-signal-600 hover:bg-signal-600 hover:text-white">
          Delete <?= e($listing['ref']) ?>
        </button>
      </section>
    <?php endif; ?>
  </div>
</form>

<?php if (!$isNew): ?>
  <!-- Separate form: HTML does not allow one form inside another, so the picker's
       checkboxes reference this one by id. -->
  <form id="attachForm" method="post"
        action="<?= e(admin_url('listings/' . $listing['id'] . '/images/attach')) ?>" hidden>
    <?= csrf_field() ?>
  </form>
<?php endif; ?>

<?php forget_old(); ?>
