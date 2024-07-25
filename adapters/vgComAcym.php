<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class vgComAcym
{
	public function __construct()
	{
		// todo
	}

    /**
     * @param array $res
     * @param int $mailId
     * @param string $mailContent
     * @return mixed
     */
    public static function updateAcymMailContent($res, $mailId, $mailContent)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $fields = [
            '`body` = '. $db->quote($mailContent)
        ];
        $query->update('#__acym_mail')
            ->set($fields)
            ->where('`id` = ' . $db->quote($mailId));
        $db->setQuery($query);
        if ($db->execute()) {
            $res['message'] = "Update mail content success: mail_id = $mailId";
            return $res;
        }
        $res['code'] = 201;
        $res['message'] = 'Update mail content failed!';
        return $res;
    }

    /**
     * @param int $mailId
     * @param string $mailBody
     *
     * @return array|null
     *
     * @since version
     */
    public static function sendMail($mailId, $mailBody)
    {
        $usersListAllowed = vgComTradingTech::getUsersSendMail(true);
		$res = [
			'code' => 200,
			'message' => ''
		];
        $mailData = self::getMailAttrs($mailId);
        $subject = $mailData['subject'];
        foreach ($usersListAllowed as $user) {
            $res = self::sendOneMail($user['email'], $user['name'], $subject, $mailBody, $res);
        }
		return $res;
    }

    /**
     * @param string $email
     * @param string $username
     * @param string $subject
     * @param string $mailBody
     * @param array $res
     *
     *
     * @since version
     */
    public static function sendOneMail($email, $username, $subject, $mailBody, $res)
    {
        $mailer = Factory::getMailer();
        $config = Factory::getConfig();
        $sender = [
            $config->get('mailfrom'),
            $config->get('fromname'),
        ];
        $mailer->setSender($sender);
        $mailer->addRecipient($email);
        $mailer->addBCC($config->get('mailfrom'));
        $mailer->setSubject($subject);
        $mailer->setBody($mailBody);
        $mailer->isHtml(true);
        if ($mailer->send()) {
            $res['message'] .= "<p>The Trading data has been sent to: <b>$username</b></p>";
			return $res;
        }
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
     * @param $mailId
     *
     * @return mixed
     *
     * @since version
     */
    public static function getMailAttrs($mailId)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('`#__acym_mail`')
            ->where('`id` = ' . $db->quote($mailId));
        $db->setQuery($query);
        return $db->loadAssoc();
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
        $openTemplateId = $db->loadResult();

		$db->setQuery("select template2_id from #__tt_mail_template");
        $closeTemplateId = $db->loadResult();

        $openMailContent = $templates[$openTemplateId]->body;
        $closeMailContent = $templates[$closeTemplateId]->body;

		$acymData = [
			'acym_templates' => $templates,
            'tt_open_mail_id' => $openTemplateId,
            'tt_close_mail_id' => $closeTemplateId,
            'open_mail_content' => $openMailContent,
            'close_mail_content' => $closeMailContent,
		];

		return $acymData;
	}

    /**
     * @param array $allMails
     * @param integer $id
     * @param string $field_select_name
     * @return string
     */
    public static function tradingOpenMail($allMails, $id, $field_select_name, $preview=1)
    {
        $strOpenMailIds = json_encode(array_keys($allMails['acym_templates']));
        $openMailId = $allMails['tt_open_mail_id'];
		$openText = Text::_('PLG_VG_TRADING_TECH_ACYM_SELECT_OPEN_MAIL');
	    $htmlOpen = '<div class="trading-open-mail">';
		$htmlOpen .= "<span>$openText</span>";
        $htmlOpen .= "<select onchange='triggerUpdateTtSignalMail(this, value, $preview)' id='$id-open' name='$field_select_name-open' class='form-select required' data-id='$strOpenMailIds' required>";
        // $htmlOpen .= "<option value=''>Please Select Open Mail Templates</option>";
	    foreach ($allMails['acym_templates'] as $acymTemplate)
	    {
            $acymTemplateId = $acymTemplate->id;
            $acymTemplateName = $acymTemplate->name;
			$selected = '';
            if ($openMailId === $acymTemplateId) {
	            $selected = ' selected';
            }
            $htmlOpen .= "<option value='$acymTemplateId' $selected>$acymTemplateName - $acymTemplateId</option>";
        }
        $htmlOpen .= "</select>";
		$htmlOpen .= "<input id='for-jform_params_load_acym_mail-open' value='$openMailId' hidden>";
        $htmlOpen .= "<div class='open-mail-preview'>";
        foreach ($allMails['acym_templates'] as $acymTemplate) {
            $openMailBody = $acymTemplate->body;
            $acymTemplateId = $acymTemplate->id;
            $display = 'vg-show';
            if ($openMailId !== $acymTemplateId || $preview === 0) {
                $display = 'vg-hide';
            }
            // $display = $openMailId !== $acymTemplateId ? 'vg-hide' : 'vg-show';
            $htmlOpen .= "<div class='$display' id='open-mail-$id-$acymTemplateId'>$openMailBody</div>";
        }
        $htmlOpen .= "</div>";
        /*if ($preview)
        {
	        $htmlOpen .= "<div class='open-mail-preview'>";
	        foreach ($allMails['acym_templates'] as $acymTemplate)
	        {
		        $openMailBody   = $acymTemplate->body;
		        $acymTemplateId = $acymTemplate->id;
		        $display        = $openMailId !== $acymTemplateId ? 'vg-hide' : 'vg-show';
		        $htmlOpen       .= "<div class='$display' id='open-mail-$acymTemplateId'>$openMailBody</div>";
	        }
	        $htmlOpen .= "</div>";
        }*/
		$htmlOpen .= "</div>";
		return $htmlOpen;
	}

    /**
     * @param array $allMails
     * @param integer $id
     * @param string $field_select_name
     * @return string
     */
    public static function tradingCloseMail($allMails, $id, $field_select_name, $preview=true)
    {
        $strCloseMailIds = json_encode(array_keys($allMails['acym_templates']));
		$closeMailId = $allMails['tt_close_mail_id'];
		$closeText = Text::_('PLG_VG_TRADING_TECH_ACYM_SELECT_CLOSE_MAIL');
		$htmlClose = "<div class='trading-close-mail'>";
		$htmlClose .= "<span>$closeText</span>";
	    $htmlClose .= "<select onchange='triggerUpdateTtSignalMail(this, value)' id='$id-close' name='$field_select_name-close' class='form-select required' data-id='$strCloseMailIds' required>";
        foreach ($allMails['acym_templates'] as $acymTemplate)
	    {
            $acymTemplateId = $acymTemplate->id;
            $acymTemplateName = $acymTemplate->name;
            $selected = '';
            if ($closeMailId === $acymTemplateId) {
                $selected = ' selected';
            }
            $htmlClose .= "<option value='$acymTemplateId' $selected>$acymTemplateName - $acymTemplateId</option>";
        }
        $htmlClose .= "</select>";
        $htmlClose .= "<input id='for-jform_params_load_acym_mail-close' value='$closeMailId' hidden>";
        if ($preview)
        {
	        $htmlClose .= "<div class='close-mail-preview'>";
	        foreach ($allMails['acym_templates'] as $acymTemplate)
	        {
		        $closeMailBody  = $acymTemplate->body;
		        $acymTemplateId = $acymTemplate->id;
		        $display        = 'vg-hide';
		        if ($closeMailId === $acymTemplateId)
		        {
			        $display = 'vg-show';
		        }
		        $htmlClose .= "<div class='$display' id='close-mail-$acymTemplateId'>$closeMailBody</div>";
	        }
	        $htmlClose .= "</div>";
        }
		$htmlClose .= "</div>";
		return $htmlClose;
	}
}

?>