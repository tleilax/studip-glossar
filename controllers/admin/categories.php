<?php
class Admin_CategoriesController extends Glossar\Controller
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem($this->plugin->path.'/glossar/categories');
        PageLayout::setTitle('Glossar - Kategorien');

        $this->setInfoboxImage('infobox/administration.jpg');
    }

    public function index_action($page = 0)
    {
        if ($this->check_confirmation($page)) {
            return;
        }

        $html = '<a href="'.$this->url_for('edit').'">Neuer Eintrag</a>';
        $this->addToInfobox('', $html, 'icons/16/black/plus.png');

        $this->records = Glossar\Category::Load();
        $this->paginate($this->records, $page);
    }

    public function edit_action($id = null, $page = 0)
    {
        $this->record = new Glossar\Category($id);
        $this->id     = $id;
        $this->page   = $page;

    }

    public function store_action ($id = null, $page = 0)
    {
        if (Request::submitted('submit')) {
            $this->record = new Glossar\Category($id);
            $this->record['category']    = trim(Request::get('category'));
            $this->record['description'] = trim(Request::get('description'));
            $this->record->store();

            $this->record->assign_entries(Request::intArray('entries', array()));

            $message = $id
                ? _('Die Kategorie wurde bearbeitet.')
                : _('Die Kategorie wurde erstellt.');
            PageLayout::postMessage(MessageBox::success($message));
        }
        $this->redirect('index', $page);
    }

    public function delete_action ($id, $page = 0) {
        $record = new Glossar\Category($id);
        $record->delete();

        PageLayout::postMessage(MessageBox::success(_('Der Eintrag wurde gelöscht.')));
        $this->redirect('index');
    }

}