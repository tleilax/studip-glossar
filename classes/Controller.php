<?php
namespace Glossar;
use StudipController;
use Request, PluginEngine, UserConfig;
use Flexi_TemplateFactory;

class Controller extends StudipController
{
    protected $plugin;
    protected $current_action;
    protected $flash;
    protected $template_factory;

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $this->dispatcher->plugin;
    }

// Infobox
    protected function populateInfobox()
    {
        if (!isset($this->infobox)) {
            $this->infobox = array(
                'picture' => 'blank.gif',
                'content' => array()
            );
        }
    }

    function setInfoBoxImage($image)
    {
        $this->infobox['picture'] = $image;
    }

    function addToInfobox($category, $text, $icon = 'blank.gif')
    {
        $infobox = $this->infobox;

        if (!isset($infobox['content'][$category])) {
            $infobox['content'][$category] = array(
                'kategorie' => $category,
                'eintrag'   => array(),
            );
        }
        $infobox['content'][$category]['eintrag'][] = compact('icon', 'text');

        $this->infobox = $infobox;
    }

// Defaults
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->plugin = $this->dispatcher->plugin;
        $this->context = Context::Get($this->plugin->context);

        Category::SetContext($this->context);
        Entry::SetContext($this->context);
        Lists::SetContext($this->context);

        $this->current_action = $action;

        $this->template_factory = new Flexi_TemplateFactory($this->plugin->getPluginPath().'/views/');
        $layout = $this->template_factory->open('layout');
        $this->set_layout($layout);
    }
    
// Pagination
    public function paginate(&$records, $page, $action = '')
    {
        $this->config = UserConfig::get($GLOBALS['auth']->auth['uid']);

        $max_per_page = $this->config->getValue('ENTRIES_PER_PAGE') ?: 20;
        $max_page = ceil(count($records) / $max_per_page) - 1;

        $this->pagination = $this->template_factory->render('pagination', array(
            'controller'   => $this,
            'max_page'     => $max_page,
            'max_per_page' => $max_per_page,
            'page'         => $page,
            'action'       => $action,
        ));

        $records = array_slice($records, $page * $max_per_page, $max_per_page);
        $records = array_pad($records, $max_per_page, null);

        $this->addToInfobox('', $this->pagination);
    }

//
    public function check_confirmation()
    {
        $arguments = func_get_args();

        if (count(Request::optionArray('confirm')) and Request::option('confirmed')) {
            foreach (Request::optionArray('confirm') as $action => $id) {
                if (Request::option('confirmed') === 'true') {
                    $args = array_merge(array($action, $id), $arguments);
                    call_user_func_array(array($this, 'redirect'), $args);
                } else {
                    $this->redirect('index');
                }
            }
            return true;
        }
        return false;
    }

// URLs
    private function url_params(&$arguments)
    {
        $params = is_array(end($arguments)) ? array_pop($arguments) : array();

        $to = array_shift($arguments);
        if (empty($to)) {
            $to = $this->current_action;
        }
        if (strpos($to, '/') === false) {
            $path = substr(strtolower(get_class($this)), 0, -10);
            $path = str_replace('_', '/', $path);
            $to = $path.'/'.$to;
        }
        if (count(array_filter($arguments))) {
            $to .= '/'.implode('/', array_filter($arguments));
        }

        $arguments = $params;
        
        return sprintf('%s/%s', $this->context->path(), $to);
    }

    public function redirect($to = '', $params = array())
    {
        header('Location: '.$this->url_for($to, $params));
        die;
    }

    public function url_for($to = '', $params = array())
    {
        $params = func_get_args();
        $to = $this->url_params($params);
        return PluginEngine::getUrl($this->plugin, $params, $to);
    }

    public function link_for($to = '', $params = array())
    {
        $params = func_get_args();
        $to = $this->url_params($params);
        return PluginEngine::getLink($this->plugin, $params, $to);
    }
}