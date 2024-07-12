<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/helper.php';
require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/adapters/vgComTradingTech.php';

class JFormFieldSelectPositions extends FormField
{
    public $type = 'selectPositions';
	public $name = 'select_positions';

    public function getInput()
    {
	    $fieldName = 'select_positions';
		$plgTradingAttrs = vgTradingTechHelper::getPlgTradingAttrs();
		$plgTradingParams = json_decode($plgTradingAttrs->params);
	    $position_column_names = vgTradingTechHelper::getColumnNames('#__tt_positions');
		$html = '<fieldset name="'.$this->name.'" id="jform_params_'.$fieldName.'" class="checkboxes">';
		$html .= "<legend class='visually-hidden'></legend>";

	    foreach ($position_column_names as $key => $colName)
	    {
            $checked = in_array($colName, $plgTradingParams->$fieldName) ? 'checked' : '';
		    $html  .= '<div class="form-check form-check-inline">';
		    $id    = "jform_params_" . $fieldName . $key;
		    $name  = "jform[params][" . $fieldName . "][]";
		    $value = $colName;
			$class = 'form-check-input';

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
}

?>