<?php

/**
 *
 * create at 16/09/15
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class M_social_binder extends MY_Model{
    private $table_name;
    function __construct(){
        $this->table_name = 'social_binder';
        parent::__construct($this->table_name);
    }

    /**
     * 根据social_id找记录
     * @param $social_id
     * @param $bind_type
     * @return mixed
     */
    function get_by_social_id($social_id, $bind_type){
        $this->db->where('social_id', $social_id);
        $this->db->where('bind_type', intval($bind_type));
        $this->db->order_by('utime','desc');
        $query = $this->db->get($this->table_name);
        log_message('DEBUG', "sql=" . $this->db->last_query());
        return $query->row_array();
    }

    function get_by_union_id($union_id){
        $this->db->where('union_id', $union_id);
        $this->db->order_by('utime','desc');
        $query = $this->db->get($this->table_name);
        log_message('DEBUG', "sql=" . $this->db->last_query());
        return $query->row_array();
    }
}