<?php
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'LoginRadius.php';
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'LoginRadiusException.php';
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'Clients' . DS . 'IHttpClient.php';
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'Clients' . DS . 'DefaultHttpClient.php';
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'SocialLogin' . DS . 'GetProvidersAPI.php';
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'SocialLogin' . DS . 'SocialLoginAPI.php';
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'CustomerRegistration' . DS . 'AccountAPI.php';
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'CustomerRegistration' . DS . 'CustomObjectAPI.php';
require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'LoginRadiusSDK' . DS . 'CustomerRegistration' . DS . 'UserAPI.php';
/**
 * Class for Social Authentication.
 *
 * This is the main class to communicate with LoginRadius Unified Social API. It contains functions for Social Authentication with User Profile Data (Basic and Extended)
 *
 * Copyright 2015 LoginRadius Inc. - www.LoginRadius.com
 *
 * This file is part of the LoginRadius SDK package.
 *
 */
class Loginradius_Sociallogin_Helper_SDKClient implements \LoginRadiusSDK\Clients\IHttpClient {
    public function request($path, $query_array = array(), $options = array()) {
        $parse_url = parse_url($path);
        $request_url = '';
        if (!isset($parse_url['scheme']) || empty($parse_url['scheme'])) {
            $request_url .= API_DOMAIN;
        }
        $request_url .= $path;
        if ($query_array !== false) {
            $query_array = (isset($options['authentication']) && ($options['authentication'] == false)) ? $query_array : LoginRadiusSDK\LoginRadius::authentication($query_array);
            if (strpos($request_url, "?") === false) {
                $request_url .= "?";
            } else {
                $request_url .= "&";
            }
            $request_url .= LoginRadiusSDK\LoginRadius::queryBuild($query_array);
        }
        if (in_array('curl', get_loaded_extensions())) {
            $response = $this->curlApiMethod($request_url, $options);
        } elseif (ini_get('allow_url_fopen')) {
            $response = $this->fsockopenApiMethod($request_url, $options);
        } 
        $getBlockDir = Mage::getBlockSingleton('activation/activation')->getBlockDir();
        if (Mage::getBlockSingleton($getBlockDir . '/' . $getBlockDir)->debugMode() == '1') {
            $status = 'error';
            if (!empty($response)) {
                $result = json_decode($response);
                if (!isset($result->errorCode)) {
                    $status = 'success';
                }
            }
            $logData = array(
                'url' => $request_url,
                'method' => strtoupper(isset($options['method']) ? strtolower($options['method']) : 'get'),
                'data' => array(
                    'GET' => $query_array,
                    'POST' => (isset($options['post_data']) ? $options['post_data'] : array())
                ),
                'response' => $response,
                'status' => ucfirst($status)
            );
            Mage::dispatchEvent('lr_debug_log_event', array('logdata' => $logData));
        }
        
        if (!empty($response)) {
            $result = json_decode($response);
            if (isset($result->errorCode) && !empty($result->errorCode)) {
                throw new LoginRadiusSDK\LoginRadiusException($result->message, $result);
            }
        }
        return $response;
    }
    /**
     * Access LoginRadius API server by curl method
     *
     * @param type $request_url
     * @param type $options
     * @return type
     */
    private function curlApiMethod($request_url, $options = array()) {
        $ssl_verify = isset($options['ssl_verify']) ? $options['ssl_verify'] : false;
        $method = isset($options['method']) ? strtolower($options['method']) : 'get';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $content_type = isset($options['content_type']) ? trim($options['content_type']) : 'x-www-form-urlencoded';
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $request_url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 50);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
        if (!empty($data) || $data === true) {
            curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Content-type: application/' . $content_type));
            if ($method == 'post') {
                curl_setopt($curl_handle, CURLOPT_POST, 1);
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, (($content_type == 'json') ? json_encode($data) : LoginRadiusSDK\LoginRadius::queryBuild($data)));
            }
        }
        if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' or ! ini_get('safe_mode'))) {
           
            curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        } else {
           
            //curl_setopt($curl_handle, CURLOPT_HEADER, 1);
            //$effectiveUrl = curl_getinfo($curl_handle, CURLINFO_EFFECTIVE_URL);
            //curl_close($curl_handle);
            //$curl_handle = curl_init();
            $url = str_replace('?', '/?', $request_url);
            
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            
        }
        $json_response = curl_exec($curl_handle);
        
        curl_close($curl_handle);
        return $json_response;
    }
    /**
     * Access LoginRadius API server by fsockopen method
     *
     * @param type $request_url
     * @param type $options
     * @return type
     */
    private function fsockopenApiMethod($request_url, $options = array()) {
        $ssl_verify = isset($options['ssl_verify']) ? $options['ssl_verify'] : false;
        $method = isset($options['method']) ? strtolower($options['method']) : 'get';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $content_type = isset($options['content_type']) ? $options['content_type'] : 'form_params';
        if (!empty($data)) {
            $options = array('http' =>
                array(
                    'method' => strtoupper($method),
                    'timeout' => 50,
                    'header' => 'Content-type :application/' . $content_type,
                    'content' => (($content_type == 'json') ? json_encode($data) : LoginRadiusSDK\LoginRadius::queryBuild($data))
                ),
                "ssl" => array(
                    "verify_peer" => $ssl_verify
                )
            );
            $context = stream_context_create($options);
        } else {
            $context = NULL;
        }
        $json_response = @file_get_contents($request_url, false, $context);
        if (!$json_response) {
            throw new LoginRadiusSDK\LoginRadiusException('file_get_contents error');
        }
        return $json_response;
    }
}
?>