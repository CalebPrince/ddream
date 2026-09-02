<?php
declare(strict_types=1);

$contact = config('contact', []);

$steps = [
    ['icon' => 'calendar', 'title' => 'Book a slot in your timezone',
     'body' => 'Tell us when you are free. We work Ghana hours, but we hold evening slots for North America and morning slots for Australia.'],
    ['icon' => 'camera',   'title' => 'We go to the property',
     'body' => 'An adviser attends in person with a phone gimbal and a tape measure. You are on the call live, not watching a recording.'],
    ['icon' => 'users',    'title' => 'You direct the walkthrough',
     'body' => 'Ask us to open the cupboards, run the taps, look at the ceiling, check the meter, step outside and film the street.'],
    ['icon' => 'file-check','title' => 'You get the file afterwards',
     'body' => 'The recording, the photographs, the measurements, and an honest written note of the problems as well as the selling points.'],
];

$formats = [
    ['icon' => 'play',   'title' => 'Live guided tour',
     'meta' => '30 to 45 minutes', 'body' => 'A video call from inside the property, led by you. The most useful option before an offer.'],
    ['icon' => 'camera', 'title' => 'Filmed walkthrough',
     'meta' => 'Sent within 48 hours', 'body' => 'A recorded walk through every room, narrated, with measurements called out. Watch it whenever suits.'],
    ['icon' => 'compass','title' => 'Neighbourhood drive',
     'meta' => '15 minutes', 'body' => 'The street, the junction, the nearest school and market. What a photograph of the house never shows you.'],
];

// Any listing can be toured; these are the ones with footage already on file.
$featured = array_slice(search_listings(['sort' => 'newest']), 0, 6);
?>
<?php component('page-hero', [
    'crumbs'   => [['label' => 'Virtual tours']],
    'eyebrow'  => 'Virtual tours',
    'heading'  => 'View it properly,<br class="hidden sm:block"> from wherever you are',
    'lead'     => 'A live video walkthrough led by you, with one of our advisers standing '
        . 'in the property holding the camera. Not a slideshow, and not an agent reading '
        . 'from a brochure.',
    'image'    => '/images/slideshow/gc-prime-23.jpg',
    'imageAlt' => 'Open-plan living space in a DDREAM property',
    'facts'    => [
        ['label' => 'Typical length', 'value' => '30 min'],
        ['label' => 'Cost to you',    'value' => 'Free', 'accent' => 'text-signal-600'],
        ['label' => 'Booked within',  'value' => '48 hrs'],
    ],
]); ?>

<!-- How it works -->
<section class="bg-surface py-16 lg:py-20" aria-labelledby="how-heading">
  <div class="shell">
    <div class="max-w-2xl">
      <p class="eyebrow">How it works</p>
      <h2 id="how-heading" class="t-h2 mt-4">Four steps, no cost, no obligation</h2>
    </div>

    <ol class="mt-10 grid gap-px overflow-hidden rounded-[10px] border border-hairline bg-hairline sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($steps as $i => $step): ?>
        <li class="reveal bg-surface p-7">
          <span class="tabular inline-flex h-9 w-9 items-center justify-center rounded-full border border-gold-500/50 font-display text-[0.9375rem] font-semibold text-gold-600">
            <?= $i + 1 ?>
          </span>
          <h3 class="t-h3 mt-5 text-[1.0625rem] leading-snug"><?= e($step['title']) ?></h3>
          <p class="mt-2.5 text-[0.9375rem] leading-relaxed text-muted"><?= e($step['body']) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- Formats -->
<section class="relative overflow-hidden bg-navy-800 py-16 text-navy-200 lg:py-20" aria-labelledby="formats-heading">
  <div class="motif-lattice pointer-events-none absolute inset-0 opacity-[0.06]" aria-hidden="true"></div>
  <div class="shell relative">
    <div class="max-w-2xl">
      <p class="eyebrow eyebrow-light">Three ways to view</p>
      <h2 id="formats-heading" class="t-h2 mt-4 text-white">Pick the one that fits your week</h2>
    </div>

    <ul class="mt-10 grid gap-6 lg:grid-cols-3">
      <?php foreach ($formats as $format): ?>
        <li class="reveal rounded-[10px] border border-hairline-dark bg-navy-900/60 p-7">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] border border-gold-500/40 text-gold-400">
            <?= icon($format['icon'], 'h-5 w-5') ?>
          </span>
          <h3 class="t-h3 mt-5 text-white"><?= e($format['title']) ?></h3>
          <p class="mt-1.5 t-meta font-semibold uppercase tracking-[0.12em] text-gold-400"><?= e($format['meta']) ?></p>
          <p class="mt-3 text-[0.9375rem] leading-relaxed text-navy-200/80"><?= e($format['body']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-10 flex flex-wrap gap-3">
      <a href="/contact" class="btn btn-accent">Book a virtual tour</a>
      <a href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string) $contact['whatsapp'])) ?>"
         target="_blank" rel="noopener noreferrer" class="btn btn-outline-light">
        <?= icon('whatsapp', 'h-4 w-4') ?>Ask on WhatsApp
      </a>
    </div>
  </div>
</section>

<!-- Available to tour -->
<section class="bg-canvas py-16 lg:py-20" aria-labelledby="tourable-heading">
  <div class="shell">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <div class="max-w-2xl">
        <p class="eyebrow">Ready to walk through</p>
        <h2 id="tourable-heading" class="t-h2 mt-3">Available to tour this week</h2>
        <p class="t-lead mt-3 text-muted">
          Any property on our books can be toured. These are the ones with an adviser
          already scheduled nearby.
        </p>
      </div>
      <a href="/selling" class="btn btn-outline">Browse everything <?= icon('arrow-right', 'h-4 w-4') ?></a>
    </div>

    <ul class="mt-9 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($featured as $property): ?>
        <li class="reveal"><?php component('property-card', ['property' => $property]); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php section('cta'); ?>
