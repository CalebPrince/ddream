<?php
declare(strict_types=1);

/**
 * The shared HTML shell. Every route renders through this.
 *
 * @var array  $route        the matched entry from src/routes.php
 * @var string $currentPath  path used for nav active state
 */

require_once dirname(__DIR__) . '/content.php';

// Everything rendered from here on belongs to this page, so its sections can
// find their own wording.
content_page($route['page']);

// Anything set on the Page contents screen wins over the route defaults.
$meta = content_page_meta($route['page']);

$siteName    = config('name');
// A title typed into Page contents is the whole browser title, brand and all.
// Without one, the route's title gets the site name added to it.
$title       = $meta['title'] ?? (empty($route['bare'])
    ? ($route['title'] . ' | ' . $siteName)
    : ($siteName . ' | ' . $route['title']));
$description = $meta['description'] ?? $route['desc'] ?? config('description');
$ogImage     = $meta['image'] ?? $route['image'] ?? '/images/slideshow/gc-prime-01.jpg';
$path        = $requestPath ?? $currentPath;
$canonical   = rtrim((string) config('url'), '/') . ($path === '/' ? '/' : $path);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-pt-24">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($description) ?>">
  <meta name="theme-color" content="#0a2240">
  <link rel="canonical" href="<?= e($canonical) ?>">

  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:site_name" content="<?= e(config('legal')) ?>">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($description) ?>">
  <meta property="og:image" content="<?= e(rtrim((string) config('url'), '/') . asset($ogImage)) ?>">
  <meta name="twitter:card" content="summary_large_image">

  <link rel="icon" href="<?= e(asset('/images/brand/ddream-logo.png')) ?>" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&family=Public+Sans:wght@400;500;600;700&display=swap">
  <link rel="stylesheet" href="<?= e(asset('/assets/css/app.css')) ?>">

  <script type="application/ld+json">
  <?= json_encode([
      '@context'      => 'https://schema.org',
      '@type'         => 'RealEstateAgent',
      'name'          => config('legal'),
      'alternateName' => config('name'),
      'description'   => config('description'),
      'slogan'        => config('tagline'),
      'telephone'     => config('contact.phone'),
      'email'         => config('contact.email'),
      'url'           => config('url'),
      'areaServed'    => config('corridors'),
      'address'       => [
          '@type'           => 'PostalAddress',
          'streetAddress'   => 'Airport Residential Area',
          'addressLocality' => 'Accra',
          'addressCountry'  => 'GH',
      ],
  ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
  </script>
</head>
<body>

  <?php require __DIR__ . '/header.php'; ?>

  <main id="main">
    <?php require dirname(__DIR__) . '/pages/' . $route['page'] . '.php'; ?>
  </main>

  <?php require __DIR__ . '/footer.php'; ?>

  <script src="<?= e(asset('/assets/js/site.js')) ?>" defer></script>
</body>
</html>
