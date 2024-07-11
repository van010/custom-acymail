<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

class tradingtechnologiesControllerPositions extends JControllerForm {

	public function __construct() {
        $this->view_list = 'positions';
        parent::__construct();
    }

	public function getPositions() {

		ttHelper::getPositions();

		$this->setRedirect( 'index.php?option=com_tradingtechnologies&view=positions', $msg );
	}
	
	public function getAccounts() {

		ttHelper::getAccounts();

		$this->setRedirect( 'index.php?option=com_tradingtechnologies&view=positions', $msg );
	}
	
	public function getInstruments() {

		ttHelper::getInstruments();

		$this->setRedirect( 'index.php?option=com_tradingtechnologies&view=positions', $msg );
	}


}