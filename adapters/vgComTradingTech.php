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
     * @param string $task
     * @param string $name
     * @return string
     */
    public static function displayTradingPosition($task, $name)
    {
        $fieldName = $task . "_positions";
        $fieldsetId = 'jform_params_' . $fieldName;
        $textSelectAll = Text::_('PLG_VG_TRADING_TECH_SELECT_ALL_POSITION_ATTRS');
        $textSelectNone = Text::_('PLG_VG_TRADING_TECH_SELECT_NONE_POSITION_ATTRS');
        $plgTradingAttrs = vgTradingTechHelper::getPlgTradingAttrs();
        $plgTradingParams = json_decode($plgTradingAttrs->params);
        $position_column_names = vgTradingTechHelper::getColumnNames('#__tt_positions');
        $html = "<fieldset name='$name' id='$fieldsetId' class='checkboxes'>";
		$html .= "<button class='btn-attrs-all' type='button' data-for='$fieldsetId' onclick='selectAllPositionAttrs(this, 1)'>$textSelectAll</button>";
		$html .= "<button class='btn-attrs-none' type='button' data-for='$fieldsetId' onclick='selectAllPositionAttrs(this, 0)'>$textSelectNone</button><br>";
		$html .= "<legend class='visually-hidden'></legend>";

	    foreach ($position_column_names as $key => $colName)
	    {
            $checked = !empty($plgTradingParams->$fieldName) && in_array($colName, $plgTradingParams->$fieldName) ? 'checked' : '';
		    $html  .= '<div class="form-check form-check-inline">';
		    $id    = $fieldsetId. '-' . $key;
		    $name  = "jform[params][" . $fieldName . "][]";
		    $value = $colName;
			$class = 'form-check-input';

			$html .= "<input type='checkbox' class='$class' id='$id' name='$name' value='$value' $checked/>";
            $html .= "<label for='$id' class='form-check-label'>$colName</label>";
            $html .= '</div>';
			if (($key+1) % 5 == 0) {
			    $html .= '<br>';
			}
		}
		$html .= '</fieldset>';
        return $html;
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
            $tagName = json_encode($position);
            // $tagName = "{positionId:$positionId}";
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