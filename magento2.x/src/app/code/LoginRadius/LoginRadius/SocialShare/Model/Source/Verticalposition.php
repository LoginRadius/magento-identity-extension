<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */

namespace LoginRadius\SocialShare\Model\Source;

class Verticalposition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => 'Top Left'],
            ['value' => 1, 'label' => 'Top Right'],
            ['value' => 2, 'label' => 'Bottom Left'],
            ['value' => 3, 'label' => 'Bottom Right']];        
    }        

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => 'Top Left',
            1 => 'Top Right',
            2 => 'Bottom Left',
            3 => 'Bottom Right'];
    }
}
