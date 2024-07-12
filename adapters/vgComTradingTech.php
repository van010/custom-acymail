<?php

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class vgComTradingTech
{
	private static $tbl_tt_positions = '#__tt_positions';

    public function __construct()
    {
        // todo
    }

    public static function test()
    {
        
    }

	public static function loadPositions($loadMore=true)
	{
		$db = Factory::getDbo();
		$start = 20;
		$query = $db->getQuery(true);
        // $columns = ['`id`', '`portfolio_id`', '`accountId`', '`open`', '`closed`', '`openAvgPrice`', '`pnlPrice`', '`pnlPriceType`', '`date`'];
        $columns = vgTradingTechHelper::getPlgTradingAttrs();
//		echo '<pre style="color: red">';print_r($columns);echo '</pre>';die;
		$query->select($columns)
			->from(self::$tbl_tt_positions)
			->order('date DESC')
			->setLimit(8, $start);
        $db->setQuery($query);

        $positions = $db->loadAssoclist();
        return $positions;

	}
}

?>