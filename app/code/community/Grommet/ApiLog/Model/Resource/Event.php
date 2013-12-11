<?php
/**
 * Event resource model
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Model_Resource_Event extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct ()
	{
		$this->_init('apilog/event', 'event_id');
		$this->_serializableFields['arguments'] = array(null, array());
	}

	/**
	 * @param Grommet_ApiLog_Model_Event $object
	 * @return Grommet_ApiLog_Model_Resource_Event
	 */
	protected function _beforeSave (Mage_Core_Model_Abstract $object)
	{
		if (!$object->getId()) {
			$object->setTime($this->formatDate(time()));
		}
		return parent::_beforeSave($object);
	}

	/**
	 * Remove entries before a given date.
	 *
	 * @param string $date
	 * @return int
	 */
	public function cleanBeforeDate ($date)
	{
		return $this->_getWriteAdapter()->delete($this->getMainTable(), array('time < ?' => $date));
	}
}
