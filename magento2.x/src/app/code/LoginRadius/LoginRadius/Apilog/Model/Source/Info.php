<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
 
namespace LoginRadius\Apilog\Model\Source;

class Info implements \Magento\Framework\Option\ArrayInterface
{
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
     

   
        <div id="apilogTab">        
        
        <div id="tabs-10">        
          
                <table class="form-table sociallogin_table" cellspacing="0">
                    <thead>
                        <tr>
                           <th align="center" class="head borderWidth"><?php echo 'Id'; ?></th>
                           <th align="center" class="head borderWidth"><?php echo 'Api Url'; ?></th>
                           <th align="center" class="head borderWidth"><?php echo 'Requested Type '; ?></th>
                           <th align="center" class="head borderWidth"><?php echo 'Data'; ?></th>
                           <th align="center" class="head borderWidth"><?php echo 'Response'; ?></th>
                           <th align="center" class="head borderWidth"><?php echo 'Response Type'; ?></th>
                           <th align="center" class="head"><?php echo 'Created Date'; ?></th>
                        </tr>
                    </thead>
                    <?php
                    $i = 1;
                    if(isset($result) && !empty($result)){
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
    <?php $i++;
}}else{?>
                        <tr>
                            <td style="font-size: 20px;padding: 30px;" colspan="7" align="center" class="borderWidth">No Log Data Available</td>
                        </tr>
<?php } ?>
                </table>
            


        </div>  
    

   
</div>
   <input type="submit" name="clearApi" value="Clear Logs">  
  <?php 

  }
  
}
