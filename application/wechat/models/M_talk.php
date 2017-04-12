<?php

/**
 *
 * create at 16/09/15
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class M_talk extends MY_Model{
    private $table_name;
    function __construct(){
        $this->table_name = 'talk';
        parent::__construct($this->table_name);
    }

    /**
     * 获取主题的排行榜
     * @param string $open_id
     * @return mixed
     */
    function get_ranking_stat($open_id = ''){
        // $sql = "SELECT COUNT( * ) AS count, subject ,max(createTime ) as mxt FROM  `wx_talk` where openId = '" . $this->_postObj['from'] . "' GROUP BY subject ORDER BY createTime DESC";
        $this->db->select('count( * ) as count, topic ,max(ctime ) as mxt');
        if(!empty($open_id)){
            $this->db->where('open_id', $open_id);
        }
        $this->db->group_by('topic');
        $this->db->order_by('ctime', 'DESC');
        $query = $this->db->get($this->table_name);
        log_message('DEBUG', "sql=" . $this->db->last_query());
        return $query->result_array();
    }

    /**
     * 获取指定个数指定主题的数据
     * @param $topic
     * @param int $limit
     * @param string $open_id
     * @return mixed
     */
    function get_rand_words($topic, $limit = 5, $open_id = ''){
        $this->db->select('*');
        if(!empty($open_id)){
            $this->db->where('open_id', $open_id);
        }
        $this->db->like('topic', $topic);
        $this->db->limit($limit);
        $this->db->order_by('ctime', 'DESC');
        $query = $this->db->get($this->table_name);
        log_message('DEBUG', "sql=" . $this->db->last_query());
        return $query->result_array();
    }

    /**
     * 根据talk id找到相应到image id
     * @param $msg_id
     * @return mixed
     */
    function get_img_ids_by_msgid($msg_id){
        $this->db->select('id,img_ids');
        $this->db->where('msg_id', $msg_id);
        $query = $this->db->get($this->table_name);
        log_message('DEBUG', "sql=" . $this->db->last_query());
        return $query->row_array();
    }

}