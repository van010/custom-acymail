<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

class tradingtechnologiesControllerTraders extends JControllerForm {

    public function __construct() {
        parent::__construct();
    }

    public function update_accounts_mail_list(){
        $model = self::getModel('traders');
        $model->save($_POST);
        JFactory::getApplication()->enqueueMessage('accounts mailing list updated');
        $this->setRedirect( 'index.php?option=com_tradingtechnologies&view=traders');
    }


}
