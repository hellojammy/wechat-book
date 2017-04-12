<?php

/**
 *
 * create at 16/09/10
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class Anynote extends MY_Controller{

    public function preview_images(){
        $data['head_title'] = '图片预览';
        $t_id = $this->input->get('t_id');
        $open_id = $this->input->get('open_id');

        $this->load->service('s_anynote_img');
        $data['img_data'] = $this->s_anynote_img->get_imgs_by_txtid($t_id, $open_id);

        $this->render('preview_images', $data);
    }
}