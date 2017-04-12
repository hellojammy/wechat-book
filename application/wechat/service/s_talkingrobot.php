<?php

/**
 *
 * create at 16/09/19
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class S_talkingrobot extends CI_Service{
    public function response($from, $text){
        //83e2d06ed582447baf3ef51242e9c3be cfea5b33b19b5445ec11df92f00308cf
        $this->load->library('myapi');
        $response = MyApi::excute("http://www.tuling123.com/openapi/api?key=83e2d06ed582447baf3ef51242e9c3be&userid={$from}&info=" . urlencode($text), NULL, 'GET');
        log_message('debug', 'data:' . json_encode($response) . ',code:' . $response['code']);
        if(!$response){
            return array(
                'ret' => '10',
                'msg' => '我无法理解你的问题。抱歉...',
            );
        }

        $this->load->library('wechat/wechatools');
        switch($response['code']){
            //文本类数据
            case 100000:
                $tmp = $response['text'];
                break;
            //网址类数据 打开百度
            case 200000:
                $tmp = $response['text'] . "\n" . $response['url'];
                break;
            //菜谱  红烧肉怎么做？
            case 308000:
                $tmp = $response['text'] . "\n\n";

                foreach($response['list'] as $kv){
                    $t = Wechatools::buildHref($kv->name,$kv->detailurl,false);
                    $t .= "(" . $kv->info . ")";
                    $t .= "\n\n";

                    if(!Wechatools::maxLen($tmp, $t)){
                        $tmp .= $t;
                    }else{
                        break;
                    }
                }
                break;

            //列车信息  深圳到成都的火车
            case 305000:
                $tmp = $response['text'] . "\n\n";
                foreach($response['list'] as $kv){
                    $t = $kv->trainnum . "\n";
                    $t .= $kv->start . "(" . $kv->starttime . ")" . " → " . $kv->terminal . "(" . $kv->endtime . ")";
                    $t .= "\n\n";

                    if(!Wechatools::maxLen($tmp, $t)){
                        $tmp .= $t;
                    }else{
                        break;
                    }
                }
                break;
            //航班 明天成都飞深圳的飞机
            case 306000:
                $tmp = $response['text'] . "\n\n";
                foreach($response['list'] as $kv){
                    $t = $kv->starttime . " - " . $kv->endtime . "  " . $kv->flight . "\n\n";
                    if(!Wechatools::maxLen($tmp, $t)){
                        $tmp .= $t;
                    }else{
                        break;
                    }
                }
                break;
            //酒店 深圳南山区附近的酒店
            case 309000:
                $tmp = $response['text'] . "\n\n";
                foreach($response['list'] as $kv){
                    $t = $kv->price . "  " . $kv->satisfaction . "  " . Wechatools::buildHref($kv->name,$kv->icon) . "\n\n";
                    if(!Wechatools::maxLen($tmp, $t)){
                        $tmp .= $t;
                    }else{
                        break;
                    }
                }
                break;
            //商品价格 惠人榨汁机多少钱
            case 311000:
                $tmp = $response['text'] . "\n\n";
                foreach($response['list'] as $kv){
                    $t = $kv->price . "  " . Wechatools::buildHref($kv->name,$kv->detailurl) . "\n\n";
                    if(!Wechatools::maxLen($tmp, $t)){
                        $tmp .= $t;
                    }else{
                        break;
                    }
                }
                break;
            //新闻 最新新闻
            case 302000:
                $tmp = $response['text'] . "\n\n";
                foreach($response['list'] as $kv){
                    $t = Wechatools::buildHref($kv->article,$kv->icon) . "(" . $kv->source . ")" . "\n\n";
                    if(!Wechatools::maxLen($tmp, $t)){
                        $tmp .= $t;
                    }else{
                        break;
                    }
                }
                break;

            case 40001:
                $tmp = "key的长度错误（32位）";
                break;
            case 40002:
                $tmp = "请求内容为空";
                break;
            case 40003:
                $tmp = "key错误或帐号未激活";
                break;
            case 40004:
                $tmp = "当天请求次数已用完";
                break;
            case 40005:
                $tmp = "暂不支持该功能";
                break;
            case 40006:
                $tmp = "服务器升级中";
                break;
            case 40007:
                $tmp = "服务器数据格式异常";
                break;
            case 50000:
                $tmp = "机器人设定的“学用户说话”或者“默认回答”";
                break;
            default:
                $tmp = "我无法理解你的问题。抱歉。";
                break;
        }


        return array(
            'ret' => '0',
            'msg' => $tmp,
        );

    }

}