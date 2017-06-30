<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\SingleSignOn\Model;

use Magento\Framework\Event\ObserverInterface;

class Ssooptions implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $singleSignOnHelper = $this->_objectManager->get('LoginRadius\SingleSignOn\Model\Helper\Data');
        if ($singleSignOnHelper->enableSinglesignon() == '1') {
            $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
            $ssoRootUrl = parse_url($urlInterface->getUrl(''));
            $ssoRootUrl['path'] = isset($ssoRootUrl['path']) ? trim(trim($ssoRootUrl['path'], "/")) : '';
            $ssoTempDir = explode("/", $ssoRootUrl['path']);
            $ssoPath = isset($ssoTempDir[0]) ? '/' . trim($ssoTempDir[0]) . '/' : '';?>
            <?= 'commonOptions.appPath = '.$ssoPath.';';?>
            <?php
        }
        return;
    }

}
