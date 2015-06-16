<?php
namespace Glossar;
use Plain_ORM;
use Exception;
use URLHelper, Request, User;

class Context extends Plain_ORM
{

    protected static $TABLE = 'glossar_context';
    protected static $COLUMNS = array(
        'context'     => 'primary',
        'active'      => 'boolean',
        'collapsable' => 'boolean',
        'public'      => 'boolean',
        'open'        => 'boolean',
        'chdate'      => 'unix-timestamp',
        'chuserid'    => 'userid',
    );
    protected static $ID_COLUMN = 'context';
    
    protected $type;
    
    public function __construct($context, $type)
    {
        try {
            parent::__construct($context);
        } catch (Exception $e) {
            $this['context']      = $context;
            $this['active']       = true;
            $this['collapsable']  = true;
            $this['public']       = false;
            $this['open']         = false;
            $this->store();         
        }

        $this->type = $type;
    }
    
    public function path ()
    {
        return $this->type;
    }

    public function priviledgedAccess($strict = false)
    {
        // Homepage and user's homepage
        if ($this->type === 'homepage' && $this['context'] == $GLOBALS['user']->id) {
            return true;
        }
        // Course and user is at least tutor
        if ($this->type === 'course' && $GLOBALS['perm']->have_studip_perm('tutor', $this['context'])) {
            return true;
        }
        if ($this->type === 'zsb') {
            return true;
        }
        if (!$strict and $this['open']) {
            return true;
        }
        
        return $GLOBALS['perm']->have_perm('root');
    }

    public function __toString ()
    {
        return $this['context'];
    }
    
/** **/

    public static function Get($context)
    {
        $ctx = $context;
        
        if ($context === 'course') {
            $context = Request::option('cid');
            URLHelper::addLinkParam('cid', $context);
        } elseif ($context === 'homepage') {
            $context = Request::get('username');
            if ($context) {
                URLHelper::addLinkParam('username', $context);
                $context = User::findByUsername($context)->user_id;
            } else {
                $context = $GLOBALS['user']->id;
            }
        }
        return new self($context, $ctx);        
    }
}
