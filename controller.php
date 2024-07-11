<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die;

class tradingtechnologiesController extends JControllerLegacy { 

	protected $default_view = 'positions';

	public function display($cachable = false, $urlparams = false) {

		$input = JFactory::getApplication()->input;

		$view   = $input->get('view', $this->default_view);
		$layout = $input->get('layout', 'default');

		$input->set('view', $view);

		parent::display($cachable, $urlparams);
	}
}

