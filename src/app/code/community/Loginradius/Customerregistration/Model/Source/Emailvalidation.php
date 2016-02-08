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
 *  sociallogin horizontalsharing source model
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Customerregistration_Model_Source_Emailvalidation which return horizontal sharing theme options
 */
class Loginradius_Customerregistration_Model_Source_Emailvalidation
{
    /**
     * function return array of Email validation option
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => '0', 'label' => 'Required');
        $result[] = array('value' => '1', 'label' => 'Disable');
        $result[] = array('value' => '2', 'label' => 'Optional');
        return $result;
    }
}