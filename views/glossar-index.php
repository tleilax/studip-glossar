<h1><?= htmlReady(_('�bersicht')) ?></h1>
<ul class="glossar">
<? foreach ($data as $record): ?>
    <li>
        <a href="<?= $controller->url_for('category', $record['id']) ?>">
            <?= htmlReady($record) ?>
        </a>
    </li>
<? endforeach; ?>
</ul>
