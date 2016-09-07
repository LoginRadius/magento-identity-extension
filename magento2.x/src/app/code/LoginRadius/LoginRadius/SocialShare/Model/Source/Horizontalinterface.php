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

class Horizontalinterface implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => '<div class="lr-horizontal-interface-responcive"></div>'], 
            ['value' => 1, 'label' => '<div class="lr-horizontal-interface-32"></div>'], 
            ['value' => 2, 'label' => '<div class="lr-horizontal-interface-16"></div>'], 
            ['value' => 3, 'label' => '<div class="lr-horizontal-image-interface-32"></div>'], 
            ['value' => 4, 'label' => '<div class="lr-horizontal-image-interface-16"></div>'], 
            ['value' => 5, 'label' => '<div class="lr-horizontal-horizontal-count-interface"></div>'], 
            ['value' => 6, 'label' => '<div class="lr-horizontal-vertical-count-interface"></div>']];
    }        

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => '<div class="lr-horizontal-interface-responcive"></div>', 
            1 => '<div class="lr-horizontal-interface-32"></div>', 
            2 => '<div class="lr-horizontal-interface-16"></div>', 
            3 => '<div class="lr-horizontal-image-interface-32"></div>', 
            4 => '<div class="lr-horizontal-image-interface-16"></div>', 
            5 => '<div class="lr-horizontal-horizontal-count-interface"></div>', 
            6 => '<div class="lr-horizontal-vertical-count-interface"></div>'];
    }
}
