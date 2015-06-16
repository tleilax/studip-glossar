<? ob_start(); ?>

<? foreach (Request::optionArray('confirm') as $action => $id): ?>
    <?= createQuestion(Request::get('confirm_message', 'Bitte bestätigen Sie die Aktion'), array(
            'confirmed' => 'true',
            'confirm' => array($action => $id),
        )) ?>
<? endforeach; ?>

<?= $content_for_layout ?>

<? $content_for_layout = ob_get_clean(); ?>

<?
    $layout = 'layouts/base.php';
    $variables = compact('content_for_layout');
    if ($infobox && !empty($infobox['content'])) {
        $layout = 'layouts/base';
        $variables += compact('infobox');
    }
    echo $GLOBALS['template_factory']->render($layout, $variables);
