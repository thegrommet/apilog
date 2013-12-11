<?php
/**
 * Server
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Model_Api_Server extends Mage_Api_Model_Server
{
	/**
	 * @var string
	 */
	protected $_adapterCode;

	/**
	 * Initialize server components. Lightweight implementation of init() method
	 *
	 * @param string $adapterCode Adapter code
	 * @param string $handler OPTIONAL Handler name (if not specified, it will be found from config)
	 * @return Mage_Api_Model_Server
	 */
	public function initialize ($adapterCode, $handler = null)
	{
		$this->_adapterCode = $adapterCode;
		return parent::initialize($adapterCode, $handler);
	}

	/**
	 * @return string
	 */
	public function getAdapterCode ()
	{
		return $this->_adapterCode;
	}
}
