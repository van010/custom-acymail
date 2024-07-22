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

    /**
     *
     * @return bool
     *
     * @since version
     */
    public static function comTradingTechExisted()
    {
        $tradingAdminPath = JPATH_ROOT . '/administrator/components/com_tradingtechnologies';
        $tradingSitePath = JPATH_ROOT . '/components/com_tradingtechnologies';
        if (is_dir($tradingAdminPath) && is_dir($tradingSitePath)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @return bool
     *
     * @since version
     */
    public static function comAcymExisted()
    {
        $comAcymAdminPath = JPATH_ROOT . '/administrator/components/com_acym';
        $comAcymSitePath = JPATH_ROOT . '/components/com_acym';
        if (is_dir($comAcymAdminPath) && is_dir($comAcymSitePath)) {
            return true;
        }
        return false;
    }

	public static function getParams($key)
	{
		$allParams = self::getPlgTradingAttrs()->params;
		if (empty($key)) {
		    return $allParams;
		}
		return isset(json_decode($allParams)->$key) ? json_decode($allParams)->$key : null;
	}

    /**
     * @return array
     */
	public static function getTradingPositionAttrs()
	{
		$allParams = self::getPlgTradingAttrs()->params;

        if (isset(json_decode($allParams)->select_positions)) {
            return json_decode($allParams)->select_positions;
        }

        $columnsPositions = self::getColumnNames('#__tt_positions');
        $columnsInstruments = self::getColumnNames('#__tt_instruments');
        /*$columnsPositionsPrefix = array_map(function ($pos){
            return "pos_$pos";
        }, $columnsPositions);
        $columnsInstrumentsPrefix = array_map(function ($instru){
            return "instru_$instru";
        }, $columnsInstruments);*/
        $data = [
            'all' => array_merge($columnsInstruments, $columnsPositions),
            'tt_positions' => $columnsPositions,
            'tt_instruments' => $columnsInstruments
        ];
        return $data;
	}

    /**
     * @param string $key
     * @return array|mixed
     */
    public static function getAllAssocTradingColumns($key='', $tblPrefix=true)
    {
        $allColumns = self::getTradingPositionAttrs();
        if (isset($allColumns['all'])) {
            return self::initGetAllCols($allColumns, $tblPrefix);
        }
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

    /**
     * get string query of position and instrument
     *
     * @param array $data
     * @param boolean $tblPrefix
     * @return mixed
     */
    public static function initGetAllCols($data, $tblPrefix)
    {
        $data['tt_instruments_str'] = array_map(function ($instru) use ($tblPrefix){
            $asIns = '';
            if ($instru === 'id' || $instru === 'modified') {
                $asIns = 'AS instru_' . $instru;
            }
            if ($tblPrefix) {
                return self::$instrumentTblPrefix . ".`$instru` $asIns, ";
            } else {
                return "`$instru`, ";
            }
        }, $data['tt_instruments']);
        $data['tt_instruments_str'] = rtrim(trim(implode('', $data['tt_instruments_str'])), ',');

        $data['tt_positions_str'] = array_map(function ($pos) use ($tblPrefix){
            if ($tblPrefix) {
                return self::$positionsTblPrefix . ".`$pos`, ";
            } else {
                return "`$pos`, ";
            }
        }, $data['tt_positions']);
        $data['tt_positions_str'] = rtrim(trim(implode('', $data['tt_positions_str'])), ',');
        return $data;
    }

    /**
     * @return array|mixed
     */
	public static function getPlgTradingAttrs()
	{
		$allAttrs = PluginHelper::getPlugin('system', 'vg_trading_tech');
		return $allAttrs;
	}

    /**
     * get all column names of the table
     *
     * @param $tblName
     * @return array|mixed
     */
	public static function getColumnNames($tblName)
	{
		$db = Factory::getDbo();
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