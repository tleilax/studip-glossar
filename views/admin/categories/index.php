<h1><?= $category_id ? '' : 'Alle Kategorien' ?></h1>

<table class="paginated default">
	<colgroup>
		<col>
		<col width="100px">
		<col width="100px">
	</colgroup>
	<thead>
		<tr>
			<th><?= htmlReady(_('Kategorie')) ?></th>
			<th><?= htmlReady(_('Einträge')) ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody style="vertical-align: top;">
	<?php foreach ($records as $record): ?>
		<tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
		<?php if ($record === null): ?>
			<td colspan="3" style="text-align: center;">&nbsp;</td>
		<?php else: ?>
			<td>
				<a href="<?= $controller->url_for('admin/entries', $record['id']) ?>" name="id<?= $record['id'] ?>">
    				<?= htmlReady($record) ?>
			    </a>
			</td>
			<td><?= count($record->entries) ?></td>
			<td style="text-align: right;">
				<a href="<?= $controller->url_for('edit', $record['id'], $page) ?>">
					<?= Assets::img('icons/16/blue/edit.png', array(
							'title' => _('Eintrag bearbeiten'),
							'alt'   => 'edit',
						)) ?>
				</a>
				<a href="<?= $controller->url_for('index', $page, array('confirm[delete]' => $record['id'])) ?>">
					<?= Assets::img('icons/16/blue/trash.png', array(
							'title' => _('Eintrag löschen'),
							'alt'   => 'del',
						)) ?>
				</a>
			</td>
		<?php endif; ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
