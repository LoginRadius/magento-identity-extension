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
 *  sociallogin verticalsharing source model
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_Model_Source_VerticalSharing which return vertical sharing theme options
 */
class Loginradius_Socialshare_Model_Source_verticalinterface {

    /**
     * function return array of vertical themes
     *
     * @return array
     */
    public function toOptionArray() {
        $result = array();
        $result[] = array('value' => '32', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/vertical/32verticlewithbox.png', array('_area' => 'adminhtml')) . '" />');
        $result[] = array('value' => '16', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/vertical/16verticlewithbox.png', array('_area' => 'adminhtml')) . '" />');
        $result[] = array('value' => 'counter_vertical', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/vertical/verticalvertical.png', array('_area' => 'adminhtml')) . '" />');
        $result[] = array('value' => 'counter_horizontal', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/vertical/verticalhorizontal.png', array('_area' => 'adminhtml')) . '" />');

        return $result;
    }

}
