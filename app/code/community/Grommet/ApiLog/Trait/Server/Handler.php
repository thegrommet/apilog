<?php
/**
 * Server Handler Trait
 *
 * @author tmannherz
 */
trait Grommet_ApiLog_Trait_Server_Handler
{
	/**
	 * @var Grommet_ApiLog_Model_Event 
	 */
	protected $_log;

	/**
	 * Trait constructor logic
	 */
	public function traitConstruct ()
	{
		$this->_log = Mage::getModel('apilog/event');
		$this->_log->setAdapter($this->_getServer()->getAdapterCode());
	}

	/**
	 * Call resource functionality
	 *
	 * @param string $sessionId
	 * @param string $apiPath
	 * @param array  $args
	 * @return mixed
	 */
	public function call ($sessionId, $apiPath, $args = array())
	{
		if (!$this->isEnabled()) {
			return parent::call($sessionId, $apiPath, $args);
		}
		$this->_startSession($sessionId);
		$user = Mage::getModel('api/user')->loadBySessId($sessionId);

		$this->_log
			->setUser($user->getUsername())
			->setUserId($user->getId())
			->setPath($apiPath)
			->setArguments((array)$args);

		if (!$this->_getSession()->isLoggedIn($sessionId)) {
			return $this->_fault('session_expired');
		}

		list($resourceName, $methodName) = explode('.', $apiPath);

		if (empty($resourceName) || empty($methodName)) {
			return $this->_fault('resource_path_invalid');
		}

		$resourcesAlias = $this->_getConfig()->getResourcesAlias();
		$resources = $this->_getConfig()->getResources();
		if (isset($resourcesAlias->$resourceName)) {
			$resourceName = (string)$resourcesAlias->$resourceName;
		}

		if (!isset($resources->$resourceName) || !isset($resources->$resourceName->methods->$methodName)) {
			return $this->_fault('resource_path_invalid');
		}

		if (!isset($resources->$resourceName->public) && isset($resources->$resourceName->acl)
			&& !$this->_isAllowed((string)$resources->$resourceName->acl)) {
			return $this->_fault('access_denied');
		}


		if (!isset($resources->$resourceName->methods->$methodName->public)
			&& isset($resources->$resourceName->methods->$methodName->acl)
			&& !$this->_isAllowed((string)$resources->$resourceName->methods->$methodName->acl)) {
			return $this->_fault('access_denied');
		}

		$methodInfo = $resources->$resourceName->methods->$methodName;

		try {
			$method = (isset($methodInfo->method) ? (string)$methodInfo->method : $methodName);

			$modelName = $this->_prepareResourceModelName((string)$resources->$resourceName->model);
			$this->_log->setModel($modelName);
			try {
				$model = Mage::getModel($modelName);
				if ($model instanceof Mage_Api_Model_Resource_Abstract) {
					$model->setResourceConfig($resources->$resourceName);
				}
			} catch (Exception $e) {
				$this->_log
					->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
					->setFaultCode('resource_path_not_callable')
					->save();
				throw new Mage_Api_Exception('resource_path_not_callable');
			}

			if (method_exists($model, $method)) {
				if (isset($methodInfo->arguments) && ((string)$methodInfo->arguments) == 'array') {
					$ret = $model->$method((is_array($args) ? $args : array($args)));
				}
				elseif (!is_array($args)) {
					$ret = $model->$method($args);
				}
				else {
					$ret = call_user_func_array(array(&$model, $method), $args);
				}
				$this->_log->setStatus(Grommet_ApiLog_Model_Event::STATUS_SUCCESS)
					->setLogResponse($apiPath, print_r($ret, 1))
					->save();

				return $ret;
			}
			else {
				$this->_log
					->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
					->setFaultCode('resource_path_not_callable')
					->save();
				throw new Mage_Api_Exception('resource_path_not_callable');
			}
		} catch (Mage_Api_Exception $e) {
			return $this->_fault($e->getMessage(), $resourceName, $e->getCustomMessage());
		} catch (Exception $e) {
			Mage::logException($e);
			return $this->_fault('internal', null, $e->getMessage());
		}
	}
	
	/**
	 * Multiple calls of resource functionality
	 *
	 * @param string $sessionId
	 * @param array $calls
	 * @param array $options
	 * @return array
	 */
	public function multiCall ($sessionId, array $calls = array(), $options = array())
	{
		if (!$this->isEnabled()) {
			return parent::multiCall($sessionId, $calls, $options);
		}
		$this->_startSession($sessionId);
		$user = Mage::getModel('api/user')->loadBySessId($sessionId);
		
		$this->_log
			->setUser($user->getUsername())
			->setUserId($user->getId());

		if (!$this->_getSession()->isLoggedIn($sessionId)) {
			return $this->_fault('session_expired');
		}

		$result = array();

		$resourcesAlias = $this->_getConfig()->getResourcesAlias();
		$resources = $this->_getConfig()->getResources();

		foreach ($calls as $call) {
			if (!isset($call[0])) {
				$result[] = $this->_faultAsArray('resource_path_invalid');
				if (isset($options['break']) && $options['break'] == 1) {
					break;
				}
				else {
					continue;
				}
			}

			$apiPath = $call[0];
			$args = (isset($call[1]) ? $call[1] : array());

			list($resourceName, $methodName) = explode('.', $apiPath);

			$log = clone $this->_log;
			$log->setPath('multiCall.' . $apiPath)
				->setArguments((array)$args);

			if (empty($resourceName) || empty($methodName)) {
				$fault = $this->_faultAsArray('resource_path_invalid');
				$result[] = $fault;
				$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
					->setFaultCode($fault['faultCode'])
					->setFaultMessage($fault['faultMessage'])
					->save();
				if (isset($options['break']) && $options['break'] == 1) {
					break;
				}
				else {
					continue;
				}
			}

			if (isset($resourcesAlias->$resourceName)) {
				$resourceName = (string)$resourcesAlias->$resourceName;
			}

			if (!isset($resources->$resourceName) || !isset($resources->$resourceName->methods->$methodName)) {
				$fault = $this->_faultAsArray('resource_path_invalid');
				$result[] = $fault;
				$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
					->setFaultCode($fault['faultCode'])
					->setFaultMessage($fault['faultMessage'])
					->save();
				if (isset($options['break']) && $options['break'] == 1) {
					break;
				}
				else {
					continue;
				}
			}

			if (!isset($resources->$resourceName->public) && isset($resources->$resourceName->acl) && !$this->_isAllowed((string)$resources->$resourceName->acl)) {
				$fault = $this->_faultAsArray('access_denied');
				$result[] = $fault;
				$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
					->setFaultCode($fault['faultCode'])
					->setFaultMessage($fault['faultMessage'])
					->save();
				if (isset($options['break']) && $options['break'] == 1) {
					break;
				}
				else {
					continue;
				}
			}


			if (!isset($resources->$resourceName->methods->$methodName->public) && isset($resources->$resourceName->methods->$methodName->acl) && !$this->_isAllowed((string)$resources->$resourceName->methods->$methodName->acl)) {
				$fault = $this->_faultAsArray('access_denied');
				$result[] = $fault;
				$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
					->setFaultCode($fault['faultCode'])
					->setFaultMessage($fault['faultMessage'])
					->save();
				if (isset($options['break']) && $options['break'] == 1) {
					break;
				}
				else {
					continue;
				}
			}

			$methodInfo = $resources->$resourceName->methods->$methodName;

			try {
				$method = (isset($methodInfo->method) ? (string)$methodInfo->method : $methodName);

				$modelName = $this->_prepareResourceModelName((string)$resources->$resourceName->model);
				$log->setModel($modelName);

				try {
					$model = Mage::getModel($modelName);
				} catch (Exception $e) {
					$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
						->setFaultCode('resource_path_not_callable')
						->save();
					throw new Mage_Api_Exception('resource_path_not_callable');
				}

				if (method_exists($model, $method)) {
					if (isset($methodInfo->arguments) && ((string)$methodInfo->arguments) == 'array') {
						$result[] = $model->$method((is_array($args) ? $args : array($args)));
					}
					elseif (!is_array($args)) {
						$result[] = $model->$method($args);
					}
					else {
						$result[] = call_user_func_array(array(&$model, $method), $args);
					}
					$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_SUCCESS)
						->setLogResponse($apiPath, print_r(end($result), 1))
						->save();
				}
				else {
					$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
						->setFaultCode('resource_path_not_callable')
						->save();
					throw new Mage_Api_Exception('resource_path_not_callable');
				}
			} catch (Mage_Api_Exception $e) {
				$fault = $this->_faultAsArray($e->getMessage(), $resourceName, $e->getCustomMessage());
				$result[] = $fault;
				$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
					->setFaultCode($fault['faultCode'])
					->setFaultMessage($fault['faultMessage'])
					->save();
				if (isset($options['break']) && $options['break'] == 1) {		
					break;
				}
				else {
					continue;
				}
			} catch (Exception $e) {
				Mage::logException($e);
				$fault = $this->_faultAsArray('internal');
				$result[] = $fault;
				$log->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
					->setFaultCode($fault['faultCode'])
					->setFaultMessage($fault['faultMessage'])
					->save();
				if (isset($options['break']) && $options['break'] == 1) {
					break;
				}
				else {
					continue;
				}
			}
		}

		return $result;
	}
	
	/**
	 * Login user and Retrieve session id
	 *
	 * @param string $username
	 * @param string $apiKey
	 * @return string
	 */
	public function login ($username, $apiKey = null)
	{
		if (!$this->isEnabled()) {
			return parent::login($username, $apiKey);
		}
		$this->_log
			->setPath('login')
			->setArguments(array('username' => $username));

		if (empty($username) || empty($apiKey)) {
			return $this->_fault('invalid_request_param');
		}

		try {
			$this->_startSession();
			$this->_getSession()->login($username, $apiKey);

			$this->_log
				->setStatus(Grommet_ApiLog_Model_Event::STATUS_SUCCESS)
				->setUser($username)
				->setUserId($this->_getSession()->getUser()->getId())
				->save();
		} catch (Exception $e) {
			return $this->_fault('access_denied');
		}
		return $this->_getSession()->getSessionId();
	}

	/**
	 * End web service session
	 *
	 * @param string $sessionId
	 * @return boolean
	 */
	public function endSession ($sessionId)
	{
		$this->_startSession($sessionId);
		$this->_getSession()->clear();
		
		if ($this->isEnabled()) {
			$this->_log
				->setPath('endSession')
				->setStatus(Grommet_ApiLog_Model_Event::STATUS_SUCCESS)
				->save();			
		}
		return true;
	}

	/**
	 * Dispatch webservice fault
	 *
	 * @param string $faultName
	 * @param string $resourceName
	 * @param string $customMessage
	 */
	protected function _fault ($faultName, $resourceName = null, $customMessage = null)
	{
		$faults = $this->_getConfig()->getFaults($resourceName);
		if (!isset($faults[$faultName]) && !is_null($resourceName)) {
			$this->_fault($faultName);
			return;
		}
		elseif (!isset($faults[$faultName])) {
			$this->_fault('unknown');
			return;
		}
		$code = $faults[$faultName]['code'];
		$message = is_null($customMessage) ? $faults[$faultName]['message'] : $customMessage;
		
		if ($this->isEnabled()) {
			$this->_log
				->setStatus(Grommet_ApiLog_Model_Event::STATUS_FAULT)
				->setFaultCode($code)
				->setFaultMessage($message)
				->save();			
		}
		$this->_getServer()->getAdapter()->fault($code, $message);
	}

	/**
	 * @return bool
	 */
	public function isEnabled ()
	{
		return Mage::helper('apilog')->isEnabled();
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
		return Mage::helper('apilog')->getConfig($path, $storeId);
	}
}
