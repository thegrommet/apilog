<?php
/**
 * API Log Cron
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Model_Cron
{
	/**
	 * Delete old log entries.
	 * 
	 * @param string $schedule
	 */
	public function clean ($schedule)
	{
		$helper = Mage::helper('apilog');
		if (!$helper->isEnabled()) {
			return;
		}
		$lifetime = (int)$helper->getConfig('lifetime');
		if (!$lifetime || $lifetime <= 0) {
			return;
		}
		$date = new Zend_Date(Mage::getModel('core/date')->gmtTimestamp());
		$date->subDay($lifetime);

		Mage::getResourceModel('apilog/event')->cleanBeforeDate($date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
	}
}
