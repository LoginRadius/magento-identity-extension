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
 *  sociallogin observer model
 *
 * @category    Loginradius
 * @package     Loginradius_Activation
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sociallogin_Model_Observer responsible for LoginRadius api keys verification!
 */
class Loginradius_Logs_Model_Observer extends Mage_Core_Helper_Abstract {

    /**
     * @throws Exception while api keys are not valid!
     */
    public function lr_debug_log_event($observer) {        
        $logData = $observer->getEvent()->getLogdata();
        $this->insertLog('logs_data',$logData);
    }

    private function insertLog($tableName, $data) {
        $socialloginData = Mage::helper('sociallogin/Data');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $lrTable = 'lr_' . $tableName;
        $logTable = $socialloginData->getMazeTable($lrTable);

        $query = "INSERT INTO " . $logTable . " VALUES (null";
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            $query .= ", " . $connection->quote($value);
        }

        $query .= ", now())";
        try {
            $connection->query($query);
        } catch (Exception $e) {
            
            Mage::logException($e);
        }
        $connection->commit();
    }

}
