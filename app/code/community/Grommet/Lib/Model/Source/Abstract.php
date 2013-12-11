<?php
/**
 * Abstract Source model
 *
 * @author tmannherz
 */
abstract class Grommet_Lib_Model_Source_Abstract extends Varien_Object
{
	/**
	 * @param string $source
	 * @param bool $showBlank
	 * @return array
	 */
	public function toOptionArray ($source, $showBlank = false)
	{
		$method = 'get' . $this->_camelize($source);
		if (method_exists($this, $method)) {
			$opts = $this->$method($showBlank);
			if ($showBlank) {
				$new = array('' => '');
				foreach ($opts as $id => $val) {
					$new[$id] = $val;
				}
				return $new;
			}
			return $opts;
		}
		return array();
	}

	/**
	 * @param string $source
	 * @param bool $showBlank
	 * @return array
	 */
	public function getOptions ($source, $showBlank = true)
	{
		$opts = array();
		if ($showBlank) {
			$opts[] = array('label' => '', 'value' => '');
		}
		$items = $this->toOptionArray($source, false);
		foreach ($items as $value => $label) {
			$opts[] = array('label' => $label, 'value' => $value);
		}
		return $opts;
	}

	/**
	 * @param string $source
	 * @param mixed $value
	 * @return mixed
	 */
	public function getLabel ($source, $value)
	{
		$options = $this->toOptionArray($source, false);
		if (isset($options[$value])) {
			return $options[$value];
		}
		return null;
	}
}
