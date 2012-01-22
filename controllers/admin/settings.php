<?php
class Admin_SettingsController extends GlossarController {

    public function before_filter(&$action, &$args) {

        parent::before_filter($action, $args);

        Navigation::activateItem($this->plugin->path.'/glossar/settings');
        PageLayout::setTitle('Glossar - Einstellungen');

        $this->setInfoboxImage('infobox/administration.jpg');
    }

    public function index_action () {
        $config = Config::GetInstance();        
        $this->homepage   = $config[GlossarPlugin::CONFIG_HOMEPAGE];
        $this->restricted = $config[GlossarPlugin::CONFIG_RESTRICTED];
    }
    
    public function store_action () {
        if (Request::submitted('submit')) {
            $this->context['active']      = (bool)Request::int('active', 0);
            $this->context['collapsable'] = (bool)Request::int('collapsable', 0);
            $this->context['open']        = (bool)Request::int('open', 0);
            $this->context['public']      = (bool)Request::int('public', 0);
            $this->context->store();

            if ((string)$this->context === 'global') {
                $this->store_config(
                    GlossarPlugin::CONFIG_HOMEPAGE, 
                    Request::int('homepage', 0),
                    'Glossar activated on profile pages?'
                );
                $this->store_config(
                    GlossarPlugin::CONFIG_RESTRICTED,
                    Request::option('restricted', ''),
                    'Minimum status a user must have to be able to activate his glossar'
                );
            }

            PageLayout::postMessage(MessageBox::success(_('Die Einstellungen wurden gespeichert.')));
        }
        $this->redirect('admin/settings');
    }
    
    private function store_config($key, $value, $comment) {
        $config = Config::GetInstance();
        try {
            $config->store($key, $value);
        } catch (Exception $e) {
            $config->create($key, compact('value', 'comment'));
        }
    }
}
