<?php

namespace LoginRadius\CustomerRegistration\Controller\Accounts;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

 global $apiClient_class;
$apiClient_class = '\LoginRadius\CustomerRegistration\Controller\Auth\Customhttpclient';
class Linking extends \Magento\Framework\App\Action\Action {

    protected $_resultPageFactory;
    protected $_customerSession;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Load the page defined in view/frontend/layout/samplenewpage_index_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() {
        ///////////////////////////////////RAAS Linking Interface////////////////////////////////////////
        /** @var \Magento\Framework\App\ObjectManager $om */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\Http\Context $context */
        $context = $objectManager->get('Magento\Framework\App\Http\Context');
        /** @var bool $isLoggedIn */
        $isLoggedIn = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');

        $redirectPage = 'customer/account';
        if ($isLoggedIn) {
            $providerid = isset($_REQUEST['providerid']) && !empty($_REQUEST['providerid']) ? trim($_REQUEST['providerid']) : '';
            $provider = isset($_REQUEST['provider']) && !empty($_REQUEST['provider']) ? trim($_REQUEST['provider']) : '';
            $redirectPage = '';
            if (!empty($providerid) && !empty($provider)) {
                $this->_helperActivation = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
                $customerRegistrationHelper = $this->_objectManager->get('LoginRadius\CustomerRegistration\Model\Helper\Data');
                if ($customerRegistrationHelper->enableRaas() == '1') {
                    $accountAPI = new \LoginRadiusSDK\CustomerRegistration\AccountAPI($this->_helperActivation->siteApiKey(), $this->_helperActivation->siteApiSecret(), array('authentication' => true, 'output_format' => 'json'));
                    try {
                        $accountUnlink = $accountAPI->accountUnlink($customerSession->getLoginRadiusUid(), $providerid, $provider);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
                    }
                }else{
                    $linkedAccounts = $customerRegistrationHelper->selectSocialLinkingData($customerSession->getId());
                    if (is_array($linkedAccounts) && count($linkedAccounts) > 0) {
                        foreach ($linkedAccounts as $linkedAccount) {
                            if(($linkedAccount['sociallogin_id'] == $providerid) && ($linkedAccount['provider'] == $provider)){
                                $accountUnlink = new \stdClass();
                                $accountUnlink->isPosted = true;
                            }
                        }
                    }                    
                }
                $this->_messageManager = $this->_objectManager->get('Magento\Framework\Message\ManagerInterface');
                if (isset($accountUnlink) && $accountUnlink->isPosted == true) {
                    $this->removeSocialLinkingData($providerid);
                    $customerSession->setLoginRadiusStatus('Success');
                    $customerSession->setLoginRadiusMessage('Your Account has been Removed successfully.');
                } else {                    
                    $customerSession->setLoginRadiusStatus('Error');
                    $customerSession->setLoginRadiusMessage('You can not remove this account.');
                }
                $redirectPage = 'customerregistration/accounts/linking';
            }
            if (empty($redirectPage)) {
                $resultPage = $this->_resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set('');
                
                $block = $resultPage->getLayout()->getBlock('accountlinking');
                if ($block) {
                    $block->setRefererUrl($this->_redirect->getRefererUrl());
                }
            }
        }
        if (!empty($redirectPage)) {
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultPage->setPath($redirectPage);
        }
        return $resultPage;
    }

    private function removeSocialLinkingData($socialId) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_sociallogin');
        $connection = $resource->getConnection();
        $connection->delete($changelogName, array('sociallogin_id="' . $socialId . '"'));
    }
    
    

}
