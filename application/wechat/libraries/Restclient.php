<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class RESTClient {

    protected $url;
    protected $verb;
    protected $requestBody;
    protected $requestLength;
    protected $username;
    protected $password;
    protected $acceptType;
    protected $contentType;
    protected $responseBody;
    protected $responseCode;
    protected $responseInfo;
    protected $sslVerify;

    public function __construct($url = null, $verb = 'GET', $requestBody = null) {
        $this->url = $url;
        $this->verb = $verb;
        $this->requestBody = $requestBody;
        $this->sslVerify = FALSE;
        $this->requestLength = 0;
        $this->username = null;
        $this->password = null;
        $this->acceptType = null;
        $this->contentType = null;
        $this->responseBody = null;
        $this->responseCode = null;
        $this->responseInfo = null;
    }

    public function execute() {
        $handle = curl_init();
        $this->setAuth($handle);
        if ($this->sslVerify) {
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($handle, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        }
        try {
            switch (strtoupper($this->verb)) {
                case 'GET':
                    $this->doGet($handle);
                    break;
                case 'POST':
                    $this->doPost($handle);
                    break;
                case 'PUT':
                    $this->doPut($handle);
                    break;
                case 'DELETE':
                    $this->doDelete($handle);
                    break;
                default:
                    throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
            }
        } catch (InvalidArgumentException$e) {
            curl_close($handle);
            throw $e;
        } catch (Exception$e) {
            curl_close($handle);
            throw $e;
        }
    }

    protected function setAuth(&$handle) {
        if ($this->username !== null && $this->password !== null) {
            curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }
    }

    protected function doGet($handle) {
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Accept: ' . $this->acceptType));
        $this->doExecute($handle);
    }

    protected function doPut($handle) {
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "PUT");
        $content = $this->requestBody;
        $length = strlen($content);

        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type:' . $this->contentType, 'Content-Length', $length, 'Accept: ' . $this->acceptType));
        curl_setopt($handle, CURLOPT_POSTFIELDS, $content);
        $this->doExecute($handle);
    }

    protected function doPost($handle) {
        curl_setopt($handle, CURLOPT_POST, 1);
        $content = $this->requestBody;
        curl_setopt($handle, CURLOPT_POSTFIELDS, $content);
        $this->doExecute($handle);
    }

    protected function doDelete($handle) {
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->doExecute($handle);
    }

    protected function doExecute(&$handle) {
        $this->setCurlOpts($handle);
        $this->responseBody = curl_exec($handle);
        $this->responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $this->responseInfo = curl_getinfo($handle);
        curl_close($handle);
    }

    protected function setCurlOpts(&$handle) {
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        curl_setopt($handle, CURLOPT_URL, $this->url);
        //curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    public function getAcceptType() {
        return $this->acceptType;
    }

    public function setAcceptType($acceptType) {
        $this->acceptType = $acceptType;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getResponseBody() {
        return $this->responseBody;
    }

    public function getResponseCode() {
        return $this->responseCode;
    }

    public function getResponseInfo() {
        return $this->responseInfo;
    }

    public function setRequestBody($requestBody){
        $this->requestBody = $requestBody;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getVerb() {
        return $this->verb;
    }

    public function setVerb($verb) {
        $this->verb = $verb;
    }

    public function setSslVerify($sslVerify) {
        $this->sslVerify = $sslVerify;
    }

    public function __get($name) {
        $method = 'get' . $name;
        return $this->$method();
    }

    public function __set($name, $value) {
        $method = 'set' . $name;
        return $this->$method($value);
    }

}
