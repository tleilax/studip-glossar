<div class="pagination">
<?php if ($page): ?>
	<a href="<?= $controller->url_for($action) ?>">
		<?= Assets::img('icons/16/blue/arr_eol-left.png') ?>
	</a>
	<a href="<?= $controller->url_for($action, $page - 1) ?>">
		<?= Assets::img('icons/16/blue/arr_1left.png') ?>
	</a>
<?php else: ?>
	<?= Assets::img('icons/16/grey/arr_eol-left.png') ?>
	<?= Assets::img('icons/16/grey/arr_1left.png') ?>
<?php endif; ?>
	Seite <strong><?= $page + 1 ?></strong> von <strong><?= $max_page + 1 ?></strong>
<?php if ($page < $max_page): ?>
	<a href="<?= $controller->url_for($action, $page + 1) ?>">
		<?= Assets::img('icons/16/blue/arr_1right.png') ?>
	</a>
	<a href="<?= $controller->url_for($action, $max_page) ?>">
		<?= Assets::img('icons/16/blue/arr_eol-right.png') ?>
	</a>
<?php else: ?>
	<?= Assets::img('icons/16/grey/arr_1right.png') ?>
	<?= Assets::img('icons/16/grey/arr_eol-right.png') ?>
<?php endif; ?>
</div>
