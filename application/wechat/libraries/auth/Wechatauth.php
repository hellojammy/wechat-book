<?php

/**
 * create at 16/11/29
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 * 微信网页授权 授权成功后,session中的 KEY_SOCIAL_USER_INFO 存放授权得到的信息,如openid等信息
 */

require_once  'Authbase.php';
class WechatAuth extends Authbase{
    private $config = array
    (
        'auth_scope' => 'snsapi_userinfo', //授权作用域,默认为获取snsapi_userinfo
        'state'     => '',                //回调附加参数
        'get_auth_code_only'  => false,   //是否只是获取授权code
    );

    function __construct($custom_config = array())
    {
        parent::__construct();
        $this->config = array_merge($this->config, $custom_config);
        $this->CI->config->load('wechat');
        //实例化wechat对象
        $this->CI->load->library('wechat', $this->CI->config->item('wechat'));
    }

    /**
     * 微信OAuth2.0授权过程
     */
    function auth(){
        log_message('debug', '[wechat_auth] from_url:' . $this->request_url());
        //是否已授权
        $auth_data = $this->CI->session->userdata(KEY_SOCIAL_USER_INFO);
        if(!empty($auth_data) && !$this->config['get_auth_code_only']){
            return;
        }

        if(!isset($_GET['code'])){
            $url = $this->CI->wechat->getOauthRedirect($this->request_url(), $this->config['state'], $this->config['auth_scope']);
            redirect($url);
            exit;
        }else if(!$this->config['get_auth_code_only']){
            $base_data = $this->CI->wechat->getOauthAccessToken();
            if('snsapi_base' === $this->config['auth_scope']){
                $auth_data = array(
                    'authorize_time' => time(), //授权时间
                    'social_id' => $base_data['openid'],
                    'access_token' => $base_data['access_token'],
                    'refresh_token' => $base_data['refresh_token'],
                    'bind_type' => SOCIAL_BINDER_TYPE_WECHAT,
                    'expired' => !empty($base_data['expires_in']) ? $base_data['expires_in'] + time() : 0,
                );
            }else if('snsapi_userinfo' === $this->config['auth_scope']){
                $rich_data = $this->CI->wechat->getOauthUserinfo($base_data['access_token'], $base_data['openid']);
                if(!empty($rich_data)){
                    $auth_data = array(
                        'authorize_time' => time(), //授权时间
                        'social_id' => $base_data['openid'],
                        'union_id' => isset($rich_data['unionid']) ? $rich_data['unionid'] : '', //绑定了微信开发平台才会有该值
                        'access_token' => $base_data['access_token'],
                        'refresh_token' => $base_data['refresh_token'],
                        'bind_type' => SOCIAL_BINDER_TYPE_WECHAT,
                        'expired' => !empty($base_data['expires_in']) ? $base_data['expires_in'] + time() : 0,
                        'nickname' => $rich_data['nickname'],
                        'province' => $rich_data['province'],
                        'city' => $rich_data['city'],
                        'country' => $rich_data['country'],
                        'year' => isset($rich_data['year']) ? $rich_data['year'] : 0,
                        'avatar_url' => stripslashes($rich_data['headimgurl']),
                        'gender' => $rich_data['sex']
                    );
                }else{
                    show_error('微信授权失败', 401, '出错了 :( . code:0x688');
                    log_message('error', 'wechat_auth_fail:401');
                    return;
                }
            }else{
                show_error('微信授权失败类型不存在', 402, '出错了 :( . code:0x689. type:' . $this->config['auth_scope']);
                log_message('error', 'wechat_auth_scope_not_exists:402');
                return;
            }

            if(isset($auth_data)){
                $this->CI->session->set_userdata(KEY_SOCIAL_USER_INFO, $auth_data);
                log_message('DEBUG', 'auth data: ' . json_encode($auth_data));
            }
        }
    }

    /**
     * 获取当前页面地址
     * @return string
     */
    private function request_url(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}