<?php

$installer = $this;

$installer->startSetup();

$installer->installEntities();
//$installer->removeAttribute('catalog_product', 'docebo_course'); echo "my install"; die();

$installer->endSetup(); 