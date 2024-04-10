<?php
if (acym_level(ACYM_ENTERPRISE)) {
    ?>
	<div class="acym_area padding-vertical-1 padding-horizontal-2">
		<div class="acym__title acym__title__secondary"><?php echo acym_translation('ACYM_BOUNCE_HANDLING'); ?>
			<a class="acym__external__link margin-left-1" href="<?php echo acym_completeLink('bounces&task=bounces'); ?>">
                <?php echo acym_translation('ACYM_MANAGE_BOUNCE_RULES'); ?>
			</a>
		</div>

		<div class="grid-x margin-y">
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_BOUNCE_EMAIL').acym_info('ACYM_BOUNCE_ADDRESS_DESC'); ?></span>
				<input class="cell medium-4"
					   type="text"
					   placeholder="<?php echo acym_translation('ACYM_BOUNCE_EMAIL_PLACEHOLDER'); ?>"
					   value="<?php echo acym_escape($this->config->get('bounce_email')); ?>"
					   id="bounceAddress2" />
			</label>
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_SMTP_SERVER'); ?></span>
				<input class="cell medium-4" type="text" name="config[bounce_server]" value="<?php echo acym_escape($this->config->get('bounce_server')); ?>">
			</label>
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_SMTP_PORT').acym_info('ACYM_BOUNCE_PORT_DESC'); ?></span>
				<input
						class="cell medium-2"
						type="number"
						name="config[bounce_port]"
						value="<?php echo acym_escape($this->config->get('bounce_port')); ?>">
			</label>
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_CONNECTION_METHOD').acym_info('ACYM_CONNECTION_METHOD_DESC'); ?></span>
				<div class="cell medium-2">
                    <?php
                    $connectionMethods = [
                        "" => "---",
                        'imap' => 'IMAP ('.acym_translation('ACYM_RECOMMENDED').')',
                        'pop3' => 'POP3',
                        'pear' => 'POP3 ('.acym_translation('ACYM_WITHOUT_IMAP_EXT').')',
                    ];

                    echo acym_select(
                        $connectionMethods,
                        'config[bounce_connection]',
                        $this->config->get('bounce_connection', 'imap'),
                        [
                            'class' => 'acym__select',
                            'acym-data-infinite' => '',
                        ],
                        '',
                        '',
                        'acym__config__bounce__connection'
                    );
                    ?>
				</div>
			</label>
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_SMTP_SECURE'); ?></span>
				<div class="cell medium-2">
                    <?php
                    $secureMethods = [
                        "" => "---",
                        "ssl" => "SSL",
                        "tls" => "TLS",
                    ];

                    echo acym_select(
                        $secureMethods,
                        'config[bounce_secured]',
                        $this->config->get('bounce_secured', 'ssl'),
                        [
                            'class' => 'acym__select',
                            'acym-data-infinite' => '',
                        ],
                        "",
                        "",
                        'acym__config__bounce__secure_method'
                    );
                    ?>
				</div>
			</label>
			<div class="cell grid-x">
                <?php echo acym_switch('config[bounce_certif]', $this->config->get('bounce_certif', 1), acym_translation('ACYM_SELF_SIGNED_CERTIFICATE'), [], 'medium-3'); ?>
			</div>
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_SMTP_USERNAME'); ?></span>
				<input class="cell medium-4" type="text" name="config[bounce_username]" value="<?php echo acym_escape($this->config->get('bounce_username')); ?>">
			</label>
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_SMTP_PASSWORD'); ?></span>
				<input class="cell medium-4" type="text" name="config[bounce_password]" value="<?php echo str_repeat('*', strlen($this->config->get('bounce_password'))); ?>">
			</label>
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_CONNECTION_TIMEOUT_SECOND'); ?></span>
				<input class="cell medium-2" type="text" name="config[bounce_timeout]" value="<?php echo acym_escape($this->config->get('bounce_timeout', 10)); ?>">
			</label>
			<label class="cell grid-x">
				<span class="cell medium-3"><?php echo acym_translation('ACYM_MAX_NUMBER_EMAILS'); ?></span>
				<input class="cell medium-2" type="text" name="config[bounce_max]" value="<?php echo acym_escape($this->config->get('bounce_max', 100)); ?>">
			</label>
			<div class="cell grid-x">
                <?php echo acym_switch('config[auto_bounce]', $this->config->get('auto_bounce'), acym_translation('ACYM_ENABLE_AUTO_BOUNCE'), [], 'medium-3'); ?>
			</div>
			<div class="cell grid-x grid-margin-x" id="acym__configuration__bounce__auto_bounce__configuration" <?php echo $this->config->get(
                'auto_bounce'
            ) ? '' : "style='display: none'"; ?>>
				<div class="cell grid-x">
					<label class="cell medium-3" for="delayvalue3"><?php echo acym_translation('ACYM_FREQUENCY'); ?></label>
					<div class="cell medium-9">
                        <?php
                        $delayTypeBounceAuto = $data['typeDelay'];
                        echo $delayTypeBounceAuto->display('config[auto_bounce_frequency]', $this->config->get('auto_bounce_frequency', 21600), 1);
                        ?>
					</div>
					<span class="cell medium-3"><?php echo acym_translation('ACYM_LAST_RUN'); ?></span>
                    <?php $bounceLast = $this->config->get('auto_bounce_last'); ?>
					<span class="cell medium-9"><?php echo empty($bounceLast) ? '-' : acym_date($bounceLast, acym_getDateTimeFormat()); ?></span>
					<span class="cell medium-3"><?php echo acym_translation('ACYM_NEXT_RUN_TIME'); ?></span>
                    <?php $bounceNext = $this->config->get('auto_bounce_next'); ?>
					<span class="cell medium-9"><?php echo empty($bounceNext) ? '-' : acym_date($bounceNext, acym_getDateTimeFormat()); ?></span>
					<span class="cell medium-3"><?php echo acym_translation('ACYM_REPORT'); ?></span>
					<span class="cell medium-9"><?php echo $this->config->get('auto_bounce_report'); ?></span>
				</div>
			</div>
			<div class="cell grid-x acym__mailbox__edition__configuration__test">
				<button type="button"
						data-task="testMailboxAction"
						class="button button-secondary cell medium-4 large-shrink"
						id="acym__mailbox__edition__configuration__test-test">
                    <?php echo acym_translation('ACYM_TEST_CONNECTION'); ?>
				</button>
				<span class="acymicon-spin acymicon-circle-o-notch acym_vcenter cell shrink margin-left-1" id="acym__mailbox__edition__configuration__test-loader"></span>
				<span class="cell shrink acym_vcenter margin-left-1" id="acym__mailbox__edition__configuration__test-result"></span>
				<span class="acymicon-check-circle acym__color__green acymicon-times-circle acym__color__red cell shrink acym_vcenter"
					  id="acym__mailbox__edition__configuration__test-icon"></span>
			</div>
		</div>
	</div>
	<div class="acym__content acym_area padding-horizontal-2 acym__configuration__advanced">
		<div class="cell grid-x acym__configuration__showmore-head">
			<div class="acym__title acym__title__secondary cell auto margin-bottom-0"><?php echo acym_translation('ACYM_WHAT_IS_BOUNCE_HANDLING'); ?></div>
			<div class="cell shrink">
                <?php echo acym_showMore('acym__configuration__bounce__advanced__content'); ?>
			</div>
		</div>
		<div id="acym__configuration__bounce__advanced__content" style="display: none;">
            <?php
            echo '<div class="margin-top-1">';
            include acym_getView('bounces', 'splashscreen');
            echo '</div>';
            ?>
		</div>
	</div>
<?php }
if (!acym_level(ACYM_ENTERPRISE)) {
    $data['isEnterprise'] = false;
    echo '<div class="margin-top-1">';
    include acym_getView('bounces', 'splashscreen');
    echo '</div>';
} ?>
