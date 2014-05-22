<form action="<?= $controller->url_for() ?>" method="post">
    <select name="category_id" style="width: 100%;" onchange="this.form.submit();">
        <option value="">- Alle Kategorien -</option>
    <?php foreach (GlossarCategory::Load() as $category):
            $state = ($category_id === $category['id'] ? 'selected' : '');
        ?>
        <option value="<?= $category['id'] ?>" <?= $state ?>>
            <?= htmlReady($category) ?>
        </option>
    <?php endforeach; ?>
    </select>
    <noscript>
        <br>
        <?= Studip\Button::create(_('Auswählen'), 'auswaehlen') ?>
    </noscript>
</form>