<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

class tradingtechnologiesControllerConfig extends JControllerForm {

    public function __construct() {
        $this->view_list = 'config';
        parent::__construct();
    }

}
