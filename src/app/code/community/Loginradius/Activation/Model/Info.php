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
 *  sociallogin system config info model
 *
 * @category    Loginradius
 * @package     Loginradius_Sociallogin
 * @author      LoginRadius Team
 */
class Loginradius_Activation_Model_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        // Get LoginRadius Module Thanks message container..
        $this->render_module_thanks_message_container();

        // Get LoginRadius Module information container..
        $this->render_module_info_container();

        // Get LoginRadius Module Help & Documentations container..
        $this->render_module_help_and_documentations_container();

        // Get LoginRadius Module Support Us container..
        $this->render_module_support_us_container();

        // Get LoginRadius Module Support Us container..
        $this->render_module_admin_script_container();
    }

    /**
     * Get LoginRadius Module Thanks message container..
     */
    public function render_module_thanks_message_container() {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_left" id="lr_thank_message_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Thank you for installing LoginRadius Extension!') ?></strong></h4>

            <p>
                <?php echo $this->__('To activate the extension, you will need to first configure it (manage your desired social networks, etc.) from your LoginRadius account. If you do not have an account, click') ?>
                <a target="_blank" href="http://www.loginradius.com/"><?php echo $this->__('here') ?></a> <?php echo $this->__('and create one for FREE!') ?>
            </p>

            <p>
                <?php echo $this->__('We also offer Social Plugins for') ?>
                <a href="http://ish.re/ADDT" target="_blank">Wordpress</a>,
                <a href="http://ish.re/TRXL" target="_blank">Joomla</a>,
                <a href="http://ish.re/TRXK" target="_blank">Drupal</a>,
                <a href="http://ish.re/TRXM" target="_blank">vBulletin</a>,
                <a href="http://ish.re/TRXR" target="_blank">VanillaForum</a>,
                <a href="http://ish.re/TRXT" target="_blank">osCommerce</a>,
                <a href="http://ish.re/TRXU" target="_blank">PrestaShop</a>,
                <a href="http://ish.re/TRXW" target="_blank">X-Cart</a>,
                <a href="http://ish.re/TRXY" target="_blank">Zen-Cart</a>,
                <a href="http://ish.re/TRY1" target="_blank">DotNetNuke</a>,
                <a href="http://ish.re/TRY2" target="_blank">SMF</a> <?php echo $this->__('and') ?>
                <a href="http://ish.re/TRY3" target="_blank">phpBB</a> !
            </p>
            </br>
            <div style="margin-top:10px">
                <a style="text-decoration:none;margin-right:10px;" href="https://www.loginradius.com/" target="_blank">
                    <input class="form-button" type="button" value="<?php echo $this->__('Set up my account!') ?>">
                </a>
                <a class="loginRadiusHow" target="_blank"
                   href="http://ish.re/4">(<?php echo $this->__('How to set up an account?') ?>)</a>
            </div>
        </fieldset>
        <?php
    }

    /**
     * Get LoginRadius Module information container..
     */
    public function render_module_info_container() {
        $media = array(
            'facebook' => "https://www.facebook.com/loginradius",
            'twitter' => "https://twitter.com/LoginRadius",
            'google' => "https://plus.google.com/+Loginradius",
            'linkedin' => "http://www.linkedin.com/company/loginradius",
            'youtube' => "https://www.youtube.com/user/LoginRadius"
        );
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_right" id="lr_extension_info_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Extension Information!') ?></strong></h4>

            <div style="margin:5px 0">
                <strong>Version: </strong><?php echo Mage::getConfig()->getNode()->modules->Loginradius_Activation->version; ?> <br/>
                <strong>Author:</strong> LoginRadius<br/>
                <strong>Website:</strong> <a href="https://www.loginradius.com" target="_blank">www.loginradius.com</a>
                <br/>
                <div id="sociallogin_get_update" style="float:left;">To receive updates on new features, releases, etc. Please connect to one of our social media pages
                </div>
                <div id="lr_media_pages_container">
                    <?php
                    foreach ($media as $name => $href) {
                        echo '<a target="_blank" href="' . $href . '"><img src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/Loginradius/common/images/media/' . $name . '.png"></a>';
                    }
                    ?>
                </div>
            </div>
        </fieldset>
        <?php
    }

    /**
     * Get LoginRadius Module Help & Documentations container..
     */
    public function render_module_help_and_documentations_container() {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_left" id="lr_extension_help_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Help & Documentations') ?></strong></h4>
            <ul style="float:left; margin-right:43px">
                <li><a target="_blank" href="http://ish.re/TRYA">Extension Installation, Configuration and Troubleshooting</a></li>
                <li><a target="_blank" href="http://ish.re/9VBI">How to get LoginRadius API Key &amp; Secret</a></li>
                <li><a target="_blank" href="http://ish.re/9Z34">Magento Multisite Feature</a></li>
                <li><a target="_blank" href="http://ish.re/96M9">LoginRadius Products</a></li>
            </ul>
            <ul style="float:left; margin-right:43px">
                <li><a target="_blank" href="http://ish.re/96M7">About LoginRadius</a></li>
                <li><a target="_blank" href="http://ish.re/8PG8">Social Plugins</a></li>
                <li><a target="_blank" href="http://ish.re/O1W4">Social SDKs</a></li>
            </ul>
        </fieldset>
        <?php
    }

    /**
     * Get LoginRadius Module Help & Documentations container..
     */
    public function render_module_support_us_container() {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_right" id="lr_extension_support_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Support Us') ?></strong></h4>

            <p>
                <?php echo $this->__('If you liked our FREE open-source extension, please send your feedback/testimonial to') ?>
                <a href="mailto:feedback@loginradius.com">feedback@loginradius.com</a> !</p>
        </fieldset>
        <div style='clear:both'></div>
        <div id="loginRadiusKeySecretNotification" style="background-color: rgb(255, 255, 224); border: 1px solid rgb(230, 219, 85); padding: 5px; margin-bottom: 11px; display:none"><?php echo $this->__('To activate the <strong>Social Login</strong>, insert LoginRadius API Key and Secret in the <strong>Social Login Basic Settings</strong> section below. <strong>Social Sharing does not require API Key and Secret</strong>') ?>
        </div>
        <div style='clear:both'></div>
        <?php
    }

    /**
     * Render script for extension admin configuration options
     */
    public function render_module_admin_script_container() {
        ?>
        <div style='clear:both'></div>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#system_config_tabs').find('.active').each(function () {
                    if (jQuery(this).text().replace(/\s/g, "") == "") {
                        jQuery(this).parents('dd').hide();
                    }
                });
            });
        </script>
        <?php
    }

}
