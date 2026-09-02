<?php
declare(strict_types=1);

$about = data_set('about');
?>
<section id="mission" class="relative scroll-mt-24 overflow-hidden bg-navy-800 py-16 text-navy-200 lg:py-24" aria-labelledby="mission-heading">
  <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.06]" aria-hidden="true"></div>

  <div class="shell relative">
    <p class="eyebrow eyebrow-light">What we are here to do</p>
    <h2 id="mission-heading" class="t-h2 mt-4 max-w-2xl text-white">
      Our mission and vision
    </h2>

    <div class="mt-10 grid gap-6 lg:grid-cols-2">
      <article class="reveal rounded-[10px] border border-hairline-dark bg-navy-900/60 p-7 lg:p-9">
        <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-gold-500/40 text-gold-400">
          <?= icon('target', 'h-5 w-5') ?>
        </span>
        <h3 class="t-h3 mt-5 text-white">Our Mission</h3>
        <p class="mt-3 text-[1.0625rem] leading-relaxed text-navy-200/85">
          <?= e($about['mission']) ?>
        </p>
      </article>

      <article class="reveal rounded-[10px] border border-hairline-dark bg-navy-900/60 p-7 lg:p-9">
        <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-gold-500/40 text-gold-400">
          <?= icon('eye', 'h-5 w-5') ?>
        </span>
        <h3 class="t-h3 mt-5 text-white">Our Vision</h3>
        <p class="mt-3 text-[1.0625rem] leading-relaxed text-navy-200/85">
          <?= e($about['vision']) ?>
        </p>
      </article>
    </div>
  </div>
</section>
