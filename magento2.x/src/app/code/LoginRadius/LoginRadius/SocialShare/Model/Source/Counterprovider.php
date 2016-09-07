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

class Counterprovider implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'facebook-like', 'label' => 'Facebook Like'],
            ['value' => 'facebook-recommend', 'label' => 'Facebook Recommend'],
            ['value' => 'facebook-send', 'label' => 'Facebook Send'],
            ['value' => 'twitter-tweet', 'label' => 'Twitter Tweet'],
            ['value' => 'pinterest-pin-it', 'label' => 'Pinterest Pin it'],
            ['value' => 'linkedin-share', 'label' => 'LinkedIn Share'],
            ['value' => 'stumbleupon-badge', 'label' => 'StumbleUpon Badge'],
            ['value' => 'reddit', 'label' => 'Reddit'],
            ['value' => 'google-1', 'label' => 'Google+ +1'],
            ['value' => 'google-share', 'label' => 'Google+ Share']];        
    }        
    

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['facebook-like' => 'Facebook Like',
            'facebook-recommend' => 'Facebook Recommend',
            'facebook-send' => 'Facebook Send',
            'twitter-tweet' => 'Twitter Tweet',
            'pinterest-pin-it' => 'Pinterest Pin it',
            'linkedIn-share' => 'LinkedIn Share',
            'stumbleupon-badge' => 'StumbleUpon Badge',
            'reddit' => 'Reddit',
            'google-1' => 'Google+ +1',
            'google-share' => 'Google+ Share'];
    }
}
