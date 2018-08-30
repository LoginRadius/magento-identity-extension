<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\Apilog\Observer;

use Magento\Framework\Event\ObserverInterface;

class Apilog implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
     
       
        
       if(isset($_REQUEST['clearApi']) && !empty($_REQUEST['clearApi'])){
            $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\App\ResourceConnection');
        $connection = $this->_resources->getConnection();
        $changelogName = $this->_resources->getTableName('lr_api_log');
        $select = $connection->delete($changelogName);
       }
       
         return;
    }

}
