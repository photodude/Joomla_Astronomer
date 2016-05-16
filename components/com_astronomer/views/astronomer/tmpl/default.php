<?php
/**
 * @version    CVS: 1.0.1
 * @package    Com_Astronomer
 * @author     Troy Hall <troy@jowwow.net>
 * @copyright  2016 Troy Hall
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_astronomer.' . $this->item->id);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_astronomer' . $this->item->id)) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

	<div class="item_fields">
		<table class="table">
			<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_HUMANDATE'); ?></th>
			<td><?php echo $this->item->humandate; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_DESIGNATION'); ?></th>
			<td><?php echo $this->item->designation; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_YEAR'); ?></th>
			<td><?php echo $this->item->year; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_MONTH'); ?></th>
			<td><?php echo $this->item->month; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_DAY'); ?></th>
			<td><?php echo $this->item->day; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_MAG'); ?></th>
			<td><?php echo $this->item->mag; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_OBSERVATORY'); ?></th>
			<td><?php echo $this->item->observatory; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_ENTRY'); ?></th>
			<td><?php echo $this->item->entry; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_ASTRONOMER_FORM_LBL_ASTRONOMER_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
</tr>

		</table>
	</div>
	<?php if($canEdit && $this->item->checked_out == 0): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_astronomer&task=astronomer.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_ASTRONOMER_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_astronomer.astronomer.'.$this->item->id)):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_astronomer&task=astronomer.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_ASTRONOMER_DELETE_ITEM"); ?></a>
								<?php endif; ?>
	<?php
else:
	echo JText::_('COM_ASTRONOMER_ITEM_NOT_LOADED');
endif;
