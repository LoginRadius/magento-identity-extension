<?php
$this->startSetup();
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_logs_data')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(2000) NOT NULL,
  `method` varchar(10) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `response` text DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `created_date` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);");
$this->endSetup();
