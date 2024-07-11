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

class tradingtechnologiesModelUsers extends JModelList
{


    public function add_users_to_subscribers_list()
    {

        $db = JFactory::getDbo();
        //truncate the acymailing subscriber table then add users
        $db->setQuery('select * from #__users join #__tt_mail_list on #__tt_mail_list.user_id = #__users.id where send_mail = 1');
        $db->execute();
        $result = $db->loadObjectList();

        foreach ($result as $row) {
            $user = new stdClass();
            $user->email = $row->email;
            $user->name = $row->name;
            $user->confirmed = 1;

            $userClass = new UserClass();
            $userClass->sendConf = false; // Or false if you don't want a confirmation email to be sent
            $userClass->save($user);
        }

    }


    public function save($records)
    {

        $db = JFactory::getDbo();
        //truncate the acymailing subscriber table then add users
        //$db->setQuery('delete from #__acym_user');
        //$db->execute();


        foreach ($records as $user_id => $record) {
            $db->setQuery("REPLACE INTO #__tt_mail_list VALUES ($record,$user_id)");
            $db->execute();
        }

        //$this->add_users_to_subscribers_list();
    }
}
