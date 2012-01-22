<?php
class ShowController extends GlossarController {

    public function before_filter(&$action, &$args) {
		parent::before_filter($action, $args);

		Navigation::activateItem($this->plugin->path.'/glossar');
		PageLayout::setTitle('Glossar');

		$this->collapsable = $this->context['collapsable'];
    }

    public function index_action() {
        log_event('FOO', '1', '2', '3', '4');
		$this->data = GlossarCategory::Load();

    }

	public function category_action($id) {
		if (!$id) {
			$this->redirect('show/index');
			return;
		}

		$this->category = new GlossarCategory($id);
		$this->data     = GlossarCategory::Get($id, true);

		PageLayout::setTitle('Glossar: '.$this->category);
	}

}
