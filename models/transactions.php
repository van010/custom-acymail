<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;

require_once JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'ttHelper.php';
if(!class_exists('tradingtechnologiesTabletransactions'))require(JPATH_TT_ADMINISTRATOR.DS.'tables'.DS.'transactions.php');

jimport('joomla.application.component.modellist');

class tradingtechnologiesModelTransactions extends JModelList {

	public function __construct($config = array()) {

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'customar_id', 't.customar_id',
			);
		}

		parent::__construct($config);

	}

	public function getTable($type = 'transactions', $prefix = 'tradingtechnologiesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {

		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JFactory::getApplication()->input->get('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$transferred = $this->getUserStateFromRequest($this->context.'.filter.transferred', 'filter_transferred');
		$this->setState('filter.transferred', $transferred);

		// List state information.
		parent::populateState('t.created', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '') {

		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.customer_id');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query
			->select(array(
						't.*, u.name as customer_name'
						)
					)
			->from($db->quoteName('#__tt_transactions', 't'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('t.customer_id'));
			//->join('LEFT', $db->quoteName('#__tt_instrument', 'i') . ' ON ' . $db->quoteName('o.order_number') . ' = ' . $db->quoteName('f.orderNumber'));

		// Filter by search in orderNumber.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where($db->quoteName('u.name') . ' LIKE ' . $search);
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 't.created');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;

	}

	/**
	 * Method to get a list of fulfillments.
	 * Overridden to add a check for access levels.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6.1
	 */
	public function getItems() {

		$items	= parent::getItems();

		return $items;
	}

	/*
	public function getTransactions($date, $accountId, $instrumentId) {

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select(array(
					'p.*'
					)
				)
			->from($db->quoteName('#__tt_positions', 'p'));

		$query->where($db->quoteName('p.date') .' = "'. $date .'"');
		$query->where($db->quoteName('p.accountId') .' = "'. $accountId .'"');
		$query->where($db->quoteName('p.instrumentId') .' = "'. $instrumentId .'"');

		$db->setQuery($query);

		return $db->loadObject();
	}
	*/

	/**
	 * Method to get a list of fulfillments.
	 * Overridden to add a check for access levels.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6.1
	 */
/*
	public function getFulfillment($orderNumber, $vmData = false) {

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if ($vmData) {
			$query
				->select(array(
						'o.virtuemart_order_id', 
						'o.order_number', 
						'o.order_status', 
						'os.order_status_name',
						'f.*'
						)
					)
				->from($db->quoteName('#__virtuemart_orders', 'o'))
				->join('LEFT', $db->quoteName('#__virtuemart_orderstates', 'os') . ' ON ' . $db->quoteName('o.order_status') . ' = ' . $db->quoteName('os.order_status_code'))
				->join('LEFT', $db->quoteName('#__tt_positions', 'f') . ' ON ' . $db->quoteName('o.order_number') . ' = ' . $db->quoteName('f.orderNumber'));
		} else {
			$query
				->select(array(
						'f.*'
						)
					)
				->from($db->quoteName('#__tt_positions', 'f'));
		}
		$query->where($db->quoteName('f.orderNumber') .' = "'. $orderNumber .'"');

		$db->setQuery($query);

		return $db->loadObject();
	}

	public function getFulfillmentByVmOrderId($virtuemart_order_id) {

		$q  = 'SELECT f.*, o.virtuemart_order_id FROM `#__tt_positions` f ';
		$q .= 'LEFT JOIN `#__virtuemart_orders` o on o.`order_number` = f.`orderNumber` ';
		$q .= 'WHERE o.`virtuemart_order_id` = "'. $virtuemart_order_id .'"';
		$db = JFactory::getDbo();
		$db->setQuery($q);

		return $db->loadObject();
	}

	public function getOrders() {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query
			->select(array(
						'o.virtuemart_order_id', 
						'o.order_number', 
						'o.order_status', 
						'os.order_status_name', 
						'f.fulfillment_id', 
						'f.fulfillment_status', 
						'f.carrier', 
						'f.carrierTrackingNumber', 
						'f.carrierTrackingURL'
						)
					)
			->from($db->quoteName('#__virtuemart_orders', 'o'))
			->join('LEFT', $db->quoteName('#__virtuemart_orderstates', 'os') . ' ON ' . $db->quoteName('o.order_status') . ' = ' . $db->quoteName('os.order_status_code'))
			->join('LEFT', $db->quoteName('#__tt_positions', 'f') . ' ON ' . $db->quoteName('o.order_number') . ' = ' . $db->quoteName('f.orderNumber'));

		// Filter by search in orderNumber.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where($db->quoteName('o.order_number') . ' LIKE ' . $search);
		}

		// Filter by transferred shipment
		$transferred = $this->getState('filter.transferred');
		if ($transferred == 1) {
			$query->where($db->quoteName('f.fulfillment_status') . ' = "" ');
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'o.virtuemart_order_id');
		$orderDirn	= $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function save($data) {

		$date = new Date();
		$data['date'] = $date->format(Text::_('DATE_FORMAT_LC4'));

		if (empty($data['id']) && $exist = $this->getPositionByDateAccountInstrument($data['date'], $data['accountId'], $data['instrumentId'])) {
			$data['id'] = $exist->id;
		}
		
		$data['portfolio_id'] = 1;

		$row = $this->getTable();

		$row->bind($data);
		$row->check($data);

		if ($row->store()) {
			return true;
		}
		else {
			$this->setError($row->getError());
			return false;
		}
	}

	*/
}
