<?php
require_once  'Restclient.php';
class MyApi {
    public static function excute($uri, $data=null, $verb='POST'){
        $configs =get_config();
        if(strtoupper($verb)=='GET'&&$data){
            if(is_array($data)){
                $tmp = array();
                foreach($data as $key=>$val){
                    $tmp[] = $key.'='.urlencode($val);
                }
                $uri = $uri.(strstr($uri,'?')?'&':'?').implode('&',$tmp);
            }else{
                $uri = $uri.(strstr($uri,'?')?'&':'?').$data;
            }
            $data = null;
        }
        //$url_pre = preg_match('/^https?:/',$uri)?'':$configs['api_path'];
        $url_pre = '';
        log_message('debug','request api_uri:'.$verb.',' . $url_pre.$uri . ',data:' . json_encode($data));
        $client = new RESTClient($url_pre.$uri, $verb, $data);
        $client->execute();
        $body = $client->getResponseBody();
        $code = $client->getResponseCode();
        $r = json_decode( $body ,true);
        
        if($r===null&&$body&&$body!=='null'||preg_match('/^(4|5)/',$code)){
            $log = 'error request api_uri:'. $url_pre.$uri . ',code:'.$code.',response:' . $body;
            log_message('debug',$log,'api_error');
            if(ENVIRONMENT==='development'){
                echo $log;
                exit;
            }else{

            }
        }
        return $r;
    }
}
