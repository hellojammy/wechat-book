

### 配置文件修改

1、修改数据库配置：
把以下代码中的`hostname`、`password`、`database`改为你自己的配置，通样，其他环境下的database文件也相应修改。

```php
application/wechat/config/production/database.php
```

2、微信公众号配置：
把以下代码中的`token`、`appid`、`appsecret` 改为你自己的配置，通样，其他环境下的wechat文件也相应修改。

```php
application/wechat/config/production/wechat.php
```

3、缓存配置：
假如使用文件缓存，忽略该配置

把以下代码中的`host`、`password` 改为你自己的配置，通样，其他环境下的redis文件也相应修改。

```php
application/wechat/config/production/redis.php
```

### 微信支付官方SDK代码修改及注意事项

1、wxpay/example/WxPay.JsApiPay.php
头部引用文件改为绝对引用
require_once "../lib/WxPay.Api.php" → require_once dirname(__FILE__) . "/../lib/WxPay.Api.php"

2、微信支付发起的url,要以/结尾
