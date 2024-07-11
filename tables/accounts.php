<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;


class tradingtechnologiesTableAccounts extends JTable {

	function __construct($db) {
		parent::__construct( '#__tt_accounts' , 'id' , $db );
	}

}
