<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

class JFormFieldExcludePositions extends FormField
{
    public $type = 'excludePositions';

    public function getInput()
    {
        $fieldName = 'exclude_positions';
        $html = vgComTradingTech::displayTradingPosition('exclude', $this->name);
        return $html;
    }
}

?>