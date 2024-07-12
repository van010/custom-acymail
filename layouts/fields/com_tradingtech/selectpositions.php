<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

class JFormFieldSelectPositions extends FormField
{
    public $type = 'selectPositions';

    public function getInput()
    {
        return '<p>Select trading position attributes.</p>';
    }
}

?>