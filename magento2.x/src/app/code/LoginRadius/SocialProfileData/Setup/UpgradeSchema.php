<?php

namespace LoginRadius\SocialProfileData\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
       
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '1.1.2', '<')) {

            // Get module table
            $tableName = $setup->getTable('lr_addresses');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'country' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'country name',
                        
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }

            }
            
            // Get module table
            $tableNameExtendedProfile = $setup->getTable('lr_extended_profile_data');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableNameExtendedProfile) == true) {
                // Declare data
                $columnsAdd = [
                    'no_of_login' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'no of login',
                        
                    ],
                ];

                $connectionData = $setup->getConnection();
                foreach ($columnsAdd as $name => $definition) {
                    $connectionData->addColumn($tableNameExtendedProfile, $name, $definition);
                }

            }
            
        }

        $setup->endSetup();
    }

       

}
