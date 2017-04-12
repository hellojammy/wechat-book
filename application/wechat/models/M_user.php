<?php

/**
 *
 * create at 16/09/15
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class M_user extends MY_Model{
    private $table_name;
    function __construct(){
        $this->table_name = 'user';
        parent::__construct($this->table_name);
    }
}