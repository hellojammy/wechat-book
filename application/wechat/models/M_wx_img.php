<?php

/**
 *
 * create at 16/09/15
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class M_wx_img extends MY_Model{
    private $table_name;
    function __construct(){
        $this->table_name = 'wx_img';
        parent::__construct($this->table_name);
    }

    /**
     * 根据img_id获取图片信息,只能获取某个人的,这里用open_id限制
     * @param $img_ids
     * @param $open_id
     * @return mixed
     */
    function get_imgs_by_id($img_ids, $open_id){
        $this->db->select("id, msg_id, open_id, url, status, ctime");
        $this->db->where('open_id', $open_id);
        $this->db->where_in('msg_id', explode(',', $img_ids));

        $query = $this->db->get($this->table_name);
        log_message('DEBUG', "sql=" . $this->db->last_query());
        return $query->result_array();
    }

    /**
     * 根据图片状态找图片
     * @param int $status
     * @return mixed
     */
    function get_imgs_by_status($status = PIC_STATUS_UPLOAD){
        $this->db->select('id, msg_id, open_id, url, status, ctime');
        $this->db->where('status', $status);

        $query = $this->db->get($this->table_name);
        log_message('DEBUG', "sql=" . $this->db->last_query());
        return $query->result_array();
    }

    /**
     * 设置图片的状态,比如下载成功,下载失败
     * @param $img_ids
     * @param $status
     * @return bool
     */
    function set_image_status($img_ids, $status){
        $data['status'] = $status;
        $this->db->where_in('id', $img_ids);
        $r = $this->db->update($this->table_name, $data);
        log_message('DEBUG', "sql=" . $this->db->last_query());
        return $r > 0 ? true : false;
    }
}