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
        $html .= vgComAcym::tradingOpenMail($allMails, $id, $field_select_name);
        $html .= vgComAcym::tradingCloseMail($allMails, $id, $field_select_name);
        $html .= "<button type='button' onclick='updateTtSignalMail()' class='btn btn-primary mb-2'>$btnSaveText</button>";
        $html .= "<p class='update-mail-msg'></p>";
		$html .= '</div>';

        return $html;
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