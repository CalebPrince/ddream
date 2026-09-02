<?php declare(strict_types=1); ?>
<div class="card divide-y divide-hairline">
  <?php if (!$rows): ?><div class="p-10 text-center"><h2 class="t-h3">No editable pages found</h2><p class="mt-2 text-muted">Run the latest database migrations to initialise page content.</p></div><?php endif; ?>
  <?php foreach($rows as $row): ?><a class="flex items-center gap-4 px-5 py-4 hover:bg-canvas" href="<?= e(admin_url('pages/'.$row['id'])) ?>"><div class="flex-1"><p class="font-semibold text-navy-700"><?= e($row['name']) ?></p><p class="t-meta text-muted">/<?= e($row['slug']==='home'?'':$row['slug']) ?></p></div><span class="t-meta text-muted"><?= (int)$row['enabled_count'] ?> of <?= (int)$row['section_count'] ?> sections enabled</span><?= icon('chevron-right','h-4 w-4 text-muted') ?></a><?php endforeach; ?>
</div>
