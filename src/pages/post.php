<?php
declare(strict_types=1);

/** @var array $route */

$post   = $route['post'];
$others = array_values(array_filter(
    data_set('posts'),
    static fn (array $p): bool => $p['slug'] !== $post['slug']
));
$others = array_slice($others, 0, 3);
?>
<article>
  <header class="border-b border-hairline bg-canvas">
    <div class="shell max-w-3xl py-10 lg:pb-14 lg:pt-[5.5rem]">
      <nav aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1.5 t-meta text-muted">
          <li><a href="/" class="transition-colors hover:text-navy-700">Home</a></li>
          <li aria-hidden="true" class="text-hairline"><?= icon('chevron-right', 'h-3.5 w-3.5') ?></li>
          <li><a href="/blog" class="transition-colors hover:text-navy-700">Blogs</a></li>
          <li aria-hidden="true" class="text-hairline"><?= icon('chevron-right', 'h-3.5 w-3.5') ?></li>
          <li><span class="font-medium text-navy-700" aria-current="page"><?= e($post['category']) ?></span></li>
        </ol>
      </nav>

      <div class="mt-6 flex flex-wrap items-center gap-2 t-meta">
        <span class="badge border border-gold-200 bg-gold-100 text-gold-600"><?= e($post['category']) ?></span>
        <span class="text-muted"><?= e($post['read']) ?></span>
        <span class="text-hairline" aria-hidden="true">·</span>
        <time class="text-muted" datetime="<?= e($post['date']) ?>"><?= e(date('j F Y', strtotime($post['date']))) ?></time>
      </div>

      <h1 class="t-h1 mt-4 font-display"><?= e($post['title']) ?></h1>
      <p class="t-lead mt-5 text-muted"><?= e($post['excerpt']) ?></p>
    </div>
  </header>

  <div class="bg-surface">
    <div class="shell max-w-3xl py-10 lg:py-14">
      <figure class="overflow-hidden rounded-[10px] border border-hairline">
        <img src="<?= e(asset($post['image'])) ?>" alt="" width="1600" height="1000"
             fetchpriority="high" class="w-full object-cover">
      </figure>

      <div class="mt-10 space-y-5">
        <?php foreach ($post['body'] as $block): ?>
          <?php if (str_starts_with($block, '## ')): ?>
            <h2 class="t-h3 pt-4"><?= e(substr($block, 3)) ?></h2>
          <?php else: ?>
            <p class="text-[1.0625rem] leading-relaxed text-ink"><?= e($block) ?></p>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="mt-12 rounded-[10px] border border-hairline bg-canvas p-7">
        <p class="flex items-baseline gap-2 font-display text-2xl font-semibold leading-none">
          <span class="text-signal-600">No</span><span class="text-navy-700">Client Commission</span>
        </p>
        <p class="mt-3 text-[0.9375rem] leading-relaxed text-muted">
          If any of the above applies to something you are considering, talk it through
          with us. Advice costs nothing and the commission never falls on you.
        </p>
        <div class="mt-5 flex flex-wrap gap-3">
          <a href="/contact" class="btn btn-primary">Book a Consultation</a>
          <a href="/blog" class="btn btn-outline">More guides</a>
        </div>
      </div>
    </div>
  </div>

  <?php if ($others): ?>
    <section class="border-t border-hairline bg-canvas py-14 lg:py-16" aria-labelledby="more-heading">
      <div class="shell">
        <h2 id="more-heading" class="t-h2">Keep reading</h2>
        <ul class="mt-8 grid gap-6 md:grid-cols-3">
          <?php foreach ($others as $other): ?>
            <li class="reveal">
              <article class="card card-interactive group relative flex h-full flex-col overflow-hidden">
                <div class="aspect-[16/10] overflow-hidden bg-navy-100">
                  <img src="<?= e(asset($other['image'])) ?>" alt="" width="1600" height="1000" loading="lazy"
                       class="card-zoom h-full w-full object-cover">
                </div>
                <div class="flex flex-1 flex-col p-5">
                  <span class="badge w-fit border border-hairline bg-canvas text-gold-600"><?= e($other['category']) ?></span>
                  <h3 class="t-h3 mt-3 text-[1.0625rem] leading-snug">
                    <a href="/blog/<?= e($other['slug']) ?>" class="after:absolute after:inset-0 after:content-['']">
                      <?= e($other['title']) ?>
                    </a>
                  </h3>
                  <span class="mt-3 t-meta text-muted"><?= e($other['read']) ?></span>
                </div>
              </article>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
  <?php endif; ?>
</article>
