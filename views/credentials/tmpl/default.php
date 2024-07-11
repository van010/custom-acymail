<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

//ttdebug('$this->account',$this->account);
//ttdebug('$this->token',$this->token);
?>

<form action="<?php echo JRoute::_('index.php?option=com_tradingtechnologies');?>" id="adminForm" method="post" name="adminForm" class="form-validate uk-form uk-form-horizontal">
	<?php if(JVERSION >= 3 && JVERSION < 4) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo JHtmlSidebar::render(); ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php endif; ?>
		<div class="adminform">
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>    
		<div id="config-document" class="uk-grid uk-grid-medium uk-grid-margin uk-active">
			<div class="uk-width-large-1-2">
				<div class="uk-panel-box uk-margin-bottom">
					<legend class="uk-margin-remove"><?php echo JText::_('COM_TT_CREDENTIALS_TOKEN'); ?></legend>
					<table class="uk-table uk-table-striped uk-margin-remove">
						<tr>
							<td>expiration</td><td><?php if (isset($this->token->exp)) echo date('Y-m-d H:i:s', $this->token->exp); ?></td>
						</tr>
						<tr>
							<td>token</td><td><div style="white-space: pre-wrap; word-break: break-all;"><?php if (isset($this->token->token)) echo $this->token->token; ?></div></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="clr"></div>
	<?php if(JVERSION >= 3 && JVERSION < 4) : ?>
	</div>
	<?php endif; ?>
</form>
<?php
echo ttJsApi::writeJS();
?>