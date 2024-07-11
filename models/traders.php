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

class tradingtechnologiesModelTraders extends JModelList
{




    public function save($records)
    {

        $db = JFactory::getDbo();

        foreach ($records as $account_id => $is_allowed) {
            $db->setQuery("UPDATE #__tt_accounts SET send_mail = $is_allowed where id = $account_id");
            $db->execute();
        }

    }
}
