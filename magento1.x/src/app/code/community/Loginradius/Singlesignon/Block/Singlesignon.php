<?php

class Loginradius_Singlesignon_Block_Singlesignon extends Mage_Core_Block_Template {
    
    public static function enableSinglesignon() {
      
        return Mage::getStoreConfig('singlesignon/ssosettings/enable');
       
    }
}
