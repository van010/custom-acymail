<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
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
        if (!vgTradingTechHelper::comTradingTechExisted()) {
            return Text::_('PLG_VG_TRADING_COM_TRADING_NOT_EXIST');
        }
        $html = "<h1>".Text::_('PLG_VG_TRADING_TECH_LOAD_POSITION_LABEL')."</h1>";
        $html .= '';
		$app = Factory::getApplication();
		$input = $app->input;
		$tradingPageId = $input->getInt('page', 0);
		// $positions = vgComTradingTech::loadPositions(false);
        $html .= $this->htmlSearchPosition();
	    // $html .= vgComTradingTech::showTableData($positions);
	    $html .= '<div id="tbl-trading-data">';
	    $html .= vgComTradingTech::handlePagination([], $tradingPageId)['data']['html'];
        $html .= '</div>';
        $html .= "<div id='tbl-trading-pagination'>";
	    $html .= vgComTradingTech::paginationTradingPositions(vgComTradingTech::loadPositions(true));
        $html .= "</div>";
        return $html;
    }

	public function htmlSearchPosition()
	{
		$searchText = Text::_('PLG_VG_TRADING_TECH_POSITION_SEARCH');
		$searchPlaceholderText = Text::_('PLG_VG_TRADING_TECH_POSITION_SEARCH_PLACEHOLDER');
        $searchHintText = Text::_('PLG_VG_TRADING_TECH_SEARCH_HINT');
		$html = "<div class='vg-position-search-container'>";
        $html .= "<input type='text' onchange='new vgApiHandling().searchPosition(this)' placeholder='$searchPlaceholderText' value=''>";
		$html .= "<button type='button' onclick='new vgApiHandling().searchPosition(this)'>$searchText</button>";
        $html .= "<div class='search-hint'>$searchHintText</div>";
		$html .= "</div>";
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