<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;
use \LoginRadiusSDK\CustomerRegistration\Management\AccountAPI;

global $apiClient_class;
$apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';

class EditUser implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $events = $observer->getEvent();
        $customerData = $events->getRequest()->getPostValue();

        $customer = $observer->getEvent()->getCustomer();
        $this->_date = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime');
        $customer->setDob($this->_date->gmDate('m-d-Y', $this->_date->strToTime($customerData['customer']['dob'])));

        if (isset($_POST['customer']['entity_id'])) {
            $editUserData = '{
            "Email":[
               {
                  "Type":"Primary",
                  "Value":"' . $customerData['customer']['email'] . '"
               }
            ],
            "FirstName":"' . $customerData['customer']['firstname'] . '",
            "LastName":"' . $customerData['customer']['lastname'] . '",
            "Birthdate":"' . $customer->getDob() . '"
            }';

            $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $ruleTable = $resource->getTableName('lr_sociallogin');
            $connection = $resource->getConnection();
            $select = $connection->select()->from(['r' => $ruleTable])->where('entity_id=?', $customerData['customer']['entity_id']);
            $output = $connection->fetchAll($select);

            $accountApi = new AccountAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('output_format' => 'json'));

            try {
                $response = $accountApi->update($output[0]['uid'], $editUserData);
            }
            catch (\LoginRadiusSDK\LoginRadiusException $e) {

                $errorDescription = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : '';
                $this->_messageManager->addError($errorDescription);
            }
        }
    }
}
