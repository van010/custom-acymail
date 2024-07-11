<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;
?>
<div class="uk-width-large-1-2">
	<div class="uk-panel-box uk-margin-bottom">
		<legend><?php echo JText::_('COM_TT_CONFIG_CREDENTIALS'); ?></legend>
		<div class="uk-form-row">
			<?php echo $this->form->getLabel('tt_app_key'); ?>
			<div class="uk-form-controls">
				<?php echo $this->form->getInput('tt_app_key'); ?>
			</div>
		</div>
		<div class="uk-form-row">
			<?php echo $this->form->getLabel('tt_app_secret'); ?>
			<div class="uk-form-controls">
				<?php echo $this->form->getInput('tt_app_secret'); ?>
			</div>
		</div>
		<div class="uk-form-row">
			<?php echo $this->form->getLabel('tt_environment'); ?>
			<div class="uk-form-controls">
				<?php echo $this->form->getInput('tt_environment'); ?>
			</div>
		</div>
		<div class="uk-form-row">
			<?php echo $this->form->getLabel('tt_company'); ?>
			<div class="uk-form-controls">
				<?php echo $this->form->getInput('tt_company'); ?>
			</div>
		</div>
	</div>
</div>
<div class="uk-width-large-1-2">
	<div class="uk-panel-box uk-margin-bottom">
		<legend><?php echo JText::_('COM_TT_CONFIG_SYSTEM'); ?></legend>
		<div class="uk-form-row">
			<?php echo $this->form->getLabel('debug'); ?>
			<div class="uk-form-controls">
				<?php echo $this->form->getInput('debug'); ?>
			</div>
		</div>
		<div class="uk-form-row">
			<?php echo $this->form->getLabel('logging'); ?>
			<div class="uk-form-controls">
				<?php echo $this->form->getInput('logging'); ?>
			</div>
		</div>
	</div>
</div>