<?php
class Admin_EntriesController extends Glossar\Controller
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if ($action === 'new') {
            $action = 'edit';
        } elseif (is_numeric($action)) {
            $this->category_id = $action;
            $action = 'index';
        }

        if ($action === 'index' and isset($_REQUEST['category_id'])) {
            $id = Request::int('category_id');
            $this->redirect('admin/entries/' . ($id ?: 'index'));
        }

        Navigation::activateItem($this->plugin->path.'/glossar/entries');
        PageLayout::setTitle('Glossar - Einträge');

        $this->setInfoboxImage('infobox/administration.jpg');
    }

    public function index_action($page = 0)
    {
        if ($this->check_confirmation($page)) {
            return;
        }

        if ($this->category_id) {
            $this->category = new Glossar\Category($this->category_id);
            $this->records  = Glossar\Category::Get($this->category_id);
        } else {
            $this->records = Glossar\Entry::Load();
        }
        $this->page = $page;

        // Infobox
        $factory = new Flexi_TemplateFactory($this->plugin->getPluginPath().'/templates');
        $template = $factory->open('entries-infobox.php');
        $template->set_attribute('category_id', $this->category_id);
        $template->set_attribute('controller', $this);
        $this->addToInfobox('Kategorie wählen', $template->render());

        $html = '<a href="'.$this->url_for('new', array('category_id' => $this->category_id)).'">Neuer Eintrag</a>';
        $title = sprintf(_('%u Einträge'), count($this->records));
        $this->addToInfobox($title, $html, 'icons/16/black/plus.png');

        $this->paginate($this->records, $page, $this->category_id ?: '');
    }

    public function display_action($id)
    {
        $this->record = new Glossar\Entry($id);
        $this->render_text(formatReady($this->record['description']));
    }

    public function edit_action ($id = null, $page = 0)
    {
        $this->record = new Glossar\Entry($id);
        if ($id === null) {
            $this->record->categories = array(Request::int('category_id'));
        }
        $this->page = $page;
        $this->id = $id;

    }

    public function store_action ($id = null, $page = 0)
    {
        if (Request::submitted('submit')) {
            $this->record = new Glossar\Entry($id);
            $this->record['term']        = trim(Request::get('term'));
            $this->record['description'] = trim(Request::get('description'));
            $this->record['link']        = trim(Request::get('link'));
            $this->record->categories    = Request::intArray('categories', array());
            $this->record->store();

            $message = $id
                ? _('Der Eintrag wurde bearbeitet.')
                : _('Der Eintrag wurde erstellt.');
            PageLayout::postMessage(MessageBox::success($message));
        }
        $this->redirect('index', $page);
    }

    public function delete_action ($id, $page = 0)
    {
        $record = new Glossar\Entry($id);
        $record->delete();

        PageLayout::postMessage(MessageBox::success(_('Der Eintrag wurde gelöscht.')));
        $this->redirect('index', $page);
    }
}
