<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 *  sociallogin observer model
 *
 * @category    Loginradius
 * @package     Loginradius_Activation
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_Model_Observer responsible for LoginRadius api keys verification!
 */
class Loginradius_Activation_Model_Observer extends Mage_Core_Helper_Abstract
{
    /**
     * @throws Exception while api keys are not valid!
     */
    
    public function validateLoginradiusKeys()
    {
        $post = Mage::app()->getRequest()->getPost();
        if((isset($post['groups']['apisettings']['fields']['apikey']['inherit']) && $post['groups']['apisettings']['fields']['apikey']['inherit'] == '1') && (isset($post['groups']['apisettings']['fields']['apisecret']['inherit']) && $post['groups']['apisettings']['fields']['apisecret']['inherit'] == '1')){
            
        }elseif (isset($post['config_state']['activation_apisettings'])) {/*
            $apiKey = $post['groups']['apisettings']['fields']['apikey']['value'];
            $apiSecret = $post['groups']['apisettings']['fields']['apisecret']['value'];
            $validateUrl = 'https://api.loginradius.com/api/v2/app/validate?apikey=' . rawurlencode($apiKey) . '&apisecret=' . rawurlencode($apiSecret);
            $result = $this->getLoginRadiusKeysValidationStatus($validateUrl);
            if ($result['status']!='Success') {
                if ($result['message'] == 'API_KEY_NOT_FORMATED') {
                    $result['message'] = 'LoginRadius API key is not correct.';
                } elseif ($result['message'] == 'API_SECRET_NOT_FORMATED') {
                    $result['message'] = 'LoginRadius API Secret key is not correct.';
                } elseif ($result['message'] == 'API_KEY_NOT_VALID') {
                    $result['message'] = 'LoginRadius API key is not valid.';
                } elseif ($result['message'] == 'API_SECRET_NOT_VALID') {
                    $result['message'] = 'LoginRadius API Secret key is not valid.';
                }
                throw new Exception($result['message']);
            }*/
        }
    }

    /**
     * function is used to get response form LoginRadius api validation.
     *
     * @param string $url
     *
     * @return array $result
     */
    public function getLoginRadiusKeysValidationStatus($url)
    {
        if(class_exists('Loginradius_Sociallogin_Helper_LoginradiusSDK')){
            $loginradiusObject = Mage::helper('sociallogin/LoginradiusSDK');
            $responce = json_decode($loginradiusObject->loginradius_api_client($url));
            $result['status'] = isset($responce->Status) ? $responce->Status : 'Error';
            $result['message'] = isset($responce->Messages[0]) ? $responce->Messages[0] : 'an error occurred';
        }else{
            $result['status'] = 'Success';
        }
        return $result;
    }
}
