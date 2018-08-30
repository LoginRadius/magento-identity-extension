<?php

namespace LoginRadius\CustomerRegistration\Controller\Verification;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action {

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
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set('');
        $block = $resultPage->getLayout()->getBlock('verifyemail');
        if ($block) {
                    $block->setRefererUrl($this->_redirect->getRefererUrl());
                }
        
            return $resultPage;
    }

}
