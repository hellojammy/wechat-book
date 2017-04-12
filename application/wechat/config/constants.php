<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('MY_GENDER_MALE', 1); //男
define('MY_GENDER_FEMALE', 2);//女
define('MY_GENDER_UNKNOWN',3);//未设置性别
//存储social userinfo 的 session key
define('KEY_SOCIAL_USER_INFO', "_key_social_user_info");
//用户登录信息
define('KEY_USER_INFO', "_user_info");

//授权信息
define('KEY_SOCIAL_ACCESS_TOKEN_INFO', "_key_social_access_token_info");

//客户端referrer,用来重定向回之前的页面
define('APP_REFERRER_URL', "_app_referrer_url");

//短信验证码的前缀
define('RAND_CODE_KEY_PREFIX', '_rand_code_key_prefix_');

//图形验证码的前缀
define('PIC_RAND_CODE_KEY_PREFIX', '_pic_rand_code_key_prefix_');

//上次获取短信验证码时间
define('RAND_CODE_LAST_GET_TIME_KEY_PREFIX', '_rand_code_last_get_time_key_prefix_');

//用户session_id的前缀
define('KEY_USER_SESSION_PREFIX', '_key_user_session_prefix_');

//cookie有效时间
define('COOKIE_CACHE_TIME', 7776000);

//绑定类型
define('SOCIAL_BINDER_TYPE_WECHAT', 1); //微信
define('SOCIAL_BINDER_TYPE_QQ', 2); //QQ

//授权登录类型
define('LOGIN_TYPE_SOCIAL_INFO_ONLY', 1); //只要获取社交账号授权信息
define('LOGIN_TYPE_TRY_LOGIN', 2); //获取社交账号授权信息,并尝试登录
define('LOGIN_TYPE_REGISTED', 3); //获取社交账号授权信息,并尝试登陆,登陆失败跳转至绑定页面


//绑定状态
define('SOCIAL_BINDER_STATUS_NORMAL', 1); //已绑定，有效
define('SOCIAL_BINDER_STATUS_CANCEL', 2);//取消绑定
define('SOCIAL_BINDER_STATUS_DEL', 3);//已删除

//浏览器类型
define('BROSWER_TYPE_WECHAT', 1); //微信浏览器
define('BROSWER_TYPE_QQ', 2); //手Q的webview

//支付渠道
define('PAY_CHANNEL_NOTSET', 1); //未设置
define('PAY_CHANNEL_BEECLOUD', 2); //beecloud集成支付
define('PAY_CHANNEL_WECHAT', 5); //微信支付
define('PAY_CHANNEL_QQ', 6); //手Q的支付

//
//http://www.cnblogs.com/mfryf/p/3598257.html
//回复给微信的消息长度不能超过2048字节
define("WECHAT_MAX_RESPONSE_LEN" , 2048);

//随手记的配置
define('ANYNOTE_SUBJECT_A',  '/^(.*)(#.*#)$/');  // ....#...#
define('ANYNOTE_SUBJECT_B',  '/^(#.*#)(.*)$/');  // #...#...
define('ANYNOTE_RANDWORDS',  '/^(给我|我要)(.*)$/'); //随机返回

//用户上传图片的状态
define('PIC_STATUS_UPLOAD', 1); //刚刚上传
define('PIC_STATUS_DOWNLOAD_OK', 2); //已下载
define('PIC_STATUS_DOWNLOAD_FAIL', 3); //下载失败
define('PIC_STATUS_DEL', 4); //已删除


