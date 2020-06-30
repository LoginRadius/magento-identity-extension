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

class DeleteUser implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => '1', 'label' => __('Yes')], 
            ['value' => '0', 'label' => __('No')]];
    }    
}
