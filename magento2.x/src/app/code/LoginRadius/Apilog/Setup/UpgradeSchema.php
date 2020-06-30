<?php

namespace LoginRadius\Apilog\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
       
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '4.0.0', '<')) {

            
            $this->createTables($setup);
            
            
        }
        
        
        
        $setup->endSetup();
    }
    
    /**
     * 
     * @param type $tableName
     * @return type
     */
    private function getTablesColamns($tableName) {
        $colamns = array(
            
            'api_log' => array('id', 'api_url','requested_type','data','response','response_type','created_date')
            
        );
        return isset($colamns[$tableName]) ? $colamns[$tableName] : null;
    }

    /**
     * 
     * @param type $name
     * @return type
     */
    private function getColamnArray($name) {
        $colamn = array(
            'id' => array('id', Table::TYPE_INTEGER, null, array('nullable' => false, 'primary' => true, 'identity'=>true), 'Auto Increment Id'),
           'created_date' => array('created_date', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Created Date'),
            'api_url' => array('api_url', Table::TYPE_TEXT, 600, array('nullable' => true, 'primary' => false), 'Api Url'),
            'requested_type' => array('requested_type', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Requested Type'),
            'data' => array('data', Table::TYPE_TEXT, 2000, array('nullable' => true, 'primary' => false), 'Data'),
            'response' => array('response', Table::TYPE_TEXT, 3000, array('nullable' => true, 'primary' => false), 'Response'),
            'response_type' => array('response_type', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Response Type'),
            
        );
        return isset($colamn[$name]) ? $colamn[$name] : null;
    }

    /**
     * 
     * @param type $setup
     */
    private function createTables($setup) {
        $tables = array(
            'api_log' => 'Lr Api Log'
        );
        foreach ($tables as $table => $comment) {
            $this->createTable($setup, $table, $comment);
        }
    }

    /**
     * 
     * @param type $setup
     * @param type $tname
     * @param type $comment
     */
    private function createTable($setup, $tname, $comment) {

        // Get tutorial_simplenews table
        $tableName = $setup->getTable('lr_' . $tname);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $setup->getConnection()->newTable($tableName);
            $columnNames = $this->getTablesColamns($tname);
            $columns = array();
            foreach ($columnNames as $columnName) {
                $columns[] = $this->getColamnArray($columnName);
            }
            foreach ($columns as $column) {
                $table->addColumn(
                        $column[0], $column[1], $column[2], $column[3], $column[4]
                );
            }
            $table->setComment($comment)
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

       

}
