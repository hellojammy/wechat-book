<?php

/**
 *
 * create at 16/1128
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 *  Controller 基类
 */
class MY_Controller extends CI_Controller
{
    protected $layout = 'layout/_main';
    protected $user_id;
    protected $user_name;
    protected $mobile_phone;

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session'); //加载session
        //$this->load->driver('cache', array('adapter' => 'redis'));
        $user_info = $this->session->userdata(KEY_USER_INFO);
        if (!empty($user_info)) {
            //$this->user_id = $user_info["id"];
            $this->user_name = isset($user_info["username"]) ? $user_info["username"] : '';
            $this->mobile_phone = isset($user_info['mobile_phone']) ? $user_info['mobile_phone'] : '';
        } else {
            //$this->user_id = '';
            $this->user_name = '';
            $this->mobile_phone = '';
        }
    }

    /**
     * @param null $file
     * @param array $viewData
     * @param array $layoutData
     * @param bool $is_ajax
     * file 表示是否使用渲染子视图文件，viewData表示的是子视图中渲染数据，$layout表示父视图中使用的全局数据
     */
    protected function render($file = NULL, &$viewData = array(), $layoutData = array(),$is_ajax=false) {
        $data['deploy_ver'] = $this->config->item('deploy_ver');
        $data['app_path'] = $this->config->item('app_path');
        $data['client_ip'] = $this->input->ip_address();
        $pageData = array_merge($viewData, $data);
        $data['content'] = $this->load->view(strtolower(get_class($this)) . '/' . $file, $pageData, TRUE);
        $data['layout'] = $layoutData;
        $this->load->view($this->layout, $data);
        $viewData = array();
    }

    /**
     * ajax返回JSON
     */
    function responseJson($result, $msg)
    {
        echo json_encode(array('ret' => $result, 'msg' => $msg));
        exit;
    }

    /**
     * 微信多域名授权
     */
    function check_login_multiple(){
        $social_info = $this->session->userdata(KEY_SOCIAL_USER_INFO);
        if(empty($_GET['code']) && empty($social_info)){
            $wx_params['__callback'] = $this->request_url();
            $redirect_url = $this->config->item('root_path') . 'auth/wechat_auth_multiple?' . http_build_query($wx_params);
            log_message('debug', '##redirect to auth, url:' . $redirect_url);
            header("Location: $redirect_url");
            exit;
        }else if(empty($social_info)){
            $this->load->library('auth/wechatauth');
            $this->wechatauth->auth();
        }
    }

    /**
     * 登录
     * @param int $login_type
     */
    function try_login($login_type = LOGIN_TYPE_SOCIAL_INFO_ONLY)
    {
        $user_info = $this->session->userdata(KEY_USER_INFO);
        $social_info = $this->session->userdata(KEY_SOCIAL_USER_INFO);
        $need_try_login = $this->need_try_login($login_type);
        if ((empty($social_info) && empty($user_info)) || (empty($user_info) && $need_try_login)){
            if(empty($social_info)){
                //自动授权, 这里会进行多次页面跳转
                $this->auth_redirect();
            }else if($need_try_login){
                log_message('debug','[mobile]get social info ok:' . json_encode($social_info));
                $this->load->service('s_social_binder');
                $user_info = $this->s_social_binder->get_userinfo_by_social_id($social_info["social_id"], $social_info['bind_type']);
                if(empty($user_info) && ($social_info['bind_type'] === SOCIAL_BINDER_TYPE_WECHAT)){
                    $user_info = $this->s_social_binder->get_userinfo_by_union_id($social_info["union_id"]);
                    log_message('debug','####[mobile]try binder_union_id login, get_user_info:' . json_encode($user_info));
                    //添加绑定关系,下次就能以social_id去登录了
                    if(!empty($user_info)){
                        $this->load->model('M_social_binder');
                        $data = array(
                            "user_id" => $user_info["id"],
                            "phone" => $user_info["mobile_phone"],
                            "social_id" => $user_info["social_id"],
                            "union_id" => $user_info["union_id"],
                            "bind_type" => $user_info["bind_type"]

                        );
                        $this->M_social_binder->save_entry($data);
                    }

                }
                log_message('debug','####[mobile]try binder_social_id login, get_user_info:' . json_encode($user_info));
                //设置session,即完成登录过程.包括注册用户/游客身份
                if($user_info){
                    $this->_login($user_info);
                }
            }
        }

        //登陆完成之后,再看是否需要强制跳转到登陆页面
        if(($login_type === LOGIN_TYPE_REGISTED) && !$this->is_register()){
            $register_url = $this->config->item('app_path') . 'user/bind';
            log_message('debug', '####[mobile]login with guest, but require phone user login. redirect to register page:' . $register_url);
            $this->session->set_userdata(APP_REFERRER_URL, $this->request_url());
            redirect($register_url);
            exit;
        }
    }

    function try_login_miniapp(){

    }

    function check_login()
    {
        $social_info = $this->session->userdata(KEY_SOCIAL_USER_INFO);
        if (empty($social_info)){
            //自动授权, 这里会进行多次页面跳转
            $this->auth_redirect();
        }
    }

    /**
     * 根据登录模式,判断是否需要尝试登录.可以以游客身份登录
     * @param int $login_type
     * @return bool
     */
    public function need_try_login($login_type = LOGIN_TYPE_SOCIAL_INFO_ONLY){
        if(in_array($login_type, [LOGIN_TYPE_SOCIAL_INFO_ONLY, LOGIN_TYPE_REGISTED])){
            return true;
        }

        return false;
    }

    /**
     * 真正的登录,会设置session信息
     * @param $user_info
     * @return null
     */
    public function _login($user_info){
        if(empty($user_info)){
            log_message('debug', 'login fail, user info empty');
            return null;
        }

        log_message('debug', 'login_ok_' . $user_info['id'] . ',user_info:' . json_encode($user_info));
        $this->session->set_userdata(KEY_USER_INFO, $user_info);

        return $user_info;
    }

    /**
     * 新增一个游客身份,没有phone,状态为4
     * @param $base_info
     * @return mixed
     */
    private function create_guest($base_info){
        $social_id = $base_info['social_id'];
        log_message('debug', '####try guest login, fail!! create guest, social_id:' . $social_id);
        $un = $base_info['nickname'];
        if(strpos($un, '@') === 0){
            log_message('debug', 'username startwith @:' . $un);
            $un = ' ' . $un;
        }
        $guest['mobile_phone'] = ''; //有些地方会用到,设置一个空值
        $guest['email'] = ''; //有些地方会用到,设置一个空值
        $guest['social_id'] = $social_id;
        $guest['username'] = $un;
        $guest['gender'] = $base_info['gender'];
        $guest['country'] = $base_info['country'];
        $guest['province'] = $base_info['province'];
        $guest['city'] = $base_info['city'];
        $guest['year'] = $base_info['year'];
        $guest['avatar_url'] = $base_info['avatar_url'];
        $guest['company_id'] = 0;
        $guest['status'] = 4;  //游客身份,没有手机号
        $guest['reg_type'] = $base_info['bind_type'];

        return $guest;
    }

    /**
     * 判断当前用户是否为注册用户
     * 假如不是注册用户,则为游客身份
     * @return bool
     */
    public function is_register(){
        $user_info = $this->session->userdata(KEY_USER_INFO);
        if(!empty($user_info) && ($user_info['status'] == 1)){
            return true;
        }

        if(!empty($user_info)){
            $status = $user_info['status'];
            //普通注册用户
            if(($status == 1) && !empty($user_info['mobile_phone'])){
                return true;
            }
            //游客
            if($status == 4){
                return false;
            }
        }else{
            return false;
        }

        return true;
    }

    /**
     * 微信的基本授权登录,只获取openid
     */
    public function wechat_auth_base(){
        $this->auth_redirect(array('auth_type' => 'snsapi_base'));
    }

    /**
     * 跳转到统一登录页面进行登录
     * @param array $wx_params
     */
    public function auth_redirect($wx_params = array()){
        $social_info = $this->session->userdata(KEY_SOCIAL_USER_INFO);
        //测试环境
        if(!$this->config->item('is_wx') && empty($social_info)){
            $authorData = array(
                'social_id' => $this->config->item('test_social_id'),
                'bind_type' => SOCIAL_BINDER_TYPE_WECHAT,
            );
            $this->session->set_userdata(KEY_SOCIAL_USER_INFO, $authorData);
            return;
        }

        if(empty($social_info)){
            $wx_params['__callback'] = $this->request_url();
            $redirect_url = $this->config->item('root_path') . 'auth?' . http_build_query($wx_params);
            log_message('debug', '##redirect to auth, url:' . $redirect_url);
            header("Location: $redirect_url");
            exit;
        }
    }

    /**
     * 获取用户浏览器类别
     * 微信
     * 手Q
     * 手Q6.5
     * 其他
     * @return int
     */
    function user_browser_type(){
        $user_agent_str = $_SERVER['HTTP_USER_AGENT'];

        $type = 0;

        //微信
        if(strpos($user_agent_str, 'MicroMessenger') !== false){
            $type = BROSWER_TYPE_WECHAT;
        }else if(strpos($user_agent_str, 'QQ/') !== false){
            //手Q
            $type = BROSWER_TYPE_QQ;
            if(strpos($user_agent_str, 'QQ/6.5') !== false){
                //手Q6.5版本,可以静默授权
                $type = BROSWER_TYPE_QQ_GH65;
            }
        }else if(strpos($user_agent_str, 'MQQBrowser')){
            //手机QQ浏览器
            $type = BROSWER_TYPE_MQQBROWSER;
        }

        log_message('debug', 'browser_type:' . $type . '. request : ' . $this->request_url() . ',user agent:' . $_SERVER['HTTP_USER_AGENT']);

        return $type;
    }

    /**
     * 是否为微信浏览器
     * @return bool
     */
    function is_wechat_browser(){
        $extra_str = '';
        $user_info = $this->session->userdata(KEY_USER_INFO);
        if($user_info){
            $extra_str .= ', user_id:' . $user_info['id'];
            $extra_str .= ',mobile:' . $user_info['mobile_phone'];
            $extra_str .= ',company_id:' . $user_info['company_id'];
        }

        $is_wechat = false;
        if ($this->user_browser_type() === BROSWER_TYPE_WECHAT) {
            $is_wechat = true;
        }

        log_message('debug', 'wechat_browser:' . ($is_wechat ? 'true' : 'false') . '. request : ' . $this->request_url() . $extra_str . ',user agent:' . $_SERVER['HTTP_USER_AGENT']);

        return $is_wechat;
    }

    /**
     * 获得jssdk需要的签名
     * @return string
     */
    function get_sign_package($url = '')
    {
        //本地开发环境,直接返回空吧
        if(!$this->config->item('is_wx')){
            return '';
        }
        //初始化jsSDK参数
        $this->config->load('wechat');
        $this->load->library('wechat');
        $signPackage = json_encode($this->wechat->getJsSign(
            $url,
            time(),
            $this->wechat->generateNonceStr(),
            $this->config->item('wechat')['appid'])
        );
        return $signPackage;
    }

    function get_wechat_config()
    {
        return $this->config->item('wechat');
    }

    /**
     * 获取当前页面url
     * @return string
     */
    function request_url()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

}
