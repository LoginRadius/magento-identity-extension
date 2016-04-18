<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 *  sociallogin sharingverticalalignment source model
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_Model_Source_VerticalSharing which returns vertical alignment options
 */
class Loginradius_Socialshare_Model_Source_Verticalalignment
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => 'top_left', 'label' => 'Top Left');
        $result[] = array('value' => 'top_right', 'label' => 'Top Right');
        $result[] = array('value' => 'bottom_left', 'label' => 'Bottom Left');
        $result[] = array('value' => 'bottom_right', 'label' => 'Bottom Right');
        return $result;
    }
}