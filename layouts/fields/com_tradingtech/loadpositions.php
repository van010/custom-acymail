<?php

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
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
		$positions = vgComTradingTech::loadPositions(false);
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
		$idxText = Text::_('PLG_VG_TRADING_TECH_POSITION_INDEX');
        $html = '';
        $html .= $this->htmlSearchPosition();
	    $html .= '<table id="tt-position-lists">';
		$columnNames = array_keys($positions[0]);
		$html .= '<tr class="tt-column-names">';
        $html .= "<th class='tt-name-idx'>$idxText</th>";
        foreach ($columnNames as $columnName) {
			$colum = ucfirst(str_replace('_', ' ', $columnName));
			$html .= "<th class='tt-name-$columnName'>$colum</th>";
		}
		$html .= '</tr>';

        foreach ($positions as $key => $position) {
            $key += 1;
            $positionId = $position['id'];
            $tagName = "{positionId:$positionId}";
	        $html .= "<tr style='cursor:pointer' onclick='changePosition($tagName, jQuery(this))'>";
			$html .= "<td>$key</td>";
            foreach ($position as $item) {
	            $html .= "<td>$item</td>";
			}
	        $html .= "</tr>";
		}
		$html .= '</table>';
        $html .= $this->paginationTradingPositions();
		return $html;
	}

	public function htmlSearchPosition()
	{
		$searchText = Text::_('PLG_VG_TRADING_TECH_POSITION_SEARCH');
		$searchPlaceholderText = Text::_('PLG_VG_TRADING_TECH_POSITION_SEARCH_PLACEHOLDER');
		$html = "<div class='vg-position-search-container'>";
        $html .= "<input type='text' onchange='triggerSearchPosition(this)' onclick='triggerSearchPosition(this)' placeholder='$searchPlaceholderText'>";
		$html .= "<button type='button'>$searchText</button>";
		$html .= "</div>";
		return $html;
	}

	public function paginationTradingPositions()
	{
        $totalPositions = vgComTradingTech::loadPositions(true);
        $html = '<ul class="vg-position-pagination">';
        $html .= '<li class="pag-prev"><a href="#">&laquo;</a></li>';
		for ($i=0; $i<$totalPositions; $i++)
		{
            $page = $i + 1;
            $class = "page-$i";
            if ($i === 0) {
                $class .= ' active';
            }
            if ($i < 3 || in_array($i, [$totalPositions-1, $totalPositions-2, $totalPositions-3])) {
				$html .= "<li class='$class' onclick='loadPage($i, this)'><a>$page</a></li>";  // directly reload in page
            } elseif ($i == round($totalPositions/2)) {
                $html .= "<li class='$class vg-position-pagination-dot' onclick='loadPage($i, this)' class='vg-position-pagination-dot'><a>. . .</a></li>";
            }
        }
        $html .= '<li class="pag-next"><a href="#">&raquo;</a></li>';
        $html .= '</ul>';
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