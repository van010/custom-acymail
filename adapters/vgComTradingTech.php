<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

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

    /**
	 * @param $positions
	 *
	 * @return string
	 *
	 * @since version
	 */
	public static function showTableData($positions, $pageNum=0)
    {
		if (empty($positions)) {
		    return '';
		}
		$idxText = Text::_('Idx');
		$imgLoading = Uri::root(true) . '/plugins/system/vg_trading_tech/assets/images/loading.gif';
        $html = '';
	    $html .= '<table id="tt-position-lists">';
		$columnNames = array_keys($positions[0]);
		$html .= '<tr class="tt-column-names">';
        $html .= "<th class='tt-name-idx'>$idxText</th>";
        foreach ($columnNames as $columnName) {
			$colum = ucfirst(str_replace('_', ' ', $columnName));
			$html .= "<th class='tt-name-$columnName'>$colum</th>";
		}
		$html .= '</tr>';

        foreach ($positions as $key => $position) {
            $key += 1;
            $positionId = $position['id'];
            $tagName = "{positionId:$positionId}";
	        $html .= "<tr style='cursor:pointer' onclick='changePosition($tagName, jQuery(this))'>";
			$html .= "<td>$key</td>";
            foreach ($position as $item) {
	            $html .= "<td>$item</td>";
			}
	        $html .= "</tr>";
		}
		$html .= '</table>';
		// $html .= '<div class="table-overlay"></div>';
		$html .= '<div id="vg-trading-executing">';
		$html .= "<span><img src='$imgLoading'/></span>";
		$html .= '</div>';
		return $html;
	}

    public static function handlePagination($res, $pageNum)
    {
		require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/helper.php';
		$plgParams = json_decode(vgTradingTechHelper::getPlgTradingAttrs()->params);
        $limitPositions = $plgParams->limit_positions;
		$db = Factory::getDbo();
        $query = $db->getQuery(true);
        $columns = vgTradingTechHelper::getTradingPositionAttrs();
		$query->select($columns)
			->from('`#__tt_positions`')
			->order('date DESC')
			->setLimit($limitPositions, $pageNum * $limitPositions);
		$db->setQuery($query);
	    $res['code'] = 200;
	    $res['message'] = sprintf('Loading trading position at page %s success!', $pageNum);
		$res['success'] = true;
		$res['data']['html'] = self::showTableData($db->loadAssocList(), $pageNum);
		return $res;
    }

    public static function searchPosition($key)
    {
	    return 1;
	}

	public static function loadPositions($countTotal=false, $loadMore=true)
	{
        $start = 0;
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