<?php

global $apiClient_class;
$apiClient_class = 'Ciam_Authentication_Helper_SDKClient';
require_once Mage::getModuleDir('', 'Ciam_Authentication') . DS . 'Helper' . DS . 'SDKClient.php';

class Ciam_Authentication_Model_Observer extends Mage_Core_Model_Abstract {

    public function admin_customer_save_after($observer) {
        global $LRprofile;

        $this->dataObject = new Ciam_Authentication_Block_Authentication();
        $postData = Mage::app()->getRequest()->getPost();
        if (!isset($postData['customer_id']) && ($postData['account']['website_id'] != 0) && !empty($LRprofile)) {
            $customer_email = $postData['account']['email'];
            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId($postData['account']['website_id']);
            $customer->loadByEmail($customer_email);
            $this->dataObject->linkingData($customer->getId(), $LRprofile);
            return;
        }
    }

    public function admin_customer_save_before($observer) {

        $this->blockObj = new Ciam_Authentication_Block_Authentication();
        $session = Mage::getSingleton('core/session');
        $postData = Mage::app()->getRequest()->getPost();
        $birthdate = isset($postData['dob']) ? date('m-d-Y', strtotime($postData['dob'])) : ''; //dob
        /**
         * Creating user on RAAS
         */
        $accountObject = new LoginRadiusSDK\CustomerRegistration\Management\AccountAPI($this->blockObj->apiKey(), $this->blockObj->apiSecret(), array("output_format" => 'json'));

        if (!isset($postData['customer_id'])) {
            if ($postData['account']['website_id'] != 0) {
                $params = array('email' => array(array('Type' => 'Primary', 'Value' => $postData['account']['email'])), 'firstname' => $postData['account']['firstname'], 'lastname' => $postData['account']['lastname'], 'birthdate' => $birthdate, 'password' => $postData['account']['password']);
                if (isset($postData['account']['gender'])) {
                    if ($postData['account']['gender'] == '1') {
                        $params['gender'] = 'M';
                    } elseif ($postData['account']['gender'] == '0') {
                        $params['gender'] = 'F';
                    }
                }
                try {
                    global $LRprofile;
                    $LRprofile = $accountObject->create(json_encode($params));
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    $session->addError(__($e->getErrorResponse()->Description));
                }
            }
        } else {

            // Updating user profile
            $params = array('firstname' => $postData['account']['firstname'], 'lastname' => $postData['account']['lastname'], 'birthdate' => $birthdate);
            if (isset($postData['account']['gender'])) {
                if ($postData['account']['gender'] == '1') {
                    $params['gender'] = 'M';
                } elseif ($postData['account']['gender'] == '0') {
                    $params['gender'] = 'F';
                }
            }

            $connection = Mage::getSingleton('core/resource');
            $readConnection = $connection->getConnection('core_read');
            $tableName = $connection->getTableName('lr_authentication');
            $query = "select uid from $tableName where entity_id= '" . $postData['customer_id'] . "'";
            $result = $readConnection->query($query)->fetch();
            if (isset($result['uid']) && !empty($result['uid'])) {

                //Code for password changing
                if (isset($postData['account']['new_password']) && !empty($postData['account']['new_password'])) {

                    try {
                        $accountObject->setPassword($result['uid'], $postData['account']['new_password']);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $session->addError(__($e->getErrorResponse()->Description));
                    }
                }
                try {
                    $accountObject->update($result['uid'], json_encode($params));
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    $session->addError(__($e->getErrorResponse()->Description));
                }
            }
        }
    }

    public function delete_before_customer($observer) {
        $session = Mage::getSingleton('core/session');
        $this->blockObj = new Ciam_Authentication_Block_Authentication();
        $accountObject = new LoginRadiusSDK\CustomerRegistration\Management\AccountAPI($this->blockObj->apiKey(), $this->blockObj->apiSecret(), array("output_format" => 'json'));

        $postData = Mage::app()->getRequest()->getPost();

        if (is_array($postData['customer'])) {
            $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');

            foreach ($postData['customer'] as $customerId) {
                $loginRadiusQuery = "select uid from " . Mage::getSingleton('core/resource')->getTableName('lr_authentication') . " where entity_id = '" . $customerId . "' LIMIT 1";
                $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
                $loginRadiusResult = $loginRadiusQueryHandle->fetch();
                if (isset($loginRadiusResult['uid']) && !empty($loginRadiusResult['uid'])) {
                    try {
                        $accountObject->delete($loginRadiusResult['uid']);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $session->addError(__($e->getErrorResponse()->Description));
                    }
                }
            }
        }
    }

    public function get_customer_saved_data($customer) {
        $modified = array();
        $modified['FirstName'] = (string) $customer->firstname; //firstname
        $modified['LastName'] = (string) $customer->lastname; //lastname
        $modified['Email'] = array(array('Type' => 'Primary', 'Value' => (string) $customer->email));
        $modified['Password'] = (string) $customer->password; //password
        $modified['BirthDate'] = date('m-d-Y', strtotime((string) $customer->dob)); //dob
        $modified['Gender'] = (string) $customer->gender; //gender
        if ($modified['Gender'] == '0') {
            $modified['Gender'] = 'F';
        } else {
            $modified['Gender'] = 'M';
        }
        return $modified;
    }

    public function checkout_customer_save_before($observer) {
        //customer basic info
        global $ciamCheckout;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            if (isset($ciamCheckout['status'])) {
                $session = Mage::getSingleton("customer/session");
                $session->setLoginRadiusId($ciamCheckout['data']->ID);
                $session->setLoginRadiusUid($ciamCheckout['data']->Uid);
                $session->setLoginRadiusCheckoutUid($ciamCheckout['data']->Uid);
            }
        }
    }

    public function customer_save_before($observer) {
        global $ciamCheckout;
        $this->blockObj = new Ciam_Authentication_Block_Authentication();
        $accountObject = new LoginRadiusSDK\CustomerRegistration\Management\AccountAPI($this->blockObj->apiKey(), $this->blockObj->apiSecret(), array("output_format" => 'json'));
        $customer = $observer->getCustomer();
        $address = $observer->getCustomerAddress();
        if (!isset($address) || empty($address)) {
            $address = new stdClass;
        }
        $session = Mage::getSingleton('core/session');
        if ((Mage::app()->getFrontController()->getRequest()->getRouteName() == "checkout") && !Mage::getSingleton('customer/session')->isLoggedIn()) {
            $modified = $this->get_customer_saved_data($customer);
            if (isset($modified['Password']) && !empty($modified['Password'])) {
                try {
                    $modified['EmailVerified'] = true; //EmailVerified                        
                    $response = $accountObject->create(json_encode($modified));
                    if (isset($response->Description)) {
                        $session->addError($response->Description);
                    } else {
                        $ciamCheckout['status'] = true;
                        $ciamCheckout['data'] = $response;
                    }
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    $session->addError(__($e->getErrorResponse()->Description));
                }
            }
        }
    }

    /**
     * @throws Exception while api keys are not valid!
     */
    public function validate_apikey_and_secret() {
        $post = Mage::app()->getRequest()->getPost();
        if ((isset($post['groups']['apisettings']['fields']['apikey']['inherit']) && $post['groups']['apisettings']['fields']['apikey']['inherit'] == '1') && (isset($post['groups']['apisettings']['fields']['apisecret']['inherit']) && $post['groups']['apisettings']['fields']['apisecret']['inherit'] == '1')) {
            
        } elseif (isset($post['config_state']['authentication_apisettings'])) {
            $apiKey = $post['groups']['apisettings']['fields']['apikey']['value'];
            $apiSecret = $post['groups']['apisettings']['fields']['apisecret']['value'];
            $validateUrl = 'https://api.loginradius.com/api/v2/app/validate';
            $result = $this->get_keys_validation_status($validateUrl, $apiKey, $apiSecret);
            if ($result['status'] != 'Success') {
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
            }
        }
    }

    /**
     * function is used to get response form LoginRadius api validation.
     *
     * @param string $url
     *
     * @return array $result
     */
    public function get_keys_validation_status($url, $apiKey, $secret) {
        $function = new \LoginRadiusSDK\Utility\Functions($apiKey, $secret, array("output_format" => 'json', "authentication" => "secret"));
        $responce = ($function->apiClient($url));
        $result['status'] = isset($responce->Status) && $responce->Status == true ? 'Success' : 'Error';
        $result['message'] = isset($responce->Messages[0]) ? $responce->Messages[0] : 'an error occurred';

        return $result;
    }

}
