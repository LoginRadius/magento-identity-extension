<?php

namespace LoginRadius\CustomerRegistration\Controller\Auth;

use LoginRadiusSDK\Utility\Functions;
use \LoginRadiusSDK\LoginRadiusException;
use Magento\Framework\Event\ObserverInterface;

class CustomHttpClient implements \LoginRadiusSDK\Clients\IHttpClient {

    protected $_helperCustomerRegistration;

    public function request($path, $query_array = array(), $options = array()) {
        $parse_url = parse_url($path);
        $request_url = '';
        if (!isset($parse_url['scheme']) || empty($parse_url['scheme'])) {
            $request_url .= API_DOMAIN;
        }
        $request_url .= $path;

        if ($query_array !== false) {
            if (isset($options['authentication']) && $options['authentication'] == 'headsecure') {
                $options = array_merge($options, Functions::authentication(array(), $options['authentication']));
                $query_array = isset($options['authentication']) ? $query_array : $query_array;
            }
            else {
                $query_array = isset($options['authentication']) ? Functions::authentication($query_array, $options['authentication']) : $query_array;
            }
            if (strpos($request_url, "?") === false) {
                $request_url .= "?";
            }
            else {
                $request_url .= "&";
            }
            $request_url .= Functions::queryBuild($query_array);
        }


        if (in_array('curl', get_loaded_extensions())) {
            $response = $this->curlApiMethod($request_url, $options);
        }
        elseif (ini_get('allow_url_fopen')) {
            $response = $this->fsockopenApiMethod($request_url, $options);
        }
        else {
            throw new LoginRadiusException('cURL or FSOCKOPEN is not enabled, enable cURL or FSOCKOPEN to get response from LoginRadius API.');
        }

        $this->_helperActivation = \Magento\Framework\App\ObjectManager::getInstance()->get('LoginRadius\CustomerRegistration\Model\Helper\Data');
        if ($this->_helperActivation->debug() == '1') {
            $response_type = 'error';
            if (isset($response['status']) && $response['status'] == '200') {
                $response_type = 'success';
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $changelogName = $resource->getTableName('lr_api_log'); //gives table name with prefix
            $requestedData = array('get' => $query_array,
              'post' => (isset($options['post_data']) ? $options['post_data'] : ''));

            $data = ['api_url' => $request_url, 'requested_type' => (isset($options['method']) ? $options['method'] : 'GET'), 'data' => json_encode($requestedData), 'response' => (isset($response['response']) ? json_encode($response['response']) : ''), 'response_type' => $response_type, 'created_date' => date('m/d/Y h:i:s a', time())];
            $connection->insert($changelogName, $data);
        }

        if (!empty($response['response'])) {
            $result = json_decode($response['response']);
            if (isset($result->errorCode) && !empty($result->errorCode)) {
                throw new LoginRadiusException($result->message, $result);
            }
        }
        return $response['response'];
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
        $method = isset($options['method']) ? strtoupper($options['method']) : 'GET';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $content_type = isset($options['content_type']) ? trim($options['content_type']) : 'x-www-form-urlencoded';
        $sott_header_content = isset($options['X-LoginRadius-Sott']) ? trim($options['X-LoginRadius-Sott']) : '';
        $apikey_header_content = isset($options['X-LoginRadius-ApiKey']) ? trim($options['X-LoginRadius-ApiKey']) : '';
        $secret_header_content = isset($options['X-LoginRadius-ApiSecret']) ? trim($options['X-LoginRadius-ApiSecret']) : '';

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $request_url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 50);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Content-type: application/' . $content_type, 'X-LoginRadius-Sott:' . $sott_header_content, 'X-LoginRadius-ApiKey:' . $apikey_header_content, 'X-LoginRadius-ApiSecret:' . $secret_header_content));
        if (isset($options['proxy']) && $options['proxy']['host'] != '' && $options['proxy']['port'] != '') {
            curl_setopt($curl_handle, CURLOPT_PROXY, 'http://' . $options['proxy']['user'] . ':' . $options['proxy']['password'] . '@' . $options['proxy']['host'] . ':' . $options['proxy']['port']);
        }
        if (!empty($data) || $data === true) {
            if (($content_type == 'json') && (is_array($data) || is_object($data))) {
                $data = json_encode($data);
            }

            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, (($content_type == 'json') ? $data : Functions::queryBuild($data)));

            if ($method == 'POST') {
                curl_setopt($curl_handle, CURLOPT_POST, 1);
            }
            elseif ($method == 'DELETE') {
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "DELETE");
            }
            elseif ($method == 'PUT') {
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "PUT");
            }
        }
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        $json_response = curl_exec($curl_handle);
        $http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
        if (curl_error($curl_handle)) {
            $json_response = curl_error($curl_handle);
        }

        curl_close($curl_handle);
        $api_response = array(
          'response' => $json_response,
          'status' => $http_code,
        );
        return $api_response;
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
        $method = isset($options['method']) ? strtoupper($options['method']) : 'GET';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $content_type = isset($options['content_type']) ? $options['content_type'] : 'form_params';
        $sott_header_content = isset($options['X-LoginRadius-Sott']) ? trim($options['X-LoginRadius-Sott']) : '';
        $apikey_header_content = isset($options['X-LoginRadius-ApiKey']) ? trim($options['X-LoginRadius-ApiKey']) : '';
        $secret_header_content = isset($options['X-LoginRadius-ApiSecret']) ? trim($options['X-LoginRadius-ApiSecret']) : '';

        $optionsArray = array('http' =>
          array(
            'method' => $method,
            'timeout' => 50,
            'ignore_errors' => true,
            'header' => 'Content-Type: application/' . $content_type
          ),
          "ssl" => array(
            "verify_peer" => $ssl_verify
          )
        );
        if (!empty($data) || $data === true) {
            if (($content_type == 'json') && (is_array($data) || is_object($data))) {
                $data = json_encode($data);
            }
            $optionsArray['http']['header'] .= "\r\n" . 'Content-Length:' . (($data === true) ? '0' : strlen($data));
            $optionsArray['http']['content'] = (($content_type == 'json') ? $data : Functions::queryBuild($data));
        }
        if ($sott_header_content != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'X-LoginRadius-Sott: ' . $sott_header_content;
        }
        if ($apikey_header_content != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'X-LoginRadius-ApiKey: ' . $apikey_header_content;
        }
        if ($secret_header_content != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'X-LoginRadius-ApiSecret: ' . $secret_header_content;
        }

        $context = stream_context_create($optionsArray);
        $json_response = file_get_contents($request_url, false, $context);
        if (!$json_response) {
            throw new LoginRadiusException('file_get_contents error');
        }

        $api_response = array(
          'response' => $json_response,
          'status' => '',
        );
        return $api_response;
    }
}
