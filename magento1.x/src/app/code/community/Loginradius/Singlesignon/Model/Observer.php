<?php

class Loginradius_Singlesignon_Model_Observer {

    public function lr_logout_sso($observer) {
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        if (Mage::getBlockSingleton('singlesignon/singlesignon')->enableSinglesignon() == 1) {
            $getBlockDir = $activationBlockObj->getBlockDir();
            echo '<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>';
            echo Mage::getBlockSingleton($getBlockDir . '/' . $getBlockDir)->getLoginRadiusInterfaceScript();
            echo '<script src="//cdn.loginradius.com/hub/prod/js/LoginRadiusSSO.js"></script>';
            echo '<script>';
            echo 'jQuery(document).ready(function () {';
            echo 'LoginRadiusSSO.init("' . $activationBlockObj->siteName() . '");';
            echo 'LoginRadiusSSO.logout("' . html_entity_decode(Mage::getUrl('customer/account/logout')) . '");';
            echo '});';
            echo '</script>';
            if (Mage::getBlockSingleton($getBlockDir . '/' . $getBlockDir)->debugMode() == 1) {
                $event = $observer->getEvent();
                $e = $event->getException();
                $errorDescription = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : '';
                Mage::getSingleton('core/session')->addNotice($errorDescription);
            }
            die;
        }
    }

}
