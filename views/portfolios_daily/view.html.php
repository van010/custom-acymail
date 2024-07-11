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

class tradingtechnologiesViewPortfolios_daily extends ttView {

	protected $items;
	protected $pagination;
	protected $status;

	public function display($tpl = null) {

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

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

		JToolBarHelper::title(JText::_('COM_TT_PORTFOLIOS_DAILY'), 'tradingtechnologies.png');
		//JToolBarHelper::custom('portfolios.getPortfoliosDaily', 'refresh', 'refresh', JText::_('COM_TT_PORTFOLIOS_DAILY_UPDATE'), false);

	}
}
