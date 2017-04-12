<?php

/**
 *
 * create at 16/09/18
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class User extends MY_Controller{

    public function index(){
        $data['head_title'] = '用户信息';
        //$this->try_login(LOGIN_TYPE_REGISTED);
        //$user_info = $this->session->userdata(KEY_USER_INFO);

        //$data['user_info'] = $user_info;

        $this->render('index', $data);
    }

    public function session_test(){
        session_start();
        session_id('hellojammy');

        var_dump($_SESSION);
    }

    /**
     * 账号绑定
     */
    public function bind(){
        $data['head_title'] = '账户信息绑定';
        $this->try_login(LOGIN_TYPE_SOCIAL_INFO_ONLY);
        //看看是否已经登录了,假如登录了,则不能到注册页面
        $user_info = $this->session->userdata(KEY_USER_INFO);
        //已经登录
        if($user_info){
            $url = $this->request_url();
            //假如是从绑定页面跳过来的,则回首页,不然会无限循环
            if(strpos($url, 'user/bind') !== FALSE){
                log_message('debug', 'request url:' . $this->request_url() . ',has logined,redirect to app_root_path');
                redirect($this->config->item('app_path'));
            }else{
                redirect($url);
            }
        }

        $this->try_login(LOGIN_TYPE_SOCIAL_INFO_ONLY);
        //信息提交
        if(isset($_POST['mobile_phone'])){
            $mobile_phone = $this->input->post('mobile_phone');
            //手机号唯一性验证，手机短信验证码验证，逻辑省略
            //.....

            $social_info = $this->session->userdata(KEY_SOCIAL_USER_INFO);
            $this->load->model('M_user');
            $user_data = array(
                'nick_name'    => $social_info['nickname'],
                'mobile_phone' => $mobile_phone,
                'avatar_url'   => $social_info['avatar_url']
            );
            $ret = $this->M_user->save_entry($user_data);

            if($ret > 0){
                $user_data['id'] = $ret;
                //添加绑定关系，user_id和social_id这两个参数是关键
                $bind_data = array(
                    'user_id'   => intval($ret),
                    'social_id' => $social_info['social_id'],
                    'union_id'  => $social_info['union_id'],
                    'bind_type' => $social_info['bind_type']
                );
                $this->load->model('M_social_binder');
                $ret = $this->M_social_binder->save_entry($bind_data);
                if($ret > 0){
                    $url = $this->config->item('app_path') . 'user';
                    //登录
                    $this->_login($user_data);
                    header("Location: $url");
                    exit;
                }else{
                    die('账号信息绑定失败');
                }
            }else{
                die('账号信息绑定失败');
            }

        }else{
            //正常授权登录
            $this->try_login(LOGIN_TYPE_SOCIAL_INFO_ONLY);
        }

        $this->render('bind', $data);
    }
}