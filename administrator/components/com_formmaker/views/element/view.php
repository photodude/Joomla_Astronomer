<?php 
  
 /**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class formmakerViewElement extends JViewLegacy
{
	public function display($tpl=NULL)
	{
		$mainframe = JFactory::getApplication();
		$db			= JFactory::getDBO();
		$nullDate	= $db->getNullDate();

		$document	=  JFactory::getDocument();
		$document->setTitle(JText::_('Form Selection'));

		JHTML::_('behavior.modal');

		$template = $mainframe->getTemplate();

		$limitstart = JRequest::getVar('limitstart', '0', '', 'int');

		$lists = $this->_getLists();

		//Ordering allowed ?
		$ordering = ($lists['order'] == 'section_name' && $lists['order_Dir'] == 'ASC');

		$rows = $this->get('List');
		$page = $this->get('Pagination');
		JHTML::_('behavior.tooltip');
		?>
		<script>
		Joomla.tableOrdering=function ( order, dir, task ) {

    var form = document.adminForm;



    form.filter_order.value     = order;

    form.filter_order_Dir.value = dir;

    submitform( task );

}

function tableOrdering( order, dir, task ) {

    var form = document.adminForm;



    form.filter_order.value     = order;

    form.filter_order_Dir.value = dir;

    submitform( task );

}


		</script>
		<form action="index.php?option=com_formmaker&amp;task=element&amp;tmpl=component&amp;object=id" method="post"  id="adminForm" name="adminForm">

			<table>
				<tr>
					<td width="100%">
                <input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" placeholder="Search title" style="margin:0px" />
				<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.id('search').value='';this.form.submit();" data-original-title="Clear">
				<i class="icon-remove"></i></button>
					</td>
				</tr>
			</table>

			<table class="table table-striped" cellspacing="1">
			<thead>
				<tr>
					<th width="15">
						<?php echo JText::_( 'Num' ); ?>
					</th>
					<th width="550">
						<?php echo JHTML::_('grid.sort',   'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th>
						<?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $page->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++)
			{
				$row = &$rows[$i];

				$link 	= '';
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $page->getRowOffset( $i ); ?>
					</td>
					<td>
						<a style="cursor: pointer;" onclick="window.parent.jSelectChart('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""),$row->title); ?>', '<?php echo JRequest::getVar('object'); ?>');">
							<?php echo $row->title; ?></a>
					</td>
					<td>
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
		<?php
	}

	function _getLists()
	{
		$mainframe = JFactory::getApplication();

		// Initialize variables
		$db		= JFactory::getDBO();

		// Get some variables from the request
		$sectionid			= JRequest::getVar( 'sectionid', -1, '', 'int' );
		$redirect			= $sectionid;
		$option				= JRequest::getCmd( 'option' );
		$filter_order		= $mainframe->getUserStateFromRequest('articleelement.filter_order',		'filter_order',		'',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest('articleelement.filter_order_Dir',	'filter_order_Dir',	'',	'word');
		$filter_state		= $mainframe->getUserStateFromRequest('articleelement.filter_state',		'filter_state',		'',	'word');
		$catid				= $mainframe->getUserStateFromRequest('articleelement.catid',				'catid',			0,	'int');
		$filter_authorid	= $mainframe->getUserStateFromRequest('articleelement.filter_authorid',		'filter_authorid',	0,	'int');
		$filter_sectionid	= $mainframe->getUserStateFromRequest('articleelement.filter_sectionid',	'filter_sectionid',	-1,	'int');
		$limit				= $mainframe->getUserStateFromRequest('global.list.limit',					'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart			= $mainframe->getUserStateFromRequest('articleelement.limitstart',			'limitstart',		0,	'int');
		$search				= $mainframe->getUserStateFromRequest('articleelement.search',				'search',			'',	'string');
		$search				= JString::strtolower($search);

		// get list of categories for dropdown filter
		if ($filter_order == 'id' or $filter_order == 'group_id' or $filter_order == 'date' or $filter_order == 'ip'){
			$orderby 	= ' ORDER BY id';
		} else {
			$orderby 	= ' ORDER BY '. 
			 $filter_order .' '. $filter_order_Dir .', id';
		}	

		// get list of categories for dropdown filter
		$query = 'SELECT cc.id AS value, cc.title AS text, section' .
				' FROM #__categories AS cc' .
				' INNER JOIN #__sections AS s ON s.id = cc.section' .
				$orderby .
				' ORDER BY s.ordering, cc.ordering';

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;

		return $lists;
	}
}
?>
