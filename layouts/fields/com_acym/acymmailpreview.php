<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/adapters/editors.php';

class JFormFieldAcymMailPreview extends FormField
{
	public $type = 'acymMailPreview';

	public function getInput()
	{
		$editor = new vgEditors();
		$allMails = vgComAcym::getMailTemplates();
		$content = $allMails['open_mail_content'];
		$html = 'Load acym templates';
		$html .= "<div class='select-acym-templates'>";
		$html .= vgComAcym::tradingOpenMail($allMails, 'jform_params_acym_temps_preview', 'jform[params][acym_temps_preview]', 0);
		$html .= "</div>";
		$html .= "<div class='vg-editor'>";
		// $html .= $editor->embedContentToEditor($this->name, '<p>hello world</p>');
		$html .= $editor->embedContentToEditor($this->name, $content);
		$html .= "</div>";
		return $html;
	}
}

?>