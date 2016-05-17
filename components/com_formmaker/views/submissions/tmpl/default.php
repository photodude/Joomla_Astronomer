<?php
/**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 
	defined('_JEXEC') or die('Restricted access'); 
	@session_start();
	
	JHTML::_('behavior.tooltip');
	JHTML::_('behavior.calendar');
	JHTML::_('behavior.modal');
	JHtml::_('behavior.formvalidation');
	JHtml::_('behavior.switcher');
	JHtml::_('formbehavior.chosen', 'select');
	jimport('joomla.filesystem.path');
	jimport('joomla.filesystem.file');

	$input_get = JFactory::getApplication()->input;
	$db = JFactory::getDBO();
	
	$rows	= $this->rows;
	$lists = $this->lists;
	$pageNav = $this->pageNav;
	$labels = $this->sorted_labels;
	$label_titles = $this->label_titles;
	$group_id_s	= $this->rows_ord;
	$labels_id = $this->sorted_labels_id;
	$sorted_labels_type = $this->sorted_labels_type;
	$total_entries = $this->total_entries;
	$total_views = $this->total_views;
	$join_count = $this->join_count;
	$form_title = $this->form_title;

	$form_id = $input_get->getString('id', 0, 'get', 'int');
	$checked_ids = JRequest::getVar('checked_ids');
	$stats_fields = JRequest::getVar('stats_fields');
	$params = explode(',',JRequest::getVar('params'));
	$params = array_slice($params,0, count($params)-1);  

	$document = JFactory::getDocument();
	$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js');
	$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/noconflict.js');
	$document->addStyleSheet(JURI::root(true).'/components/com_formmaker/views/submissions/tmpl/style.css');

	$other_fileds = array('submit_id','submit_date', 'submitter_ip', 'username', 'useremail', 'payment_info');
	foreach($other_fileds as $other_filed)
		$$other_filed= false;

	if($checked_ids)
	{
		$checked_ids = explode(',',$checked_ids);
		$checked_ids 	= array_slice($checked_ids,0, count($checked_ids)-1);   			
		
		foreach($other_fileds as $other_filed)
		{
			if(in_array($other_filed, $checked_ids))
				$$other_filed= true;
		}
	}
	else
	$checked_ids = Array();
	
	$label_titles_copy=$label_titles;
	$language = JFactory::getLanguage();
	$language->load('com_formmaker', JPATH_SITE, null, true);	
	
	?>

<script type="text/javascript">
Joomla.tableOrdering=  function( order, dir, task ) 
{
    var form = document.adminForm;
    form.filter_order2.value     = order;
    form.filter_order_Dir2.value = dir;
    submitform( task );
}

function renderColumns()
{
	allTags=document.getElementsByTagName('*');
	
	for(curTag in allTags)
	{
		if(typeof(allTags[curTag].className)!="undefined")
		if(allTags[curTag].className.indexOf('_fc')>0)
		{
			curLabel=allTags[curTag].className.replace('_fc','');
			if(document.forms.adminForm.hide_label_list.value.indexOf('@'+curLabel+'@')>=0)
				allTags[curTag].style.display = 'none';
			else
				allTags[curTag].style.display = '';
		}
	}
}

function clickLabChB(label, ChB)
{
	document.forms.adminForm.hide_label_list.value=document.forms.adminForm.hide_label_list.value.replace('@'+label+'@','');
	if(document.forms.adminForm.hide_label_list.value=='') document.getElementById('ChBAll').checked=true;
	
	if(!(ChB.checked)) 
	{
		document.forms.adminForm.hide_label_list.value+='@'+label+'@';
		document.getElementById('ChBAll').checked=false;
	}
	renderColumns();
}

function toggleChBDiv(b)
{
	if(b)
	{
		sizes=window.getSize();
		document.getElementById("sbox-overlay").style.width=sizes.x+"px";
		document.getElementById("sbox-overlay").style.height=sizes.y+"px";
		document.getElementById("ChBDiv").style.left=Math.floor((sizes.x-350)/2)+"px";
		
		document.getElementById("ChBDiv").style.display="block";
		document.getElementById("sbox-overlay").style.display="block";
	}
	else
	{
		document.getElementById("ChBDiv").style.display="none";
		document.getElementById("sbox-overlay").style.display="none";
	}
}

function clickLabChBAll(ChBAll)
{
	<?php
	if(isset($labels))
	{
		$templabels=array_merge(array('submitid','submitdate','submitterip','submitterusername','submitteremail'),$labels_id);
		$label_titles=array_merge(array('ID','Submit date', 'Submitter\'s IP Address', 'Submitter\'s Username', 'Submitter\'s Email Address'),$label_titles);
	}
	?>

	if(ChBAll.checked)
	{ 
		document.forms.adminForm.hide_label_list.value='';

		for(i=0; i<=ChBAll.form.length; i++)
			if(typeof(ChBAll.form[i])!="undefined")
				if(ChBAll.form[i].type=="checkbox")
					ChBAll.form[i].checked=true;
	}
	else
	{
		document.forms.adminForm.hide_label_list.value='@<?php echo implode($templabels,'@@') ?>@'+'@payment_info@';

		for(i=0; i<=ChBAll.form.length; i++)
			if(typeof(ChBAll.form[i])!="undefined")
				if(ChBAll.form[i].type=="checkbox")
					ChBAll.form[i].checked=false;
	}

	renderColumns();
}

function remove_all()
{
	if(document.getElementById('startdate'))
	document.getElementById('startdate').value='';
	if(document.getElementById('enddate'))
	document.getElementById('enddate').value='';
	if(document.getElementById('id_search'))
	document.getElementById('id_search').value='';
	if(document.getElementById('ip_search'))
	document.getElementById('ip_search').value='';
	if(document.getElementById('username_search'))
	document.getElementById('username_search').value='';
	if(document.getElementById('useremail_search'))
	document.getElementById('useremail_search').value='';
	<?php
		$n=count($rows);
	
	for($i=0; $i < count($labels) ; $i++)
	{
	echo "
	if(document.getElementById('".$form_id.'_'.$labels_id[$i]."_search'))
		document.getElementById('".$form_id.'_'.$labels_id[$i]."_search').value='';
	";
	}
	?>
}

function show_hide_filter()
{	
	if(document.getElementById('fields_filter').style.display=="none")
	{
		document.getElementById('fields_filter').style.display='';
		document.getElementById('filter_img').src='<?php echo JURI::root(true) ?>/components/com_formmaker/images/filter_hide.png';
	}
	else
	{
		document.getElementById('fields_filter').style.display="none";
		document.getElementById('filter_img').src='<?php echo JURI::root(true) ?>/components/com_formmaker/images/filter_show.png';
	}
}
</script>


<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_formmaker">
	
	<div class="submission_params"> 
	<?php if(isset($form_id) and $form_id>0):?>	
	<?php if(in_array('title',$params)): ?>
	<div class="form_title"><strong><?php echo $form_title; ?></strong></div>
	<?php endif; ?>
	<div>
		<?php if(in_array('entries',$params)):?>
			<div class="reports" style="width: 80px;"><strong><?php echo addslashes(JText::_('WDF_ENTRIES')); ?></strong><br /><?php echo $total_entries; ?></div>
		<?php endif; if(in_array('views',$params)): ?>
			<div class="reports" style="width: 80px;"><strong><?php echo addslashes(JText::_('WDF_VIEWS')); ?></strong><br /><?php echo $total_views ?></div>
		<?php endif; if(in_array('conversion_rate',$params)): ?>
			<div class="reports" style="width: 112px;"><strong><?php echo addslashes(JText::_('WDF_CONVERSION_RATE')); ?></strong><br /><?php  if($total_views) echo round((($total_entries/$total_views)*100),2).'%'; else echo '0%' ?></div>
		<?php endif; if(in_array('csv',$params) || in_array('xml',$params)):?>
			<div <?php echo ((in_array('entries',$params) || in_array('views',$params) || in_array('conversion_rate',$params)) ? 'class="csv_xml"' : '') ?>>
			Export to
			<?php if(in_array('csv',$params)): ?>
			<input type="button" value="CSV" onclick="window.location='<?php echo ($_SERVER['QUERY_STRING'] ? $_SERVER['REQUEST_URI'].'&' : $_SERVER['REQUEST_URI']."/?"); ?>task=generate_csv&tmpl=component'" />&nbsp;
			<?php endif; ?>
			<?php if(in_array('xml',$params)): ?>
			<input type="button" value="XML" onclick="window.location='<?php echo ($_SERVER['QUERY_STRING'] ? $_SERVER['REQUEST_URI'].'&' : $_SERVER['REQUEST_URI']."/?"); ?>task=generate_xml&tmpl=component'" />&nbsp;
			<?php endif; ?>
			</div>
		<?php endif; ?>	
	</div>
	<?php if(in_array('search',$params) || in_array('pagination',$params)): ?>
	<div class="search_and_pagination">
		<?php if(in_array('search',$params)): ?>
		<div>
			<input type="hidden" name="hide_label_list" value="<?php  echo $lists['hide_label_list']; ?>" /> 
			<img id="filter_img" src="components/com_formmaker/images/filter_show.png" width="40" style="vertical-align:middle; cursor:pointer" onclick="show_hide_filter()"  title="Search by fields" />
			<input type="button" onclick="this.form.submit();" style="vertical-align:middle; cursor:pointer" value="<?php echo JText::_( 'Go' ); ?>" />	
			<input type="button" onclick="remove_all();this.form.submit();" style="vertical-align:middle; cursor:pointer" value="<?php echo JText::_( 'Reset' ); ?>" />
		</div>
		<div>
		<?php if($join_count) echo ($total_entries-$join_count).' of '.$total_entries.' submissions are not shown, as the field you sorted by is missing in those submissions.'; ?>
		</div>
		<?php endif; if(in_array('pagination',$params)): ?>
		<div class="submits_pagination">
			<?php echo $pageNav->getLimitBox(); ?>
		</div>	
		<?php endif; ?>			
	</div>
	<?php endif; ?>
	<?php endif; ?>
	</div>
	
	<div style="overflow-x:scroll;">
		<table class="submissions" width="100%">
			<thead>
				<tr>
					<th width="3%"><?php echo '#'; ?></th>
					<?php
					if($submit_id)
					{
						echo '<th width="4%" class="submitid_fc"';
						if(!(strpos($lists['hide_label_list'],'@submitid@')===false)) 
						echo 'style="display:none;"';
						echo '>';
						if(in_array('ordering',$params))
						echo JHTML::_('grid.sort', 'Id', 'group_id', @$lists['order_Dir'], @$lists['order'] );
						else
						echo 'Id';
						echo '</th>';
					}
					
					if($submit_date)
					{
						echo '<th width="150" align="center" class="submitdate_fc"';
						if(!(strpos($lists['hide_label_list'],'@submitdate@')===false)) 
						echo 'style="display:none;"';
						echo '>';
						if(in_array('ordering',$params))
						echo JHTML::_('grid.sort', 'Submit Date', 'date', @$lists['order_Dir'], @$lists['order'] );
						else
						echo 'Submit Date';
						echo '</th>';
					}
						
					if($submitter_ip)
					{
						echo '<th width="100" align="center" class="submitterip_fc"';
						if(!(strpos($lists['hide_label_list'],'@submitterip@')===false)) 
						echo 'style="display:none;"';
						echo '>';
						if(in_array('ordering',$params))
						echo JHTML::_('grid.sort', 'Submitter\'s IP Address', 'ip', @$lists['order_Dir'], @$lists['order'] );
						else
						echo 'Submitter\'s IP Address';
						echo '</th>';
					}
				 
					if($username)
					{
						echo '<th width="100" class="submitterusername_fc"';
						if(!(strpos($lists['hide_label_list'],'@submitterusername@')===false)) 
						echo 'style="display:none;"';
						echo '>';
						if(in_array('ordering',$params))
						echo JHTML::_('grid.sort', 'Submitter\'s Username', 'username', @$lists['order_Dir'], @$lists['order'] );
						else
						echo 'Submitter\'s Username';
						echo '</th>';
					}
					
					if($useremail)
					{
						echo '<th width="100" class="submitteremail_fc"';
						if(!(strpos($lists['hide_label_list'],'@submitteremail@')===false)) 
						echo 'style="display:none;"';
						echo '>';
						if(in_array('ordering',$params))
						echo JHTML::_('grid.sort', 'Submitter\'s Email Address', 'email', @$lists['order_Dir'], @$lists['order'] );
						else
						echo 'Submitter\'s Email Address';
						echo '</th>';
					} 
					 
					$n=count($rows);
					$ispaypal=false;
					for($i=0; $i < count($labels) ; $i++)
					{
						if(in_array($labels_id[$i], $checked_ids))
						{
							if(strpos($lists['hide_label_list'],'@'.$labels_id[$i].'@')===false)  $styleStr='';
							else $styleStr='style="display:none;"';
							
							$field_title=$label_titles_copy[$i];
								
							if($sorted_labels_type[$i]=='type_paypal_payment_status')		
							{	
								$ispaypal=true;
								echo '<th align="center" class="'.$labels_id[$i].'_fc" '.$styleStr.'>';
								if(in_array('ordering',$params))
								echo JHTML::_('grid.sort', $field_title, $labels_id[$i]."_field", @$lists['order_Dir'], @$lists['order'] );
								else
								echo $field_title;
								echo '</th>';
									
							}
							else
							{
								echo '<th align="center" class="'.$labels_id[$i].'_fc" '.$styleStr.'>';
								if(in_array('ordering',$params))
								echo JHTML::_('grid.sort', $field_title, $labels_id[$i]."_field", @$lists['order_Dir'], @$lists['order'] );
								else
								echo $field_title;
								echo '</th>';
							}
						}
					}
					if($payment_info)		
					{
						if(strpos($lists['hide_label_list'],'@payment_info@')===false)  
							$styleStr2='aa';
						else 
							$styleStr2='style="display:none;"';
						echo '<th class="payment_info_fc" '.$styleStr2.'>Payment Info</th>';
					}	
				?>
				</tr>
				<tr id="fields_filter" style="display:none">
					<th width="3%"></th>
					<?php if($submit_id): ?>
					<th width="4%" class="submitid_fc" <?php if(!(strpos($lists['hide_label_list'],'@submitid@')===false)) echo 'style="display:none;"';?> >
					<input type="text" name="id_search" id="id_search" value="<?php echo $lists['id_search'] ?>" onChange="this.form.submit();" style="width:50px"/>
					</th>
					<?php endif; 
					
					if($submit_date): ?>
					<th class="submitdate_fc" style="text-align:left; <?php if(!(strpos($lists['hide_label_list'],'@submitdate@')===false)) echo 'display:none;';?>" align="center"> 
					<table class="simple_table">
						<tr>
							<td>From:</td>
							<td><input class="inputbox" type="text" name="startdate" id="startdate" maxlength="10" value="<?php echo $lists['startdate'];?>" /> </td>
							<td><button class="btn" id="startdate_but"><i class="icon-calendar"></i></button></td>
						</tr>
						<tr>
							<td>To:</td>
							<td><input class="inputbox" type="text" name="enddate" id="enddate" maxlength="10" value="<?php echo $lists['enddate'];?>" /> </td>
							<td><button class="btn" id="enddate_but"><i class="icon-calendar"></i></button></td>
						</tr>
					</table>
					
					</th>
					<?php endif; 

					if($submitter_ip): ?>
					<th class="submitterip_fc"  <?php if(!(strpos($lists['hide_label_list'],'@submitterip@')===false)) echo 'style="display:none;"';?>>
					 <input type="text" name="ip_search" id="ip_search" value="<?php echo $lists['ip_search'] ?>" onChange="this.form.submit();" style="width:96%"/>
					</th>
					<?php endif; 		 
					if($username): ?>		
					<th width="100"class="submitterusername_fc"  <?php if(!(strpos($lists['hide_label_list'],'@submitterusername@')===false)) echo 'style="display:none;"';?>>
						 <input type="text" name="username_search" id="username_search" value="<?php echo $lists['username_search'] ?>" onChange="this.form.submit();" style="width:150px"/>
						</th>
					<?php endif; 	
					if($useremail): ?>		
						<th width="100"class="submitteremail_fc"  <?php if(!(strpos($lists['hide_label_list'],'@submitteremail@')===false)) echo 'style="display:none;"'; ?>>
						 <input type="text" name="useremail_search" id="useremail_search" value="<?php echo $lists['useremail_search'] ?>" onChange="this.form.submit();" style="width:150px"/>
						</th>
					<?php endif; 
					
						$ka_fielderov_search=false;
						
						if($lists['id_search'] || $lists['ip_search'] || $lists['startdate'] || $lists['enddate'] || $lists['username_search'] || $lists['useremail_search']){
							$ka_fielderov_search=true;
						}
					
						for($i=0; $i < count($labels) ; $i++)
						{
							if(strpos($lists['hide_label_list'],'@'.$labels_id[$i].'@')===false)  
								$styleStr='';
							else 
								$styleStr='style="display:none;"';
							
							if(!$ka_fielderov_search)
								if($lists[$form_id.'_'.$labels_id[$i].'_search'])
								{
									$ka_fielderov_search=true;
								} 
				
							if(in_array($labels_id[$i], $checked_ids))		
							switch($sorted_labels_type[$i])
							{
								case 'type_mark_map': echo '<th class="'.$labels_id[$i].'_fc" '.$styleStr.'>'.'</th>'; break;
								case 'type_paypal_payment_status':
								echo '<th class="'.$labels_id[$i].'_fc" '.$styleStr.'>';
								?>
								<select name="<?php echo $form_id.'_'.$labels_id[$i]; ?>_search" id="<?php echo $form_id.'_'.$labels_id[$i]; ?>_search" onChange="this.form.submit();" value="<?php echo $lists[$form_id.'_'.$labels_id[$i].'_search']; ?>" >
									<option value="" ></option>
									<option value="canceled" >Canceled</option>
									<option value="cleared" >Cleared</option>
									<option value="cleared by payment review" >Cleared by payment review</option>
									<option value="completed" >Completed</option>
									<option value="denied" >Denied</option>
									<option value="failed" >Failed</option>
									<option value="held" >Held</option>
									<option value="in progress" >In progress</option>
									<option value="on hold" >On hold</option>
									<option value="paid" >Paid</option>
									<option value="partially refunded" >Partially refunded</option>
									<option value="pending verification" >Pending verification</option>
									<option value="placed" >Placed</option>
									<option value="processing" >Processing</option>
									<option value="refunded" >Refunded</option>
									<option value="refused" >Refused</option>
									<option value="removed" >Removed</option>
									<option value="returned" >Returned</option>
									<option value="reversed" >Reversed</option>
									<option value="temporary hold" >Temporary hold</option>
									<option value="unclaimed" >Unclaimed</option>
								</select>	
								<script> 
								var element = document.getElementById('<?php echo $form_id.'_'.$labels_id[$i]; ?>_search');
								element.value = '<?php echo $lists[$form_id.'_'.$labels_id[$i].'_search']; ?>';
								</script>
								<?php				
								echo '</th>';

								break;
								default : 	
								echo '<th class="'.$labels_id[$i].'_fc" '.$styleStr.'>'.'<input name="'.$form_id.'_'.$labels_id[$i].'_search" id="'.$form_id.'_'.$labels_id[$i].'_search" type="text" value="'.$lists[$form_id.'_'.$labels_id[$i].'_search'].'"  onChange="this.form.submit();" style="width:96%">'.'</th>'; 
								break;			
							
							}
						}
						if($payment_info)
						{
							if(strpos($lists['hide_label_list'],'@payment_info@')===false)  
								$styleStr2='';
							else 
								$styleStr2='style="display:none;"';
							echo '<th class="payment_info_fc" '.$styleStr2.'></th>';
						}	
					?>
				</tr>
				</thead>
		<?php if(in_array('pagination',$params)): ?>
			<tfoot>
				<tr>
					<td colspan="100"> <?php echo $pageNav->getListFooter(); ?>

					</td>
				</tr>
			</tfoot>
		<?php endif; 
	$k = 0;
	$m=count($labels);

	for($www=0, $qqq=count($group_id_s); $www < $qqq ; $www++)
	{	
		$i=$group_id_s[$www];

		$temp= array();
		for($j=0; $j < $n ; $j++)
		{
			$row = &$rows[$j];
			if($row->group_id==$i)
			{
				array_push($temp, $row);
			}
		}
		$f=$temp[0];
		$date=$f->date;
		$ip = $f->ip;
		$user_id = JFactory::getUser($f->user_id);
		$user_name = $user_id->username;
		$user_email= $user_id->email;

		$checked 	= JHTML::_('grid.id', $www, $group_id_s[$www]);	
		?>

		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $www+1+$pageNav->limitstart;?></td>
			<?php

			if($submit_id)
			{
				if(strpos($lists['hide_label_list'],'@submitid@')===false)
				echo '<td align="center" class="submitid_fc">'.$f->group_id.'</td>';
				else 
				echo '<td align="center" class="submitid_fc" style="display:none;">'.$f->group_id.'</td>';
			}
			
			if($submit_date)
			{
				if(strpos($lists['hide_label_list'],'@submitdate@')===false)
				echo '<td align="center" class="submitdate_fc">'.$date.'</td>';
				else 
				echo '<td align="center" class="submitdate_fc" style="display:none;">'.$date.'</td>'; 
			}

			if($submitter_ip)
			{
				
				if(strpos($lists['hide_label_list'],'@submitterip@')===false)
				echo '<td align="center" class="submitterip_fc">'.$ip.'</td>';
				else 
				echo '<td align="center" class="submitterip_fc" style="display:none;">'.$ip.'</td>';
			}

			if($username)
			{
				if(strpos($lists['hide_label_list'],'@submitterusername@')===false)
				echo '<td align="center" class="submitterusername_fc" style="display:table-cell;">'.$user_name.'</td>';
				else 
				echo '<td align="center" class="submitterusername_fc" style="display:none;">'.$user_name.'</td>';
			}

			if($useremail)
			{
				if(strpos($lists['hide_label_list'],'@submitteremail@')===false)
				echo '<td align="center" class="submitteremail_fc" style="display:table-cell;">'.$user_email.'</td>';
				else 
				echo '<td align="center" class="submitteremail_fc" style="display:none;">'.$user_email.'</td>';
			}
			
			$ttt=count($temp);
			for($h=0; $h < $m ; $h++)
			{	
				if(in_array($labels_id[$h], $checked_ids))	
				{
					$not_label=true;
					for($g=0; $g < $ttt ; $g++)
					{			
						$t = $temp[$g];
						if(strpos($lists['hide_label_list'],'@'.$labels_id[$h].'@')===false)
							$styleStr='';
						else
							$styleStr='style="display:none;"';
						if($t->element_label==$labels_id[$h])
						{
							if(strpos($t->element_value,"***map***"))
							{
								$map_params=explode('***map***',$t->element_value);
								
								$longit	=$map_params[0];
								$latit	=$map_params[1];
								
								echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'><a class="modal"  href="index.php?option=com_formmaker&task=show_map&long='.$longit.'&lat='.$latit.'&tmpl=component" rel="{handler: \'iframe\', size: {x:630, y: 570}}">'.'Show on Map'."</a></td>";
							}
							else
								if(strpos($t->element_value,"*@@url@@*"))
								{
									echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'>'; 
									$new_files=explode("*@@url@@*", $t->element_value);

									foreach($new_files as $new_file)
									if($new_file)
									{
										$new_filename=explode('/', $new_file);
										$new_filename=$new_filename[count($new_filename)-1];
										if(strpos(strtolower($new_filename), 'jpg')!== false or strpos(strtolower($new_filename), 'png')!== false or strpos(strtolower($new_filename), 'gif')!== false or strpos(strtolower($new_filename), 'jpeg')!== false)
											echo  '<a href="'.$new_file.'" class="modal">'.$new_filename."</a></br>";
										else
											echo  '<a target="_blank" href="'.$new_file.'">'.$new_filename."</a></br>";
									}
									echo "</td>";		
								}
								else
									if(strpos($t->element_value,"@@@")>-1 || $t->element_value=="@@@" || $t->element_value=="@@@@@@@@@" || $t->element_value=="::" || $t->element_value==":" || $t->element_value=="--")
									{
										echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'><pre style="font-family:inherit">'.str_replace(array("@@@",":","-")," ",$t->element_value).'</pre></td>';
									}	
									else
										if(strpos($t->element_value,"***grading***"))
										{	
											$new_filename= str_replace("***grading***",'', $t->element_value);	
											$grading = explode(":",$new_filename);                        
											
											$items_count = sizeof($grading)-1;
											$items = "";
											$total = "";
										
											for($k=0;$k<$items_count/2;$k++)
											{
												$items .= $grading[$items_count/2+$k].": ".$grading[$k]."</br>";
												$total += $grading[$k];
											}
											
											$items .="Total: ".$total;
										
											echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'><pre style="font-family:inherit">'.$items.'</pre></td>';
										}		
										else
											if(strpos($t->element_value,"***matrix***"))
											{	
											
												echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'><a class="modal"  href="index.php?option=com_formmaker&task=show_matrix&matrix_params='.$t->element_value.'&tmpl=component" rel="{handler: \'iframe\', size: {x:650, y: 450}}">'.'Show Matrix'.'</a></td>';
											}
											else
											{
												if(strpos($t->element_value, "***quantity***"))
													$t->element_value = str_replace("***quantity***"," ",$t->element_value);

												if(strpos($t->element_value,"***property***"))
													$t->element_value = str_replace("***property***"," ",$t->element_value);
									
												echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'><pre style="font-family:inherit; white-space: pre;">'.str_replace("***br***",'<br>', $t->element_value).'</pre></td>';
											}	
											$not_label=false;
						}
					}
					if($not_label)
							echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'></td>';
				}
			}
			
			if($payment_info)
			{
				if(strpos($lists['hide_label_list'],'@payment_info@')===false) 
					$styleStr='';
				else 
					$styleStr='style="display:none;"';
				echo  '<td align="center" class="payment_info_fc" '.$styleStr.'>		
				<a class="modal" href="index.php?option=com_formmaker&amp;task=paypal_info&amp;tmpl=component&amp;id='.$i.'" rel="{handler: \'iframe\', size: {x:703, y: 550}}"><img src="components/com_formmaker/images/info.png" /></a></td>';
			}
		?>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
		</table>
	</div>

	<?php
	$is_stats=false;
	if(in_array('stats',$params))
	{
		foreach($sorted_labels_type as $key => $label_type)
		{
			if($label_type=="type_checkbox" || $label_type=="type_radio" || $label_type=="type_own_select" || $label_type=="type_country" || $label_type=="type_paypal_select" || $label_type=="type_paypal_radio" || $label_type=="type_paypal_checkbox" || $label_type=="type_paypal_shipping")
			{
				$is_stats=true;
				break;
				
			}
		}
		
		if($is_stats)
		{
			?>
			<br><br>
			<h1 style="border-bottom: 1px solid; color:#000 !important;">Statistics</h1>
			
			<table class="stats">
				<tr valign="top">
					<td class="key" style="vertical-align: middle;">
						<label> <?php echo JText::_( 'Select a Field' ); ?>: </label>
					</td>
					<td>
						<select id="stat_id">
						<option value="">Select a Field</option>;
						<?php 
							$stats_fields = explode(',',$stats_fields);	
							$stats_fields 	= array_slice($stats_fields,0, count($stats_fields)-1);
							
							foreach($sorted_labels_type as $key => $label_type)
							{
								if(($label_type=="type_checkbox" || $label_type=="type_radio" || $label_type=="type_own_select" || $label_type=="type_country" || $label_type=="type_paypal_select" || $label_type=="type_paypal_radio" || $label_type=="type_paypal_checkbox" || $label_type=="type_paypal_shipping") && in_array($labels_id[$key], $stats_fields))
								{
									echo '<option value="'.$labels_id[$key].'">'.$label_titles_copy[$key].'</option>';
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr valign="middle">
					<td class="key" style="vertical-align: middle;">
						<label> <?php echo JText::_( 'Select a Date' ); ?>: </label>
					</td>
					<td>
						From:<input class="inputbox" type="text" name="startstats" id="startstats"  maxlength="10" />
							<button class="btn" id="startstats_but"><i class="icon-calendar"></i></button>
						To: <input class="inputbox" type="text" name="endstats" id="endstats"  maxlength="10" />
							<button class="btn" id="endstats_but"><i class="icon-calendar"></i></button>
					</td>
				</tr>
				<tr valign="top">
					<td class="key" style="vertical-align: middle;" colspan="2">
					<input type="button" onclick="show_stats()" style="vertical-align:middle; cursor:pointer" value="Show">
					</td>
				</tr>
				
			</table>

			<div id="div_stats">
			</div>
			
			<script>
			Calendar.setup({
					inputField: "startstats",
					ifFormat: "%Y-%m-%d",
					button: "startstats_but",
					align: "Tl",
					singleClick: true,
					firstDay: 0
					});
					
			Calendar.setup({
					inputField: "endstats",
					ifFormat: "%Y-%m-%d",
					button: "endstats_but",
					align: "Tl",
					singleClick: true,
					firstDay: 0
					});
					
			function show_stats()
			{
			wdformjQuery('#div_stats').html('<div id="saving"><div id="saving_text">Loading</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');
			
			
			if(wdformjQuery('#stat_id').val()!="")
				wdformjQuery('#div_stats').load('index.php?option=com_formmaker&view=stats&form_id=<?php echo $form_id;?>&id='+wdformjQuery('#stat_id').val()+'&from='+wdformjQuery('#startstats').val()+'&to='+wdformjQuery('#endstats').val()+"&tmpl=component");
			else
				wdformjQuery('#div_stats').html("Please select the field!")

			}
			</script>
			<?php
		}
	}
	?>

	<input type="hidden" name="boxchecked" value="0">
	<input type="hidden" name="filter_order2" value="<?php echo $lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir2" value="<?php echo $lists['order_Dir']; ?>" />

</form>
<?php if($submit_date): ?>
<script>
	Calendar.setup({
			inputField: "startdate",
			ifFormat: "%Y-%m-%d",
			button: "startdate_but",
			align: "Tl",
			singleClick: true,
			firstDay: 0
			});
			
	Calendar.setup({
			inputField: "enddate",
			ifFormat: "%Y-%m-%d",
			button: "enddate_but",
			align: "Tl",
			singleClick: true,
			firstDay: 0
			});
</script>
<?php endif; ?>			
			
<?php if($ka_fielderov_search):?> 
<script>
document.getElementById('fields_filter').style.display='';
document.getElementById('filter_img').src='<?php echo JURI::root(true) ?>/components/com_formmaker/images/filter_hide.png';
</script>
<?php endif; ?>	
