<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class JFormFieldSelectUsersSendMail extends FormField
{
	public $type = 'selectUsersSendMail';

	public function getInput()
	{
		$html = '';
		$html .= vgComTradingTech::displayUsers($this->name);
		return $html;
	}
}