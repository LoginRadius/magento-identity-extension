<?php

/**
 * Copyright Â© 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\CustomerRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;
global $apiClient_class;
$apiClient_class = 'LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';
class DeleteUser implements ObserverInterface {

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
        $customerRegistrationHelper = $this->_objectManager->get("LoginRadius" . "\\" . $activationHelper->getAuthDirectory() . "\Model\Helper\Data");
        $customer = $observer->getEvent()->getCustomer();
        $customerId = $customer->getId();  
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        $select = $connection->select()->from(['r' => $changelogName])->where('entity_id=?', $customerId);
        $output = $connection->fetchAll($select);  
           
        /* Delete profile from local db */
        $accountAPI = new \LoginRadiusSDK\CustomerRegistration\Management\AccountAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('output_format' => 'json'));
        try {
            $output[0]['uid'] = isset($output[0]['uid']) ? $output[0]['uid'] : '';
            if (!empty($output[0]['uid'])) {
                $result = $accountAPI->delete($output[0]['uid']);
                $changelogName = $resource->getTableName('lr_sociallogin');
                $connection = $resource->getConnection();
                $where = array("entity_id =" . $customerId);
                $connection->delete($changelogName, $where);
           }
        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
               $errorDescription = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : '';
               $this->_messageManager->addError($errorDescription);
        }
    }
}