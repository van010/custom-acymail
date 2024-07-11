<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

require_once JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'ttHelper.php';
if(!class_exists('ttTableAccount'))require(JPATH_TT_ADMINISTRATOR.DS.'tables'.DS.'config.php');

jimport( 'joomla.application.component.modeladmin');

class tradingtechnologiesModelConfig extends JModelAdmin {

	private $context = 'com_tradingtechnologies.config';

	private $_params = false;
	
	public function getTable($type = 'config', $prefix = 'ttTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$options = array('control' => 'jform', 'load_data' => $loadData);
    	$form    = $this->loadForm('com_tradingtechnologies.config', 'config', $options);
   		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tradingtechnologies.config.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	public function getItem($pk = null) {
		if (!$this->_params) {
			$row  = $this->getTable('config');
			$row->load(1);
			$registry = new JRegistry();
			$registry->loadString($row->params);
			$this->_params = $registry->toArray();
		}
		return $this->_params;
	}

	public function save($data) {

		$row  = $this->getTable('config');
		$registry = new JRegistry();
		$registry->loadArray($data);
		
		// Set the values
		$row->id = 1;
		$row->params = $registry->toString();
		if ($row->store()) {
			$this->_params = $registry;
			return true;
		}
		else {
			$this->setError($row->getError());
			return false;
		}
	}
	
}
?>