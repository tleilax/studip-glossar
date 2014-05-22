<form action="<?= $controller->url_for('store', $id, $page) ?>" method="post">
    <fieldset>
        <legend>
        <?= _($id ? 'Kategorie bearbeiten' : 'Neue Kategorie erstellen') ?></legend>
        
        <div class="type-text required">
            <label for="category"><?= _('Name') ?>:</label>
            <input type="text" id="category" name="category" autofocus
                value="<?= htmlReady(Request::get('category', @$record)) ?>">
        </div>

        <div class="type-select">
            <label for="entries"><?= _('Einträge') ?></label>
            <select id="entries" name="entries[]" multiple>
            <? foreach (GlossarEntry::Load() as $id => $entry): ?>
                <option value="<?= $id ?>" <?= in_array($id, $record->entries) ? 'selected' : ''?>>
                    <?= $entry ?>
                </option>
            <? endforeach; ?>
            </select>
            <script>$(function(){
                $('#entries').prev().remove();
                STUDIP.MultiSelect.create('#entries', 'Einträge');
            });</script>
        </div>

        <div class="type-submit">
            <?= Studip\Button::createAccept(_('Speichern'), 'submit') ?>
            <?= Studip\LinkButton::createCancel(_('Abbrechen'),
                                                $controller->url_for('index', $page)) ?>
        </div>
    </fieldset>
</form>
