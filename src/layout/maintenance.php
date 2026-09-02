<?php
declare(strict_types=1);

/**
 * Shown to visitors while maintenance mode is on. Standalone: no navigation,
 * because none of it would work.
 */

$contact  = config('contact', []);
$heading  = setting('maintenance_heading', 'We are making some improvements');
$message  = setting('maintenance_message', 'The site is briefly offline. Please try again shortly.');
$backAt   = setting('maintenance_back_at', '');
$whatsapp = preg_replace('/\D+/', '', (string) ($contact['whatsapp'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>Back shortly &middot; <?= e(config('name')) ?></title>
  <meta name="description" content="<?= e($heading) ?>">
  <link rel="icon" href="<?= e(asset('/images/brand/ddream-logo.png')) ?>" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,600;1,9..144,600&family=Public+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?= e(asset('/assets/css/app.css')) ?>">
</head>
<body class="min-h-full bg-navy-900">

<div class="relative min-h-screen overflow-hidden">
  <img src="<?= e(asset('/images/slideshow/gc-prime-11.jpg')) ?>" alt=""
       class="absolute inset-0 h-full w-full object-cover opacity-25">
  <div class="absolute inset-0 bg-gradient-to-t from-navy-900 via-navy-900/90 to-navy-900/70"></div>
  <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.06]"></div>

  <div class="relative mx-auto flex min-h-screen w-full max-w-2xl flex-col justify-center px-6 py-16">

    <span class="inline-block w-fit rounded-[6px] bg-white p-3">
      <img src="<?= e(asset('/images/brand/ddream-logo.png')) ?>"
           alt="<?= e(config('legal')) ?>" class="h-16 w-auto">
    </span>

    <p class="eyebrow eyebrow-light mt-10">Temporarily offline</p>
    <h1 class="t-h1 mt-4 text-white"><?= e($heading) ?></h1>
    <p class="t-lead mt-5 max-w-xl text-navy-200/85"><?= e($message) ?></p>

    <?php if ($backAt !== '' && $backAt !== null): ?>
      <p class="mt-4 flex items-center gap-2.5 text-[0.9375rem] font-medium text-gold-400">
        <?= icon('clock', 'h-[18px] w-[18px]') ?>Expected back <?= e($backAt) ?>
      </p>
    <?php endif; ?>

    <!-- Reaching a person still has to work while the site does not. -->
    <div class="mt-9 flex flex-wrap gap-3">
      <a href="tel:<?= e($contact['phone_href'] ?? '') ?>" class="btn btn-accent btn-lg">
        <?= icon('phone', 'h-[18px] w-[18px]') ?><span class="tabular"><?= e($contact['phone'] ?? '') ?></span>
      </a>
      <?php if ($whatsapp !== ''): ?>
        <a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener noreferrer"
           class="btn btn-outline-light btn-lg">
          <?= icon('whatsapp', 'h-[18px] w-[18px]') ?>WhatsApp
        </a>
      <?php endif; ?>
      <a href="mailto:<?= e($contact['email'] ?? '') ?>" class="btn btn-outline-light btn-lg">
        <?= icon('mail', 'h-[18px] w-[18px]') ?>Email us
      </a>
    </div>

    <div class="mt-12 border-t border-hairline-dark pt-6">
      <a href="/admin/login"
         class="inline-flex items-center gap-2 text-[0.9375rem] font-semibold text-navy-200/80 transition-colors hover:text-white">
        <?= icon('lock', 'h-[18px] w-[18px] text-gold-400') ?>
        Staff sign in
        <?= icon('arrow-right', 'h-4 w-4') ?>
      </a>
      <p class="mt-2 t-meta text-navy-200/50">
        Signed-in staff can browse the site as normal while it is closed.
      </p>
    </div>
  </div>
</div>
</body>
</html>
