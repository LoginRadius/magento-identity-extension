<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\EnableHostedPage\Observer\Frontend;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class Logout implements ObserverInterface {
 protected $_helperActivation;
 protected $_helperCustomerRegistration;


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_helperCustomerRegistration = \Magento\Framework\App\ObjectManager::getInstance()->get('LoginRadius\CustomerRegistration\Model\Helper\Data');
        if($this->_helperCustomerRegistration->enableHostedPage() =='1'){
             $this->_helperActivation = \Magento\Framework\App\ObjectManager::getInstance()->get('LoginRadius\Activation\Model\Helper\Data');
             $islogout = isset($_REQUEST['islogout']) && !empty($_REQUEST['islogout']) ? trim($_REQUEST['islogout']) : '';
       if(empty($islogout)){
           $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
         $url = $urlInterface->getUrl('customer/account/logout/');
         
           $hostedPageUrl = 'https://' . $this->_helperActivation->siteName().'.hub.loginradius.com/auth.aspx?action=logout&return_url='.$url.'?islogout=true';
            header('Location:' . $hostedPageUrl);
            die;
        }
        }
       
        
    }

}
