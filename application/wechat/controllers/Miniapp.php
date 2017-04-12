<?php

/**
 *
 * create at 16/11/28
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class Miniapp extends MY_Controller{
    private $miniapp_config;
    function __construct() {
        parent::__construct();
        $this->config->load('wechat');
        $this->miniapp_config = $this->config->item('mini_app');
    }

    public function pay(){
        //加载文件
        require_once APPPATH . 'third_party/wxpay/lib/WxPay.Api.php';
        require_once APPPATH . 'third_party/wxpay/WxPay.JsApiPay.php';
        $this->load->driver('cache', array('adapter' => 'redis'));
        $session_id = $this->input->get('s_id');
        $openId = "oHy7q0HCzcrsEGUlCyipWxaaRVis";//$this->cache->get($session_id);
        $tools = new JsApiPay();
        //五个字段参与签名(区分大小写)：appId,nonceStr,package,signType,timeStamp
        $input = new WxPayUnifiedOrder();
        $input->SetAppid($this->miniapp_config['appid']);
        $input->SetBody("测试商品");
        $input->SetAttach("hello_attach_data");
        $input->SetOut_trade_no(date("YmdHis", time()) . (microtime(true) * 10000));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url($this->config->item('app_path') . 'wxpay/notify');
        $input->SetTrade_type("JSAPI");  //小程序支付类型为JSAPI
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        log_message('debug', 'miniapp pay, data:' . json_encode($input) . ',order:' . json_encode($order) . ',values:' . json_encode($input->GetValues()));
        $jsApiParameters = $tools->GetJsApiParameters($order);
        //{"appId":"wxa5838e226cb3951c","nonceStr":"j2rau95cm6hhzn8gevcue7ruc6alnmhd","package":"prepay_id=wx201701142312055ec008ab3e0785303722","signType":"MD5","timeStamp":"1484406725","paySign":"ED86563644A26A4B7A419884EBC21079"}
        log_message('debug', 'miniapp pay, jsApiParameters:' . $jsApiParameters);
        $pay_data = json_decode($jsApiParameters, true);
        echo json_encode(array(
            'timeStamp' => $pay_data['timeStamp'],
            'nonceStr' => $pay_data['nonceStr'],
            'package' => $pay_data['package'],
            'paySign' => $pay_data['paySign']
        ));
    }

    public function auth(){
        log_message('debug', 'post data:' . json_encode($_POST));
        log_message('debug', 'get data:' . json_encode($_GET));

        require_once APPPATH . 'third_party/miniapp/wxBizDataCrypt.php';

        $appid = $this->miniapp_config['appid'];
        $iv = $this->input->get('iv');
        $code = $this->input->get('authCode');
        $encryptData = $this->input->get('encryptData');

        $session_data = $this->get_session_key($code);
        $pc = new WXBizDataCrypt($appid, $session_data['session_key']);
        $errCode = $pc->decryptData($encryptData, $iv, $data);
        //"{\"openId\":\"oHy7q0HCzcrsEGUlCyipWxaaRVis\",\"nickName\":\"hellojammy\",\"gender\":1,\"language\":\"zh_CN\",\"city\":\"Shenzhen\",\"province\":\"Guangdong\",\"country\":\"CN\",\"avatarUrl\":\"http:\/\/wx.qlogo.cn\/mmopen\/vi_32\/x6mCY2pdjTduu8T9EgcUA0UK1owa47NspvibWmscTdBFYib41S95WEcuKl5tn9NsVYe2lIica3EkylYvibYxxfuNuA\/0\",\"unionId\":\"o5PSqv0c9ldAWqInTHr3hcCZDCQk\",\"watermark\":{\"timestamp\":1484205866,\"appid\":\"wxa5838e226cb3951c\"}}"
        log_message('debug', 'get miniapp user data:' . json_encode($data) . ',openid:' . $session_data['openid']);
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->save(md5($session_data['openid']), $session_data['openid'], intval($session_data['expires_in']) - 3600);

        echo md5($session_data['openid']);
    }

    public function get_session_key($code){
        //https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
        //{"session_key":"X6Tu1CJ1nxk6PfpI+prUAw==","expires_in":2592000,"openid":"oHy7q0HCzcrsEGUlCyipWxaaRVis"}
        $appid = $this->miniapp_config['appid'];
        $appsecret = $this->miniapp_config['appsecret'];
        $this->load->library('myapi');
        $data = MyApi::excute("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appsecret}&js_code={$code}&grant_type=authorization_code", null, 'GET');
        log_message('debug', 'get miniapp session data:' . json_encode($data));
        return $data;
    }
}