<?php
/**
 * Event Grid
 *
 * @author tmannherz
 */
class Grommet_ApiLog_Block_Adminhtml_Event_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected function _construct ()
	{
		parent::_construct();
		$this->setId('apilogEvents');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setDefaultSort('time');
		$this->setDefaultDir('DESC');
		//$this->setNoFilterMassactionColumn(true);
	}

	/**
	 * @return Grommet_ApiLog_Block_Adminhtml_Event_Grid
	 */
	protected function _prepareCollection ()
	{
		$collection = Mage::getResourceModel('apilog/event_collection');
		/* @var $collection Grommet_ApiLog_Model_Resource_Event_Collection */
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	/**
	 * @return Grommet_ApiLog_Block_Adminhtml_Event_Grid
	 */
	protected function _prepareColumns ()
	{
		$this->addColumn('time', array(
			'header' => $this->__('Time'),
			'index' => 'time',
			'type' => 'datetime',
			'width' => 160,
		));

		$this->addColumn('adapter', array(
			'header' => $this->__('API Adapter'),
			'index' => 'adapter',
			'type' => 'options',
			'width' => '100px',
			'options' => Mage::getSingleton('apilog/source')->toOptionArray('event_adapter')
		));

		$this->addColumn('user', array(
			'header' => $this->__('Username'),
			'index' => 'user',
			'type' => 'text',
			'escape' => true,
			'sortable' => false,
			'filter' => 'apilog/adminhtml_grid_filter_user',
			'width' => 150,
		));

		$this->addColumn('path', array(
			'header' => $this->__('API Path'),
			'index' => 'path',
			'type' => 'text'
		));

		$this->addColumn('status', array(
			'header' => $this->__('Status'),
			'index' => 'status',
			'type' => 'options',
			'width' => '100px',
			'options' => Mage::getSingleton('apilog/source')->toOptionArray('event_status')
		));

		$this->addColumn('fault_message', array(
			'header' => $this->__('Fault Message'),
			'index' => 'fault_message',
			'type' => 'text'
		));

		if (!$this->_isExport) {
			$this->addColumn('view', array(
				'header' => $this->__('Details'),
				'width' => 50,
				'type' => 'action',
				'getter' => 'getId',
				'actions' => array(array(
						'caption' => $this->__('View'),
						'url' => array(
							'base' => '*/apilog/view',
						),
						'field' => 'id'
					)),
				'filter' => false,
				'sortable' => false,
			));			
		}
		else {
			$this->addColumn('fault_code', array(
				'header' => $this->__('Fault Code'),
				'index' => 'fault_code',
				'type' => 'text'
			));
			
			$this->addColumn('arguments', array(
				'header' => $this->__('Arguments'),
				'index' => 'arguments',
				'type' => 'text'
			));
		}

		$this->addExportType('*/apilog/exportCsv', Mage::helper('apilog')->__('CSV'));
		$this->addExportType('*/apilog/exportXml', Mage::helper('apilog')->__('XML'));

		return parent::_prepareColumns();
	}

	/**
	 * @param Varien_Object $item
	 * @return string
	 */
	public function getRowUrl ($item)
	{
		return $this->getUrl('*/apilog/view', array('id' => $item->getId()));
	}

	/**
	 * @return string
	 */
	public function getGridUrl ()
	{
		return $this->getUrl('*/apilog/grid');
	}
}