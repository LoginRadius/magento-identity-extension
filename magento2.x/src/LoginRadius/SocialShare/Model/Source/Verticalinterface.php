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

class Verticalinterface implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => '<div class="lr-vertical-interface-32"></div>'], 
            ['value' => 1, 'label' => '<div class="lr-vertical-interface-16"></div>'], 
            ['value' => 2, 'label' => '<div class="lr-vertical-vertical-count-interface"></div>'], 
            ['value' => 3, 'label' => '<div class="lr-vertical-horizontal-count-interface"></div>']];
    }        

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => '<div class="lr-vertical-interface-32"></div>',
            1 => '<div class="lr-vertical-interface-16"></div>', 
            2 => '<div class="lr-vertical-vertical-count-interface"></div>', 
            3 => '<div class="lr-vertical-horizontal-count-interface"></div>'];
    }
}
