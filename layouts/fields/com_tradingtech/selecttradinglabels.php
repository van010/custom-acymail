<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;


class JFormFieldSelectTradingLabels extends FormField
{
    public $type = 'selectTradingLabels';

    public function getInput()
    {
        $html = '';
        $id = "jform_params_select_trading_labels";
        $name = $this->name;
        $text = strtoupper(Text::_('PLG_VG_TRADING_TECH_LABEL_SHORTCODE_NAME'));
        $labels = vgComTradingTech::mappingLabelsWithDb();
        $html .= "<fieldset name='$name' id='$id'>";
        $html .= "<div class='label-shortcode'>";
        $html .= "<label class='shortcode-name'>{$text}</label>";
        $i = 0;
        foreach ($labels as $label => $val) {
            $html .= "<div class='label-shortcode-{$label}'>";
            $html .= "<label for='{$label}'>$label</label>";
            $html .= "<input type='text' id='{$id}-{$label}' name='{$name}[$i]' value='{$val}'/>";
            $html .= "</div>";
            $i++;
        }
        $html .= "</div>";
        $html .= "</fieldset>";
        return $html;
    }
}