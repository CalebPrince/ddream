<?php
declare(strict_types=1);

$contact = config('contact', []);
$social  = config('social', []);

$channels = [
    [
        'icon'  => 'phone',
        'label' => 'Telephone',
        'value' => $contact['phone'],
        'href'  => 'tel:' . $contact['phone_href'],
        'note'  => 'Office hours, Ghana time (GMT)',
    ],
    [
        'icon'  => 'whatsapp',
        'label' => 'WhatsApp',
        'value' => $contact['whatsapp'],
        'href'  => 'https://wa.me/' . preg_replace('/\D+/', '', (string) $contact['whatsapp']),
        'note'  => 'Best if you are calling from abroad',
    ],
    [
        'icon'  => 'mail',
        'label' => 'Email',
        'value' => $contact['email'],
        'href'  => 'mailto:' . $contact['email'],
        'note'  => 'Replied to within one working day',
    ],
    [
        'icon'  => 'map-pin',
        'label' => 'Office',
        'value' => $contact['address'],
        'href'  => null,
        'note'  => $contact['hours'],
    ],
];
?>
<section class="border-b border-hairline bg-surface py-14 lg:py-16" aria-labelledby="details-heading">
  <div class="shell">
    <div class="max-w-2xl">
      <p class="eyebrow">Contact details</p>
      <h2 id="details-heading" class="t-h2 mt-4">Four ways to reach us</h2>
      <p class="t-lead mt-3 text-muted">
        Whichever you choose, you get a named adviser rather than a queue.
      </p>
    </div>

    <ul class="mt-9 grid gap-px overflow-hidden rounded-[10px] border border-hairline bg-hairline sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($channels as $channel): ?>
        <li class="reveal group bg-surface p-6 transition-colors hover:bg-canvas">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700 transition-colors group-hover:bg-navy-700 group-hover:text-gold-400">
            <?= icon($channel['icon'], 'h-5 w-5') ?>
          </span>
          <h3 class="t-meta mt-4 font-semibold uppercase tracking-[0.14em] text-muted">
            <?= e($channel['label']) ?>
          </h3>
          <p class="mt-1.5 font-sans text-[1rem] font-semibold leading-snug text-navy-700">
            <?php if ($channel['href']): ?>
              <a href="<?= e($channel['href']) ?>"
                 class="tabular transition-colors hover:text-gold-600"
                 <?= str_starts_with($channel['href'], 'http') ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                <?= e($channel['value']) ?>
              </a>
            <?php else: ?>
              <?= e($channel['value']) ?>
            <?php endif; ?>
          </p>
          <p class="mt-2 t-meta text-muted"><?= e($channel['note']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-[10px] border border-hairline bg-canvas px-6 py-4">
      <p class="t-meta text-muted">
        You are also welcome to walk in. Meetings are held in our own offices.
      </p>
      <ul class="flex items-center gap-2">
        <?php foreach ($social as $item): ?>
          <li>
            <a href="<?= e($item['href']) ?>" aria-label="<?= e($item['label']) ?>"
               class="inline-flex h-9 w-9 items-center justify-center rounded-[4px] border border-hairline text-muted transition-colors hover:border-gold-500 hover:text-gold-600">
              <?= icon($item['icon'], 'h-4 w-4') ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>
