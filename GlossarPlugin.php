<?php
require 'bootstrap.php';

/**
 * GlossarPlugin.php
 *
 * @author    Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @copyright IBIT 2011-2012
 * @version   0.7
 */
class GlossarPlugin extends StudIPPlugin implements SystemPlugin, StandardPlugin, HomepagePlugin {

    const CONFIG_HOMEPAGE   = 'GLOSSARPLUGIN_ACTIVATED_ON_PROFILE_PAGES';
    const CONFIG_RESTRICTED = 'GLOSSARPLUGIN_PROFILE_PAGES_RESTRICTED';
    const CSS = '/assets/glossar.css';
    const JS  = '/assets/glossar.js';

    private static $paths = array(
        'course'   => '/course',
        'global'   => '',
        'homepage' =>  '/profile',
    );

    public function __construct() {
        parent::__construct();

        $this->add_navigation('global');

        $config = Config::getInstance();
        if ($config[self::CONFIG_HOMEPAGE]) {
            $this->add_navigation('homepage');
        }

        if (Navigation::hasItem('/course')) {
            $context = Request::option('cid');
            if ($this->isActivated($context)) {
                $this->add_navigation('course');
            }
        }
    }

    private function add_navigation ($context) {
        $path = self::$paths[$context].'/glossar';

        $ctx = GlossarContext::Get($context);

        if (!(($ctx['active'] and $ctx['public']) or $ctx->priviledgedAccess())) {
            return;
        }

        if ($context === 'homepage' and !$ctx['active']) {
            $config = Config::getInstance();
            if ($config[self::CONFIG_RESTRICTED] and !$GLOBALS['perm']->have_perm($config[self::CONFIG_RESTRICTED])) {
                return;
            }
        }

        $navigation = new AutoNavigation(_('Glossar'));
        $navigation->setURL(PluginEngine::getURL($this, array(), $context.($ctx['active'] ? '/show/index' : '/admin/settings')));
        if ($context === 'global') {
            // TODO real icon
            $navigation->setImage(Assets::image_path('blank.gif'));
        }
        Navigation::addItem($path, $navigation);

        if ($ctx['active']) {
            $navigation = new AutoNavigation(_('Übersicht'));
            $navigation->setURL(PluginEngine::GetURL($this, array(), $context.'/show/index'));
            Navigation::addItem($path.'/index', $navigation);
        }

        if (!$ctx->priviledgedAccess()) {
            return;
        }

        if ($ctx['active']) {
            $navigation = new AutoNavigation(_('Kategorien'));
            $navigation->setURL(PluginEngine::GetURL($this, array(), $context.'/admin/categories'));
            $navigation->setImage(Assets::image_path('icons/16/black/folder-full.png'));
            Navigation::addItem($path.'/categories', $navigation);

            $navigation = new AutoNavigation(_('Einträge'));
            $navigation->setURL(PluginEngine::GetURL($this, array(), $context.'/admin/entries'));
            $navigation->setImage(Assets::image_path('icons/16/black/edit.png'));
            Navigation::addItem($path.'/entries', $navigation);
        }

        if ($ctx->priviledgedAccess(true)) {
            $navigation = new AutoNavigation(_('Einstellungen'));
            $navigation->setURL(PluginEngine::GetURL($this, array(), $context.'/admin/settings'));
            $navigation->setImage(Assets::image_path('icons/16/black/admin.png'));
            Navigation::addItem($path.'/settings', $navigation);
        }
    }

    public function getIconNavigation ($course_id, $last_visit) {
        return null;
    }

    public function getInfoTemplate($course_id) {
        return null;
    }

    public function getHomepageTemplate($user_id) {
        return null;
    }

    public function initialize() {
        // Add css to site header (create if neccessary)
        if (isset($_REQUEST['debug']) or !file_exists($this->getPluginPath().self::CSS)) {
            $factory = new Flexi_TemplateFactory($this->getPluginPath().'/templates/');
            $css = $factory->render('css');
            file_put_contents($this->getPluginPath().self::CSS, $css);
        }
        PageLayout::addStylesheet($this->getPluginURL().self::CSS);
        PageLayout::addStylesheet(Assets::stylesheet_path('ui.multiselect.css'));

        PageLayout::addScript($this->getPluginURL().self::JS);
        PageLayout::addScript(Assets::javascript_path('ui.multiselect.js'));
    }

    /**
     * This method dispatches all actions.
     *
     * @param string   part of the dispatch path that was not consumed
     */
    public function perform($unconsumed_path) {

        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'show'
        );
        $dispatcher->plugin = $this;

        $tokens = explode('/', $unconsumed_path);
        $this->context = array_shift($tokens);
        $this->path    = self::$paths[$this->context];
        $unconsumed_path = implode('/', $tokens);

        $context = GlossarContext::Get($this->context);
        if (!(($context['active'] and $context['public']) or $context->priviledgedAccess())) {
            throw new AccessDeniedException('You should not be here!');
        }

        $dispatcher->dispatch($unconsumed_path);
    }

}
