<?php

/**
 *
 * create at 16/09/19
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class S_social_binder extends CI_Service{

    public function __construct() {
    }

    /**
     * 根据social_id找到用户信息
     * @param $social_id
     * @param int $bind_type
     * @return bool
     */
    public function get_userinfo_by_social_id($social_id, $bind_type = SOCIAL_BINDER_TYPE_WECHAT){
        if($social_id){
            $this->load->model('M_social_binder');
            $social_data = $this->M_social_binder->get_by_social_id($social_id, $bind_type);
            if(!empty($social_data)){
                $this->load->model('M_user');
                $user_data = $this->M_user->get_by_id($social_data['user_id']);
                return $user_data;
            }
        }
        return false;
    }

    /**
     * 根据union_id找到用户信息
     * @param $union_id
     * @return bool
     */
    public function get_userinfo_by_union_id($union_id){
        if($union_id){
            $this->load->model('M_social_binder');
            $social_data = $this->M_social_binder->get_by_union_id($union_id);
            if(!empty($social_data)){
                $this->load->model('M_user');
                $user_data = $this->M_user->get_by_id($social_data['user_id']);
                return $user_data;
            }
        }
        return false;
    }
}