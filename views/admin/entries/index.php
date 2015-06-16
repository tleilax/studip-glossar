<h1><?= $category ?: 'Alle Einträge' ?></h1>

<table class="paginated default">
    <colgroup>
        <col>
        <col width="35%">
        <col width="100px">
    </colgroup>
    <thead>
        <tr>
            <th><?= htmlReady(_('Begriff')) ?></th>
            <th><?= htmlReady(_('Kategorien')) ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody style="vertical-align: top;">
    <? foreach ($records as $record): ?>
        <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
        <? if ($record === null): ?>
            <td colspan="3" style="text-align: center;">&nbsp;</td>
        <? else: ?>
            <td>
                <a name="id<?= $record['id'] ?>"></a>
                <a href="<?= $controller->url_for('display', $record['id']) ?>">
                    <?= htmlReady($record) ?>
                </a>
            </td>
            <td><?= count($record->categories) ?></td>
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
        <? endif; ?>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>
