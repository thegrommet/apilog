<?php
/**
 * Soap V2 API Handler
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Model_Api_Server_V2_Handler extends Mage_Api_Model_Server_V2_Handler
{
	use Grommet_ApiLog_Trait_Server_Handler;

	public function __construct ()
	{
		parent::__construct();
		$this->traitConstruct();
	}
}
