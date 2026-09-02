<?php
declare(strict_types=1);

$posts      = data_set('posts');
$categories = array_values(array_unique(array_column($posts, 'category')));
$filter     = $_GET['category'] ?? '';

if ($filter !== '') {
    $posts = array_values(array_filter(
        $posts,
        static fn (array $p): bool => $p['category'] === $filter
    ));
}

$lead = current(array_filter($posts, static fn (array $p): bool => !empty($p['featured'])))
    ?: ($posts[0] ?? null);
$rest = array_values(array_filter($posts, static fn (array $p): bool => $p !== $lead));
?>
<?php component('page-hero', [
    'crumbs'  => [['label' => 'Blogs']],
    'eyebrow' => 'Blogs and guides',
    'heading' => 'Market intelligence,<br class="hidden sm:block"> written plainly',
    'lead'    => 'What we have learned doing this, written for people making a decision '
        . 'rather than for search engines. No jargon, no filler, no sales pitch.',
]); ?>

<section class="bg-canvas py-14 lg:py-16" aria-labelledby="blog-heading">
  <div class="shell">
    <h2 id="blog-heading" class="sr-only">Articles</h2>

    <!-- Category filter -->
    <nav class="rail -mx-5 overflow-x-auto px-5 lg:mx-0 lg:px-0" aria-label="Article categories">
      <ul class="flex min-w-max items-center gap-2 pb-1">
        <li>
          <a href="/blog"
             class="badge border px-3 py-1.5 text-[0.8125rem] font-medium normal-case tracking-normal transition-colors <?= $filter === '' ? 'border-navy-700 bg-navy-700 text-white' : 'border-hairline bg-surface text-navy-700 hover:border-navy-200' ?>">
            All articles
          </a>
        </li>
        <?php foreach ($categories as $category): ?>
          <li>
            <a href="/blog?category=<?= urlencode($category) ?>"
               class="badge border px-3 py-1.5 text-[0.8125rem] font-medium normal-case tracking-normal transition-colors <?= $filter === $category ? 'border-navy-700 bg-navy-700 text-white' : 'border-hairline bg-surface text-navy-700 hover:border-navy-200' ?>">
              <?= e($category) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <?php if ($lead): ?>
      <!-- Lead article -->
      <article class="reveal card card-interactive group relative mt-8 grid overflow-hidden lg:grid-cols-2">
        <div class="aspect-[16/10] overflow-hidden bg-navy-100 lg:aspect-auto">
          <img src="<?= e(asset($lead['image'])) ?>" alt="" width="1600" height="1000"
               class="card-zoom h-full w-full object-cover">
        </div>
        <div class="flex flex-col justify-center p-7 lg:p-10">
          <div class="flex flex-wrap items-center gap-2 t-meta">
            <span class="badge border border-gold-200 bg-gold-100 text-gold-600"><?= e($lead['category']) ?></span>
            <span class="text-muted"><?= e($lead['read']) ?></span>
            <span class="text-hairline" aria-hidden="true">·</span>
            <time class="text-muted" datetime="<?= e($lead['date']) ?>"><?= e(date('j F Y', strtotime($lead['date']))) ?></time>
          </div>
          <h3 class="t-h2 mt-4 text-[1.75rem] leading-tight lg:text-[2rem]">
            <a href="/blog/<?= e($lead['slug']) ?>" class="after:absolute after:inset-0 after:content-['']">
              <?= e($lead['title']) ?>
            </a>
          </h3>
          <p class="t-lead mt-3 text-muted"><?= e($lead['excerpt']) ?></p>
          <span class="mt-6 inline-flex items-center gap-2 text-[0.9375rem] font-semibold text-navy-700 transition-colors group-hover:text-gold-600">
            Read the guide <?= icon('arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-1') ?>
          </span>
        </div>
      </article>
    <?php endif; ?>

    <?php if ($rest): ?>
      <ul class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($rest as $post): ?>
          <li class="reveal">
            <article class="card card-interactive group relative flex h-full flex-col overflow-hidden">
              <div class="aspect-[16/10] overflow-hidden bg-navy-100">
                <img src="<?= e(asset($post['image'])) ?>" alt="" width="1600" height="1000" loading="lazy"
                     class="card-zoom h-full w-full object-cover">
              </div>
              <div class="flex flex-1 flex-col p-5">
                <div class="flex flex-wrap items-center gap-2 t-meta">
                  <span class="badge border border-hairline bg-canvas text-gold-600"><?= e($post['category']) ?></span>
                  <span class="text-muted"><?= e($post['read']) ?></span>
                </div>
                <h3 class="t-h3 mt-3 text-[1.125rem] leading-snug">
                  <a href="/blog/<?= e($post['slug']) ?>" class="after:absolute after:inset-0 after:content-['']">
                    <?= e($post['title']) ?>
                  </a>
                </h3>
                <p class="mt-2.5 flex-1 text-[0.9375rem] leading-relaxed text-muted"><?= e($post['excerpt']) ?></p>
                <time class="mt-4 t-meta text-muted" datetime="<?= e($post['date']) ?>">
                  <?= e(date('j F Y', strtotime($post['date']))) ?>
                </time>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if (!$lead): ?>
      <div class="mt-8 rounded-[10px] border border-dashed border-hairline bg-surface p-10 text-center">
        <h3 class="t-h3">Nothing in that category yet</h3>
        <a href="/blog" class="btn btn-outline mt-5">All articles</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php section('cta'); ?>
