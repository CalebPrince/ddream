<?php
declare(strict_types=1);

$contact   = config('contact', []);
$columns   = config('footer_columns', []);
$social    = config('social', []);
$corridors = config('corridors', []);
?>
<footer class="relative overflow-hidden bg-navy-900 text-navy-200">
  <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.05]" aria-hidden="true"></div>

  <div class="relative">
    <!-- Newsletter -->
    <div class="border-b border-hairline-dark">
      <div class="shell grid gap-8 py-12 lg:grid-cols-12 lg:items-center lg:py-14">
        <div class="lg:col-span-5">
          <h2 class="t-h3 text-white">Property alerts, straight to your inbox</h2>
          <p class="mt-2 text-[0.9375rem] text-navy-200/80">
            New listings, diaspora payment plans and market notes. One email a fortnight, no more.
          </p>
        </div>
        <form class="lg:col-span-7 lg:justify-self-end lg:w-full lg:max-w-xl" novalidate>
          <div class="flex flex-col gap-3 sm:flex-row">
            <label for="newsletter" class="sr-only">Email address</label>
            <input id="newsletter" type="email" name="email" required autocomplete="email"
                   placeholder="you@example.com"
                   class="field h-12 flex-1 border-hairline-dark bg-navy-800 text-white placeholder:text-navy-200/50 hover:border-navy-500 focus:border-gold-500 focus:shadow-[0_0_0_3px_rgba(200,160,70,0.2)]">
            <button type="submit" class="btn btn-accent h-12">Subscribe</button>
          </div>
          <p class="mt-2.5 t-meta text-navy-200/60">
            We never share your details. Unsubscribe in one click.
          </p>
        </form>
      </div>
    </div>

    <!-- Link columns -->
    <div class="shell grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-12 lg:gap-8">
      <div class="lg:col-span-4">
        <div class="inline-block bg-white p-3 rounded-[6px]">
          <img src="<?= e(asset('/images/brand/ddream-logo.png')) ?>" alt="DDREAM"
               width="200" height="134" class="h-16 w-auto">
        </div>
        <p class="mt-5 max-w-sm text-[0.9375rem] leading-relaxed text-navy-200/80">
          <?= e(config('legal')) ?> is a Ghanaian real estate solutions company bridging
          property owners, investors and home seekers at home and across the diaspora.
        </p>
        <dl class="mt-6 space-y-2.5 text-[0.9375rem]">
          <div class="flex gap-3">
            <dt class="sr-only">Address</dt>
            <?= icon('map-pin', 'h-[18px] w-[18px] shrink-0 translate-y-0.5 text-gold-500') ?>
            <dd><?= e($contact['address']) ?></dd>
          </div>
          <div class="flex gap-3">
            <dt class="sr-only">Telephone</dt>
            <?= icon('phone', 'h-[18px] w-[18px] shrink-0 translate-y-0.5 text-gold-500') ?>
            <dd><a class="tabular transition-colors hover:text-white" href="tel:<?= e($contact['phone_href']) ?>"><?= e($contact['phone']) ?></a></dd>
          </div>
          <div class="flex gap-3">
            <dt class="sr-only">Email</dt>
            <?= icon('mail', 'h-[18px] w-[18px] shrink-0 translate-y-0.5 text-gold-500') ?>
            <dd><a class="transition-colors hover:text-white" href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a></dd>
          </div>
          <div class="flex gap-3">
            <dt class="sr-only">Opening hours</dt>
            <?= icon('clock', 'h-[18px] w-[18px] shrink-0 translate-y-0.5 text-gold-500') ?>
            <dd><?= e($contact['hours']) ?></dd>
          </div>
        </dl>
      </div>

      <div class="grid gap-10 sm:grid-cols-2 lg:col-span-8 lg:grid-cols-4">
        <?php foreach ($columns as $heading => $links): ?>
          <div>
            <h3 class="text-[0.75rem] font-semibold uppercase tracking-[0.14em] text-gold-400"><?= e($heading) ?></h3>
            <ul class="mt-4 space-y-2.5">
              <?php foreach ($links as $link): ?>
                <li>
                  <a href="<?= e($link['href']) ?>" class="text-[0.9375rem] text-navy-200/85 transition-colors hover:text-white">
                    <?= e($link['label']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Corridors -->
    <div class="border-t border-hairline-dark">
      <div class="shell flex flex-wrap items-center gap-x-3 gap-y-2 py-5 t-meta text-navy-200/70">
        <span class="font-semibold uppercase tracking-[0.14em] text-gold-400">We serve clients in</span>
        <?php foreach ($corridors as $i => $city): ?>
          <?php if ($i > 0): ?><span class="text-navy-200/30" aria-hidden="true">·</span><?php endif; ?>
          <span><?= e($city) ?></span>
        <?php endforeach; ?>
        <span class="text-navy-200/30" aria-hidden="true">·</span>
        <span>and anywhere else in the world</span>
      </div>
    </div>

    <!-- Legal bar -->
    <div class="border-t border-hairline-dark">
      <div class="shell flex flex-col gap-5 py-6 md:flex-row md:items-center md:justify-between">
        <div class="t-meta text-navy-200/60">
          <p>&copy; <?= date('Y') ?> <?= e(config('legal')) ?> All rights reserved.</p>
          <p class="mt-1"><?= e(config('founded_note')) ?>.</p>
        </div>

        <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
          <ul class="flex items-center gap-4 t-meta">
            <li><a href="/privacy" class="transition-colors hover:text-white">Privacy</a></li>
            <li><a href="/terms" class="transition-colors hover:text-white">Terms</a></li>
            <li><a href="/cookies" class="transition-colors hover:text-white">Cookies</a></li>
            <li><a href="/complaints" class="transition-colors hover:text-white">Complaints</a></li>
          </ul>
          <ul class="flex items-center gap-2">
            <?php foreach ($social as $item): ?>
              <li>
                <a href="<?= e($item['href']) ?>" aria-label="<?= e($item['label']) ?>"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-[4px] border border-hairline-dark text-navy-200/80 transition-colors hover:border-gold-500 hover:text-gold-400">
                  <?= icon($item['icon'], 'h-4 w-4') ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <!-- Build credit -->
    <div class="border-t border-hairline-dark">
      <div class="shell py-5 text-center">
        <p class="t-meta text-navy-200/55">
          Built by
          <a href="https://princecaleb.dev" target="_blank" rel="noopener noreferrer"
             class="font-medium text-navy-200/80 underline decoration-gold-500/40 decoration-1 underline-offset-4 transition-colors hover:text-gold-400 hover:decoration-gold-400">
            princecaleb.dev
          </a>
        </p>
      </div>
    </div>
  </div>
</footer>
