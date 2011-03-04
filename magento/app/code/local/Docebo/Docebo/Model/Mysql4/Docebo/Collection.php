<?php

class Docebo_Docebo_Model_Mysql4_Docebo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('docebo/docebo');
    }
}