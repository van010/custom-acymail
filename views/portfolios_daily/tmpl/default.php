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

<form action="<?php echo JRoute::_('index.php?option=com_tradingtechnologies&view=portfolios_daily');?>" method="post" name="adminForm" class="uk-form" id="adminForm">
	<?php if(JVERSION >= 3 && JVERSION < 4) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo JHtmlSidebar::render(); ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php endif; ?>
	<div class="adminform">

	<div id="filter-bar">
		<div class="filter-search uk-float-left uk-align-center uk-form-row uk-hidden">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
			<div class="uk-button-group">
				<button type="submit" class="uk-button uk-icon-search" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
				<button type="button" class="uk-button uk-icon-remove" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();" />
			</div>
		</div>

		<div class="filter-select uk-float-right uk-hidden">
			<select name="filter_account" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_TT_FILTER_ACCOUNTS');?></option>
				<?php foreach ($this->accounts as $i => $account) { ?>
				<option value="<?php echo $account->name; ?>" <?php if ($this->state->get('filter.account') == $account->name) echo 'selected'; ?>><?php echo $account->name; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="uk-clearfix"> </div>

	<table class="adminlist uk-table" id="inventorylist">
		<thead>
			<tr>
				<th width="30%">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'pd.date', $listDirn, $listOrder); ?>
				</th>
				<th width="40%" class="uk-text-right">
					<?php echo JHtml::_('grid.sort', 'COM_TT_PNL', 'pd.pnl', $listDirn, $listOrder); ?>
				</th>
				<th width="40%" class="uk-text-right">
					<?php echo JHtml::_('grid.sort', 'COM_TT_AMOUNT', 'pd.amount', $listDirn, $listOrder); ?>
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
			<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="">
					<?php echo $item->date; ?>
				</td>
				<td class="uk-text-right">
					<?php echo number_format($item->pnl, 2, ',', '.'); ?>
				</td>
				<td class="uk-text-right">
					<?php echo number_format($item->amount, 2, ',', '.'); ?>
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
