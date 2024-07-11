<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die;

class tradingtechnologiesTableTransactions extends JTable {

	public $id;
	public $portfolio_id;
	public $customer_id;
	public $invest;
	public $payout;
	public $created;

	public function __construct($db) {

		parent::__construct('#__tt_transactions', 'id', $db);

	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetName() {

		$k = $this->_tbl_key;
		return 'com_tradingtechnologies.transactions.'.(int) $this->$k;

	}

	/**
	 * Overloaded bind function
	 *
	 * @param	array		$hash named array
	 *
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see		JTable:bind
	 * @since	1.5
	 */
	public function bind($array, $ignore = '') {

		/*if (isset($array['attribs']) && is_array($array['attribs'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['attribs']);
			$array['attribs'] = (string)$registry;
		}*/

		return parent::bind($array, $ignore);

	}

	/**
	 * Overloaded check function
	 *
	 * @return	boolean
	 * @see		JTable::check
	 * @since	1.5
	 */
	public function check() {

		return true;

	}

	/**
	 * Overriden JTable::store to set modified data and user id.
	 *
	 * @param	boolean	True to update fields even if they are null.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function store($updateNulls = false) {

		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		$this->created		= $date->toSQL();


		$return = parent::store($updateNulls);

		return $return;

	}

}
