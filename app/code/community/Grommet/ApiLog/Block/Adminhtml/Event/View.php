<?php
/**
 * Event View block
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Block_Adminhtml_Event_View extends Mage_Adminhtml_Block_Template
{
	/**
	 * @return Grommet_ApiLog_Model_Event
	 */
	public function getEvent ()
	{
		return Mage::registry('current_event');
	}
}