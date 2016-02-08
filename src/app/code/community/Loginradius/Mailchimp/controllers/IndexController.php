<?php

Mage::app('default');

/**
 * Class Loginradius_Sociallogin_IndexController this is the controller where loginradius login and registration takes place
 */
class Loginradius_Mailchimp_IndexController extends Mage_Core_Controller_Front_Action {

    function indexAction() {
        $action = $this->getRequest()->getPost('action');
        if(in_array($action, array('getLists','getFields'))){
            $mailchimp = Mage::helper('mailchimp/mailchimp');
            $functionMailchimp = $action.'Mailchimp';
            echo $mailchimp->$functionMailchimp();
            exit();
        }
    }
}
