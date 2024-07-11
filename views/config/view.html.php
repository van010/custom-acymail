<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;
jimport( 'joomla.application.component.view' );

if(!class_exists('ttView')) require(JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'ttview.php');
require_once JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'config.php';


class tradingtechnologiesViewConfig extends ttView {

	function display($tpl = null) {

		$this->form = $this->get('Form');

		JHtml::stylesheet( 'administrator/components/com_tradingtechnologies/assets/css/tradingtechnologies.css' );

		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Set the toolbar
		$this->addToolBar();

		$config = new ttConfig();

		parent::display($tpl);

	}
 
		/**
		 * Setting the toolbar
		 */
		protected function addToolBar() {
				JToolBarHelper::title(JText::_('COM_TT_CONFIG'), 'tradingtechnologies.png');
				JToolBarHelper::apply('config.apply');
				JToolBarHelper::save('config.save');
				JToolBarHelper::divider();
				JToolBarHelper::cancel('config.cancel');
				JToolBarHelper::divider();
				JToolBarHelper::preferences('com_tradingtechnologies');
		}

}