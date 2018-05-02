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

    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Load the page defined in view/frontend/layout/samplenewpage_index_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() {

        ///////////////////////////////////CIAM Linking Interface////////////////////////////////////////
        /** @var \Magento\Framework\App\ObjectManager $om */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\Http\Context $context */
        $context = $objectManager->get('Magento\Framework\App\Http\Context');
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $this->_request = $objectManager->get('\Magento\Framework\App\RequestInterface');
        $request = $this->_request->getParams();
        $redirectPage = 'customer/account';
        if ($context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            $providerid = isset($request['providerid']) && !empty($request['providerid']) ? trim($request['providerid']) : '';
            $provider = isset($request['provider']) && !empty($request['provider']) ? trim($request['provider']) : '';
            $redirectPage = '';
 
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


}
