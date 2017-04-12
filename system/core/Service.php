<?php

/**
 *
 * create at 16/09/15
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class CI_Service {

    /**
     * Constructor
     * @access public
     */
    function __construct(){
        log_message('debug', "Service Class Initialized");

    }

    /**
     * __get
     *
     * Allows Services to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param	string
     * @access private
     */
    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }
}
// END Service Class

/* End of file Service.php */
/* Location: ./system/core/Service.php */