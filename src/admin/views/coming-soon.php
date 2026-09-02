<?php
declare(strict_types=1);

/** @var string $section */

$phase = [
    'listings' => ['2', 'The listing editor, image library and locations.'],
    'inbox'    => ['3', 'Every enquiry from the site in one queue, with assignment and internal notes.'],
    'pages'    => ['4', 'Section by section editing for every public page.'],
    'settings' => ['4', 'Company details, navigation, footer and SEO defaults.'],
    'blog'     => ['5', 'Articles and categories, with draft and publish states.'],
    'careers'  => ['5', 'Vacancies, with a proper empty state when nothing is advertised.'],
    'services' => ['5', 'The fifteen services, their order and which six are featured.'],
    'media'    => ['2', 'One image library, reusable across listings and pages.'],
    'users'    => ['1', 'Staff accounts, roles and suspension.'],
    'activity' => ['1', 'A record of who changed what, and when.'],
][$section] ?? ['later', 'This section is planned but not built yet.'];
?>
<div class="mx-auto max-w-lg py-16 text-center">
  <span class="inline-flex h-12 w-12 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700">
    <?= icon('hard-hat', 'h-5 w-5') ?>
  </span>
  <h2 class="t-h3 mt-4">Not built yet</h2>
  <p class="mx-auto mt-2 max-w-sm text-[0.9375rem] leading-relaxed text-muted">
    <?= e($phase[1]) ?>
  </p>
  <p class="mt-4 t-meta text-muted">
    Scheduled for phase <?= e($phase[0]) ?> of the backend build.
  </p>
  <a href="<?= e(admin_url()) ?>" class="btn btn-outline mt-7">Back to the dashboard</a>
</div>
