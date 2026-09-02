<?php
declare(strict_types=1);
/** @var int $code @var string $title @var string $message */
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($title) ?> &middot; DDREAM admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Public+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?= e(asset('/assets/css/app.css')) ?>">
</head>
<body class="min-h-full bg-canvas">
  <div class="mx-auto flex min-h-screen max-w-lg flex-col justify-center px-6 py-12">
    <p class="eyebrow">Error <?= (int) $code ?></p>
    <h1 class="t-h2 mt-4"><?= e($title) ?></h1>
    <p class="t-lead mt-3 text-muted"><?= e($message) ?></p>
    <div class="mt-8 flex flex-wrap gap-3">
      <a href="<?= e(admin_url()) ?>" class="btn btn-primary">Back to the dashboard</a>
      <a href="/" class="btn btn-outline">View the site</a>
    </div>
  </div>
</body>
</html>
