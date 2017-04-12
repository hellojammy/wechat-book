<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * create at 16/11/16
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 *  微信相关工具方法
 */

/**
 * 根据openid和bind_type获取用户信息
 * @param $social_id
 * @param $bind_type
 * @return null
 */
function get_user_info_by_social_id($social_id, $bind_type){
    if($social_id){
        $data = array(
            "social_id" => $social_id,
            "bind_type" => $bind_type,
        );


        $r = CI_MyApi::excute('socialbinder/get_entry_by_social_id', $data, 'POST');
        if(!empty($r)){
            $data = array(
                //假如openid对应了多个有效的用户,只取第一个用户,即最后注册的用户
                "id" => $r[0]["user_id"],
            );
            $user = CI_MyApi::excute('usercenter/get_entry_by_id', $data, 'POST');
            return $user;
        }
    }
    return null;
}


function save_social_binder($data){
    $r = CI_MyApi::excute('socialbinder/insert_entry', $data, 'POST');
    log_message('debug', 'insert social_binder:' . $r . ',data:' . json_encode($data));
    return $r;
}

function get_info_by_social_id($social_id, $bind_type){
    $r = CI_MyApi::excute('socialbinder/get_entry_by_social_id',array('social_id' => $social_id, 'bind_type' => $bind_type), 'POST');
    return $r;
}

function responseMaxLen($allText, $text, $maxLen = 2048){
    $len = strlen($text);
    $total_bytes = strlen($allText);
    $total_bytes += $len;

    return $total_bytes < $maxLen ? false : true;
}

function get_social_user($social_id, $bind_type){
    $r = CI_MyApi::excute('socialbinder/social_user', array('social_id' => $social_id, 'bind_type' => $bind_type), 'POST');
    return $r;
}