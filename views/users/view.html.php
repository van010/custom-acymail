<?php

/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

if (!class_exists('ttView')) require(JPATH_TT_ADMINISTRATOR . DS . 'helpers' . DS . 'ttview.php');
if (!class_exists('ttHelper')) require(JPATH_TT_ADMINISTRATOR . DS . 'helpers' . DS . 'ttHelper.php');

class tradingtechnologiesViewUsers extends ttView
{


	public function display($tpl = null)
	{


		parent::display($tpl);
	}
}
