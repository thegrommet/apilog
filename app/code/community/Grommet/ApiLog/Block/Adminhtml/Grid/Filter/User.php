<?php
/**
 * Username Grid Filter
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Block_Adminhtml_Grid_Filter_User extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
	/**
	 * Build filter options list
	 *
	 * @return array
	 */
	public function _getOptions ()
	{
		$options = array(array('value' => '', 'label' => Mage::helper('apilog')->__('All Users')));
		foreach (Mage::getResourceModel('api/user_collection') as $user) {
			$options[] = array('value' => $user->getUsername(), 'label' => $user->getUsername());
		}
		return $options;
	}

	/**
	 * Filter condition getter
	 *
	 * @string
	 */
	public function getCondition ()
	{
		return $this->getValue();
	}
}