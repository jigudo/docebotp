<?php

class Docebo_Docebo_Block_Adminhtml_Docebo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();      
      $fieldset = $form->addFieldset('docebo_form', array('legend'=>Mage::helper('docebo')->__('Users synchronization')));

			$fieldset->addField('description', 'note', array(
          'text'     => Mage::helper('docebo')->__('You can synchronize your Docebo users with the one from you Magento installation. Clicking "Sync users from Magento to Docebo" will "link" the accounts in Magento with the correspoinding ones (having the same username) in your Magento installation.'),
          'name'      => 'description',
			));

			$this->setForm($form);

			return parent::_prepareForm();
  }
}