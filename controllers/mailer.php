<?php

/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

use AcyMailing\Classes\UserClass;

class tradingtechnologiesControllerMailer extends JControllerForm
{

    public function __construct()
    {
        $this->view_list = 'users';
        parent::__construct();
    }

    public function save_template(){
        $model = self::getModel('mails');
        $model->save($_POST) ? JFactory::getApplication()->enqueueMessage('Mail template set') : JFactory::getApplication()->enqueueMessage('Something went wrong'); 
        $this->setRedirect( 'index.php?option=com_tradingtechnologies&view=mail');
    }
}
