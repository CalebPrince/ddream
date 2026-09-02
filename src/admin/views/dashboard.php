<?php
declare(strict_types=1);

/** @var array $stats @var array $byBasis @var array $recentEnquiries @var array $recentActivity */

$user = current_user();
$hour = (int) date('G');
$part = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

$tiles = [
    ['label' => 'New enquiries', 'value' => $stats['new_enquiries'], 'icon' => 'mail',
     'href' => admin_url('inbox?status=new'), 'urgent' => $stats['new_enquiries'] > 0],
    ['label' => 'Open enquiries', 'value' => $stats['open_enquiries'], 'icon' => 'clock',
     'href' => admin_url('inbox')],
    ['label' => 'Published property', 'value' => $stats['published'], 'icon' => 'key',
     'href' => admin_url('listings?state=published')],
    ['label' => 'Drafts', 'value' => $stats['drafts'], 'icon' => 'file-check',
     'href' => admin_url('listings?state=draft')],
];
?>

<p class="text-[0.9375rem] text-muted">
  <?= e($part) ?>, <?= e(explode(' ', (string) ($user['name'] ?? ''))[0]) ?>.
  <?php if ($stats['new_enquiries'] > 0): ?>
    <strong class="font-semibold text-navy-700">
      <?= (int) $stats['new_enquiries'] ?> <?= $stats['new_enquiries'] === 1 ? 'enquiry needs' : 'enquiries need' ?> a reply.
    </strong>
  <?php else: ?>
    Nothing is waiting on you.
  <?php endif; ?>
</p>

<!-- Tiles -->
<ul class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <?php foreach ($tiles as $tile): ?>
    <li>
      <a href="<?= e($tile['href']) ?>"
         class="card card-interactive group flex items-center gap-4 p-5">
        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-[4px] <?= !empty($tile['urgent']) ? 'bg-signal-600 text-white' : 'bg-navy-100 text-navy-700' ?>">
          <?= icon($tile['icon'], 'h-5 w-5') ?>
        </span>
        <span class="min-w-0">
          <span class="tabular block font-display text-3xl font-semibold leading-none text-navy-700"><?= (int) $tile['value'] ?></span>
          <span class="mt-1 block t-meta text-muted"><?= e($tile['label']) ?></span>
        </span>
        <?= icon('arrow-right', 'ml-auto h-4 w-4 shrink-0 text-muted transition-transform group-hover:translate-x-0.5') ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<div class="mt-6 grid gap-6 xl:grid-cols-3">

  <!-- Recent enquiries -->
  <section class="card xl:col-span-2" aria-labelledby="recent-enq">
    <div class="flex items-center justify-between gap-4 border-b border-hairline px-5 py-4">
      <h2 id="recent-enq" class="font-sans text-[0.9375rem] font-semibold text-navy-700">Latest enquiries</h2>
      <?php if (can('inbox.view')): ?>
        <a href="<?= e(admin_url('inbox')) ?>" class="t-meta font-semibold text-gold-600 hover:underline">Open Inbox</a>
      <?php endif; ?>
    </div>

    <?php if ($recentEnquiries === []): ?>
      <div class="px-5 py-10 text-center">
        <span class="inline-flex h-11 w-11 items-center justify-center rounded-[4px] bg-navy-100 text-navy-700">
          <?= icon('mail', 'h-5 w-5') ?>
        </span>
        <p class="mt-3 text-[0.9375rem] font-medium text-navy-700">No enquiries yet</p>
        <p class="mx-auto mt-1 max-w-sm t-meta text-muted">
          Once the contact form is live, everything sent from the site lands here.
        </p>
      </div>
    <?php else: ?>
      <ul class="divide-y divide-hairline">
        <?php foreach ($recentEnquiries as $enquiry): ?>
          <li>
            <a href="<?= e(admin_url('inbox/' . $enquiry['id'])) ?>"
               class="flex items-center gap-4 px-5 py-3.5 transition-colors hover:bg-canvas">
              <span class="badge shrink-0 <?= $enquiry['status'] === 'new' ? 'bg-signal-600 text-white' : 'border border-hairline bg-canvas text-muted' ?>">
                <?= e($enquiry['type']) ?>
              </span>
              <span class="min-w-0 flex-1">
                <span class="block truncate text-[0.9375rem] font-medium text-navy-700"><?= e($enquiry['name']) ?></span>
                <span class="block truncate t-meta text-muted"><?= e($enquiry['email']) ?></span>
              </span>
              <span class="shrink-0 t-meta text-muted"><?= e(time_ago($enquiry['created_at'])) ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <!-- Inventory + activity -->
  <div class="grid gap-6">
    <section class="card" aria-labelledby="inventory">
      <div class="border-b border-hairline px-5 py-4">
        <h2 id="inventory" class="font-sans text-[0.9375rem] font-semibold text-navy-700">Inventory</h2>
      </div>
      <?php if ($byBasis === []): ?>
        <div class="px-5 py-8 text-center">
          <p class="text-[0.9375rem] font-medium text-navy-700">No property yet</p>
          <?php if (can('listings.edit')): ?>
            <a href="<?= e(admin_url('listings/new')) ?>" class="btn btn-primary mt-4">Add the first one</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <ul class="divide-y divide-hairline">
          <?php foreach ($byBasis as $row): ?>
            <li class="flex items-center justify-between gap-4 px-5 py-3">
              <span class="text-[0.9375rem] text-navy-700"><?= e($row['basis']) ?></span>
              <span class="tabular font-display text-lg font-semibold text-gold-600"><?= (int) $row['total'] ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

    <section class="card" aria-labelledby="activity">
      <div class="border-b border-hairline px-5 py-4">
        <h2 id="activity" class="font-sans text-[0.9375rem] font-semibold text-navy-700">Recent activity</h2>
      </div>
      <?php if ($recentActivity === []): ?>
        <p class="px-5 py-8 text-center t-meta text-muted">Nothing recorded yet.</p>
      <?php else: ?>
        <ul class="divide-y divide-hairline">
          <?php foreach ($recentActivity as $entry): ?>
            <li class="px-5 py-3">
              <p class="text-[0.875rem] leading-snug text-navy-700">
                <?= e($entry['summary'] ?? ($entry['action'] . ' ' . $entry['entity'])) ?>
              </p>
              <p class="mt-0.5 t-meta text-muted">
                <?= e($entry['user_name'] ?? 'System') ?> &middot; <?= e(time_ago($entry['created_at'])) ?>
              </p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </div>
</div>
