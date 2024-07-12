<?php

defined('_JEXEC') or die;


use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;


class JFormFieldJapanel extends FormField
{
    public $type = 'japanel';

    public function getInput()
    {
        $this->loadDocuments();
    }

    public function loadDocuments()
    {
        $doc = Factory::getDocument();
        // HTMLHelper::_('jquery.framework');
	    $doc->addScript(Uri::root(true) . '/plugins/system/vg_trading_tech/assets/js/vg-trading-tech.js');
	    $doc->addStyleSheet(Uri::root(true) . '/plugins/system/vg_trading_tech/assets/style/vg-trading-tech.css');
    }
}

?>