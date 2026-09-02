<?php
declare(strict_types=1);

$differentiators = [
    ['icon' => 'globe',        'title' => 'Local expertise, global perspective', 'body' => 'Our staff were born and bred in Ghana and have spent years working abroad. They know both sides of the conversation.'],
    ['icon' => 'landmark',     'title' => 'A real, identifiable office',          'body' => 'A registered limited liability company operating from a recognised address. Meetings happen in our offices, face to face.'],
    ['icon' => 'users',        'title' => 'Direct landlord access',               'body' => 'We hold the relationships ourselves. No agent chains, no inflated asking prices, no phantom listings.'],
    ['icon' => 'clock',        'title' => 'Swift and bespoke service',            'body' => 'One client at a time. You get a named contact who answers, and a service shaped around your circumstances.'],
];
?>
<section id="apart" class="bg-canvas py-16 lg:py-24" aria-labelledby="commission-heading">
  <div class="shell grid gap-12 lg:grid-cols-12 lg:gap-14">

    <div class="lg:col-span-5">
      <p class="eyebrow">What sets us apart</p>
      <h2 id="commission-heading" class="t-h2 mt-4">
        The commission never<br class="hidden sm:block"> falls on you
      </h2>

      <div class="mt-7 rounded-[10px] border border-hairline bg-surface p-6 shadow-panel">
        <p class="flex items-baseline gap-2 font-display text-3xl font-semibold leading-none">
          <span class="text-signal-600">No</span>
          <span class="text-navy-700">Client Commission</span>
        </p>
        <div class="mt-5 h-px bg-hairline"></div>

        <dl class="mt-5 space-y-4">
          <div class="flex items-start justify-between gap-6">
            <dt class="text-[0.9375rem] text-muted">Paid by the seller or landlord</dt>
            <dd class="shrink-0 font-sans text-[0.9375rem] font-semibold text-navy-700">The commission</dd>
          </div>
          <div class="flex items-start justify-between gap-6 border-t border-hairline pt-4">
            <dt class="text-[0.9375rem] text-muted">Paid by you, the client</dt>
            <dd class="shrink-0 tabular font-sans text-[0.9375rem] font-semibold text-navy-700"><?= e(config('admin_fee')) ?> flat</dd>
          </div>
        </dl>

        <p class="mt-5 t-meta text-muted">
          A single <?= e(config('admin_fee')) ?> administrative fee per client covers our
          running costs. That is the whole of what you pay us. There is no percentage,
          no success fee and no surprise at closing.
        </p>
      </div>

      <p class="mt-6 max-w-md text-[0.9375rem] leading-relaxed text-muted">
        Whether you are in Accra, Kumasi, London, Amsterdam, Berlin, New York, Toronto,
        Johannesburg, a member of the diplomatic community or anywhere else in the world,
        the arithmetic is the same.
      </p>
    </div>

    <div class="lg:col-span-7">
      <ul class="grid gap-5 sm:grid-cols-2">
        <?php foreach ($differentiators as $item): ?>
          <li class="reveal card flex h-full flex-col p-6">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-hairline bg-canvas text-navy-700">
              <?= icon($item['icon'], 'h-5 w-5') ?>
            </span>
            <h3 class="t-h3 mt-4 text-[1.0625rem] leading-snug"><?= e($item['title']) ?></h3>
            <p class="mt-2 text-[0.9375rem] leading-relaxed text-muted"><?= e($item['body']) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>

      <blockquote class="reveal mt-5 rounded-[10px] border border-gold-200 bg-gold-100/60 p-6">
        <?= icon('quote', 'h-6 w-6 text-gold-600') ?>
        <p class="mt-3 font-display text-xl leading-snug text-navy-700">
          We combine local expertise with a global perspective, so every client enjoys a
          safe, transparent and rewarding real estate experience.
        </p>
        <footer class="mt-4 t-meta font-semibold uppercase tracking-[0.12em] text-gold-600">
          The DDREAM commitment
        </footer>
      </blockquote>
    </div>
  </div>
</section>
