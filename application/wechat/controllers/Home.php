<?php

/**
 *
 * create at 16/11/25
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class Home extends MY_Controller{

    public function multiple_login(){
        $data['head_title'] = '多域名授权测试';
        $this->check_login_multiple();
        echo $this->request_url();
    }

    public function test_service(){
        $this->load->service('s_test');
        $this->s_test->test('abc');
    }

    public function index(){
        $data['head_title'] = '首页';

        $this->check_login();
        echo $this->request_url();
        $social_info = $this->session->userdata(KEY_SOCIAL_USER_INFO);
        var_dump($social_info);
        //$this->render('index', $data);
    }

    /**
     * 查看文章.文章中的图片通过调用wx.previewImage接口来实现
     */
    public function post(){
        $data['head_title'] = '文章阅读';
        //签名相关参数
        $data['sign'] = $this->get_sign_package();
        $data['share'] = json_encode(
            array
            (	'shareTitle' => 'hellojammy的技术博客',
                'shareDesc' => '这是一个使用JS-SDK的示例文章',
                'shareLink' => $this->request_url(),

            )
        );
        $this->render('post', $data);
    }
}