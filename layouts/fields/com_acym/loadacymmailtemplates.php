<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class JFormFieldLoadacymmailtemplates extends FormField
{
	/**
	 * @return string
	 */
	public function getInput(): string
	{
		$html = 'Acym mail templates';
		return $html;
	}
}

?>