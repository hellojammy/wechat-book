<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/../third_party/wechat/wechat.class.php');

class Wechat extends WechatApi {
    protected $_CI;
    public function __construct() {
        $this->_CI =& get_instance();
        $this->_CI->config->load('wechat');
        $options = $this->_CI->config->item('wechat');
        $this->_CI->load->driver('cache', array('adapter' => 'redis'));

        parent::__construct($options);
    }

    /**
     * 重载设置缓存
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename, $value, $expired) {
        if($this->_CI){
            return $this->_CI->cache->save($cachename, $value, $expired);
        }
        return false;
    }

    /**
     * 重载获取缓存
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename) {
        if($this->_CI){
            return $this->_CI->cache->get($cachename);
        }
        return false;
    }

    /**
     * 重载清除缓存
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename) {
        if($this->_CI){
            return $this->_CI->cache->delete($cachename);
        }
        return false;
    }
}

/* End of file CI_Wechat.php */
/* Location: ./application/libraries/Wechat.php */
