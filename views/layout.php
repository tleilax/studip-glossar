<?php ob_start(); ?>

<?php foreach (Request::optionArray('confirm') as $action => $id): ?>
    <?= createQuestion(Request::get('confirm_message', 'Bitte bestätigen Sie die Aktion'), array(
            'confirmed' => 'true',
            'confirm' => array($action => $id),
        )) ?>
<?php endforeach; ?>

<?= $content_for_layout ?>

<?php $content_for_layout = ob_get_clean(); ?>

<?php
    $layout = 'layouts/base_without_infobox';
    $variables = compact('content_for_layout');
    if ($infobox and !empty($infobox['content'])) {
        $layout = 'layouts/base';
        $variables += compact('infobox');
    }
    echo $GLOBALS['template_factory']->render($layout, $variables);
