<?php
/**
 * API Log Data helper
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Helper_Data extends Grommet_Lib_Helper_Abstract
{
	/**
	 * @var string
	 */
	protected $_defaultLogfile = 'apilog.log';

	/**
	 * @return bool
	 */
	public function isEnabled ()
	{
		return (bool)$this->getConfig('enabled');
	}

	/**
	 * Config getter.
	 *
	 * @param int $path
	 * @param int $storeId
	 * @return string
	 */
	public function getConfig ($path, $storeId = null)
	{
		return Mage::getStoreConfig('system/apilog/' . $path, $storeId);
	}
}
