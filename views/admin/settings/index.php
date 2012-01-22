<?php
    $status = array(
        ''       => _('Offen f�r alle'),
        'autor'  => 'autor',
        'tutor'  => 'tutor',
        'dozent' => 'dozent',
        'admin'  => 'admin',
        'root'   => 'root',
    );
?>
<form class="settings" action="<?= $controller->url_for('store') ?>" method="post">
    <fieldset>
        <legend><?= _('Einstellungen dieses Glossars') ?></legend>
        
        <div class="type-checkbox">
            <label for="active"><?= _('Aktiviert') ?></label>
            <input type="checkbox" id="active" name="active" value="1"
                <?= $context['active'] ? 'checked' : '' ?>>
        </div>
        
        <div class="type-checkbox">
            <label for="public"><?= _('�ffentlich') ?></label>
            <input type="checkbox" id="public" name="public" value="1"
                <?= $context['public'] ? 'checked' : '' ?>>
        </div>

        <div class="type-checkbox">
            <label for="open">
                <?= _('�ffentlich bearbeitbar') ?>
                <small><?= _('Durch diesen Schalter k�nnen Sie bestimmen, ob alle Nutzer des Systems dieses Glossar bearbeiten k�nnen.') ?></small>
            </label>
            <input type="checkbox" id="open" name="open" value="1"
                <?= $context['open'] ? 'checked' : '' ?>>
        </div>

        <div class="type-checkbox">
            <label for="collapsable"><?= _('Klappbare Eintr�ge') ?></label>
            <input type="checkbox" id="collapsable" name="collapsable" value="1"
                <?= $context['collapsable'] ? 'checked' : '' ?>>
        </div>

    </fieldset>

<?php if ((string)$context === 'global'): ?>
    <fieldset>
        <legend><?= _('Globale Einstellungen') ?></legend>
        
        <div class="type-checkbox">
            <label for="homepage">
                <?= _('Auf Profilseiten aktiviert') ?>
                <small><?= _('Sollen Nutzer eigene Glossare auf ihren Profilseiten nutzen k�nnen?') ?></small>
            </label>
            <input type="checkbox" id="homepage" name="homepage" value="1"
                <?= $homepage ? 'checked' : '' ?>>
        </div>

        <div class="type-checkbox">
            <label for="free">
                <?= _('Selbstaktivierbar ab folgendem Status') ?>
                <small><?= _('Nutzer, die mindestens diesen Status haben, k�nnen Ihr Glossar selbst aktivieren.') ?></small>
            </label>
            <select name="restricted" id="restricted">
            <?php foreach ($status as $key => $title): ?>
                <option value="<?= $key ?>" <?= $key == $restricted ? 'selected' : ''?>>
                    <?= $title ?>
                </option>
            <?php endforeach; ?>
            </select>
        </div>
    </fieldset>
<?php endif; ?>

    <div class="type-button">
        <?= makebutton('absenden', 'input', null, 'submit') ?>
        <a href="<?= $controller->url_for('') ?>" class="cancel">
            <?= makebutton('abbrechen', 'img') ?>
        </a>
    </div>
</form>
