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
 * Class Loginradius_Sociallogin_Model_Source_HorizontalSharing which return horizontal sharing theme options
 */
class Loginradius_Socialshare_Model_Source_Horizontalinterface
{
    /**
     * function return array of horizontal themes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => '32', 'label' => '<img style="margin-left: 6px;" src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/horizontal/horizonsharing32.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => '16', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/horizontal/horizonsharing16.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'responsive', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/horizontal/responsiveicons.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'single_large', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/horizontal/singleimagethemelarge.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'single_small', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/horizontal/singleimagethemesmall.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'counter_vertical', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/horizontal/vertical.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'counter_horizontal', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/socialshare/images/horizontal/horizontal.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        return $result;
    }
}