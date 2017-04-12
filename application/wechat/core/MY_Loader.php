<?php
class MY_Loader extends CI_Loader{

    /**
     * List of loaded sercices
     *
     * @var array
     * @access protected
     */
    protected $_ci_services = array();
    /**
     * List of paths to load sercices from
     *
     * @var array
     * @access protected
     */
    protected $_ci_service_paths		= array();

    /**
     * Constructor
     *
     * Set the path to the Service files
     */
    public function __construct()
    {

        parent::__construct();
        load_class('Service','core');
        $this->_ci_service_paths = array(APPPATH);
    }

    /**
     * Service Loader
     *
     * This function lets users load and instantiate classes.
     * It is designed to be called from a user's app controllers.
     *
     * @param	string	the name of the class
     * @param	mixed	the optional parameters
     * @param	string	an optional object name
     * @return	void
     */
    public function service($service = '', $params = NULL, $object_name = NULL)
    {
        if(is_array($service))
        {
            foreach($service as $class)
            {
                $this->service($class, $params);
            }

            return;
        }

        if($service == '' or isset($this->_ci_services[$service])) {
            return FALSE;
        }

        if(! is_null($params) && ! is_array($params)) {
            $params = NULL;
        }

        $subdir = '';

        // Is the service in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($service, '/')) !== FALSE)
        {
            // The path is in front of the last slash
            $subdir = substr($service, 0, $last_slash + 1);

            // And the service name behind it
            $service = substr($service, $last_slash + 1);
        }

        foreach($this->_ci_service_paths as $path)
        {

            $filepath = $path .'service/'.$subdir.$service.'.php';
            log_message('debug', 'service path:' . $filepath);

            if ( ! file_exists($filepath))
            {
                continue;
            }

            include_once($filepath);

            $service = strtolower($service);

            if (empty($object_name))
            {
                $object_name = $service;
            }

            $service = ucfirst($service);
            $CI = &get_instance();
            if($params !== NULL)
            {
                $CI->$object_name = new $service($params);
            }
            else
            {
                $CI->$object_name = new $service();
            }

            $this->_ci_services[] = $object_name;

            return;
        }
    }
}