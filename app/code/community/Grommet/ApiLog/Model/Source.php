<?php
/**
 * API Log Source model
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Model_Source extends Grommet_Lib_Model_Source_Abstract
{
	/**
	 * @return array
	 */
	public function getEventStatus ()
	{
		return array(
			Grommet_ApiLog_Model_Event::STATUS_SUCCESS => Mage::helper('apilog')->__('Success'),
			Grommet_ApiLog_Model_Event::STATUS_FAULT => Mage::helper('apilog')->__('Fault'),
		);
	}
	
	/**
	 * @return array
	 */
	public function getEventAdapter ()
	{
		$adapters = array();
		$types = Mage::getSingleton('api/config')->getAdapters();
		foreach ($types as $adapterName => $adapter) {
			if ($adapterName != 'default') {
				$adapters[$adapterName] = isset($adapter->label) ? $adapter->label : $adapterName;
			}
		}
		return $adapters;
	}
	
	/**
	 * @return array
	 */
	public function getLogResponseOptions ()
	{
		return array(
			Grommet_ApiLog_Model_Event::LOG_RESPONSE_DISABLED => Mage::helper('apilog')->__('Disabled'),
			Grommet_ApiLog_Model_Event::LOG_RESPONSE_FULL => Mage::helper('apilog')->__('Log All Responses'),
			Grommet_ApiLog_Model_Event::LOG_RESPONSE_FILTERED => Mage::helper('apilog')->__('Log Responses by Path'),
		);
	}
}