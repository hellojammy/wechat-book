<?php

/**
 *
 * create at 16/11/27
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */

class Wxpay extends MY_Controller{
    /**
     * 微信支付
     */
    public function pay(){
        $data['head_title'] = '微信支付';
        $this->check_login();
        $social_info = $this->session->userdata(KEY_SOCIAL_USER_INFO);

        //加载文件
        require_once APPPATH . 'third_party/wxpay/lib/WxPay.Api.php';
        require_once APPPATH . 'third_party/wxpay/WxPay.JsApiPay.php';
        $tools = new JsApiPay();
        $openId = $social_info['social_id'];
        $input = new WxPayUnifiedOrder();
        $input->SetBody("测试商品");
        $input->SetAttach("hello_attach_data");
        $input->SetOut_trade_no(date("YmdHis", time()) . (microtime(true) * 10000));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url($this->config->item('app_path') . 'wxpay/notify');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        $data['jsApiParameters'] = $jsApiParameters;

        $this->render('index', $data);
    }


    /**
     * 微信支付结果通知
     * 回调地址: wxpay/notify
     */
    public function notify(){
        require_once APPPATH . 'third_party/wxpay/WxPay.Notify.php';
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }
}