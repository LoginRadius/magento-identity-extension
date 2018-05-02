<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */

namespace LoginRadius\CustomerRegistration\Model\Source;
use LoginRadiusSDK\Utility\Functions;

class Welcomesms implements \Magento\Framework\Option\ArrayInterface
{
    
    protected $_objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_objectManager = $objectManager;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
       
        if ($activationHelper->siteApiKey() != '' && $activationHelper->siteApiSecret() != '') {
            try {
                $url = "https://cloud-api.loginradius.com/configuration/ciam/appInfo/templates?apikey=" . $activationHelper->siteApiKey() . "&apiSecret=" . $activationHelper->siteApiSecret();
                $templates = json_decode(Functions::apiClient($url));
            }
            catch (LoginRadiusException $e) {
                
            }
        }
            
        $template = array();
        if(!empty($templates->SMSTemplates)){
        foreach ($templates->SMSTemplates->Welcome as $name) {
            $template[$name] = $name; 
        }}
        
        if(empty($template)){            
            $template['default'] = 'default';  
        }
        return $template;
    }
}
