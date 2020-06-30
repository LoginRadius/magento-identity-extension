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

class RedirectionCheckout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionArray()
    {
            return [['value' => 'oncheckout', 'label' => __('Display login form')], 
                     ['value' => 'onlogin', 'label' => __('Redirect to login page')], 
                    ['value' => 'guestlogin', 'label' => __('Guest Checkout')]];
    }
}

