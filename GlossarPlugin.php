<?php
require 'bootstrap.php';

/**
 * GlossarPlugin.php
 *
 * @author    Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @copyright IBIT 2011-2012
 * @version   0.7
 */
class GlossarPlugin extends StudIPPlugin implements SystemPlugin, StandardPlugin, HomepagePlugin
{

    const CONFIG_HOMEPAGE   = 'GLOSSARPLUGIN_ACTIVATED_ON_PROFILE_PAGES';
    const CONFIG_RESTRICTED = 'GLOSSARPLUGIN_PROFILE_PAGES_RESTRICTED';
    const LESS = '/assets/glossar.less';
    const JS   = '/assets/glossar.js';

    private static $paths = array(
        'course'   => '/course',
        'global'   => '',
        'homepage' =>  '/profile',
        'zsb'      => '/zsb',
    );

    public function __construct()
    {
        parent::__construct();

        if (PluginEngine::getPlugin('eStudienplaner') !== null) {
            if (!class_exists('PersonalRechte')) {
                require_once 'plugins_packages/data-quest/eStudienplaner/models/personalRechte.php';
            }
            if (PersonalRechte::isRoot()) {
                $navigation = new Navigation(_('Glossare'));
                $navigation->setURL(PluginEngine::getLink('glossarplugin/zsb/show/index'));

                $navigation->addSubNavigation('index', new Navigation(_('Übersicht'), PluginEngine::getLink('glossarplugin/zsb/show/index')));
                $navigation->addSubNavigation('categories', new Navigation(_('Kategorien'), PluginEngine::getLink('glossarplugin/zsb/admin/categories')));
                $navigation->addSubNavigation('entries', new Navigation(_('Einträge'), PluginEngine::getLink('glossarplugin/zsb/admin/entries')));
                $navigation->addSubNavigation('settings', new Navigation(_('Einstellungen'), PluginEngine::getLink('glossarplugin/zsb/admin/settings')));

                Navigation::insertItem('/zsb/glossar', $navigation, 'verwaltung');
                Navigation::addItem('/start/zsb/glossar', $navigation);
            }
        }

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

    private function add_navigation ($context)
    {
        $path = self::$paths[$context].'/glossar';

        $ctx = Glossar\Context::Get($context);

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

    public function getIconNavigation ($course_id, $last_visit, $user_id)
    {
        return null;
    }

    public function getInfoTemplate($course_id)
    {
        return null;
    }

    public function getHomepageTemplate($user_id)
    {
        return null;
    }

    public function getTabNavigation($course_id)
    {
        // TODO: Move code from add_navigation here
        return array();
    }

    public function getNotificationObjects($course_id, $since, $user_id)
    {
        return array();
    }

    /**
     * This method dispatches all actions.
     *
     * @param string   part of the dispatch path that was not consumed
     */
    public function perform($unconsumed_path)
    {
        $this->addStylesheet(self::LESS);
        PageLayout::addStylesheet(Assets::stylesheet_path('jquery-ui-multiselect.css'));

        PageLayout::addScript($this->getPluginURL().self::JS);
        PageLayout::addScript(Assets::javascript_path('ui.multiselect.js'));

        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'show'
        );
        $dispatcher->plugin = $this;

        $tokens          = explode('/', $unconsumed_path);
        $this->context   = array_shift($tokens);
        $unconsumed_path = implode('/', $tokens);

        $this->path = self::$paths[$this->context];

        $context = Glossar\Context::Get($this->context);
        if (!$context['active'] || !($context['public'] || $context->priviledgedAccess())) {
            throw new AccessDeniedException('You should not be here!');
        }

        $dispatcher->dispatch($unconsumed_path);
    }
}
