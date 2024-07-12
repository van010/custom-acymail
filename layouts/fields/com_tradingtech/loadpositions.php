<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

class JFormFieldLoadPositions extends FormField
{
    public $type = 'loadPositions';

    public function getInput()
    {
        return "<h1>Load Trading Data</h1>";
    }
}

?>