<?php
/**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 
defined('_JEXEC') or die('Restricted access'); 

	$choices	= $this->choices;
	$colors=array('#D6DEE9','#F5DFCA');
	$choices_colors=array('#6095CB','#FF9630');
	$choices_labels=array();
	$choices_count=array();
	$all=count($choices);
	$unanswered=0;	
	?>
	<style>
	.adminlist
	{
		border-collapse: separate;
		font-size:14px;
		width: 100%;
	}
	
	.adminlist th
	{
		font-size:14px;
		padding: 10px 0;
	}
	
	.adminlist td
	{
		border: none !important;
	}
	
	.adminlist td:first-child
	{
		
		color:#fff;
		padding: 4px;
	}

	.label0
	{
		background:#6095CB;
		border: 2px solid #6095CB;
	}
	.label1
	{
		background:#FF9630;
		border: 2px solid #FF9630;
	}
	.bordered0:before
	{
		content: " ";
		width:20px;
		height:16px;
		margin-left: -15px;
		background:#D6DEE9;
		display: inline-block;
		-webkit-transform: scale(1) rotate(0deg) translateX(0px) translateY(0px) skewX(-25deg) skewY(0deg);
	}
	
	.bordered1:before
	{
		content: " ";
		width:20px;
		height:16px;
		margin-left: -15px;
		background:#F5DFCA;
		display: inline-block;
		-webkit-transform: scale(1) rotate(0deg) translateX(0px) translateY(0px) skewX(-25deg) skewY(0deg);
	}
	

	
	</style>
	<?php 
	foreach($choices as $key => $choice)
	{
		if($choice->element_value=='')
		{
			$unanswered++;
		}
		else
		{
			if(!in_array( $choice->element_value,$choices_labels))
			{
				array_push($choices_labels, $choice->element_value);
				array_push($choices_count, 0);
			}

			$choices_count[array_search($choice->element_value, $choices_labels)]++;
		}
	}
	array_multisort($choices_count,SORT_DESC,$choices_labels);
	?><table  class="adminlist">
		<thead>
			<tr>
				<th width="20%">Choices</th>
				<th>Percentage</th>
				<th width="10%">Count</th>
			</tr>
		</thead>
	<?php 
	$k=0;
	foreach($choices_labels as $key => $choices_label)
	{
	?>
		<tr>
			<td class="label<?php echo $k; ?>"><?php echo str_replace("***br***",'<br>', $choices_label)?></td>
			<td><div class="bordered" style="width:<?php echo ($choices_count[$key]/($all-$unanswered))*100; ?>%; height:16px; background-color:<?php echo $colors[$key % 2]; ?>; display:inline-block;"></div><div <?php echo ($choices_count[$key]/($all-$unanswered)!=1 ? 'class="bordered'.$k.'"' : "") ?> style="width:<?php echo 100-($choices_count[$key]/($all-$unanswered))*100; ?>%; height:16px; background-color:#F2F0F1; display:inline-block;"></div></td>
			<td><div><div style="width: 0; height: 0; border-top: 8px solid transparent;border-bottom: 8px solid transparent; border-right:8px solid <?php echo $choices_colors[$key % 2]; ?>; float:left;"></div><div style="background-color:<?php echo $choices_colors[$key % 2]; ?>; height:16px; width:16px; text-align: center; margin-left:8px; color: #fff;"><?php echo $choices_count[$key]?></div></div></td>
		</tr>
		<tr>
			<td colspan="3">
			</td>
		</tr>
	<?php 
		$k = 1 - $k;
	}
	
	if($unanswered){
	?>
	<tr>
	<td colspan="2" style="text-align:right; color: #000;">Unanswered</th>
	<td><strong style="margin-left:10px;"><?php echo $unanswered;?></strong></th>
	</tr>

	<?php	
	}
	?>
	<tr>
	<td colspan="2" style="text-align:right; color: #000;"><strong>Total</strong></th>
	<td><strong style="margin-left:10px;"><?php echo $all;?></strong></th>
	</tr>

	</table>
<?php


