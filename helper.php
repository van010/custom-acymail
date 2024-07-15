<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

class vgTradingTechHelper
{

    public static $instrumentTblPrefix = 'ins';
    public static $positionsTblPrefix = 'pos';

	public function __construct()
	{
		// todo
	}

	public static function getParams($key)
	{
		$allParams = self::getPlgTradingAttrs()->params;
		if (empty($key)) {
		    return $allParams;
		}
		return json_decode($allParams)->$key;
	}

	public static function getTradingPositionAttrs()
	{
		$allParams = self::getPlgTradingAttrs()->params;
		return json_decode($allParams)->select_positions;
	}

    /**
     * @param string $key
     * @return array|mixed
     */
    public static function getAllAssocTradingColumns($key='', $tblPrefix=true)
    {
        $allColumns = self::getTradingPositionAttrs();
        $colData = [];
        $ttPositionsPrefix = 'pos_';
        $ttInstrumentPrefix = 'instru_';
        $ttPositionStr = '';
        $ttInstrumentStr = '';
        foreach ($allColumns as $column) {
            if (str_contains($column, $ttPositionsPrefix)) {
                $originPosCol = str_replace($ttPositionsPrefix, '', $column);
                $asPos = '';
                /*if ($originPosCol === 'id' || $originPosCol === 'modified') {
                    $asPos = "AS $column ";
                }*/
                $colData['tt_positions'][] = $originPosCol;
                if ($tblPrefix) {
                    $ttPositionStr .= self::$positionsTblPrefix . ".`$originPosCol` $asPos, ";
                } else {
                    $ttPositionStr .= "`$originPosCol`, ";
                }
            }
            if (str_contains($column, $ttInstrumentPrefix)) {
                $originInstruCol = str_replace($ttInstrumentPrefix, '', $column);
                $asIns = '';
                if ($originInstruCol === 'id' || $originInstruCol === 'modified') {
                    $asIns = "AS $column";
                }
                $colData['tt_instruments'][] = $originInstruCol;

                if ($tblPrefix) {
                    $ttInstrumentStr .= self::$instrumentTblPrefix . ".`$originInstruCol` $asIns, ";
                } else {
                    $ttInstrumentStr .= "`$originInstruCol`, ";
                }
            }
        }
        $colData['all'] = array_merge($colData['tt_instruments'], $colData['tt_positions']);
        $colData['tt_positions_str'] = rtrim(trim($ttPositionStr), ',');
        $colData['tt_instruments_str'] = rtrim(trim($ttInstrumentStr), ',');

        if (!empty($key) && !empty($colData[$key])) {
            return $colData[$key];
        }
        return $colData;
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

    /**
     * @param bool $modify
     * @return mixed
     */
    public static function getInstrumentColumnNames($modify=false)
    {
        $allColumns = self::getColumnNames('#__tt_instruments');
        if (!$modify) return $allColumns;
        foreach ($allColumns as $key => $column) {
            $allColumns[$key] = 'instru_' . $column;
        }
        return $allColumns;
    }

    /**
     * @param bool $modify
     * @return mixed
     */
    public static function getTradingPositionsColumnNames($modify=false)
    {
        $allColumns = self::getColumnNames('#__tt_positions');
        if (!$modify) return $allColumns;
        foreach ($allColumns as $key => $column) {
            $allColumns[$key] = 'pos_' . $column;
        }
        return $allColumns;
    }
}

?>