<?
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
<? for ($i = 'A', $letters = $category->get_letters(); strlen($i) == 1 and $i <= 'Z'; $i++): ?>
    <li>
    <? if (in_array($i, $letters)): ?>
        <a href="#<?= $i ?>"><?= $i ?></a>
    <? else: ?>
        <?= $i ?>
    <? endif; ?>
    </li>
<? endfor; ?>
</ul>

<dl class="glossar">
<? foreach ($data as $letter => $entries): ?>
    <dt><a id="<?= $letter ?>"><?= $letter ?></a></dt>
    <dd>
        <dl class="entries <?= $collapsable ? 'collapsable' : '' ?>">
        <? foreach ($entries as $entry): ?>
            <dt>
                <a href="#<?= $a = anchorify($entry) ?>" id="<?= $a ?>">
                    <?= htmlReady($entry) ?>
                </a>
            </dt>
            <dd><?= formatReady($entry['description']) ?></dd>
        <? endforeach; ?>
        </dl>
    </dd>
<? endforeach; ?>
</dl>
