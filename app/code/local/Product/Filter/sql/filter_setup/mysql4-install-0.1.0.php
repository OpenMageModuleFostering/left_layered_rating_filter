<?php
$installer = $this;
$installer->startSetup();
$installer->run("INSERT INTO {$this->getTable('eav_attribute')} (entity_type_id, attribute_code, frontend_label) VALUES ('10', 'review', 'Review')");
$installer->endSetup();
