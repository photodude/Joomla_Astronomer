<?php
/**
 * @version    CVS: 1.0.4
 * @package    Com_Astronomer
 * @author     Troy Hall <troy@jowwow.net>
 * @copyright  2016 Troy Hall
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_astronomer', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_astronomer/js/form.js');

/*
if($this->item->state == 1){
	$state_string = 'Publish';
	$state_value = 1;
} else {
	$state_string = 'Unpublish';
	$state_value = 0;
}
if($this->item->id) {
	$canState = JFactory::getUser()->authorise('core.edit.state','com_astronomer.entry');
} else {
	$canState = JFactory::getUser()->authorise('core.edit.state','com_astronomer.entry.'.$this->item->id);
}*/
?>
<script type="text/javascript">
	if (jQuery === 'undefined') {
		document.addEventListener("DOMContentLoaded", function (event) {
			jQuery('#form-list').submit(function (event) {
				
			});

			
		});
	} else {
		jQuery(document).ready(function () {
			jQuery('#form-list').submit(function (event) {
				
			});

			
		});
	}
</script>

<div class="list-edit front-end-edit">
	<?php if (!empty($this->item->id)): ?>
		<h1>Edit <?php echo $this->item->id; ?></h1>
	<?php else: ?>
		<h1>Add</h1>
	<?php endif; ?>

	<form id="form-list"
		  action="<?php echo JRoute::_('index.php?option=com_astronomer&task=list.save'); ?>"
		  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
		
		<div class="control-group">
			<div class="controls">

				<?php if ($this->canSave): ?>
					<button type="submit" class="validate btn btn-primary">
						<?php echo JText::_('JSUBMIT'); ?>
					</button>
				<?php endif; ?>
				<a class="btn"
				   href="<?php echo JRoute::_('index.php?option=com_astronomer&task=listform.cancel'); ?>"
				   title="<?php echo JText::_('JCANCEL'); ?>">
					<?php echo JText::_('JCANCEL'); ?>
				</a>
			</div>
		</div>

		<input type="hidden" name="option" value="com_astronomer"/>
		<input type="hidden" name="task"
			   value="listform.save"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
