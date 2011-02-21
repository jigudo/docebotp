<?php

class Docebo_Docebo_Model_Docebo extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('docebo/docebo');
    }
}