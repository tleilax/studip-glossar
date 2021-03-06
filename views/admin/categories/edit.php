<form action="<?= $controller->url_for('store', $id, $page) ?>" method="post">
    <fieldset>
        <legend>
        <?= _($id ? 'Kategorie bearbeiten' : 'Neue Kategorie erstellen') ?></legend>
        
        <div class="type-text required">
            <label for="category"><?= _('Name') ?></label>
            <input type="text" id="category" name="category" autofocus required
                value="<?= htmlReady(Request::get('category', @$record['category'])) ?>">
        </div>

        <div class="type-text">
            <label for="description"><?= _('Beschreibung') ?></label>
            <textarea id="description" name="description"><?= htmlReady(Request::get('description', @$record['description'])) ?></textarea>
        </div>

        <div class="type-select">
            <label for="entries"><?= _('Eintr�ge') ?></label>
            <select id="entries" name="entries[]" multiple>
            <? foreach (GlossarEntry::Load() as $id => $entry): ?>
                <option value="<?= $id ?>" <?= in_array($id, $record->entries) ? 'selected' : ''?>>
                    <?= $entry ?>
                </option>
            <? endforeach; ?>
            </select>
            <script>$(function(){
                $('#entries').prev().remove();
                STUDIP.MultiSelect.create('#entries', 'Eintr�ge');
            });</script>
        </div>

        <div class="type-submit">
            <?= Studip\Button::createAccept(_('Speichern'), 'submit') ?>
            <?= Studip\LinkButton::createCancel(_('Abbrechen'),
                                                $controller->url_for('index', $page)) ?>
        </div>
    </fieldset>
</form>
