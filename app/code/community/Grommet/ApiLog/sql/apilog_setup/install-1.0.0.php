<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

/**
 * Create table 'apilog/event'
 */
$table = $installer->getConnection()
	->newTable($installer->getTable('apilog/event'))
	->addColumn('event_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity' => true,
		'nullable' => false,
		'primary' => true,
		), 'Event Id')
	->addColumn('adapter', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(
		), 'API Adapter')
	->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'Resource Path')
	->addColumn('model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'Model Class Name')
	->addColumn('arguments', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
		), 'Arguments')
	->addColumn('response', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
		), 'Response')
	->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
		), 'Status')
	->addColumn('fault_code', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
		), 'Fault Code')
	->addColumn('fault_message', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
		), 'Fault Message')
	->addColumn('user', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(
		), 'User name')
	->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		), 'User Id')
	->addColumn('time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Event date')
	->addIndex($installer->getIdxName('apilog/event', array('user_id')), array('user_id'))
	->addIndex($installer->getIdxName('apilog/event', array('user')), array('user'))
	->addForeignKey(
		$installer->getFkName('apilog/event', 'user_id', 'api/user', 'user_id'),
		'user_id',
		$installer->getTable('api/user'),
		'user_id',
		Varien_Db_Ddl_Table::ACTION_SET_NULL,
		Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->setComment('Api Log Event');

$installer->getConnection()->createTable($table);
