<?php

class Docebo_Docebo_Model_Observer_Main {


	public function __construct() {

	}


	public function orderPayment($observer) {

		$order_id =$observer->event->invoice->order_id;
		$order =Mage::getModel('sales/order')->load($order_id);
		$docebo_req =Mage::helper('docebo/doceborequest');

		$items_log ='';

		$data =$order->getItemsCollection();
		foreach ($data as $item) {
			/* $items_log.="\n\n".print_r($item->item_id, true);
			$items_log.=" | ".print_r($item->product_id, true);
			$items_log.=" | ".print_r($item->is_virtual, true);
			$items_log.=" | ".print_r($item->sku, true);
			$items_log.=" | ".print_r($item->name, true);
			$items_log.=" | ".print_r($item->price, true); */
			
			$product =Mage::getModel('catalog/product')->load($item->product_id);
			$items_log.=" | ".print_r($product->docebo_course, true);

			$course_id =(int)$product->docebo_course;

			// --- Call to the docebo API ------------------------
			if ($course_id > 0) {
				$userdata =array();
				$userdata['course_id']=$course_id;
				$userdata['user_level']='student';
				$userdata['ext_user'] = $order->customer_id;
				$userdata['ext_user_type'] = 'magento';

				//$res =$docebo_req::request('addusersubscription', $userdata, $order->customer_email, 'course');
				$res = call_user_func(array($docebo_req, 'request'), 'addusersubscription', $userdata, $order->customer_email, 'course');
			}
			// ---
		}

		/* $items_log.="\n\n".$order->customer_email;
		$items_log.=" | ".print_r($order->customer_id, true); */

		/*
		$user =Mage::getSingleton('customer/session')->getCustomer();
		$items_log.=" | ".print_r($user->getEmail(), true);
		*/

		// $item->loadAllAttributes();
		// $items_log.="\n\n :: ".print_r($product->getAttributeText('docebo_course'), true);
		// $items_log.="\n\n :: ".print_r($product->getResource()->getAttribute('docebo_course')->getValue($product), true);
		// $items_log.="\n\n".print_r($product, true);
		// $items_log.="\n\n".print_r($item, true);

//		file_put_contents('mylog.txt', 'order payment: '.
//			/* var_export($observer->event->payment->item_id, true).' - '.
//			var_export($observer->event->payment->order_id, true).' - '.
//			var_export($observer->event->payment->is_virtual, true).' - '. */
//			// var_export($observer->event->invoice->item_id, true).' - '.
//			var_export($observer->event->invoice->order_id, true).' - '. // ok!
//			//var_export($observer->event->invoice->is_virtual, true).' - '.
//			//var_export($observer->event->invoice->data, true).' - '.
//			//print_r($observer->event->payment, true).
//			' ----------------------------- '.
//			"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n".
//			//print_r($observer->event->invoice, true)
//			$items_log
//		);
	}


	public function customerSave($observer) {

		$docebo_req =Mage::helper('docebo/doceborequest');

		$userdata =array();
		$userdata['email']=$observer->customer->getData('email');
		$userdata['firstname']=$observer->customer->getData('firstname');
		$userdata['lastname']=$observer->customer->getData('lastname');
		$userdata['ext_user'] = $observer->customer->getData('entity_id');
		$userdata['ext_user_type'] = 'magento';

		//$res =$docebo_req::request('updateuser', $userdata, $observer->customer->getData('email'));
		$res = call_user_func(array($docebo_req, 'request'), 'updateuser', $userdata, $observer->customer->getData('email'));

		
		/* file_put_contents('mylog.txt', 'customer: '.
			print_r($observer->customer->getData('firstname'), true)." | ".
			print_r($observer->customer->getData('lastname'), true)." | ".
			print_r($observer->customer->getData('email'), true)." | ".
			print_r($observer->customer->getData('entity_id'), true)." | ".
			' ----------------------------- '.
			"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n".
			print_r($observer->customer, true).
			""
		); */
	}


	public function customerNew($observer) {		

		$customer_id =(int)$observer->customer->getData('entity_id');

		if ($observer->customer->isObjectNew() && $customer_id > 0) { // only if it is a new customer:

			$docebo_req =Mage::helper('docebo/doceborequest');

			$userdata =array();
			$userdata['userid'] = $observer->customer->getData('email');
			$userdata['email']=$observer->customer->getData('email');
			$userdata['password'] = $observer->customer->getData('password');
			//$userdata['password'] = uniqid('', true);
			$userdata['firstname']=$observer->customer->getData('firstname');
			$userdata['lastname']=$observer->customer->getData('lastname');
			$userdata['ext_user'] = $customer_id;
			$userdata['ext_user_type'] = 'magento';

			//$res =$docebo_req::request('createuser', $userdata, $observer->customer->getData('email'));
			$res = call_user_func(array($docebo_req, 'request'), 'createuser', $userdata, $observer->customer->getData('email'));
		}


		/* file_put_contents('mylog.txt', "\n\n-----------------\n\n".'new customer: '.
			//print_r($observer->isObjectNew(), true)." | ".
			var_export($observer->customer->isObjectNew(), true)." | ".
			print_r($observer->customer->getData('firstname'), true)." | ".
			print_r($observer->customer->getData('lastname'), true)." | ".
			print_r($observer->customer->getData('email'), true)." | ".
			print_r($observer->customer->getData('password'), true)." | ".
			print_r($observer->customer->getData('entity_id'), true)." | ".
			// ' ----------------------------- '.
			// "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n".
			// print_r($observer->customer, true).
			// "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n".
			// print_r($observer, true).
			"",
			FILE_APPEND
		); */
	}


}

?>
