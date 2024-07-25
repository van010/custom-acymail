<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/adapters/vgComAcym.php';
require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/adapters/vgComTradingTech.php';

class PlgSystemVg_trading_tech extends CMSPlugin
{
    public function __construct()
    {
        // todo
    }

	public function onContentPrepareForm($form, $data){
		// todo
	}

    public function onExtensionBeforeSave($context, $tblData, $is_new)
    {
		$extensionEl = $tblData->get('element');
		if ($extensionEl !== 'vg_trading_tech') return ;
		$params = $tblData->get('params');
		if (empty($params)) return ;
		$params = json_decode($params);

        vgComTradingTech::updateUsersSendMail($params->select_users_send_mail);

        /*$mailContent = $params->preview_acym_mail_templates;
        if (!empty($mailContent)) {
            $mailId = 1;
            vgComAcym::updateAcymMailContent($mailId, $mailContent);
        }*/
    }

    public static function onExtensionAfterSave($context, $tbl, $is_new)
    {

	}

    public function onAjaxVg_trading_tech()
    {
        $app = Factory::getApplication();
        $user = Factory::getUser();
        if (!$user->id) {
			die('You need to be authorised to do this action!');
        }

        $input = $app->input;
        $task = $input->get('task', '');
		$res = [
			'message' => '',
			'code' => 200,
			'success' => true
		];
        switch ($task) {
            case 'pagination':
                $pageNum = $input->getInt('pageNum', 0);
                $res = vgComTradingTech::handlePagination($res, $pageNum);
                break;
            case 'searchPosition':
                $data = $input->get('data', '[]', 'RAW');
                $res = vgComTradingTech::searchPosition($res, json_decode($data));
                break;
            case 'updateTtSignalMail':
				$mailIds = $input->get('mailIds', [], 'RAW');
                $res = vgComAcym::updateTtSignalMail($res, json_decode($mailIds));
                break;
            case 'updateAcymMailContent':
                $mailId = $input->get('mailId', '');
                $mailContent = $input->get('mailContent', '', 'RAW');
                $res = vgComAcym::updateAcymMailContent($res, $mailId, $mailContent);
                break;
            case 'sendMail':
                $mailBody = $input->get('mailBody', '', 'RAW');
				$mailId = $input->get('mailId', 0);
                $res = vgComAcym::sendMail($mailId, $mailBody);
                break;
            case '':
            default:
                break;
        }
		return $res;
    }
}

?>