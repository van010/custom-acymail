<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

require_once JPATH_TT_ADMINISTRATOR.DS.'helpers'.DS.'ttHelper.php';
if(!class_exists('tradingtechnologiesTableCredentials'))require(JPATH_TT_ADMINISTRATOR.DS.'tables'.DS.'credentials.php');

jimport( 'joomla.application.component.modeladmin');

class tradingtechnologiesModelCredentials extends JModelList {

	function __construct() {

		$this->db = JFactory::getDbo();

		parent::__construct();
	}

	public function getTable($type = 'credentials', $prefix = 'tradingtechnologiesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getToken() {

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName(array('params','modified')))
			->from($this->db->quoteName('#__tt_credentials'))
			->where($this->db->quoteName('title') . ' = "token"');
		$this->db->setQuery($query);

		if ($result = $this->db->loadObject()) {
			$result->params = json_decode($result->params);
			$token = new StdClass;
			if (isset($result->params->access_token)) {
				$token->token = $result->params->access_token;
				$token->exp = strtotime($result->modified) + $result->params->seconds_until_expiry - 3600;
				return $token;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function getAccount() {

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('params'))
			->from($this->db->quoteName('#__tt_credentials'))
			->where($this->db->quoteName('title') . ' = "user"');
		$this->db->setQuery($query);

		if ($result = $this->db->loadResult()) {
			$result = json_decode($result);

			return $result->data;
		} else {
			return false;
		}
	}

	public function save($data) {

		$row = $this->getTable();

		$row->id = $data['name'] == 'token' ? 1 : 2;
		$row->title = $data['name'];
		$row->modified = JFactory::getDate()->toSQL();
		$row->params = json_encode($data['params']);

		if ($row->store()) {
			return true;
		}
		else {
			$this->setError($row->getError());
			return false;
		}
	}

}
?>