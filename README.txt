

1. wxpay/example/WxPay.JsApiPay.php

头部引用文件改为绝对引用
require_once "../lib/WxPay.Api.php" → require_once dirname(__FILE__) . "/../lib/WxPay.Api.php"

2.微信支付发起的url,要以/结尾