<?php

/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;

require_once JPATH_TT_ADMINISTRATOR . DS . 'helpers' . DS . 'ttHelper.php';
if (!class_exists('tradingtechnologiesTablePositions')) require(JPATH_TT_ADMINISTRATOR . DS . 'tables' . DS . 'positions.php');

jimport('joomla.application.component.modellist');

class tradingtechnologiesModelPositions extends JModelList
{

	public function __construct($config = array())
	{

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'p.accountId', 'a.name', 'i.name', 'p.date', 'p.pnl', 'i.productName',
			);
		}

		parent::__construct($config);
	}

	public function getTable($type = 'positions', $prefix = 'tradingtechnologiesTable', $config = array())
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
	protected function populateState($ordering = null, $direction = null)
	{

		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JFactory::getApplication()->input->get('layout')) {
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$account = $this->getUserStateFromRequest($this->context . '.filter.account', 'filter_account');
		$this->setState('filter.account', $account);

		// List state information.
		parent::populateState('p.date', 'desc');
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
	protected function getStoreId($id = '')
	{

		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.shipment_status');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query
			->select(
				array(
					'p.*',
					'a.name as accountName',
					'i.name as instrumentName',
					'i.productName as productName'
				)
			)
			->from($db->quoteName('#__tt_positions', 'p'))
			->join('LEFT', $db->quoteName('#__tt_accounts', 'a') . ' ON ' . $db->quoteName('p.accountId') . ' = ' . $db->quoteName('a.id'))
			->join('LEFT', $db->quoteName('#__tt_instruments', 'i') . ' ON ' . $db->quoteName('p.instrumentId') . ' = ' . $db->quoteName('i.id'));

		// Filter by search in orderNumber.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where($db->quoteName('a.name') . ' LIKE ' . $search . ' OR ' . $db->quoteName('i.name') . ' LIKE ' . $search);
		}

		// Filter by search in orderNumber.
		$account = $this->getState('filter.account');
		if (!empty($account)) {
			$query->where($db->quoteName('a.name') . ' = ' . $db->Quote($account));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'p.date');
		$orderDirn	= $this->state->get('list.direction', 'desc');

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
	public function getItems()
	{

		$items	= parent::getItems();

		return $items;
	}

	public function getPositionByDateAccountInstrument($date, $accountId, $instrumentId)
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				array(
					'p.*'
				)
			)
			->from($db->quoteName('#__tt_positions', 'p'));

		$query->where($db->quoteName('p.date') . ' = "' . $date . '"');
		$query->where($db->quoteName('p.accountId') . ' = "' . $accountId . '"');
		$query->where($db->quoteName('p.instrumentId') . ' = "' . $instrumentId . '"');

		$db->setQuery($query);

		return $db->loadObject();
	}

	public function getPositionInstruments()
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select(
				array(
					'p.instrumentId',
					'i.name'
				)
			)
			->from($db->quoteName('#__tt_positions', 'p'))
			->join('LEFT', $db->quoteName('#__tt_instruments', 'i') . ' ON ' . $db->quoteName('p.instrumentId') . ' = ' . $db->quoteName('i.id'));

		$query->group($db->quoteName('instrumentId'));

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function isPositionAlreadyOpened($data)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(array('p.*'))->from($db->quoteName('#__tt_positions', 'p'));
		$query->where($db->quoteName('p.date') . ' = "' . $data['date'] . '" - INTERVAL 1 DAY');
		$query->where($db->quoteName('p.accountId') . ' = "' . $data['accountId'] . '"');
		$query->where($db->quoteName('p.instrumentId') . ' = "' . $data['instrumentId'] . '"');

		$db->setQuery($query);

		$old_record = $db->loadObject();

		// now jugar starts from here
		if ($old_record) {
			// ab dekho kia ye close tha agar nahi hai iska matlab ye close hi nahi hua hai ye kal se open hai
			if ($old_record->closed == 0 && $old_record->open == 1) {
				// to chalo phir ham isse yahan open kardenge
				return $old_record->id;
			} else {
				return false;
			}
		}
	}

	public function save($data)
	{

		$date = new Date();
		$data['date'] = $date->format('Y-m-d');


		if (empty($data['id']) && $exist = $this->getPositionByDateAccountInstrument($data['date'], $data['accountId'], $data['instrumentId'])) {
			$data['id'] = $exist->id;



			// send opening mails
			if($data['netPosition'] != 0 && $exist->open == 0){
				$data['open'] = 1;
				if(ttHelper::is_allowed($data['accountId'])){
					ttHelper::sendNotificationMail("New openings", $data);
				}
			}

			// send closing mails
			if($data['netPosition'] == 0 && $exist->open == 1 && $exist->closed == 0){
				$data['closed'] = 1;
				if(ttHelper::is_allowed($data['accountId'])){
					ttHelper::sendNotificationMail("close", (array)$exist);
					// ttHelper::sendNotificationMail("close", $data);
				}

			}
		} else {
			// when new data insert this block will run
			$is_open = $this->isPositionAlreadyOpened($data);
			if ($is_open != false) {
				$data['open'] = 1;
				$data['parent'] = $is_open;
			} else {
				if (ttHelper::is_allowed($data['accountId']) && $data['netPosition'] != 0) {
					$data['open'] = 1;
					ttHelper::sendNotificationMail("New openings", $data);
				}
			}
		}


		$data['portfolio_id'] = 1;

		$row = $this->getTable();


		$row->bind($data);
		$row->check($data);


		if ($row->store()) {
			return true;
		} else {
			$this->setError($row->getError());
			ttdebug('error', $row->getError());
			return false;
		}
	}
}
