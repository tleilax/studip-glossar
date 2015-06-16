<?php
class ShowController extends Glossar\Controller
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem($this->plugin->path . '/glossar/index');
        PageLayout::setTitle('Glossar');

        $this->collapsable = $this->context['collapsable'];
    }

    public function index_action()
    {
        $this->data = Glossar\Category::Load();

    }

    public function category_action($id)
    {
        if (!$id) {
            $this->redirect('show/index');
            return;
        }

        $this->category = new Glossar\Category($id);
        $this->data     = Glossar\Category::Get($id, true);

        PageLayout::setTitle('Glossar: ' . $this->category);
    }

}
