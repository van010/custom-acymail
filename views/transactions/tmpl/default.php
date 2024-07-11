<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik K�nnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik K�nnemann
 */

defined('_JEXEC') or die;

JHtml::_('script','system/multiselect.js');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

JFormHelper::addFieldPath(JPATH_TT_ADMINISTRATOR.DS.'models'.DS.'fields');

?>

<form action="<?php echo JRoute::_('index.php?option=com_tradingtechnologies&view=transactions');?>" method="post" name="adminForm" class="uk-form" id="adminForm">
	<?php if(JVERSION >= 3 && JVERSION < 4) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo JHtmlSidebar::render(); ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php endif; ?>
	<div class="adminform">

	<div id="filter-bar">
		<div class="filter-search uk-float-left uk-align-center uk-form-row">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
			<div class="uk-button-group">
				<button type="submit" class="uk-button uk-icon-search" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
				<button type="button" class="uk-button uk-icon-remove" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();" />
			</div>
		</div>

		<div class="filter-select uk-float-right">
			<select name="filter_published" class="inputbox uk-hidden" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_TT_FILTER_ACTIVE');?></option>
				<option value="true"><?php echo JText::_('JYES');?></option>
				<option value="false"><?php echo JText::_('JNO');?></option>
			</select>
		</div>
	</div>
	<div class="uk-clearfix"> </div>

	<table class="adminlist uk-table" id="inventorylist">
		<thead>
			<tr>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JDATE', 't.created', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_TT_CUSTOMER', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_TT_INVEST'); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('COM_TT_PAYOUT'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->transactions as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="">
					<?php echo $item->created; ?>
				</td>
				<td>
					<?php echo $item->customer_name; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->invest; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->payout; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	
	</div>

</form>
