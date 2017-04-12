<?php

/**
 *
 * create at 16/11/28
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class Auth extends MY_Controller{
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('wechat');
    }

    /**
     * 授权获取用户基本信息,这里可以接入多种授权方式,例如微信,手机QQ...
     */
    public function index(){

        log_message('debug', '[auth] request_url:' . $this->request_url());
        $query_arr = array();
        $query_str = $_SERVER['QUERY_STRING'];
        if(!empty($query_str)){
            parse_str($_SERVER['QUERY_STRING'], $query_arr);
        }

        if(!$this->session->userdata('__return_url')){
            //设置回调地址
            $callback_url = $this->config->item('app_path');
            if(isset($query_arr['__callback'])){
                $callback_url = $query_arr['__callback'];
            }
            $this->session->set_userdata('__return_url', $callback_url);
        }

        $this->load->library('auth/wechatauth', $query_arr);
        $this->wechatauth->auth();

        //跳回原来的页面
        $return_url = $this->session->userdata('__return_url');
        $this->session->unset_userdata('__return_url');
        if($return_url){
            $this->session->unset_userdata('__return_url');
            $redirect_url = $return_url;
            log_message('debug', '[auth], return to : ' . $redirect_url);
        }else{
            $redirect_url = $this->config->item('app_path');
            log_message('debug', 'no return url, return to : ' . $redirect_url);
        }

        header("Location: $redirect_url");
        exit;
    }

    /**
     *微信多域名授权
     */
    public function wechat_auth_multiple(){

        log_message('debug', '[auth_multiple] request_url:' . $this->request_url());
        $query_arr = array();
        $query_str = $_SERVER['QUERY_STRING'];
        if(!empty($query_str)){
            parse_str($_SERVER['QUERY_STRING'], $query_arr);
        }

        //进行微信授权, 只获取授权code
        $query_arr['get_auth_code_only'] = true;
        $this->load->library('auth/wechatauth', $query_arr);
        $this->wechatauth->auth();

        $redirect_query = array();
        parse_str($_SERVER['QUERY_STRING'], $redirect_query);
        $base_redirect_url = $redirect_query['__callback'];
        unset($redirect_query['__callback']);

        $finalRedirectUrl = $base_redirect_url;
        //组装回调链接:原链接+授权code等参数
        if(count($redirect_query) > 0){
            $finalRedirectUrl .= ((strpos($base_redirect_url, '?') === false ? '?' : '&') . http_build_query($redirect_query));
            log_message('debug', '[auth_multiple] return to origin url : ' . $finalRedirectUrl);
        }
        header("Location: $finalRedirectUrl");
        exit;
    }

    public function clear_session(){
        $this->session->sess_destroy();
        //clear package info in redis
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->delete('cache_package_info');
        $this->cache->delete('cache_package_class_info');
        $this->cache->delete('cache_package_city_200');

        echo '注销登录成功';
    }

    public function session(){
        var_dump($_SESSION);
    }

}