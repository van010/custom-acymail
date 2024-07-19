<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/helper.php';
require_once JPATH_ROOT . '/plugins/system/vg_trading_tech/adapters/vgComTradingTech.php';

class JFormFieldSelectPositions extends FormField
{
    public $type = 'selectPositions';
	public $name = 'select_positions';

    public function getInput()
    {
        if (!vgTradingTechHelper::comTradingTechExisted()) {
            return Text::_('PLG_VG_TRADING_COM_TRADING_NOT_EXIST');
        }
		$html = vgComTradingTech::displayTradingPosition('select', $this->name);
        return $html;
    }
}

?>