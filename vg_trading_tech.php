<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/adapters/vgComTradingTech.php';

class PlgSystemVg_trading_tech extends CMSPlugin
{
    public function __construct()
    {
        // todo
    }

    public function onAjaxVg_trading_tech()
    {
        $app = Factory::getApplication();
        $user = Factory::getUser();
        if (!$user->id) return ;

        $input = $app->input;
        $task = $input->get('vgTask', '');
        $pageNum = $input->getInt('pageNum', 0);
		$res = [
			'message' => '',
			'code' => 404,
			'success' => false
		];
        switch ($task) {
            case 'pagination':
                $res = vgComTradingTech::handlePagination($res, $pageNum);
                break;
            case 'searchPosition':
                vgComTradingTech::searchPosition('');
                break;
            case '':
            default:
                break;
        }
		return $res;
    }
}

?>