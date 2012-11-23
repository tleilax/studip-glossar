<?php
class GlossarEntry extends Plain_ORM {

    protected static $TABLE = 'glossar';
    protected static $COLUMNS = array(
        'context'     => 'primary',
        'glossar_id'  => 'primary',
        'term'        => 'required',
        'description' => 'required',
        'chdate'      => 'unix-timestamp',
        'chuserid'    => 'userid',
    );
    protected static $ORDER = '`term` ASC';
    protected static $ID_COLUMN = 'glossar_id';
    protected static $CONNECTIONS = array();

    public $categories = array();

    public function populate($data) {
        if (!empty($data['glossar_id'])) {
            $temp = GlossarList::Filter(array('glossar_id' => $data['glossar_id']));
            $this->categories = array_pluck($temp, 'category_id');
        }
        return parent::populate($data);
    }
    
    public function store() {
        parent::store();
        $this->assign_categories($this->categories);
        return true;
    }
    
    public function delete($id = null) {
        if ($success = parent::delete($id)) {
            DBManager::get()
                ->prepare("DELETE FROM glossar_lists WHERE context = ? AND glossar_id = ?")
                ->execute(array(self::$context, $id));
        }
        return $success;
    }

    protected static function LoadFromDB($ids) {

        GlossarList::Filter(array('glossar_id' => $ids));

        return parent::LoadFromDB($ids);
    }

    public function assign_categories($categories) {
        if ($this['id']) {
            DBManager::get()
                ->prepare("DELETE FROM glossar_lists WHERE context = ? AND glossar_id = ? ")
                ->execute(array(self::$context, $this['id']));
        }

        $statement = DBManager::get()->prepare("INSERT INTO glossar_lists (context, glossar_id, category_id, chdate, chuserid) VALUES (?, ?, ?, UNIX_TIMESTAMP(), ?)");
        foreach ($categories as $id) {
            $statement->execute(array(self::$context, $this['id'], $id, $GLOBALS['user']->id));
        }
        $this->categories = $categories;
    }
    
    public function __toString() {
        return ''.@$this['term'];
    }
}