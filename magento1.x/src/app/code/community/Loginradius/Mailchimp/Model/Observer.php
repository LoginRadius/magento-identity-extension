<?php

class Loginradius_Mailchimp_Model_Observer {

    function create_update_profile_data($observer){
        $event = $observer->getEvent();
        $entityid = $event->getEntityid();
        Mage::helper('mailchimp/mailchimp')->createProfileDataatMailchimp($entityid);
        return;
    }

}