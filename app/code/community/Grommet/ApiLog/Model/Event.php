<?php
/**
 * Event model
 *
 * @author tmannherz
 * @method int getEventId()
 * @method string getPath()
 * @method Grommet_ApiLog_Model_Event setPath(string $path)
 * @method string getModel()
 * @method Grommet_ApiLog_Model_Event setModel(string $model)
 * @method string getAdapter()
 * @method Grommet_ApiLog_Model_Event setAdapter(string $adapter)
 * @method string getArguments()
 * @method Grommet_ApiLog_Model_Event setArguments(string $arguments)
 * @method string getStatus()
 * @method Grommet_ApiLog_Model_Event setStatus(string $status)
 * @method string getFaultCode()
 * @method Grommet_ApiLog_Model_Event setFaultCode(string $code)
 * @method string getFaultMessage()
 * @method Grommet_ApiLog_Model_Event setFaultMessage(string $message)
 * @method string getUser()
 * @method Grommet_ApiLog_Model_Event setUser(string $user)
 * @method int getUserId()
 * @method Grommet_ApiLog_Model_Event setUserId(int $userId)
 * @method string getTime()
 * @method Grommet_ApiLog_Model_Event setTime(string $time)
 * @method string getResponse()
 * @method Grommet_ApiLog_Model_Event setResponse(string $response)
 */
class Grommet_ApiLog_Model_Event extends Mage_Core_Model_Abstract
{
	const STATUS_SUCCESS = 'success';
	const STATUS_FAULT = 'fault';

	const LOG_RESPONSE_DISABLED = 0;
	const LOG_RESPONSE_FULL = 1;
	const LOG_RESPONSE_FILTERED = 2;

	/**
	 * Event descriptors
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'apilog_event';
	protected $_eventObject = 'event';

	protected function _construct ()
	{
		$this->_init('apilog/event');
	}

	/**
	 * @return string
	 */
	public function getAdapterLabel ()
	{
		$adapter = $this->getAdapter();
		$adapters = Mage::getSingleton('apilog/source')->getEventAdapter();
		if (isset($adapters[$adapter])) {
			return $adapters[$adapter];
		}
		return $adapter;
	}

	/**
	 * @return string
	 */
	public function getStatusLabel ()
	{
		$status = $this->getStatus();
		$statuses = Mage::getSingleton('apilog/source')->getEventStatus();
		if (isset($statuses[$status])) {
			return $statuses[$status];
		}
		return $status;
	}

	/**
	 * Log the response depending on the config.
	 * 
	 * @param string $path
	 * @param string $response
	 * @return Grommet_ApiLog_Model_Event
	 */
	public function setLogResponse ($path, $response)
	{
		$logType = Mage::helper('apilog')->getConfig('log_response');
		switch ($logType) {
			case self::LOG_RESPONSE_FULL:
				$this->setResponse($response);
				break;

			case self::LOG_RESPONSE_FILTERED:
				$logPaths = Mage::helper('apilog')->getConfig('log_response_paths');
				if ($logPaths) {
					$logPaths = explode("\n", $logPaths);
					if (in_array($path, $logPaths)) {
						$this->setResponse($response);
					}
				}
				break;

			case self::LOG_RESPONSE_DISABLED:
			default:
				// do nothing
		}
		return $this;
	}
}
