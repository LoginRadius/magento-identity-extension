<?php
$this->startSetup();
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_sociallogin')} (
  `sociallogin_id` varchar(1000) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `avatar` varchar(1000) DEFAULT NULL,
  `verified` enum('0','1') DEFAULT NULL,
  `vkey` varchar(40) DEFAULT NULL,
  `provider` varchar(20) DEFAULT NULL,
  `uid` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'unblocked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$this->endSetup();
