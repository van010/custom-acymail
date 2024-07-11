<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

class tradingtechnologiesControllerCredentials extends JControllerForm {

    public function __construct() {
        $this->view_list = 'credentials';
        parent::__construct();
    }

	public function refreshToken() {

		ttHelper::getNewToken();

		$this->setRedirect( 'index.php?option=com_tradingtechnologies&view=credentials', $msg );

	}

}
