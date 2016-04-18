<?php

/**
 * Created by PhpStorm.
 * User: nyaconcept
 * Date: 10/18/14
 * Time: 4:00 PM
 */
class Loginradius_Customerregistration_Block_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $var = '<script type="text/javascript">function loginradiusChangeRaasStatus(fieldId, url, event) {
            new Ajax.Request(url, {
        method: "POST",
        parameters: {lr_entity_id: fieldId},
        onSuccess: function (response) {
                document.getElementById("lr_block_unblock"+fieldId).textContent=response.responseText;
                return false;
            }
    });
    return false;}
    </script>';

        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'customerregistration/';
        $connection = Mage::getSingleton('core/resource');
        $readConnection = $connection->getConnection('core_read');
        $tableName = $connection->getTableName('lr_sociallogin');
        $query = "select status from $tableName where entity_id= '" . $row->getId() . "'";
        $result = $readConnection->query($query)->fetch();
        $statusText = 'Block';
        if (is_array($result) && in_array('blocked', $result)) {
            $statusText = 'Unblock';
        } else if (isset($result['status']) && $result['status'] == 'blocked') {
            $statusText = 'Unblock';
        }
        return '<a href="#"><span id="lr_block_unblock' . $row->getId() . '" onclick="loginradiusChangeRaasStatus(' . $row->getId() . ', &quot;' . $url . '&quot;);">' . $statusText . '</span></a>' . $var;
    }

}
