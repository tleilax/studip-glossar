<form action="<?= $controller->url_for('store', $id, $page) ?>" method="post">
    <fieldset>
        <legend>
        <?= _($id ? 'Eintrag bearbeiten' : 'Neuen Eintrag erstellen') ?></legend>
        
        <div class="type-text required">
            <label for="term"><?= _('Begriff') ?>:</label>
            <input type="text" id="term" name="term" autofocus required
                value="<?= htmlReady(Request::get('term', @$record['term'])) ?>">
        </div>

        <div class="type-text required">
            <label for="description"><?= _('Erläuterung') ?>:</label>
            <textarea id="description" name="description" required
                class="add_toolbar" cols="80" rows="8"><?= htmlReady(Request::get('term', @$record['description'])) ?></textarea>
        </div>

        <div class="type-text">
            <label for="link"><?= _('Link') ?></label>
            <input type="text" id="link" name="link" value="<?= htmlReady(Request::get('link', @$record['link'])) ?>">
        </div>

        <div class="type-select">
            <label for="categories"><?= _('Kategorien') ?></label>
            <select id="categories" name="categories[]" multiple style="height: 120px;">
            <? foreach (Glossar\Category::Load() as $id => $category): ?>
                <option value="<?= $id ?>" <?= in_array($id, $record->categories) ? 'selected' : ''?>>
                    <?= $category ?>
                </option>
            <? endforeach; ?>
            </select>
            <script>$(function(){
                $('#categories').prev().remove();
                STUDIP.MultiSelect.create('#categories', 'Kategorien');
            });</script>
        </div>

        <div class="type-submit">
            <?= Studip\Button::createAccept(_('Speichern'), 'submit') ?>
            <?= Studip\LinkButton::createCancel(_('Abbrechen'),
                                                $controller->url_for('index', $page)) ?>
        </div>
    </fieldset>
</form>
