<div class="page__hd">
    <h1 class="page__title">
       微信支付演示
    </h1>
    <p class="page__desc">
        商品名称: 测试商品
    </p>
    <p class="page__desc">
        商品价格: ￥0.01
    </p>
    <br>
    <a href="javascript:;" id="pay" class="weui-btn weui-btn_primary">微信支付</a>
</div>

<script type="text/javascript">

    /**
     * 绑定微信支付事件
     */
    $('#pay').bind('click', function () {
        callWechatPay();
    });

    /**
     * 调用微信JS api 支付
     */
    function wechatJsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo isset($jsApiParameters) ? $jsApiParameters : "''"; ?>,
            function(res){
                if(res.err_msg == "get_brand_wcpay_request:ok"){
                    //微信支付成功
                    alert('微信支付成功');
                }else if(res.err_msg == "get_brand_wcpay_request:cancel"){
                    //用户取消支付
                    alert('用户取消支付');
                }else if(res.err_msg == "get_brand_wcpay_request:fail"){
                    //微信支付失败
                    alert('微信支付失败');
                }
            }
        );
    }

    /**
     * 微信支付
     */
    function callWechatPay()
    {
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', wechatJsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', wechatJsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', wechatJsApiCall);
            }
        }else{
            wechatJsApiCall();
        }
    }
</script>
