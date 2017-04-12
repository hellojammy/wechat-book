<?php

/**
 *
 * create at 16/09/19
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class S_anynote_img extends CI_Service{

    /**
     * 用户上传图片的响应.添加图片到相应的主题
     * @param $url
     * @param $msg_id
     * @param $open_id
     * @return bool|string
     */
    public function img($url, $msg_id, $open_id){
        $this->load->model('M_wx_img');
        $save_data = array(
            'msg_id'  => $msg_id,
            'open_id' => $open_id,
            'url'     => $url
        );
        $ret = $this->M_wx_img->save_entry($save_data);
        if($ret){
            //从session中获取text_msg_id,看看之前是否有主题,有的话则关联到对于的主题
            $txt_msg_id = $_SESSION['key_anynote_text_msgid'];
            if($txt_msg_id){
                log_message('debug', '用户之前发表过主题,msg_id:' . $txt_msg_id);
                $this->load->model('M_talk');
                $txt_data = $this->M_talk->get_img_ids_by_msgid($txt_msg_id);
                if($txt_data){
                    $new_msg_ids = (isset($txt_data['img_ids'])? ($txt_data['img_ids'] . ',' . $msg_id) : ($msg_id));
                    $update_txt_data = array(
                        'id'      => $txt_data['id'],
                        'img_ids' => $new_msg_ids
                    );
                    $ret = $this->M_talk->save_entry($update_txt_data);
                    return ($ret > 0) ? "/:,@-D成功添加图片到主题" : "/::(添加图片到主题失败";
                }else{
                    return "/::(获取原有图片失败";
                }
            }
        }
        return false;
    }

    /**
     *
     * @param $txt_id
     * @param $open_id
     * @return bool
     */
    public function get_imgs_by_txtid($txt_id, $open_id){
        $this->load->model('M_talk');
        //找到对应的talk
        $txt_data = $this->M_talk->get_by_id($txt_id);
        if(empty($txt_data)){
            return false;
        }
        //根据image id找在图片表里到图片信息
        $img_ids = $txt_data['img_ids'];
        $this->load->model('M_wx_img');
        return $this->M_wx_img->get_imgs_by_id($img_ids, $open_id);
    }

    /**
     * 格式化图片文件名  2016_12_09_113002_186.jpg
     * @param $img
     * @return string
     */
    function format_pic_name($img){
        return date("Y_m_d_His", strtotime($img["ctime"])) . "_" . $img["id"] . ".jpg";
    }
}