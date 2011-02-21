<?php /*

class Docebo_Docebo_Block_Adminhtml_Docebo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'docebo';
        $this->_controller = 'adminhtml_docebo';
        
        $this->_updateButton('save', 'label', Mage::helper('docebo')->__('Sync users from Magento to Docebo'));     

				$this->removeButton('reset');
    }

    public function getHeaderText()
    {
			return Mage::helper('docebo')->__('Users');
    }
} */