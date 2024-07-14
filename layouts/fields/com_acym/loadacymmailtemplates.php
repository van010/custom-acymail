<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/adapters/vgComAcym.php';

class JFormFieldLoadacymmailtemplates extends FormField
{

    public $type = 'loadAcymMailTemplates';

	/**
	 * @return string
	 */
	public function getInput(): string
	{
		$html = '';
        $html .= $this->loadAcymMailTemplates();
		return $html;
	}

    /**
     * @return string
     */
    public function loadAcymMailTemplates()
    {
        $name = 'load_acym_mail';
        $id = "jform_params_$name";
        $field_select_name = "jform[params][$name]";
        $allMails = vgComAcym::getMailTemplates();
		$btnSaveText = Text::_('PLG_VG_TRADING_TECH_ACYM_SAVE_TO_TRADING_TECH');
        // $url = Uri::root(true) . '/administrator/index.php?option=com_plugins&view=plugin&layout=edit&extension_id=10247&task=save_mail';
	    $url = Uri::root(true) . '/index.php?option=com_ajax&plugin=vg_trading_tech&format=json&group=system&task=save_mail';

        $html = '<div class="trading-mail-config">';
        $html .= $this->tradingOpenMail($allMails, $id, $field_select_name);
        $html .= $this->tradingCloseMail($allMails, $id, $field_select_name);
        $html .= "<button type='button' onclick='updateTtSignalMail()' class='btn btn-primary mb-2'>$btnSaveText</button>";
        $html .= "<p class='update-mail-msg'></p>";
		$html .= '</div>';

        return $html;
    }

    /**
     * @param array $allMails
     * @param integer $id
     * @param string $field_select_name
     * @return string
     */
    public function tradingOpenMail($allMails, $id, $field_select_name)
    {
        $strOpenMailIds = json_encode(array_keys($allMails['acym_templates']));
        $openMailId = $allMails['tt_open_mail_id'];
		$openText = Text::_('PLG_VG_TRADING_TECH_ACYM_SELECT_OPEN_MAIL');
	    $htmlOpen = '<div class="trading-open-mail">';
		$htmlOpen .= "<span>$openText</span>";
        $htmlOpen .= "<select onchange='triggerUpdateTtSignalMail(this, value)' id='$id-open' name='$field_select_name-open' class='form-select required' data-id='$strOpenMailIds' required>";
        // $htmlOpen .= "<option value=''>Please Select Open Mail Templates</option>";
	    foreach ($allMails['acym_templates'] as $acymTemplate)
	    {
            $acymTemplateId = $acymTemplate->id;
            $acymTemplateName = $acymTemplate->name;
			$selected = '';
            if ($openMailId === $acymTemplateId) {
	            $selected = ' selected';
            }
            $htmlOpen .= "<option value='$acymTemplateId' $selected>$acymTemplateName - $acymTemplateId</option>";
        }
        $htmlOpen .= "</select>";
		$htmlOpen .= "<input id='for-jform_params_load_acym_mail-open' value='$openMailId' hidden>";
        $htmlOpen .= "<div class='open-mail-preview'>";
        foreach ($allMails['acym_templates'] as $acymTemplate) {
            $openMailBody = $acymTemplate->body;
            $acymTemplateId = $acymTemplate->id;
            $display = $openMailId !== $acymTemplateId ? 'vg-hide' : 'vg-show';
            $htmlOpen .= "<div class='$display' id='open-mail-$acymTemplateId'>$openMailBody</div>";
        }
        $htmlOpen .= "</div>";
		$htmlOpen .= "</div>";
		return $htmlOpen;
	}

    /**
     * @param array $allMails
     * @param integer $id
     * @param string $field_select_name
     * @return string
     */
    public function tradingCloseMail($allMails, $id, $field_select_name)
    {
        $strCloseMailIds = json_encode(array_keys($allMails['acym_templates']));
		$closeMailId = $allMails['tt_close_mail_id'];
		$closeText = Text::_('PLG_VG_TRADING_TECH_ACYM_SELECT_CLOSE_MAIL');
		$htmlClose = "<div class='trading-close-mail'>";
		$htmlClose .= "<span>$closeText</span>";
	    $htmlClose .= "<select onchange='triggerUpdateTtSignalMail(this, value)' id='$id-close' name='$field_select_name-close' class='form-select required' data-id='$strCloseMailIds' required>";
        foreach ($allMails['acym_templates'] as $acymTemplate)
	    {
            $acymTemplateId = $acymTemplate->id;
            $acymTemplateName = $acymTemplate->name;
            $selected = '';
            if ($closeMailId === $acymTemplateId) {
                $selected = ' selected';
            }
            $htmlClose .= "<option value='$acymTemplateId' $selected>$acymTemplateName - $acymTemplateId</option>";
        }
        $htmlClose .= "</select>";
        $htmlClose .= "<input id='for-jform_params_load_acym_mail-close' value='$closeMailId' hidden>";
        $htmlClose .= "<div class='close-mail-preview'>";
        foreach ($allMails['acym_templates'] as $acymTemplate) {
			$closeMailBody = $acymTemplate->body;
			$acymTemplateId = $acymTemplate->id;
            $display = 'vg-hide';
            if ($closeMailId === $acymTemplateId) {
                $display = 'vg-show';
            }
            $htmlClose .= "<div class='$display' id='close-mail-$acymTemplateId'>$closeMailBody</div>";
        }
		$htmlClose .= "</div>";
		$htmlClose .= "</div>";
		return $htmlClose;
	}

    /**
     * @return string
     */
    public function sampleJSelection()
    {
        $html = '<select id="jform_params_mylistvalue" name="jform[params][mylistvalue]" class="form-select required" required="">
                    <option value="" selected="selected">Please Select</option>
                    <option value="0">Option 1</option>
                    <option value="1">Option 2</option>
                </select>';
        return $html;
    }
}

?>