<?php

class Docebo_Docebo_Model_Mysql4_Docebo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the docebo_id refers to the key field in your database table.
        $this->_init('docebo/docebo', 'docebo_id');
    }
}