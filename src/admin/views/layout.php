<?php
declare(strict_types=1);

/**
 * The admin shell: navy sidebar, light working area.
 *
 * @var string $viewFile  absolute path to the page view
 * @var string $title     browser title and page heading
 */

$user     = current_user();
$title  ??= 'Dashboard';
$unread   = can('inbox.view')
    ? (int) db_value("SELECT COUNT(*) FROM enquiries WHERE status = 'new'")
    : 0;

/** Sidebar. Items the user cannot reach are not drawn, but the route still checks. */
$nav = [
    ['group' => null, 'items' => [
        ['label' => 'Dashboard', 'icon' => 'chart', 'href' => admin_url(), 'section' => '', 'cap' => null],
        ['label' => 'Inbox', 'icon' => 'mail', 'href' => admin_url('inbox'), 'section' => 'inbox', 'cap' => 'inbox.view', 'badge' => $unread],
    ]],
    ['group' => 'Content', 'items' => [
        ['label' => 'Listings', 'icon' => 'key', 'href' => admin_url('listings'), 'section' => 'listings', 'cap' => 'listings.view'],
        ['label' => 'Page contents', 'icon' => 'layers', 'href' => admin_url('pages'), 'section' => 'pages', 'cap' => 'pages.edit'],
        ['label' => 'Blogs', 'icon' => 'file-check', 'href' => admin_url('blog'), 'section' => 'blog', 'cap' => 'blog.edit'],
        ['label' => 'Careers', 'icon' => 'users', 'href' => admin_url('careers'), 'section' => 'careers', 'cap' => 'careers.edit'],
        ['label' => 'Services', 'icon' => 'building', 'href' => admin_url('services'), 'section' => 'services', 'cap' => 'services.edit'],
        ['label' => 'Media', 'icon' => 'camera', 'href' => admin_url('media'), 'section' => 'media', 'cap' => 'media.upload'],
    ]],
    ['group' => 'Administration', 'items' => [
        ['label' => 'Settings', 'icon' => 'wrench', 'href' => admin_url('settings'), 'section' => 'settings', 'cap' => 'settings.company'],
        ['label' => 'Users', 'icon' => 'shield-check', 'href' => admin_url('users'), 'section' => 'users', 'cap' => 'users.manage'],
        ['label' => 'Activity', 'icon' => 'clock', 'href' => admin_url('activity'), 'section' => 'activity', 'cap' => 'activity.view'],
        ['label' => 'View website', 'icon' => 'globe', 'href' => '/', 'section' => '__site', 'cap' => null, 'external' => true],
    ]],
];
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($title) ?> &middot; DDREAM admin</title>
  <link rel="icon" href="<?= e(asset('/images/brand/ddream-logo.png')) ?>" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Public+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?= e(asset('/assets/css/app.css')) ?>">
</head>
<body class="min-h-full bg-canvas">

<a href="#work" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-[4px] focus:bg-navy-700 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">Skip to content</a>

<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside id="adminNav"
         class="fixed inset-y-0 left-0 z-40 hidden w-[15.5rem] shrink-0 flex-col overflow-y-auto bg-navy-800 lg:flex">
    <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-hairline-dark px-5">
      <span class="inline-flex h-8 w-8 items-center justify-center rounded-[4px] bg-white">
        <img src="<?= e(asset('/images/brand/ddream-logo.png')) ?>" alt="" class="h-6 w-auto">
      </span>
      <span class="font-display text-[0.9375rem] font-semibold text-white">DDREAM admin</span>
    </div>

    <nav class="flex-1 px-3 py-4" aria-label="Admin sections">
      <?php foreach ($nav as $group): ?>
        <?php
        $visible = array_values(array_filter(
            $group['items'],
            static fn (array $i): bool => $i['cap'] === null || can($i['cap'])
        ));
        if ($visible === []) { continue; }
        ?>
        <?php if ($group['group'] !== null): ?>
          <p class="mt-6 px-3 pb-2 text-[0.625rem] font-bold uppercase tracking-[0.14em] text-navy-200/50">
            <?= e($group['group']) ?>
          </p>
        <?php endif; ?>
        <ul class="space-y-0.5">
          <?php foreach ($visible as $item): ?>
            <?php $on = is_current($item['section']); ?>
            <li>
              <a href="<?= e($item['href']) ?>"
                 <?= !empty($item['external']) ? 'target="_blank" rel="noopener"' : '' ?>
                 class="flex items-center gap-3 rounded-[4px] px-3 py-2.5 text-[0.875rem] transition-colors <?= $on ? 'bg-navy-700 font-semibold text-white' : 'font-medium text-navy-200/85 hover:bg-navy-700/50 hover:text-white' ?>"
                 <?= $on ? 'aria-current="page"' : '' ?>>
                <?= icon($item['icon'], 'h-[18px] w-[18px] shrink-0 ' . ($on ? 'text-gold-400' : 'text-navy-200/60')) ?>
                <span class="flex-1"><?= e($item['label']) ?></span>
                <?php if (!empty($item['badge'])): ?>
                  <span class="tabular rounded-full bg-signal-600 px-1.5 py-0.5 text-[0.6875rem] font-bold text-white"><?= (int) $item['badge'] ?></span>
                <?php endif; ?>
                <?php if (!empty($item['external'])): ?>
                  <?= icon('arrow-up-right', 'h-3.5 w-3.5 shrink-0 text-navy-200/50') ?>
                <?php endif; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endforeach; ?>
    </nav>

  </aside>

  <!-- Working area -->
  <div class="flex min-w-0 flex-1 flex-col lg:pl-[15.5rem]">

    <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-hairline bg-surface/95 px-5 backdrop-blur-md lg:px-8">
      <button type="button" id="adminNavToggle"
              class="inline-flex h-10 w-10 items-center justify-center rounded-[4px] border border-hairline text-navy-700 lg:hidden"
              aria-expanded="false" aria-controls="adminNav" aria-label="Open menu">
        <?= icon('menu', 'h-5 w-5') ?>
      </button>

      <h1 class="min-w-0 flex-1 truncate font-display text-[1.125rem] font-semibold text-navy-700"><?= e($title) ?></h1>

      <?php if (!empty($headerAction)): ?>
        <?= $headerAction ?>
      <?php endif; ?>

      <div class="flex items-center gap-3 border-l border-hairline pl-4">
        <div class="hidden text-right sm:block">
          <p class="text-[0.8125rem] font-semibold leading-tight text-navy-700"><?= e($user['name'] ?? '') ?></p>
          <p class="t-meta leading-tight text-muted"><?= e(role_label($user['role'] ?? null)) ?></p>
        </div>
        <form method="post" action="<?= e(admin_url('logout')) ?>">
          <?= csrf_field() ?>
          <button type="submit"
                  class="inline-flex h-10 items-center gap-2 rounded-[4px] border border-hairline px-3 text-[0.8125rem] font-semibold text-navy-700 transition-colors hover:border-navy-200 hover:bg-canvas">
            Sign out
          </button>
        </form>
      </div>
    </header>

    <main id="work" class="flex-1 px-5 py-7 lg:px-8 lg:py-9">
      <?php if (setting_bool('maintenance_enabled')): ?>
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-[6px] border border-signal-600/40 bg-signal-600/10 px-4 py-3" role="status">
          <p class="flex items-center gap-2.5 text-[0.9375rem] font-semibold text-signal-600">
            <?= icon('lock', 'h-[18px] w-[18px] shrink-0') ?>
            The public site is closed to visitors.
          </p>
          <?php if (can('settings.maintenance')): ?>
            <a href="<?= e(admin_url('settings?tab=maintenance')) ?>"
               class="t-meta font-semibold text-signal-600 underline underline-offset-4">Open it again</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php foreach (take_flashes() as $message): ?>
        <?php
        $tone = match ($message['type']) {
            'success' => 'border-verified/40 bg-verified/10 text-verified',
            'error'   => 'border-signal-600/40 bg-signal-600/10 text-signal-600',
            default   => 'border-hairline bg-surface text-navy-700',
        };
        ?>
        <div class="mb-5 flex items-start gap-2.5 rounded-[6px] border px-4 py-3 text-[0.9375rem] font-medium <?= $tone ?>" role="status">
          <?= icon($message['type'] === 'error' ? 'x' : 'check', 'h-[18px] w-[18px] shrink-0 translate-y-0.5') ?>
          <span><?= e($message['message']) ?></span>
        </div>
      <?php endforeach; ?>

      <?php require $viewFile; ?>
    </main>
  </div>
</div>

<script>
  // Sidebar drawer below lg.
  (function () {
    var toggle = document.getElementById('adminNavToggle');
    var nav = document.getElementById('adminNav');
    if (!toggle || !nav) return;

    var backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 z-30 hidden bg-navy-900/60 lg:hidden';
    document.body.appendChild(backdrop);

    function open() {
      nav.classList.remove('hidden');
      nav.classList.add('flex');
      backdrop.classList.remove('hidden');
      toggle.setAttribute('aria-expanded', 'true');
    }
    function close() {
      nav.classList.add('hidden');
      nav.classList.remove('flex');
      backdrop.classList.add('hidden');
      toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', open);
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') close();
    });
  })();
</script>
</body>
</html>
