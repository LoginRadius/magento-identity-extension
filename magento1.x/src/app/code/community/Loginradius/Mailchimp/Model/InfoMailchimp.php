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
 *  sociallogin system config infopremium model
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */
class Loginradius_Mailchimp_Model_InfoMailchimp extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render extension's advanced configuration page html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $loginradiusInfo = new Loginradius_Activation_Model_Info();
        $loginradiusInfo->render($element);
        $this->render_module_admin_script_container();
    }

    public function render_module_admin_script_container()
    {
        ?>
        <script type="text/javascript">
            function lrMailChimpLists() {
                var enableMailchimp = jQuery('#mailchimp_mailchimp_enable').val();
                jQuery('#row_mailchimp_mailchimp_lists,#row_mailchimp_mailchimp_mappingFieldsHeading,#row_mailchimp_mailchimp_mappingFields').hide();
                jQuery('#loading-mask').show();
                jQuery('.getListButtonMessage').html('');
                if(enableMailchimp != '1'){
                    jQuery('.getListButtonMessage').html('Please enable mailchmp first.');
                    jQuery('#loading-mask').hide();
                    return;
                }
                var mailchimpApikey = jQuery('#mailchimp_mailchimp_apikey').val();
                jQuery.post('<?php echo $this->getBaseUrl();?>mailchimp?isAjax=true', {apikey: mailchimpApikey,action:'getLists'}, function (data) {
                    data = JSON.parse(data);
                    if(data.status == true){
                        jQuery('#mailchimp_mailchimp_lists').html(data.html);
                        jQuery('#row_mailchimp_mailchimp_lists').show();
                        jQuery('#mailchimp_mailchimp_lists').attr('onchange','lrMailChimpFields()');
                        var mailchimpList = jQuery('#mailchimp_mailchimp_lists').val();
                        if(mailchimpList != ''){
                            lrMailChimpFields();
                        }else{
                            jQuery('#loading-mask').hide();
                        }
                    }else{
                        jQuery('.getListButtonMessage').html(data.message);
                        jQuery('#loading-mask').hide();
                    }
                });
            }
            function lrMailChimpFields(){
                jQuery('#row_mailchimp_mailchimp_mappingFieldsHeading,#row_mailchimp_mailchimp_mappingFields').hide();
                jQuery('#loading-mask').show();
                jQuery('.getListButtonMessage').html('');
                var mailchimpApikey = jQuery('#mailchimp_mailchimp_apikey').val();
                var mailchimpList = jQuery('#mailchimp_mailchimp_lists').val();
                jQuery.post('<?php echo $this->getBaseUrl();?>mailchimp?isAjax=true', {apikey: mailchimpApikey,list:mailchimpList,action:'getFields'}, function (data) {
                    data = JSON.parse(data);
                    if(data.status == true){
                        jQuery('#row_mailchimp_mailchimp_mappingFields .value').html(data.html);
                        jQuery('#row_mailchimp_mailchimp_mappingFieldsHeading,#row_mailchimp_mailchimp_mappingFields').show();
                    }else{
                        jQuery('.getListButtonMessage').html(data.message);
                    }
                    fillMappingFields();
                    jQuery('#loading-mask').hide();
                });                
            }
        </script>
    <?php
    }
    
}