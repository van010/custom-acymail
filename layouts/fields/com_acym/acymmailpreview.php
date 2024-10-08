<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/adapters/editors.php';

class JFormFieldAcymMailPreview extends FormField
{
	public $type = 'acymMailPreview';

	public function getInput()
	{
        if (!vgTradingTechHelper::comAcymExisted()) {
            return Text::_('PLG_VG_TRADING_COM_ACYM_NOT_EXIST');
        }
		$editor = new vgEditors();
		$allMails = vgComAcym::getMailTemplates();
		$content = $allMails['open_mail_content'];
        $btnSendmailText = Text::_('PLG_VG_TRADING_TECH_BTN_SEND_MAIL');
        $btnUpdateText = Text::_('PLG_VG_TRADING_TECH_BTN_UPDATE_ACYM_MAIL_CONTENT');
		$html = 'Load acym templates';
		$html .= "<div class='select-acym-templates'>";
		$html .= vgComAcym::tradingOpenMail($allMails, 'jform_params_acym_temps_preview', 'jform[params][acym_temps_preview]', 0);
        $html .= "<button id='vg-send-mail' type='button' onclick='new vgApiHandling().sendMailToUsers()'>$btnSendmailText</button>";
        $html .= "<p id='send-mail-success' class='vg-hide'></p>";
		$html .= "</div>";
        $html .= "<div class='update-acym-mail'>";
        $html .= "<button id='update-acym-mail-content' type='button' class='btn btn-primary mb-2' onclick='new vgApiHandling().updateAcymMailContent()'>$btnUpdateText</button>";
        $html .= "<p class='update-acym-mail-msg'></p>";
        $html .= "</div>";
		$html .= "<div class='vg-editor'>";
		$html .= $editor->embedContentToEditor($this->name, $content);
        $html .= "<div class='content-select-shortcode'>";
        $html .= vgComTradingTech::embedTradingPositionsToEditor('embed', $this->name);
		$html .= "</div>";
		$html .= "</div>";
		return $html;
	}
}

?>