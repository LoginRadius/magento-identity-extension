<?php

require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . "AccountController.php";

class Loginradius_Hostedregistration_AccountController extends Mage_Customer_AccountController {

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch() {

        $action = $this->getRequest()->getActionName();
        $this->dataObject = Mage::helper('hostedregistration/Data');

        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }


        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    public function loginAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getLoginUrl());
        } else {
            parent::loginAction();
        }
    }

    public function createAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getLoginUrl());
        } else {
            parent::createAction();
        }
    }

    public function forgotPasswordAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getForgotPasswordUrl());
        } else {
            parent::forgotPasswordAction();
        }
    }

    public function editAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getProfileUrl());
        } else {
            parent::editAction();
        }
    }

    public function logoutAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $this->_redirectUrl($this->dataObject->getLogoutUrl());
        } else {
            parent::logoutAction();
        }
    }

    public function logoutSuccessAction() {
        if (Mage::getBlockSingleton('hostedregistration/hostedregistration')->enableHostedPage() == '1') {
            $islogout = $this->getRequest()->getParam('islogout');
            if ($islogout == 'true') {
                parent::logoutAction();
            }
            parent::logoutSuccessAction();
        } else {
            parent::logoutSuccessAction();
        }
    }

}
