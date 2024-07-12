<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class JFormFieldLoadPositions extends FormField
{
    public $type = 'loadPositions';

	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
    public function getInput()
    {
        $html = "<h1>".Text::_('PLG_VG_TRADING_TECH_LOAD_POSITION_LABEL')."</h1>";
        $html .= '';
		$positions = vgComTradingTech::loadPositions();
		$html .= $this->showTableData($positions);
        return $html;
    }

	/**
	 * @param $positions
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function showTableData($positions)
    {
		if (empty($positions)) {
		    return '';
		}
	    $html = '<table id="tt-position-lists">';
		$columnNames = array_keys($positions[0]);
		$html .= '<tr class="tt-column-names">';
        foreach ($columnNames as $columnName) {
			$colum = ucfirst(str_replace('_', ' ', $columnName));
			$html .= "<th class='tt-name-$columnName'>$colum</th>";
		}
		$html .= '</tr>';

        foreach ($positions as $position) {
            $positionId = $position['id'];
            $tagName = "{positionId:$positionId}";
	        $html .= "<tr style='cursor:pointer' onclick='changePosition($tagName, jQuery(this))'>";
            foreach ($position as $item) {
	            $html .= "<td>$item</td>";
			}
	        $html .= "</tr>";
		}
		$html .= '</table>';
		return $html;
	}

	/**
	 * show select box, let user choose and show data inside
     * after that, user can directly override on this template id or create new template id with some old params of this id
	 *
	 * @since version
	 */
	public function loadAcymMailTemplate()
	{
        
    }
    
}

?>