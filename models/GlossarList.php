<?php
class GlossarList extends Plain_ORM {

    protected static $TABLE = 'glossar_lists';
    protected static $COLUMNS = array(
        'context'     => 'primary',
        'id'          => 'primary',
        'glossar_id'  => 'required',
        'category_id' => 'required',
        'chdate'      => 'unix-timestamp',
        'chuserid'    => 'userid',
    );
    protected static $ID_COLUMN = 'id';
    protected static $CONNECTIONS = array(
        'glossar'  => 'GlossarEntry(glossar_id)',
        'category' => 'GlossarCategory(category_id)',
    );
}
