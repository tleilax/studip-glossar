<?php
namespace Glossar;
use Plain_ORM;
use DBManager, PDO;

class Category extends Plain_ORM
{

    protected static $TABLE = 'glossar_categories';
    protected static $COLUMNS = array(
        'context'     => 'primary',
        'category_id' => 'primary',
        'category'    => 'required',
        'description' => '',
        'chdate'      => 'unix-timestamp',
        'chuserid'    => 'userid',
    );
    protected static $ORDER = '`category` ASC';
    protected static $ID_COLUMN = 'category_id';
    protected static $CONNECTIONS = array();

    public function __toString()
    {
        return ''.@$this['category'];
    }

// Get all entries
    public static function Get($category_id, $combined = false)
    {
        $records = Lists::Filter(compact('category_id'));
        $entries = Entry::Filter(array('glossar_id' => array_pluck($records, 'glossar_id')));
        
        if ($combined) {
            $temp = array();
            foreach ($entries as $entry) {
                $letter = str_replace(array('Ä','Ö','Ü'), array('A', 'O', 'U'), $entry['term'][0]);
                $letter = strtoupper($letter);
                
                if (!isset($temp[$letter])) {
                    $temp[$letter] = array();
                }
                $temp[$letter][] = $entry;
            }
            $entries = $temp;
        }
        
        return $entries;
    }

// Automatically load entries for category
    public $entries = array();

    public function delete(&$id = null)
    {
        if ($success = parent::delete($id)) {
            DBManager::get()
                ->prepare("DELETE FROM glossar_lists WHERE context = ? AND category_id = ?")
                ->execute(array(self::$context, $id));
        }
        return $success;
    }

    public function populate($data)
    {
        if (!empty($data['category_id'])) {
            $temp = Lists::Filter(array('category_id' => $data['category_id']));
            $this->entries = array_pluck($temp, 'glossar_id');
        }
        return parent::populate($data);
    }

    public function get_letters()
    {
        $statement = DBManager::get()
            ->prepare("SELECT DISTINCT SUBSTR(UPPER(`term`), 1, 1)"
                     ." FROM `glossar`"
                     ." JOIN `glossar_lists` USING (`glossar_id`, `context`)"
                     ." WHERE `category_id` = ? AND `context` = ?");
        $statement->execute(array($this['id'], self::$context));
        $letters = $statement->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($letters as $index => $letter) {
            foreach (array('Ä'=>'A', 'Ö'=>'O', 'Ü'=>'U') as $u => $l) {
                if ($letter === $u) {
                    unset($letters[$index]);
                    $letters[] = $l;
                }
            }
        }
        
        sort($letters);
        return array_unique($letters);
    }

    protected static function LoadFromDB($ids)
    {
        Lists::Filter(array('category_id' => $ids));
        return parent::LoadFromDB($ids);
    }

    public function assign_entries($entries)
    {
        $db = DBManager::get();
        
        if ($this['id']) {
            $db->prepare("DELETE FROM glossar_lists WHERE context = ? AND category_id = ? AND glossar_id NOT IN (?)")
                ->execute(array(self::$context, $this['id'], $entries ?: null));
        }
        
        $statement = $db->prepare("INSERT INTO glossar_lists (context, glossar_id, category_id, chdate, chuserid) VALUES (?, ?, ?, UNIX_TIMESTAMP(), ?) ON DUPLICATE KEY UPDATE chdate = VALUES(chdate), chuserid = VALUES(chuserid)");
        foreach ($entries as $id) {
            $statement->execute(array(self::$context, $id, $this['id'], $GLOBALS['user']->id));
        }
        $this->entries = $entries;
    }

}