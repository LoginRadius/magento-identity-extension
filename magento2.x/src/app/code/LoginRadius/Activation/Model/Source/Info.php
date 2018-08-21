<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Used in creating options for Yes|No config value selection
 *
 */

namespace LoginRadius\Activation\Model\Source;

class Info implements \Magento\Framework\Option\ArrayInterface {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        ?>
        <div id="firstDiv">
            <div class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_left" id="lr_thank_message_container">
                <div class="innerDiv">
                    <h4 class="lr_admin_fieldset_title titleHeading"><strong><?= 'Thank you for installing LoginRadius Extension!' ?></strong></h4>

                    <p>
                        <?= 'To activate the extension, you will need to first configure it (manage your desired social networks, etc.) from your LoginRadius account. If you do not have an account, click' ?>
                        <a target="_blank" href="http://www.loginradius.com/"><?= 'here' ?></a> <?= 'and create one for FREE!' ?>
                    </p>

                    <p>
                        <?= 'We also offer Social Plugins for' ?>
                        <a href="http://ish.re/1EVIO" target="_blank">Wordpress</a>,
                        <a href="http://ish.re/1FITS" target="_blank">Joomla</a>,
                        <a href="http://ish.re/1FITT" target="_blank">Drupal</a> !
                    </p>
                    </br>
                    <div style="margin-top:10px">
                        <a style="text-decoration:none;margin-right:10px;" href="https://www.loginradius.com/" target="_blank">
                            <input class="form-button" style="background-color: #e1e1e1;" type="button" value="<?= 'Set up my account!' ?>">
                        </a>
                        <a class="loginRadiusHow" target="_blank"
                           href="http://ish.re/4">(<?= 'How to set up an account?' ?>)</a>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_right" id="lr_extension_info_container">
                    <div class="innerDivRight">
                        <h4 class="lr_admin_fieldset_title titleHeading"><strong><?= 'Extension Information!' ?></strong></h4>

                        <div style="margin:5px 0">
                            <strong>Version: </strong> 3.0.1<br/>
                            <strong>Author:</strong> LoginRadius<br/>
                            <strong>Website:</strong> <a href="https://www.loginradius.com" target="_blank">www.loginradius.com</a>
                            <br/>
                            <div id="sociallogin_get_update" style="float:left;">To receive updates on new features, releases, etc. Please connect to one of our social media pages
                            </div>
                            <div id="lr_media_pages_container">
                                <a target="_blank" href="https://www.facebook.com/loginradius"><div class="facebookHelp"></div></a>
                                <a target="_blank" href="https://twitter.com/LoginRadius"><div class="twitterHelp"></div></a>
                                <a target="_blank" href="https://plus.google.com/+Loginradius"><div class="googleHelp"></div></a>
                                <a target="_blank" href="http://www.linkedin.com/company/loginradius"><div class="linkedinHelp"></div></a>
                                <a target="_blank" href="https://www.youtube.com/user/LoginRadius"><div class="youtubeHelp"></div></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="secondDiv">
            <div class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_left" id="lr_thank_message_container2">
                <div class="innerDiv">
                    <h4 class="lr_admin_fieldset_title titleHeading"><strong><?= 'Help & Documentations' ?></strong></h4>
                    <ul style="float:left; margin-right:43px; width: 58%;">
                        <li><a target="_blank" href="http://ish.re/11W6R">Extension Installation, Configuration and Troubleshooting</a></li>
                        <li><a target="_blank" href="http://ish.re/1FITY">How to get LoginRadius API Key &amp; Secret</a></li>
                        <li><a target="_blank" href="http://ish.re/96M9">LoginRadius Products</a></li>
                    </ul>
                    <ul style="float:left; margin-right:43px">
                        <li><a target="_blank" href="http://ish.re/96M7">About LoginRadius</a></li>
                        <li><a target="_blank" href="http://ish.re/8PG8">Social Plugins</a></li>
                        <li><a target="_blank" href="http://ish.re/O1W4">Social SDKs</a></li>
                    </ul>

                </div>
            </div>
            <div style="margin-top:19px;" class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_right" id="lr_extension_support_container">
                <div class="innerDivRight">
                    <h4 class="lr_admin_fieldset_title titleHeading"><strong><?= 'Support Us' ?></strong></h4>

                    <p>
        <?= 'If you liked our FREE open-source extension, please send your feedback/testimonial to' ?>
                        <a href="mailto:feedback@loginradius.com">feedback@loginradius.com</a> </p>
                </div>
            </div>
        </div>



    <?php
    }

}
