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
class Loginradius_Logs_Model_Logs extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $logsData = $this->selectLog('logs_data');
        if (is_array($logsData)) {
            ?>
            <style type="text/css">
                .sociallogin_table {
                    background-color: #efefef;
                    border: 1px solid #ccc;
                    margin-bottom: 10px;
                    border-collapse: collapse;
                    font-family: sans-serif;
                    font-size: 12px;
                    line-height: 1.4em;
                    margin-left: 2px;
                    width: 100%;
                    clear: both
                }
                .sociallogin_table th {
                    padding: 10px;
                    text-align: left;
                }
                .sociallogin_table td {
                    border: 1px solid #ddd;
                    padding: 5px;
                    vertical-align: middle;
                }
                .sociallogin_table th.head {
                    background: #2d444f;
                    color: #fff;
                }
                .sociallogin_table .Error {
                    color: red;
                }
                .sociallogin_table .Success {
                    color: green;
                }
                .sociallogin_table a {
                    color: #0254EB;
                }
                .sociallogin_table a:visited {
                    color: #0254EB;  
                }
                .sociallogin_table a.morelink {
                    text-decoration:none;
                    outline: none;
                }
                .sociallogin_table .morecontent span {
                    display: none;
                }
                .sociallogin_table .comment {
                    max-width: 240px;
                    margin: 10px;
                    word-wrap: break-word;
                }
                div#ajaxclear {
                    float: right;
                    border-color: #ed6502 #a04300 #a04300 #ed6502;
                    background: #ffac47;
                    padding: 3px 10px;
                    margin: 5px;
                    color: #fff;
                }
                div#ajaxclear:hover{box-shadow: 0px 0px 2px 1px #a04300;cursor: pointer;font-weight: bold;}
            </style>
            <div id="ajaxclear">Clear</div>
            <table class="form-table sociallogin_table">
                <tr>
                    <th class="head">Id</th>
                    <th class="head">Url</th>
                    <th class="head">Method</th>
                    <th class="head">Data</th>
                    <th class="head">Response</th>
                    <th class="head">Status</th>
                    <th class="head">Created Date</th>
                </tr>

                <?php
            }
            foreach ($logsData as $data) {
                ?>
                <tr>
                    <td><?php echo $data['id']; ?>.</td>
                    <td class="manage-colum comment more"><?php echo $data['url']; ?></td>
                    <td><?php echo $data['method']; ?></td>
                    <td class="manage-colum comment more"><?php echo $data['data']; ?></td>
                    <td class="manage-colum comment more"><?php echo $data['response']; ?></td>
                    <td class="<?php echo $data['status']; ?>"><?php echo $data['status']; ?></td>
                    <td><?php echo $data['created_date']; ?></td>
                </tr>
            <?php } ?>
        </table>
        <?php
        // Get LoginRadius Module Support Us container..
        $this->render_module_admin_script_container();
    }

    private function selectLog($tableName) {
        $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $loginRadiusQuery = "SELECT * FROM " . Mage::getSingleton('core/resource')->getTableName('lr_' . $tableName) . " ORDER BY id DESC LIMIT 0, 20";
        $loginRadiusQueryHandle = $loginRadiusConn->query($loginRadiusQuery);
        return $loginRadiusQueryHandle->fetchAll();
    }

    /**
     * Render script for extension admin configuration options
     */
    public function render_module_admin_script_container() {
        ?>
        <div style='clear:both'></div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.content-header').hide();
                var showChar = 200;
                var ellipsestext = "...";
                var moretext = "more";
                var lesstext = "less";
                $('.more').each(function () {
                    var content = $(this).html();

                    if (content.length > showChar) {

                        var c = content.substr(0, showChar);
                        var h = content.substr(showChar - 1, content.length - showChar);

                        var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

                        $(this).html(html);
                    }

                });

                $(".morelink").click(function () {
                    if ($(this).hasClass("less")) {
                        $(this).removeClass("less");
                        $(this).html(moretext);
                    } else {
                        $(this).addClass("less");
                        $(this).html(lesstext);
                    }
                    $(this).parent().prev().toggle();
                    $(this).prev().toggle();
                    return false;
                });

                $("#ajaxclear").click(function () {
                    new Ajax.Request("<?php echo $this->getUrl('adminhtml/index/index'); ?>",
                            {
                                method: 'post',
                                parameters: {
                                    lrlogclear: 'true'
                                },
                                onComplete: function (result)
                                {
                                    location.reload();
                                }
                            });
                });
            });
        </script>
        <?php
    }

}
