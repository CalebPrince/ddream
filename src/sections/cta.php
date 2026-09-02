<?php
declare(strict_types=1);

$contact = config('contact', []);
?>
<section class="relative overflow-hidden bg-navy-700" aria-labelledby="cta-heading">
  <img src="<?= e(asset('/images/hero-city.png')) ?>" alt="" aria-hidden="true"
       width="1536" height="1024" loading="lazy"
       class="absolute inset-0 h-full w-full object-cover opacity-25">
  <div class="absolute inset-0 bg-gradient-to-r from-navy-900 via-navy-900/90 to-navy-800/70" aria-hidden="true"></div>
  <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.05]" aria-hidden="true"></div>

  <div class="shell relative grid gap-10 py-16 lg:grid-cols-12 lg:items-center lg:py-20">
    <div class="lg:col-span-7">
      <p class="eyebrow eyebrow-light">Start the conversation</p>
      <h2 id="cta-heading" class="t-h2 mt-4 max-w-2xl text-white">
        Invest with confidence. Manage with peace of mind.
      </h2>
      <p class="t-lead mt-4 max-w-xl text-navy-200/85">
        Tell us what you are looking for and we will come back within one working day
        with a shortlist, an honest view of the market, and a clear fee of
        <?= e(config('admin_fee')) ?>, and nothing else.
      </p>

      <div class="mt-8 flex flex-wrap gap-3">
        <a href="/contact" class="btn btn-accent btn-lg">Book a Consultation</a>
        <a href="tel:<?= e($contact['phone_href']) ?>" class="btn btn-outline-light btn-lg">
          <?= icon('phone', 'h-[18px] w-[18px]') ?><span class="tabular"><?= e($contact['phone']) ?></span>
        </a>
      </div>
    </div>

    <div class="lg:col-span-5 lg:justify-self-end">
      <ul class="space-y-3 lg:w-[22rem]">
        <?php
        $assurances = [
            ['icon' => 'badge-check', 'text' => 'A named adviser, not a call centre'],
            ['icon' => 'shield-check','text' => 'Due diligence before any payment'],
            ['icon' => 'globe',       'text' => 'Calls scheduled in your timezone'],
            ['icon' => 'file-check',  'text' => 'Everything confirmed in writing'],
        ];
        foreach ($assurances as $item): ?>
          <li class="flex items-center gap-3 rounded-[6px] border border-hairline-dark bg-navy-900/50 px-4 py-3 backdrop-blur-sm">
            <?= icon($item['icon'], 'h-5 w-5 shrink-0 text-gold-400') ?>
            <span class="text-[0.9375rem] text-white"><?= e($item['text']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>
