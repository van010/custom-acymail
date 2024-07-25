<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/helper.php';

class vgComTradingTech
{
	private static $tbl_tt_positions = '#__tt_positions';
    private static $noRecordsFound = '<p class="no-records-found">No records found!</p>';

    public function __construct()
    {
        // todo
    }

    public static function test()
    {
        
    }

	public static function displayTblUsers($name)
	{
		$allUsers = self::getUsersSendMail();
		$fieldName = 'select_users';
		$fieldsetId = 'jform_params_' . $fieldName;
		$textSelectAll = Text::_('PLG_VG_TRADING_TECH_SELECT_ALL_USERS');
        $textSelectNone = Text::_('PLG_VG_TRADING_TECH_SELECT_NONE_POSITION_ATTRS');
        $textUpdateUser = Text::_('PLG_VG_TRADING_TECH_UPDATE_USERS');
        $html = '';
		$html .= "<div class='group-btn-trading'><button class='btn-attrs-all' type='button' data-for='$fieldsetId' onclick='selectAllPositionAttrs(this, 1)'>$textSelectAll</button>";
		$html .= "<button class='btn-attrs-none' type='button' data-for='$fieldsetId' onclick='selectAllPositionAttrs(this, 0)'>$textSelectNone</button><br>";
        $html .= "<button id='update-users' type='button' onclick='new vgApiHandling().updateUsersSendMail()'>$textUpdateUser</button></div>";
		$html .= "<legend class='visually-hidden'></legend>";
		$html .= "<table id='tbl-select-users'>";
		$html .= "<tr>
					<th>Users</th>
					<th>Email</th>
					<th>Send Mail</th>
				</tr>";
		foreach ($allUsers as $user)
		{
			$userName = $user['name'];
            $userId = $user['id'];
			$email = $user['email'];
			$sendMail = $user['send_mail'];
            $checked = $sendMail == 1 ? 'checked' : '';
			$html .= "<tr></tr>";
			$html .= "<td>$userName</td>";
			$html .= "<td>$email</td>";
			$html .= "<td><input type='checkbox' id='$userId' name='$userId' $checked></td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
		return $html;
	}

    /**
     * @param string $fieldsetName
     * @return string
     */
    public static function displayUsers($fieldsetName) {
        $allUsers = self::getUsersSendMail();
		$fieldName = 'select_users_send_mail';
		$fieldsetId = 'jform_params_' . $fieldName;
		$textSelectAll = Text::_('PLG_VG_TRADING_TECH_SELECT_ALL_USERS');
        $textSelectNone = Text::_('PLG_VG_TRADING_TECH_SELECT_NONE_POSITION_ATTRS');
        $textUpdateUser = Text::_('PLG_VG_TRADING_TECH_UPDATE_USERS');
        $html = '';
		$html .= "<div class='group-btn-trading'>";
		$html .= "<button class='btn-attrs-all' type='button' data-for='$fieldsetId' onclick='selectAllPositionAttrs(this, 1)'>$textSelectAll</button>";
		$html .= "<button class='btn-attrs-none' type='button' data-for='$fieldsetId' onclick='selectAllPositionAttrs(this, 0)'>$textSelectNone</button><br>";
        $html .= "</div>";
		$html .= "<legend class='visually-hidden'></legend>";
        $html .= "<fieldset name='$fieldsetName' id='$fieldsetId' class='checkboxes'>";
        foreach ($allUsers as $user) {
            $userName = $user['name'];
            $userId = $user['id'];
			$email = $user['email'];
            $id = "user_$userId";
			$sendMail = $user['send_mail'];
            $checked = $sendMail == 1 ? 'checked' : '';
            $name_ = "jform[params][" . $fieldName . "][]";
            $html  .= '<div class="form-check form-check-inline">';
            $html .= "<input class='form-check-input' type='checkbox' id='$id' name='$name_' value='$userId' $checked>";
            $html .= "<label for='$id' class='form-check-label'><span class='label-user-name'>$userName</span>|<span class='label-user-email'>$email</span></label>";
            $html .= "</div>";
            $html .= "<br>";
        }
        $html .= "</fieldset>";
        return $html;
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
        $position_column_names = vgTradingTechHelper::getTradingPositionsColumnNames(true);
		$instrument_column_names = vgTradingTechHelper::getInstrumentColumnNames(true);
		$allColumns = array_merge($position_column_names, $instrument_column_names);
        $html = "<fieldset name='$name' id='$fieldsetId' class='checkboxes'>";
		$html .= "<button class='btn-attrs-all' type='button' data-for='$fieldsetId' onclick='selectAllPositionAttrs(this, 1)'>$textSelectAll</button>";
		$html .= "<button class='btn-attrs-none' type='button' data-for='$fieldsetId' onclick='selectAllPositionAttrs(this, 0)'>$textSelectNone</button><br>";
		$html .= "<legend class='visually-hidden'></legend>";

	    foreach ($allColumns as $key => $colName)
	    {
            $checked = !empty($plgTradingParams->$fieldName) && in_array($colName, $plgTradingParams->$fieldName) ? 'checked' : '';
		    $id    = $fieldsetId. '-' . $key;
		    $name  = "jform[params][" . $fieldName . "][]";
		    $value = $colName;
			$class = 'form-check-input';

            $html  .= '<div class="form-check form-check-inline">';
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
	public static function showTableData($positions, $allPositionsData, $pageNum=0)
    {
		if (empty($positions)) {
		    return self::$noRecordsFound;
		}
		$idxText = Text::_('Idx');
        $columnNames = array_keys($positions[0]);
        $imgLoading = Uri::root(true) . '/plugins/system/vg_trading_tech/assets/images/loading.gif';

        $insertPositionBy = vgTradingTechHelper::getParams('insert_position_by');
		/*$sampleData = json_encode(self::mappingPositionsKey($positions[0]));
		echo "<script type='text/javascript'>var mapKeys1 = '$sampleData'</script>";*/

        $html = '';
	    $html .= '<table id="tt-position-lists">';
		$html .= '<tr class="tt-column-names">';
        $html .= "<th class='tt-name-idx'>$idxText</th>";
        foreach ($columnNames as $columnName) {
			$colum = ucfirst(str_replace('_', ' ', $columnName));
			$html .= "<th class='tt-name-$columnName'>$colum</th>";
		}
		$html .= '</tr>';
        foreach ($positions as $key => $position) {
            $idx = $key + 1;
            $tagName = json_encode($position);
            $mapKeys = json_encode(self::mappingPositionsKey($position));
            $allDataMapKeys = json_encode(self::mappingPositionsKey($allPositionsData[$key]));
			// echo '<pre style="color: red">';print_r($mappingKeys);echo '</pre>';
            // $tagName = "{positionId:$positionId}";
	        $html .= "<tr style='cursor:pointer' onclick='changePosition($tagName, $mapKeys, $allDataMapKeys, \"$insertPositionBy\", jQuery(this))'>";
			$html .= "<td>$idx</td>";
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

    /**
     * @param int $allPositions
     *
     * @return string
     *
     * @since version
     */
	public static function paginationTradingPositions($allPositions)
	{
		// vgComTradingTech::loadPositions(true)
		$pageLimit = vgTradingTechHelper::getParams('limit_positions');
        $totalPositions = round($allPositions / $pageLimit);
        $html = '<ul class="vg-position-pagination">';
        // $html .= '<li class="pag-prev" onclick="new vgApiHandling().loadPage(0, \'prev\', this)"><a href="#">&laquo;</a></li>';
        //$html .= '<li class="pag-prev"><a href="#">&laquo;</a></li>';
		for ($i=0; $i<$totalPositions; $i++)
		{
            $page = $i + 1;
            $class = "page-$i";
            if ($i === 0) {
                $class .= ' active first';
            } elseif ($i+1 == $totalPositions) {
                $class .= ' last';
            }
            if ($i < 5 || in_array($i, [$totalPositions-1, $totalPositions-2, $totalPositions-3, $totalPositions-4, $totalPositions-5])) {
                $html .= "<li class='$class' onclick='new vgApiHandling().loadPage($i, \"currPage\", this)' curr-page='{$i}'><a>$page</a></li>";  // directly reload in page
            } elseif ($i == round($totalPositions/2)) {
                $html .= "<li class='$class vg-position-pagination-dot' onclick='new vgApiHandling().loadPage($i, \"currPage\", this)' curr-page='{$i}'><a>. . .</a></li>";
            }
        }
        // $html .= '<li class="pag-next" onclick="new vgApiHandling().loadPage(0, \'next\', this)"><a href="#">&raquo;</a></li>';
        //$html .= '<li class="pag-next"><a href="#">&raquo;</a></li>';
        $html .= '</ul>';
        return $html;
	}

    public static function handlePagination($res, $pageNum, $condition='')
    {
		require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/helper.php';
		$plgParams = json_decode(vgTradingTechHelper::getPlgTradingAttrs()->params);
        $limitPositions = $plgParams->limit_positions;
		$db = Factory::getDbo();
        $query = $db->getQuery(true);
        // $columns = vgTradingTechHelper::getTradingPositionAttrs();
        $allColumns = vgTradingTechHelper::getAllAssocTradingColumns();
        $columns = [$allColumns['tt_positions_str'] . ', ' .$allColumns['tt_instruments_str']];
		$query->select($columns)
			->from('`#__tt_positions` AS pos')
            ->join('INNER', '`#__tt_instruments` AS ins ON ins.id = pos.instrumentId');
		if (!empty($condition)) {
			$query->where($condition);
		}
        $query->order('pos.date DESC')
			->setLimit($limitPositions, $pageNum * $limitPositions);
		$db->setQuery($query);
        $neededData = $db->loadAssocList();
        // parse position open and close
        $neededData = self::parsePositionOpenClose($neededData);
//		echo '<pre style="color: red">';print_r($neededData);echo '</pre>';die;

        // load all association data between #__tt_positions and #__tt_instruments
        $query->clear();
        $query->select('pos.*, ins.*, ins.id AS instru_id, ins.modified AS instru_modified')
            ->from('`#__tt_positions` AS pos')
            ->join('INNER', '`#__tt_instruments` AS ins ON ins.id = pos.instrumentId');
		if (!empty($condition)) {
			$query->where($condition);
		}
        $query->order('pos.date DESC')
            ->setLimit($limitPositions, $pageNum * $limitPositions);
        $db->setQuery($query);
        $allPositionsData = $db->loadAssocList();
        // parse position open and close
        $allPositionsData = self::parsePositionOpenClose($allPositionsData);

		// count all data with conditions
		$query->clear();
		$query->select('COUNT("pos.id")')
			->from('`#__tt_positions` AS pos')
            ->join('INNER', '`#__tt_instruments` AS ins ON ins.id = pos.instrumentId');
		if (!empty($condition)) {
			$query->where($condition);
		}
        $query->order('pos.date DESC');
		$db->setQuery($query);
		$numRows = $db->loadResult();

	    $res['code'] = 200;
	    $res['message'] = sprintf('Loading trading position at page %s success!', $pageNum);
		$res['success'] = true;
		$res['data']['html'] = self::showTableData($neededData, $allPositionsData, $pageNum);
		$res['data']['numRows'] = $numRows;
		return $res;
    }

    /**
     * @param array $res
     * @param object $data
     *
     * @return array
     *
     * @since version
     */
    public static function searchPosition($res, $data)
    {
		$db = Factory::getDbo();
		$currentDate = date('Y-m-d');
		$query = '';
        $emptyRes = [
            'code' => 201,
            'message' => 'Date is Not a valid date format Y-m-d',
            'success' => false,
            'data' => ['html' => self::$noRecordsFound]
        ];

        if (empty($data->date_in) && empty($data->date)) {
            return $emptyRes;
        }

		if (!empty($data->date_in)) {
			$date = $data->date_in;
			$dateFrom = explode(' ', $date)[0] ?? '';
			$dateTo = explode(' ', $date)[1] ?? $currentDate;
			if (empty($dateFrom) || !vgTradingTechHelper::isValidDate($dateFrom)
				|| !vgTradingTechHelper::isValidDate($dateTo)) {
				return $emptyRes;
			}
		    $query = "`date` BETWEEN " . $db->quote($dateFrom) . " AND " . $db->quote($dateTo);
		} else if (!empty($data->date)) {
			$date = $data->date;
			if (!vgTradingTechHelper::isValidDate($date)) return $emptyRes;
			$query = "`date` = " . $db->quote($date);
		}
		$tradingData = self::handlePagination($res, 0, $query);
		$tradingData['data']['htmlPagination'] = self::paginationTradingPositions($tradingData['data']['numRows']);

	    return $tradingData;
	}

    /**
     * modified data, add attributes: position_close & position_open
     * 
     * @param array $data
     * @return array $data
     */
	public static function parsePositionOpenClose($data)
	{
        if (!isset($data[0]['netPosition'])){
            $app = Factory::getApplication();
            $app->enqueueMessage(Text::_('PLG_VG_TRADING_TECH_SELECT_NETPOSITION'));
            return $data;
        }
        if (!empty($data['position_open']) && !empty($data['position_close'])) return ;
        foreach ($data as $key => $tradeData) {
            $sell = isset($tradeData['avgSell']) ? "Sell: " . $tradeData['avgSell'] : '';
            $buy = isset($tradeData['avgBuy']) ? "Buy: " . $tradeData['avgBuy'] : '';
            $data[$key]['position_open'] = $sell;
            $data[$key]['position_close'] = $sell;
            if ($tradeData['netPosition'] > 0) {
                $data[$key]['position_open'] = $buy;
            } else {
                $data[$key]['position_close'] = $buy;
            }
        }
		return $data;
    }

	/**
	 *
	 * @return array|mixed
	 *
	 * @since version
	 */
	public static function getUsersSendMail($sendMail=false)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('u.*, ml.send_mail')
			->from('`#__users` AS u')
			->join('LEFT', '`#__tt_mail_list` AS ml ON ml.user_id = u.id');
        if ($sendMail) {
            $query->where('ml.`send_mail` = 1');
        }
        $query->where('u.block = 0');
        $db->setQuery($query);
		$allUsers = $db->loadAssocList('id');
		return $allUsers;
	}

    public static function getAllTradingData()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('pos.*, ins.*, ins.id AS instru_id, ins.modified AS instru_modified')
            ->from('#__tt_positions AS pos')
            ->join('INNER', '`#__tt_instruments` AS ins ON ins.id = pos.instrumentId')
            ->order('pos.date DESC')
            ->setLimit();
    }

	public static function loadPositions($countTotal=false, $loadMore=true)
	{
        $start = 0;
        $plgParams = json_decode(vgTradingTechHelper::getPlgTradingAttrs()->params);
        $limitPositions = $plgParams->limit_positions;

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        // $columns = ['`id`', '`portfolio_id`', '`accountId`', '`open`', '`closed`', '`openAvgPrice`', '`pnlPrice`', '`pnlPriceType`', '`date`'];
        // $columns = vgTradingTechHelper::getTradingPositionAttrs();
        $allColumns = vgTradingTechHelper::getAllAssocTradingColumns();
        $columns = [$allColumns['tt_positions_str'] . ', ' .$allColumns['tt_instruments_str']];
		if ($countTotal) {
		    $query->select('COUNT("id")');
		} else {
			$query->select($columns);
		}
		$query->from('`#__tt_positions` AS pos')
            ->join('INNER', '#__tt_instruments AS ins ON ins.id = pos.instrumentId')
			->order('pos.date DESC');
		if (!$countTotal) {
			$query->setLimit($limitPositions, $start);
		}
        $db->setQuery($query);

		if ($countTotal) {
		    return $db->loadResult();
		}
        return $db->loadAssoclist();
	}

    /**
     * @param $userList
     * @return void
     */
	public static function updateUsersSendMail($userList)
	{
        $db = Factory::getDbo();
        $app = Factory::getApplication();
        $query = $db->getQuery(true);

        $query->select('user_id')
            ->from('`#__tt_mail_list`');
        $db->setQuery($query);
        $allUsers = $db->loadColumn();
		if (empty($userList)) {
			$userList = [];
		}
        $diff = array_diff($userList, $allUsers);

        if (!empty($diff)) {
            $query->clear();
            $query->insert('`#__tt_mail_list`')
                ->columns(['send_mail', 'user_id']);
	        foreach ($diff as $userId)
	        {
                $query->values('1,' . $db->quote($userId));
            }
            $db->setQuery($query);
            if ($db->execute()) {
                $app->enqueueMessage(sprintf('Insert %s user(s) into Users Trading Techs', count($diff)));
            }
        }

        $userNonSend = array_diff($allUsers, $userList);
		if (!empty($userNonSend))
		{
			$query->clear();
			$fields = [
				'`send_mail` = 0',
			];
			$query->update('#__tt_mail_list')
				->set($fields)
				->where('`user_id` IN (' . implode(', ', $userNonSend) . ')');
			$db->setQuery($query);
			$db->execute();
		}

		if (empty($userList)) {
			$query->clear();
			$fields = [
				'`send_mail` = 0',
			];
			$query->update('#__tt_mail_list')
				->set($fields)
				->where('`user_id` IN (' . implode(', ', $allUsers) . ')');
			$db->setQuery($query);
			$db->execute();
		} else {
            $query->clear();
            $fields = [
                '`send_mail` = 1',
            ];
            $query->update('#__tt_mail_list')
                ->set($fields)
                ->where('`user_id` IN (' . implode(', ', $userList) . ')');
            $db->setQuery($query);
            if ($db->execute()) {
                $app->enqueueMessage('Update users to send mail success!');
            }
        }

    }

    public static function mappingPositionsKey($data)
    {
        $mapKeysOrigin = [
            'portfolio_id' => 'Portfolio ID',
            'accountId' => 'Account ID',
            'instrumentId' => 'Instrument ID',
            'avgBuy' => 'Average Buy',
            'avgSell' => 'Average Sell',
            'buyFillQty' => 'Buy Fill Quantity',
            'buyWorkingQty' => 'Buy Working Quantity',
            'netPosition' => 'Net Position',
            'openAvgPrice' => 'Open Average Price',
            'pnl' => 'PNL',
            'pnlPrice' => 'PNL Price',
            'pnlPriceType' => 'PNL Price Type',
            'realizedPnl' => 'Realized PNL',
            'sellFillQty' => 'Sell Fill Quantity',
            'sellWorkingQty' => 'Sell Working Quantity',
            'sodNetPos' => 'Sod Net Position',
            'sodPriceType' => 'Sod Price Type',
            'date' => 'Date',
            'modified' => 'Date Modified',
            'open' => 'Open',
            'closed' => 'Closed',
            'parent' => 'Parent',
	        'name' => 'Instrument Name',
	        'productSymbol' => 'Product Symbol',
	        'productId' => 'Product ID',
	        'productName' => 'Product Name',
	        'instru_modified' => 'Instrument Date Modified',
	        'instrument_link' => 'Instrument Link',
	        'created' => 'Instrument Date Created',
            'position_open' =>  'Position Open',
            'position_close' => 'Position Close'
        ];
		/*// map to create language
        foreach ($mapKeysOrigin as $key => $mapKey) {
			$new = str_replace(' ', '_', $mapKey);
			$new = strtoupper($new);
			$value = "MAP_$new=\"" . $mapKey . '"';
			echo '<pre style="color: red">';print_r($value);echo '</pre>';
		}*/
	    // print to create array with text::_ language
        /*foreach ($mapKeysOrigin as $key => $mapKey) {
			$new = str_replace(' ', '_', $mapKey);
			$new = strtoupper($new);
			$value = "'$key'" . " => Text::_('MAP_$new'),";
			echo '<pre style="color: red">';print_r($value);echo '</pre>';
	    }*/
        $mapKeys = [
            'accountId' => Text::_('MAP_ACCOUNT_ID'),
            'portfolio_id' => Text::_('MAP_PORTFOLIO_ID'),
            'instrumentId' => Text::_('MAP_INSTRUMENT_ID'),
            'avgBuy' => Text::_('MAP_AVERAGE_BUY'),
            'avgSell' => Text::_('MAP_AVERAGE_SELL'),
            'buyFillQty' => Text::_('MAP_BUY_FILL_QUANTITY'),
            'buyWorkingQty' => Text::_('MAP_BUY_WORKING_QUANTITY'),
            'netPosition' => Text::_('MAP_NET_POSITION'),
            'openAvgPrice' => Text::_('MAP_OPEN_AVERAGE_PRICE'),
            'pnl' => Text::_('MAP_PNL'),
            'pnlPrice' => Text::_('MAP_PNL_PRICE'),
            'pnlPriceType' => Text::_('MAP_PNL_PRICE_TYPE'),
            'realizedPnl' => Text::_('MAP_REALIZED_PNL'),
            'sellFillQty' => Text::_('MAP_SELL_FILL_QUANTITY'),
            'sellWorkingQty' => Text::_('MAP_SELL_WORKING_QUANTITY'),
            'sodNetPos' => Text::_('MAP_SOD_NET_POSITION'),
            'sodPriceType' => Text::_('MAP_SOD_PRICE_TYPE'),
            'date' => Text::_('MAP_DATE'),
            'modified' => Text::_('MAP_DATE_MODIFIED'),
            'open' => Text::_('MAP_OPEN'),
            'closed' => Text::_('MAP_CLOSED'),
            'parent' => Text::_('MAP_PARENT'),
            'name' => Text::_('MAP_INSTRUMENT_NAME'),
            'productSymbol' => Text::_('MAP_PRODUCT_SYMBOL'),
            'productId' => Text::_('MAP_PRODUCT_ID'),
            'productName' => Text::_('MAP_PRODUCT_NAME'),
            'instru_modified' => Text::_('MAP_INSTRUMENT_DATE_MODIFIED'),
            'instrument_link' => Text::_('MAP_INSTRUMENT_LINK'),
            'created' => Text::_('MAP_INSTRUMENT_DATE_CREATED'),
            'position_open' => Text::_('MAP_POSITION_OPEN'),
            'position_close' => Text::_('MAP_POSITION_CLOSE'),
        ];

        if (empty($data)) return $mapKeys;

        // map labels input from db with JText
        $labelsFromDb = vgTradingTechHelper::getParams('select_trading_labels');
        if (!empty($labelsFromDb)) {
            $i = 0;
            foreach ($mapKeys as $label => $value) {
                if (empty($labelsFromDb[$i])) break;
                $mapKeys[$label] = $labelsFromDb[$i];
                $i++;
            }
        }

        foreach ($data as $key => $val) {
			$newKey = $key;
            if (isset($mapKeys[$key])) {
	            $newKey = $mapKeys[$key];
            }
            $data[$key] = [
                'val' => $val,
                'key' => $newKey
            ];
        }

        return $data;
    }

    public static function mappingLabelsWithDb()
    {
        $labelsFromDb = vgTradingTechHelper::getParams('select_trading_labels');
        $labelsFromJTex = self::mappingPositionsKey([]);
        if (empty($labelsFromDb)) {
            return $labelsFromJTex;
        }
        // parse label
        $i = 0;
        foreach ($labelsFromJTex as $label => $value) {
            if (empty($labelsFromDb[$i])) break;
            $labelsFromJTex[$label] = $labelsFromDb[$i];
            $i++;
        }
        return $labelsFromJTex;
    }
}

?>