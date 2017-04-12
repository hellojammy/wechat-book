<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * create at 16/09/07
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class Wx_Api extends CI_Controller {
    //微信各事件的回调
    private $wx_callback_hooks = [];
    function __construct()
    {
        parent::__construct();
        //$this->load->library('session'); //加载session
    }

    public function index() {
        //实例化wechat对象
        $this->load->library('wechat');
        //验证回调合法性
        $this->wechat->valid();
        //设置各事件的回调
        $this->wx_callback_hooks = [
            WechatApi::MSGTYPE_TEXT => array($this, 'responseTxt'),
            WechatApi::MSGTYPE_LOCATION => array($this, 'responseLocation'),
            WechatApi::MSGTYPE_IMAGE => array($this, 'responseImage'),
            WechatApi::MSGTYPE_VOICE => array($this, 'responseVoice'),
            WechatApi::EVENT_SUBSCRIBE => array($this, 'responseSubscribe'),
            WechatApi::EVENT_UNSUBSCRIBE => array($this, 'responseUnSubscribe'),
            WechatApi::EVENT_SCAN => array($this, 'responseScan'),
            WechatApi::EVENT_MENU_CLICK => array($this, 'responseClick'),
            WechatApi::EVENT_LOCATION => array($this, 'responseEventLocation'),
            WechatApi::EVENT_MENU_VIEW => array($this, 'responseView')
        ];

        $type = $this->wechat->getRev()->getRevType();
        $event = $this->wechat->getRevEvent();
        if ($type == 'event' && isset($event['event'])) {
            $type = $event['event'];
        }

        //检验回调函数是否存在
        if (
                !isset($this->wx_callback_hooks[$type]) || !is_callable($this->wx_callback_hooks[$type])) {
            log_message('debug','call undefined function, type:' . $type);
            //$this->wechat->text("我们的工程师正在玩命开发中 ^_^ , $type")->reply();
            return;
        }

        //启用session,使用openid的md5值作为session_id
        session_id(md5($this->wechat->getRevFrom()));
        session_start();

        //调用响应函数
        call_user_func($this->wx_callback_hooks[$type]);
    }

    /**
     * 自定义菜单信息推送
     */
    function postMenu(){
        $data = array (
      	    'button' => array (
      	      0 => array (
      	        'name' => '系统功能',
      	        'sub_button' => array (
      	            0 => array (
      	              'type' => 'scancode_waitmsg',
      	              'name' => '扫码带提示',
      	              'key' => 'rselfmenu_0_0',
     	            ),
                    1 => array (
                        'type' => 'pic_sysphoto',
                        'name' => '系统拍照发图',
                        'key' => 'rselfmenu_1_0',
                    ),
                    2=> array (
                        'type' => 'pic_photo_or_album',
                        'name' => '拍照或者相册发图',
                        'key' => 'rselfmenu_1_1',
                    ),
                    3 => array (
                        'type' => 'location_select',
                        'name' => '发送位置',
                        'key' => 'rselfmenu_2_0'
                    ),
      	        ),
      	      ),
                1 => array(
                    'name' => '功能演示',
                    'sub_button' => array (
                        0 => array (
                            'type' => 'view',
                            'name' => '授权获取用户信息',
                            'url' => 'http://wx.hello1010.com/',
                        ),
                        1 => array (
                            'type' => 'view',
                            'name' => '微信支付',
                            'url' => 'http://wx.hello1010.com/wxpay/pay/',
                        ),
                    ),
                ),
      	    ),
      	);

        //实例化wechat对象
        $this->load->library('wechat');
        $this->wechat->createMenu($data);
    }

    /**
     * 文本消息的响应
     */
    function responseTxt() {
        //find /var/lib/php/session -cmin +24 -type f | xargs rm -rvf
        //Anynote的处理逻辑
        $this->load->service('s_anynote_txt');
        $res_msg = $this->s_anynote_txt->topic(
            $this->wechat->getRevContent(),
            $this->wechat->getRevID(),
            $this->wechat->getRevFrom()
        );

        //Anynote没有处理,则交给机器人处理
        if(!$res_msg){
            $this->load->service('s_talkingrobot');
            $ret = $this->s_talkingrobot->response($this->wechat->getRevFrom(), $this->wechat->getRevContent());
            $res_msg = $ret['msg'];
        }

        $this->wechat->text($res_msg)->reply();
    }

    /**
     * 上传图片的响应
     */
    function responseImage() {
        $postObj = $this->wechat->getRevPic();
        $this->load->service('s_anynote_img');
        $res_msg = $this->s_anynote_img->img(
            $postObj['picurl'],
            $this->wechat->getRevID(),
            $this->wechat->getRevFrom()
        );

        if(!$res_msg){
            $this->load->library('wechatools');
            $res_msg = Wechatools::buildHref('点击查看原图', $postObj['picurl']);
        }

        $this->wechat->text($res_msg)->reply();
    }

    /**
     * 上传地理位置的响应
     */
    function responseLocation() {
        $postObj = $this->wechat->getRevGeo();
        $this->wechat->text("您的坐标是: {$postObj['x']} , {$postObj['y']}")->reply();
    }

    /**
     * 语言消息的响应
     */
    function responseVoice() {
        $postObj = $this->wechat->getRevData();
        $txt = $postObj['Recognition'];
        if (!$txt) {
            $txt = '万万没想到，语音识别失败了！';
        }
        $this->wechat->text($txt)->reply();
    }

    /**
     * 用户关注事件响应
     */
    function responseSubscribe() {
        log_message('debug', "用户关注,openid:{$this->wechat->getRevFrom()}");
        //是否是扫描推广二维码过来的
        $scene_info = $this->wechat->getRevSceneId();
        if($scene_info){
            log_message('debug','subscribe_scan_' . $scene_info);

        }else{
            $welcome = "嘿~谢天谢地你终于来了 ^_^  \n我可以在线记录你的碎片想法(文字、图片都支持)。 \n\nBTW：其实我还是一个智能聊天机器人。";
            $welcome .= "您的openid是:{$this->wechat->getRevFrom()}";
            $this->wechat->text($welcome)->reply();
        }
    }

    /**
     * 用户取消关注事件响应
     */
    function responseUnSubscribe() {
        log_message('debug', "用户取消关注,openid:{$this->wechat->getRevFrom()}");
        $this->wechat->text("亲爱的，不要离开我  :(")->reply();
    }

    /**
     * 用户扫描带参数二维码事件响应
     */
    function responseScan() {
        $scene_info = $this->wechat->getRevSceneId();
        if($scene_info){
            log_message('debug','qrcode_scan_' . $scene_info);
        }
    }

    /**
     * 用户点击菜单后的响应
     */
    function responseClick() {
        $postObj = $this->wechat->getRevEvent();
        switch ($postObj['key']) {
            case 'CLICK_A':
                $this->wechat->text("我们的工程师正在玩命开发中，敬请期待！")->reply();
                break;
            default:
                break;
        }
    }

    /**
     *上报地理位置事件的响应
     */
    function responseEventLocation() {
        $postObj = $this->wechat->getRevEventGeo();
        //log_message('debug', "用户上报地理位置:{$postObj['x']} , {$postObj['y']}");
        //$this->wechat->text("用户上报地理位置: {$postObj['x']} , {$postObj['y']}")->reply();
    }

    /**
     * 点击菜单跳转链接时的事件响应
     */
    function responseView() {
        log_message('debug','view:' . $this->wechat->getRevSceneId() . ',openid:' . $this->wechat->getRevFrom());
    }
}
