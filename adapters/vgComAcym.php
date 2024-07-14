<?php

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class vgComAcym
{
	public function __construct()
	{
		// todo
	}

	/**
	 *
	 * @return array
	 *
	 * @since version
	 */
	public static function getMailTemplates()
	{
		$db = Factory::getDbo();

        $db->setQuery("select `id`, `name`, `body` from #__acym_mail where `type` = 'template'");
        $templates = $db->loadObjectList();

		$db->setQuery("select template_id from #__tt_mail_template");
        $openTemplate = $db->loadResult();

		$db->setQuery("select template2_id from #__tt_mail_template");
        $closeTemplate = $db->loadResult();

		return ['acym_templates' => $templates, 'tt_open_mail_id' => $openTemplate, 'tt_close_mail_id' => $closeTemplate];
	}


}

?>