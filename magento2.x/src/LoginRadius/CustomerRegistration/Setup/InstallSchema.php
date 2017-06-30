<?php

namespace LoginRadius\CustomerRegistration\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        $this->createTables($setup);
        $setup->endSetup();
    }

    /**
     * 
     * @param type $tableName
     * @return type
     */
    private function getTablesColamns($tableName) {
        $colamns = array(
            'sociallogin' => array('id','entity_id','sociallogin_id','uid','avatar','verified','vkey','status','provider')
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
            'entity_id' => array('entity_id', Table::TYPE_INTEGER, null, array('nullable' => false, 'primary' => false), 'Customer Id'),
            'sociallogin_id' => array('sociallogin_id', Table::TYPE_TEXT, 1000, array('nullable' => false, 'primary' => false), 'Social Provider Id'),
            'provider' => array('provider', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Provider Name'),
            'avatar' => array('avatar', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Avatar'),
            'verified' => array('verified', Table::TYPE_TEXT, 1, array('nullable' => true, 'primary' => false), 'Verified'),
            'vkey' => array('vkey', Table::TYPE_TEXT, 40, array('nullable' => true, 'primary' => false), 'Verified Key'),
            'uid' => array('uid', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'uid'),
            'status' => array('status', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Status')
        );
        return isset($colamn[$name]) ? $colamn[$name] : null;
    }

    /**
     * 
     * @param type $setup
     */
    private function createTables($setup) {
        $this->createTable($setup, 'sociallogin', 'Basic social Profile Data');
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
