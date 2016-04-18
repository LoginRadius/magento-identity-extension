<?php

class Loginradius_Customerregistration_Helper_RaasSDK {

    function __construct() {
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $this->raas_domain = 'https://api.loginradius.com';
        $this->apiKey = $activationBlockObj->apiKey();
        $this->secret = $activationBlockObj->apiSecret();
    }

    function raas_create_user($params) {
        $url = $this->raas_domain . "/raas/v1/user?appkey=" . $this->apiKey . "&appsecret=" . $this->secret;
        return $this->raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded');
    }

    function raas_get_response_from_raas($validateUrl, $post = '', $contentType = 'application/x-www-form-urlencoded') {
        if (self::getApiMethod()) {
            $response = self::curlApiMethod($validateUrl, $post, $contentType);
        } else {
            $response = self::fsockopenApiMethod($validateUrl, $post, $contentType);
        }

        return $response;
    }

    function raas_update_user($params, $uid) {
        $url = $this->raas_domain . "/raas/v1/user?appkey=" . $this->apiKey . "&appsecret=" . $this->secret . "&userid=" . $uid;

        return $this->raas_get_response_from_raas($url, json_encode($params), 'application/json');
    }

    function raas_block_user($params, $uid) {
        $url = $this->raas_domain . "/raas/v1/user/status?appkey=" . $this->apiKey . "&appsecret=" . $this->secret . "&uid=" . $uid;

        return $this->raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded');
    }

    function raas_update_password($params, $uid) {
        $url = $this->raas_domain . "/raas/v1/account/password?appkey=" . $this->apiKey . "&appsecret=" . $this->secret . "&accountid=" . $uid;
        return $this->raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded');
    }

    function raas_create_user_with_email_verification($params) {
        $url = $this->raas_domain . "/raas/v1/user/register?appkey=" . $this->apiKey . "&appsecret=" . $this->secret;

        return $this->raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded');
    }

    function raas_set_password($params) {
        $url = $this->raas_domain . "/raas/v1/account/profile?appkey=" . $this->apiKey . "&appsecret=" . $this->secret;

        return $this->raas_get_response_from_raas($url, $params);
    }

    function raas_admin_set_password($params, $uid) {
        $url = $this->raas_domain . "/raas/v1/user/password?appkey=" . $this->apiKey . "&appsecret=" . $this->secret . "&userid=" . $uid . "&action=set";

        return $this->raas_get_response_from_raas($url, $params);
    }

    function raas_admin_delete_user($uid) {
        $url = $this->raas_domain . "/raas/v1/user/delete?appkey=" . $this->apiKey . "&appsecret=" . $this->secret . "&uid=" . $uid;

        return $this->raas_get_response_from_raas($url);
    }

    function raas_link_account($uid, $provider, $providerid) {
        $url = $this->raas_domain . "/raas/v1/account/link?appkey=" . $this->apiKey . "&appsecret=" . $this->secret;
        $params = http_build_query(array('accountid' => $uid, 'provider' => $provider, 'providerid' => $providerid));

        return $this->raas_get_response_from_raas($url, $params);
    }

    function raas_unlink_account($uid, $provider, $providerid) {
        $url = $this->raas_domain . "/raas/v1/account/unlink?appkey=" . $this->apiKey . "&appsecret=" . $this->secret;
        $params = http_build_query(array('accountid' => $uid, 'provider' => $provider, 'providerid' => $providerid));

        return $this->raas_get_response_from_raas($url, $params);
    }

    function raas_getlink_account($uid) {
        $url = $this->raas_domain . "/raas/v1/account?appkey=" . $this->apiKey . "&appsecret=" . $this->secret . "&accountid=" . $uid;

        return $this->raas_get_response_from_raas($url);
    }
    function raas_getlink_account_by_email($email) {
        $url = $this->raas_domain . "/raas/v1/user?appkey=" . $this->apiKey . "&appsecret=" . $this->secret . "&emailid=" . $email;
        return $this->raas_get_response_from_raas($url);
    }

    /**
     * check server option
     *
     * @return boolean
     */
    function getApiMethod() {
        return function_exists('curl_version');
    }

    /**
     * @param $validateUrl
     *
     * @return mixed|string
     */
    function curlApiMethod($validateUrl, $data, $contentType) {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $validateUrl);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 105);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 500);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        if (!empty($data)) {
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type: ' . $contentType));
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        }

        if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' or ! ini_get('safe_mode'))) {
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        } else {
            curl_setopt($curlHandle, CURLOPT_HEADER, 1);
            $effectiveUrl = curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL);
            curl_close($curlHandle);
            $curlHandle = curl_init();
            $url = str_replace('?', '/?', $effectiveUrl);
            curl_setopt($curlHandle, CURLOPT_URL, $url);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        }

        $jsonResponse = curl_exec($curlHandle);
        curl_close($curlHandle);

        return json_decode($jsonResponse);
    }

    /**
     * @param $validateUrl
     *
     * @return mixed|string
     */
    function fsockopenApiMethod($validateUrl, $data, $contentType) {
        if (!empty($data)) {
            $options = array('http' => array('method' => 'POST', 'timeout' => 15, 'header' => 'Content-type :' . $contentType, 'content' => $data));
            $context = stream_context_create($options);
        } else {
            $context = null;
        }
        $JsonResponse = @file_get_contents($validateUrl, false, $context);

        return json_decode($JsonResponse);
    }

}
