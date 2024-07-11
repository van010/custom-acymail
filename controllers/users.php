<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

class tradingtechnologiesControllerUsers extends JControllerForm {

    public function __construct() {
        $this->view_list = 'users';
        parent::__construct();
    }

    public function update_users_mail_list(){

		/*$app = Factory::getApplication();
		$input = $app->input;
		dd($input);*/
        $model = self::getModel('users');
        $model->save($_POST);
        JFactory::getApplication()->enqueueMessage('users mailing list updated');
        $this->setRedirect( 'index.php?option=com_tradingtechnologies&view=users');
    }


}
