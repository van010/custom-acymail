<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');

require_once JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'ttHelper.php';

class ttView extends JViewLegacy {

	protected $canDo;

	function __construct($config = array()) {

		parent::__construct($config);

	}

	function display($tpl = null) {

		ttJsApi::jQuery();
		ttJsApi::uikit();
		ttJsApi::css('/administrator/components/com_tradingtechnologies/assets/css/tradingtechnologies.css');
		echo ttJsApi::writeJS();
		//JHTML::_('behavior.tooltip');

		$view = JFactory::getApplication()->input->get('view', 'credentials');
		$this->addSubmenu($view);

		parent::display($tpl);

	}

	public function addSubmenu($name) {

		$submenu = JVERSION < 3 ? 'JSubMenuHelper' : 'JHtmlSidebar';

		$submenu::addEntry(
			JText::_('COM_TT_POSITIONS'),
			'index.php?option=com_tradingtechnologies&view=positions', $name == 'positions'
		);

		$submenu::addEntry(
			JText::_('COM_TT_CONFIG'),
			'index.php?option=com_tradingtechnologies&view=config', $name == 'config'
		);

		$submenu::addEntry(
			JText::_('COM_TT_CREDENTIALS'),
			'index.php?option=com_tradingtechnologies&view=credentials', $name == 'credentials'
		);

	}

}

