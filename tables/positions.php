<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die;

class tradingtechnologiesTablePositions extends JTable {

	public $id;
	public $portfolio_id;
	public $accountId;
	public $uniqueId;
	public $avgBuy;
	public $avgSell;
	public $buyFillQty;
	public $buyWorkingQty;
	public $instrumentId;
	public $netPosition;
	public $openAvgPrice;
	public $pnl;
	public $pnlPrice;
	public $pnlPriceType;
	public $realizedPnl;
	public $sellFillQty;
	public $sellWorkingQty;
	public $sodNetPos;
	public $sodPriceType;
	public $date;
	public $modified;

	public function __construct($db) {

		parent::__construct('#__tt_positions', 'id', $db);

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
		return 'com_tradingtechnologies.positions.'.(int) $this->$k;

	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetTitle() {

		return $this->title;

	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 * @since	1.6
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null) {

		return parent::_getAssetParentId($table, $id);

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

		$this->modified		= $date->toSQL();
/*
		$this->uniqueId		= $this->accountId .'-'. $this->instrumentId;
		$this->id			= $this->accountId .'-'. $this->instrumentId;

		if ($this->id) {
			// Existing item
			$this->modified		= $date->toSQL();
			$this->modified_by	= $user->get('id');
		} else {
			// New item. An video created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->created)) {
				$this->created = $date->toSQL();
			}

			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}
*/

		$return = parent::store($updateNulls);

		return $return;

	}

}
