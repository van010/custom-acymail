<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

class vgTradingTechHelper
{
	public function __construct()
	{
		// todo
	}

	public static function getTradingPositionAttrs()
	{
		$allParams = self::getPlgTradingAttrs()->params;
		return json_decode($allParams)->select_positions;
	}

	public static function getPlgTradingAttrs()
	{
		$allAttrs = PluginHelper::getPlugin('system', 'vg_trading_tech');
		return $allAttrs;
	}

	public static function getColumnNames($tblName)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$queryColumns = "show columns from `$tblName`";
		$db->setQuery($queryColumns);
		$columns = $db->loadColumn();
		return $columns;
	}
}

?>