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
 *  sociallogin controller
 *
 * @category    Loginradius
 * @package     Loginradius_Customerregistration
 * @author      LoginRadius Team
 */

Mage::app('default');
/**
 * Class Loginradius_Customerregistration_IndexController this is the controller where loginradius login and registration takes place
 */
class Loginradius_Customerregistration_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Default action doe LoginRadius Customerregistration controller
     */
    public function indexAction()
    {
        if (isset($_REQUEST['lr_entity_id']) && !empty($_REQUEST['lr_entity_id'])) {
            $this->updateUserStatus($_REQUEST['lr_entity_id']);
            exit();
        }
    }
    public function verificationAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        
    }

    public function updateUserStatus($entity_id)
    {

        $result = Mage::helper('sociallogin/data')->loginRadiusRead("lr_sociallogin", "get status", array($entity_id), true);
        if (isset($result['status']) && $result['status'] == 'blocked') {
            $params['isblock'] = 'false';
            $displayText = "Block";
            $tableStatus = 'unblocked';
        } else {
            $params['isblock'] = 'true';
            $displayText = 'Unblock';
            $tableStatus = 'blocked';

        }
        require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'SDKClient.php';
        global $apiClient_class;
        $apiClient_class = 'Loginradius_Sociallogin_Helper_SDKClient';
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $accountAPI = new LoginRadiusSDK\CustomerRegistration\AccountAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));
        $accountAPI->setStatus($result['uid'], $params['isblock']);
        Mage::helper('sociallogin/data')->SocialLoginInsert("lr_sociallogin", array('status' => $tableStatus, 'entity_id' => $entity_id),true, '', true);
        die($displayText);

    }
}
