<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die;


class tradingtechnologiesTableInstruments extends JTable {

	function __construct($db) {
		parent::__construct( '#__tt_instruments' , 'id' , $db );
	}

}
