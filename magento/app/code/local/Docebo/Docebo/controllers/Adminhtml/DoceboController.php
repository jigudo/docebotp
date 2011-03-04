<?php

class Docebo_Docebo_Adminhtml_DoceboController extends Mage_Adminhtml_Controller_action
{

 
	public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('docebo/index');

		//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));

		//$this->_addContent($this->getLayout()->createBlock('docebo/adminhtml_docebo_edit'));
				//->_addLeft($this->getLayout()->createBlock('docebo/adminhtml_docebo_edit_tabs'))
				//->_addContent($this->getLayout()->createBlock('docebo/adminhtml_docebo_edit_tab_form'));

		$this->_addContent($this->getLayout()->createBlock('docebo/adminhtml_docebo'));
		$this->_addContent($this->getLayout()->createBlock('docebo/adminhtml_docebo_edit_tab_form'));

		$this->renderLayout();
	}

 
	public function saveAction() { // sync users:

		$userdata =array();

		/* $users = Mage::getModel('customer/customer')->getCollection();
		/* foreach ($users as $customer) {
			$is_active =$customer->is_active;
			$email =$customer->email;
			$customer_id =$customer->entity_id;
			// print_r($customer_id.": ".$email);
			//$users[$customer_id]=$email;
		} */


		$users = Mage::getModel('customer/customer')->getCollection();

		$i = 0;
    foreach($users as $customer) {
			$is_active =$customer->is_active;
			$email =$customer->email;
			$customer_id =$customer->entity_id;
			
      if ($customer_id > 0) {
        $userdata['user_'.$i.'[ext_user]'] = $customer_id;
        $userdata['user_'.$i.'[ext_user_type]'] = 'magento';
        $userdata['user_'.$i.'[email]'] = $email;

        $i++;
      }
    }

		$res =false;
		if (!empty($userdata)) {
			$docebo_req =Mage::helper('docebo/doceborequest');
			$res = $docebo_req::request('importextusersfromemail', $userdata);
		}

		// var_dump($res); die();

		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('docebo')->__('Magento users has been synchronized with the ones matching in Docebo.'));
		$this->_redirect('*/*/');

		// Mage::getSingleton('adminhtml/session')->addError(Mage::helper('docebo')->__('Unable to find item to save'));
	}
 
}