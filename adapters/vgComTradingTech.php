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

	public static function loadPositions($countTotal=false, $loadMore=true)
	{
        $start = 20;
        $plgParams = json_decode(vgTradingTechHelper::getPlgTradingAttrs()->params);
        $limitPositions = $plgParams->limit_positions;

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        // $columns = ['`id`', '`portfolio_id`', '`accountId`', '`open`', '`closed`', '`openAvgPrice`', '`pnlPrice`', '`pnlPriceType`', '`date`'];
        $columns = vgTradingTechHelper::getTradingPositionAttrs();
		if ($countTotal) {
		    $query->select('COUNT("id")');
		} else {
			$query->select($columns);
		}
		$query->from(self::$tbl_tt_positions)
			->order('date DESC');
		if (!$countTotal) {
			$query->setLimit($limitPositions, $start);
		}
        $db->setQuery($query);

		if ($countTotal) {
		    return $db->loadResult();
		}
        return $db->loadAssoclist();
	}
}

?>