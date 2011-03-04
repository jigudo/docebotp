<?php
class Docebo_Docebo_Block_Adminhtml_Docebo extends Mage_Adminhtml_Block_Widget_Form_Container
{

	  public function __construct()
    {
        parent::__construct();

        //$this->_objectId = 'id';
				$this->_controller = 'adminhtml_docebo';
        $this->_blockGroup = 'docebo';        

        $this->_updateButton('save', 'label', Mage::helper('docebo')->__('Sync users from Magento to Docebo'));
        // $this->_updateButton('delete', 'label', Mage::helper('docebo')->__('Delete Item'));

				//$this->removeButton('reset');
				//$this->removeButton('back');
    }

    public function getHeaderText()
    {
        /* if( Mage::registry('docebo_data') && Mage::registry('docebo_data')->getId() ) {
            return Mage::helper('docebo')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('docebo_data')->getTitle()));
        } else {
            return Mage::helper('docebo')->__('Add Item');
        } */

			return Mage::helper('docebo')->__('Users');
    }

}