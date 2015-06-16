<div class="pagination">
<? if ($page): ?>
    <a href="<?= $controller->url_for($action) ?>">
        <?= Assets::img('icons/16/blue/arr_eol-left.png') ?>
    </a>
    <a href="<?= $controller->url_for($action, $page - 1) ?>">
        <?= Assets::img('icons/16/blue/arr_1left.png') ?>
    </a>
<? else: ?>
    <?= Assets::img('icons/16/grey/arr_eol-left.png') ?>
    <?= Assets::img('icons/16/grey/arr_1left.png') ?>
<? endif; ?>
    Seite <strong><?= $page + 1 ?></strong> von <strong><?= $max_page + 1 ?></strong>
<? if ($page < $max_page): ?>
    <a href="<?= $controller->url_for($action, $page + 1) ?>">
        <?= Assets::img('icons/16/blue/arr_1right.png') ?>
    </a>
    <a href="<?= $controller->url_for($action, $max_page) ?>">
        <?= Assets::img('icons/16/blue/arr_eol-right.png') ?>
    </a>
<? else: ?>
    <?= Assets::img('icons/16/grey/arr_1right.png') ?>
    <?= Assets::img('icons/16/grey/arr_eol-right.png') ?>
<? endif; ?>
</div>
