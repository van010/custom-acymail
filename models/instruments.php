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
if(!class_exists('tradingtechnologiesTableInstruments'))require(JPATH_TT_ADMINISTRATOR.DS.'tables'.DS.'instruments.php');

jimport('joomla.application.component.modellist');

class tradingtechnologiesModelInstruments extends JModelList {

	public function __construct($config = array()) {

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id'
			);
		}

		parent::__construct($config);

	}

	public function getTable($type = 'instruments', $prefix = 'tradingtechnologiesTable', $config = array())
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

		//$transferred = $this->getUserStateFromRequest($this->context.'.filter.transferred', 'filter_transferred');
		//$this->setState('filter.transferred', $transferred);

		// List state information.
		parent::populateState('i.name', 'asc');
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
		$id	.= ':'.$this->getState('filter.id');

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
						'i.*'
						)
					)
			->from($db->quoteName('#__tt_instruments', 'i'));
			//->join('LEFT', $db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('t.customer_id'));
			//->join('LEFT', $db->quoteName('#__tt_instrument', 'i') . ' ON ' . $db->quoteName('o.order_number') . ' = ' . $db->quoteName('f.orderNumber'));

		// Filter by search in 
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$query->where($db->quoteName('i.name') . ' LIKE ' . $search);
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'i.name');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;

	}

	public function getItems() {

		$items	= parent::getItems();

		return $items;
	}

	public function getInstrumentById($instrumentId) {

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select(array(
					'i.*'
					)
				)
			->from($db->quoteName('#__tt_instruments', 'i'));

		$query->where($db->quoteName('i.id') .' = "'. $instrumentId .'"');

		$db->setQuery($query);

		return $db->loadObject();
	}
	
	public function insertInstrumentId($instrumentId) {

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Insert columns.
		$columns = array('id');

		// Insert values.
		$values = array($instrumentId);

		// Prepare the insert query.
		$query
			->insert($db->quoteName('#__tt_instruments'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));

		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		$db->execute();
	}

	public function save($data) {

		$date = new Date();
		$data['modified'] = $date->toSQL();

		if (!$exist = $this->getInstrumentById($data['id'])) {
			$data['created'] = $date->toSQL();
			$this->insertInstrumentId($data['id']);
		}

		$row = $this->getTable('instruments');

		$row->bind($data);
		$row->check($data);

		if ($row->store()) {
			return true;
		}
		else {
			$this->setError($row->getError());
			ttdebug('error',$row->getError(),$data);
			return false;
		}
	}

	
}
