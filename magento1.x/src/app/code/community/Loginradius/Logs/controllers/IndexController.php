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
 * @package     Loginradius_Logs
 * @author      LoginRadius Team
 */
Mage::app('default');

/**
 * Class Loginradius_Logs_IndexController this is the controller where loginradius login and registration takes place
 */
class Loginradius_Logs_IndexController extends Mage_Adminhtml_Controller_Action {

    /**
     * Default action doe LoginRadius Logs controller
     */
    public function indexAction() {
         if (!$this->getRequest()->isAjax()) {
            $this->_redirectReferer();
            return;
        }
        $params = $this->getRequest()->getParams();
        if(isset($params['lrlogclear']) && $params['lrlogclear'] == 'true' ){
            $this->clearLog('logs_data');
            die('success');
        }
        
    }
    private function clearLog($tableName) {
        $socialloginData = Mage::helper('sociallogin/Data');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $lrTable = 'lr_' . $tableName;
        $logTable = $socialloginData->getMazeTable($lrTable);

        $query = "DELETE FROM " . $logTable;
        try {
            $connection->query($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $connection->commit();
    }

}
