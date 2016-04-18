<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\SingleSignOn\Model;

use Magento\Framework\Event\ObserverInterface;

class Observer implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $singleSignOnHelper = $this->_objectManager->get('LoginRadius\SingleSignOn\Model\Helper\Data');
        if ($singleSignOnHelper->enableSinglesignon() == '1') {
            $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
            echo '<html><head><script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>';
            echo '<script src="//hub.loginradius.com/include/js/LoginRadius.js"></script>';
            echo '<script src="//cdn.loginradius.com/hub/prod/js/LoginRadiusRaaS.js"></script>';
            echo '<script src="//cdn.loginradius.com/hub/prod/js/LoginRadiusSSO.js"></script>';
            echo '<script>';
            echo 'jQuery(document).ready(function () {';
            echo 'LoginRadiusSSO.init("' . $activationHelper->siteName() . '");';
            echo 'LoginRadiusSSO.logout("' . html_entity_decode($this->_objectManager->get('\Magento\Customer\Model\Url')->getLogoutUrl()) . '");';
            echo '});';
            echo '</script></head><body>Loading...</body></html>';
            if ($this->_objectManager->get("LoginRadius" . "\\" . $activationHelper->getAuthDirectory() . "\Model\Helper\Data")->debug() == '1') {
                $e = $observer->getEvent()->getException();
                $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                $this->_messageManager->addError($errorDescription);
            }
        }
        return;
    }

}
