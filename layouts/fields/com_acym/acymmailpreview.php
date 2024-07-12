<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

class JFormFieldAcymMailPreview extends FormField
{
	public $type = 'acymMailPreview';

	public function getInput()
	{
		return '<p>Create acym mail template, mail preview.</p>';
	}
}

?>