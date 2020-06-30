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
                    <br/>
                    <div style="margin-top:10px">
                        <a style="text-decoration:none;margin-right:10px;" href="https://www.loginradius.com/" target="_blank">
                            <input class="form-button" style="background-color: #e1e1e1;" type="button" value="<?= 'Set up my account!' ?>">
                        </a>
                        <a class="loginRadiusHow" target="_blank"
                           href="http://ish.re/4">(<?= 'How to set up an account?' ?>)</a>
                    </div>
                </div>
            </div>            
        </div>
    <?php
    }

}
