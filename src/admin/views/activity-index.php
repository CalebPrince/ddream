<?php declare(strict_types=1); ?>
<div class="mb-5 flex items-end justify-between gap-4"><div><p class="text-sm text-muted">A permanent record of staff changes.</p></div><p class="t-meta text-muted"><?= number_format($total) ?> events</p></div>
<div class="card overflow-hidden">
  <?php if (!$rows): ?><p class="p-8 text-center text-muted">No activity has been recorded yet.</p><?php else: ?>
  <div class="overflow-x-auto"><table class="w-full text-left"><thead class="border-b border-hairline bg-canvas text-xs uppercase tracking-wider text-muted"><tr><th class="px-5 py-3">When</th><th class="px-5 py-3">Staff</th><th class="px-5 py-3">Action</th><th class="px-5 py-3">Details</th><th class="px-5 py-3">IP</th></tr></thead><tbody class="divide-y divide-hairline">
  <?php foreach($rows as $row): ?><tr><td class="whitespace-nowrap px-5 py-4 t-meta text-muted" title="<?= e(nice_date($row['created_at'])) ?>"><?= e(time_ago($row['created_at'])) ?></td><td class="px-5 py-4 font-semibold text-navy-700"><?= e($row['user_name'] ?: 'System') ?></td><td class="px-5 py-4"><span class="badge bg-navy-100 text-navy-700"><?= e($row['action']) ?></span></td><td class="px-5 py-4 text-sm"><p><?= e($row['summary'] ?: $row['entity']) ?></p><p class="t-meta text-muted"><?= e($row['entity']) ?><?= $row['entity_id'] ? ' #'.e($row['entity_id']) : '' ?></p></td><td class="px-5 py-4 t-meta text-muted"><?= e($row['ip'] ?: '—') ?></td></tr><?php endforeach; ?>
  </tbody></table></div><?php endif; ?>
</div>
<?php if($pages>1): ?><nav class="mt-5 flex justify-end gap-2" aria-label="Pages"><?php for($i=1;$i<=$pages;$i++): ?><a class="btn <?= $i===$page?'btn-primary':'btn-outline' ?>" href="<?= e(admin_url('activity?page='.$i)) ?>"><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
