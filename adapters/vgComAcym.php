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
     * @param array $res
     * @param object $mailIds
     * @return mixed
     */
    public static function updateTtSignalMail($res, $mailIds)
    {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$fields = [
			'`template_id` = ' . $db->quote($mailIds->openMailId),
			'`template2_id` = ' . $db->quote($mailIds->closeMailId)
		];
		$query->update('`#__tt_mail_template`')
			->set($fields)
			->where('`id` = 1');
		$db->setQuery($query);
		if ($db->execute()) {
			$res['message'] .= 'Update signal mail success!';
			return $res;
		}
		$res['message'] .= 'Update signal mail failed!';
		$res['code'] = 201;
		$res['success'] = false;
		return $res;
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
        $templates = $db->loadObjectList('id');

		$db->setQuery("select template_id from #__tt_mail_template");
        $openTemplate = $db->loadResult();

		$db->setQuery("select template2_id from #__tt_mail_template");
        $closeTemplate = $db->loadResult();

		return ['acym_templates' => $templates, 'tt_open_mail_id' => $openTemplate, 'tt_close_mail_id' => $closeTemplate];
	}


}

?>