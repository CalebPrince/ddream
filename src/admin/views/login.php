<?php
declare(strict_types=1);

/** @var string|null $error @var bool $timedOut @var string $next */
$error    ??= null;
$timedOut ??= false;
$next     ??= '';

$manages = [
    'Property, prices and photographs',
    'Page content across the whole site',
    'Articles, vacancies and services',
    'Every enquiry the site receives',
];
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Sign in &middot; DDREAM admin</title>
  <link rel="icon" href="<?= e(asset('/images/brand/ddream-logo.png')) ?>" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Public+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?= e(asset('/assets/css/app.css')) ?>">
</head>
<body class="min-h-full bg-canvas">
<div class="grid min-h-screen lg:grid-cols-2">

  <!-- Form. First in the markup so a phone shows it without scrolling, moved
       to the right column on desktop. Aligned to the top, not the page centre. -->
  <div class="order-1 flex justify-center px-6 pb-12 pt-10 lg:order-2 lg:px-12 lg:pt-16">
    <div class="w-full max-w-sm">
      <img src="<?= e(asset('/images/brand/ddream-logo.png')) ?>"
           alt="DDREAM, Domestic Diaspora Real Estate Management"
           class="h-16 w-auto">

      <h1 class="t-h2 mt-7 text-[1.75rem]">Sign in</h1>
      <p class="mt-2 text-[0.9375rem] text-muted">
        Staff access to the DDREAM website.
      </p>

      <?php if ($timedOut): ?>
        <p class="mt-5 flex items-start gap-2.5 rounded-[6px] border border-hairline bg-surface px-4 py-3 text-[0.875rem] text-navy-700">
          <?= icon('clock', 'h-[18px] w-[18px] shrink-0 translate-y-0.5 text-gold-600') ?>
          You were signed out after a period of inactivity.
        </p>
      <?php endif; ?>

      <?php if ($error !== null): ?>
        <p class="mt-5 flex items-start gap-2.5 rounded-[6px] border border-signal-600/40 bg-signal-600/10 px-4 py-3 text-[0.875rem] font-medium text-signal-600" role="alert">
          <?= icon('x', 'h-[18px] w-[18px] shrink-0 translate-y-0.5') ?>
          <span><?= e($error) ?></span>
        </p>
      <?php endif; ?>

      <form method="post" action="<?= e(admin_url('login')) ?>" class="mt-7 space-y-5" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= e($next) ?>">

        <div>
          <label class="field-label" for="email">Email address</label>
          <input class="field" id="email" name="email" type="email" required autocomplete="username"
                 autofocus value="<?= e(old('email')) ?>">
        </div>

        <div>
          <label class="field-label" for="password">Password</label>
          <input class="field" id="password" name="password" type="password" required
                 autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full">Sign in</button>
      </form>

      <p class="mt-6 t-meta text-muted">
        Lost your password? A Superadmin can reset it for you.
      </p>

      <a href="/" class="mt-8 inline-flex items-center gap-2 t-meta font-semibold text-muted transition-colors hover:text-navy-700">
        <?= icon('chevron-left', 'h-4 w-4') ?>Back to the website
      </a>
    </div>
  </div>

  <!-- Brand panel -->
  <div class="relative order-2 hidden overflow-hidden bg-navy-800 lg:order-1 lg:block">
    <img src="<?= e(asset('/images/front-desk.png')) ?>" alt=""
         class="absolute inset-0 h-full w-full object-cover opacity-35">
    <div class="absolute inset-0 bg-gradient-to-t from-navy-900 via-navy-900/88 to-navy-900/65"></div>
    <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.06]"></div>

    <div class="relative flex h-full flex-col justify-end p-12 xl:p-14">
      <p class="eyebrow eyebrow-light">DDREAM</p>
      <p class="t-h2 mt-4 max-w-md text-white">
        Domestic, Diaspora Real&nbsp;Estate Management
      </p>
      <p class="mt-4 max-w-md text-[0.9375rem] leading-relaxed text-navy-200/80">
        Everything the public site shows is managed from here.
      </p>

      <ul class="mt-7 space-y-2.5">
        <?php foreach ($manages as $item): ?>
          <li class="flex items-center gap-3 text-[0.9375rem] text-navy-200/90">
            <?= icon('check', 'h-4 w-4 shrink-0 text-gold-400') ?><?= e($item) ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <p class="mt-9 flex items-baseline gap-2 border-t border-hairline-dark pt-6 font-display text-lg font-semibold">
        <span class="text-signal-500">No</span><span class="text-white">Client Commission</span>
      </p>
    </div>
  </div>
</div>
</body>
</html>
