<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

if(!class_exists('ttView'))require(JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'ttview.php');
if(!class_exists('ttHelper'))require(JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'ttHelper.php');

class tradingtechnologiesViewPositions extends ttView {

	protected $items;
	protected $positions;
	protected $pagination;
	protected $status;
	protected $accounts;

	public function display($tpl = null) {

		$this->positions	= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		$accountaModel = ttHelper::getModel('accounts');
		$this->accounts = $accountaModel->getItems();

		JHtml::stylesheet( 'administrator/components/com_tradingtechnologies/assets/css/tradingtechnologies.css' );

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		// Set the toolbar
		$this->addToolBar();

		parent::display($tpl);

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar() {

		JToolBarHelper::title(JText::_('COM_TT_POSITIONS'), 'tradingtechnologies.png');
		JToolBarHelper::custom('positions.getPositions', 'refresh', 'refresh', JText::_('COM_TT_POSITIONS_UPDATE'), false);
		JToolBarHelper::custom('positions.getAccounts', 'refresh', 'refresh', JText::_('COM_TT_ACCOUNTS_UPDATE'), false);
		JToolBarHelper::custom('positions.getInstruments', 'refresh', 'refresh', JText::_('COM_TT_INSTRUMENTS_UPDATE'), false);

	}
}
