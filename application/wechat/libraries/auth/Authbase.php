<?php

/**
 * create at 16/9/10
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */

class Authbase{
    protected $CI;
    function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->helper('url');
    }

    function auth(){

    }
}