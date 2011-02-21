<?php
class Docebo_Docebo_Block_Docebo extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getDocebo()     
     { 
        /* if (!$this->hasData('docebo')) {
            $this->setData('docebo', Mage::registry('docebo'));
        }
        return $this->getData('docebo'); */
        
    }
}