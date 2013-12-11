<?php
/**
 * Event Grid Container
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Block_Adminhtml_Event extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct ()
	{
		$this->_blockGroup = 'apilog';
		$this->_controller = 'adminhtml_event';
		$this->_headerText = Mage::helper('apilog')->__('API Requests Log');
		
		parent::__construct();

		$this->_removeButton('add');
	}
}
