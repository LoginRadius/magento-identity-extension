<?php
/**
 * Copyright Â© 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Used in creating options for Yes|No config value selection
 *
 */

namespace LoginRadius\Apilog\Model\Source;

class Info implements \Magento\Framework\Option\ArrayInterface {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {

        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
        $connection = $this->_resources->getConnection();

        $select = $connection->select()
                ->from(
                    ['o' => $this->_resources->getTableName('lr_api_log')]
                )->limit(20);

        $result = $connection->fetchAll($select);
        ?>
        <h2>Debug Log</h2>
        <?php if (isset($result) && !empty($result)) { ?> 
            <input type="submit" name="clearApi" id="clearApi" class="clearApi" value="Clear Logs"> 
        <?php } ?>
        <div id="apilogTab">        
            <div id="tabs-10">        
                <?php if (isset($result) && !empty($result)) { ?> 
                    <table class="form-table sociallogin_table" cellspacing="0">
                        <thead>
                            <tr>
                                <th align="center" class="head borderWidth"><?= 'Id'; ?></th>
                                <th align="center" class="head borderWidth"><?= 'Api Url'; ?></th>
                                <th align="center" class="head borderWidth"><?= 'Requested Type '; ?></th>
                                <th align="center" class="head borderWidth"><?= 'Data'; ?></th>
                                <th align="center" class="head borderWidth"><?= 'Response'; ?></th>
                                <th align="center" class="head borderWidth"><?= 'Response Type'; ?></th>
                                <th align="center" class="head"><?= 'Created Date'; ?></th>
                            </tr>
                        </thead>
                        <?php
                        $i = 1;

                        foreach ($result as $keys => $values) {
                            ?>
                            <tr>
                                <td align="center" class="borderWidth"><?= $i ?></td>
                                <td align="center" class="borderWidth"><?= $values['api_url'] ?></td>
                                <td align="center" class="borderWidth"><?= strtoupper($values['requested_type']) ?></td>
                                <td align="center" class="borderWidth"><?= $values['data'] ?></td>
                                <td align="center" class="borderWidth comment more responseData"><?= $values['response'] ?></td>
                                <td align="center" class="borderWidth"><?= $values['response_type'] ?></td>
                                <td align="center"><?= $values['created_date'] ?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </table>
                    <?php
                }
                else {
                    ?>
                    <div>No Log Data Available</div>
                <?php }
                ?>
            </div>  
        </div>
        <script type="text/javascript">

            require(["jquery"], function ($) {

                $(document).ready(function () {
                    if (jQuery('#messages').children().children().length > 0) {
                        jQuery('#messages').children().children().text("Log cleared successfully.");
                    }
                });
            });

        </script>
        <?php
    }

}
