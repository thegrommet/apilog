<?php
/**
 * API Log Event Controller
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Adminhtml_ApiLogController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Additional initialization
	 */
	protected function _construct ()
	{
		$this->setUsedModuleName('Grommet_ApiLog');
	}

	/**
	 * Initializes the model for use.
	 *
	 * @param string $idField
	 * @return bool|Grommet_ApiLog_Model_Event
	 */
	protected function _initModel ($idField = 'id')
	{
		$id = (int)$this->getRequest()->getParam($idField);
		$model = Mage::getModel('apilog/event');
		if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->getSession()->addError(Mage::helper('apilog')->__('The event you are trying to access no longer exists.'));
				$this->_redirect('*/*');
				return false;
			}
		}
		Mage::register('current_event', $model);
		return $model;
	}

	/**
	 * Grid
	 */
	public function indexAction ()
	{
		$this->_title($this->__('System'))
			->_title($this->__('API Event Log'));

		$this->loadLayout();
		$this->_setActiveMenu('system/apilog');
		$this->renderLayout();
	}

	/**
	 * AJAX grid
	 */
	public function gridAction ()
	{
		$this->loadLayout(false);
		$this->renderLayout();
	}

	/**
	 * View event details
	 */
	public function viewAction ()
	{
		if ($this->_initModel()) {
			$this->_title($this->__('System'))
				->_title($this->__('API Event Log'))
				->_title($this->__('View Event'));

			$this->loadLayout();
			$this->_setActiveMenu('system/apilog');
			$this->renderLayout();
		}
	}

	/**
	 * Export log to CSV
	 */
	public function exportCsvAction ()
	{
		$this->_prepareDownloadResponse('apilog.csv', $this->getLayout()->createBlock('apilog/adminhtml_event_grid')->getCsvFile());
	}

	/**
	 * Export log to XML
	 */
	public function exportXmlAction ()
	{
		$this->_prepareDownloadResponse('apilog.xml', $this->getLayout()->createBlock('apilog/adminhtml_event_grid')->getExcelFile());
	}

	/**
	 * ACL check
	 *
	 * @return bool
	 */
	protected function _isAllowed ()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/system/apilog');
	}
}