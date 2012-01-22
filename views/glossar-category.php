<?php
function anchorify ($string) {
    $string = strtolower($string);
    $string = str_replace(array('ä', 'ö', 'ü'), array('ae', 'oe', 'ue'), $string);
    $string = str_replace(' ', '-', $string);
    $string = preg_replace('/[^a-z0-9-]/', '', $string);
    $string = preg_replace('/-{2,}/', '_', $string);
    return $string;
}
?>

<h1><a name="glossar-top"><?= htmlReady($category) ?></a></h1>

<ul class="glossar-letters">
<?php for ($i = 'A', $letters = $category->get_letters(); strlen($i) == 1 and $i <= 'Z'; $i++): ?>
    <li>
    <?php if (in_array($i, $letters)): ?>
        <a href="#<?= $i ?>"><?= $i ?></a>
    <?php else: ?>
        <?= $i ?>
    <?php endif; ?>
    </li>
<?php endfor; ?>
</ul>

<dl class="glossar">
<?php foreach ($data as $letter => $entries): ?>
    <dt><a id="<?= $letter ?>"><?= $letter ?></a></dt>
    <dd>
        <dl class="entries <?= $collapsable ? 'collapsable' : '' ?>">
        <?php foreach ($entries as $entry): ?>
        	<dt>
        	    <a href="#<?= $a = anchorify($entry) ?>" id="<?= $a ?>">
        	        <?= htmlReady($entry) ?>
        	    </a>
        	</dt>
        	<dd><?= formatReady($entry['description']) ?></dd>
        <?php endforeach; ?>
        </dl>
    </dd>
<?php endforeach; ?>
</dl>
