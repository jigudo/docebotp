<?php
class Docebo_Docebo_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/docebo?id=15 
    	 *  or
    	 * http://site.com/docebo/id/15 	
    	 */
    	/* 
		$docebo_id = $this->getRequest()->getParam('id');

  		if($docebo_id != null && $docebo_id != '')	{
			$docebo = Mage::getModel('docebo/docebo')->load($docebo_id)->getData();
		} else {
			$docebo = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($docebo == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$doceboTable = $resource->getTableName('docebo');
			
			$select = $read->select()
			   ->from($doceboTable,array('docebo_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$docebo = $read->fetchRow($select);
		}
		Mage::register('docebo', $docebo);
		*/

			
			$this->loadLayout();
			$this->renderLayout();
    }
}