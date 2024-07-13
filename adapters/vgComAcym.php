<?php

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class vgComAcym
{
	public function __construct()
	{
		// todo
	}

	public static function loadAcymMailTemplates()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$columns = [];
		$query->select($columns)
			->from('`#__acym_mail`')
			->where();

	}


}

?>