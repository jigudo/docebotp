<?php

class Docebo_Docebo_Model_Product_Attribute_Source_Unit extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

	public function getAllOptions() {
	

		if (!$this->_options) {

			$userdata =array();
			$docebo_req =Mage::helper('docebo/doceborequest');
			$res = call_user_func(array($docebo_req, 'request'), 'courses', $userdata, false, 'course');
			//$res = $docebo_req::request('courses', $userdata, false, 'course');
			//var_export($res);

			$xml = new SimpleXMLElement($res);
			$nodes = $xml->xpath('//course_info');

			$this->_options =array(array('value'=>'','label'=>''));

			foreach ($nodes as $item) {
				$this->_options[]=array(
					'value'=>$item->course_id,
					'label'=>$item->course_name,
				);
			}
		}

		return $this->_options;


		die();

		if (!$this->_options) {
			$this->_options = array(
				array(
					'value'=>'',
					'label'=>'',
				),
				array(
					'value'=>'1',
					'label'=>'Day',
				),
				array(
					'value'=>'2',
					'label'=>'Week',
				),
				array(
					'value'=>'3',
					'label'=>'Month',
				),
				array(
					'value'=>'4',
					'label'=>'Year',
				),
				array(
					'value'=>'5',
					'label'=>'Bla',
				)
			);
		}
		return $this->_options;
	}


}