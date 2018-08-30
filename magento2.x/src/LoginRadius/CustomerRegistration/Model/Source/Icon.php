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

class Icon implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => '0', 'label' => __('Small')], 
            ['value' => '1', 'label' => __('Large')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['0' => __('Small'), '1' => __('Large')];
    }
}
