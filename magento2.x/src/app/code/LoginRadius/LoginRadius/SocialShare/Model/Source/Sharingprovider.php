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

class Sharingprovider implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'facebook', 'label' => 'Facebook'],
            ['value' => 'googleplus', 'label' => 'GooglePlus'],
            ['value' => 'linkedin', 'label' => 'LinkedIn'],
            ['value' => 'twitter', 'label' => 'Twitter'],
            ['value' => 'pinterest', 'label' => 'Pinterest'],
            ['value' => 'email', 'label' => 'Email'],
            ['value' => 'google', 'label' => 'Google'],
            ['value' => 'digg', 'label' => 'Digg'],
            ['value' => 'reddit', 'label' => 'Reddit'],
            ['value' => 'vkontakte', 'label' => 'Vkontakte'],
            ['value' => 'tumblr', 'label' => 'Tumblr'],
            ['value' => 'myspace', 'label' => 'MySpace'],
            ['value' => 'delicious', 'label' => 'Delicious'],
            ['value' => 'print', 'label' => 'Print']];        
    }        

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['facebook' => 'Facebook',
            'googleplus' => 'GooglePlus',
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter',
            'pinterest' => 'Pinterest',
            'email' => 'Email',
            'google' => 'Google',
            'digg' => 'Digg',
            'reddit' => 'Reddit',
            'vkontakte' => 'Vkontakte',
            'tumblr' => 'Tumblr',
            'myspace' => 'MySpace',
            'delicious' => 'Delicious',
            'print' => 'Print'];
    }
}
