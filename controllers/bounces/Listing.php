<?php

namespace AcyMailing\Controllers\Bounces;

use AcyMailing\Classes\ListClass;
use AcyMailing\Classes\RuleClass;
use AcyMailing\Helpers\BounceHelper;
use AcyMailing\Helpers\CronHelper;
use AcyMailing\Helpers\SplashscreenHelper;
use AcyMailing\Helpers\ToolbarHelper;
use AcyMailing\Helpers\UpdateHelper;
use AcyMailing\Helpers\WorkflowHelper;

trait Listing
{
    public function listing()
    {
        $this->storeRedirectListing(true);
    }

    public function prepareToolbar(&$data, $element = 'bounces')
    {
        $toolbarHelper = new ToolbarHelper();
        if ($element === 'bounces') {
            $toolbarHelper->addButton(
                'ACYM_CONFIGURE',
                [
                    'data-task' => 'config',
                    'id' => 'acym__bounce__button__config',
                    'type' => 'button',
                ],
                'settings'
            );
            $toolbarHelper->addButton(
                'ACYM_RESET_DEFAULT_RULES',
                [
                    'data-task' => 'reinstall',
                    'type' => 'button',
                    'data-confirmation-message' => 'ACYM_ARE_YOU_SURE',
                ],
                'repeat'
            );
            $toolbarHelper->addButton('ACYM_RUN_BOUNCE_HANDLING', ['data-task' => 'test'], 'play_arrow');
            $toolbarHelper->addButton('ACYM_CREATE', ['data-task' => 'rule', 'type' => 'submit'], 'add', true);
        } else {
            $toolbarHelper = new ToolbarHelper();
            $toolbarHelper->addSearchBar($data['search'], 'mailboxes_search', 'ACYM_SEARCH');
            $toolbarHelper->addButton(acym_translation('ACYM_CREATE'), ['data-task' => 'mailboxAction'], 'add', true);
            $data['toolbar'] = $toolbarHelper;
        }

        $data['toolbar'] = $toolbarHelper;
    }

    public function bounces()
    {
        $this->currentClass = new RuleClass();
        $splashscreenHelper = new SplashscreenHelper();
        $data = [];

        if (acym_level(ACYM_ENTERPRISE)) {
            if ($splashscreenHelper->getDisplaySplashscreenForViewName('bounces')) {
                acym_setVar('layout', 'splashscreen');

                $data['isEnterprise'] = true;
            } else {
                acym_setVar('layout', 'listing');
                $ruleClass = new RuleClass();
                $listsClass = new ListClass();

                $bounceRules = $ruleClass->getAll();

                $finalRule = null;
                foreach ($bounceRules as $key => $oneRule) {
                    if ($oneRule->id === $this->currentClass::FINAL_RULE_ID) {
                        $finalRule = $oneRule;
                        unset($bounceRules[$key]);
                        break;
                    }
                }

                if ($finalRule !== null) {
                    $bounceRules[] = $finalRule;
                }

                $data = [
                    'allRules' => $bounceRules,
                    'lists' => $listsClass->getAllWithIdName(),
                ];
            }
        }

        $data['workflowHelper'] = new WorkflowHelper();
        if (!acym_level(ACYM_ENTERPRISE)) {
            acym_setVar('layout', 'splashscreen');
            $data['isEnterprise'] = false;
        }

        $this->prepareToolbar($data);

        parent::display($data);
    }

    public function passSplash()
    {
        $splashscreenHelper = new SplashscreenHelper();
        $splashscreenHelper->setDisplaySplashscreenForViewName('bounces', 0);

        $this->listing();
    }

    public function ajaxSetOrdering()
    {
        $order = json_decode(acym_getVar('string', 'order'));
        $i = 1;
        $error = false;
        foreach ($order as $rule) {
            $query = 'UPDATE #__acym_rule SET `ordering` = '.intval($i).' WHERE `id` = '.intval($rule);
            $error = acym_query($query) < 0 || $error;
            $i++;
        }

        acym_sendAjaxResponse('', [], !$error);
    }

    public function test()
    {
        $ruleClass = new RuleClass();

        if ($ruleClass->getOrderingNumber() < 1) {
            acym_enqueueMessage(acym_translation('ACYM_NO_RULES'), 'error');

            $this->listing();

            return;
        }

        acym_increasePerf();
        $bounceHelper = new BounceHelper();
        $bounceHelper->report = true;

        if ($bounceHelper->init()) {
            if ($bounceHelper->connect()) {
                $this->runBounce = true;
            } else {
                $errors = $bounceHelper->getErrors();
                if (!empty($errors)) {
                    acym_enqueueMessage($errors, 'error');
                    $errorString = implode(' ', $errors);
                    $port = $this->config->get('bounce_port', '');
                    if (preg_match('#certificate#i', $errorString) && !$this->config->get('bounce_certif', false)) {
                        acym_enqueueMessage(acym_translationSprintf('ACYM_YOU_MAY_TURN_ON_OPTION', '<i>'.acym_translation('ACYM_SELF_SIGNED_CERTIFICATE').'</i>'), 'warning');
                    } elseif (!empty($port) && !in_array($port, ['993', '143', '110'])) {
                        acym_enqueueMessage(acym_translation('ACYM_BOUNCE_WRONG_PORT'), 'warning');
                    }
                }
            }
        }

        $this->listing();
    }

    public function process()
    {
        acym_increasePerf();

        $bounceHelper = new BounceHelper();
        $bounceHelper->report = true;
        if (!$bounceHelper->init()) {
            return;
        }
        if (!$bounceHelper->connect()) {
            acym_display($bounceHelper->getErrors(), 'error');

            return;
        }
        $disp = "<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />\n";
        $disp .= '<title>'.addslashes(acym_translation('ACYM_BOUNCE_PROCESS')).'</title>'."\n";
        $disp .= "<style>body{font-size:12px;font-family: Arial,Helvetica,sans-serif;padding-top:30px;}</style>\n</head>\n<body>";
        echo $disp;

        acym_display(acym_translationSprintf('ACYM_BOUNCE_CONNECT_SUCC', $this->config->get('bounce_username')), 'success');
        $nbMessages = $bounceHelper->getNBMessages();
        $nbMessagesReport = acym_translationSprintf('ACYM_NB_MAIL_MAILBOX', $nbMessages);
        acym_display($nbMessagesReport, 'info');

        if (empty($nbMessages)) {
            exit;
        }

        $bounceHelper->handleMessages();

        $cronHelper = new CronHelper();
        $cronHelper->messages[] = $nbMessagesReport;
        $cronHelper->detailMessages = $bounceHelper->messages;
        $cronHelper->saveReport();

        if ($this->config->get('bounce_max', 0) != 0 && $nbMessages > $this->config->get('bounce_max', 0)) {
            $url = acym_completeLink('bounces&task=process&continuebounce=1', true, true);
            if (acym_getVar('int', 'continuebounce')) {
                echo '<script type="text/javascript">document.location.href=\''.$url.'\';</script>';
            } else {
                echo '<div style="padding:20px;"><a href="'.$url.'">'.acym_translation('ACYM_CLICK_HANDLE_ALL_BOUNCES').'</a></div>';
            }
        }

        echo '</body></html>';
        while ($bounceHelper->obend-- > 0) {
            ob_start();
        }
        exit;
    }

    public function reinstall()
    {
        $ruleClass = new RuleClass();
        $ruleClass->cleanTable();

        $updateHelper = new UpdateHelper();
        $updateHelper->installBounceRules();

        return $this->listing();
    }

    public function config()
    {
        acym_redirect(acym_completeLink('configuration', false, true));
    }

    public function delete()
    {
        $rulesSelected = acym_getVar('array', 'elements_checked');

        if (in_array(RuleClass::FINAL_RULE_ID, $rulesSelected)) {
            acym_enqueueMessage(acym_translation('ACYM_CANNOT_DELETE_FINAL_RULE'), 'error');

            $key = array_search(RuleClass::FINAL_RULE_ID, $rulesSelected);
            if ($key !== false) {
                unset($rulesSelected[$key]);
            }
        }

        $ruleClass = new RuleClass();
        $ruleClass->delete($rulesSelected);

        $this->listing();
    }

    public function setInactive()
    {
        $rulesSelected = acym_getVar('array', 'elements_checked');
        $ruleClass = new RuleClass();
        $ruleClass->setInactive($rulesSelected);

        $this->listing();
    }

    public function setActive()
    {
        $rulesSelected = acym_getVar('array', 'elements_checked');
        $ruleClass = new RuleClass();
        $ruleClass->setActive($rulesSelected);

        $this->listing();
    }
}
