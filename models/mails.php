<?php

/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

use AcyMailing\Classes\UserClass;

require_once JPATH_TT_ADMINISTRATOR . DS . 'helpers' . DS . 'ttHelper.php';
if (!class_exists('tradingtechnologiesTableaccounts')) require(JPATH_TT_ADMINISTRATOR . DS . 'tables' . DS . 'accounts.php');

jimport('joomla.application.component.modellist');

class tradingtechnologiesModelMails extends JModelList
{

    public function save($payload)
    {

        if(isset($payload['template'])){
            $db = JFactory::getDbo();
            $db->setQuery("INSERT INTO #__tt_mail_template VALUES (1, {$payload['template']},{$payload['template2']}) ON DUPLICATE KEY UPDATE template_id = {$payload['template']}, template2_id = {$payload['template2']}");
            $db->execute();
            return true;
        }

        return false;

    }
}
