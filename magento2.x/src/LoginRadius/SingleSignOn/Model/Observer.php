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
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $singleSignOnHelper = $this->_objectManager->get('LoginRadius\SingleSignOn\Model\Helper\Data');
        if ($singleSignOnHelper->enableSinglesignon() == '1') {
            $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
            $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
            $ssoRootUrl = parse_url($urlInterface->getUrl(''));
            $ssoRootUrl['path'] = isset($ssoRootUrl['path']) ? trim(trim($ssoRootUrl['path'], "/")) : '';
            $ssoTempDir = explode("/", $ssoRootUrl['path']);
            $ssoPath = isset($ssoTempDir[0]) ? '/' . trim($ssoTempDir[0]) . '/' : '';
            ?>
            <html>
                <head>
                    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
                    <script src_type="url" src="//auth.lrcontent.com/v2/js/LoginRadiusV2.js"></script>
                    <script>
                        require(['jquery'], function ($) {
                            jQuery(document).ready(function () {
                                var commonOptions = {};
                                commonOptions.apiKey = '<?= $activationHelper->siteApiKey(); ?>';
                                commonOptions.appName = '<?= $activationHelper->siteName(); ?>';
                                commonOptions.appPath = '<?= $ssoPath; ?>';
                                var LRObject = new LoginRadiusV2(commonOptions);
                                var logout_options = {};
                                logout_options.onSuccess = function () {
                                    window.location = "<?= html_entity_encode($this->getUrl('customer/account/logout')); ?>";
                                    // On Success
                                    //Write your custom code here
                                };
                                LRObject.init("logout", logout_options);
                            });
                        });
                    </script></head><body>Loading...</body></html>
            <?php
            if ($this->_objectManager->get("LoginRadius" . "\\" . $activationHelper->getAuthDirectory() . "\Model\Helper\Data")->debug() == '1') {
                $e = $observer->getEvent()->getException();
                $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                $this->_messageManager->addError($errorDescription);
            }
        }
        return;
    }

}
