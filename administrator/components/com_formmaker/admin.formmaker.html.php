<?php 

  

 /**

 * @package Form Maker

 * @author Web-Dorado

 * @copyright (C) 2011 Web-Dorado. All rights reserved.

 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

 **/



defined('_JEXEC') or die('Restricted access');







class HTML_contact

{

    const first_css = ".wdform-page-and-images

{

font-size:14px;

font-weight:normal;

color:#000000;

width:100%;

}



.time_box

{

border-width:1px;

margin: 0px;

padding: 0px;

text-align:right;

width:30px;

vertical-align:middle

}



.mini_label

{

font-size:10px;

font-family: 'Lucida Grande', Tahoma, Arial, Verdana, sans-serif;

}



.ch-rad-label

{

display:inline;

//float:left !important;

margin: 2px 5px 2px 0 !important;

}



.label

{

border:none;

}





.td_am_pm_select

{

padding-left:5px;

}



.am_pm_select

{

width:62px !important;

vertical-align: middle;

}



.input_deactive

{

color:#999999;

font-style:italic;

border-width:1px;

margin: 0px;

padding: 0px

}



.input_active

{

color:#000000;

font-style:normal;

border-width:1px;

margin: 0px;

padding: 0px

}



.required

{

border:none;

color:red

}



.captcha_img

{

border-width:0px;

margin: 0px;

padding: 0px;

cursor:pointer;





}



.captcha_refresh

{

width:30px;

height:30px;

border-width:0px;

margin: 0px;

padding: 0px;

vertical-align:middle;

cursor:pointer;

background-image: url(components/com_formmaker/images/refresh_black.png);

}



.captcha_input

{

height:20px;

border-width:1px;

margin: 0px;

padding: 0px;

vertical-align:middle;

}



.file_upload

{

border-width:1px;

margin: 0px;

padding: 0px

}    



.page_deactive

{

border:1px solid black;

padding:4px 7px 4px 7px;

margin:4px;

cursor:pointer;

background-color:#DBDBDB;

}



.page_active

{

border:1px solid black;

padding:4px 7px 4px 7px;

margin:4px;

cursor:pointer;

background-color:#878787;

}



.page_percentage_active

{

padding:0px;

margin:0px;

border-spacing: 0px;

height:30px;

line-height:30px;

background-color:yellow;

border-radius:30px;

font-size:15px;

float:left;

text-align: right !important; 

}





.page_percentage_deactive

{

height:30px;

line-height:30px;

padding:5px;

border:1px solid black;

width:100%;

background-color:white;

border-radius:30px;

text-align: left !important; 

}



.page_numbers

{

font-size:11px;

}



.phone_area_code

{

width:50px;

}



.phone_number

{

width:100px;

}";



public static function show_conditions($ids, $all_ids, $types, $labels, $all_labels, $paramss, $count_of_conditions, $show_hide, $field_label, $all_any, $condition_params, $form_id){

	

	$select_type_fields = array("type_country", "type_address", "type_checkbox", "type_radio", "type_own_select", "type_paypal_select", "type_paypal_checkbox", "type_paypal_radio", "type_paypal_shipping");

?>

	<script>

	function change_choices(id, field_id)

	{

		jQuery("#field_choices"+id).load('index.php?option=com_formmaker&task=change_choices&form_id=<?php echo $form_id; ?>&field_id='+field_id+'&num='+id+'&format=row');

	}	

	

	function add_condition_fields(cond_index)

	{

		var max_index = 0;

		jQuery('#condition'+cond_index).find(jQuery('.cond_fields')).each(function() {

			var value = parseInt(jQuery(this)[0].id.replace('condition_div'+cond_index+'_',''));

			max_index = (value >= max_index) ? value+1 : max_index;

		});



		jQuery("#condition"+cond_index).append(jQuery('<div id="condition_div'+cond_index+'_'+max_index+'" class="cond_fields" style="display:none;">').load('index.php?option=com_formmaker&task=add_condition_fields&form_id=<?php echo $form_id; ?>&cond_index='+cond_index+'&cond_fieldindex='+max_index+'&cond_fieldid='+jQuery('#fields'+cond_index).val()+'&format=row'));



	}

	</script>

	<?php

	for($k=0; $k<$count_of_conditions; $k++)

	{				

		if(in_array($field_label[$k],$all_ids)) : ?>

			<div id="condition<?php echo $k; ?>" class="cond_div">

				<div id="conditional_fileds<?php echo $k; ?>">

					<select id="show_hide<?php echo $k; ?>" name="show_hide<?php echo $k; ?>" style="width:70px; ">

						<option value="1" <?php if($show_hide[$k]==1) echo 'selected="selected"'; ?>>show</option>

						<option value="0" <?php if($show_hide[$k]==0) echo 'selected="selected"'; ?>>hide</option>

					</select> 			

					<select id="fields<?php echo $k; ?>" name="fields<?php echo $k; ?>" style="width:180px; " >

						<?php 

						foreach($all_labels as $key => $value) 		

						{ 	

							$selected = ($field_label[$k]==$all_ids[$key] ? 'selected="selected"' : '');

							echo '<option value="'.$all_ids[$key].'" '.$selected.'>'.$value.'</option>';

						}

						?>

					</select> 

					<span style="vertical-align:top;">if</span>			

					<select id="all_any<?php echo $k; ?>" name="all_any<?php echo $k; ?>" style="width:70px; ">

						<option value="and" <?php if($all_any[$k]=="and") echo 'selected="selected"'; ?>>all</option>

						<option value="or" <?php if($all_any[$k]=="or") echo 'selected="selected"'; ?>>any</option>

					</select> 

					<span style="vertical-align:top;">of the following match:</span>	

					<img src="components/com_formmaker/images/add.png" title="add" onclick="add_condition_fields('<?php echo $k; ?>')" style="cursor: pointer; vertical-align: top; margin-top:5px;">	

					<img src="components/com_formmaker/images/page_delete.png" onclick="delete_condition('<?php echo $k; ?>')" style="cursor: pointer; vertical-align: top;">				

				</div>

					<?php 

						if($condition_params[$k])

						{

							$_params =explode('*:*next_condition*:*',$condition_params[$k]);

							$_params 	= array_slice($_params,0, count($_params)-1); 

								

								foreach($_params as $key=>$_param)

								{

									$key_select_or_input ='';

									$param_values = explode('***',$_param);

									$multiselect = explode('@@@',$param_values[2]);

				

									if(in_array($param_values[0],$ids)): ?>

									<div id="condition_div<?php echo $k; ?>_<?php echo $key; ?>"  class="cond_fields">

										<select id="field_labels<?php echo $k; ?>_<?php echo $key; ?>" onchange="change_choices('<?php echo $k; ?>_<?php echo $key; ?>',this.value)" style="width:180px;">

											<?php 

											foreach($labels as $key1 => $value) 		

											{ 

												$selected ='';

												if($param_values[0]==$ids[$key1])

												{

													$selected = 'selected="selected"';

													$multiple = (($types[$key1]=="type_checkbox" || $types[$key1]=="type_paypal_checkbox") ? 'multiple="multiple"' : '');

													$key_select_or_input = $key1;

												}	

													echo $ids[$key1].' ';

												if($field_label[$k]!=$ids[$key1])

													echo '<option value="'.$ids[$key1].'" '.$selected.'>'.$value.'</option>';

										

											}

											?>	

										</select>

										<select id="is_select<?php echo $k; ?>_<?php echo $key; ?>" style="vertical-align: top; width:100px;">

											<option value="==" <?php if($param_values[1]=="==") echo 'selected="selected"'; ?>>is</option>

											<option value="!=" <?php if($param_values[1]=="!=") echo 'selected="selected"'; ?>>is not</option>

											<option value="%" <?php if($param_values[1]=="%") echo 'selected="selected"'; ?>>like</option>

											<option value="!%" <?php if($param_values[1]=="!%") echo 'selected="selected"'; ?>>not like</option>

											<option value="=" <?php if($param_values[1]=="=") echo 'selected="selected"'; ?>>empty</option>

											<option value="!" <?php if($param_values[1]=="!") echo 'selected="selected"'; ?>>not empty</option>

										</select>

										<div id="field_choices<?php echo $k; ?>_<?php echo $key; ?>" style="display:inline-block;">

										<?php

										switch($types[$key_select_or_input])

										{

											case "type_text":

											case "type_password":

											case "type_textarea":

											case "type_name":

											case "type_number":

											case "type_phone":

											case "type_submitter_mail":

											case "type_paypal_price":
											
											case "type_spinner":
												$keypress_function ='';

												if($types[$key_select_or_input]=="type_number" || $types[$key_select_or_input]=="type_phone")

													$keypress_function = "return check_isnum_space(event)";

												else

													if($types[$key_select_or_input]=="type_paypal_price")

														$keypress_function = "return check_isnum_point(event)";



												echo '<input id="field_value'.$k.'_'.$key.'" type="text" value="'. $param_values[2].'" onkeypress="'.$keypress_function.'" style="vertical-align: top; width: 128px;">';

											break;

											

											case "type_address":

												$w_countries = array("","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");

												echo '<select id="field_value'.$k.'_'.$key.'" style="width: 142px;">';		

													foreach($w_countries as $choise)

													{

														$selected =(in_array($choise, $multiselect) ? 'selected="selected"' : '');									

														echo '<option value="'.$choise.'" '.$selected.'>'.$choise.'</option>';	

													}	

												echo '</select>';	

												

											break;

											case "type_country":
												$temp=$paramss[$key_select_or_input];
												$temp = explode('*:*w_size*:*',$temp);
												$temp = explode('*:*w_countries*:*',$temp[1]);
												$w_countries =  explode('***',$temp[0]);
												echo '<select id="field_value'.$k.'_'.$key.'" style="width: 142px;">';		
													foreach($w_countries as $choise)
													{
														$selected =(in_array($choise, $multiselect) ? 'selected="selected"' : '');	
														echo '<option value="'.$choise.'" '.$selected.'>'.$choise.'</option>';
													}	
												echo '</select>';	
											break;

											case "type_checkbox":

											case "type_radio":

											case "type_own_select":

											case "type_paypal_select":

												$temp=$paramss[$key_select_or_input];

												$exp_par = (($types[$key_select_or_input]== 'type_checkbox' || $types[$key_select_or_input]== 'type_radio') ? '*:*w_flow*:*' : '*:*w_size*:*');

											

												$temp = explode($exp_par,$temp);

												$temp = explode('*:*w_choices*:*',$temp[1]);

												$param['w_choices'] = $temp[0];

												$param['w_choices']	= explode('***',$param['w_choices']);

												

												if($types[$key_select_or_input] != 'type_paypal_select')

												{

													$param['w_choices_value'] = '';

													if(strpos($temp[1], 'w_value_disabled') > -1)

													{

														$temp= explode('*:*w_value_disabled*:*',$temp[1]);

														$temp = explode('*:*w_choices_value*:*',$temp[1]);

														$param['w_choices_value'] = $temp[0];

													}

												

													if(isset($param['w_choices_value']))

														$param['w_choices_value'] = explode('***',$param['w_choices_value']);

													else

														$param['w_choices_value'] = $param['w_choices'];

												}

												else

												{

													$temp= explode('*:*w_choices_price*:*',$temp[1]);

													$param['w_choices_value'] = $temp[0];

													$param['w_choices_value'] = explode('***',$param['w_choices_value']);

												}

												

												$multiple = ($types[$key_select_or_input]=='type_checkbox' ? 'multiple="multiple"' : '');	

												

												echo '<select id="field_value'.$k.'_'.$key.'"  style="width: 142px;" '.$multiple.'>';		

													foreach($param['w_choices'] as $key1=>$choise_label)

													{

														$selected = (in_array($param['w_choices_value'][$key1],$multiselect) ? 'selected="selected"' : '');

														if(strpos($choise_label, '[') === false && strpos($choise_label, ']') === false && strpos($choise_label, ':') === false)

															echo '<option value="'.$param['w_choices_value'][$key1].'" '.$selected.'>'.$choise_label.'</option>';	

													}	

												echo '</select>';				

											break;

										

											case "type_paypal_checkbox":

											case "type_paypal_radio":

											case "type_paypal_shipping":

												$temp=$paramss[$key_select_or_input];

												$temp = explode('*:*w_flow*:*',$temp);

												$temp = explode('*:*w_choices*:*',$temp[1]);

												$param['w_choices'] = $temp[0];

												$temp= explode('*:*w_choices_price*:*',$temp[1]);

												$param['w_choices_price'] = $temp[0];



												$param['w_choices']	= explode('***',$param['w_choices']);

												$param['w_choices_price'] = explode('***',$param['w_choices_price']);



												$multiple = ($types[$key_select_or_input]=='type_paypal_checkbox' ? 'multiple="multiple"' : '');	

												echo '<select id="field_value'.$k.'_'.$key.'" style="width: 142px;" '.$multiple.'>';		

													foreach($param['w_choices'] as $key1=>$choise_label)

													{

														$choise_value = ($types[$key_select_or_input]=='type_paypal_checkbox' ? $choise_label.'*:*value*:*'.$param['w_choices_price'][$key1] : $param['w_choices_price'][$key1]);

													

														$selected = (in_array($choise_value,$multiselect) ? 'selected="selected"' : '');

														if(strpos($choise_label, '[') === false && strpos($choise_label, ']') === false && strpos($choise_label, ':') === false)

															echo '<option value="'.$choise_value.'" '.$selected.'>'.$choise_label.'</option>';	

													}	

												echo '</select>';			

											break;			

										}

										?>

										</div>

										<img src="components/com_formmaker/images/delete.png" id="delete_condition<?php echo $k; ?>_<?php echo $key; ?>" onclick="delete_field_condition('<?php echo $k; ?>_<?php echo $key; ?>')" style="vertical-align: top; margin-top:5px;">

									</div>

									<?php endif;

								}

						}

						?>

			</div>

			

		<?php endif; 

	} 

	?>

	<script>

		jQuery('select').chosen({

			disable_search_threshold : 10,

			allow_single_deselect : true

		});

	</script>	

	<?php

}







public static function add_condition_fields($cond_index, $cond_fieldindex, $cond_fieldid, $ids, $types, $labels, $paramss, $form_id){

	$first_id = (!empty($ids) ? ($cond_fieldid != $ids[0] ? $ids[0] : (isset($ids[1]) ? $ids[1] : -1)) : -1);

?>	

	<script>

	function change_choices(id, field_id)

	{

		jQuery("#field_choices"+id).load('index.php?option=com_formmaker&task=change_choices&form_id=<?php echo $form_id; ?>&field_id='+field_id+'&num='+id+'&format=row');

	}	
	
	if(<?php echo $first_id; ?>)
		change_choices('<?php echo $cond_index.'_'.$cond_fieldindex; ?>',<?php echo $first_id; ?>);

	</script>

	
	<?php if(!empty($ids)): ?>
    <select id="field_labels<?php echo $cond_index.'_'.$cond_fieldindex; ?>" onchange="change_choices('<?php echo $cond_index.'_'.$cond_fieldindex; ?>',this.value)" style="width: 180px; vertical-align: top;">

	<?php 
		foreach($ids as $key => $field_id)
		{
			if($field_id != $cond_fieldid)

			echo '<option value="'.$field_id.'">'.$labels[$key].'</option>';

		}

	?>		

	</select>

	<select id="is_select<?php echo $cond_index.'_'.$cond_fieldindex; ?>" style="vertical-align: top; width: 100px;">
		<option value="==">
			is
		</option>
		<option value="!=">
			is not
		</option>
		<option value="%">
			like
		</option>
		<option value="!%">
			not like
		</option>
		<option value="=">
			empty
		</option>
		<option value="!">
			not empty
		</option>
	</select>

	<div id="field_choices<?php echo $cond_index.'_'.$cond_fieldindex; ?>" style="display:inline-block;">
	</div>	

	<img src="components/com_formmaker/images/delete.png" id="delete_condition<?php echo $cond_index.'_'.$cond_fieldindex; ?>" onclick="delete_field_condition('<?php echo $cond_index.'_'.$cond_fieldindex; ?>')" style="vertical-align: middle;">
	<?php endif; ?>

	<script>

		jQuery('select').chosen({

			disable_search_threshold : 10,

			allow_single_deselect : true

		});

	</script>	

<?php

}







public static function add_condition($cond_index, $ids, $types, $labels, $form_id){

?>	

	<script>

		function add_condition_fields(cond_index)

		{

			var max_index = 0;

			jQuery('#condition'+cond_index).find(jQuery('.cond_fields')).each(function() {

				var value = parseInt(jQuery(this)[0].id.replace('condition_div'+cond_index+'_',''));

				max_index = (value >= max_index) ? value+1 : max_index;

			});

			jQuery("#condition"+cond_index).append(jQuery('<div id="condition_div'+cond_index+'_'+max_index+'" class="cond_fields" style="display:none;">').load('index.php?option=com_formmaker&task=add_condition_fields&form_id=<?php echo $form_id; ?>&cond_index='+cond_index+'&cond_fieldindex='+max_index+'&cond_fieldid='+jQuery('#fields'+cond_index).val()+'&format=row'));

		}

	</script>





    <div id="conditional_fileds<?php echo $cond_index; ?>">

        <select id="show_hide<?php echo $cond_index; ?>" name="show_hide<?php echo $cond_index; ?>" style="width: 70px;">

            <option value="1">show</option>

			<option value="0">hide</option>

		</select>

		<select id="fields<?php echo $cond_index; ?>" name="fields<?php echo $cond_index; ?>"style="width: 180px;">

		<?php 

			foreach($ids as $key => $field_id)

			{

				echo '<option value="'.$field_id.'">'.$labels[$key].'</option>';

			}

		?>

		</select>

		<span style="vertical-align: top;">if</span>        

		<select id="all_any<?php echo $cond_index; ?>" name="all_any<?php echo $cond_index; ?>" style="width: 70px;">

			<option value="and">

				all

			</option>

			<option value="or">

				any

			</option>

		</select>

        <span style="vertical-align: top;">

			of the following match:

		</span> 

        <img src="components/com_formmaker/images/add.png" onclick="add_condition_fields(<?php echo $cond_index; ?>)" style="cursor: pointer; vertical-align: top; margin-top: 5px;"> 

        <img src="components/com_formmaker/images/page_delete.png" onclick="delete_condition(<?php echo $cond_index; ?>)" style="cursor: pointer; vertical-align: top;">

     </div>



	<script>

	jQuery('select').chosen({

		disable_search_threshold : 10,

		allow_single_deselect : true

	});

	</script>	

<?php

}





public static function show_ip_info($ip){

	$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));

	 if ($query && $query['status'] == 'success' && $query['countryCode']) {

      $country_flag = '<img width="16px" src="../components/com_formmaker/images/flags/' . strtolower($query['countryCode']) . '.png" class="sub-align" alt="' . $query['country'] . '" title="' . $query['country'] . '" />';

      $country = $query['country'] ;

      $countryCode = $query['countryCode'] ;

      $city = $query['city'];

      $timezone = $query['timezone'];

      $lat = $query['lat'];

      $lon = $query['lon'];

    }

    else {

      $country_flag = '';

      $country = '';

      $countryCode = '';

      $city = '';

      $timezone = '';

      $lat = '';

      $lon = '';

    }

    ?>

    <style>

      .admintable {

        height: 100%;

        margin: 0 auto;

        padding: 0;

        width: 100%;

      }

      table.admintable td.key, table.admintable td.paramlist_key {

        background-color: #F6F6F6;

        border-bottom: 1px solid #E9E9E9;

        border-right: 1px solid #E9E9E9;

        color: #666666;

        font-weight: bold;

        margin-right: 10px;

        text-align: right;

        width: 140px;

      }

    </style>

    <table class="admintable">

      <tr>

        <td class="key"><b>IP:</b></td><td><?php echo $ip; ?></td>

      </tr>

      <tr>

        <td class="key"><b>Country:</b></td><td><?php echo $country . ' ' . $country_flag; ?></td>

      </tr>

      <tr>

        <td class="key"><b>CountryCode:</b></td><td><?php echo $countryCode; ?></td>

      </tr>

	    <tr>

        <td class="key"><b>City:</b></td><td><?php echo $city; ?></td>

      </tr>

      <tr>

        <td class="key"><b>Timezone:</b></td><td><?php echo $timezone; ?></td>

      </tr>

      <tr>

        <td class="key"><b>Latitude:</b></td><td><?php echo $lat; ?></td>

      </tr>

      <tr>

        <td class="key"><b>Longitude:</b></td><td><?php echo $lon; ?></td>

      </tr>

    </table>

    <?php

    die();

		

}



public static function view_submissions(&$label_titles, $form_id, $labels_id, $labels_type, $after_save, $payment_info)

{

	$stats_labels = array();

	$stats_labels_ids = array();

	foreach($labels_type as $key => $label_type)

	{	

		if($label_type=="type_checkbox" || $label_type=="type_radio" || $label_type=="type_own_select" || $label_type=="type_country" || $label_type=="type_paypal_select" || $label_type=="type_paypal_radio" || $label_type=="type_paypal_checkbox" || $label_type=="type_paypal_shipping")

		{

			$stats_labels_ids[] = &$labels_id[$key];

			$stats_labels[] = &$label_titles[$key];

		}

	}

	?>



<script type="text/javascript">

function inArray(needle, myarray) {

    var length = myarray.length;

    for(var i = 0; i < length; i++) {

        if(myarray[i] == needle) return true;

    }

    return false;

}



function checked_labels(class_name)

{

	var checked_ids ='';

	

	jQuery('.'+class_name).each(function() {

		if(this.checked)

		{

			checked_ids += this.value+',';		

		}

		});

		

	if(class_name=='filed_label')

		document.getElementById("jform_request_checked_ids").value =checked_ids ;

	else

		document.getElementById("jform_request_stats_fields").value =checked_ids ;

}





function datesrange()

{

	if(document.getElementById("startdate"))

	{

		document.getElementById("jform_request_dates_range").value = document.getElementById("startdate").value+'***';

		document.getElementById("jform_request_dates_range").value += document.getElementById("enddate").value;

	}	

}



var payment_info = '';



if(<?php echo $payment_info ?>)

	payment_info = 'payment_info,';	



if(<?php echo $after_save ?>==0)

{

	jQuery('.params li input').each(function() {

		this.checked =true;	

		});



	document.getElementById("jform_request_checked_ids").value = "submit_id,<?php echo implode(',',$labels_id).","; ?>"+payment_info;

	document.getElementById("jform_request_stats_fields").value = "<?php echo implode(',',$stats_labels_ids).","; ?>";

	document.getElementById("jform_request_params").value = "title,search,ordering,entries,views,conversion_rate,pagination,stats,csv,xml,";

}





	jQuery('.params li input').click(function(){

		var checked_params ='';

		jQuery('.params li input').each(function() {

			if(this.checked)

				checked_params += this.value+',';		



		});

		

		document.getElementById("jform_request_params").value = checked_params;

	});

	

	

	jQuery('.filed_label').each(function() {

		

			if(document.getElementById("jform_request_checked_ids").value == "<?php echo "submit_id,submit_date,submitter_ip,username,useremail,".implode(',',$labels_id).","; ?>"+payment_info)

				document.getElementById("all_fields").checked = true;

		

			if(inArray(this.value, document.getElementById("jform_request_checked_ids").value.split(",")))

			{

				this.checked = true;		

			}

			

		});



		

	jQuery('.stats_filed_label').each(function() {

		if(document.getElementById("jform_request_stats_fields").value == "<?php echo implode(',',$stats_labels_ids).","; ?>")	

			document.getElementById("all_stats_fields").checked = true;

	

		if(inArray(this.value, document.getElementById("jform_request_stats_fields").value.split(",")))

		{

			this.checked = true;		

		}

			

		});

	

	jQuery('.params li input').each(function() {

			if(inArray(this.value, document.getElementById("jform_request_params").value.split(",")))

				this.checked = true;		

		});

		

	

	if(document.getElementById("jform_request_dates_range").value)

	{

		var dates = document.getElementById("jform_request_dates_range").value.split('***');

			document.getElementById("startdate").value = dates[0];

			document.getElementById("enddate").value = dates[1];

	}

		



jQuery(document).on('change','input[name="all_fields"]',function() {

    jQuery('.filed_label').prop("checked" , this.checked);

});



jQuery(document).on('change','input[name="all_stats_fields"]',function() {

    jQuery('.stats_filed_label').prop("checked" , this.checked);

});

</script>



<style>

li{

list-style-type: none;

}



.simple_table

{

padding-left: 0px !important;

}



.simple_table input, .simple_table label, .simple_table img

{

display:inline-block !important;

vertical-align:top !important;

float:none !important;

}



</style>



		<?php if(count($label_titles)): ?>

		<table style="margin-left:-3px;">

		<tr>

			<td style="vertical-align:top;"> 

				<label>Select fields:</label>

			</td>

			<td  class="simple_table">

				<ul id="form_fields">

					<li>

					<input type="checkbox" name="all_fields" id="all_fields" value="" onclick="checked_labels('filed_label')"/>

					<label for="all_fields">Select All</label>

					</li>

					<?php 

					echo "<li><input type=\"checkbox\" id=\"submit_id\" name=\"submit_id\" value=\"submit_id\" class=\"filed_label\"  onclick=\"checked_labels('filed_label')\"><label for=\"submit_id\">ID</label></li><li><input type=\"checkbox\" id=\"submit_date\" name=\"submit_date\" value=\"submit_date\" class=\"filed_label\"  onclick=\"checked_labels('filed_label')\"><label for=\"submit_date\">Submit Date</label></li><li><input type=\"checkbox\" id=\"submitter_ip\" name=\"submitter_ip\" value=\"submitter_ip\" class=\"filed_label\"  onclick=\"checked_labels('filed_label')\"><label for=\"submitter_ip\">Submitter's IP Address</label></li><li><input type=\"checkbox\" id=\"username\" name=\"username\" value=\"username\" class=\"filed_label\"  onclick=\"checked_labels('filed_label')\"><label for=\"username\">Submitter's Username</label></li><li><input type=\"checkbox\" id=\"useremail\" name=\"useremail\" value=\"useremail\" class=\"filed_label\"  onclick=\"checked_labels('filed_label')\"><label for=\"useremail\">Submitter's Email Address</label></li>";		

						

					for($i=0, $n=count($label_titles); $i < $n ; $i++)     

					{

						$field_label = &$label_titles[$i];



						echo "<li><input type=\"checkbox\" id=\"filed_label".$i."\" name=\"filed_label".$i."\" value=\"".$labels_id[$i]."\" class=\"filed_label\" onclick=\"checked_labels('filed_label')\"><label for=\"filed_label".$i."\">".(strlen($field_label) > 80 ? substr ($field_label ,0, 80).'...' : $field_label)."</label></li>";

							   

					}

					if($payment_info)

						echo "<li><input type=\"checkbox\" id=\"payment_info\" name=\"payment_info\" value=\"payment_info\" class=\"filed_label\" onclick=\"checked_labels('filed_label')\"><label for=\"payment_info\">Payment Info</label></li>";



					?>

				</ul>

			</td>	

		</tr>

		<tr>

			<td style="vertical-align:top;"> 

					<label>Select Date Range:</label>

				</td>

			<td class="simple_table">

				<label style="min-width:30px !important;">From:</label>

				<input class="inputbox" type="text" name="startdate" id="startdate" style="width:70px;" maxlength="10" value="" onchange="datesrange()"/> 

				<button class="btn" id="startdate_but"><i class="icon-calendar"></i></button>

				<label style="min-width:30px !important;">To:</label>

				<input class="inputbox" type="text" name="enddate" id="enddate" style="width:70px;" maxlength="10" value="" onchange="datesrange()"/>

				<button class="btn" id="enddate_but"><i class="icon-calendar"></i></button>

			</td>

		</tr>	

		<?php if($stats_labels): ?>

		<tr id="stats">

			<td style="vertical-align:top;"> 

				<label>Stats fields:</label>

			</td>

			<td class="simple_table">

				<ul id="stats_fields">

					<li>

					<input type="checkbox" name="all_stats_fields" id="all_stats_fields" value="" onclick="checked_labels('stats_filed_label')">

					<label for="all_stats_fields">Select All</label>

					</li>

					<?php 

					for($i=0, $n=count($stats_labels); $i < $n ; $i++)     

					{

						$field_label = &$stats_labels[$i];

						echo "<li><input type=\"checkbox\" id=\"stats_filed_label".$i."\" name=\"stats_filed_label".$i."\" value=\"".$stats_labels_ids[$i]."\" class=\"stats_filed_label\" onclick=\"checked_labels('stats_filed_label')\" ><label for=\"stats_filed_label".$i."\">".(strlen($field_label) > 80 ? substr ($field_label ,0, 80).'...' : $field_label)."</label></li>";

							   

					}

					?>

				</ul>

			</td>	

		</tr>	

		<?php endif; ?>

		<tr class="params">

			<td style="vertical-align:top;"> 

				<label>Show:</label>

			</td>

			<td class="simple_table">

				<ul>

					<li>

					<input type="checkbox" id="jform_request_show_params0" name="jform[request][show_params][]" value="title">

					<label for="jform_request_show_params0">Title</label>

					</li>

					<li>

					<input type="checkbox" id="jform_request_show_params1" name="jform[request][show_params][]" value="search">

					<label for="jform_request_show_params1">Search</label>

					</li>

					<li>

					<input type="checkbox" id="jform_request_show_params2" name="jform[request][show_params][]" value="ordering">

					<label for="jform_request_show_params2">Ordering</label>

					</li>

					<li>

					<input type="checkbox" id="jform_request_show_params3" name="jform[request][show_params][]" value="entries">

					<label for="jform_request_show_params3">Entries</label>

					</li>

					<li>

					<input type="checkbox" id="jform_request_show_params4" name="jform[request][show_params][]" value="views">

					<label for="jform_request_show_params4">Views</label></li>

					<li>

					<input type="checkbox" id="jform_request_show_params5" name="jform[request][show_params][]" value="conversion_rate">

					<label for="jform_request_show_params5">Conversion Rate</label>

					</li>

					<li>

					<input type="checkbox" id="jform_request_show_params6" name="jform[request][show_params][]" value="pagination">

					<label for="jform_request_show_params6">Pagination</label>

					</li>

					<li>

					<input type="checkbox" id="jform_request_show_params7" name="jform[request][show_params][]" value="stats">

					<label for="jform_request_show_params7">Statistics</label>

					</li>

				</ul>

			</td>	

		</tr>		

		<tr class="params">

			<td style="vertical-align:top;"> 

				<label>Export to:</label>

			</td>

			<td class="simple_table">

				<ul>

					<li>

						<input type="checkbox" id="jform_request_export1" name="jform[request][export][]" value="csv">

						<label for="jform_request_export1">CSV</label>

					</li>

					<li>

						<input type="checkbox" id="jform_request_export2" name="jform[request][export][]" value="xml">

						<label for="jform_request_export2">XML</label>

					</li>

				</ul>

			</td>	

		</tr>		

		



		</table>

	

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

	<?php elseif($form_id):?>

	<div style="color:red; font-size:13px;">This form has no submissions yet</div>

	<?php endif; 

	

}



public static function form_layout(&$row, &$fields){

		JRequest::setVar( 'hidemainmenu', 1 );

		$document = JFactory::getDocument();

 		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';



		$document->addScript($cmpnt_js_path.'/codemirror.js');

		$document->addScript($cmpnt_js_path.'/formatting.js');

		$document->addScript($cmpnt_js_path.'/css.js');

		$document->addScript($cmpnt_js_path.'/clike.js');

		$document->addScript($cmpnt_js_path.'/javascript.js');

		$document->addScript($cmpnt_js_path.'/jquery.min.js');

		$document->addScript($cmpnt_js_path.'/htmlmixed.js');

		$document->addScript($cmpnt_js_path.'/xml.js');

		$document->addScript($cmpnt_js_path.'/php.js');



		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/codemirror.css');

		

		?>

<script>



Joomla.submitbutton= function (pressbutton) {

	

	var form = document.adminForm;

	

	if (pressbutton == 'cancel') 

	{

		submitform( pressbutton );

		return;

	}

	

	if($('#autogen_layout').is(':checked'))

		$('#custom_front').val(custom_front.replace(/\s+/g, ' ').replace(/> </g, '><'));

	else

		$('#custom_front').val(editor.getValue().replace(/\s+/g, ' ').replace(/> </g, '><'));



	submitform( pressbutton );

}





var form_front ='<?php echo addslashes($row->form_front);?>';

var custom_front ='<?php echo addslashes($row->custom_front);?>';

if(custom_front=='')

	custom_front=form_front;



function insertAtCursor_form(myId, myLabel) 

{  

	if($('#autogen_layout').is(':checked'))

		return;

	myValue='<div wdid="'+myId+'" class="wdform_row">%'+myId+' - '+myLabel+'%</div>';



	line=editor.getCursor().line;

	ch=editor.getCursor().ch;

	

	text=editor.getLine(line);

	text1=text.substr(0,ch);

	text2=text.substr(ch);

	text=text1+myValue+text2;

	editor.setLine(line, text);

	editor.focus();

}





function autogen(status)

{



	if(status)

	{

		custom_front = editor.getValue();

		editor.setValue(form_front);

		editor.setOption('readOnly', true);

		autoFormat();

	}

	else

	{

		editor.setValue(custom_front);

		editor.setOption('readOnly', false);

		autoFormat();

	}

	

}



function autoFormat() {

	CodeMirror.commands["selectAll"](editor);

	editor.autoFormatRange(editor.getCursor(true), editor.getCursor(false));

	editor.scrollTo(0,0);

}



</script>        



<style>

button.submit {

	width: 100%;

	padding: 10px 0;

	cursor: pointer;

	margin: 0;

}

button.submit em {

	font-size: 11px;

	font-style: normal;

	color: #999;

}

label {

	cursor: pointer;

	display: inline-block;

}

.CodeMirror {

	border: 1px solid #ccc;

	font-size: 12px;

	margin-bottom: 6px;

	background: white;

}

.field_buttons

{

max-width:200px;

overflow: hidden;

white-space: nowrap;

text-overflow: ellipsis; 

word-break: break-all; 

word-wrap: break-word;

padding: 4px 15px;

font-weight:bold;

}



p

{

font-size: 14px;

font-family: segoe ui;

text-shadow: 0px 0px 1px rgb(202, 202, 202);

}

</style>

<h2> Description</h2>

<p>To customize the layout of the form fields uncheck the Auto-Generate Layout box and edit the HTML.</p>

<p>You can change positioning, add in-line styles and etc. Click on the provided buttons to add the corresponding field.<br/> This will add the following line:

 

 <b><span class="cm-tag">&lt;div</span> <span class="cm-attribute">wdid</span>=<span class="cm-string">"example_id"</span> <span class="cm-attribute">class</span>=<span class="cm-string">"wdform_row"</span><span class="cm-tag">&gt;</span>%example_id - Example%<span class="cm-tag">&lt;/div&gt;</span></b>

 

 , where <b><span class="cm-tag">&lt;div&gt;</span></b> is used to set a row.</p>

<p>To return to the default settings you should check Auto-Generate Layout box.</p>





<h3 style="color:red"> Notice</h3>

<p>Make sure not to publish the same field twice. This will cause malfunctioning of the form.</p>



<hr/>



<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<label for="autogen_layout" style="font-size:20px; line-height:45px; margin-left:10px">Auto Generate Layout? </label>

	<input type="checkbox" value="1" name="autogen_layout" id="autogen_layout" <?php  if($row->autogen_layout) echo 'checked="checked"'?> />

    <input type="hidden" name="custom_front" id="custom_front" value="" />

    <input type="hidden" name="option" value="com_formmaker" />

    <input type="hidden" name="id" value="<?php echo $row->id?>" />

    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />

    <input type="hidden" name="task" value="" />

</form>





<br/>



<?php 

	$ids 	= $fields['ids'];

	$types 	= $fields['types'];

	$labels = $fields['labels'];

	







	foreach($ids as $key => $id)

	{

		if($types[$key]!="type_section_break")

		{

		?>

		<button class="btn" onClick="insertAtCursor_form('<?php echo $ids[$key]; ?>','<?php echo $labels[$key]; ?>')" class="field_buttons" title="<?php echo $labels[$key]; ?>"><?php echo $labels[$key]; ?></button>

		<?php

		}



	}





  ?>

<button  class="submit btn" onclick="autoFormat()"><strong>Apply Source Formatting</strong>  <em>(ctrl-enter)</em></button>

<textarea id="source" name="source" style="display: none;"></textarea>





<script>

var editor = CodeMirror.fromTextArea(document.getElementById("source"), {

    lineNumbers: true,

    lineWrapping: true,

    mode: "htmlmixed",

	value: form_front

    });

	

if($('#autogen_layout').is(':checked'))

{

	editor.setOption('readOnly',  true);

	editor.setValue(form_front);

}

else

{

	editor.setOption('readOnly',  false);

	editor.setValue(custom_front);

}



$('#autogen_layout').click(function(){autogen($(this).is(':checked'))});



autoFormat();



</script>





		<?php

}



public static function db_table_struct_select($table_struct, $field_type){

	

	$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';

	

		$cond='<div id="condid"><select id="sel_condid" style="width: 110px">';

		

		foreach($table_struct as $col)

		{

			$cond.='<option>'.$col->Field.'</option>';

		}

		$cond.='</select>';

		

		$cond.='<select id="op_condid" style="width: 150px"><option value="=" selected="selected">=</option><option value="!=">!=</option><option value=">">&gt;</option><option value="<">&lt;</option><option value=">=">&gt;=</option><option value="<=">&lt;=</option><option value="%..%">Like</option><option value="%..">Starts with</option><option value="..%">Ends with</option></select><input id="val_condid" style="width:120px; margin:0px !important; padding: 3px 6px;" type="text" /><select id="andor_condid" style="visibility: hidden; width:70px;"><option value="AND">AND</option><option value="OR">OR</option></select><img src="components/com_formmaker/images/delete.png" onclick="delete_cond(&quot;condid&quot;)" style="vertical-align: middle;"></div>';





?>

<script src="<?php echo $cmpnt_js_path ?>/formmaker_div1.js?version=1.2" type="text/javascript" style=""></script>

<script>

var selected_field ='';

var isvisible = 1;

var cond_id = 1;



conds='<?php echo $cond ?>';

if(jQuery('#value_disabled').val()=='no')

	jQuery('.ch_rad_value_disabled').hide();

	

function dis(id, x)

{

	if(x)

		jQuery('#'+id).removeAttr('disabled');

	else

		jQuery('#'+id).attr('disabled','disabled');

	

}



function update_vis()

{

	previous=0;

	for(i=1; i<cond_id; i++)

	{



		if(jQuery('#'+i).html())

		{	

			jQuery('#andor_'+i+'_chzn').css('visibility', 'hidden');

			

			if(previous)

				jQuery('#andor_'+previous+'_chzn').css('visibility', 'visible');

				

			previous=i;

		}

	}



}



function delete_cond(id)

{

	jQuery('#'+id).remove();

	update_vis();

}



jQuery('.add_cond').click( function() {

	jQuery('.cols').append(conds.replace(/condid/g, cond_id++));
	jQuery(document).ready(function (){

		jQuery('select').chosen({

			disable_search_threshold : 10,

			allow_single_deselect : true

		});

	});
	update_vis();

	
});





function save_query()

{	str = '';

	product_name = jQuery('#product_name').val();	

	product_price = jQuery('#product_price').val();	

	con_type	=jQuery('input[name=con_type]:checked').val();	table		=jQuery('#tables').val();	host		=jQuery('#host_rem').val();	port		=jQuery('#port_rem').val();	username	=jQuery('#username_rem').val();	password	=jQuery('#password_rem').val();	database	=jQuery('#database_rem').val();	if(con_type=='remote')		str += host+"@@@wdfhostwdf@@@"+port+"@@@wdfportwdf@@@"+username+"@@@wdfusernamewdf@@@"+password+"@@@wdfpasswordwdf@@@"+database+"@@@wdfdatabasewdf@@@";

	gen_query();

	

	var where = jQuery('#where').val();

	var order = jQuery('#order').val();

	var value_disabled = jQuery('#value_disabled').val();



	var num = jQuery("#form_field_id").val();

	var field_type = jQuery("#field_type").val();

	if(product_name || product_price)

	{	

		jQuery('.c1').html('<div id="saving"><div id="saving_text">Saving</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');

	

		var max_value = 0;

		window.parent.jQuery('.change_pos').each(function() {

			var value = jQuery(this)[0].id;

			max_value = (value > max_value) ? value : max_value;

		});	

		max_value = parseInt(max_value) + 1;



		if(field_type =="checkbox" || field_type =="radio")

		{	

			var choices_td = window.parent.document.getElementById('choices');

	

			var div = document.createElement('div');

				div.setAttribute("id", max_value);

				div.setAttribute("class", "change_pos");

				

			var el_choices = document.createElement('input');

				el_choices.setAttribute("id", "el_choices"+max_value);

				el_choices.setAttribute("type", "text");

				el_choices.setAttribute("value", '['+table+':'+product_name+']');

				el_choices.style.cssText =   "width:100px; margin:1px; padding:0; border-width: 1px";

				el_choices.setAttribute("onKeyUp", "change_label('"+num+"_label_element"+max_value+"', this.value); change_in_value('"+num+"_elementform_id_temp"+max_value+"', this.value)");

				el_choices.setAttribute("disabled", 'disabled');	

		

			var el_choices_value = document.createElement('input');

				el_choices_value.setAttribute("id", "el_option_value"+max_value);		

				el_choices_value.setAttribute("type", "text");


				if(value_disabled=='no')

				el_choices_value.setAttribute("value", '['+table+':'+product_name+']');

				else

				el_choices_value.setAttribute("value", '['+table+':'+product_price+']');

				el_choices_value.style.cssText = "width:100px; margin:1px; padding:0; border-width: 1px";

				el_choices_value.setAttribute("onKeyUp", "change_label_value('"+num+"_elementform_id_temp"+max_value+"', this.value)");

				el_choices_value.setAttribute("disabled", 'disabled');

		

			var el_choices_params = document.createElement('input');

				el_choices_params.setAttribute("id", "el_option_params"+max_value);

				el_choices_params.setAttribute("class", "el_option_params");

				el_choices_params.setAttribute("type", "hidden");

				el_choices_params.setAttribute("value", where+'[where_order_by]'+order + '[db_info]'+'['+str+']');

			

			var el_choices_remove = document.createElement('img');

				el_choices_remove.setAttribute("id", "el_choices"+max_value+"_remove");

				el_choices_remove.setAttribute("src", '<?php echo  JURI::base(); ?>components/com_formmaker/images/delete.png');

				el_choices_remove.style.cssText =  'cursor:pointer;vertical-align:middle; margin:3px 3px 3px 7px;';

				el_choices_remove.setAttribute("align", 'top');

				el_choices_remove.setAttribute("onClick", "remove_choise('"+max_value+"','"+num+"','"+field_type+"')");

		

			var el_choices_handle = document.createElement('img');

				el_choices_handle.setAttribute("class", "el_choices_sortable");

				el_choices_handle.setAttribute("src", '<?php echo  JURI::base(); ?>components/com_formmaker/images/move_cursor.png');		

				el_choices_handle.style.cssText = 'cursor:move; vertical-align:middle; margin:3px 3px 3px 9px;';

				el_choices_handle.setAttribute("align", 'top');

		

			div.appendChild(el_choices);

			div.appendChild(el_choices_value);	

			div.appendChild(el_choices_remove);

			div.appendChild(el_choices_handle);

			div.appendChild(el_choices_params);

			choices_td.appendChild(div);

			

			window.parent["refresh_rowcol"](num, field_type);

			

			if(field_type=='checkbox')

			{	

				window.parent["refresh_id_name"](num, 'type_checkbox');

				window.parent["refresh_attr"](num, 'type_checkbox');

			}

				

			if(field_type=='radio')

			{

				window.parent["refresh_id_name"](num, 'type_radio');

				window.parent["refresh_attr"](num, 'type_radio');

			}

	

		}

		

		if(field_type =="select")

		{

			var select_ = window.parent.document.getElementById(num+'_elementform_id_temp');

			var option = document.createElement('option');

				option.setAttribute("id", num+"_option"+max_value);

				option.setAttribute("onselect", "set_select('"+num+"_option"+max_value+"')");

				option.setAttribute("where", where);

				option.setAttribute("order_by", order);			

				option.setAttribute("db_info", '['+str+']');	

				option.innerHTML = '['+table+':'+product_name+']';

				if(value_disabled =='no')

				option.setAttribute("value", '['+table+':'+product_name+']');

				else

				option.setAttribute("value", '['+table+':'+product_price+']');

				

				select_.appendChild(option);

				

			var choices_td= window.parent.document.getElementById('choices');

						

			var div = document.createElement('div');

				div.setAttribute("id", max_value);

				div.setAttribute("class", "change_pos");

				

			var el_choices = document.createElement('input');

				el_choices.setAttribute("id", "el_option"+max_value);

				el_choices.setAttribute("type", "text");

				el_choices.setAttribute("value", '['+table+':'+product_name+']');

				el_choices.style.cssText =   "width:100px; margin:1px; padding:0; border-width: 1px";

				el_choices.setAttribute("onKeyUp", "change_label_name('"+max_value+"', '"+num+"_option"+max_value+"', this.value)");

				el_choices.setAttribute("disabled", 'disabled');

				

			var el_choices_remove = document.createElement('img');

				el_choices_remove.setAttribute("id", "el_option"+max_value+"_remove");

				el_choices_remove.setAttribute("src", '<?php echo  JURI::base(); ?>components/com_formmaker/images/delete.png');

				el_choices_remove.style.cssText = 'cursor:pointer; vertical-align:middle; margin:3px';

				el_choices_remove.setAttribute("align", 'top');

				el_choices_remove.setAttribute("onClick", "remove_option('"+max_value+"','"+num+"')");

				

			var el_choices_dis = document.createElement('input');

				el_choices_dis.setAttribute("type", 'checkbox');

				el_choices_dis.setAttribute("id", "el_option"+max_value+"_dis");

				el_choices_dis.setAttribute("class", "el_option_dis");

				el_choices_dis.setAttribute("onClick", "dis_option('"+num+"_option"+max_value+"', this.checked)");	

				el_choices_dis.style.cssText ="vertical-align: middle; margin-left:24px; margin-right:24px;";

				if(value_disabled == 'yes')

					el_choices_dis.setAttribute("disabled", 'disabled');

		  

			var el_choices_value = document.createElement('input');

				el_choices_value.setAttribute("id", "el_option_value"+max_value);

				el_choices_value.setAttribute("type", "text");

				if(value_disabled=='no')

				el_choices_value.setAttribute("value", '['+table+':'+product_name+']');

				else

				el_choices_value.setAttribute("value", '['+table+':'+product_price+']');

				el_choices_value.style.cssText =   "width:100px; margin:1px; padding:0; border-width: 1px";

				el_choices_value.setAttribute("onKeyUp", "change_label_value('"+num+"_option"+max_value+"', this.value)");

				el_choices_value.setAttribute("disabled", 'disabled');

			

			var el_choices_params = document.createElement('input');

				el_choices_params.setAttribute("id", "el_option_params"+max_value);

				el_choices_params.setAttribute("class", "el_option_params");

				el_choices_params.setAttribute("type", "hidden");

				el_choices_params.setAttribute("value", where+'[where_order_by]'+order + '[db_info]'+'['+str+']');



			var el_choices_handle = document.createElement('img');

				el_choices_handle.setAttribute("class", "el_choices_sortable");

				el_choices_handle.setAttribute("src", '<?php echo  JURI::base(); ?>components/com_formmaker/images/move_cursor.png');		

				el_choices_handle.style.cssText = 'cursor:move; vertical-align:middle; margin:3px 3px 3px 10px;';

				el_choices_handle.setAttribute("align", 'top');

			

			div.appendChild(el_choices);

			div.appendChild(el_choices_value);

			div.appendChild(el_choices_dis);

			div.appendChild(el_choices_remove);

			div.appendChild(el_choices_handle);	

			div.appendChild(el_choices_params);

			choices_td.appendChild(div);

		}	



		if(field_type=='paypal_select')

		{		

				

			var select_ = window.parent.document.getElementById(num+'_elementform_id_temp');

			var option = document.createElement('option');

				option.setAttribute("id", num+"_option"+max_value);

				option.setAttribute("onselect", "set_select('"+num+"_option"+max_value+"')");

				option.setAttribute("where", where);

				option.setAttribute("order_by", order);				option.setAttribute("db_info", '['+str+']');

				option.innerHTML = '['+table+':'+product_name+']';

				option.setAttribute("value", '['+table+':'+product_price+']');

				

				select_.appendChild(option);

			

			var choices_td= window.parent.document.getElementById('choices');

			var div = document.createElement('div');

				div.setAttribute("id", max_value);

				div.setAttribute("class", "change_pos");

				

			var el_choices = document.createElement('input');

				el_choices.setAttribute("id", "el_option"+max_value);

				el_choices.setAttribute("type", "text");

				el_choices.setAttribute("value", '['+table+':'+product_name+']');

				el_choices.style.cssText =   "width:100px; margin:1px; padding:0; border-width: 1px";

				el_choices.setAttribute("onKeyUp", "change_label_price('"+num+"_option"+max_value+"', this.value)");el_choices.setAttribute("disabled", 'disabled');

				

			var el_choices_price = document.createElement('input');

				el_choices_price.setAttribute("id", "el_option_price"+max_value);

				el_choices_price.setAttribute("type", "text");

				el_choices_price.setAttribute("value", '['+table+':'+product_price+']');

				el_choices_price.style.cssText =   "width:50px; margin:1px; padding:0; border-width: 1px";

				el_choices_price.setAttribute("onKeyUp", "change_value_price('"+num+"_option"+max_value+"', this.value)");

				el_choices_price.setAttribute("onKeyPress", "return check_isnum_point(event)");

				el_choices_price.setAttribute("disabled", 'disabled');

		

			var el_choices_params = document.createElement('input');

				el_choices_params.setAttribute("id", "el_option_params"+max_value);

				el_choices_params.setAttribute("class", "el_option_params");

				el_choices_params.setAttribute("type", "hidden");

				el_choices_params.setAttribute("value", where+'[where_order_by]'+order + '[db_info]'+'['+str+']');

				

			var el_choices_remove = document.createElement('img');

				el_choices_remove.setAttribute("id", "el_option"+max_value+"_remove");

				el_choices_remove.setAttribute("src", '<?php echo  JURI::base(); ?>components/com_formmaker/images/delete.png');

				el_choices_remove.style.cssText = 'cursor:pointer; vertical-align:middle;  margin-left:4px;';

				el_choices_remove.setAttribute("align", 'top');

				el_choices_remove.setAttribute("onClick", "remove_option_price('"+max_value+"','"+num+"')");

				

			var el_choices_dis = document.createElement('input');

				el_choices_dis.setAttribute("type", 'checkbox');

				el_choices_dis.setAttribute("id", "el_option"+max_value+"_dis");

				el_choices_dis.setAttribute("onClick", "dis_option_price('"+num+"','"+max_value+"', this.checked)");

				el_choices_dis.style.cssText ="vertical-align: middle; margin-right:24px; margin-left:24px;";





			var el_choices_handle = document.createElement('img');

				el_choices_handle.setAttribute("class", "el_choices_sortable");

				el_choices_handle.setAttribute("src", '<?php echo  JURI::base(); ?>components/com_formmaker/images/move_cursor.png');		

				el_choices_handle.style.cssText = 'cursor:move; vertical-align:middle; margin:3px 3px 3px 20px;';

				el_choices_handle.setAttribute("align", 'top');

			

			div.appendChild(el_choices);

			div.appendChild(el_choices_price);

			div.appendChild(el_choices_dis);

			div.appendChild(el_choices_remove);

			div.appendChild(el_choices_handle);

			div.appendChild(el_choices_params);

			

			choices_td.appendChild(div);	

		

		}	

		


		window.parent.SqueezeBox.close(); 

	}

	else

	{

		if(field_type=="checkbox" || field_type=="radio" || field_type=="select")

			alert('Select a option(s).');

		else

			alert('Select a product name or product price.');

	}

	return false; 





}



function gen_query()

{

		query="";

		query_price = "";

		where="";

		previous='';

		

		for(i=1; i<cond_id; i++)

		{

			if(jQuery('#'+i).html())

			{

				if(jQuery('#op_'+i).val()=="%..%")

					op_val=' LIKE "%'+jQuery('#val_'+i).val()+'%"';

					

				else if(jQuery('#op_'+i).val()=="%..")

					op_val=' LIKE "%'+jQuery('#val_'+i).val()+'"';

					

				else if(jQuery('#op_'+i).val()=="..%")

					op_val=' LIKE "'+jQuery('#val_'+i).val()+'%"';

				

				else

					op_val=' '+jQuery('#op_'+i).val()+' "'+jQuery('#val_'+i).val()+'"';

					

				where+=previous+' `'+jQuery('#sel_'+i).val()+'`'+op_val;

				

				previous=' '+ jQuery('#andor_'+i).val();

			}

			

		}



		

	query = '['+where+']';

		

	query_price = '['+(jQuery('#order_by').val() ? '`'+jQuery('#order_by').val()+'`' +' '+jQuery('#order_by_asc').val() : jQuery('#product_name').val() ? '`'+jQuery('#product_name').val()+'`' +' '+jQuery('#order_by_asc').val() : jQuery('#product_price').val() ? '`'+jQuery('#product_price').val()+'`' +' '+jQuery('#order_by_asc').val() : '' )+']';

	

	jQuery('#where').val(query);

	jQuery('#order').val(query_price);

}



window.addEvent('domready', function() {

			$$('.hasTip').each(function(el) {

				var title = el.get('title');

				if (title) {

					var parts = title.split('::', 2);

					el.store('tip:title', parts[0]);

					el.store('tip:text', parts[1]);

				}

			});

			var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});

		});



</script>

<style>

.table_fields

{

margin-bottom:2px;

}



.table_fields select 

{

line-height: 18px;

width: inherit;

margin: inherit;

}



.table_fields input[type="text"]

{

width: 225px;

line-height: 18px;

height: 20px;



}



.gen_query, .gen_query:focus

{

width: 200px !important;

height: 38px;

background: #0E73D4;

color: white;

cursor: pointer;

border: 0px;

font-size: 16px;

font-weight: bold;

margin: 20px 20px 20px 0;

}



.gen_query:active

{

background: #ccc;



}



</style>





<?php if($table_struct): ?>

	<div class="cols">

		<div class="table_fields">

			<label for="product_name" style="display:inline-block;width:157px;font-weight: bold; vertical-align: top;"><?php echo  (strpos($field_type, 'paypal_') === false ? 'Select a name' : ($field_type == 'paypal_shipping' ? 'Select a shipping type' : 'Select a product name')); ?></label>

			<select name="product_name" id="product_name"> 

				<option value="" ></option>

				<?php

				

				foreach($table_struct as $col)

					echo '<option value="'.$col->Field.'" >'.$col->Field.'</option>';

				?>

			</select>

		</div>

		<div class="table_fields ch_rad_value_disabled">

			<label for="product_price" style="display:inline-block;width:157px;font-weight: bold; vertical-align: top;"><?php echo  (strpos($field_type, 'paypal_') === false ? 'Select a value' : 'Select a product price'); ?></label>

			<select name="product_price" id="product_price"> 

				<option value="" ></option>

				<?php

				

				foreach($table_struct as $col)

					echo '<option value="'.$col->Field.'" >'.$col->Field.'</option>';

				?>

			</select>

		</div>

		<br/>

		<div style="background: none;text-align: center;font-size: 20px;color: rgb(0, 164, 228);font-weight: bold;">WHERE </div>

		<img src="components/com_formmaker/images/add_condition.png" title="ADD" class="add_cond"/>	

	</div>

	</br>

	<div style="background: none;text-align: center;font-size: 20px;color: rgb(0, 164, 228);font-weight: bold; margin:8px 0;">ORDER BY</div>

	<div class="table_fields">

		<label for="order_by" style="display:inline-block;width:157px;font-weight: bold; vertical-align: top;">Select an option</label>

		<select name="order_by" id="order_by"> 

			<option value="" ></option>

			<?php	

			foreach($table_struct as $col)

				echo '<option value="'.$col->Field.'" >'.$col->Field.'</option>';

			?>

		</select>

		<select name="order_by_asc" id="order_by_asc" style="width:70px;">

			<option value="asc">asc</option>

			<option value="desc">desc</option>

		</select>

	</div>

	<br/>

	<input type="button" value="Save" class="gen_query" onclick="save_query()">

	

	<form name="query_form" id="query_form"  style="display:none;">

		<textarea id="where" name="where"></textarea>

		<textarea id="order" name="order"></textarea>

	</form>

<?php endif; ?>



	<script>



	jQuery(document).ready(function (){

					jQuery('select').chosen({

						disable_search_threshold : 10,

						allow_single_deselect : true

					});

				});

	</script>

	<?php

}







public static function db_table_struct($table_struct, $label,$con_method){

	

		$filter_types=array("type_submit_reset", "type_map", "type_editor", "type_captcha", "type_recaptcha", "type_button", "type_paypal_total", "type_send_copy");



		$label_id= array();

		$label_order= array();

		$label_order_original= array();

		$label_type= array();

	

		///stexic

		$label_all	= explode('#****#',$label);

		$label_all 	= array_slice($label_all,0, count($label_all)-1);   

	

		foreach($label_all as $key => $label_each) 

		{

			$label_id_each=explode('#**id**#',$label_each);

			$label_oder_each=explode('#**label**#', $label_id_each[1]);

			

			if(in_array($label_oder_each[1],$filter_types))

				continue;

				

			array_push($label_id, $label_id_each[0]);

		

		

			array_push($label_order_original, $label_oder_each[0]);

		

			$ptn = "/[^a-zA-Z0-9_]/";

			$rpltxt = "";

			$label_temp=preg_replace($ptn, $rpltxt, $label_oder_each[0]);

			array_push($label_order, $label_temp);

		

			array_push($label_type, $label_oder_each[1]);

		}



		$form_fields='';



		foreach($label_id as $key => $id)

		{

			$form_fields.='<a onclick="insert_field('.$id.'); jQuery(\'#fieldlist\').hide();" style="display:block; text-decoration:none;">'.$label_order_original[$key].'</a>';



		}

		$user_fields = array("subid"=>"Submission ID", "ip"=>"Submitter's IP", "userid"=>"User ID", "username"=>"Username", "useremail"=>"User Email");
		foreach($user_fields as $user_key=>$user_field) {
			$form_fields.='<a onclick="insert_field(\''.$user_key.'\'); jQuery(\'#fieldlist\').hide();" style="display:block; text-decoration:none;">'.$user_field.'</a>';
		}

		$cond='<div id="condid"><select id="sel_condid" style="width: 110px">';

		

		foreach($table_struct as $col)

		{

			$cond.='<option>'.$col->Field.'</option>';

		}

		$cond.='</select>';

		

		$cond.='<select id="op_condid"><option value="=" selected="selected">=</option><option value="!=">!=</option><option value=">">&gt;</option><option value="<">&lt;</option><option value=">=">&gt;=</option><option value="<=">&lt;=</option><option value="%..%">Like</option><option value="%..">Starts with</option><option value="..%">Ends with</option></select><input id="val_condid" style="width:120px" type="text" /><select id="andor_condid" style="visibility: hidden;"><option value="AND">AND</option><option value="OR">OR</option></select><img src="components/com_formmaker/images/delete.png" onclick="delete_cond(&quot;condid&quot;)" style="vertical-align: middle;"></div>';





?>

<script>

var selected_field ='';

var isvisible = 1;

var cond_id = 1;

 //onclick="gen_query()"

conds='<?php echo $cond ?>';

		

fields=new Array(<?php

	

	$fields = "";

	

	if($table_struct)

	{

		foreach($table_struct as $col)

		{

			$fields.=' "'.$col->Field.'",';

		}

	

	

		echo  substr($fields, 0, -1);

	}

	?>);



function dis(id, x)

{

	if(x)

		jQuery('#'+id).removeAttr('disabled');

	else

		jQuery('#'+id).attr('disabled','disabled');

	

}



function update_vis()

{

	previous=0;

	for(i=1; i<cond_id; i++)

	{



		if(jQuery('#'+i).html())

		{	

			jQuery('#andor_'+i).css('visibility', 'hidden');

			

			if(previous)

				jQuery('#andor_'+previous).css('visibility', 'visible');

				

			previous=i;

		}

	}



}



function delete_cond(id)

{

	jQuery('#'+id).remove();

	update_vis();

}



jQuery('.add_cond').click( function() {

		jQuery('.cols').append(conds.replace(/condid/g, cond_id++));

		update_vis();

	}

);





jQuery('html').click(function() {



	if(jQuery("#fieldlist").css('display')=="block")

	{

		jQuery("#fieldlist").hide();

	}

});



jQuery('.cols input[type="text"]').live('click', function() {

    event.stopPropagation();

	jQuery("#fieldlist").css("top",jQuery(this).offset().top+jQuery(this).height()+2);

	jQuery("#fieldlist").css("left",jQuery(this).offset().left);

	jQuery("#fieldlist").slideDown('fast');

	selected_field=this.id;



});



jQuery('#query_txt').click(function() {

	event.stopPropagation();

	jQuery("#fieldlist").css("top",jQuery(this).offset().top+jQuery(this).height()+2);

	jQuery("#fieldlist").css("left",jQuery(this).offset().left);

	jQuery("#fieldlist").slideDown('fast');

	selected_field=this.id;

});



jQuery('#fieldlist').click(function(event){

    event.stopPropagation();

});



function save_query()

{

	con_type	=jQuery('input[name=con_type]:checked').val();

	con_method	=jQuery('input[name=con_method]:checked').val();

	table		=jQuery('#tables').val();

	table		=jQuery('#tables').val();

	host		=jQuery('#host_rem').val();

	port		=jQuery('#port_rem').val();

	username	=jQuery('#username_rem').val();

	password	=jQuery('#password_rem').val();

	database	=jQuery('#database_rem').val();

		

	str=con_type+"***wdfcon_typewdf***"+con_method+"***wdfcon_methodwdf***"+table+"***wdftablewdf***"+host+"***wdfhostwdf***"+port+"***wdfportwdf***"+username+"***wdfusernamewdf***"+password+"***wdfpasswordwdf***"+database+"***wdfdatabasewdf***";

		

	if(fields.length)

	{

		for(i=0; i<fields.length; i++)

			str+=fields[i]+'***wdfnamewdf***'+jQuery('#'+fields[i]).val()+'***wdfvaluewdf***'+jQuery('#ch_'+fields[i]+":checked" ).length+'***wdffieldwdf***'

	}

			

	for(i=1; i<cond_id; i++)

	{

		if(jQuery('#'+i).html())

		{

			str+=jQuery('#sel_'+i).val()+'***sel***'+jQuery('#op_'+i).val()+'***op***'+jQuery('#val_'+i).val()+'***val***'+jQuery('#andor_'+i).val()+'***where***';

		}

	}



	

	if(!jQuery('#query_txt').val())

	{

		gen_query();

	}



	

	jQuery('#details').val(str);



	var datatxt = jQuery("#query_form").serialize()+'&form_id='+jQuery("#form_id").val(); 

	if(jQuery('#query_txt').val())

	{

	jQuery('.c1').html('<div id="saving"><div id="saving_text">Saving</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');

	jQuery.ajax({

		   type: "POST",

		   url: "index.php?option=com_formmaker&task=save_query",

		   data: datatxt,

		   success: function(data)

		   {

				window.parent.Joomla.submitbutton('apply_form_options');

			   	window.parent.SqueezeBox.close(); 

		   }

		 });

	}

	else

	{

		alert('The query is empty.');

	}

	return false; 





}



function gen_query()

{

	if(jQuery('#query_txt').val())

		if(!confirm('Are you sure you want to replace the Query? All the modifications to the Query will be lost.'))

			return;

			

	query="";

	fields=new Array(<?php

	

	$fields = "";

	

	if($table_struct)

	{

		foreach($table_struct as $col)

		{

			$fields.=' "'.$col->Field.'",';

		}

	

	

		echo  substr($fields, 0, -1);

	}

	?>);



	con_type	=jQuery('input[name=con_type]:checked').val();

	con_method	=jQuery('input[name=con_method]:checked').val();

	table		=jQuery('#tables').val();



	

	fls='';

	vals='';	

	valsA=new Array();

	flsA=new Array();

	

	if(fields.length)

	{

		for(i=0; i<fields.length; i++)

			if(jQuery('#ch_'+fields[i]+":checked" ).length)

			{

				flsA.push(fields[i]);

				valsA.push(jQuery('#'+fields[i]).val());

			}

	}

	



	if(con_method=="insert")

	{

		if(flsA.length)

		{

			for(i=0; i<flsA.length-1; i++)

			{

					fls+= '`'+flsA[i]+'`, ';

					vals+= '"'+valsA[i]+'", ';

			}

					fls+= '`'+flsA[i]+'`';

					vals+= '"'+valsA[i]+'"';

		}

		

		if(fls)

			query="INSERT INTO "+jQuery('#tables').val()+" (" +fls+") VALUES ("+vals+")";

	}

	

	if(con_method=="update")

	{

		if(flsA.length)

		{

			for(i=0; i<flsA.length-1; i++)

			{

					vals+= '`'+flsA[i]+'`="'+valsA[i]+'", ';



			}

					vals+= '`'+flsA[i]+'`="'+valsA[i]+'"';

		}



		

		where="";

		previous='';

		

		for(i=1; i<cond_id; i++)

		{

			if(jQuery('#'+i).html())

			{

				if(jQuery('#op_'+i).val()=="%..%")

					op_val=' LIKE "%'+jQuery('#val_'+i).val()+'%"';

					

				else if(jQuery('#op_'+i).val()=="%..")

					op_val=' LIKE "%'+jQuery('#val_'+i).val()+'"';

					

				else if(jQuery('#op_'+i).val()=="..%")

					op_val=' LIKE "'+jQuery('#val_'+i).val()+'%"';

				

				else

					op_val=' '+jQuery('#op_'+i).val()+' "'+jQuery('#val_'+i).val()+'"';

					

				where+=previous+' `'+jQuery('#sel_'+i).val()+'`'+op_val;

				

				previous=' '+ jQuery('#andor_'+i).val();

			}

			

		}



		if(vals)

			query="UPDATE "+jQuery('#tables').val()+" SET " + vals+(where? ' WHERE'+where: '') ;

	}



	if(con_method=="delete")

	{

	

		where="";

		previous='';

		

		for(i=1; i<cond_id; i++)

		{

			if(jQuery('#'+i).html())

			{

				if(jQuery('#op_'+i).val()=="%..%")

					op_val=' LIKE "%'+jQuery('#val_'+i).val()+'%"';

					

				else if(jQuery('#op_'+i).val()=="%..")

					op_val=' LIKE "%'+jQuery('#val_'+i).val()+'"';

					

				else if(jQuery('#op_'+i).val()=="..%")

					op_val=' LIKE "'+jQuery('#val_'+i).val()+'%"';

				

				else

					op_val=' '+jQuery('#op_'+i).val()+' "'+jQuery('#val_'+i).val()+'"';

					

				where+=previous+' '+jQuery('#sel_'+i).val()+op_val;

				

				previous=' '+ jQuery('#andor_'+i).val();

			}

			

		}

		//DELETE FROM `jos_categories` WHERE  `id` = 'asdgasdg' 

		if(where)

			query="DELETE FROM "+jQuery('#tables').val()+ ' WHERE'+where ;

	}

	

	jQuery('#query_txt').val(query);

}



window.addEvent('domready', function() {

			$$('.hasTip').each(function(el) {

				var title = el.get('title');

				if (title) {

					var parts = title.split('::', 2);

					el.store('tip:title', parts[0]);

					el.store('tip:text', parts[1]);

				}

			});

			var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});

		});



function insert_field(myValue) {  

  

	if(!selected_field)

		return; 

	

	myField=document.getElementById(selected_field);

	

	if (document.selection) {      

	   myField.focus();      

	   sel = document.selection.createRange();    

	   sel.text = myValue;    

	   }    

	else

		if (myField.selectionStart || myField.selectionStart == '0') {     

		   var startPos = myField.selectionStart;       

		   var endPos = myField.selectionEnd;      

		   myField.value = myField.value.substring(0, startPos)           

		   +  "{"+myValue+"}"        

		   + myField.value.substring(endPos, myField.value.length);   

		} 

   else {     

   myField.value += "{"+myValue+"}";    

   }

   }		

</script>

<style>



.cols div:nth-child(even) {background: #FFF}

.cols div:nth-child(odd) {background: #F5F5F5}



.cols div

{

height: 28px;

padding: 5px

}



.cols label

{

display:inline-block;

width:200px;

font-size:15px;

overflow: hidden;

white-space: nowrap;

text-overflow: ellipsis;

vertical-align: middle;

}



.cols input[type="text"]

{

width: 225px;

line-height: 18px;

height: 20px;

margin:0px

}



.cols input[type="text"]:disabled

{

cursor: not-allowed;

background-color: #eee;

}



.cols input[type="checkbox"]

{

width: 20px;

line-height: 18px;

height: 20px;

vertical-align: middle;

margin:5px

}



.cols select

{

line-height: 18px;

width: inherit;

margin: inherit;

}



#fieldlist

{

position: absolute;

width:225px;

background: #fff;

border: solid 1px #c7c7c7;

top: 0;

left: 0;

z-index: 1000;

}



#fieldlist a

{

padding: 5px;

cursor:pointer;

overflow: hidden;

white-space: nowrap;

text-overflow: ellipsis;

}



#fieldlist a:hover

{

background: #ccc;

}



.gen_query, .gen_query:focus

{

width: 200px !important;

height: 38px;

background: #0E73D4;

color: white;

cursor: pointer;

border: 0px;

font-size: 16px;

font-weight: bold;

margin: 20px;

}



.gen_query:active

{

background: #ccc;



}



</style>



<?php 



	if($table_struct)

	{

	?>



	<div class="cols">

	<?php	

		if($con_method=='insert' or $con_method=='update')

		{

			echo '<div style="background: none;text-align: center;font-size: 20px;color: rgb(0, 164, 228);font-weight: bold;"> SET </div>';

			foreach($table_struct as $col)

			{

				$title=' '.$col->Field;

				$title.="<ul style='padding-left: 17px;'>";

				$title.="<li>Type - ".$col->Type."</li>";

				$title.="<li>Null - ".$col->Null."</li>";

				$title.="<li>Key - ".$col->Key."</li>";

				$title.="<li>Default - ".$col->Default."</li>";

				$title.="<li>Extra - ".$col->Extra."</li>";

				$title.="</ul>";

				

			?>

				<div><label title="<?php echo $title; ?>" class="hasTip"><b><?php echo $col->Field; ?></b><img src="components/com_formmaker/images/info.png" style="width:20px; vertical-align:middle;padding-left: 10px;" /></label><input type="text" id="<?php echo $col->Field; ?>" disabled="disabled"/><input id="ch_<?php echo $col->Field; ?>"  type="checkbox" onClick="dis('<?php echo $col->Field; ?>', this.checked)"/></div>

			<?php

			}

		}	

		if($con_method=='delete' or $con_method=='update')

		{

			echo '<div style="background: none;text-align: center;font-size: 20px;color: rgb(0, 164, 228);font-weight: bold;"> WHERE </div>



			<img src="components/com_formmaker/images/add_condition.png" title="ADD" class="add_cond"/></br>';

			

		}

	?>

	</div>

	<br/>

	<input type="button" value="Generate Query" class="gen_query" onclick="gen_query()">

	<br/>

	<form name="query_form" id="query_form" >

		<label style="vertical-align: top;" for="query_txt" > Query: </label><textarea id="query_txt" name="query" rows=5 style="width:400px"></textarea>

		<input type="hidden" name="details" id="details">

	</form>

	<input type="button" value="Save" style="float: right;width: 200px;height: 38px;background: #0E73D4;color: white;cursor: pointer;border: 0px;font-size: 16px;font-weight: bold;margin: 20px;" onclick="save_query()">

	

	

	<div id="fieldlist" style="display: none;">

	<?php echo $form_fields ?>

	</div>

	



			<?php

	}



}





public static function db_tables($tables){

?>

	<label for="tables" style="display:inline-block;width:157px;font-weight: bold; vertical-align: top;">Select a table</label><select name="tables" id="tables" style="margin-top:20px"> 

		<option value="" ></option>

	<?php

	

	foreach($tables as $table)

		echo '<option value="'.$table.'" >'.$table.'</option>';

	?>

	</select>

	<br/><br/>

	<div id="table_struct">

	</div>

	

	<script>



	jQuery("#tables").change(function (){

		jQuery('#table_struct').html('<div id="saving"><div id="saving_text">Loading</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');



		if(jQuery("#field_type").val())

			jQuery("#table_struct").load('index.php?option=com_formmaker&task=db_table_struct_select&name='+jQuery(this).val()+'&con_type='+jQuery('input[name=con_type]:checked').val()+'&con_method='+jQuery('input[name=con_method]:checked').val()+'&host='+jQuery('#host_rem').val()+'&port='+jQuery('#port_rem').val()+'&username='+jQuery('#username_rem').val()+'&password='+jQuery('#password_rem').val()+'&database='+jQuery('#database_rem').val()+'&format=row&field_type='+jQuery("#field_type").val());

		else

			jQuery("#table_struct").load('index.php?option=com_formmaker&task=db_table_struct&name='+jQuery(this).val()+'&con_type='+jQuery('input[name=con_type]:checked').val()+'&con_method='+jQuery('input[name=con_method]:checked').val()+'&host='+jQuery('#host_rem').val()+'&port='+jQuery('#port_rem').val()+'&username='+jQuery('#username_rem').val()+'&password='+jQuery('#password_rem').val()+'&database='+jQuery('#database_rem').val()+'&format=row&id='+jQuery("#form_id").val());

	});

	

	jQuery(document).ready(function (){

					jQuery('select').chosen({

						disable_search_threshold : 10,

						allow_single_deselect : true

					});

				});

	</script>



<?php



}



public static function edit_query($id, $label, $query_obj, $tables, $table_struct, $con_type, $con_method, $table_cur, $details, $host, $port, $username, $password, $database){

		JHtml::_('behavior.tooltip');

		$document		= JFactory::getDocument();

		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js');

		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/style.css?version=1.2');



		$filter_types=array("type_submit_reset", "type_map", "type_editor", "type_captcha", "type_recaptcha", "type_button", "type_paypal_total", "type_send_copy");

		$label_id= array();

		$label_order= array();

		$label_order_original= array();

		$label_type= array();

	

		///stexic

		$label_all	= explode('#****#',$label);

		$label_all 	= array_slice($label_all,0, count($label_all)-1);   

	

		foreach($label_all as $key => $label_each) 

		{

			$label_id_each=explode('#**id**#',$label_each);

			$label_oder_each=explode('#**label**#', $label_id_each[1]);

			

			if(in_array($label_oder_each[1],$filter_types))

				continue;

				

			array_push($label_id, $label_id_each[0]);

		

		

			array_push($label_order_original, $label_oder_each[0]);

		

			$ptn = "/[^a-zA-Z0-9_]/";

			$rpltxt = "";

			$label_temp=preg_replace($ptn, $rpltxt, $label_oder_each[0]);

			array_push($label_order, $label_temp);

		

			array_push($label_type, $label_oder_each[1]);

		}



		$form_fields='';



		foreach($label_id as $key => $lid)

		{

			$form_fields.='<a onclick="insert_field('.$lid.'); jQuery(\'#fieldlist\').hide();" style="display:block; text-decoration:none;">'.$label_order_original[$key].'</a>';



		}

		$user_fields = array("subid"=>"Submission ID", "ip"=>"Submitter's IP", "userid"=>"User ID", "username"=>"Username", "useremail"=>"User Email");
		foreach($user_fields as $user_key=>$user_field) {
			$form_fields.='<a onclick="insert_field(\''.$user_key.'\'); jQuery(\'#fieldlist\').hide();" style="display:block; text-decoration:none;">'.$user_field.'</a>';
		}

		$cond='<div id="condid"><select id="sel_condid" style="width: 110px">';

		

		foreach($table_struct as $col)

		{

			$cond.='<option>'.$col->Field.'</option>';

		}

		$cond.='</select>';

		

		$cond.='<select id="op_condid"><option value="=" selected="selected">=</option><option value="!=">!=</option><option value=">">&gt;</option><option value="<">&lt;</option><option value=">=">&gt;=</option><option value="<=">&lt;=</option><option value="%..%">Like</option><option value="%..">Starts with</option><option value="..%">Ends with</option></select><input id="val_condid" style="width:120px" type="text" /><select id="andor_condid" style="visibility: hidden;"><option value="AND">AND</option><option value="OR">OR</option></select><img src="components/com_formmaker/images/delete.png" onclick="delete_cond(&quot;condid&quot;)" style="vertical-align: middle;"></div>';

?>

	<script>

	function connect()

	{

		jQuery("input[type='radio']").attr('disabled','');

		jQuery('#struct').html('<div id="saving"><div id="saving_text">Loading</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');



		jQuery("#struct").load('index.php?option=com_formmaker&task=db_tables&con_type='+jQuery('input[name=con_type]:checked').val()+'&con_method='+jQuery('input[name=con_method]:checked').val()+'&format=row');

	}

	jQuery( document ).ready(function() {

	

		jQuery("#tables").change(function (){

			jQuery('#table_struct').html('<div id="saving"><div id="saving_text">Loading</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');



			jQuery("#table_struct").load('index.php?option=com_formmaker&task=db_table_struct&name='+jQuery(this).val()+'&con_type='+jQuery('input[name=con_type]:checked').val()+'&con_method='+jQuery('input[name=con_method]:checked').val()+'&host='+jQuery('#host_rem').val()+'&port='+jQuery('#port_rem').val()+'&username='+jQuery('#username_rem').val()+'&password='+jQuery('#password_rem').val()+'&database='+jQuery('#database_rem').val()+'&format=row&id='+jQuery("#form_id").val());

		})





		jQuery('html').click(function() {



			if(jQuery("#fieldlist").css('display')=="block")

			{

				jQuery("#fieldlist").hide();

			}

		});



		jQuery('.cols input[type="text"]').live('click', function() {

			event.stopPropagation();

			jQuery("#fieldlist").css("top",jQuery(this).offset().top+jQuery(this).height()+2);

			jQuery("#fieldlist").css("left",jQuery(this).offset().left);

			jQuery("#fieldlist").slideDown('fast');

			selected_field=this.id;



		});



		jQuery('#query_txt').click(function() {

			event.stopPropagation();

			jQuery("#fieldlist").css("top",jQuery(this).offset().top+jQuery(this).height()+2);

			jQuery("#fieldlist").css("left",jQuery(this).offset().left);

			jQuery("#fieldlist").slideDown('fast');

			selected_field=this.id;

		});



		jQuery('#fieldlist').click(function(event){

			event.stopPropagation();

		});

		

		jQuery('.add_cond').click( function() {

				jQuery('.cols').append(conds.replace(/condid/g, cond_id++));

				update_vis();

			}

		);

	});

	var selected_field ='';

	var isvisible = 1;

	var cond_id = 1;



	conds='<?php echo $cond ?>';

			

	fields=new Array(<?php

		

		$fields = "";

		

		if($table_struct)

		{

			foreach($table_struct as $col)

			{

				$fields.=' "'.$col->Field.'",';

			}

		

		

			echo  substr($fields, 0, -1);

		}

		?>);



	function dis(id, x)

	{

		if(x)

			jQuery('#'+id).removeAttr('disabled');

		else

			jQuery('#'+id).attr('disabled','disabled');

		

	}



	function update_vis()

	{

		previous=0;

		for(i=1; i<cond_id; i++)

		{



			if(jQuery('#'+i).html())

			{	

				jQuery('#andor_'+i).css('visibility', 'hidden');

				

				if(previous)

					jQuery('#andor_'+previous).css('visibility', 'visible');

					

				previous=i;

			}

		}



	}



	function delete_cond(id)

	{

		jQuery('#'+id).remove();

		update_vis();

	}



	function save_query()

	{

		con_type	=jQuery('input[name=con_type]:checked').val();

		con_method	=jQuery('input[name=con_method]:checked').val();

		table		=jQuery('#tables').val();

		host		=jQuery('#host_rem').val();

		port		=jQuery('#port_rem').val();

		username	=jQuery('#username_rem').val();

		password	=jQuery('#password_rem').val();

		database	=jQuery('#database_rem').val();

		

		

		

		str=con_type+"***wdfcon_typewdf***"+con_method+"***wdfcon_methodwdf***"+table+"***wdftablewdf***"+host+"***wdfhostwdf***"+port+"***wdfportwdf***"+username+"***wdfusernamewdf***"+password+"***wdfpasswordwdf***"+database+"***wdfdatabasewdf***";

		

		if(fields.length)

		{

			for(i=0; i<fields.length; i++)

				str+=fields[i]+'***wdfnamewdf***'+jQuery('#'+fields[i]).val()+'***wdfvaluewdf***'+jQuery('#ch_'+fields[i]+":checked" ).length+'***wdffieldwdf***';

		}

		

		for(i=1; i<cond_id; i++)

		{

			if(jQuery('#'+i).html())

			{

				str+=jQuery('#sel_'+i).val()+'***sel***'+jQuery('#op_'+i).val()+'***op***'+jQuery('#val_'+i).val()+'***val***'+jQuery('#andor_'+i).val()+'***where***';

			}

		}

		

		if(!jQuery('#query_txt').val())

		{

			gen_query();

		}

		

		jQuery('#details').val(str);



		var datatxt = jQuery("#query_form").serialize()+'&form_id='+jQuery("#form_id").val(); 

		

		if(jQuery('#query_txt').val())

		{

				jQuery('.c1').html('<div id="saving"><div id="saving_text">Saving</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');



				jQuery.ajax({

					   type: "POST",

					   url: "index.php?option=com_formmaker&task=save_query",

					   data: datatxt,

					   success: function(data)

					   {

							window.parent.Joomla.submitbutton('apply_form_options');

							window.parent.SqueezeBox.close(); 

					   }

					 });

		}

		else

		{

			alert('The query is empty.');

		}

		return false; 





	}



	function gen_query()

	{

		if(jQuery('#query_txt').val())

			if(!confirm('Are you sure you want to replace the Query? All the modifications to the Query will be lost.'))

				return;

				

		query="";

		fields=new Array(<?php

		

		$fields = "";

		

		if($table_struct)

		{

			foreach($table_struct as $col)

			{

				$fields.=' "'.$col->Field.'",';

			}

		

		

			echo  substr($fields, 0, -1);

		}

		?>);



		con_type	=jQuery('input[name=con_type]:checked').val();

		con_method	=jQuery('input[name=con_method]:checked').val();

		table		=jQuery('#tables').val();



		

		fls='';

		vals='';	

		valsA=new Array();

		flsA=new Array();

		

		if(fields.length)

		{

			for(i=0; i<fields.length; i++)

				if(jQuery('#ch_'+fields[i]+":checked" ).length)

				{

					flsA.push(fields[i]);

					valsA.push(jQuery('#'+fields[i]).val());

				}

		}

		



		if(con_method=="insert")

		{

			if(flsA.length)

			{

				for(i=0; i<flsA.length-1; i++)

				{

						fls+= '`'+flsA[i]+'`, ';

						vals+= '"'+valsA[i]+'", ';

				}

						fls+= '`'+flsA[i]+'`';

						vals+= '"'+valsA[i]+'"';

			}

			

			if(fls)

				query="INSERT INTO "+jQuery('#tables').val()+" (" +fls+") VALUES ("+vals+")";

		}

		

		if(con_method=="update")

		{

			if(flsA.length)

			{

				for(i=0; i<flsA.length-1; i++)

				{

						vals+= '`'+flsA[i]+'`="'+valsA[i]+'", ';



				}

						vals+= '`'+flsA[i]+'`="'+valsA[i]+'"';

			}



			

			where="";

			previous='';

			

			for(i=1; i<cond_id; i++)

			{

				if(jQuery('#'+i).html())

				{

					if(jQuery('#op_'+i).val()=="%..%")

						op_val=' LIKE "%'+jQuery('#val_'+i).val()+'%"';

						

					else if(jQuery('#op_'+i).val()=="%..")

						op_val=' LIKE "%'+jQuery('#val_'+i).val()+'"';

						

					else if(jQuery('#op_'+i).val()=="..%")

						op_val=' LIKE "'+jQuery('#val_'+i).val()+'%"';

					

					else

						op_val=' '+jQuery('#op_'+i).val()+' "'+jQuery('#val_'+i).val()+'"';

						

					where+=previous+' `'+jQuery('#sel_'+i).val()+'`'+op_val;

					

					previous=' '+ jQuery('#andor_'+i).val();

				}

				

			}



			if(vals)

				query="UPDATE "+jQuery('#tables').val()+" SET " + vals+(where? ' WHERE'+where: '') ;

		}



		if(con_method=="delete")

		{

		

			where="";

			previous='';

			

			for(i=1; i<cond_id; i++)

			{

				if(jQuery('#'+i).html())

				{

					if(jQuery('#op_'+i).val()=="%..%")

						op_val=' LIKE "%'+jQuery('#val_'+i).val()+'%"';

						

					else if(jQuery('#op_'+i).val()=="%..")

						op_val=' LIKE "%'+jQuery('#val_'+i).val()+'"';

						

					else if(jQuery('#op_'+i).val()=="..%")

						op_val=' LIKE "'+jQuery('#val_'+i).val()+'%"';

					

					else

						op_val=' '+jQuery('#op_'+i).val()+' "'+jQuery('#val_'+i).val()+'"';

						

					where+=previous+' '+jQuery('#sel_'+i).val()+op_val;

					

					previous=' '+ jQuery('#andor_'+i).val();

				}

				

			}

			//DELETE FROM `jos_categories` WHERE  `id` = 'asdgasdg' 

			if(where)

				query="DELETE FROM "+jQuery('#tables').val()+ ' WHERE'+where ;

		}

		

		jQuery('#query_txt').val(query);

	}



	window.addEvent('domready', function() {

				$$('.hasTip').each(function(el) {

					var title = el.get('title');

					if (title) {

						var parts = title.split('::', 2);

						el.store('tip:title', parts[0]);

						el.store('tip:text', parts[1]);

					}

				});

				var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});

			});



	function insert_field(myValue) {  

	  

		if(!selected_field)

			return; 

		

		myField=document.getElementById(selected_field);

		

		if (document.selection) {      

		   myField.focus();      

		   sel = document.selection.createRange();    

		   sel.text = myValue;    

		   }    

		else

			if (myField.selectionStart || myField.selectionStart == '0') {     

			   var startPos = myField.selectionStart;       

			   var endPos = myField.selectionEnd;      

			   myField.value = myField.value.substring(0, startPos)           

			   +  "{"+myValue+"}"        

			   + myField.value.substring(endPos, myField.value.length);   

			} 

	   else {     

	   myField.value += "{"+myValue+"}";    

	   }

	}		

	</script>



	<style>

	label

	{

		display:inline;

	}

	.main_func

	{

		font-size: 12px;

		display:inline-block;

		width:480px;

		vertical-align:top;

	}

	

	.desc

	{

		font-size: 12px;

		display:inline-block;

		width:250px;

		position:fixed;

		margin:15px;

		padding-left:55px;

	}

	

	.desc button

	{

		max-width: 200px;

		overflow: hidden;

		white-space: nowrap;

		text-overflow: ellipsis;

	}

	

	.key label

	{

		display:inline-block;

		width:150px;

	}

	.cols

	{

		border: 3px solid red;

		padding: 4px;

	}





	.cols div:nth-child(even) {background: #FFF}

	.cols div:nth-child(odd) {background: #F5F5F5}



	.cols div

	{

	height: 28px;

	padding: 5px

	}



	.cols label

	{

	display:inline-block;

	width:200px;

	font-size:15px;

	overflow: hidden;

	white-space: nowrap;

	text-overflow: ellipsis;

	vertical-align: middle;

	}



	.cols input[type="text"]

	{

	width: 200px;

	line-height: 18px;

	height: 20px;

	margin:0px

	}



	.cols input[type="text"]:disabled

	{

	cursor: not-allowed;

	background-color: #eee;

	}



	.cols input[type="checkbox"]

	{

	width: 20px;

	line-height: 18px;

	height: 20px;

	vertical-align: middle;

	margin:5px

	}



	.cols select

	{

	line-height: 18px;

	width: inherit;

	margin: inherit;

	}



	#fieldlist

	{

	position: absolute;

	width:225px;

	background: #fff;

	border: solid 1px #c7c7c7;

	top: 0;

	left: 0;

	z-index: 1000;

	}



	#fieldlist a

	{

	padding: 5px;

	cursor:pointer;

	overflow: hidden;

	white-space: nowrap;

	text-overflow: ellipsis;

	}



	#fieldlist a:hover

	{

	background: #ccc;

	}



	.gen_query, .gen_query:focus

	{

	width: 200px !important;

	height: 38px;

	background: #0E73D4;

	color: white;

	cursor: pointer;

	border: 0px;

	font-size: 16px;

	font-weight: bold;

	margin: 20px;

	}



	.gen_query:active

	{

	background: #ccc;



	}

	</style>

	

	<div class="c1">

	<div class="main_func">

		<table class="admintable">

			<tr valign="top">

				<td  class="key">

					<label title="asf"> <?php echo JText::_( 'Connection type' ); ?>: </label>

				</td>

				<td >

					<input type="radio" name="con_type" id="local" value="local" <?php if($con_type=='local') echo 'checked="checked"'?> disabled>

					<label for="local">Local</label>

					<input type="radio" name="con_type" id="remote" value="remote"  <?php if($con_type=='remote') echo 'checked="checked"'?> disabled>

					<label for="remote">Remote</label>

				</td>

			</tr>

			<tr class="remote_info" <?php if($con_type=='local') echo 'style="display:none"'?>>

				<td class="key">Host</td>

				<td>

					<input type="text" name="host" id="host_rem" style="width:180px" value="<?php echo $host; ?>" disabled>

					Port : <input type="text" name="port" id="port_rem" value="<?php echo $port; ?>" style="width:50px" disabled>

				</td>

			</tr>

			<tr class="remote_info" <?php if($con_type=='local') echo 'style="display:none"'?>>

				<td class="key">Username</td>

				<td>

					<input type="text" name="username" id="username_rem"  style="width:272px" value="<?php echo $username; ?>" disabled>

				</td>

			</tr>

			<tr class="remote_info" <?php if($con_type=='local') echo 'style="display:none"'?>>

				<td  class="key">Password</td>

				<td>

					<input type="password" name="password" id="password_rem"  style="width:272px" value="<?php echo $password; ?>" disabled>

				</td>

			</tr>

			<tr class="remote_info" <?php if($con_type=='local') echo 'style="display:none"'?>>

				<td  class="key">Database</td>

				<td>

					<input type="text"name="database" id="database_rem"  style="width:272px" value="<?php echo $database; ?>" disabled>

				</td>

			</tr>

			<tr valign="top">

				<td  class="key">

					<label> <?php echo JText::_( 'Type' ); ?>: </label>

				</td>

				<td >

					<input type="radio" name="con_method" id="insert" value="insert" <?php if($con_method=='insert') echo 'checked="checked"'?> disabled>

					<label for="insert">Insert</label>

					<input type="radio" name="con_method" id="update" value="update" <?php if($con_method=='update') echo 'checked="checked"'?> disabled>

					<label for="update">Update</label>

					<input type="radio" name="con_method" id="delete" value="delete" <?php if($con_method=='delete') echo 'checked="checked"'?> disabled>

					<label for="delete">Delete</label>

				</td>

			</tr>

			<tr valign="top">

				<td  class="key">

				</td>

				<td >

					<input type="button" value="Connect" onclick="connect()" disabled class="btn">

				</td>

			</tr>

		</table>

		<div id="struct" style="margin-top:10px">

			<label for="tables" style="display:inline-block;width:157px;font-weight: bold; vertical-align:top">Select a table</label>

			<select name="tables" id="tables" disabled> 

				<option value="" ></option>

			<?php

			

			foreach($tables as $table)

				echo '<option value="'.$table.'" '.($table_cur==$table ? 'selected' : '').' >'.$table.'</option>';

			?>

			</select>

			<br/><br/>

			

			

			<div id="table_struct">



		<?php

	if($table_struct)

	{

	?>



	<div class="cols">

	<?php



	$temps=explode('***wdffieldwdf***',$details);

	$wheres = $temps[count($temps)-1];   

	$temps 	= array_slice($temps,0, count($temps)-1);   

	$col_names= array();

	$col_vals= array();

	$col_checks= array();



	foreach($temps as $temp)

	{

		$temp=explode('***wdfnamewdf***',$temp);

		array_push($col_names, $temp[0]);

		$temp=explode('***wdfvaluewdf***',$temp[1]);

		array_push($col_vals, $temp[0]);

		array_push($col_checks, $temp[1]);

	}

		if($con_method=='insert' or $con_method=='update')

		{

			echo '<div style="background: none;text-align: center;font-size: 20px;color: rgb(0, 164, 228);font-weight: bold;"> SET </div>';



			foreach($table_struct as $key =>$col)

			{

			

				$title=' '.$col->Field;

				$title.="<ul style='padding-left: 17px;'>";

				$title.="<li>Type - ".$col->Type."</li>";

				$title.="<li>Null - ".$col->Null."</li>";

				$title.="<li>Key - ".$col->Key."</li>";

				$title.="<li>Default - ".$col->Default."</li>";

				$title.="<li>Extra - ".$col->Extra."</li>";

				$title.="</ul>";

				

			?>

				<div><label title="<?php echo $title; ?>" class="hasTip"><b><?php echo $col->Field; ?></b><img src="components/com_formmaker/images/info.png" style="width:20px; vertical-align:middle;padding-left: 10px;" /></label><input type="text" id="<?php echo $col->Field; ?>" <?php if(!$col_checks[$key]) echo 'disabled="disabled"'?>  value="<?php echo $col_vals[$key]; ?>" /><input id="ch_<?php echo $col->Field; ?>"  type="checkbox" onClick="dis('<?php echo $col->Field; ?>', this.checked)" <?php if($col_checks[$key]) echo 'checked="checked"'?> /></div>

			<?php

			}

		}

		if($con_method=='delete' or $con_method=='update')

		{

			echo '<div style="background: none;text-align: center;font-size: 20px;color: rgb(0, 164, 228);font-weight: bold;"> WHERE </div>

			<img src="components/com_formmaker/images/add_condition.png" title="ADD" class="add_cond"/></br>';

			

			if($wheres)

			{

				echo '<script>';



				$wheres	=explode('***where***',$wheres);

				$wheres = array_slice($wheres,0, count($wheres)-1);   

				foreach($wheres as $where)

				{			

					$temp=explode('***sel***',$where);

					$sel = $temp[0];

					$temp=explode('***op***',$temp[1]);

					$op = $temp[0];

					$temp=explode('***val***',$temp[1]);

					$val = $temp[0];

					$andor = $temp[1];

					echo 'jQuery(".cols").append(conds.replace(/condid/g, cond_id++));	update_vis();

						jQuery("#sel_"+(cond_id-1)).val("'.$sel.'");

						jQuery("#op_"+(cond_id-1)).val("'.$op.'");

						jQuery("#val_"+(cond_id-1)).val("'.$val.'");

						jQuery("#andor_"+(cond_id-1)).val("'.$andor.'");

					';



				}

				echo '</script>';



			}			



			

		}

	?>

	<div style="color:red; background: none">The changes above will not affect the query until you click "Generate query".</div>

	</div>

	<br/>

	<input type="button" value="Generate Query" class="gen_query" onclick="gen_query()">

	<br/>

	<form name="query_form" id="query_form">

		<label style="vertical-align: top;" for="query_txt" > Query: </label><textarea id="query_txt" name="query" rows=5 style="width:400px" ><?php echo $query_obj->query; ?></textarea>

		<input type="hidden" name="details" id="details">

		<input type="hidden" name="id" value="<?php echo $query_obj->id; ?>">

	</form>

	<input type="button" value="Save" style="float: right;width: 200px;height: 38px;background: #0E73D4;color: white;cursor: pointer;border: 0px;font-size: 16px;font-weight: bold;margin: 20px;" onclick="save_query()">

	

	

	<div id="fieldlist" style="display: none;">

	<?php echo $form_fields ?>

	</div>

	



			<?php

	}

	?>

			</div>

		</div>

		<input type="hidden" value="<?php echo $id ?>" id="form_id">



	</div>



	<div class="desc">

	<?php

	foreach($label_id as $key => $lid)

	{

		echo '<div>{'.$lid.'} - <button class="btn" onclick="insert_field('.$lid.');">'.$label_order_original[$key].'</button></div>';



	}

	$user_fields = array("subid"=>"Submission ID", "ip"=>"Submitter's IP", "userid"=>"User ID", "username"=>"Username", "useremail"=>"User Email");
	foreach($user_fields as $user_key=>$user_field) {
		echo '<div><span>{'.$user_key.'}</span> - <button class="btn" onclick="insert_field(\''.$user_key.'\');">'.$user_field.'</button></div>';
	}

	?>

	</div>

	</div>

	<?php

}



public static function select_data_from_db($id, $field_id, $field_type, $value_disabled){



		JHtml::_('behavior.tooltip');

		JHtml::_('behavior.formvalidation');

		JHtml::_('behavior.switcher');

		JHtml::_('formbehavior.chosen', 'select');

		jimport('joomla.filesystem.path');

		jimport('joomla.filesystem.file');

		

		$document		= JFactory::getDocument();

		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/style.css?version=1.2');

		

?>

	<script>

	function insert_field(){}

	

	function connect()

	{

		jQuery("input[type='radio']").attr('disabled','');

		jQuery(".connect").attr('disabled','');

		jQuery('#struct').html('<div id="saving"><div id="saving_text">Loading</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');





		jQuery.ajax({

			   type: "POST",

			   url: "index.php?option=com_formmaker&task=db_tables",

			   data: 'con_type='+jQuery('input[name=con_type]:checked').val()+'&con_method='+jQuery('input[name=con_method]:checked').val()+'&host='+jQuery('#host_rem').val()+'&port='+jQuery('#port_rem').val()+'&username='+jQuery('#username_rem').val()+'&password='+jQuery('#password_rem').val()+'&database='+jQuery('#database_rem').val()+'&field_type='+jQuery('#field_type').val()+'&format=row',

			   success: function(data)

			   {

					if(data==1)

					{

						jQuery("#struct").html('<div style="font-size: 22px; text-align: center; padding-top: 15px;">Could not connect to MySQL.</div>')

						jQuery(".connect").removeAttr('disabled');

						jQuery("input[type='radio']").removeAttr('disabled','');

					}

					else

						jQuery("#struct").html(data)

				

			   }

			 });



	}

	

	function shh(x)

	{

		if(x)

			jQuery(".remote_info").show();

		else

			jQuery(".remote_info").hide();

	}

		

	</script>

	<style>

	label

	{

		display:inline;

		margin-bottom: 5px;

	}

	

	.main_func

	{

		font-size: 12px;

		display:inline-block;

		width:480px;

		vertical-align:top;

	}



	.key label

	{

		display:inline-block;

		width:150px;

	}

	

	</style>

	

	<div class="c1">

	<div class="main_func">

		<table class="admintable">

			<tr valign="top">

				<td  class="key">

					<label style="font-weight:bold;"> <?php echo JText::_( 'Connection type' ); ?>: </label>

				</td>

				<td >

					<input type="radio" name="con_type" id="local" value="local" checked="checked" onclick="shh(false)">

					<label for="local">Local</label>

					<input type="radio" name="con_type" id="remote" value="remote" onclick="shh(true)">

					<label for="remote">Remote</label>

				</td>

			</tr>

			<tr class="remote_info" style="display:none">

				<td class="key">Host</td>

				<td>

					<input type="text" name="host" id="host_rem" style="width:180px">

					Port : <input type="text" name="port" id="port_rem" value="3306" style="width:50px">

				</td>

			</tr>

			<tr class="remote_info" style="display:none">

				<td class="key">Username</td>

				<td>

					<input type="text" name="username" id="username_rem"  style="width:272px">

				</td>

			</tr>

			<tr class="remote_info" style="display:none">

				<td  class="key">Password</td>

				<td>

					<input type="password" name="password" id="password_rem"  style="width:272px">

				</td>

			</tr>

			<tr class="remote_info" style="display:none">

				<td  class="key">Database</td>

				<td>

					<input type="text"name="database" id="database_rem"  style="width:272px">

				</td>

			</tr>

			<tr valign="top" style="display:none;">

				<td  class="key">

					<label> <?php echo JText::_( 'Type' ); ?>: </label>

				</td>

				<td >

					<input type="radio" name="con_method" id="select" value="select" checked="checked">

					<label for="select">Select</label>			

				</td>

				

			</tr>

			

			<tr valign="top">

				<td  class="key">

				</td>

				<td >

					<input type="button" value="Connect" onclick="connect()"  class="btn connect">

				</td>

			</tr>

		</table>

		<div id="struct" style="margin-top:10px">

		</div>

		<input type="hidden" id="form_id" value="<?php echo $id ?>" >

		<input type="hidden" id="form_field_id" value="<?php echo $field_id ?>" >

		<input type="hidden" id="field_type" value="<?php echo $field_type ?>" >

		<input type="hidden" id="value_disabled" value="<?php echo $value_disabled ?>" >



	</div>



	</div>



	<?php

	

}



public static function add_query($id,$label ){



		JHtml::_('behavior.tooltip');

		JHtml::_('behavior.formvalidation');

		JHtml::_('behavior.switcher');

		JHtml::_('formbehavior.chosen', 'select');

		jimport('joomla.filesystem.path');

		jimport('joomla.filesystem.file');

		

		$document		= JFactory::getDocument();

	//	$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js');

		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/style.css?version=1.2');



		$filter_types=array("type_submit_reset", "type_map", "type_editor", "type_captcha", "type_recaptcha", "type_button", "type_paypal_total", "type_send_copy");

		$label_id= array();

		$label_order= array();

		$label_order_original= array();

		$label_type= array();

	

		///stexic

		$label_all	= explode('#****#',$label);

		$label_all 	= array_slice($label_all,0, count($label_all)-1);   

	

		foreach($label_all as $key => $label_each) 

		{

			$label_id_each=explode('#**id**#',$label_each);

			$label_oder_each=explode('#**label**#', $label_id_each[1]);

			

			if(in_array($label_oder_each[1],$filter_types))

				continue;

				

			array_push($label_id, $label_id_each[0]);

		

		

			array_push($label_order_original, $label_oder_each[0]);

		

			$ptn = "/[^a-zA-Z0-9_]/";

			$rpltxt = "";

			$label_temp=preg_replace($ptn, $rpltxt, $label_oder_each[0]);

			array_push($label_order, $label_temp);

		

			array_push($label_type, $label_oder_each[1]);

		}





?>

	<script>

	function insert_field(){}

	

	function connect()

	{

		jQuery("input[type='radio']").attr('disabled','');

		jQuery(".connect").attr('disabled','');

		jQuery('#struct').html('<div id="saving"><div id="saving_text">Loading</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');





		jQuery.ajax({

			   type: "POST",

			   url: "index.php?option=com_formmaker&task=db_tables",

			   data: 'con_type='+jQuery('input[name=con_type]:checked').val()+'&con_method='+jQuery('input[name=con_method]:checked').val()+'&host='+jQuery('#host_rem').val()+'&port='+jQuery('#port_rem').val()+'&username='+jQuery('#username_rem').val()+'&password='+jQuery('#password_rem').val()+'&database='+jQuery('#database_rem').val()+'&format=row',

			   success: function(data)

			   {

					if(data==1)

					{

						jQuery("#struct").html('<div style="font-size: 22px; text-align: center; padding-top: 15px;">Could not connect to MySQL.</div>')

						jQuery(".connect").removeAttr('disabled');

						jQuery("input[type='radio']").removeAttr('disabled','');

					}

					else

						jQuery("#struct").html(data)

				

			   }

			 });



	}

	

	function shh(x)

	{

		if(x)

			jQuery(".remote_info").show();

		else

			jQuery(".remote_info").hide();

	}

		

	</script>

	<style>

	label

	{

		display:inline;

		margin-bottom: 5px;

	}

	

	.main_func

	{

		font-size: 12px;

		display:inline-block;

		width:480px;

		vertical-align:top;

	}

	

	.desc

	{

		font-size: 12px;

		display:inline-block;

		width:250px;

		position:fixed;

		margin:15px;

		margin-left:55px;

	}

	

	.desc button

	{

		max-width: 200px;

		overflow: hidden;

		white-space: nowrap;

		text-overflow: ellipsis;

	}

	

	.key label

	{

		display:inline-block;

		width:150px;

	}

	

	</style>

	

	<div class="c1">

	<div class="main_func">

		<table class="admintable">

			<tr valign="top">

				<td  class="key">

					<label title="asf"> <?php echo JText::_( 'Connection type' ); ?>: </label>

				</td>

				<td >

					<input type="radio" name="con_type" id="local" value="local" checked="checked" onclick="shh(false)">

					<label for="local">Local</label>

					<input type="radio" name="con_type" id="remote" value="remote" onclick="shh(true)">

					<label for="remote">Remote</label>

				</td>

			</tr>

			<tr class="remote_info" style="display:none">

				<td class="key">Host</td>

				<td>

					<input type="text" name="host" id="host_rem" style="width:180px">

					Port : <input type="text" name="port" id="port_rem" value="3306" style="width:50px">

				</td>

			</tr>

			<tr class="remote_info" style="display:none">

				<td class="key">Username</td>

				<td>

					<input type="text" name="username" id="username_rem"  style="width:272px">

				</td>

			</tr>

			<tr class="remote_info" style="display:none">

				<td  class="key">Password</td>

				<td>

					<input type="password" name="password" id="password_rem"  style="width:272px">

				</td>

			</tr>

			<tr class="remote_info" style="display:none">

				<td  class="key">Database</td>

				<td>

					<input type="text"name="database" id="database_rem"  style="width:272px">

				</td>

			</tr>

			<tr valign="top">

				<td  class="key">

					<label> <?php echo JText::_( 'Type' ); ?>: </label>

				</td>

				<td >

					<input type="radio" name="con_method" id="insert" value="insert" checked="checked">

					<label for="insert">Insert</label>

					<input type="radio" name="con_method" id="update" value="update">

					<label for="update">Update</label>

					<input type="radio" name="con_method" id="delete" value="delete">

					<label for="delete">Delete</label>

				</td>

			</tr>

			<tr valign="top">

				<td  class="key">

				</td>

				<td >

					<input type="button" value="Connect" onclick="connect()"  class="btn connect">

				</td>

			</tr>

		</table>

		<div id="struct" style="margin-top:10px">

		</div>

		<input type="hidden" value="<?php echo $id ?>" id="form_id">



	</div>



	<div class="desc">

	<?php

	foreach($label_id as $key => $lid)

	{

		echo '<div>{'.$lid.'} - <button onclick="insert_field('.$lid.');" class="btn">'.$label_order_original[$key].'</button></div>';

	}

	$user_fields = array("subid"=>"Submission ID", "ip"=>"Submitter's IP", "userid"=>"User ID", "username"=>"Username", "useremail"=>"User Email");
	foreach($user_fields as $user_key=>$user_field) {
		echo '<div>{'.$user_key.'} - <button class="btn" onclick="insert_field(\''.$user_key.'\');">'.$user_field.'</button></div>';
	}

	?>

	</div>

	</div>

	

	<?php

}



public static function form_options(&$row, &$themes, &$queries, &$tabs, &$userGroups){

		

		$document		= JFactory::getDocument();

		JHtml::_('behavior.tooltip');

		JHtml::_('behavior.formvalidation');

		JHtml::_('behavior.switcher');

		JHtml::_('formbehavior.chosen', 'select');

		jimport('joomla.filesystem.path');

		jimport('joomla.filesystem.file');



		JRequest::setVar( 'hidemainmenu', 1 );

		$user = JFactory::getUser();

		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';



		$is_editor=false;

		

		$plugin = JPluginHelper::getPlugin('editors', 'tinymce');

		if (isset($plugin->type))

		{ 

			$editor	= JFactory::getEditor('tinymce');

			$is_editor=true;

		}

		

		$editor	= JFactory::getEditor('tinymce');



		$value="";



		$article = JTable::getInstance('content');

		if ($value) {

			$article->load($value);

		} else {

			$article->title = JText::_('Select an Article');

		}

		

			$label_id= array();		

			$label_label= array();		

			$label_type= array();			

			$label_all	= explode('#****#',$row->label_order_current);		

			$label_all 	= array_slice($label_all,0, count($label_all)-1);   	

			

		foreach($label_all as $key => $label_each) 		

		{			

			$label_id_each=explode('#**id**#',$label_each);			

			array_push($label_id, $label_id_each[0]);					

		

		$label_order_each=explode('#**label**#', $label_id_each[1]);				

			array_push($label_label, $label_order_each[0]);		

			array_push($label_type, $label_order_each[1]);		

		}			

		

		?>



<script language="javascript" type="text/javascript">

Joomla.submitbutton= function (pressbutton)

{

	var form = document.adminForm;

	if (pressbutton == 'cancel') 

	{

		submitform( pressbutton );

		return;

	}

	

	if(form.mail.value!='')

	{

		subMailArr=form.mail.value.split(',');

		emailListValid=true;

		for(subMailIt=0; subMailIt<subMailArr.length; subMailIt++)

		{

		trimmedMail = subMailArr[subMailIt].replace(/^\s+|\s+$/g, '') ;

		if (trimmedMail.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1)

		{

					alert( "This is not a list of valid Email addresses." );	

					emailListValid=false;

					break;

		}

		}

		if(!emailListValid)	

		return;

	}	



	field_condition ='';

	

	

	jQuery('.cond_div').each(function() {

			conditions = '';

			cond_id = jQuery(this)[0].id.replace('condition','');

			field_condition += jQuery("#show_hide"+cond_id).val()+"*:*show_hide*:*";

			field_condition += jQuery("#fields"+cond_id).val()+"*:*field_label*:*";

			field_condition += jQuery("#all_any"+cond_id).val()+"*:*all_any*:*";

			this2 = this;

			

			jQuery(this2).find(jQuery('.cond_fields')).each(function() {

				cond_fieldid = jQuery(this)[0].id.replace('condition_div'+cond_id+'_','');

				conditions += jQuery("#field_labels"+cond_id+"_"+cond_fieldid).val()+"***";

				conditions += jQuery("#is_select"+cond_id+"_"+cond_fieldid).val()+"***";



				if(jQuery("#field_value"+cond_id+"_"+cond_fieldid).prop("tagName")=="SELECT" && jQuery("#field_value"+cond_id+"_"+cond_fieldid).attr('multiple'))

				{

			

					sel = jQuery("#field_value"+cond_id+"_"+cond_fieldid)[0];

						

					selValues = '';

					for(m=0; m < sel.length; m++)

					{

						if(sel.options[m].selected)

							selValues += sel.options[m].value+"@@@";

					}



					conditions+=selValues;

				}

				else

					conditions+=jQuery("#field_value"+cond_id+"_"+cond_fieldid).val();

				conditions+="*:*next_condition*:*";	

			});	

			

			field_condition+=conditions;

			field_condition+="*:*new_condition*:*";

			

		});	



	document.getElementById('condition').value = field_condition;

	

	document.getElementById('tabs').value = jQuery('li[class="active"] a').attr('href').replace('#','');

	

	submitform( pressbutton );

}



function remove_query()

{

	submitform( 'remove_query' );

}

function wdhide(id)

{

	document.getElementById(id).style.display="none";

}

function wdshow(id)

{

	document.getElementById(id).style.display="block";

}



function check_isnum(e)

{

	

   	var chCode1 = e.which || e.keyCode;

    	if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57))

        return false;

	return true;

}

function check_isnum_point(e)

{

   	var chCode1 = e.which || e.keyCode;

	

	if (chCode1 ==46)

		return true;

	

	if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57))

        return false;

	return true;

}



function check_isnum_space(e)

{

	

	var chCode1 = e.which || e.keyCode;

	

	if (chCode1 ==32)

		return true;

		

		if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57))

		return false;

	return true;

}

function jSelectArticle(id, title, object) {

			document.getElementById('article_id').value = id;

			document.getElementById(object + '_name').value = title;

			document.getElementById('sbox-window').close();

			}

			

function remove_article()

{

	document.getElementById('id_name').value="Select an Article";

	document.getElementById('article_id').value="";

}



function set_type(type)

{

	switch(type)

	{

		case '2':

			document.getElementById('article').removeAttribute('style');

			document.getElementById('custom1').setAttribute('style','display:none');

			document.getElementById('url').setAttribute('style','display:none');

			document.getElementById('none').setAttribute('style','display:none');

			break;

			

		case '3':

			document.getElementById('article').setAttribute('style','display:none');

			document.getElementById('custom1').removeAttribute('style');

			document.getElementById('url').setAttribute('style','display:none');

			document.getElementById('none').setAttribute('style','display:none');

			break;

			

		case '4':

			document.getElementById('article').setAttribute('style','display:none');

			document.getElementById('custom1').setAttribute('style','display:none');

			document.getElementById('url').removeAttribute('style');

			document.getElementById('none').setAttribute('style','display:none');

			break;

			

		case '1':

			document.getElementById('article').setAttribute('style','display:none');

			document.getElementById('custom1').setAttribute('style','display:none');

			document.getElementById('url').setAttribute('style','display:none');

			document.getElementById('none').removeAttribute('style');

			break;

	}

}



function insertAtCursorform(myField, myValue) {  

	if(myField.style.display=="none")

	{



		tinyMCE.execCommand('mceInsertContent',false,"%"+myValue+"%");

		return;

	}



	

	   if (document.selection) {      

	   myField.focus();      

	   sel = document.selection.createRange();    

	   sel.text = myValue;    

	   }    

   else

		if (myField.selectionStart || myField.selectionStart == '0') {     

		   var startPos = myField.selectionStart;       

		   var endPos = myField.selectionEnd;      

		   myField.value = myField.value.substring(0, startPos)           

		   +  "%"+myValue+"%"        

		   + myField.value.substring(endPos, myField.value.length);   

		} 

   else {     

   myField.value += "%"+myValue+"%";    

   }





   }



function wdhide(id)

{

	document.getElementById(id).style.display="none";

}

function wdshow(id)

{

	document.getElementById(id).style.display="block";

}

   

   

function set_preview()

{

	appWidth			=parseInt(document.body.offsetWidth);

	appHeight			=parseInt(document.body.offsetHeight);



document.getElementById('modalbutton').href='<?php echo JURI::root(true) ?>/index.php?option=com_formmaker&amp;id=<?php echo $row->id ?>&tmpl=component&amp;test_theme='+document.getElementById('theme').value;

//document.getElementById('modalbutton').setAttribute("rel","{handler: 'iframe', size: {x:"+(appWidth-100)+", y: "+(appHeight-100)+"}}");



}





function add_condition()

{

	var max_id = 0;

	jQuery('.cond_div').each(function() {

		var value = parseInt(jQuery(this)[0].id.replace('condition',''));

		max_id = (value >= max_id) ? value+1 : max_id;

	});

	jQuery("#conditions").append(jQuery('<div id="condition'+max_id+'" class="cond_div">').load('index.php?option=com_formmaker&task=add_condition&form_id=<?php echo $row->id; ?>&cond_index='+max_id+'&format=row'));

}



function delete_condition(num)

{

	jQuery('#conditions').find(jQuery('#condition'+num)).remove();

}



function delete_field_condition(id)

{

	jQuery('#condition_div'+id).remove();

}





function acces_level(length)

{

	var value='';

	for(i=0; i<parseInt(length); i++)

		{

			if (document.getElementById('user_'+i).checked)

			{

				value=value+document.getElementById('user_'+i).value+',';			

			}	

		}

	document.getElementById('user_id').value=value;

}

			





document.switcher = null;

			window.addEvent('domready', function(){

				toggler = document.id('submenu');

				element = document.id('config-document');

				if (element) {

					document.switcher = new JSwitcher(toggler, element, {cookieName: toggler.getProperty('class')});

				}

			});



			

gen="<?php echo $row->counter; ?>";

form_view_max=20;

</script>

<style>

.borderer

{

border-radius:5px;

padding-left:5px;

background-color:#F0F0F0;

height:19px;

width:153px;

}



fieldset.adminform1 {

border-radius: 7px;

border: 1px solid #CCC;

padding: 13px;

margin-top: 20px;

}



label

{

display:inline;

}



.btn-group.btn-group-yesno > .btn {

min-width: 84px;

padding: 2px 12px;

}



.admintable tr

{

margin-bottom: 18px;

}

#theme_chzn{

vertical-align: top;

}



.cond_div

{

margin-top:20px;

}



.cond_div > div

{

margin-top:4px;

}



.email_labels

{

position: absolute;

background: #fff;

border: solid 1px #c7c7c7;

top: 0;

left: 0;

z-index: 1000;

}



.email_labels a

{

padding: 5px;

cursor:pointer;

}



.email_labels a:hover

{

background: #ccc;

}



.chzn-container

{

vertical-align:top;

}

</style>









<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="row-fluid">

		<div class="span12 form-horizontal">

			<ul class="nav nav-tabs">

				<li <?php if($tabs=="general_op") { ?> class="active" <?php } ?> ><a href="#general_op" data-toggle="tab">General Options</a></li>

				<li <?php if($tabs=="email_op") { ?> class="active" <?php } ?>><a href="#email_op" data-toggle="tab">Email Options</a></li>

				<li <?php if($tabs=="actions_op") { ?> class="active" <?php } ?>><a href="#actions_op" data-toggle="tab">Actions after Submission</a></li>

				<li <?php if($tabs=="payment_op") { ?> class="active" <?php } ?>><a href="#payment_op" data-toggle="tab">Payment Options</a></li>

				<li <?php if($tabs=="javascript_op") { ?> class="active" <?php } ?>><a href="#javascript_op" data-toggle="tab">JavaScript</a></li>

				<li <?php if($tabs=="conditional_op") { ?> class="active" <?php } ?>><a href="#conditional_op" data-toggle="tab">Conditional Fields</a></li>

				<li <?php if($tabs=="mapping_op") { ?> class="active" <?php } ?>><a href="#mapping_op" data-toggle="tab">MySQL Mapping</a></li>

				

			</ul>

			

			<div class="tab-content">

				<div class="tab-pane  <?php if($tabs=="general_op") { ?> active <?php } ?>" id="general_op">

					<div class="row-fluid">

						<div class="span12">

							<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Published' ); ?>: </label>

								</div>

								<div class="controls">

									<fieldset class="radio btn-group btn-group-yesno">

										<input type="radio" id="publishedyes" name="published" value="1" <?php if($row->published==1 ) echo "checked='checked'" ?> />

										<label for="publishedyes">Yes</label>

										<input type="radio" id="publishedno" name="published" value="0" <?php if($row->published==0 ) echo "checked='checked'" ?> />

										<label for="publishedno">No</label>

									</fieldset>	

								</div>

							</div>

							<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Save data(to database)' ); ?>: </label>

								</div>

								<div class="controls">

									<fieldset class="radio btn-group btn-group-yesno">

										<input type="radio" id="savedbyes" name="savedb" value="1" <?php if($row->savedb==1 ) echo "checked='checked'" ?> />

										<label for="savedbyes">Yes</label>

										<input type="radio" id="savedbno" name="savedb" value="0" <?php if($row->savedb==0 ) echo "checked='checked'" ?> />

										<label for="savedbno">No</label>

									</fieldset>	

								</div>

							</div>

							

							<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Theme' ); ?>: </label>

								</div>

								<div class="controls">

									<select id="theme" name="theme" onChange="set_preview()" >

									<?php 

									foreach($themes as $theme) 

									{

										if($theme->id==$row->theme)

										{

											echo '<option value="'.$theme->id.'" selected>'.$theme->title.'</option>';

										}

										else

											echo '<option value="'.$theme->id.'">'.$theme->title.'</option>';

									}

									?>

									</select>

									<a class="modal" id="modalbutton" href="<?php echo JURI::root(true) ?>/index.php?option=com_formmaker&amp;id=<?php echo $row->id ?>&tmpl=component&amp;test_theme=<?php echo $row->theme ?>" rel="{handler: 'iframe', size: {x:1000, y: 520}}">

										<div class="btn">

												<span class="icon-search" title="Preview" >

												</span>

											Preview

										</div>

									</a>

									<?php if($user->authorise('core.create', 'com_formmaker') || $user->authorise('core.edit', 'com_formmaker')): ?>

									<a class="modal" id="add_theme" href="index.php?option=com_formmaker&amp;task=edit_css&amp;tmpl=component&amp;theme=<?php echo $row->theme ?>&amp;form_id=<?php echo $row->id ?>&amp;new=0" rel="{handler: 'iframe', size: {x:800, y: 450}}">

										<div class="btn">

												<span class="icon-edit" title="Edit Css" >

												</span>

											Edit CSS

										</div>

									</a>	

									<?php endif; ?>

								</div>

								

							</div>

							<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Required fields mark' ); ?>: </label>

								</div>

								<div class="controls">

									<input type="text" id="requiredmark" name="requiredmark" value="<?php echo $row->requiredmark ?>" />

								</div>

							</div>

							<fieldset>

								<legend style="font-size: 18px;padding:0px 10px;">Front end submissions access level</legend>

								<div class="control-group">

									<div class="control-label">

										<label>

											<?php echo JText::_( 'Allow User to see submissions' ); ?>:

										</label>

									</div>

									<div class="controls" style="margin-left:200px !important;">

										<?php

											$checked_UserGroup=explode(',',$row->user_id);

										

											for($i=0;$i<count($userGroups);$i++)

											{

											

												echo '<input type="checkbox" value="'.$userGroups[$i]->id .'"  id="user_'.$i.'"'; 

												

												if(in_array($userGroups[$i]->id ,$checked_UserGroup))

												echo 'checked="checked"';

												

												echo 'onchange="acces_level('.count($userGroups).')"/><label for="user_'.$i.'" style="margin-left:3px;">'.$userGroups[$i]->title.'</label><br>' ; 

											

											} 

											?>

											<input type="hidden" name="user_id" value="<?php echo $row->user_id ?>" id="user_id" />

										</div>

							</fieldset>



						</div>

					</div>

				</div>

				

				<div class="tab-pane <?php if($tabs=="email_op") { ?> active <?php } ?>" id="email_op">

					<div class="control-group">

						<div class="control-label">

							<label> <?php echo JText::_( 'Send E-mail' ); ?>: </label>

						</div>

						<div class="controls">

								<fieldset class="radio btn-group btn-group-yesno">

									<input type="radio" id="sendemailyes" name="sendemail" value="1" <?php if($row->sendemail==1 ) echo "checked='checked'" ?> />

									<label for="sendemailyes">Yes</label>

									<input type="radio" id="sendemailno" name="sendemail" value="0" <?php if($row->sendemail==0 ) echo "checked='checked'" ?> />

									<label for="sendemailno">No</label>

								</fieldset>	

						</div>

					</div>

					<div class="row-fluid">

						<div class="span6" style="">

							<fieldset class="form-horizontal">

								<legend>Email to Administrator</legend>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'Email to Send Submissions to' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail" name="mail" value="<?php echo $row->mail ?>" style="width:250px;" />

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'Email From' ); ?>: </label>

									</div>

									<div class="controls">

											<?php 

												$fields =$row->form_fields;

												$fields=explode('*:*id*:*type_submitter_mail*:*type*:*', $fields);

												$n=count($fields);

												$is_other=true;



												for($i=0; $i<$n-1; $i++)

												{

													echo '<div style="height: 20px;"><input type="radio" name="mail_from" id="mail_from'.$i.'" value="'.(strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]).'"  '.((strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]) == $row->mail_from ? 'checked="checked"' : '' ).' style="margin:0px 5px 0px 0px" onclick="wdhide(\'mail_from_other\')"/><label for="mail_from'.$i.'" style="cursor:pointer">'.substr($fields[$i+1], 0, strpos($fields[$i+1], '*:*w_field_label*:*')).'</label></div>';

													

													if(strlen($fields[$i])!=1)

													{

														if(substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) == $row->mail_from)

															$is_other=false;

													}

													else

													{

														if($fields[$i] == $row->mail_from)

														$is_other=false;

													}

												}

												



											?>

											<div style="height: 20px; <?php if($n==1) echo 'display:none;' ?>">

											<input type="radio" id="other" name="mail_from" value="other" <?php if($is_other) echo 'checked="checked"' ;?> style="margin:0px 5px 0px 0px" onclick="wdshow('mail_from_other')" />

												<label for="other" style="cursor:pointer">Other</label>

											</div>

											<input type="text" style="<?php if($n==1) echo 'width:250px'; else  echo 'width:230px' ?>; margin:0px; <?php if($n!=1) echo 'margin-left:20px' ?>;<?php if($is_other) echo 'display:block'; else  echo 'display:none';?>" id="mail_from_other" name="mail_from_other" value="<?php if($is_other)  echo $row->mail_from ?>" style="width:250px;" />

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'From Name' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_from_name" name="mail_from_name" value="<?php echo $row->mail_from_name ?>" style="width:250px;" />

									<img src="components/com_formmaker/images/add.png" onclick="document.getElementById('mail_from_labels').style.display='block';" style="vertical-align: middle; display:inline-block; margin:0px; float:none;">

									<?php 

									$choise = 'document.getElementById(\'mail_from_name\')';

									echo '<div style="position:relative; top:-3px;"><div id="mail_from_labels" class="email_labels" style="display:none;">';	

									

									for($i=0; $i<count($label_label); $i++)			

									{ 			

										if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy" || $label_type[$i]=="type_matrix")			

										continue;		

										

										$param = htmlspecialchars(addslashes($label_label[$i]));			

										

										$fld_label = $param;

										if(strlen($fld_label)>30)

										{

											$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);

											$fld_label = explode("\n", $fld_label);

											$fld_label = $fld_label[0] . ' ...';	

										}

									

										echo "<a onClick=\"insertAtCursorform(".$choise.",'".$param."'); document.getElementById('mail_from_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";	



									}

									echo "<a onClick=\"insertAtCursorform(".$choise.",'subid'); document.getElementById('mail_from_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";	

									echo "<a onClick=\"insertAtCursorform(".$choise.",'username'); document.getElementById('mail_from_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";								

									echo '</div></div>';								

									?>

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label  for="reply_to" class="hasTip" > <?php echo JText::_( 'Reply to' ); ?>:<br>(if different from "Email From") </label>

									</div>

									<div class="controls">

											<?php 

												$fields =$row->form_fields;

												$fields=explode('*:*id*:*type_submitter_mail*:*type*:*', $fields);

												$n=count($fields);

												$is_other=true;



												for($i=0; $i<$n-1; $i++)

												{

													echo '

													<div style="height: 20px;">

														<input type="radio" name="reply_to" id="reply_to'.$i.'" value="'.(strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]).'"  '.((strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]) == $row->reply_to ? 'checked="checked"' : '' ).' style="margin:0px 5px 0px 0px" onclick="wdhide(\'reply_to_other\')"/>

														<label for="reply_to'.$i.'" style="cursor:pointer">'.substr($fields[$i+1], 0, strpos($fields[$i+1], '*:*w_field_label*:*')).'</label>

													</div>';

													

													if(strlen($fields[$i])!=1)

													{

														if(substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) == $row->reply_to)

															$is_other=false;

													}

													else

													{

														if($fields[$i] == $row->reply_to)

														$is_other=false;

													}	

												}

												



											?>

											<div style="height: 20px; <?php if($n==1) echo 'display:none;' ?>"><input type="radio" id="other1" name="reply_to" value="other" <?php if($is_other) echo 'checked="checked"' ;?> style="margin:0px 5px 0px 0px" onclick="wdshow('reply_to_other')" /><label for="other1" style="cursor:pointer">Other</label></div>

											<input type="text" style="<?php if($n==1) echo 'width:250px'; else  echo 'width:230px' ?>; margin:0px; <?php if($n!=1) echo 'margin-left:20px' ?>; <?php if($is_other) echo 'display:block'; else  echo 'display:none';?>" id="reply_to_other" name="reply_to_other" value="<?php if($is_other)  echo $row->reply_to; ?>" style="width:250px;" />

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'CC' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_cc" name="mail_cc" value="<?php echo $row->mail_cc ?>" style="width:250px;" />

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'BCC' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_bcc" name="mail_bcc" value="<?php echo $row->mail_bcc ?>" style="width:250px;" />

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'Subject' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_subject" name="mail_subject" value="<?php echo $row->mail_subject ?>" style="width:250px;" />

										<img src="components/com_formmaker/images/add.png" onclick="document.getElementById('mail_subject_labels').style.display='block';" style="vertical-align: middle; display:inline-block; margin:0px; float:none;">

									<?php 

									$choise = 'document.getElementById(\'mail_subject\')';

									echo '<div style="position:relative; top:-3px;"><div id="mail_subject_labels" class="email_labels" style="display:none;">';	

												

									for($i=0; $i<count($label_label); $i++)			

									{ 			

										if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy" || $label_type[$i]=="type_matrix")			

										continue;		

										

										$param = htmlspecialchars(addslashes($label_label[$i]));			

										

										$fld_label = $param;

										if(strlen($fld_label)>30)

										{

											$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);

											$fld_label = explode("\n", $fld_label);

											$fld_label = $fld_label[0] . ' ...';	

										}

									

										echo "<a onClick=\"insertAtCursorform(".$choise.",'".$param."'); document.getElementById('mail_subject_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";	



									}

									echo "<a onClick=\"insertAtCursorform(".$choise.",'subid'); document.getElementById('mail_subject_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";	

									echo "<a onClick=\"insertAtCursorform(".$choise.",'username'); document.getElementById('mail_subject_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";

									echo '</div></div>';								

									?>

									</div>

								</div>

								<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Mode' ); ?>: </label>

								</div>

								<div class="controls">

									<fieldset class="radio btn-group btn-group-yesno">

										<input type="radio" id="htmlmode" name="mail_mode" value="1" <?php if($row->mail_mode==1 ) echo "checked='checked'" ?> />

										<label for="htmlmode">HTML</label>

										<input type="radio" id="textmode" name="mail_mode" value="0" <?php if($row->mail_mode==0 ) echo "checked='checked'" ?> />

										<label for="textmode">Text</label>

									</fieldset>	

								</div>

								</div>

								

								<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Attach File' ); ?>: </label>

								</div>

								<div class="controls">

									<fieldset class="radio btn-group btn-group-yesno">

										<input type="radio" id="en_attach" name="mail_attachment" value="1" <?php if($row->mail_attachment==1 ) echo "checked='checked'" ?> />

										<label for="en_attach">Yes</label>

										<input type="radio" id="dis_attach" name="mail_attachment" value="0" <?php if($row->mail_attachment==0 ) echo "checked='checked'" ?> />

										<label for="dis_attach">No</label>

									</fieldset>	

								</div>

								</div>

								

								<div class="control-group span10" style="border-top:1px solid #999; margin:0px;">

									<div style="margin:10px 0px">

										<label > <?php echo JText::_( 'Custom Text in Email For Administrator' ); ?>: </label>

									</div>

									<div>

										<?php 

										$choise = 'document.getElementById(\'script_mail\')';

										for($i=0; $i<count($label_label); $i++)			

										{ 			

											if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha"|| $label_type[$i]=="type_button" || $label_type[$i]=="type_send_copy")			

											continue;		

											

											$param = htmlspecialchars(addslashes($label_label[$i]));

											$fld_label = $param;

											if(strlen($fld_label)>30)

											{

												$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);

												$fld_label = explode("\n", $fld_label);

												$fld_label = $fld_label[0] . ' ...';	

											}	

											

											if($label_type[$i]=="type_file_upload")

												$fld_label .= '(as image)';

												

											echo "<input type=\"button\" class=\"btn\" value='".$fld_label."' onClick=\"insertAtCursorform(".$choise.",'".$param."')\" /> ";	

										}	

											echo '<input type="button" class="btn" value="Submission ID" onClick="insertAtCursorform('.$choise.',\'subid\')" /> ';

											echo '<input type="button" class="btn" value="Ip" onClick="insertAtCursorform('.$choise.',\'ip\')" /> ';

											echo '<input type="button" class="btn" value="Username" onClick="insertAtCursorform('.$choise.',\'username\')" /> ';

											echo '<input type="button" class="btn" value="User Email" onClick="insertAtCursorform('.$choise.',\'useremail\')" /> ';	

											echo '<br/><input style="margin:3px 0; font-weight:bold;" type="button" class="btn" value="All fields list" onClick="insertAtCursorform('.$choise.',\'all\')" /> ';			

										?>

									





							<?php if($is_editor) echo $editor->display('script_mail',$row->script_mail,'50%','350','40','6');

							else

							{

							?>

							<textarea name="script_mail" id="script_mail" cols="40" rows="6" style="width: 450px; height: 350px; " class="mce_editable" aria-hidden="true"><?php echo $row->script_mail ?></textarea>

							<?php



							}

							 ?>		   		   



									</div>	



								</div>

							</fieldset>

						</div>

						<div class="span6" style="">

							<fieldset class="form-horizontal">

								<legend>Email to User</legend>

								<div class="control-group">

									<div class="control-label">

										<label class="hasTip"> <?php echo JText::_( 'Send to' ); ?>: </label>

									</div>

									<div class="controls">

										<?php 

											$fields =$row->form_fields;

											$fields=explode('*:*id*:*type_submitter_mail*:*type*:*', $fields);

											$n=count($fields);

											if($n==1)

												echo 'There is no email field';

											else

											for($i=0; $i<$n-1; $i++)

											{

												echo '

												<div style="height: 20px;">

													<input type="checkbox" name="send_to'.$i.'" id="send_to'.$i.'" value="'.(strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]).'"  '.(is_numeric(strpos($row->send_to, '*'.(strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]).'*')) ? 'checked="checked"' : '' ).' style="margin:0px 5px 0px 0px"/>

													<label for="send_to'.$i.'" style="cursor:pointer">'.substr($fields[$i+1], 0, strpos($fields[$i+1], '*:*w_field_label*:*')).'</label>

												</div>';

											}

										?>

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'Email From' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_from_user" name="mail_from_user" value="<?php echo $row->mail_from_user ?>" style="width:250px;" />

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'From Name' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_from_name_user" name="mail_from_name_user" value="<?php echo $row->mail_from_name_user ?>" style="width:250px;" />

										<img src="components/com_formmaker/images/add.png" onclick="document.getElementById('mail_from_name_user_labels').style.display='block';" style="vertical-align: middle; display:inline-block; margin:0px; float:none;">

										<?php 

										$choise = 'document.getElementById(\'mail_from_name_user\')';

										echo '<div style="position:relative; top:-3px;"><div id="mail_from_name_user_labels" class="email_labels" style="display:none;">';

																			

										for($i=0; $i<count($label_label); $i++)			

										{ 			

											if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy")			

											continue;		

											

											$param = htmlspecialchars(addslashes($label_label[$i]));			

											

											$fld_label = $param;

											if(strlen($fld_label)>30)

											{

												$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);

												$fld_label = explode("\n", $fld_label);

												$fld_label = $fld_label[0] . ' ...';	

											}

										

											echo "<a onClick=\"insertAtCursorform(".$choise.",'".$param."'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";	



										}

										echo "<a onClick=\"insertAtCursorform(".$choise.",'subid'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";	

										echo "<a onClick=\"insertAtCursorform(".$choise.",'username'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";

										echo '</div></div>';								

										?>

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'Reply to' ); ?>:<br>(if different from "Email Form") </label>

									</div>

									<div class="controls">

										<input type="text" id="reply_to_user" name="reply_to_user" value="<?php echo $row->reply_to_user ?>" style="width:250px;" />

									</div>

								</div>

								<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'CC' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_cc_user" name="mail_cc_user" value="<?php echo $row->mail_cc_user ?>" style="width:250px;" />

									</div>

								</div>

								

									<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'BCC' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_bcc_user" name="mail_bcc_user" value="<?php echo $row->mail_bcc_user ?>" style="width:250px;" />

									</div>

								</div>

								

									<div class="control-group">

									<div class="control-label">

										<label> <?php echo JText::_( 'Subject' ); ?>: </label>

									</div>

									<div class="controls">

										<input type="text" id="mail_subject_user" name="mail_subject_user" value="<?php echo $row->mail_subject_user ?>" style="width:250px;" />

										<img src="components/com_formmaker/images/add.png" onclick="document.getElementById('mail_subject_user_labels').style.display='block';" style="vertical-align: middle; display:inline-block; margin:0px; float:none;">

										<?php 

										$choise = 'document.getElementById(\'mail_subject_user\')';

										echo '<div style="position:relative; top:-3px;"><div id="mail_subject_user_labels" class="email_labels" style="display:none;">';	

																			

										for($i=0; $i<count($label_label); $i++)			

										{ 			

											if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy")			

											continue;		

											

											$param = htmlspecialchars(addslashes($label_label[$i]));			

											

											$fld_label = $param;

											if(strlen($fld_label)>30)

											{

												$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);

												$fld_label = explode("\n", $fld_label);

												$fld_label = $fld_label[0] . ' ...';	

											}

											

											echo "<a onClick=\"insertAtCursorform(".$choise.",'".$param."'); document.getElementById('mail_subject_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";	



										}

										echo "<a onClick=\"insertAtCursorform(".$choise.",'subid'); document.getElementById('mail_subject_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";	

										echo "<a onClick=\"insertAtCursorform(".$choise.",'username'); document.getElementById('mail_subject_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";

										echo '</div></div>';								

										?>

									</div>

								</div>

								<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Mode' ); ?>: </label>

								</div>

								<div class="controls">

									<fieldset class="radio btn-group btn-group-yesno">

										<input type="radio" id="htmlmode_user" name="mail_mode_user" value="1" <?php if($row->mail_mode_user==1 ) echo "checked='checked'" ?> />

										<label for="htmlmode_user">HTML</label>

										<input type="radio" id="textmode_user" name="mail_mode_user" value="0" <?php if($row->mail_mode_user==0 ) echo "checked='checked'" ?> />

										<label for="textmode_user">Text</label>

									</fieldset>	

								</div>

								</div>

								

								<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Attach File' ); ?>: </label>

								</div>

								<div class="controls">

									<fieldset class="radio btn-group btn-group-yesno">

										<input type="radio" id="en_attach_user" name="mail_attachment_user" value="1" <?php if($row->mail_attachment_user==1 ) echo "checked='checked'" ?> />

										<label for="en_attach_user">Yes</label>

										<input type="radio" id="dis_attach_user" name="mail_attachment_user" value="0" <?php if($row->mail_attachment_user==0 ) echo "checked='checked'" ?> />

										<label for="dis_attach_user">No</label>

									</fieldset>	

								</div>

								</div>

								

								<div class="control-group span10" style="border-top:1px solid #999; margin:0px;">

									<div style="margin:10px 0px">

										<label > <?php echo JText::_( 'Custom Text in Email For User' ); ?>: </label>

									</div>

									<div>

										<?php 

										$choise = 'document.getElementById(\'script_mail_user\')';	

										for($i=0; $i<count($label_label); $i++)			

										{ 			

											if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha"|| $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy")		

												continue;		

											

											$param = htmlspecialchars(addslashes($label_label[$i]));

											$fld_label = $param;

											if(strlen($fld_label)>30)

											{

												$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);

												$fld_label = explode("\n", $fld_label);

												$fld_label = $fld_label[0] . ' ...';	

											}	

											if($label_type[$i]=="type_file_upload")

												$fld_label .= '(as image)';

												

											echo "<input type=\"button\" class=\"btn\" value='".$fld_label."' onClick=\"insertAtCursorform(".$choise.",'".$param."')\" /> ";	

										}

										echo '<input type="button" class="btn" value="Submission ID" onClick="insertAtCursorform('.$choise.',\'subid\')" /> ';

										echo '<input type="button" class="btn" value="Ip" onClick="insertAtCursorform('.$choise.',\'ip\')" /> ';

										echo '<input type="button" class="btn" value="Username" onClick="insertAtCursorform('.$choise.',\'username\')" /> ';

										echo '<input type="button" class="btn" value="User Email" onClick="insertAtCursorform('.$choise.',\'useremail\')" /> ';										

										echo '<br/><input style="margin:3px 0; font-weight:bold;" type="button" class="btn" value="All fields list" onClick="insertAtCursorform('.$choise.',\'all\')" /> ';					

										

										if($is_editor) echo $editor->display('script_mail_user',$row->script_mail_user,'50%','350','40','6');

										else

										{

										?>

										<textarea name="script_mail_user" id="script_mail_user" cols="40" rows="6" style="width: 450px; height: 350px; " class="mce_editable" aria-hidden="true"><?php echo $row->script_mail_user ?></textarea>

										<?php

										}

										?>		   		   



									</div>

								</div>

							</fieldset>					

						</div>

					</div>

				</div>

				

				<div class="tab-pane <?php if($tabs=="actions_op") { ?> active <?php } ?>" id="actions_op">

					<div class="row-fluid">

						<div class="span12">

							<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Action type' ); ?>: </label>

								</div>

								<div class="controls">

									<select id="submit_text_type" name="submit_text_type" onchange="set_type(this.value)">

										<option value="1"  <?php if($row->submit_text_type==1 ) echo "selected='selected'" ?>>Stay on Form</option>

										<option value="2"  <?php if($row->submit_text_type==2 ) echo "selected='selected'" ?>>Article</option>

										<option value="3"  <?php if($row->submit_text_type==3 ) echo "selected='selected'" ?>>Custom Text</option>

										<option value="4"  <?php if($row->submit_text_type==4 ) echo "selected='selected'" ?>>URL</option>

									</select>

								</div>

							</div>

							<div class="control-group"  id="none" <?php if($row->submit_text_type!=1) echo 'style="display:none"' ?> >

								<div class="control-label">

									<label> <?php echo JText::_( 'Stay on Form' ); ?>: </label>

								</div>

								<div class="controls">

									<i class="icon-ok"></i>

								</div>

							</div>

							<div class="control-group"  id="article" <?php if($row->submit_text_type!=2) echo 'style="display:none"' ?>   >

								<div class="control-label">

									<label> <?php echo JText::_( 'Article' ); ?>: </label>

								</div>

								<div class="controls">

									<?php 



									$name="id";

									$value=$row->article_id;

									$control_name="urlparams";



									$db		= JFactory::getDBO();

									$doc 		= JFactory::getDocument();

									$fieldName	= $control_name.'['.$name.']';

									$article = JTable::getInstance('content');

									if ($value) {

										$article->load($value);

									} else {

										$article->title = JText::_('Select an Article');

									}



									$js = "	function jSelectArticle_".$name."(id, title, object) {

										document.getElementById('article_id').value = id;

										document.getElementById('".$name."_name').value = title;

										SqueezeBox.close();

									}";

									$doc->addScriptDeclaration($js);



									$link = 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$name;



									JHTML::_('behavior.modal', 'a.modal');

									$html = "\n".'<div><a class="modal" title="'.JText::_('Select an Article').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 750, y: 500}}"><input style="background:none; border:none; font-size:11px" type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'"  readonly="readonly" /></a></div>';

									$html .= "\n".'<input type="hidden" id="article_id" name="article_id" value="'.(int)$value.'" />';



									echo $html;



									?>

									<span onclick="remove_article()" style="color:#000000; cursor:pointer; padding-left:5px;"><i>Remove article</i></span>			

								</div>

							</div>

							<div class="control-group" <?php if($row->submit_text_type!=3 ) echo 'style="display:none"' ?>  id="custom1">

								<div class="control-label">

									<label for="submissioni text"> <?php echo JText::_( 'Text' ); ?>: </label>

								</div>

								<div class="controls">

									<?php 

									if($is_editor) 

										echo $editor->display('submit_text',$row->submit_text,'50%','350','40','6');

									else

									{

										?>

										<textarea name="submit_text" id="submit_text" cols="40" rows="6" style="width: 450px; height: 350px; " class="mce_editable" aria-hidden="true"><?php echo $row->submit_text ?></textarea>

										<?php

									}

									?>		   		   

								</div>

							</div>

							<div class="control-group" <?php if($row->submit_text_type!=4 ) echo 'style="display:none"' ?>  id="url">

								<div class="control-label">

									<label for="submissioni text"> <?php echo JText::_( 'URL' ); ?>: </label>

								</div>

								<div class="controls">

									<input type="text" id="url" name="url" value="<?php echo $row->url ?>" />

								</div>

							</div>

						</div>

					</div>

				</div>



				<div class="tab-pane <?php if($tabs=="payment_op") { ?> active <?php } ?>" id="payment_op">

					<div class="row-fluid">

						<div class="span12">

							<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Turn PayPal on' ); ?>: </label>

								</div>

								<div class="controls">

									<fieldset class="radio btn-group btn-group-yesno">

										<input type="radio" name="paypal_mode" id="paypal_mode1" value="1" <?php if($row->paypal_mode=="1" ) echo "checked='checked'" ?> /> <label for="paypal_mode1">On </label>

										<input type="radio" name="paypal_mode" id="paypal_mode2" value="0" <?php if($row->paypal_mode!="1" ) echo "checked='checked'" ?> /> <label for="paypal_mode2">Off </label>

									</fieldset>	

								</div>

							</div>

							<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Checkout Mode' ); ?>: </label>

								</div>

								<div class="controls">

									<select id="checkout_mode" name="checkout_mode">

										<option value="production" <?php if($row->checkout_mode=="production" ) echo "selected='selected'" ?>>Production</option>

										<option value="testmode"  <?php if($row->checkout_mode!="production" ) echo "selected='selected'" ?>>Test Mode</option>

									</select>

								</div>

							</div>

							<div class="control-group">

								<div class="control-label">

									<label > <?php echo JText::_( 'PayPal Email' ); ?>: </label>

								</div>

								<div class="controls">

									<input type="text" name="paypal_email" id="paypal_email" value="<?php echo $row->paypal_email; ?>" class="text_area" >			

								</div>

							</div>

							<div class="control-group">

								<div class="control-label">

									<label> <?php echo JText::_( 'Payment Currency' ); ?>: </label>

								</div>

								<div class="controls">

								<select id="payment_currency" name="payment_currency">

									<option value="USD">$ · U.S. Dollar</option>

									<option value="EUR">€ · Euro</option>

									<option value="GBP">£ · Pound Sterling</option>

									<option value="JPY">¥ · Japanese Yen</option>

									<option value="CAD">C$ · Canadian Dollar</option>

									<option value="MXN">Mex$ · Mexican Peso</option>

									<option value="HKD">HK$ · Hong Kong Dollar</option>

									<option value="HUF">Ft · Hungarian Forint</option>

									<option value="NOK">kr · Norwegian Kroner</option>

									<option value="NZD">NZ$ · New Zealand Dollar</option>

									<option value="SGD">S$ · Singapore Dollar</option>

									<option value="SEK">kr · Swedish Kronor</option>

									<option value="PLN">zl · Polish Zloty</option>

									<option value="AUD">A$ · Australian Dollar</option>

									<option value="DKK">kr · Danish Kroner</option>

									<option value="CHF">CHF · Swiss Francs</option>

									<option value="CZK">Kc · Czech Koruny</option>

									<option value="ILS">₪ · Israeli Sheqel</option>

									<option value="BRL">R$ · Brazilian Real</option>

									<option value="TWD">NT$ · Taiwan New Dollars</option>

									<option value="MYR">RM  · Malaysian Ringgit</option>

									<option value="PHP">₱ · Philippine Peso</option>

									<option value="THB">฿ · Thai Bahtv</option>

									</select>

								</div>

							</div>

							<div class="control-group">

								<div class="control-label">

									<label for="tax" > <?php echo JText::_( 'Tax' ); ?>: </label>

								</div>

								<div class="controls">

									<input type="text" name="tax" id="tax" value="<?php echo $row->tax; ?>" class="text_area" style="width:30px" onKeyPress="return check_isnum_point(event)">	%		

								</div>

							</div>					   

						</div>

					</div>

				</div>



				<div class="tab-pane <?php if($tabs=="javascript_op") { ?> active <?php } ?>" id="javascript_op">

					<div class="row-fluid">

						<div class="span12">

							<div class="control-group">

								<div class="control-label">

									<label for="javascript"> <?php echo JText::_( 'JavaScript' ); ?> </label>

								</div>

								<div class="controls">

									<textarea style="margin: 0px; width:600px; height:500px"  name="javascript" id="javascript" ><?php echo $row->javascript; ?></textarea>

								</div>

							</div>

						</div>

					</div>

				</div>

		

				<div class="tab-pane <?php if($tabs=="conditional_op") { ?> active <?php } ?>" id="conditional_op">

					<div class="row-fluid">

					<div class="span12">

					<div class="control-group">

					<div>	

						<span style="font-size:13px;">Add Condition<span/>

						<img src="components/com_formmaker/images/add_condition.png" title="add" onclick="add_condition()" style="cursor: pointer; vertical-align: middle; margin-left:15px;">

					</div>

					<div id="conditions">	
					<span></span>
							

					</div>

					<script>

					jQuery("#conditions").load('index.php?option=com_formmaker&task=show_conditions&form_id=<?php echo $row->id; ?>&format=row');

					</script>

					</div>

				</div>

			</div>



			</div>

				

				<div class="tab-pane <?php if($tabs=="mapping_op") { ?> active <?php } ?>" id="mapping_op">

					<div class="row-fluid">

						<div class="span12">

							<div class="control-group">

						<a href="index.php?option=com_formmaker&task=add_query&id=<?php echo $row->id ?>&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x:900, y: 520}}" style="text-decoration:none; color:#000">

							<div class="btn">

								<span class="icon-new" title="Add Query"></span>

								Add Query

							</div>

						</a>

						<div onclick="remove_query()" class="btn">

							<span class="icon-delete" title="Delete"></span>

							Delete

						</div>

						<?php 

						if ($queries)

						{

						?>

						<table class="table table-striped" width="100%">

							<thead>

								<tr>

									<th width="4%">#</th>

									<th width="4%">ID</th>

									<th width="6%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"></th>

									<th width="86%">Query</th>

								</tr>

							</thead>

							<?php

							$k = 0;



							for($i=0, $n=count($queries); $i < $n ; $i++)

							{

								$query = &$queries[$i];

								$checked 	= JHTML::_('grid.id', $i, $query->id);



								// prepare link for id column



								$link 		= JRoute::_( 'index.php?option=com_formmaker&task=edit_query&id='.$row->id.'&tmpl=component&query_id='. $query->id );



								?>



								<tr class="<?php echo "query$k"; ?>">

									<td align="center"><?php echo $i+1?></td>

									<td align="center"><?php echo $query->id?></td>

									<td align="center"><?php echo $checked?></td>

									<td align="center"><a href="<?php echo $link; ?>" class="modal" rel="{handler: 'iframe', size: {x:900, y: 520}}"><?php echo $query->query?></a></td>

								</tr>

								<?php

								$k = 1 - $k;

								}

								?>

						</table>

						<?php

						}

						?>

							</div>

						</div>

					</div>

				</div>

				

			</div>

		</div>

	</div>



    <input type="hidden" name="option" value="com_formmaker" />

    <input type="hidden" name="id" value="<?php echo $row->id?>" />

    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />

    <input type="hidden" name="task" value="" />

    <input type="hidden" name="tabs" id="tabs" value="<?php echo $tabs; ?>" />

</form>

	



<div style="display:none" id="pages" show_title="<?php echo $row->show_title; ?>" show_numbers="<?php echo $row->show_numbers; ?>" type="<?php echo $row->pagination; ?>"></div>	

<div id="take" style="display:none">

<?php echo $row->form?>

</div>	

	

	<script language="javascript" type="text/javascript">

	function hide_email_labels(event)

{



    var e = event.toElement || event.relatedTarget;

        if (e.parentNode == this || e == this) {

           return;

        }

		

this.style.display="none";

}

if(document.getElementById('mail_from_labels'))

document.getElementById('mail_from_labels').addEventListener('mouseout',hide_email_labels,true);

if(document.getElementById('mail_subject_labels'))

document.getElementById('mail_subject_labels').addEventListener('mouseout',hide_email_labels,true);

if(document.getElementById('mail_from_name_user_labels'))

document.getElementById('mail_from_name_user_labels').addEventListener('mouseout',hide_email_labels,true);

if(document.getElementById('mail_subject_user_labels'))

document.getElementById('mail_subject_user_labels').addEventListener('mouseout',hide_email_labels,true);



	

	

		document.getElementById('payment_currency').value="<?php echo $row->payment_currency; ?>";

		set_preview();

	</script>



<?php		



       }

	   

public static function form_options_old(&$row, &$themes){



		JRequest::setVar( 'hidemainmenu', 1 );

		

		$is_editor=false;

		

		$plugin = JPluginHelper::getPlugin('editors', 'tinymce');

		if (isset($plugin->type))

		{ 

			$editor	= JFactory::getEditor('tinymce');

			$is_editor=true;

		}

		

		$editor	= JFactory::getEditor('tinymce');



		$value="";



		$article = JTable::getInstance('content');

		if ($value) {

			$article->load($value);

		} else {

			$article->title = JText::_('Select an Article');

		}

		

			$label_id= array();		

			$label_label= array();		

			$label_type= array();			

			$label_all	= explode('#****#',$row->label_order_current);		

			$label_all 	= array_slice($label_all,0, count($label_all)-1);   	

			

		foreach($label_all as $key => $label_each) 		

		{			

			$label_id_each=explode('#**id**#',$label_each);			

			array_push($label_id, $label_id_each[0]);					

		

		$label_order_each=explode('#**label**#', $label_id_each[1]);				

			array_push($label_label, $label_order_each[0]);		

			array_push($label_type, $label_order_each[1]);		

		}			

		

		?>



<script language="javascript" type="text/javascript">

Joomla.submitbutton= function (pressbutton)

{

	var form = document.adminForm;

	if (pressbutton == 'cancel') 

	{

		submitform( pressbutton );

		return;

	}

	

	if(form.mail.value!='')

	{

		subMailArr=form.mail.value.split(',');

		emailListValid=true;

		for(subMailIt=0; subMailIt<subMailArr.length; subMailIt++)

		{

		trimmedMail = subMailArr[subMailIt].replace(/^\s+|\s+$/g, '') ;

		if (trimmedMail.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1)

		{

					alert( "This is not a list of valid Email addresses." );	

					emailListValid=false;

					break;

		}

		}

		if(!emailListValid)	

		return;

	}	



	submitform( pressbutton );

}



function check_isnum(e)

{

	

   	var chCode1 = e.which || e.keyCode;

    	if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57))

        return false;

	return true;

}



function jSelectArticle(id, title, object) {

			document.getElementById('article_id').value = id;

			document.getElementById(object + '_name').value = title;

			document.getElementById('sbox-window').close();

			}

			

function remove_article()

{

	document.getElementById('id_name').value="Select an Article";

	document.getElementById('article_id').value="";

}



function set_type(type)

{

	switch(type)

	{

		case 'article':

			document.getElementById('article').removeAttribute('style');

			document.getElementById('custom1').setAttribute('style','display:none');

			document.getElementById('url').setAttribute('style','display:none');

			document.getElementById('none').setAttribute('style','display:none');

			break;

			

		case 'custom':

			document.getElementById('article').setAttribute('style','display:none');

			document.getElementById('custom1').removeAttribute('style');

			document.getElementById('url').setAttribute('style','display:none');

			document.getElementById('none').setAttribute('style','display:none');

			break;

			

		case 'url':

			document.getElementById('article').setAttribute('style','display:none');

			document.getElementById('custom1').setAttribute('style','display:none');

			document.getElementById('url').removeAttribute('style');

			document.getElementById('none').setAttribute('style','display:none');

			break;

			

		case 'none':

			document.getElementById('article').setAttribute('style','display:none');

			document.getElementById('custom1').setAttribute('style','display:none');

			document.getElementById('url').setAttribute('style','display:none');

			document.getElementById('none').removeAttribute('style');

			break;

	}

}



function insertAtCursorform(myField, myValue) {  

	if(myField.style.display=="none")

	{



		tinyMCE.execCommand('mceInsertContent',false,"%"+myValue+"%");

		return;

	}



	

	   if (document.selection) {      

	   myField.focus();      

	   sel = document.selection.createRange();    

	   sel.text = myValue;    

	   }    

   else

		if (myField.selectionStart || myField.selectionStart == '0') {     

		   var startPos = myField.selectionStart;       

		   var endPos = myField.selectionEnd;      

		   myField.value = myField.value.substring(0, startPos)           

		   +  "%"+myValue+"%"        

		   + myField.value.substring(endPos, myField.value.length);   

		} 

   else {     

   myField.value += "%"+myValue+"%";    

   }





   }



   

   

function set_preview()

{

	appWidth			=parseInt(document.body.offsetWidth);

	appHeight			=parseInt(document.body.offsetHeight);



document.getElementById('modalbutton').href='index.php?option=com_formmaker&task=preview&format=raw&theme='+document.getElementById('theme').value;

document.getElementById('modalbutton').setAttribute("rel","{handler: 'iframe', size: {x:"+(appWidth-100)+", y: "+(appHeight-100)+"}}");



}



gen="<?php echo $row->counter; ?>";

form_view_max=20;

</script>

<style>

.borderer

{

border-radius:5px;

padding-left:5px;

background-color:#F0F0F0;

height:19px;

width:153px;

}



fieldset.adminform1 {

border-radius: 7px;

border: 1px solid #CCC;

padding: 13px;

margin-top: 20px;

}



label

{

display:inline;

}





</style>



<form action="index.php" method="post" name="adminForm" id="adminForm">



	<div class="row-fluid">

		<div class="span10 form-horizontal">

		

		

		

			<fieldset>

				<ul class="nav nav-tabs">

					<li class="active"><a href="#general_op" data-toggle="tab">General Options</a></li>

					<li><a href="#actions_op" data-toggle="tab">Actions after Submission</a></li>

					<li><a href="#payment_op" data-toggle="tab">Payment Options</a></li>

					<li><a href="#javascript_op" data-toggle="tab">JavaScript</a></li>

					<li><a href="#email_op" data-toggle="tab">Custom Text in Email</a></li>

				</ul>

			</fieldset>

			

			

			

			

			<div class="tab-content">

				<div class="tab-pane active" id="general_op">

						<table class="admintable"  style="float:left">

							<tr valign="top">

								<td class="key">

									<label> <?php echo JText::_( 'Email to Send Submissions to' ); ?>: </label>

								</td>

								<td>

									<input type="text" id="mail" name="mail" value="<?php echo $row->mail ?>" style="width:235px;" />

								</td>

							</tr>

							<tr valign="top">

						<td class="key">

							<label> <?php echo JText::_( 'Email From' ); ?>: </label>

						</td>

						<td>

							<input type="text" id="mail_from" name="mail_from" value="<?php echo $row->mail_from ?>" style="width:235px;" />

						</td>

					</tr>

					<tr valign="top">

						<td class="key">

							<label> <?php echo JText::_( 'From Name' ); ?>: </label>

						</td>

						<td>

							<input type="text" id="mail_from_name" name="mail_from_name" value="<?php echo $row->mail_from_name ?>" style="width:235px;" />

						</td>

					</tr>

							<tr valign="top">

								<td class="key">

									<label> <?php echo JText::_( 'Theme' ); ?>: </label>

								</td>

								<td>

									<select id="theme" name="theme" style="width:250px; " onChange="set_preview()" >

									<?php 

									foreach($themes as $theme) 

									{

										if($theme->id==$row->theme)

										{

											echo '<option value="'.$theme->id.'" selected>'.$theme->title.'</option>';

										}

										else

											echo '<option value="'.$theme->id.'">'.$theme->title.'</option>';

									}

									?>

									</select> 

								

								</td>

							</tr>

						</table>

						<style>

						div.wd_preview span{ float: none; width: 32px; height: 32px; margin: 0 auto; display: block; }

						div.wd_preview a {display: block; float: left;	white-space: nowrap;border: 1px solid #fbfbfb;	padding: 1px 5px;cursor: pointer; text-decoration:none; margin-top:62px;}



						</style>

						

						

						

						

					<div class="button wd_preview" id="toolbar-popup-popup">

					<a class="modal" id="modalbutton" href="index.php?option=com_formmaker&amp;task=preview&amp;tmpl=component&amp;theme=<?php echo $row->theme ?>" rel="{handler: 'iframe', size: {x:1000, y: 520}}">

					<span class="icon-32-preview" title="Preview" >

					</span>

					Preview

					</a>

					</div>



				</div>

				

				<div class="tab-pane" id="actions_op">

					<table class="admintable">



						<tr valign="top">

							<td class="key">

								<label for="submissioni text"> <?php echo JText::_( 'Action type' ); ?>: </label>

							</td>

							<td>

							<input type="radio" name="submit_text_type" onclick="set_type('none')"		value="1" <?php if($row->submit_text_type!=2 and $row->submit_text_type!=3 ) echo "checked" ?> /> Stay on Form<br/>

							<input type="radio" name="submit_text_type" onclick="set_type('article')"  	value="2" <?php if($row->submit_text_type==2 ) echo "checked" ?> /> Article<br/>

							<input type="radio" name="submit_text_type" onclick="set_type('custom')" 	value="3" <?php if($row->submit_text_type==3 ) echo "checked" ?> /> Custom Text<br/>

							<input type="radio" name="submit_text_type" onclick="set_type('url')" 		value="4" <?php if($row->submit_text_type==4 ) echo "checked" ?> /> URL

							</td>

						</tr>

						<tr  id="none" <?php if($row->submit_text_type==2 or $row->submit_text_type==3 or $row->submit_text_type==4 ) echo 'style="display:none"' ?> >

							<td class="key">

								<label for="submissioni text"> <?php echo JText::_( 'Stay on Form' ); ?>: </label>

							</td>

							<td >

								<i class="icon-checkin "></i>

							</td>

					   </tr>

						<tr id="article" <?php if($row->submit_text_type!=2) echo 'style="display:none"' ?>   >

							<td class="key">

								<label for="submissioni text"> <?php echo JText::_( 'Article' ); ?>: </label>

							</td>

			<td >

		<?php 



$name="id";

$value=$row->article_id;

$control_name="urlparams";



		$db		= JFactory::getDBO();

		$doc 		= JFactory::getDocument();

		$fieldName	= $control_name.'['.$name.']';

		$article = JTable::getInstance('content');

		if ($value) {

			$article->load($value);

		} else {

			$article->title = JText::_('Select an Article');

		}



		$js = "	function jSelectArticle_".$name."(id, title, object) {

			document.getElementById('article_id').value = id;

			document.getElementById('".$name."_name').value = title;

			SqueezeBox.close();

		}";

		$doc->addScriptDeclaration($js);



		$link = 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$name;



		JHTML::_('behavior.modal', 'a.modal');

		$html = "\n".'<div style="background-color:white; height:19px"><a class="modal" title="'.JText::_('Select an Article').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 750, y: 500}}"><input style="background:none; width:151px; height:17px; border:none; font-size:11px" type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'"  readonly="readonly" /></a></div>';

		$html .= "\n".'</div><input type="hidden" id="article_id" name="article_id" value="'.(int)$value.'" />';



		echo $html;



?>

			<span onclick="remove_article()" style="color:#000000; cursor:pointer; padding-left:5px;"><i>Remove article</i></span>			

			</td>

						</tr>

						<tr  <?php if($row->submit_text_type!=3 ) echo 'style="display:none"' ?>  id="custom1">

						   <td class="key">

								<label for="submissioni text"> <?php echo JText::_( 'Text' ); ?>: </label>

						   </td>

						   <td >

						   

				<?php if($is_editor) echo $editor->display('submit_text',$row->submit_text,'50%','350','40','6');

				else

				{

				?>

				<textarea name="submit_text" id="submit_text" cols="40" rows="6" style="width: 450px; height: 350px; " class="mce_editable" aria-hidden="true"><?php echo $row->submit_text ?></textarea>

				<?php



				}

				 ?>		   		   

							</td>

						</tr>

						<tr  <?php if($row->submit_text_type!=4 ) echo 'style="display:none"' ?>  id="url">

						   <td class="key">

								<label for="submissioni text"> <?php echo JText::_( 'URL' ); ?>: </label>

						   </td>

						   <td >

							   <input type="text" id="url" name="url" style="width:300px" value="<?php echo $row->url ?>" />

							</td>

						</tr>

				   



					</table>

				</div>



				<div class="tab-pane" id="payment_op">

					<table class="admintable">

						<tr valign="top">

							<td class="key">

								<label> <?php echo JText::_( 'Turn PayPal on' ); ?>: </label>

							</td>

							<td>

							<input type="radio" name="paypal_mode" id="paypal_mode1" value="1" <?php if($row->paypal_mode=="1" ) echo "checked" ?> /> <label for="paypal_mode1">On </label><br/>

							<input type="radio" name="paypal_mode" id="paypal_mode2" value="0" <?php if($row->paypal_mode!="1" ) echo "checked" ?> /> <label for="paypal_mode2">Off </label><br/>

							</td>

						</tr>

						<tr valign="top">

							<td class="key">

								<label> <?php echo JText::_( 'Checkout Mode' ); ?>: </label>

							</td>

							<td>

							<input type="radio" name="checkout_mode" id="checkout_mode1" value="production" <?php if($row->checkout_mode=="production" ) echo "checked" ?> /> <label for="checkout_mode1">Production </label><br/>

							<input type="radio" name="checkout_mode" id="checkout_mode2" value="testmode" <?php if($row->checkout_mode!="production" ) echo "checked" ?> /> <label for="checkout_mode2">Test Mode</label><br/>

							</td>

						</tr>

						<tr valign="top">

							<td class="key">

								<label > <?php echo JText::_( 'PayPal Email' ); ?>: </label>

							</td>

							<td >

								<input type="text" name="paypal_email" id="paypal_email" value="<?php echo $row->paypal_email; ?>" class="text_area"  style="width:250px">			

							</td>

					   </tr>

						<tr valign="top">

							<td class="key">

								<label> <?php echo JText::_( 'Payment Currency' ); ?>: </label>

							</td>

							<td >

								<select id="payment_currency" name="payment_currency" style="width:253px" >

									<option value="USD">$ ?U.S. Dollar</option>

									<option value="EUR"> ?Euro</option>

									<option value="GBP">?ound Sterling</option>

									<option value="JPY">?Japanese Yen</option>

									<option value="CAD">C$ ?Canadian Dollar</option>

									<option value="MXN">Mex$ ?Mexican Peso</option>

									<option value="HKD">HK$ ?Hong Kong Dollar</option>

									<option value="HUF">Ft ?Hungarian Forint</option>

									<option value="NOK">kr ?Norwegian Kroner</option>

									<option value="NZD">NZ$ ?New Zealand Dollar</option>

									<option value="SGD">S$ ?Singapore Dollar</option>

									<option value="SEK">kr ?Swedish Kronor</option>

									<option value="PLN">zl ?Polish Zloty</option>

									<option value="AUD">A$ ?Australian Dollar</option>

									<option value="DKK">kr ?Danish Kroner</option>

									<option value="CHF">CHF ?Swiss Francs</option>

									<option value="CZK">Kc ?Czech Koruny</option>

									<option value="ILS">? ?Israeli Sheqel</option>

									<option value="BRL">R$ ?Brazilian Real</option>

									<option value="TWD">NT$ ?Taiwan New Dollars</option>

									<option value="MYR">RM  ?Malaysian Ringgit</option>

									<option value="PHP">? ?Philippine Peso</option>

									<option value="THB">? ?Thai Bahtv</option>



								</select>

							</td>

						</tr>

						<tr valign="top">

							<td class="key">

								<label for="tax" > <?php echo JText::_( 'Tax' ); ?>: </label>

							</td>

							<td >

								<input type="text" name="tax" id="tax" value="<?php echo $row->tax; ?>" class="text_area" style="width:30px" onKeyPress="return check_isnum(event)">	%		

							</td>

					   </tr>

					    

					</table>

				</div>



				<div class="tab-pane" id="javascript_op">

					<table class="admintable">



						<tr valign="top">



							<td  class="key">



								<label for="javascript"> <?php echo JText::_( 'JavaScript' ); ?> </label>



							</td>

							<td >

								<textarea style="margin: 0px; width:600px; height:500px"  name="javascript" id="javascript" ><?php echo $row->javascript; ?></textarea>

							</td>

						</tr>

					</table>

				</div>



				<div class="tab-pane" id="email_op">

					<table class="admintable">

					

						<tr>

							<td class="key" valign="top">

								<label > <?php echo JText::_( 'For Administrator' ); ?>: </label>

							</td>



							<td>

							<div style="margin-bottom:5px">

							<?php 

							$choise = 'document.getElementById(\'script_mail\')';		

							for($i=0; $i<count($label_label); $i++)			

							{ 			

								if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha"|| $label_type[$i]=="type_button" )			

								continue;		

								

								$param = "'".htmlspecialchars(addslashes($label_label[$i]))."'";					

															

								echo '<input type="button" value="'.htmlspecialchars(addslashes($label_label[$i])).'" onClick="insertAtCursorform('.$choise.','.$param.')" /> ';	

							}	



							

								echo '<input type="button" value="\'Ip\'" onClick="insertAtCursorform('.$choise.',\'ip\')" /> ';	

								echo '<input type="button" value="\'Username\'" onClick="insertAtCursorform('.$choise.',\'username\')" /> ';

								echo '<input type="button" value="\'Useremail\'" onClick="insertAtCursorform('.$choise.',\'useremail\')" /> ';		

								echo '<br/><input style="margin:3px 0; font-weight:bold;" type="button" class="btn" value="All fields list" onClick="insertAtCursorform('.$choise.',\'all\')" /> ';			

							?>



							  

						</div>





				<?php if($is_editor) echo $editor->display('script_mail',$row->script_mail,'50%','350','40','6');

				else

				{

				?>

				<textarea name="script_mail" id="script_mail" cols="40" rows="6" style="width: 450px; height: 350px; " class="mce_editable" aria-hidden="true"><?php echo $row->script_mail ?></textarea>

				<?php



				}

				 ?>		   		   



							</td>



						</tr>

						<tr>

							<td  valign="top" height="30">

							</td>

							<td  valign="top">

							</td>

						</tr>



						<tr>

							<td class="key" valign="top">

								<label > <?php echo JText::_( 'For User' ); ?>: </label>

							</td>



							<td>

							<div style="margin-bottom:5px">

							<?php

							$choise = 'document.getElementById(\'script_mail\')';									

							for($i=0; $i<count($label_label); $i++)			

							{ 			

							if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha"|| $label_type[$i]=="type_button" )			

							continue;		

								

								$param = "'".htmlspecialchars(addslashes($label_label[$i]))."'";					

							

							

							echo '<input type="button" value="'.htmlspecialchars(addslashes($label_label[$i])).'" onClick="insertAtCursorform('.$choise.','.$param.')" /> ';	

							

							}	

							

								echo '<input type="button" value="\'Ip\'" onClick="insertAtCursorform('.$choise.',\'ip\')" /> ';	

								echo '<input type="button" value="\'Username\'" onClick="insertAtCursorform('.$choise.',\'username\')" /> ';

								echo '<input type="button" value="\'Useremail\'" onClick="insertAtCursorform('.$choise.',\'useremail\')" /> ';

								echo '<br/><input style="margin:3px 0; font-weight:bold;" type="button"  class="btn" value="All fields list" onClick="insertAtCursorform('.$choise.',\'all\')" /> ';					

							?>



							  

						</div>



				<?php if($is_editor) echo $editor->display('script_mail_user',$row->script_mail_user,'50%','350','40','6');

				else

				{

				?>

				<textarea name="script_mail_user" id="script_mail_user" cols="40" rows="6" style="width: 450px; height: 350px; " class="mce_editable" aria-hidden="true"><?php echo $row->script_mail_user ?></textarea>

				<?php



				}

				 ?>		   		   



							</td>



						</tr>



					

					

					</table>



				</div>



			</div>

		</div>

	</div>



    <input type="hidden" name="option" value="com_formmaker" />

    <input type="hidden" name="id" value="<?php echo $row->id?>" />

    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />

    <input type="hidden" name="task" value="" />

</form>

	



<div style="display:none" id="pages" show_title="<?php echo $row->show_title; ?>" show_numbers="<?php echo $row->show_numbers; ?>" type="<?php echo $row->pagination; ?>"></div>	

<div id="take" style="display:none">

<?php echo $row->form?>

</div>	

	

	<script language="javascript" type="text/javascript">

		document.getElementById('payment_currency').value="<?php echo $row->payment_currency; ?>";

		set_preview();

	</script>



<?php		



       }	   

	   



public static function paypal_info($row){

if(!isset($row->ipn))

{

echo "<div style='width:100%; text-align:center; height: 100%; vertical-align:middle'><h1 style='top: 44%;position: absolute;left:38%; color:#000'>No information yet<p></h1>";

return;

}

?>

<h2>Payment Info</h2>

<table class="admintable">

	<tr>

		<td class="key">Currency</td>

		<td><?php echo $row->currency; ?></td>

	</tr>

	<tr>

		<td class="key">Last modified</td>

		<td><?php echo $row->ord_last_modified; ?></td>

	</tr>

	<tr>

		<td class="key">Status</td>

		<td><?php echo $row->status; ?></td>

	</tr>

	<tr>

		<td class="key">Full name</td>

		<td><?php echo $row->full_name; ?></td>

	</tr>

	<tr>

		<td class="key">Email</td>

		<td><?php echo $row->email; ?></td>

	</tr>

	<tr>

		<td class="key">Phone</td>

		<td><?php echo $row->phone; ?></td>

	</tr>

	<tr>

		<td class="key">Mobile phone</td>

		<td><?php echo $row->mobile_phone; ?></td>

	</tr>

	<tr>

		<td class="key">Fax</td>

		<td><?php echo $row->fax; ?></td>

	</tr>

	<tr>

		<td class="key">Address</td>

		<td><?php echo $row->address; ?></td>

	</tr>

	<tr>

		<td class="key">PayPal info</td>

		<td><?php echo $row->paypal_info; ?></td>

	</tr>	

	<tr>

		<td class="key">IPN</td>

		<td><?php echo $row->ipn; ?></td>

	</tr>

	<tr>

		<td class="key">Tax</td>

		<td><?php echo $row->tax; ?>%</td>

	</tr>

	<tr>

		<td class="key">Shipping</td>

		<td><?php echo $row->shipping; ?></td>

	</tr>

	<tr>

		<td class="key">Total</td>

		<td><b><?php echo $row->total; ?></b></td>

	</tr>

</table>





<?php



}





public static function show_map($long,$lat){



		$document = JFactory::getDocument();

 		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';



		$document->addScript($cmpnt_js_path.'/if_gmap.js');

	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
			$document->addScript('https://maps.google.com/maps/api/js?sensor=false');
		else	
			$document->addScript('http://maps.google.com/maps/api/js?sensor=false');
?>

<table style="margin:0px; padding:0px">

<tr><td><b>Address:</b></td><td><input type="text" id="addrval0" style="border:0px; background:none" size="100" readonly /> </td></tr>

<tr><td><b>Longitude:</b></td> <td><input type="text" id="longval0" style="border:0px; background:none" size="100" readonly /> </td></tr>

<tr><td><b>Latitude:</b></td><td><input type="text" id="latval0" style="border:0px; background:none" size="100" readonly /> </td></tr>

</table>

		

<div id="0_elementform_id_temp" long="<?php echo $long ?>" center_x="<?php echo $long ?>" center_y="<?php echo $lat ?>" lat="<?php echo $lat ?>" zoom="8" info="" style="width:600px; height:500px; "></div>



<script>

		if_gmap_init("0");

		add_marker_on_map(0, 0, "<?php echo $long ?>", "<?php echo $lat ?>", '');





</script>



<?php		



}







public static function show_matrix($matrix_params){





$new_filename= str_replace("***matrix***",'', $matrix_params);	



                $new_filename=explode('***', $matrix_params);

				$mat_params=array_slice($new_filename,0, count($new_filename)-1);

        $mat_rows=$mat_params[0];

		$mat_columns=$mat_params[$mat_rows+1];

							

				$matrix="<table >";

							

							

				

							$matrix .='<tr><td></td>';

						

							for( $k=1;$k<=$mat_columns;$k++)

							$matrix .='<td style="background-color:#BBBBBB; padding:5px;">'.$mat_params[$mat_rows+1+$k].'</td>';

							$matrix .='</tr>';

							

							$aaa=Array();

							   $var_checkbox=1;

							for( $k=1;$k<=$mat_rows;$k++){

							$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px; ">'.$mat_params[$k].'</td>';

							  if($mat_params[$mat_rows+$mat_columns+2]=="radio"){

							  

							  if($mat_params[$mat_rows+$mat_columns+2+$k]==0){

								    $checked=0;

									$aaa[1]="";

									}

								    else{

									$aaa=explode("_",$mat_params[$mat_rows+$mat_columns+2+$k]);

									

								    

							        }

									

							        for( $l=1;$l<=$mat_columns;$l++){

										if($aaa[1]==$l)

									    $checked="checked";

									    else

									    $checked="";

							        $matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';

                                    

							        }

									

							    } 

								else{

								if($mat_params[$mat_rows+$mat_columns+2]=="checkbox")

								{

								                          

							        for( $l=1;$l<=$mat_columns;$l++){

									if($mat_params[$mat_rows+$mat_columns+2+$var_checkbox]=="1")

									$checked="checked";

									    else

									    $checked="";

										

							        $matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';

								 $var_checkbox++;

								}

								

								}

								else

								{

								if($mat_params[$mat_rows+$mat_columns+2]=="text")

								{

								                          

							        for( $l=1;$l<=$mat_columns;$l++){

									 $checked = $mat_params[$mat_rows+$mat_columns+2+$var_checkbox];

										

							        $matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';

								 $var_checkbox++;

								}

								

								}

								else{

								 for( $l=1;$l<=$mat_columns;$l++){

									 $checked = $mat_params[$mat_rows+$mat_columns+2+$var_checkbox];

										

							        $matrix .='<td style="text-align:center">'.$checked.'</td>';

								 $var_checkbox++;

								

								}

								}

								

								}

								

								}

							    $matrix .='</tr>';

							}

							 $matrix .='</table>';

		echo $matrix;



}







public static function country_list($id){



		$document		= JFactory::getDocument();

		

		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';



		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js');

		$document->addScript($cmpnt_js_path.'/jquery.ui.core.js');

		$document->addScript($cmpnt_js_path.'/jquery.ui.widget.js');

		$document->addScript($cmpnt_js_path.'/jquery.ui.mouse.js');

    	$document->addScript($cmpnt_js_path.'/jquery.ui.slider.js');

		$document->addScript($cmpnt_js_path.'/jquery.ui.sortable.js');

		$document->addStyleSheet($cmpnt_js_path.'/jquery-ui.css');

		$document->addStyleSheet($cmpnt_js_path.'/parseTheme.css');



?>

<span style=" position: absolute;right: 29px;" >

<img alt="ADD" title="add" style="cursor:pointer; vertical-align:middle; margin:5px; " src="components/com_formmaker/images/save.png" onclick="save_list()">

<img alt="CANCEL" title="cancel" style=" cursor:pointer; vertical-align:middle; margin:5px; " src="components/com_formmaker/images/cancel_but.png" onclick="window.parent.SqueezeBox.close();">

</span>

<button onclick="select_all()">Select all</button>

<button onclick="remove_all()">Remove all</button>

<ul id="countries_list" style="list-style:none; padding:0px">

</ul>



<script>





selec_coutries=[];



coutries=["","Afghanistan","Albania",	"Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe"];	



select_=window.parent.document.getElementById('<?php echo $id ?>_elementform_id_temp');

n=select_.childNodes.length;

for(i=0; i<n; i++)

{



	selec_coutries.push(select_.childNodes[i].value);

	var ch = document.createElement('input');

		ch.setAttribute("type","checkbox");

		ch.setAttribute("checked","checked");

		ch.value=select_.childNodes[i].value;

		ch.id=i+"ch";

		//ch.setAttribute("id",i);

	

	

	var p = document.createElement('span');

	    p.style.cssText ="color:#000000; font-size: 13px; cursor:move";

		p.innerHTML=select_.childNodes[i].value;



	var li = document.createElement('li');

	    li.style.cssText ="margin:3px; vertical-align:middle";

		li.id=i;

		

	li.appendChild(ch);

	li.appendChild(p);

	

	document.getElementById('countries_list').appendChild(li);

}

cur=i;

m=coutries.length;

for(i=0; i<m; i++)

{

	isin=isValueInArray(selec_coutries, coutries[i]);

	

	if(!isin)

	{

		var ch = document.createElement('input');

			ch.setAttribute("type","checkbox");

			ch.value=coutries[i];

			ch.id=cur+"ch";

		

		

		var p = document.createElement('span');

			p.style.cssText ="color:#000000; font-size: 13px; cursor:move";

			p.innerHTML=coutries[i];



		var li = document.createElement('li');

			li.style.cssText ="margin:3px; vertical-align:middle";

			li.id=cur;

			

		li.appendChild(ch);

		li.appendChild(p);

		

		document.getElementById('countries_list').appendChild(li);

		cur++;

	}

}









	$( "#countries_list" ).sortable();

	$( "#countries_list" ).disableSelection();



function isValueInArray(arr, val) {

	inArray = false;

	for (x = 0; x < arr.length; x++)

		if (val == arr[x])

			inArray = true;

	return inArray;

}

function save_list()

{

select_.innerHTML=""

	ul=document.getElementById('countries_list');

	n=ul.childNodes.length;

	for(i=0; i<n; i++)

	{

		if(ul.childNodes[i].tagName=="LI")

		{

			id=ul.childNodes[i].id;

			if(document.getElementById(id+'ch').checked)

			{

				var option_ = document.createElement('option');

					option_.setAttribute("value", document.getElementById(id+'ch').value);

					option_.innerHTML=document.getElementById(id+'ch').value;



				select_.appendChild(option_);

			}

				

		}

		

		

	}

	window.parent.SqueezeBox.close();





}



function select_all()

{

	for(i=0; i<194; i++)

	{

		document.getElementById(i+'ch').checked=true;;	

	}

}



function remove_all()

{

	for(i=0; i<194; i++)

	{

		document.getElementById(i+'ch').checked=false;;	

	}

}

</script>









<?php



}





public static function product_option($id, $property_id){



		$document		= JFactory::getDocument();

		

		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';



		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js');

		$document->addScript($cmpnt_js_path.'/jquery.ui.core.js');

		$document->addScript($cmpnt_js_path.'/jquery.ui.widget.js');

		$document->addScript($cmpnt_js_path.'/jquery.ui.mouse.js');

    	$document->addScript($cmpnt_js_path.'/jquery.ui.slider.js');

		$document->addScript($cmpnt_js_path.'/jquery.ui.sortable.js');

		$document->addStyleSheet($cmpnt_js_path.'/jquery-ui.css');

		$document->addStyleSheet($cmpnt_js_path.'/parseTheme.css');

		JHTML::_('behavior.modal');



?>

<style>

label{

display:inline;

}

</style>

<span style=" position:fixed; right:10px" >

<img alt="ADD" title="add" style="cursor:pointer; vertical-align:middle; margin:5px; " src="components/com_formmaker/images/save.png" onclick="save_options()">

<img alt="CANCEL" title="cancel" style=" cursor:pointer; vertical-align:middle; margin:5px; " src="components/com_formmaker/images/cancel_but.png" onclick="window.parent.SqueezeBox.close();">

</span>



<div style="margin-left:10px">

	<br>

	<fieldset>

		<legend>

			<label style="color: rgb(0, 174, 239); font-weight: bold; font-size: 13px;">Properties</label>

		</legend>

		<br>

		<div style="margin-left:10px">

		<label style="color: rgb(0, 174, 239); font-weight: bold; font-size: 13px; margin-right:20px">Type </label>

		<select id="option_type" style="width: 200px; border-width: 1px;" onchange="type_add_predefined(this.value)">

			<option value="Custom" selected="selected">Custom</option>

			<option value="Color">Color</option>

			<option value="T-Shirt Size">T-Shirt Size</option>

			<option value="Print Size">Print Size</option>

			<option value="Screen Resolution">Screen Resolution</option>

			<option value="Shoe Size">Shoe Size</option>

		</select>

		<br>



		<label style="color: rgb(0, 174, 239); font-weight: bold; font-size: 13px; margin-right:23px">Title </label>

		<input type="text" style="width:200px"  id="option_name" />

		<br>

		<br>

		<label style="color: rgb(0, 174, 239); font-weight: bold; font-size: 13px;">Properties</label> &nbsp;

		<img id="el_choices_add" src="components/com_formmaker/images/add.png" style="cursor: pointer;" title="add" onclick="add_choise_option()">

		<br>



		<div style="margin-left:0px" id="options" ></div>



		</div>

	</fieldset>







</div>





<script>

var j=0;

function save_options()

{

	

	if( document.getElementById('option_name').value=='')

	{

		alert('The option must have a title')

		return;

	}

<?php



if(!isset($property_id))

{

?>



	

		for(i=30; i>=0; i--)

		{

			if(window.parent.document.getElementById(<?php echo $id ?>+"_propertyform_id_temp"+i))

			{

				i=i+1;

				select_ = document.createElement('select');

				select_.setAttribute("id", <?php echo $id ?>+"_propertyform_id_temp"+i);

				select_.setAttribute("name", <?php echo $id ?>+"_propertyform_id_temp"+i);

				select_.style.cssText = "width:auto; margin:2px 0px";

				break;	

			}

		}

		

		if(i==-1)

		{

			i=0;

			select_ = document.createElement('select');

			select_.setAttribute("id", <?php echo $id ?>+"_propertyform_id_temp"+i);

			select_.setAttribute("name", <?php echo $id ?>+"_propertyform_id_temp"+i);

			select_.style.cssText = "width:auto; margin:2px 0px";;

		}

		

		

		for(k=0; k<=50; k++)

		{

			if(document.getElementById('el_option'+k))

			{

				var option = document.createElement('option');

					option.setAttribute("id","<?php echo $id ?>_"+i+"_option"+k);

					option.setAttribute("value", document.getElementById('el_option'+k).value);

					option.innerHTML =  document.getElementById('el_option'+k).value;

					select_.appendChild(option);	

			}

		}

	



	

	var select_label = document.createElement('label');

			select_label.innerHTML =  document.getElementById('option_name').value;

			select_label.style.cssText = "margin-right:5px";		

			select_label.setAttribute("class", 'mini_label');

			select_label.setAttribute("id", '<?php echo $id ?>_property_label_form_id_temp'+i);



		var span_ = document.createElement('span');

			span_.style.cssText = "margin-right:15px";

			span_.setAttribute("id", '<?php echo $id ?>_property_'+i);

			



		

		div_=window.parent.document.getElementById("<?php echo $id ?>_divform_id_temp");

		span_.appendChild(select_label);

		span_.appendChild(select_);

		div_.appendChild(span_);

		

		var li_ = document.createElement('li');

			li_.setAttribute("id", 'property_li_'+i);



		var li_label = document.createElement('label');

			li_label.innerHTML=document.getElementById('option_name').value;

			li_label.setAttribute("id", 'label_property_'+i);

			li_label.style.cssText ="font-weight:bold; font-size: 13px; display:inline";

			

		var li_edit = document.createElement('a');	

			li_edit.setAttribute("rel", "{handler: 'iframe', size: {x: 650, y: 375}}"	);

			li_edit.setAttribute("href","index.php?option=com_formmaker&task=product_option&field_id=<?php echo $id ?>&property_id="+i+"&tmpl=component");

			li_edit.setAttribute("class","modal");



		var li_edit_img = document.createElement('img');

			li_edit_img.setAttribute("src", 'components/com_formmaker/images/edit.png');

			li_edit_img.style.cssText = "width:14px; height:14px;  display:inline-block; vertical-align:middle; margin:2px; margin-left:13px;";



		li_edit.appendChild(li_edit_img);

			

		var li_x = document.createElement('img');

			li_x.setAttribute("src", 'components/com_formmaker/images/delete.png');

			li_x.setAttribute("onClick", 'remove_property(<?php echo $id ?>,'+i+')');

			li_x.style.cssText = "width:14px; height:14px;  display:inline-block; cursor:pointer; vertical-align:middle; margin:2px";

			

			

		ul_=window.parent.document.getElementById("option_ul");

		

		li_.appendChild(li_label);

		li_.appendChild(li_edit);

		li_.appendChild(li_x);

		ul_.appendChild(li_);

		window.parent.SqueezeBox.assign(li_edit, {

					parse: 'rel'

				});

	

<?php

}	

else

	

{



?>

	

		i=<?php echo $property_id ?>;

		var select_ = window.parent.document.getElementById('<?php echo $id ?>_propertyform_id_temp<?php echo $property_id ?>');	

		select_.innerHTML="";

		for(k=0; k<=j; k++)

		{

			if(document.getElementById('el_option'+k))

			{

				var option = document.createElement('option');

					option.setAttribute("id","<?php echo $id ?>_"+i+"_option"+k);

					option.setAttribute("value", document.getElementById('el_option'+k).value);

					option.innerHTML =  document.getElementById('el_option'+k).value;

					select_.appendChild(option);	

			}

		}

		

		



		var select_label = document.createElement('label');

			select_label.innerHTML =  document.getElementById('option_name').value;

			select_label.style.cssText = "margin-right:5px";

			select_label.setAttribute("class", 'mini_label');

			select_label.setAttribute("id", '<?php echo $id ?>_property_label_form_id_temp'+i);



		var span_ = window.parent.document.getElementById('<?php echo $id ?>_property_<?php echo $property_id ?>');	

			span_.innerHTML='';

		

		span_.appendChild(select_label);

		span_.appendChild(select_);

		window.parent.document.getElementById('label_property_<?php echo $property_id ?>').innerHTML=document.getElementById('option_name').value;



	

<?php

}	



?>



	window.parent.SqueezeBox.close();

}





function type_add_predefined( type )

{

	document.getElementById('options').innerHTML='';

	

	switch(type)

	{

		case 'Custom': 

		{

			w_choices=[];

			break;	

		}



		case 'Color': 

		{

			w_choices=["Red", "Blue", "Green", "Yellow", "Black"];

			break;	

		}



		case 'T-Shirt Size': 

		{

			w_choices=["XS","S","M","L","XL","XXL","XXXL"];

			break;	

		}



		case 'Print Size': 

		{

			w_choices=["A4","A3","A2","A1"];

			break;	

		}



		case 'Screen Resolution': 

		{

			w_choices=["1024x768","1152x864","1280x768","1280x800","1280x960","1280x1024","1366x768","1440x900","1600x1200","1680x1050","1920x1080","1920x1200"];

			break;	

		}



		case 'Shoe Size': 

		{

			w_choices=["8","8.5","9","9.5","10","10.5","11","11.5","12","13","14"];

			break;	

		}



	}

	type_add_options( w_choices);



}







function delete_options()

{

document.getElementById('options').innerHTML='';

}



function type_add_options( w_choices){

	

	i=0;

	edit_main_td3=document.getElementById('options');

	var br = document.createElement('br');

	edit_main_td3.appendChild(br);



	n=w_choices.length;

	for(j=0; j<n; j++)

	{	

		var br = document.createElement('br');

		br.setAttribute("id", "br"+j);

		var el_choices = document.createElement('input');

			el_choices.setAttribute("id", "el_option"+j);

			el_choices.setAttribute("type", "text");

			el_choices.setAttribute("value", w_choices[j]);

			el_choices.style.cssText =   "width:100px; margin:0; padding:0; border-width: 1px";

	//		el_choices.setAttribute("onKeyUp", "change_label('"+i+"_option"+j+"', this.value)");

	

		var el_choices_remove = document.createElement('img');

			el_choices_remove.setAttribute("id", "el_option"+j+"_remove");

			el_choices_remove.setAttribute("src", 'components/com_formmaker/images/delete.png');

			el_choices_remove.style.cssText = 'cursor:pointer; vertical-align:middle; margin:3px';

			el_choices_remove.setAttribute("align", 'top');

			el_choices_remove.setAttribute("onClick", "remove_option("+j+","+i+")");

			

			

		edit_main_td3.appendChild(br);

		edit_main_td3.appendChild(el_choices);

		edit_main_td3.appendChild(el_choices_remove);

	

	}



}







function add_choise_option()

{

		num=0;

		j++;	

		

		var choices_td= document.getElementById('options');

		var br = document.createElement('br');

		br.setAttribute("id", "br"+j);

		var el_choices = document.createElement('input');

			el_choices.setAttribute("id", "el_option"+j);

			el_choices.setAttribute("type", "text");

			el_choices.setAttribute("value", "");

			el_choices.style.cssText =   "width:100px; margin:0; padding:0; border-width: 1px";

		//	el_choices.setAttribute("onKeyUp", "change_label('"+num+"_option"+j+"', this.value)");

			

		var el_choices_remove = document.createElement('img');

			el_choices_remove.setAttribute("id", "el_option"+j+"_remove");

			el_choices_remove.setAttribute("src", 'components/com_formmaker/images/delete.png');

			el_choices_remove.style.cssText = 'cursor:pointer; vertical-align:middle; margin:3px';

			el_choices_remove.setAttribute("align", 'top');

			el_choices_remove.setAttribute("onClick", "remove_option('"+j+"','"+num+"')");

			

	    choices_td.appendChild(br);

	    choices_td.appendChild(el_choices);

	    choices_td.appendChild(el_choices_remove);



}



function remove_option(id, num)

{

		

		var choices_td= document.getElementById('options');

		var el_choices = document.getElementById('el_option'+id);

		var el_choices_remove = document.getElementById('el_option'+id+'_remove');

		var br = document.getElementById('br'+id);

		

		choices_td.removeChild(el_choices);

		choices_td.removeChild(el_choices_remove);

		choices_td.removeChild(br);

}



<?php

if(isset($property_id))

{

?>



	label_	=		window.parent.document.getElementById('<?php echo $id ?>_property_label_form_id_temp<?php echo $property_id ?>').innerHTML;

	select_	=		window.parent.document.getElementById('<?php echo $id ?>_propertyform_id_temp<?php echo $property_id ?>');

	n = select_.childNodes.length;

	delete_options();

 	w_choices=[ ];



	document.getElementById('option_name').value=label_;

	

	

	for(k=0; k<n; k++)

	{

	w_choices.push(select_.childNodes[k].value);

	}

	type_add_options( w_choices);



<?php

}



?>





</script>

<?php



}









public static function preview_formmaker($css){

 /**

 * @package SpiderFC

 * @author Web-Dorado

 * @copyright (C) 2011 Web-Dorado. All rights reserved.

 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

 **/

		JHTML::_('behavior.tooltip');	

		JHTML::_('behavior.calendar');

		$document = JFactory::getDocument();

 		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';



				

		//$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/style.css');

		

	

		$id='form_id_temp';

?>

<script src="<?php echo $cmpnt_js_path.'/if_gmap.js'; ?>" ></script>

<script src="<?php echo $cmpnt_js_path.'/main.js'; ?>" ></script>
<?php if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'): ?>
<script src="https://maps.google.com/maps/api/js?sensor=false" ></script>
<?php else: ?>
<script src="http://maps.google.com/maps/api/js?sensor=false" ></script>
<?php endif; ?>
<script src="<?php echo $cmpnt_js_path ?>/formmaker_div1.js?version=1.2" type="text/javascript" style=""></script>

<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js'; ?>" type="text/javascript"></script>

<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/jquery-ui.js'; ?>" type="text/javascript"></script>

<link rel="stylesheet" href="<?php echo $cmpnt_js_path ?>/jquery-ui-spinner.css" type="text/css">

<style>

html

{

height:90%;

}

<?php echo str_replace('[SITE_ROOT]', JURI::root(true), $css); ?>

</style>

<div id="form_id_temppages" class="wdform_page_navigation" show_title="" show_numbers="" type=""></div>



  <form id="form_preview"></form>

<input type="hidden" id="counter<?php echo $id ?>" value="" name="counter<?php echo $id ?>" />



<script>

	JURI_ROOT				='<?php echo JURI::root(true) ?>';  

	

	document.getElementById('form_preview').innerHTML = window.parent.document.getElementById('take').innerHTML;

	document.getElementById('form_id_temppages').setAttribute('show_title', window.parent.document.getElementById('pages').getAttribute('show_title'));

	document.getElementById('form_id_temppages').setAttribute('show_numbers', window.parent.document.getElementById('pages').getAttribute('show_numbers'));

	document.getElementById('form_id_temppages').setAttribute('type', window.parent.document.getElementById('pages').getAttribute('type'));

	document.getElementById('counterform_id_temp').value=window.parent.gen;;



	form_view_count<?php echo $id ?>=0;

	for(i=1; i<=30; i++)

	{

		if(document.getElementById('<?php echo $id ?>form_view'+i))

		{

			form_view_count<?php echo $id ?>++;

			form_view_max<?php echo $id ?>=i;

			

			document.getElementById('<?php echo $id ?>form_view'+i).parentNode.removeAttribute('style');

		}

	}

	

	refresh_first();



	

	if(form_view_count<?php echo $id ?>>1)

	{

		for(i=1; i<=form_view_max<?php echo $id ?>; i++)

		{

			if(document.getElementById('<?php echo $id ?>form_view'+i))

			{

				first_form_view<?php echo $id ?>=i;

				break;

			}

		}

		

		generate_page_nav(first_form_view<?php echo $id ?>, '<?php echo $id ?>', form_view_count<?php echo $id ?>, form_view_max<?php echo $id ?>);

	}

	



function remove_add_(id)

{

			attr_name= new Array();

			attr_value= new Array();

			var input = document.getElementById(id); 

			atr=input.attributes;

			for(v=0;v<30;v++)

				if(atr[v] )

				{

					if(atr[v].name.indexOf("add_")==0)

					{

						attr_name.push(atr[v].name.replace('add_',''));

						attr_value.push(atr[v].value);

						input.removeAttribute(atr[v].name);

						v--;

					}

				}

			for(v=0;v<attr_name.length; v++)

			{

				input.setAttribute(attr_name[v],attr_value[v])

			}

}



function remove_whitespace(node)

{

    var ttt;

	for (ttt=0; ttt < node.childNodes.length; ttt++)

	{

        if( node.childNodes[ttt] && node.childNodes[ttt].nodeType == '3' && !/\S/.test(  node.childNodes[ttt].nodeValue ) )

		{

			

			node.removeChild(node.childNodes[ttt]);

            ttt--;

		}

		else

		{

			if(node.childNodes[ttt].childNodes.length)

				remove_whitespace(node.childNodes[ttt]);

		}

	}

	return

}



function refresh_first()

{

		

	n=window.parent.gen;

	for(i=0; i<n; i++)

	{

		if(document.getElementById(i))

		{	

			for(z=0; z<document.getElementById(i).childNodes.length; z++)

				if(document.getElementById(i).childNodes[z].nodeType==3)

					document.getElementById(i).removeChild(document.getElementById(i).childNodes[z]);



			if(document.getElementById(i).getAttribute('type')=="type_map")

			{

				if_gmap_init(i);

				for(q=0; q<20; q++)

					if(document.getElementById(i+"_elementform_id_temp").getAttribute("long"+q))

					{

					

						w_long=parseFloat(document.getElementById(i+"_elementform_id_temp").getAttribute("long"+q));

						w_lat=parseFloat(document.getElementById(i+"_elementform_id_temp").getAttribute("lat"+q));

						w_info=parseFloat(document.getElementById(i+"_elementform_id_temp").getAttribute("info"+q));

						add_marker_on_map(i,q, w_long, w_lat, w_info, false);

					}

			}

			

			if(document.getElementById(i).getAttribute('type')=="type_mark_map")

			{

				if_gmap_init(i);

				w_long=parseFloat(document.getElementById(i+"_elementform_id_temp").getAttribute("long"+0));

				w_lat=parseFloat(document.getElementById(i+"_elementform_id_temp").getAttribute("lat"+0));

				w_info=parseFloat(document.getElementById(i+"_elementform_id_temp").getAttribute("info"+0));

				add_marker_on_map(i,0, w_long, w_lat, w_info, true);

			}

			

			

			

			if(document.getElementById(i).getAttribute('type')=="type_captcha" || document.getElementById(i).getAttribute('type')=="type_recaptcha")

			{

				if(document.getElementById(i).childNodes[10])

				{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				}

				else

				{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				}

				continue;

			}

			

			if(document.getElementById(i).getAttribute('type')=="type_section_break")

			{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				continue;

			}

						



			if(document.getElementById(i).childNodes[10])

			{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

			}

			else

			{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

			}

		}

	}

	

	for(i=0; i<=n; i++)

	{	

		if(document.getElementById(i))

		{

			type=document.getElementById(i).getAttribute("type");

				switch(type)

				{

					case "type_text":

					case "type_number":

					case "type_password":

					case "type_submitter_mail":

					case "type_own_select":

					case "type_country":

					case "type_hidden":

					case "type_map":

					{

						remove_add_(i+"_elementform_id_temp");

						break;

					}

					

					case "type_submit_reset":

					{

						remove_add_(i+"_element_submitform_id_temp");

						if(document.getElementById(i+"_element_resetform_id_temp"))

							remove_add_(i+"_element_resetform_id_temp");

						break;

					}

					

					case "type_captcha":

					{

						remove_add_("_wd_captchaform_id_temp");

						remove_add_("_element_refreshform_id_temp");

						remove_add_("_wd_captcha_inputform_id_temp");

						break;

					}

					

					case "type_recaptcha":

					{

						remove_add_("wd_recaptchaform_id_temp");

						break;

					}

						

					case "type_file_upload":

						{

							remove_add_(i+"_elementform_id_temp");

								break;

						}

						

					case "type_textarea":

						{

						remove_add_(i+"_elementform_id_temp");



								break;

						}

						

					case "type_name":

						{

						

						if(document.getElementById(i+"_element_titleform_id_temp"))

							{

							remove_add_(i+"_element_titleform_id_temp");

							remove_add_(i+"_element_firstform_id_temp");

							remove_add_(i+"_element_lastform_id_temp");

							remove_add_(i+"_element_middleform_id_temp");

							}

							else

							{

							remove_add_(i+"_element_firstform_id_temp");

							remove_add_(i+"_element_lastform_id_temp");



							}

							break;



						}

						

					case "type_phone":

						{

						

							remove_add_(i+"_element_firstform_id_temp");

							remove_add_(i+"_element_lastform_id_temp");



							break;



						}

						case "type_address":

							{	

								if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('street1')=='no')

								remove_add_(i+"_street1form_id_temp");

							if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('street2')=='no')	

								remove_add_(i+"_street2form_id_temp");

							if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('city')=='no')

								remove_add_(i+"_cityform_id_temp");

							if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('state')=='no')

								remove_add_(i+"_stateform_id_temp");

							if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('postal')=='no')

								remove_add_(i+"_postalform_id_temp");

							if(document.getElementById(i+"_disable_fieldsform_id_temp").getAttribute('country')=='no')

								remove_add_(i+"_countryform_id_temp");

							

							

								break;

	

							}

							

						

					case "type_checkbox":

					case "type_radio":

						{

							is=true;

							for(j=0; j<100; j++)

								if(document.getElementById(i+"_elementform_id_temp"+j))

								{

									remove_add_(i+"_elementform_id_temp"+j);

								}

						/*	if(document.getElementById(i+"_randomize").value=="yes")

								choises_randomize(i);*/

							

							break;

						}

						

					case "type_button":

						{

							for(j=0; j<100; j++)

								if(document.getElementById(i+"_elementform_id_temp"+j))

								{

									remove_add_(i+"_elementform_id_temp"+j);

								}

							break;

						}

						

					case "type_time":

						{	

						if(document.getElementById(i+"_ssform_id_temp"))

							{

							remove_add_(i+"_ssform_id_temp");

							remove_add_(i+"_mmform_id_temp");

							remove_add_(i+"_hhform_id_temp");

							}

							else

							{

							remove_add_(i+"_mmform_id_temp");

							remove_add_(i+"_hhform_id_temp");

							}

							break;



						}

						

					case "type_date":

						{	

						remove_add_(i+"_elementform_id_temp");

						remove_add_(i+"_buttonform_id_temp");

							break;

						}

					case "type_date_fields":

						{	

						remove_add_(i+"_dayform_id_temp");

						remove_add_(i+"_monthform_id_temp");

						remove_add_(i+"_yearform_id_temp");

								break;

						}

						

					case "type_star_rating":

						{	

							remove_add_(i+"_elementform_id_temp");

						

								break;

						}

					case "type_scale_rating":

						{	

							remove_add_(i+"_elementform_id_temp");

						

								break;

						}

					case "type_spinner":

						{	

							remove_add_(i+"_elementform_id_temp");

							

							var spinner_value = jQuery('#'+i+"_elementform_id_temp").get( "aria-valuenow" );

							var spinner_min_value = document.getElementById(i+"_min_valueform_id_temp").value;

							var spinner_max_value = document.getElementById(i+"_max_valueform_id_temp").value;

							var spinner_step = document.getElementById(i+"_stepform_id_temp").value;

								  

									 jQuery( "#"+i+"_elementform_id_temp" ).removeClass( "ui-spinner-input" )

							.prop( "disabled", false )

							.removeAttr( "autocomplete" )

							.removeAttr( "role" )

							.removeAttr( "aria-valuemin" )

							.removeAttr( "aria-valuemax" )

							.removeAttr( "aria-valuenow" );

				

							span_ui= document.getElementById(i+"_elementform_id_temp").parentNode;

								span_ui.parentNode.appendChild(document.getElementById(i+"_elementform_id_temp"));

								span_ui.parentNode.removeChild(span_ui);

								

								jQuery("#"+i+"_elementform_id_temp")[0].spin = null;

								

								spinner = jQuery( "#"+i+"_elementform_id_temp" ).spinner();

								spinner.spinner( "value", spinner_value );

								jQuery( "#"+i+"_elementform_id_temp" ).spinner({ min: spinner_min_value});    

								jQuery( "#"+i+"_elementform_id_temp" ).spinner({ max: spinner_max_value});

								jQuery( "#"+i+"_elementform_id_temp" ).spinner({ step: spinner_step});

									break;

						}

						

								case "type_slider":

						{	

								remove_add_(i+"_elementform_id_temp");	

								

							var slider_value = document.getElementById(i+"_slider_valueform_id_temp").value;

							var slider_min_value = document.getElementById(i+"_slider_min_valueform_id_temp").value;

							var slider_max_value = document.getElementById(i+"_slider_max_valueform_id_temp").value;

							

							var slider_element_value = document.getElementById( i+"_element_valueform_id_temp" );

							var slider_value_save = document.getElementById( i+"_slider_valueform_id_temp" );

					

							document.getElementById(i+"_elementform_id_temp").innerHTML = "";

							document.getElementById(i+"_elementform_id_temp").removeAttribute( "class" );

							document.getElementById(i+"_elementform_id_temp").removeAttribute( "aria-disabled" );

							jQuery("#"+i+"_elementform_id_temp")[0].slide = null;	

							

							

							jQuery( "#"+i+"_elementform_id_temp").slider({

								range: "min",

								value: eval(slider_value),

								min: eval(slider_min_value),

								max: eval(slider_max_value),

								slide: function( event, ui ) {	

									slider_element_value.innerHTML = "" + ui.value ;

									slider_value_save.value = "" + ui.value; 



								}

							});

                         break;

						}

								case "type_range":

						{	

							remove_add_(i+"_elementform_id_temp0");

							remove_add_(i+"_elementform_id_temp1");

						

							var spinner_value0 = jQuery('#'+i+"_elementform_id_temp0").get( "aria-valuenow" );

							var spinner_step = document.getElementById(i+"_range_stepform_id_temp").value;

								  

									 jQuery( "#"+i+"_elementform_id_temp0" ).removeClass( "ui-spinner-input" )

							.prop( "disabled", false )

							.removeAttr( "autocomplete" )

							.removeAttr( "role" )

							.removeAttr( "aria-valuenow" );

							

							span_ui= document.getElementById(i+"_elementform_id_temp0").parentNode;

								span_ui.parentNode.appendChild(document.getElementById(i+"_elementform_id_temp0"));

								span_ui.parentNode.removeChild(span_ui);

							

							jQuery("#"+i+"_elementform_id_temp0")[0].spin = null;

							jQuery("#"+i+"_elementform_id_temp1")[0].spin = null;

							

							

							spinner0 = jQuery( "#"+i+"_elementform_id_temp0" ).spinner();

							spinner0.spinner( "value", spinner_value0 );

							jQuery( "#"+i+"_elementform_id_temp0" ).spinner({ step: spinner_step});

							

							

							

								var spinner_value1 = jQuery('#'+i+"_elementform_id_temp1").get( "aria-valuenow" );

												  

									 jQuery( "#"+i+"_elementform_id_temp1" ).removeClass( "ui-spinner-input" )

							.prop( "disabled", false )

							.removeAttr( "autocomplete" )

							.removeAttr( "role" )

							.removeAttr( "aria-valuenow" );

							

							span_ui1= document.getElementById(i+"_elementform_id_temp1").parentNode;

							span_ui1.parentNode.appendChild(document.getElementById(i+"_elementform_id_temp1"));

							span_ui1.parentNode.removeChild(span_ui1);

							

							spinner1 = jQuery( "#"+i+"_elementform_id_temp1" ).spinner();

							spinner1.spinner( "value", spinner_value1 );

							jQuery( "#"+i+"_elementform_id_temp1" ).spinner({ step: spinner_step});

				

								break;

						}

						case "type_grading":

						{

							

							for(k=0; k<100; k++)

								if(document.getElementById(i+"_elementform_id_temp"+k))

								{

									remove_add_(i+"_elementform_id_temp"+k);

								}

						

							

							break;

						}

						

						case "type_matrix":

						{	

							remove_add_(i+"_elementform_id_temp");

						

								break;

						}	

				}	

		}

	}

	



	for(t=1;t<=form_view_max<?php echo $id ?>;t++)

	{

		if(document.getElementById('form_id_tempform_view'+t))

		{

			form_view_element=document.getElementById('form_id_tempform_view'+t);

			remove_whitespace(form_view_element);

			xy=form_view_element.childNodes.length-2;

			for(z=0;z<=xy;z++)

			{

				if(form_view_element.childNodes[z])

				if(form_view_element.childNodes[z].nodeType!=3)

				if(!form_view_element.childNodes[z].id)

				{

					del=true;

					GLOBAL_tr=form_view_element.childNodes[z];

					//////////////////////////////////////////////////////////////////////////////////////////

					for (x=0; x < GLOBAL_tr.firstChild.childNodes.length; x++)

					{

						table=GLOBAL_tr.firstChild.childNodes[x];

						tbody=table.firstChild;

						if(tbody.childNodes.length)

							del=false;

					}

					

					if(del)

					{

						form_view_element.removeChild(form_view_element.childNodes[z]);

					}



				}

			}

		}

	}





	for(i=1; i<=window.parent.form_view_max; i++)

		if(document.getElementById('form_id_tempform_view'+i))

		{

			document.getElementById('form_id_tempform_view'+i).parentNode.removeChild(document.getElementById('form_id_tempform_view_img'+i));

			document.getElementById('form_id_tempform_view'+i).removeAttribute('style');

		}

	

}





</script>



	

	

<?php 





}



public static function add_blocked_ips($rows){



	JRequest::setVar( 'hidemainmenu', 1 );

		

		?>

        

<script>

function check_isnum_point(e)

{

   	var chCode1 = e.which || e.keyCode;

	

	if (chCode1 ==46)

		return true;

	

	if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57))

        return false;

	return true;

}



function submitbutton(pressbutton) {

	

	var form = document.adminForm;

	

	if (pressbutton == 'cancel_themes') 

	{

		submitform( pressbutton );

		return;

	}





	submitform( pressbutton );

}





</script>        

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<table class="admintable">



 

				<tr>

					<td class="key">

						<label for="title">

							IP:

						</label>

					</td>

					<td >

                                    <input type="text" name="ip" id="ip" onkeypress="return check_isnum_point(event);" size="60"/>

					</td>

				</tr>

			

</table>           

    <input type="hidden" name="option" value="com_formmaker" />

    <input type="hidden" name="task" value="" />

</form>



	   <?php	

	



}





public static function add($themes){



		JRequest::setVar( 'hidemainmenu', 1 );

		$user = JFactory::getUser();

		$document = JFactory::getDocument();

		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';



		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js');

		$document->addScript($cmpnt_js_path.'/if_gmap.js');

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
			$document->addScript('https://maps.google.com/maps/api/js?sensor=false');
		else	
			$document->addScript('http://maps.google.com/maps/api/js?sensor=false');
		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/style.css?version=1.2');

		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/js/jquery-ui-spinner.css');

		

		$is_editor=false;

		

		$plugin = JPluginHelper::getPlugin('editors', 'tinymce');

		if (isset($plugin->type))

		{ 

			$editor	= JFactory::getEditor('tinymce');

			$is_editor=true;

		}

		$editor	= JFactory::getEditor('tinymce');

		JHTML::_('behavior.tooltip');	

		JHTML::_('behavior.calendar');

		JHTML::_('behavior.modal');

		?>

	

<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js'; ?>" type="text/javascript"></script>
<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/jquery-ui.js'; ?>" type="text/javascript"></script>
<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/noconflict.js'; ?>" type="text/javascript"></script>

<script type="text/javascript">

if($)

if(typeof $.noConflict === 'function'){

   $.noConflict();

}



function remove_empty_columns()

{

	wdformjQuery('.wdform_section').each(function() {

		if(wdformjQuery(this).find('.wdform_column').last().prev().html()=='')

		{

			if(wdformjQuery(this).children().length>2)

			{

				wdformjQuery(this).find('.wdform_column').last().prev().remove();

				remove_empty_columns();

			}

		}	

	});



}





function sortable_columns()

{

    wdformjQuery( ".wdform_column" ).sortable({

		connectWith: ".wdform_column",

		cursor: 'move',

		placeholder: "highlight",

		start: function(e,ui){

			wdformjQuery('.wdform_column').each(function() {

				if(wdformjQuery(this).html())

				{

					wdformjQuery(this).append(wdformjQuery('<div class="wdform_empty_row" style="height:80px;"></div>'));

					wdformjQuery( ".wdform_column" ).sortable( "refresh" );

				}

			});			

		},

		update: function(event, ui) {

				wdformjQuery('.wdform_section .wdform_column:last-child').each(function() {

					if(wdformjQuery(this).html())

					{

						wdformjQuery(this).parent().append(wdformjQuery('<div></div>').addClass("wdform_column"));	

						sortable_columns();

					}		

				});

					

				

		},

		stop: function(event, ui) {

			wdformjQuery('.wdform_empty_row').remove();	

			remove_empty_columns();		

		}

    });



}



wdformjQuery(function() {



	wdformjQuery('.wdform_section .wdform_column:last-child').each(function() {

			wdformjQuery(this).parent().append(wdformjQuery('<div></div>').addClass("wdform_column"));		

		});



	sortable_columns();

	all_sortable_events();

});



function all_sortable_events()

{



	wdformjQuery(document).on( "click", ".wdform_row, .wdform_tr_section_break", function() {	var this2=this; setTimeout( function(){	

		if(wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).attr("class")=="wdform_arrows_show")

		{

			wdformjQuery("#wdform_field"+wdformjQuery(this2).attr("wdid")).css({"background-color":"transparent", "border":"none","margin-top":""});

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).removeClass("wdform_arrows_show");

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).addClass("wdform_arrows");

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).hide();

		}

		else

		{

			wdformjQuery(".wdform_arrows_show").addClass("wdform_arrows");

			wdformjQuery(".wdform_arrows").hide();

			wdformjQuery(".wdform_arrows_show").removeClass("wdform_arrows_show");

			wdformjQuery(".wdform_field, .wdform_field_section_break").css("background-color","transparent");

			wdformjQuery(".wdform_field, .wdform_field_section_break").css("border","none");

			wdformjQuery(".wdform_field").css("margin-top","");

			

			if(wdformjQuery("#wdform_field"+wdformjQuery(this2).attr("wdid")).attr("type")=='type_editor')

			wdformjQuery("#wdform_field"+wdformjQuery(this2).attr("wdid")).css("margin-top","-5px");

			

			wdformjQuery("#wdform_field"+wdformjQuery(this2).attr("wdid")).css({"background-color":"rgb(224, 224, 224)","border":"1px solid rgb(213, 213, 213)"});

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).removeClass("wdform_arrows");

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).addClass("wdform_arrows_show");

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).show();

		}



	},300)});



	wdformjQuery(document).on( "hover", ".wdform_tr_section_break", function() {

		wdformjQuery("#wdform_field"+wdformjQuery(this).attr("wdid")).css({"background-color":"rgb(224, 224, 224)"});

	});



	wdformjQuery(document).on( "hover", ".wdform_row", function() {

		wdformjQuery("#wdform_field"+wdformjQuery(this).attr("wdid")).css({"cursor":"move","background-color":"rgb(224, 224, 224)"});

	});



	wdformjQuery(document).on( "mouseleave", ".wdform_row, .wdform_tr_section_break", function() {

		if(wdformjQuery("#wdform_arrows"+wdformjQuery(this).attr("wdid")).attr("class")!="wdform_arrows_show")

		{

			wdformjQuery("#wdform_field"+wdformjQuery(this).attr("wdid")).css({"background-color":"transparent", "border":"none"});

			wdformjQuery("#wdform_arrows"+wdformjQuery(this).attr("wdid")).addClass("wdform_arrows");

		}

	});



}



wdformjQuery(document).on( "dblclick", ".wdform_row, .wdform_tr_section_break", function() {

		edit(wdformjQuery(this).attr("wdid"));

	});



function enable_drag(elem)

{

	if(wdformjQuery('#enable_sortable').prop( 'checked' ))

	{

		wdformjQuery('#enable_sortable').val(1);

		wdformjQuery('.wdform_column').sortable( "enable" );

		wdformjQuery( ".wdform_arrows" ).slideUp(700);

		all_sortable_events();

	}

	else

	{

		wdformjQuery('#enable_sortable').val(0);

		wdformjQuery('.wdform_column').sortable( "disable" );

		wdformjQuery(".wdform_column").css("border","none");				

		wdformjQuery( ".wdform_row, .wdform_tr_section_break" ).die("click");

		wdformjQuery( ".wdform_row" ).die("hover");

		wdformjQuery( ".wdform_tr_section_break" ).die("hover");

		wdformjQuery( ".wdform_field" ).css("cursor","default");

		wdformjQuery( ".wdform_field, .wdform_field_section_break" ).css("background-color","transparent");

		wdformjQuery( ".wdform_field, .wdform_field_section_break" ).css("border","none");

		wdformjQuery( ".wdform_arrows_show" ).hide();

		wdformjQuery( ".wdform_arrows_show" ).addClass("wdform_arrows");

		wdformjQuery( ".wdform_arrows_show" ).removeClass("wdform_arrows_show");

		wdformjQuery( ".wdform_arrows" ).slideDown(600);	

	}

}



var already_submitted=false;

function refresh_()

{

				

	document.getElementById('counter').value=gen;

	

	for(i=1; i<=form_view_max; i++)

		if(document.getElementById('form_id_tempform_view'+i))

		{

			if(document.getElementById('page_next_'+i))

				document.getElementById('page_next_'+i).removeAttribute('src');

			if(document.getElementById('page_previous_'+i))

				document.getElementById('page_previous_'+i).removeAttribute('src');

			document.getElementById('form_id_tempform_view'+i).parentNode.removeChild(document.getElementById('form_id_tempform_view_img'+i));

			document.getElementById('form_id_tempform_view'+i).removeAttribute('style');

		}

		

	document.getElementById('form_front').value=document.getElementById('take').innerHTML;

}



Joomla.submitbutton= function (pressbutton){



	var form = document.adminForm;

	if (pressbutton == 'cancel') 

	{

		submitform( pressbutton );

		return;

	}

if (already_submitted ) 

	{

		submitform( pressbutton );

		return;

	}

	if (form.title.value == "")

	{

		alert( "The form must have a title." );	

		return ;

	}		



	

	document.getElementById('take').style.display="none";

	document.getElementById('page_bar').style.display="none";

	

	document.getElementById('saving').style.display="block";

	remove_whitespace(document.getElementById('take'));

	

	wdformjQuery('.wdform_section').each(function() {

		var this2 = this;

		wdformjQuery(this2).find('.wdform_column').each(function() {

			if(!wdformjQuery(this).html() && wdformjQuery(this2).children().length>1)

				wdformjQuery(this).remove();

		});

	});

	

	

	tox='';

	form_fields='';

	

	for(t=1;t<=form_view_max;t++)

	{

		if(document.getElementById('form_id_tempform_view'+t))

		{

			wdform_page=document.getElementById('form_id_tempform_view'+t);

			n=wdform_page.childNodes.length-2;



			for(z=0;z<=n;z++)

			{

				if(!wdform_page.childNodes[z].getAttribute("wdid"))

				{

					wdform_section=wdform_page.childNodes[z];

					for (x=0; x < wdform_section.childNodes.length; x++)

					{

						wdform_column=wdform_section.childNodes[x];

						if(wdform_column.firstChild)

						for (y=0; y < wdform_column.childNodes.length; y++)

						{

							wdform_row=wdform_column.childNodes[y];

							wdid=wdform_row.getAttribute("wdid");

							l_label = document.getElementById( wdid+'_element_labelform_id_temp').innerHTML;

							l_label = l_label.replace(/(\r\n|\n|\r)/gm," ");

							wdtype=wdform_row.firstChild.getAttribute('type');



							if(wdtype=="type_address")

							{

								addr_id=parseInt(wdid);

								id_for_country= addr_id;

								

								if(document.getElementById(id_for_country+"_mini_label_street1"))

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_street1").innerHTML+'#**label**#type_address#****#';addr_id++; 

								if(document.getElementById(id_for_country+"_mini_label_street2"))	

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_street2").innerHTML+'#**label**#type_address#****#';addr_id++; 	

								if(document.getElementById(id_for_country+"_mini_label_city"))	

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_city").innerHTML+'#**label**#type_address#****#';	addr_id++;

								if(document.getElementById(id_for_country+"_mini_label_state"))	

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_state").innerHTML+'#**label**#type_address#****#';	addr_id++;		

								if(document.getElementById(id_for_country+"_mini_label_postal"))

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_postal").innerHTML+'#**label**#type_address#****#';	addr_id++; 

								if(document.getElementById(id_for_country+"_mini_label_country"))

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_country").innerHTML+'#**label**#type_address#****#'; 

							}

							else

								tox=tox+wdid+'#**id**#'+l_label+'#**label**#'+wdtype+'#****#';

								

							

							id=wdid;

							form_fields+=wdid+"*:*id*:*";

							form_fields+=wdtype+"*:*type*:*";

							

							

							w_choices=new Array();	

							w_choices_value=new Array();

							w_choices_checked=new Array();

							w_choices_disabled=new Array();

							w_choices_params =new Array();

							w_allow_other_num=0;

							w_property=new Array();	

							w_property_type=new Array();	

							w_property_values=new Array();

							w_choices_price=new Array();

							

							if(document.getElementById(id+'_element_labelform_id_temp').innerHTML)

								w_field_label=document.getElementById(id+'_element_labelform_id_temp').innerHTML.replace(/(\r\n|\n|\r)/gm," ");

								

							if(document.getElementById(id+'_label_sectionform_id_temp'))

							if(document.getElementById(id+'_label_sectionform_id_temp').style.display=="block")

								w_field_label_pos="top";

							else

								w_field_label_pos="left";

								

							if(document.getElementById(id+"_elementform_id_temp"))

							{

								s=document.getElementById(id+"_elementform_id_temp").style.width;

								w_size=s.substring(0,s.length-2);

							}

							

							if(document.getElementById(id+"_label_sectionform_id_temp"))

							{

								s=document.getElementById(id+"_label_sectionform_id_temp").style.width;

								w_field_label_size=s.substring(0,s.length-2);

							}

							

							if(document.getElementById(id+"_requiredform_id_temp"))

								w_required=document.getElementById(id+"_requiredform_id_temp").value;

								

							if(document.getElementById(id+"_uniqueform_id_temp"))

								w_unique=document.getElementById(id+"_uniqueform_id_temp").value;

								

							if(document.getElementById(id+'_label_sectionform_id_temp'))

							{

								w_class=document.getElementById(id+'_label_sectionform_id_temp').getAttribute("class");

								if(!w_class)

									w_class="";

							}

								

							gen_form_fields();

							wdform_row.innerHTML="%"+id+" - "+l_label+"%";

							

						}

					

					}

					

					

				}

				

				else

				{

						id=wdform_page.childNodes[z].getAttribute("wdid");

						w_editor=document.getElementById(id+"_element_sectionform_id_temp").innerHTML;

						

						form_fields+=id+"*:*id*:*";

						form_fields+="type_section_break"+"*:*type*:*";

												

						form_fields+="custom_"+id+"*:*w_field_label*:*";

						form_fields+=w_editor+"*:*w_editor*:*";

						form_fields+="*:*new_field*:*";

						wdform_page.childNodes[z].innerHTML="%"+id+" - "+"custom_"+id+"%";

						



				}

			}

		}

	}

	

	document.getElementById('form_fields').value=form_fields;

	document.getElementById('label_order').value=tox;

	document.getElementById('label_order_current').value=tox;

	refresh_();

	document.getElementById('pagination').value=document.getElementById('pages').getAttribute("type");

	document.getElementById('show_title').value=document.getElementById('pages').getAttribute("show_title");

	document.getElementById('show_numbers').value=document.getElementById('pages').getAttribute("show_numbers");

	

	already_submitted= true; 

		

	submitform( pressbutton );



}



gen=1; 

form_view=1; 

form_view_max=1; 

form_view_count=1;







 //add main form  id

    function enable()

	{

	alltypes=Array('customHTML','text','checkbox','radio','time_and_date','select','file_upload','captcha','map','button','page_break','section_break','paypal','survey');

	for(x=0; x<14;x++)

	{

		document.getElementById('img_'+alltypes[x]).src="components/com_formmaker/images/"+alltypes[x]+".png";

	}

	

		if(document.getElementById('formMakerDiv').style.display=='block'){wdformjQuery('#formMakerDiv').slideToggle(200);}else{wdformjQuery('#formMakerDiv').slideToggle(400);}

		

		if(document.getElementById('formMakerDiv').offsetWidth)

			document.getElementById('formMakerDiv1').style.width	=(document.getElementById('formMakerDiv').offsetWidth - 60)+'px';

		if(document.getElementById('formMakerDiv1').style.display=='block'){wdformjQuery('#formMakerDiv1').slideToggle(200);}else{wdformjQuery('#formMakerDiv1').slideToggle(400);}

		document.getElementById('when_edit').style.display		='none';

	}



    function enable2()

	{

	alltypes=Array('customHTML','text','checkbox','radio','time_and_date','select','file_upload','captcha','map','button','page_break','section_break','paypal','survey');

	for(x=0; x<14;x++)

	{

		document.getElementById('img_'+alltypes[x]).src="components/com_formmaker/images/"+alltypes[x]+".png";

	}

	

		if(document.getElementById('formMakerDiv').style.display=='block'){wdformjQuery('#formMakerDiv').slideToggle(200);}else{wdformjQuery('#formMakerDiv').slideToggle(400);}

		

		if(document.getElementById('formMakerDiv').offsetWidth)

			document.getElementById('formMakerDiv1').style.width	=(document.getElementById('formMakerDiv').offsetWidth - 60)+'px';

	if(document.getElementById('formMakerDiv1').style.display=='block'){wdformjQuery('#formMakerDiv1').slideToggle(200);}else{wdformjQuery('#formMakerDiv1').slideToggle(400);}



	document.getElementById('when_edit').style.display		='block';

		if(document.getElementById('field_types').offsetWidth)

			document.getElementById('when_edit').style.width	=document.getElementById('field_types').offsetWidth+'px';

		

		if(document.getElementById('field_types').offsetHeight)

			document.getElementById('when_edit').style.height	=document.getElementById('field_types').offsetHeight+'px';

		

	}

	



    </script>

<style>

#take_temp .element_toolbar, #take_temp .page_toolbar, #take_temp .captcha_img, #take_temp .wdform_stars

{

display:none;

}

#when_edit

{

position:absolute;

background-color:#666;

z-index:101;

display:none;

width:100%;

height:100%;

opacity: 0.7;

filter: alpha(opacity = 70);

}

#formMakerDiv

{

position:fixed;

background-color:#666;

z-index:100;

display:none;

left:0;

top:0;

width:100%;

height:100%;

opacity: 0.7;

filter: alpha(opacity = 70);

}

#formMakerDiv1

{

position:fixed;

z-index:100;

background-color:transparent;

top:0;

left:0;

display:none;

margin-left:30px;

margin-top:35px;

}



input[type="radio"], input[type="checkbox"] {

margin: 5px;

}





.pull-left

{

float:none !important;

}



.modal-body

{

max-height:100%;

}



.wdform_date

{

margin:0px !important;

}



img

{

max-width:none;

}



.formmaker_table input

{

border-radius: 3px;

padding: 2px;

}



.formmaker_table

{

border-radius:8px;

border:6px #00aeef solid;

background-color:#00aeef;

height:120px;

}



.formMakerDiv1_table

{

border:6px #00aeef solid;

background-color:#FFF;

border-radius:8px;

}



label

{

display:inline;

}



</style>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div  class="formmaker_table" width="100%" >

<div style="float:left; text-align:center">

 	</br>

   <img src="components/com_formmaker/images/FormMaker.png" />

	</br>

	</br>

	<img src="components/com_formmaker/images/logo.png" />



</div>



<div style="float:right">



    <span style="font-size:16.76pt; font-family:tahoma; color:#FFFFFF; vertical-align:middle;">Form title:&nbsp;&nbsp;</span>



    <input id="title" name="title" style="width:151px; height:19px; border:none; font-size:11px; "  />

	<br/>

	<a href="#" onclick="Joomla.submitbutton('form_options_temp')" style="cursor:pointer;margin:10px; float:right; color:#fff; font-size:20px"><img src="components/com_formmaker/images/formoptions.png" /></a>    

	<br/>

	<img src="components/com_formmaker/images/addanewfield.png" onclick="enable(); Enable()" style="cursor:pointer;margin:10px; float:right" />



</div>

	

  



</div>

 

  

<div id="formMakerDiv" onclick="close_window()"></div>   



<div id="formMakerDiv1" align="center">

    

<table border="0" width="100%" cellpadding="0" cellspacing="0" height="100%" style="border:6px #00aeef solid; background-color:#FFF">

  <tr>

    <td style="padding:0px">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">

        <tr>

		 <td width="15%" height="100%" style="border-right:dotted black 1px;" id="field_types">

			<div id="when_edit" style="display:none"></div>

            <table border="0" cellpadding="0" cellspacing="3" width="100%" style="border-collapse: separate; border-spacing: 3px;">
				<tr>
					<td align="center" onClick="addRow('customHTML')" class="field_buttons" id="table_editor"><img src="components/com_formmaker/images/customHTML.png" style="margin:5px" id="img_customHTML"/></td>
					<td align="center" onClick="addRow('text')" class="field_buttons" id="table_text"><img src="components/com_formmaker/images/text.png" style="margin:5px" id="img_text"/></td>
				</tr>
				<tr>             
					<td align="center" onClick="addRow('checkbox')" class="field_buttons" id="table_checkbox"><img src="components/com_formmaker/images/checkbox.png" style="margin:5px" id="img_checkbox"/></td>
					<td align="center" onClick="addRow('radio')" class="field_buttons" id="table_radio"><img src="components/com_formmaker/images/radio.png" style="margin:5px" id="img_radio"/></td>
				</tr>
				<tr>
					<td align="center" onClick="addRow('survey')" class="field_buttons" id="table_survey"><img src="components/com_formmaker/images/survey.png" style="margin:5px" id="img_survey"/></td>           
					<td align="center" onClick="addRow('time_and_date')" class="field_buttons" id="table_time_and_date"><img src="components/com_formmaker/images/time_and_date.png" style="margin:5px" id="img_time_and_date"/></td>
			   </tr>
				<tr>
					<td align="center" onClick="addRow('select')" class="field_buttons" id="table_select"><img src="components/com_formmaker/images/select.png" style="margin:5px" id="img_select"/></td>
					<td align="center" onClick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons" id="table_file_upload" style="background:#727171 !important;"><img src="components/com_formmaker/images/file_upload.png" style="margin:5px" id="img_file_upload"/></td>
				</tr>
				<tr>
					<td align="center" onClick="addRow('section_break')" class="field_buttons" id="table_section_break"><img src="components/com_formmaker/images/section_break.png" style="margin:5px" id="img_section_break"/></td>
					<td align="center" onClick="addRow('page_break')" class="field_buttons" id="table_page_break"><img src="components/com_formmaker/images/page_break.png" style="margin:5px" id="img_page_break"/></td>  
				</tr>
				<tr>
					<td align="center" onClick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons" id="table_map" style="background:#727171 !important;"><img src="components/com_formmaker/images/map.png" style="margin:5px" id="img_map"/></td>  
					<td align="center" onClick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" id="table_paypal" class="field_buttons" style="background:#727171 !important;"><img src="components/com_formmaker/images/paypal.png" style="margin:5px" id="img_paypal" /></td>       
			   </tr>
				<tr>
					<td align="center" onClick="addRow('captcha')" class="field_buttons" id="table_captcha"><img src="components/com_formmaker/images/captcha.png" style="margin:5px" id="img_captcha"/></td>
					<td align="center" onClick="addRow('button')" id="table_button" class="field_buttons" ><img src="components/com_formmaker/images/button.png" style="margin:5px" id="img_button"/></td>			
				</tr>
            </table>
        
         </td>

         <td width="35%" height="100%" align="left"><div id="edit_table" style="padding:0px; overflow-y:scroll; height:535px" ></div></td>

   <td align="center" valign="top" style="background:url(components/com_formmaker/images/border2.png) repeat-y;">&nbsp;</td>

         <td style="padding:15px">

         <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">

         

            <tr>

                <td align="right"><input type="radio" value="end" name="el_pos" checked="checked" id="pos_end" onclick="Disable()"/>

                  At The End

                  <input type="radio" value="begin" name="el_pos" id="pos_begin" onclick="Disable()"/>

                  At The Beginning

                  <input type="radio" value="before" name="el_pos" id="pos_before" onclick="Enable()"/>

                  Before

                  <select style="width:100px; margin-left:5px" id="sel_el_pos" onclick="change_before()" disabled="disabled">

                  </select>

                  <img alt="ADD" title="add" style="cursor:pointer; vertical-align:middle; margin:5px" src="components/com_formmaker/images/save.png" onClick="add(0, false)"/>

                  <img alt="CANCEL" title="cancel"  style=" cursor:pointer; vertical-align:middle; margin:5px" src="components/com_formmaker/images/cancel_but.png" onClick="close_window()"/>

				

                	<hr style=" margin-bottom:10px" />

                  </td>

              </tr>

              

              <tr height="100%" valign="top">

                <td  id="show_table"></td>

              </tr>

              

            </table>

         </td>

        </tr>

      </table>

    </td>

  </tr>

</table>

<input type="hidden" id="old" />

<input type="hidden" id="old_selected" />

<input type="hidden" id="element_type" />

<input type="hidden" id="editing_id" />

<input type="hidden" id="editing_page_break" />

<div id="main_editor" style="position:absolute; display:none; z-index:140;"><?php if($is_editor) echo $editor->display('editor','','440','350','40','6');

else

{

?>

<textarea name="editor" id="editor" cols="40" rows="6" style="width: 440px; height: 350px; " class="mce_editable" aria-hidden="true"></textarea>

<?php



}

 ?></div>



</div>





<?php if(!$is_editor)

?>

<iframe id="editor_ifr" style="display:none"></iframe>

<br/>

	<div style="font-size:16px; margin-left:5px; color: red; font-weight:bold;">You can use drag and drop to move the fields up/down for the change of the order and left/right for creating columns within the form.</div>

	<br/>

	<div style="margin-left:5px;"><label for="enable_sortable" style="font-size:16px; font-weight:bold;">Enable Drag & Drop</label>

	<input type="checkbox" name="sortable" id="enable_sortable" value="1" onclick="enable_drag(this)" checked="checked"/></div>

	<br/>



<fieldset>



    <legend>



    <h2 style="color:#00aeef">Form</h2>

    

    </legend>



     <style><?php echo self::first_css; ?></style>

<div id="saving" style="display:none">

	<div id="saving_text">Saving</div>

	<div id="fadingBarsG">

	<div id="fadingBarsG_1" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_2" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_3" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_4" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_5" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_6" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_7" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_8" class="fadingBarsG">

	</div>

	</div>

</div>





<div style="margin:8px; display:table; width:100%"  id="page_bar"><div id="page_navigation" style="display:table-row"><div align="center" id="pages" show_title="false" show_numbers="true" type="none" style="display:table-cell;  width:90%"></div><div align="left" id="edit_page_navigation" style="display:table-cell; vertical-align: middle;"></div></div></div><div id="take" class="main"><div class="wdform-page-and-images" style="display:table; border-top:0px solid black;"><div id="form_id_tempform_view1" class="wdform_page" page_title="Untitled page" next_title="Next" next_type="text" next_class="wdform-page-button" next_checkable="false" previous_title="Previous" previous_type="text" previous_class="wdform-page-button" previous_checkable="false"><div class="wdform_section"><div class="wdform_column"></div></div><div valign="top" class="wdform_footer" style="width: 100%;"><div style="width: 100%;"><div style="width: 100%; display: table;"><div style="display: table-row-group;"><div id="form_id_temppage_nav1" style="display: table-row;"></div></div></div></div></div></div><div id="form_id_tempform_view_img1" style="float:right ;"><div><img src="components/com_formmaker/images/minus.png" title="Show or hide the page" class="page_toolbar" onClick="show_or_hide('1')" onmouseover="chnage_icons_src(this,'minus')"  onmouseout="chnage_icons_src(this,'minus')" id="show_page_img_1" style="margin: 5px 5px 5px 0;"/><img src="components/com_formmaker/images/page_delete.png" title="Delete the page" class="page_toolbar" onClick="remove_page('1')" onmouseover="chnage_icons_src(this,'page_delete')"  onmouseout="chnage_icons_src(this,'page_delete')" style="margin: 5px 5px 5px 0;"/><img src="components/com_formmaker/images/page_delete_all.png" title="Delete the page with fields" class="page_toolbar" onClick="remove_page_all('1')" onmouseover="chnage_icons_src(this,'page_delete_all')"  onmouseout="chnage_icons_src(this,'page_delete_all')" style="margin: 5px 5px 5px 0;"/><img src="components/com_formmaker/images/page_edit.png" title="Edit the page" class="page_toolbar" onClick="edit_page_break('1')" onmouseover="chnage_icons_src(this,'page_edit')"  onmouseout="chnage_icons_src(this,'page_edit')" style="margin: 5px 5px 5px 0;"/></div></div></div></div></fieldset>



    <input type="hidden" name="form_front" id="form_front" />

    <input type="hidden" name="counter" id="counter" />

    <input type="hidden" name="mail" id="mail" />

	<input type="hidden" name="created_by" value="<?php echo $user->id; ?>" />

	

	<?php 

	$form_theme='';

	foreach($themes as $theme) 

	{

		if($theme->default == 1 )

			$form_theme=$theme->id;		

	}

	?>

	<input type="hidden" name="theme" id="theme" value="<?php echo $form_theme?>" />



    <input type="hidden" name="pagination" id="pagination" />

    <input type="hidden" name="show_title" id="show_title" />

    <input type="hidden" name="show_numbers" id="show_numbers" />

    <input type="hidden" name="payment_currency" id="show_numbers" value="USD"/>

	

    <input type="hidden" name="public_key" id="public_key" />

    <input type="hidden" name="private_key" id="private_key" />

    <input type="hidden" name="recaptcha_theme" id="recaptcha_theme" />

	<input type="hidden" name="javascript" id="javascript" value="// Occurs before the form is loaded

function before_load()

{



}



// Occurs just before submitting  the form

function before_submit()

{



}



// Occurs just before resetting the form

function before_reset()

{



}">

	<input type="hidden" name="script_mail" id="script_mail" value="%all%" />

	<input type="hidden" name="script_mail_user" id="script_mail_user"  value="%all%" />

	<input type="hidden" name="form_fields" id="form_fields" />

	<input type="hidden" name="label_order" id="label_order" />

	<input type="hidden" name="label_order_current" id="label_order_current" />

    <input type="hidden" name="option" value="com_formmaker" />



    <input type="hidden" name="task" value="" />



</form>


<script src="<?php echo  $cmpnt_js_path ?>/formmaker_div1.js?version=1.2" type="text/javascript" style=""></script>


  <?php

}



public static function show_submits(&$rows, &$forms, &$lists, &$pageNav, &$labels, $label_titles, $group_id_s, $form_id, $labels_id, $sorted_labels_type, $total_entries, $total_views, $join_count)

{



	$label_titles_copy=$label_titles;

	JHTML::_('behavior.tooltip');

	JHTML::_('behavior.calendar');

	JHTML::_('behavior.modal');

	JHtml::_('behavior.formvalidation');

	JHtml::_('behavior.switcher');

	JHtml::_('formbehavior.chosen', 'select');

	jimport('joomla.filesystem.path');

	jimport('joomla.filesystem.file');





	$user = JFactory::getUser();

	$document = JFactory::getDocument();

	$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/style.css?version=1.2');

	

	$mainframe = JFactory::getApplication();

	JSubMenuHelper::addEntry(JText::_('Forms'), 'index.php?option=com_formmaker&amp;task=forms' );

	JSubMenuHelper::addEntry(JText::_('Submissions'), 'index.php?option=com_formmaker&amp;task=submits',true );

	JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_formmaker&amp;task=themes' );

	JSubMenuHelper::addEntry(JText::_('Blocked IPs'), 'index.php?option=com_formmaker&amp;task=blocked_ips' );

	JSubMenuHelper::addEntry(JText::_('Form Maker Extensions'),'index.php?option=com_formmaker&task=extensions' );

	JSubMenuHelper::addEntry(JText::_('Featured Extensions'),'index.php?option=com_formmaker&task=featured_plugins' );

	$language = JFactory::getLanguage();

	$language->load('com_formmaker', JPATH_SITE, null, true);	



	

	$n=count($rows);

	$m=count($labels);



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

		document.getElementById('filter_img').src='components/com_formmaker/images/filter_hide.png';

	}

	else

	{

		document.getElementById('fields_filter').style.display="none";

		document.getElementById('filter_img').src='components/com_formmaker/images/filter_show.png';

	}

}

</script>



<style>

.reports

{

	border:1px solid #DEDEDE;

	border-radius:11px;

	background-color:#F0F0F0;

	text-align:center;

	width:100px;

}



.bordered

{

	border-radius:8px

}



pre

{

background:none;

border:0px;

}



#fields_filter th

{

vertical-align:middle !important;

}



input[type="radio"], input[type="checkbox"] {

margin: 5px;

}



select{

margin: 0px !important;

}





</style>

<form action="index.php?option=com_formmaker&task=submits" method="post" name="adminForm" id="adminForm">

    <input type="hidden" name="option" value="com_formmaker">

    <input type="hidden" name="task" value="submits">

<br />

    <table width="100%" style="border-collapse: separate; border-spacing: 2px;">



        <tr >

            <td align="left" width="300"> Select a form:             

                <select name="form_id" id="form_id" onchange="if(document.getElementById('startdate'))remove_all();document.adminForm.submit();">

                    <option value="0" selected="selected"> Select a Form </option>

                    <?php 

            $option='com_formmaker';

            

            if( $forms)

            for($i=0, $n=count($forms); $i < $n ; $i++)

            {

                $form = &$forms[$i];

                if($form_id==$form->id)

                {

                    echo "<option value='".$form->id."' selected='selected'>".$form->title."</option>";

                    $form_title=$form->title;

                }

                else

                    echo "<option value='".$form->id."' >".$form->title."</option>";

            }

        ?>

                    </select>

            </td>

			<?php if(isset($form_id) and $form_id>0):  ?>

			<td class="reports" ><strong>Entries</strong><br /><?php echo $total_entries; ?></td>

			<td class="reports"><strong>Views</strong><br /><?php echo $total_views ?></td>

            <td class="reports"><strong>Conversion Rate</strong><br /><?php  if($total_views) echo round((($total_entries/$total_views)*100),2).'%'; else echo '0%' ?></td>

			<td style="font-size:24px;text-align:center;">

				<?php echo $form_title ?>

			</td>

			

        </tr>

        

        <tr>



            <td  colspan=1>

                <br />

                <input type="hidden" name="hide_label_list" value="<?php  echo $lists['hide_label_list']; ?>" /> 

                <img id="filter_img" src="components/com_formmaker/images/filter_show.png" width="40" style="vertical-align:middle; cursor:pointer" onclick="show_hide_filter()"  title="Search by fields" />

				<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>

				<button class="btn tip hasTooltip" type="button" onclick="remove_all();this.form.submit();" data-original-title="Clear">

				<i class="icon-remove"></i></button>

            </td>

			<td colspan=4>

			<?php if($join_count) echo ($total_entries-$join_count).' of '.$total_entries.' submissions are not shown, as the field you sorted by is missing in those submissions.'; ?>

			</td>

			<td align="right">

                <br /><br />

                <?php if(isset($labels)) echo '<input type="button" class="btn" onclick="toggleChBDiv(true)" value="Add/Remove Columns" style="vertical-align: top;" />'; ?>

				<?php echo $pageNav->getLimitBox(); ?>



			</td>

        </tr>



		<?php else: echo '<td><br /><br /><br /></td></tr>'; endif; ?>

    </table>

    <table class="table table-striped" width="100%">

        <thead>

            <tr>



                <th width="3%"><?php echo '#'; ?></th>



				<th width="4%" class="hidden-phone">

						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />

				</th>

				

				 <?php

				 echo '<th width="4%" class="submitid_fc"';

				 if(!(strpos($lists['hide_label_list'],'@submitid@')===false)) 

				 echo 'style="display:none;"';

				 echo '>';

				 echo JHTML::_('grid.sort', 'Id', 'group_id', @$lists['order_Dir'], @$lists['order'] );

				 echo '</th>';

				 

				 echo '<th width="150" class="submitdate_fc"';

				 if(!(strpos($lists['hide_label_list'],'@submitdate@')===false)) 

				 echo 'style="display:none;"';

				 echo '>';

				 echo JHTML::_('grid.sort', 'Submit date', 'date', @$lists['order_Dir'], @$lists['order'] );

				 echo '</th>';



				 echo '<th width="100" class="submitterip_fc"';

				 if(!(strpos($lists['hide_label_list'],'@submitterip@')===false)) 

				 echo 'style="display:none;"';

				 echo '>';

				 echo JHTML::_('grid.sort', 'Submitter\'s IP Address', 'ip', @$lists['order_Dir'], @$lists['order'] );

				 echo '</th>';

				 

				 echo '<th width="100" class="submitterusername_fc"';

				 if(!(strpos($lists['hide_label_list'],'@submitterusername@')===false)) 

				 echo 'style="display:none;"';

				 echo '>';

				 echo JHTML::_('grid.sort', 'Submitter\'s Username', 'username', @$lists['order_Dir'], @$lists['order'] );

				 echo '</th>';

				 

				 echo '<th width="100" class="submitteremail_fc"';

				 if(!(strpos($lists['hide_label_list'],'@submitteremail@')===false)) 

				 echo 'style="display:none;"';

				 echo '>';

				 echo JHTML::_('grid.sort', 'Submitter\'s Email Address', 'email', @$lists['order_Dir'], @$lists['order'] );

				 echo '</th>';

	$n=count($rows);

	$ispaypal=false;

	

	for($i=0; $i < count($labels) ; $i++)

	{

		if(strpos($lists['hide_label_list'],'@'.$labels_id[$i].'@')===false)  $styleStr='';

		else $styleStr='style="display:none;"';

		

		

			$field_title=$label_titles_copy[$i];

			

	
			echo '<th class="'.$labels_id[$i].'_fc" '.$styleStr.'>'.JHTML::_('grid.sort', $field_title, $labels_id[$i]."_field", @$lists['order_Dir'], @$lists['order'] ).'</th>';

	}

?>



            </tr>

            <tr id="fields_filter" style="display:none; background:#F1F1F1">

                <th width="3%"></th>

                <th width="3%"></th>

				<th width="4%" class="submitid_fc" <?php if(!(strpos($lists['hide_label_list'],'@submitid@')===false)) echo 'style="display:none;"';?> >

				<input type="text" name="id_search" id="id_search" value="<?php echo $lists['id_search'] ?>" onChange="this.form.submit();" style="width:50px"/>

				</th>

								

				<th width="150" class="submitdate_fc" style="text-align:left; <?php if(!(strpos($lists['hide_label_list'],'@submitdate@')===false)) echo 'display:none;';?>" align="center"> 

				<table class="simple_table">

					<tr class="simple_table">

						<td class="simple_table">From:</td>

						<td class="simple_table"><input class="inputbox" type="text" name="startdate" id="startdate" style="width:70px" maxlength="10" value="<?php echo $lists['startdate'];?>" /> </td>

						<td class="simple_table">

						<button class="btn" id="startdate_but"><i class="icon-calendar"></i></button>

						</td>

					</tr>

					<tr class="simple_table">

						<td class="simple_table">To:</td>

						<td class="simple_table"><input class="inputbox" type="text" name="enddate" id="enddate" style="width:70px" maxlength="10" value="<?php echo $lists['enddate'];?>" /> </td>

						<td class="simple_table">

						<button class="btn" id="enddate_but"><i class="icon-calendar"></i></button>

						</td>

					</tr>

				</table>

				

				</th>

				

				

				

				

				<th width="100"class="submitterip_fc"  <?php if(!(strpos($lists['hide_label_list'],'@submitterip@')===false)) echo 'style="display:none;"';?>>

                 <input type="text" name="ip_search" id="ip_search" value="<?php echo $lists['ip_search'] ?>" onChange="this.form.submit();" style="width:150px"/>

				</th>

				<th width="100"class="submitterusername_fc"  <?php if(!(strpos($lists['hide_label_list'],'@submitterusername@')===false)) echo 'style="display:none;"';?>>

                 <input type="text" name="username_search" id="username_search" value="<?php echo $lists['username_search'] ?>" onChange="this.form.submit();" style="width:150px"/>

				</th>

				<th width="100"class="submitteremail_fc"  <?php if(!(strpos($lists['hide_label_list'],'@submitteremail@')===false)) echo 'style="display:none;"';?>>

                 <input type="text" name="useremail_search" id="useremail_search" value="<?php echo $lists['useremail_search'] ?>" onChange="this.form.submit();" style="width:150px"/>

				</th>

				<?php				 

                    $n=count($rows);

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







						switch($sorted_labels_type[$i])

						{

							case 'type_mark_map': echo '<th class="'.$labels_id[$i].'_fc" '.$styleStr.'>'.'</th>'; break;

							
							default : 			  echo '<th class="'.$labels_id[$i].'_fc" '.$styleStr.'>'.'<input name="'.$form_id.'_'.$labels_id[$i].'_search" id="'.$form_id.'_'.$labels_id[$i].'_search" type="text" value="'.$lists[$form_id.'_'.$labels_id[$i].'_search'].'"  onChange="this.form.submit();" >'.'</th>'; break;			

						

						}

						

						

                 }

                ?>

            </tr>

        </thead>

        <tfoot>

            <tr>

                <td colspan="100"> <?php echo $pageNav->getListFooter(); ?>



				</td>

            </tr>

        </tfoot>



        <?php

    $k = 0;

	$m=count($labels);

	$l=0;

	

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



		$username = $user_id->username;

		$useremail= $user_id->email;



		$checked 	= JHTML::_('grid.id', $www, $group_id_s[$www]);

		$link="index.php?option=com_formmaker&task=edit_submit&cid[]=".$f->group_id;

		?>



        <tr class="<?php echo "row$k"; ?>">



              <td align="center"><?php echo $www+1+$pageNav->limitstart;?></td>



          <td align="center" class="checked_cbs"><?php echo $checked?></td>

		  

<?php



if(strpos($lists['hide_label_list'],'@submitid@')===false)

{

	if($user->authorise('core.edit.submits', 'com_formmaker'))

		echo '<td align="center" class="submitid_fc"><a href="'.$link.'" >'.$f->group_id.'</a></td>';

	else

		echo '<td align="center" class="submitid_fc">'.$f->group_id.'</td>';

}

else

{ 

	if($user->authorise('core.edit.submits', 'com_formmaker'))

		echo '<td align="center" class="submitid_fc" style="display:none;"><a href="'.$link.'" >'.$f->group_id.'</a></td>';

	else

		echo '<td align="center" class="submitid_fc" style="display:none;">'.$f->group_id.'</td>';

}





if(strpos($lists['hide_label_list'],'@submitdate@')===false)

{

	if($user->authorise('core.edit.submits', 'com_formmaker'))

		echo '<td align="center" class="submitdate_fc"><a href="'.$link.'" >'.$date.'</a></td>';

	else

		echo '<td align="center" class="submitdate_fc">'.$date.'</td>';



}

else 

{

	if($user->authorise('core.edit.submits', 'com_formmaker'))

		echo '<td align="center" class="submitdate_fc" style="display:none;"><a href="'.$link.'" >'.$date.'</a></td>'; 

	else

		echo '<td align="center" class="submitdate_fc" style="display:none;">'.$date.'</td>'; 

}



if(strpos($lists['hide_label_list'],'@submitterip@')===false)

echo '<td align="center" class="submitterip_fc"><a class="modal"  href="index.php?option=com_formmaker&task=show_ip_info&ip='.$ip.'&tmpl=component" rel="{handler: \'iframe\', size: {x:400, y: 300}}">'.$ip.'</a></td>';

else 

echo '<td align="center" class="submitterip_fc" style="display:none;"><a class="modal"  href="index.php?option=com_formmaker&task=show_ip_info&ip='.$ip.'&tmpl=component" rel="{handler: \'iframe\', size: {x:400, y: 300}}">'.$ip.'</a></td>';



if(strpos($lists['hide_label_list'],'@submitterusername@')===false)

echo '<td align="center" class="submitterusername_fc">'.$username.'</td>';

else 

echo '<td align="center" class="submitterusername_fc" style="display:none;">'.$username.'</td>';





if(strpos($lists['hide_label_list'],'@submitteremail@')===false)

echo '<td align="center" class="submitteremail_fc">'.$useremail.'</td>';

else 

echo '<td align="center" class="submitteremail_fc" style="display:none;">'.$useremail.'</td>';





		$ttt=count($temp);

		for($h=0; $h < $m ; $h++)

		{		

			$not_label=true;

			for($g=0; $g < $ttt ; $g++)

			{			

				$t = $temp[$g];

				if(strpos($lists['hide_label_list'],'@'.$labels_id[$h].'@')===false)  $styleStr='';

				else $styleStr='style="display:none;"';

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

						if(strpos($t->element_value,"@@@")>-1 || $t->element_value=="@@@" || $t->element_value=="@@@@@@@@@")

						{

							echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'><pre style="font-family:inherit">'.str_replace("@@@"," ",$t->element_value).'</pre></td>';

						}

						else

						if($t->element_value=="::" || $t->element_value==":" || $t->element_value=="--")

						{

							echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'><pre style="font-family:inherit">'.str_replace(array(":","-"),"",$t->element_value).'</pre></td>';

						}						

					else

						if(strpos($t->element_value,"***matrix***"))

						{	

						

						echo  '<td align="center" class="'.$labels_id[$h].'_fc" '.$styleStr.'><a class="modal"  href="index.php?option=com_formmaker&task=show_matrix&matrix_params='.$t->element_value.'&tmpl=component" rel="{handler: \'iframe\', size: {x:650, y: 450}}">'.'Show Matrix'.'</a></td>';

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

		

		



?>

        </tr>



        <?php





		$k = 1 - $k;



	}



	?>



    </table>



		<?php

			$db = JFactory::getDBO();

			



			

//////////////////////////////////////////////////////////////////////////////STATS

//////////////////////////////////////////////////////////////////////////////STATS

//////////////////////////////////////////////////////////////////////////////STATS

//////////////////////////////////////////////////////////////////////////////STATS



$is_stats=false;



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

	

	

	<h1>Stats</h1>

	

	<table class="admintable" cellpadding=3 cellspacing=3 >

		<tr valign="top">

			<td class="key" style="vertical-align: middle;">

				<label> <?php echo JText::_( 'Select a Field' ); ?>: </label>

			</td>

			<td>

				<select id="stat_id">

				<option value="">Select a Field</option>;

				<?php 

					foreach($sorted_labels_type as $key => $label_type)

					{

						if($label_type=="type_checkbox" || $label_type=="type_radio" || $label_type=="type_own_select" || $label_type=="type_country" || $label_type=="type_paypal_select" || $label_type=="type_paypal_radio" || $label_type=="type_paypal_checkbox" || $label_type=="type_paypal_shipping")

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

				From: <input class="inputbox" type="text" name="startstats" id="startstats" size="10" maxlength="10" style="width:70px; margin:0px" />

					<button class="btn" id="startstats_but"><i class="icon-calendar"></i></button>

				To: <input class="inputbox" type="text" name="endstats" id="endstats" size="10" maxlength="10" style="width:70px; margin:0px" />

					<button class="btn" id="endstats_but"><i class="icon-calendar"></i></button>

			</td>

		</tr>

		<tr valign="top">

			<td class="key" style="vertical-align: middle;" colspan="2">

			<input type="button" class="btn tip hasTooltip" onclick="show_stats()" style="vertical-align:middle; cursor:pointer" value="Show">

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

		jQuery('#div_stats').html('<div id="saving"><div id="saving_text">Loading</div><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>');

		

		if(jQuery('#stat_id').val()!="")

			jQuery('#div_stats').load('index.php?option=com_formmaker&task=show_stats&form_id=<?php echo $form_id;?>&id='+jQuery('#stat_id').val()+'&from='+jQuery('#startstats').val()+'&to='+jQuery('#endstats').val()+"&format=row");

		else

			jQuery('#div_stats').html("Please select the field!")



	}

	</script>

	<?php

	

}

//////////////////////////////////////////////////////////////////////////////STATS

//////////////////////////////////////////////////////////////////////////////STATS



	?>







	

	

    <input type="hidden" name="boxchecked" value="0">



    <input type="hidden" name="filter_order2" value="<?php echo $lists['order']; ?>" />



    <input type="hidden" name="filter_order_Dir2" value="<?php echo $lists['order_Dir']; ?>" />



</form>

<?php 

if(isset($labels))

{

?>

<div id="sbox-overlay" style="z-index: 65555; position: fixed; top: 0px; left: 0px; visibility: visible; zoom: 1; background-color:#000000; opacity: 0.7; filter: alpha(opacity=70); display:none;" onclick="toggleChBDiv(false)"></div>

<div style="background-color:#FFFFFF; width: 350px; height: 350px; overflow-y: scroll; padding: 20px; position: fixed; top: 100px;display:none; border:2px solid #AAAAAA;  z-index:65556" id="ChBDiv">



<form action="#">

    <p style="font-weight:bold; font-size:18px;margin-top: 0px;">

    Select Columns

    </p>



    <input type="checkbox" <?php if($lists['hide_label_list']==='') echo 'checked="checked"' ?> onclick="clickLabChBAll(this)" id="ChBAll" />All</br>



	<?php 

    

        foreach($templabels as $key => $curlabel)

        {

            if(strpos($lists['hide_label_list'],'@'.$curlabel.'@')===false)

            echo '<input type="checkbox" checked="checked" onclick="clickLabChB(\''.$curlabel.'\', this)" />'.$label_titles[$key].'<br />';

            else

            echo '<input type="checkbox" onclick="clickLabChB(\''.$curlabel.'\', this)" />'.$label_titles[$key].'<br />';

        }

  

    

   

    ?>

    <br />

    <div style="text-align:center;">

        <input type="button" onclick="toggleChBDiv(false);" value="Done"  class="btn" /> 

    </div>

</form>

</div>



<?php } ?>





<script>

<?php if($ka_fielderov_search){?> 

document.getElementById('fields_filter').style.display='';

document.getElementById('filter_img').src='components/com_formmaker/images/filter_hide.png';

	<?php

 }?>

 

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



<?php





}



public static function show_stats(&$choices){



	$colors=array('#2CBADE','#FE6400');

	$choices_labels=array();

	$choices_count=array();

	$all=count($choices);

	$unanswered=0;	

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

	?><table  class="table  table-striped" width="100%">

		<thead>

			<tr>

				<th width="20%">Choices</th>

				<th>Percentage</th>

				<th width="10%">Count</th>

			</tr>

		</thead>

		<tbody>

	<?php 

	foreach($choices_labels as $key => $choices_label)

	{

	?>

		<tr class="row<?php echo ($key%2); ?>">

			<td><?php echo str_replace("***br***",'<br>', $choices_label)?></td>

			<td><div class="bordered" style="width:<?php echo ($choices_count[$key]/($all-$unanswered))*100; ?>%; height:18px; background-color:<?php echo $colors[$key % 2]; ?>"></td>

			<td><?php echo $choices_count[$key]?></td>

		</tr>

	<?php 

	}

	

	if($unanswered){

	?>

	<tr>

	<td colspan="2" align="right">Unanswered</th>

	<td><strong><?php echo $unanswered;?></strong></th>

	</tr>



	<?php	

	}

	?>

	<tr>

	<td colspan="2" align="right"><strong>Total</strong></th>

	<td><strong><?php echo $all;?></strong></th>

	</tr>

	</tbody>

	</table>

<?php





}





public static function show_blocked_ips(&$rows, &$pageNav, &$lists){



JSubMenuHelper::addEntry(JText::_('Forms'), 'index.php?option=com_formmaker&amp;task=forms');

JSubMenuHelper::addEntry(JText::_('Submissions'), 'index.php?option=com_formmaker&amp;task=submits' );

JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_formmaker&amp;task=themes' );

JSubMenuHelper::addEntry(JText::_('Blocked IPs'), 'index.php?option=com_formmaker&amp;task=blocked_ips', true  );

JSubMenuHelper::addEntry(JText::_('Form Maker Extensions'),'index.php?option=com_formmaker&task=extensions' );

JSubMenuHelper::addEntry(JText::_('Featured Extensions'),'index.php?option=com_formmaker&task=featured_plugins' );



	JHTML::_('behavior.tooltip');	

	JHtml::_('formbehavior.chosen', 'select');

	$user = JFactory::getUser();

	?>

<script>

Joomla.tableOrdering= function ( order, dir, task )  {

    var form = document.adminForm;

    form.filter_order_ips.value     = order;

    form.filter_order_Dir_ips.value = dir;

    submitform( task );

}



function SelectAll(obj) { obj.focus(); obj.select(); } </script>

<form action="index.php?option=com_formmaker" method="post" name="adminForm" id="adminForm">



    <table width="100%">



        <tr>



            <tr>



            <td align="left" width="100%">

				<input type="text" name="search_ip" id="search_ip" value="<?php echo $lists['search_ip'];?>" class="text_area"  placeholder="Search ip" style="margin:0px" />

				<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>

				<button class="btn tip hasTooltip" type="button" onclick="document.id('search_ip').value='';this.form.submit();" data-original-title="Clear">

				<i class="icon-remove"></i></button>

			

				<div class="btn-group pull-right hidden-phone">

					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>

					<?php echo $pageNav->getLimitBox(); ?>

				</div>





            </td>



        </tr>



			

			

        </tr>



    </table>



	

	   <table class="table table-striped" width="100%" >



        <thead>



            <tr>



                <th width="4%"><?php echo '#'; ?></th>

                <th width="6%"><?php echo  JHTML::_('grid.sort', 'Id', 'Id', @$lists['order_Dir'], @$lists['order'] ); ?></th>



				<th width="4%" class="hidden-phone">

						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />

				</th>



               <th width="70%"><?php echo JHTML::_('grid.sort', 'Ip', 'ip', @$lists['order_Dir'], @$lists['order'] ); ?></th>





            </tr>



        </thead>



        <tfoot>



            <tr>



                <td colspan="6"> <?php echo $pageNav->getListFooter(); ?> </td>



            </tr>



        </tfoot>



        <?php



	



    $k = 0;



	for($i=0, $n=count($rows); $i < $n ; $i++)



	{



		$row = &$rows[$i];



		$checked 	= JHTML::_('grid.id', $i, $row->id);



		// prepare link for id column



			$link 		= JRoute::_( 'index.php?option=com_formmaker&task=edit_blocked_ips&cid[]='. $row->id );



		?>



        <tr class="<?php echo "row$k"; ?>">



                      <td align="center"><?php echo $i+1?></td>

                      <td align="center"><?php echo $row->id?></td>



          <td align="center"><?php echo $checked?></td>

			<?php if($user->authorise('core.edit', 'com_formmaker')): ?>

            <td align="center"><a href="<?php echo $link; ?>"><?php echo $row->ip?></a></td>

			<?php else: ?>

			<td align="center"><?php echo $row->ip?></td>

			<?php endif; ?>            

        </tr>



        <?php



		$k = 1 - $k;



	}



	?>



    </table>



    <input type="hidden" name="option" value="com_formmaker">

    <input type="hidden" name="task" value="blocked_ips">

    <input type="hidden" name="boxchecked" value="0"  >

    <input type="hidden" name="filter_order_ips" value="<?php echo $lists['order']; ?>" />

    <input type="hidden" name="filter_order_Dir_ips" value="<?php echo $lists['order_Dir']; ?>" />    

	

</form>



<?php

}



public static function featured_plugins(){



JSubMenuHelper::addEntry(JText::_('Forms'), 'index.php?option=com_formmaker&amp;task=forms');

JSubMenuHelper::addEntry(JText::_('Submissions'), 'index.php?option=com_formmaker&amp;task=submits' );

JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_formmaker&amp;task=themes' );

JSubMenuHelper::addEntry(JText::_('Blocked IPs'), 'index.php?option=com_formmaker&amp;task=blocked_ips'  );

JSubMenuHelper::addEntry(JText::_('Form Maker Extensions'),'index.php?option=com_formmaker&task=extensions' );

JSubMenuHelper::addEntry(JText::_('Featured Extensions'),'index.php?option=com_formmaker&task=featured_plugins', true );



$document = JFactory::getDocument();

$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/featured_plugins.css');

	?>

<div id="main_featured_plugins_page">

		<table align="center" width="90%" style="margin-top: 0px;border-bottom: rgb(111, 111, 111) solid 2px;">

			<tr>

				<td colspan="2" style="height: 70px;"><h3 style="margin: 0px;font-family:Segoe UI;padding-bottom: 15px;color: rgb(111, 111, 111); font-size:18pt;">Featured Extensions</h3></td>

				<td  align="right" style="font-size:16px;">

        </td>

			</tr>

		</table>

		<form method="post">

			<ul id="featured-plugins-list">
			<li class="gallerywd">
				<div class="product">
					<div class="title">
						<strong class="heading">Gallery WD</strong>
						<p>Joomla Gallery WD Extension</p>
					</div>
				</div>
				<div class="description">
						<p>Joomla! Gallery WD is an advanced gallery extension with responsive design and layout.</p>
						<a target="_blank" href="http://web-dorado.com/products/joomla-gallery.html" class="download">Download</a>
				</div>
			</li>
			
			<li class="sliderwd">
				<div class="product">
					<div class="title">
						<strong class="heading">Slider WD</strong>
						<p>Joomla Slider WD Extension</p>
					</div>
				</div>
				<div class="description">
						<p>Slider is a highly-customizable extension for adding sliders to your Joomla website. </p>
						<a target="_blank" href="http://web-dorado.com/products/joomla-slider-wd.html" class="download">Download</a>
				</div>
			</li>
		 
			<li class="ecommerce">
					<div class="product">
						<div class="title">
							<strong class="heading">Ecommerce WD</strong>
							<p>Joomla Ecommerce extension</p>
						</div>
					</div>
					<div class="description">
							<p>Ecommerce WD is an innovative Joomla extension for creating online stores and ecommerce pages in your Joomla website.</p>
							<a target="_blank" href="http://web-dorado.com/products/joomla-ecommerce.html" class="download">Download</a>
					</div>
				</li>
		 

			 <li class="catalog">

					<div class="product">

						<div class="title">

							<strong class="heading">Spider Catalog</strong>

							<p>Joomla product catalog extension</p>

						</div>

					</div>

					<div class="description">

							<p>Spider Catalog for Joomla! is a convenient tool for organizing the products represented on your website into catalogs.</p>

							<a target="_blank" href="http://web-dorado.com/products/joomla-catalog.html" class="download">Download</a>

					</div>

				</li>

				<li class="spider-calendar">

					<div class="product">

						<div class="title">

							<strong class="heading">Spider Calendar</strong>

							<p>Joomla event calendar extension</p>

						</div>

					</div>

					<div class="description">

							<p>Spider Calendar is a highly configurable Joomla extension which allows you to have multiple organized events in a calendar.</p>

							<a target="_blank" href="http://web-dorado.com/products/joomla-calendar.html" class="download">Download</a>

					</div>

				</li>

      

				<li class="player">

					<div class="product">

						<div class="title">

							<strong class="heading">Spider Video Player</strong>

							<p>Joomla video player extension</p>

						</div>

					</div>

					<div class="description">

							<p>Spider Video Player for Joomla! is a video player extension that allows you to easily add videos to your website with the possibility of organizing videos into playlists and choosing a preferred layout for the player</p>

							<a target="_blank" href="http://web-dorado.com/products/joomla-player.html" class="download">Download</a>

					</div>

				</li>

				<li class="contacts">

					<div class="product">

						<div class="title">

							<strong class="heading">Spider Contacts</strong>

							<p>Joomla staff list extension</p>

						</div>

					</div>

					<div class="description">

							<p>Spider Contact is a Joomla! extension with large and affecting capabilities which helps you to display information about the group of people more intelligible, effective and convenient.</p>

							<a target="_blank" href="http://web-dorado.com/products/joomla-contacts.html" class="download">Download</a>

					</div>

				</li>

				 <li class="facebook">

					<div class="product">

						<div class="title">

							<strong class="heading">Spider Facebook</strong>

							<p>Joomla Facebook extension</p>

						</div>

					</div>

					<div class="description">

							<p>Spider Facebook is a Facebook integration tool for Joomla!,which contains all the available Facebook social plugins and widgets for your website.</p>

							<a target="_blank" href="http://web-dorado.com/products/joomla-facebook.html" class="download">Download</a>

					</div>

				</li>

               

			   <li class="faq">

					<div class="product">

						<div class="title">

							<strong class="heading">Spider FAQ</strong>

							<p>Joomla FAQ extension</p>

						</div>

					</div>

					<div class="description">

							<p>Spider FAQ is a highly customizable Joomla! extension for creating FAQs easily and fast.</p>

							<a target="_blank" href="http://web-dorado.com/products/joomla-faq-extension.html" class="download">Download</a>

					</div>

				</li>

                <li class="zoom">

					<div class="product">

						<div class="title">

							<strong class="heading">Zoom</strong>

							<p>Joomla text Zoom extension</p>

						</div>

					</div>

					<div class="description">

							<p>Zoom module enables site users to resize the predefined areas of the web site.</p>

							<a target="_blank" href="http://web-dorado.com/products/joomla-zoom.html" class="download">Download</a>

					</div>

				</li>

				<li class="flash-calendar">

					<div class="product">

						<div class="title">

							<strong class="heading">Spider Flash Calendar</strong>

							<p>Joomla flash calendar extension</p>

						</div>

					</div>

					<div class="description">

							<p>Spider FC is a highly configurable Joomla Flash extension which allows you to have an event calendar with flash effects.</p>

							<a target="_blank" href="http://web-dorado.com/products/joomla-event-calendar.html" class="download">Download</a>

					</div>

				</li>

      

				<li class="twitter-tools">

                 <div class="product">

                         <div class="title">

                                 <strong class="heading">Joomla Twitter Tools</strong>

                                 <p>Joomla Twitter extension</p>

                         </div>

                 </div>

                 <div class="description">

                                 <p>Twitter Tools is a Joomla Twitter integration extension, which provides fast access to a wide range of Twitter social plugins and widgets without leaving your website.</p>

                                 <a target="_blank" href="http://web-dorado.com/products/joomla-twitter-tools.html" class="download">Download</a>

                 </div>

         </li>

			<li class="audio-player">

                 <div class="product">

                         <div class="title">

                                 <strong class="heading">Spider Audio Player</strong>

                                 <p>Joomla audio player extension</p>

                         </div>

                 </div>

                 <div class="description">

                                 <p>Spider Audio Player for Joomla! is a audio player extension that allows you to easily add tracks to your website with the possibility of organizing tracks into playlists and choosing a preferred layout for the player.</p>

                                 <a target="_blank" href="http://web-dorado.com/products/joomla-audio-player.html" class="download">Download</a>

                 </div>

			</li>

			<li class="folder-menu">

                 <div class="product">

                         <div class="title">

                                 <strong class="heading">Folder Menu</strong>

                                 <p>Joomla vertical menu</p>

                         </div>

                 </div>

                 <div class="description">

                                 <p>Folder Menu is a flash dynamic menu module for your Joomla! website,

									designed to meet your needs and preferences.</p>

                                 <a target="_blank" href="http://web-dorado.com/products/joomla-menu-vertical-horizontal-drop-down.html" class="download">Download</a>

                 </div>

			</li>

			<li class="random-article">

                 <div class="product">

                         <div class="title">

                                 <strong class="heading">Spider Random Article</strong>

                                 <p>Joomla Random article module</p>

                         </div>

                 </div>

                 <div class="description">

                                 <p>Joomla Random Article is a useful tool for displaying(in a module) articles from a selected category in random order.</p>

                                 <a target="_blank" href="http://web-dorado.com/products/joomla-random.html" class="download">Download</a>

                 </div>

			</li>

			</ul>

		</form>

	</div >



<?php

}



public static function extensions(){



JSubMenuHelper::addEntry(JText::_('Forms'), 'index.php?option=com_formmaker&amp;task=forms');

JSubMenuHelper::addEntry(JText::_('Submissions'), 'index.php?option=com_formmaker&amp;task=submits' );

JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_formmaker&amp;task=themes' );

JSubMenuHelper::addEntry(JText::_('Blocked IPs'), 'index.php?option=com_formmaker&amp;task=blocked_ips'  );

JSubMenuHelper::addEntry(JText::_('Form Maker Extensions'),'index.php?option=com_formmaker&task=extensions', true);

JSubMenuHelper::addEntry(JText::_('Featured Extensions'),'index.php?option=com_formmaker&task=featured_plugins' );



	?>

<div id="main_featured_plugins_page">



		<div style="width: 120px; height: 220px; border: 1px solid #cccccc; margin: 30px; padding: 15px; margin-left: 65px;

	text-align: center;">

		<span style="font-weight: bold; font-size: 18px;">Form Maker Extensions</span>

		<br>

		<br>

		<a target="_blank" href="http://web-dorado.com/products/joomla-form/export-import.html" style="text-decoration: none;"><img src="components/com_formmaker/images/export.import.png" alt="" >

		<span style="color:#14679D;font-weight: bold;font-size: 14px;">Form Maker Export/Import</span></a>

	</div>

	</div >



<?php

}



public static function show(&$rows, &$pageNav, &$lists){



JSubMenuHelper::addEntry(JText::_('Forms'), 'index.php?option=com_formmaker&amp;task=forms', true );

JSubMenuHelper::addEntry(JText::_('Submissions'), 'index.php?option=com_formmaker&amp;task=submits' );

JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_formmaker&amp;task=themes' );

JSubMenuHelper::addEntry(JText::_('Blocked IPs'), 'index.php?option=com_formmaker&amp;task=blocked_ips' );

JSubMenuHelper::addEntry(JText::_('Form Maker Extensions'),'index.php?option=com_formmaker&task=extensions' );

JSubMenuHelper::addEntry(JText::_('Featured Extensions'),'index.php?option=com_formmaker&task=featured_plugins' );



JHtml::_('behavior.tooltip');

JHtml::_('behavior.formvalidation');

JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();



	?>

<script>



Joomla.tableOrdering= function ( order, dir, task )  {

    var form = document.adminForm;

    form.filter_order.value     = order;

    form.filter_order_Dir.value = dir;

    submitform( task );

}





 function SelectAll(obj) { obj.focus(); obj.select(); } 

 </script>

<form action="index.php?option=com_formmaker" method="post" name="adminForm" id="adminForm">



    <table width="100%">



        <tr>



            <td align="left" width="100%">

                <input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" placeholder="Search title" style="margin:0px" />



				<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>

				<button class="btn tip hasTooltip" type="button" onclick="document.id('search').value='';this.form.submit();" data-original-title="Clear">

				<i class="icon-remove"></i></button>

				<div class="btn-group pull-right hidden-phone">

					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>

					<?php echo $pageNav->getLimitBox(); ?>

				</div>





            </td>



        </tr>



    </table>



    <table class="table table-striped" width="100%" >



        <thead>



            <tr>



                <th width="4%"><?php echo '#'; ?></th>

                <th width="6%"><?php echo  JHTML::_('grid.sort', 'Id', 'Id', @$lists['order_Dir'], @$lists['order'] ); ?></th>



				<th width="4%" class="hidden-phone">

						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />

				</th>



                <th width="34%"><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>



                <th width="35%"><?php echo JHTML::_('grid.sort', 'Email to Send Submissions to', 'mail', @$lists['order_Dir'], @$lists['order'] ); ?></th>

				

                <th width="15%"><?php echo 'Plugin Code<br/>(Copy to article)'; ?></th>



            </tr>



        </thead>



        <tfoot>



            <tr>



                <td colspan="6"> <?php echo $pageNav->getListFooter(); ?> </td>



            </tr>



        </tfoot>



        <?php



	



    $k = 0;



	for($i=0, $n=count($rows); $i < $n ; $i++)



	{



		$row = &$rows[$i];



		$checked 	= JHTML::_('grid.id', $i, $row->id);



		$published 	= JHTML::_('grid.published', $row, $i); 





		// prepare link for id column

		

		$link 		= JRoute::_( 'index.php?option=com_formmaker&task=edit&cid[]='. $row->id );



		?>



        <tr class="<?php echo "row$k"; ?>">



                      <td align="center"><?php echo $i+1?></td>

                      <td align="center"><?php echo $row->id?></td>



          <td align="center"><?php echo $checked?></td>

			<?php if($user->authorise('core.edit', 'com_formmaker') || ($user->authorise('core.edit.own', 'com_formmaker') && $row->created_by == $user->id)): ?>

            <td align="center"><a href="<?php echo $link; ?>"><?php echo $row->title?></a></td>

			<?php else: ?>

			<td align="center"><?php echo $row->title?></td>

			<?php endif; ?>

            <td align="center"><?php echo $row->mail?></td>

            <td align="center"><input type="text" readonly="readonly" value="{loadformmaker <?php echo $row->id?>}" onclick="SelectAll(this)" width="130"></td>



        </tr>



        <?php



		$k = 1 - $k;



	}



	?>



    </table>



    <input type="hidden" name="option" value="com_formmaker">

    <input type="hidden" name="task" value="forms">

    <input type="hidden" name="boxchecked" value="0"  >

    <input type="text" name="filter_order"  id="filter_order" value="<?php echo $lists['order']; ?>"  class="text_area" style="display:none"/>

    <input type="text" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" class="text_area" style="display:none" />





</form>



<?php

}



public static function edit(&$row, &$labels){



	JRequest::setVar( 'hidemainmenu', 1 );

	

	$user = JFactory::getUser();

	$document = JFactory::getDocument();



	$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';

	



	$document->addScript($cmpnt_js_path.'/if_gmap.js');

	

	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
			$document->addScript('https://maps.google.com/maps/api/js?sensor=false');
		else	
			$document->addScript('http://maps.google.com/maps/api/js?sensor=false');
	$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/style.css?version=1.2');

	$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/js/jquery-ui-spinner.css');

	

	$is_editor=false;

	

	$plugin = JPluginHelper::getPlugin('editors', 'tinymce');

	if (isset($plugin->type))

	{ 

		$editor	= JFactory::getEditor('tinymce');

		$is_editor=true;

	}

	



	JHTML::_('behavior.tooltip');	

	JHTML::_('behavior.calendar');

	JHTML::_('behavior.modal');

	?>

<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js'; ?>" type="text/javascript"></script>
<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/jquery-ui.js'; ?>" type="text/javascript"></script>
<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/noconflict.js'; ?>" type="text/javascript"></script>

<script type="text/javascript">

if($)

if(typeof $.noConflict === 'function'){

   $.noConflict();

}



function remove_empty_columns()

{

	wdformjQuery('.wdform_section').each(function() {

			if(wdformjQuery(this).find('.wdform_column').last().prev().html()=='')

			{

			

				if(wdformjQuery(this).children().length>2)

				{

					wdformjQuery(this).find('.wdform_column').last().prev().remove();

					remove_empty_columns();

				}

			}	

		});	

}



function sortable_columns()

{

    wdformjQuery( ".wdform_column" ).sortable({

		connectWith: ".wdform_column",

		cursor: 'move',

		placeholder: "highlight",

		start: function(e,ui){

			wdformjQuery('.wdform_column').each(function() {

				if(wdformjQuery(this).html())

				{

					wdformjQuery(this).append(wdformjQuery('<div class="wdform_empty_row" style="height:80px;"></div>'));

					wdformjQuery( ".wdform_column" ).sortable( "refresh" );

				}

			});			

		},

		update: function(event, ui) {

				wdformjQuery('.wdform_section .wdform_column:last-child').each(function() {

					if(wdformjQuery(this).html())

					{

						wdformjQuery(this).parent().append(wdformjQuery('<div></div>').addClass("wdform_column"));	

						sortable_columns();

					}		

				});

					

				

		},

		stop: function(event, ui) {

			wdformjQuery('.wdform_empty_row').remove();	

			remove_empty_columns();		

		}

    });



}





function all_sortable_events()

{



	wdformjQuery(document).on( "click", ".wdform_row, .wdform_tr_section_break", function() {	var this2=this; setTimeout( function(){			

		if(wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).attr("class")=="wdform_arrows_show")

		{

			wdformjQuery("#wdform_field"+wdformjQuery(this2).attr("wdid")).css({"background-color":"transparent", "border":"none","margin-top":""});

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).removeClass("wdform_arrows_show");

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).addClass("wdform_arrows");

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).hide();

		}

		else

		{

			wdformjQuery(".wdform_arrows_show").addClass("wdform_arrows");

			wdformjQuery(".wdform_arrows").hide();

			wdformjQuery(".wdform_arrows_show").removeClass("wdform_arrows_show");

			wdformjQuery(".wdform_field, .wdform_field_section_break").css("background-color","transparent");

			wdformjQuery(".wdform_field, .wdform_field_section_break").css("border","none");

			wdformjQuery(".wdform_field").css("margin-top","");

			

			if(wdformjQuery("#wdform_field"+wdformjQuery(this2).attr("wdid")).attr("type")=='type_editor')

			wdformjQuery("#wdform_field"+wdformjQuery(this2).attr("wdid")).css("margin-top","-5px");

			

			wdformjQuery("#wdform_field"+wdformjQuery(this2).attr("wdid")).css({"background-color":"rgb(224, 224, 224)","border":"1px solid rgb(213, 213, 213)"});

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).removeClass("wdform_arrows");

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).addClass("wdform_arrows_show");

			wdformjQuery("#wdform_arrows"+wdformjQuery(this2).attr("wdid")).show();

		}



	},300)});



	wdformjQuery(document).on( "hover", ".wdform_tr_section_break", function() {

		wdformjQuery("#wdform_field"+wdformjQuery(this).attr("wdid")).css({"background-color":"rgb(224, 224, 224)"});

	});



	wdformjQuery(document).on( "hover", ".wdform_row", function() {

		wdformjQuery("#wdform_field"+wdformjQuery(this).attr("wdid")).css({"cursor":"move","background-color":"rgb(224, 224, 224)"});

	});



	wdformjQuery(document).on( "mouseleave", ".wdform_row, .wdform_tr_section_break", function() {

		if(wdformjQuery("#wdform_arrows"+wdformjQuery(this).attr("wdid")).attr("class")!="wdform_arrows_show")

		{

			wdformjQuery("#wdform_field"+wdformjQuery(this).attr("wdid")).css({"background-color":"transparent", "border":"none"});

			wdformjQuery("#wdform_arrows"+wdformjQuery(this).attr("wdid")).addClass("wdform_arrows");

		}

	});



}



wdformjQuery(document).on( "dblclick", ".wdform_row, .wdform_tr_section_break", function() {

		edit(wdformjQuery(this).attr("wdid"));

	});



wdformjQuery(function() {



	wdformjQuery('.wdform_section .wdform_column:last-child').each(function() {

			wdformjQuery(this).parent().append(wdformjQuery('<div></div>').addClass("wdform_column"));		

		});

		

	sortable_columns();

	if(<?php echo $row->sortable ?>==1)	

	{

		wdformjQuery( ".wdform_arrows" ).hide();

		all_sortable_events();

	}

	else

		wdformjQuery('.wdform_column').sortable( "disable" );	

	

	});	

	

	

function enable_drag(elem)

{

	if(wdformjQuery('#enable_sortable').prop( 'checked' ))

	{

		wdformjQuery('#enable_sortable').val(1);

		wdformjQuery('.wdform_column').sortable( "enable" );

		wdformjQuery( ".wdform_arrows" ).slideUp(700);

		all_sortable_events();

	}

	else

	{

		wdformjQuery('#enable_sortable').val(0);

		wdformjQuery('.wdform_column').sortable( "disable" );		

		wdformjQuery(".wdform_column").css("border","none");	

		wdformjQuery( ".wdform_row, .wdform_tr_section_break" ).die("click");

		wdformjQuery( ".wdform_row" ).die("hover");

		wdformjQuery( ".wdform_tr_section_break" ).die("hover");

		wdformjQuery( ".wdform_field" ).css("cursor","default");

		wdformjQuery( ".wdform_field, .wdform_field_section_break" ).css("background-color","transparent");

		wdformjQuery( ".wdform_field, .wdform_field_section_break" ).css("border","none");

		wdformjQuery( ".wdform_arrows_show" ).hide();

		wdformjQuery( ".wdform_arrows_show" ).addClass("wdform_arrows");

		wdformjQuery( ".wdform_arrows_show" ).removeClass("wdform_arrows_show");

		wdformjQuery( ".wdform_arrows" ).slideDown(600);	

	}

}





var already_submitted=false;

Joomla.submitbutton= function (pressbutton) 

{



	if(!document.getElementById('araqel'))

	{

		alert('Please wait while page loading');

		return;

	}

	else

		if(document.getElementById('araqel').value=='0')

		{

			alert('Please wait while page loading');

			return;

		}

		

	var form = document.adminForm;



	if (already_submitted) 

	{

		submitform( pressbutton );

		return;

	}

	

	if (pressbutton == 'cancel') 



	{



		submitform( pressbutton );



		return;



	}

	

	if (form.title.value == "")



	{

				alert( "The form must have a title." );	

				return;

	}		



		

	document.getElementById('take').style.display="none";

	document.getElementById('page_bar').style.display="none";

	document.getElementById('saving').style.display="block";

	remove_whitespace(document.getElementById('take'));

	

	wdformjQuery('.wdform_section').each(function() {

		var this2 = this;

		wdformjQuery(this2).find('.wdform_column').each(function() {

			if(!wdformjQuery(this).html() && wdformjQuery(this2).children().length>1)

				wdformjQuery(this).remove();

		});

	});

	tox='';

	form_fields='';



	l_id_array=[<?php echo $labels['id']?>];

	l_label_array=[<?php echo $labels['label']?>];

	l_type_array=[<?php echo $labels['type']?>];

	l_id_removed=[];



	for(x=0; x< l_id_array.length; x++)

	{

		l_id_removed[l_id_array[x]]=true;

	}

		

	for(t=1;t<=form_view_max;t++)

	{

		if(document.getElementById('form_id_tempform_view'+t))

		{

			wdform_page=document.getElementById('form_id_tempform_view'+t);

			remove_whitespace(wdform_page);

			n=wdform_page.childNodes.length-2;	

			for(q=0;q<=n;q++)

			{

				if(!wdform_page.childNodes[q].getAttribute("wdid"))

				{

					wdform_section=wdform_page.childNodes[q];

					for (x=0; x < wdform_section.childNodes.length; x++)

					{

						wdform_column=wdform_section.childNodes[x];

						if(wdform_column.firstChild)

						for (y=0; y < wdform_column.childNodes.length; y++)

						{

							is_in_old=false;

							wdform_row=wdform_column.childNodes[y];

							if(wdform_row.nodeType==3)

								continue;

							wdid=wdform_row.getAttribute("wdid");

							if(!wdid)

								continue;

							l_id=wdid;

							l_label = document.getElementById( wdid+'_element_labelform_id_temp').innerHTML;

							l_label = l_label.replace(/(\r\n|\n|\r)/gm," ");

							wdtype=wdform_row.firstChild.getAttribute('type');



							for(z=0; z< l_id_array.length; z++)

							{

								if(l_id_array[z]==wdid)

								{

									if(l_type_array[z]=="type_address")

									{

										if(document.getElementById(l_id+"_mini_label_street1"))		

											l_id_removed[l_id_array[z]]=false;

							

											

										if(document.getElementById(l_id+"_mini_label_street2"))		

										l_id_removed[parseInt(l_id_array[z])+1]=false;	

									

										

										if(document.getElementById(l_id+"_mini_label_city"))

										l_id_removed[parseInt(l_id_array[z])+2]=false;	

																			

										if(document.getElementById(l_id+"_mini_label_state"))

										l_id_removed[parseInt(l_id_array[z])+3]=false;	

										

										

										if(document.getElementById(l_id+"_mini_label_postal"))

										l_id_removed[parseInt(l_id_array[z])+4]=false;	

										

										

										if(document.getElementById(l_id+"_mini_label_country"))

										l_id_removed[parseInt(l_id_array[z])+5]=false;	

										

										z=z+5;

									}

									else

										l_id_removed[l_id]=false;



								}

							}

							

							if(wdtype=="type_address")

							{

								addr_id=parseInt(wdid);

								id_for_country= addr_id;

								

								if(document.getElementById(id_for_country+"_mini_label_street1"))

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_street1").innerHTML+'#**label**#type_address#****#';addr_id++; 

								if(document.getElementById(id_for_country+"_mini_label_street2"))	

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_street2").innerHTML+'#**label**#type_address#****#';addr_id++; 	

								if(document.getElementById(id_for_country+"_mini_label_city"))	

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_city").innerHTML+'#**label**#type_address#****#';	addr_id++;

								if(document.getElementById(id_for_country+"_mini_label_state"))	

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_state").innerHTML+'#**label**#type_address#****#';	addr_id++;		

								if(document.getElementById(id_for_country+"_mini_label_postal"))

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_postal").innerHTML+'#**label**#type_address#****#';	addr_id++; 

								if(document.getElementById(id_for_country+"_mini_label_country"))

								tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_country").innerHTML+'#**label**#type_address#****#'; 

							}

							else

								tox=tox+wdid+'#**id**#'+l_label+'#**label**#'+wdtype+'#****#';

								

							

							id=wdid;

							form_fields+=wdid+"*:*id*:*";

							form_fields+=wdtype+"*:*type*:*";

							

							

							w_choices=new Array();

							w_choices_value=new Array();							

							w_choices_checked=new Array();

							w_choices_disabled=new Array();

							w_choices_params =new Array();

							w_allow_other_num=0;

							w_property=new Array();	

							w_property_type=new Array();	

							w_property_values=new Array();

							w_choices_price=new Array();

							

							if(document.getElementById(id+'_element_labelform_id_temp').innerHTML)

								w_field_label=document.getElementById(id+'_element_labelform_id_temp').innerHTML.replace(/(\r\n|\n|\r)/gm," ");

							else

								w_field_label='';

							

							if(document.getElementById(id+'_label_sectionform_id_temp'))

							if(document.getElementById(id+'_label_sectionform_id_temp').style.display=="block")

								w_field_label_pos="top";

							else

								w_field_label_pos="left";

														

							if(document.getElementById(id+"_elementform_id_temp"))

							{

								s=document.getElementById(id+"_elementform_id_temp").style.width;

								 w_size=s.substring(0,s.length-2);

							}

							

							if(document.getElementById(id+"_label_sectionform_id_temp"))

							{

								s=document.getElementById(id+"_label_sectionform_id_temp").style.width;

								w_field_label_size=s.substring(0,s.length-2);

							}

							

							if(document.getElementById(id+"_requiredform_id_temp"))

								w_required=document.getElementById(id+"_requiredform_id_temp").value;

								

							if(document.getElementById(id+"_uniqueform_id_temp"))

								w_unique=document.getElementById(id+"_uniqueform_id_temp").value;

								

							if(document.getElementById(id+'_label_sectionform_id_temp'))

							{

								w_class=document.getElementById(id+'_label_sectionform_id_temp').getAttribute("class");

								if(!w_class)

									w_class="";

							}

								

							gen_form_fields();

							wdform_row.innerHTML="%"+id+" - "+l_label+"%";

							

						}

					}

				}

			

				else

				{

						id=wdform_page.childNodes[q].getAttribute("wdid");

						w_editor=document.getElementById(id+"_element_sectionform_id_temp").innerHTML;

						

						form_fields+=id+"*:*id*:*";

						form_fields+="type_section_break"+"*:*type*:*";

												

						form_fields+="custom_"+id+"*:*w_field_label*:*";

						form_fields+=w_editor+"*:*w_editor*:*";

						form_fields+="*:*new_field*:*";

						wdform_page.childNodes[q].innerHTML="%"+id+" - "+"custom_"+id+"%";

						

					



				}



			}

		}	

	}



	document.getElementById('label_order_current').value=tox;

	

	for(x=0; x< l_id_array.length; x++)

	{

		if(l_id_removed[l_id_array[x]])

			tox=tox+l_id_array[x]+'#**id**#'+l_label_array[x]+'#**label**#'+l_type_array[x]+'#****#';

	}

	

	

	document.getElementById('label_order').value=tox;

	document.getElementById('form_fields').value=form_fields;

	

	

	refresh_()

	document.getElementById('pagination').value=document.getElementById('pages').getAttribute("type");

	document.getElementById('show_title').value=document.getElementById('pages').getAttribute("show_title");

	document.getElementById('show_numbers').value=document.getElementById('pages').getAttribute("show_numbers");

	

		already_submitted=true;

		submitform( pressbutton );

}



function remove_whitespace(node)

{

var ttt;

	for (ttt=0; ttt < node.childNodes.length; ttt++)

	{

        if( node.childNodes[ttt] && node.childNodes[ttt].nodeType == '3' && !/\S/.test(  node.childNodes[ttt].nodeValue ))

		{

			

			node.removeChild(node.childNodes[ttt]);

			 ttt--;

		}

		else

		{

			if(node.childNodes[ttt].childNodes.length)

				remove_whitespace(node.childNodes[ttt]);

		}

	}

	return

}



function refresh_()

{

				

	document.getElementById('counter').value=gen;

	

	for(i=1; i<=form_view_max; i++)

		if(document.getElementById('form_id_tempform_view'+i))

		{

			if(document.getElementById('page_next_'+i))

				document.getElementById('page_next_'+i).removeAttribute('src');

			if(document.getElementById('page_previous_'+i))

				document.getElementById('page_previous_'+i).removeAttribute('src');

			document.getElementById('form_id_tempform_view'+i).parentNode.removeChild(document.getElementById('form_id_tempform_view_img'+i));

			document.getElementById('form_id_tempform_view'+i).removeAttribute('style');

		}

		

	document.getElementById('form_front').value=document.getElementById('take').innerHTML;

}









	gen=<?php echo $row->counter; ?>;//add main form  id

    function enable()

	{

	alltypes=Array('customHTML','text','checkbox','radio','time_and_date','select','file_upload','captcha','map','button','page_break','section_break','paypal','survey');

	for(x=0; x<14;x++)

	{

		document.getElementById('img_'+alltypes[x]).src="components/com_formmaker/images/"+alltypes[x]+".png";

	}

	



		if(document.getElementById('formMakerDiv').style.display=='block'){wdformjQuery('#formMakerDiv').slideToggle(200);}else{wdformjQuery('#formMakerDiv').slideToggle(400);}

		

		if(document.getElementById('formMakerDiv').offsetWidth)

			document.getElementById('formMakerDiv1').style.width	=(document.getElementById('formMakerDiv').offsetWidth - 60)+'px';

		if(document.getElementById('formMakerDiv1').style.display=='block'){wdformjQuery('#formMakerDiv1').slideToggle(200);}else{wdformjQuery('#formMakerDiv1').slideToggle(400);}

		document.getElementById('when_edit').style.display		='none';

	}



    function enable2()

	{

	alltypes=Array('customHTML','text','checkbox','radio','time_and_date','select','file_upload','captcha','map','button','page_break','section_break','paypal','survey');

	for(x=0; x<14;x++)

	{

		document.getElementById('img_'+alltypes[x]).src="components/com_formmaker/images/"+alltypes[x]+".png";

	}

	



		if(document.getElementById('formMakerDiv').style.display=='block'){wdformjQuery('#formMakerDiv').slideToggle(200);}else{wdformjQuery('#formMakerDiv').slideToggle(400);}

		

		if(document.getElementById('formMakerDiv').offsetWidth)

			document.getElementById('formMakerDiv1').style.width	=(document.getElementById('formMakerDiv').offsetWidth - 60)+'px';

	

    if(document.getElementById('formMakerDiv1').style.display=='block'){wdformjQuery('#formMakerDiv1').slideToggle(200);}else{wdformjQuery('#formMakerDiv1').slideToggle(400);}

	document.getElementById('when_edit').style.display		='block';

		if(document.getElementById('field_types').offsetWidth)

			document.getElementById('when_edit').style.width	=document.getElementById('field_types').offsetWidth+'px';

		

		if(document.getElementById('field_types').offsetHeight)

			document.getElementById('when_edit').style.height	=document.getElementById('field_types').offsetHeight+'px';

		

		//document.getElementById('when_edit').style.position='none';

		

	}

	

    </script>



<style>

#take_temp .element_toolbar, #take_temp .page_toolbar, #take_temp .captcha_img, #take_temp .wdform_stars

{

display:none;

}



#when_edit

{

position:absolute;

background-color:#666;

z-index:101;

display:none;

width:100%;

height:100%;

opacity: 0.7;

filter: alpha(opacity = 70);

}



#formMakerDiv

{

position:fixed;

background-color:#666;

z-index:100;

display:none;

left:0;

top:0;

width:100%;

height:100%;

opacity: 0.7;

filter: alpha(opacity = 70);

}

#formMakerDiv1

{

position:fixed;

z-index:100;

background-color:transparent;

top:0;

left:0;

display:none;

margin-left:30px;

margin-top:35px;

}



input[type="radio"], input[type="checkbox"] {

margin: 5px;

}



.pull-left

{

float:none !important;

}



.modal-body

{

max-height:100%;

}



img

{

max-width:none;

}

.wdform_date

{

margin:0px !important;

}



.formmaker_table input

{

border-radius: 3px;

padding: 2px;

}



.formmaker_table

{

border-radius:8px;

border:6px #00aeef solid;

background-color:#00aeef;

height:120px;

}



.formMakerDiv1_table

{

border:6px #00aeef solid;

background-color:#FFF;

border-radius:8px;

}



label

{

display:inline;

}

</style>



<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div  class="formmaker_table" width="100%" >

<div style="float:left; text-align:center">

 	</br>

   <img src="components/com_formmaker/images/FormMaker.png" />

	</br>

	</br>

	   <img src="components/com_formmaker/images/logo.png" />





</div>



<div style="float:right">



    <span style="font-size:16.76pt; font-family:tahoma; color:#FFFFFF; vertical-align:middle;">Form title:&nbsp;&nbsp;</span>



    <input id="title" name="title" style="width:151px; height:19px; border:none; font-size:11px; " value="<?php echo $row->title; ?>" />

 <br/>

	<a href="#" onclick="Joomla.submitbutton('form_options_temp')" style="cursor:pointer;margin:10px; float:right; color:#fff; font-size:20px"><img src="components/com_formmaker/images/formoptions.png" /></a>    

   <br/>

	<img src="components/com_formmaker/images/addanewfield.png" onclick="enable(); Enable()" style="cursor:pointer;margin:10px; float:right" />



</div>

	

  



</div>



<div id="formMakerDiv" onclick="close_window()"></div>  

<div id="formMakerDiv1"  align="center">

    

    

<table border="0" width="100%" cellpadding="0" cellspacing="0" height="100%" class="formMakerDiv1_table">

  <tr>

    <td style="padding:0px">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">

        <tr valign="top">

         <td width="15%" height="100%" style="border-right:dotted black 1px;" id="field_types">

            <div id="when_edit" style="display:none"></div>

			<table border="0" cellpadding="0" cellspacing="3" width="100%" style="border-collapse: separate; border-spacing: 3px;">
				<tr>
					<td align="center" onClick="addRow('customHTML')" class="field_buttons" id="table_editor"><img src="components/com_formmaker/images/customHTML.png" style="margin:5px" id="img_customHTML"/></td>
					<td align="center" onClick="addRow('text')" class="field_buttons" id="table_text"><img src="components/com_formmaker/images/text.png" style="margin:5px" id="img_text"/></td>
				</tr>
				<tr>             
					<td align="center" onClick="addRow('checkbox')" class="field_buttons" id="table_checkbox"><img src="components/com_formmaker/images/checkbox.png" style="margin:5px" id="img_checkbox"/></td>
					<td align="center" onClick="addRow('radio')" class="field_buttons" id="table_radio"><img src="components/com_formmaker/images/radio.png" style="margin:5px" id="img_radio"/></td>
				</tr>
				<tr>
					<td align="center" onClick="addRow('survey')" class="field_buttons" id="table_survey"><img src="components/com_formmaker/images/survey.png" style="margin:5px" id="img_survey"/></td>           
					<td align="center" onClick="addRow('time_and_date')" class="field_buttons" id="table_time_and_date"><img src="components/com_formmaker/images/time_and_date.png" style="margin:5px" id="img_time_and_date"/></td>
			   </tr>
				<tr>
					<td align="center" onClick="addRow('select')" class="field_buttons" id="table_select"><img src="components/com_formmaker/images/select.png" style="margin:5px" id="img_select"/></td>
					<td align="center" onClick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons" id="table_file_upload" style="background:#727171 !important;"><img src="components/com_formmaker/images/file_upload.png" style="margin:5px" id="img_file_upload"/></td>
				</tr>
				<tr>
					<td align="center" onClick="addRow('section_break')" class="field_buttons" id="table_section_break"><img src="components/com_formmaker/images/section_break.png" style="margin:5px" id="img_section_break"/></td>
					<td align="center" onClick="addRow('page_break')" class="field_buttons" id="table_page_break"><img src="components/com_formmaker/images/page_break.png" style="margin:5px" id="img_page_break"/></td>  
				</tr>
				<tr>
					<td align="center" onClick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons" id="table_map" style="background:#727171 !important;"><img src="components/com_formmaker/images/map.png" style="margin:5px" id="img_map" /></td>  
					<td align="center" onClick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" id="table_paypal" class="field_buttons" style="background:#727171 !important;"><img src="components/com_formmaker/images/paypal.png" style="margin:5px" id="img_paypal" /></td>       
			   </tr>
				<tr>
					<td align="center" onClick="addRow('captcha')" class="field_buttons" id="table_captcha"><img src="components/com_formmaker/images/captcha.png" style="margin:5px" id="img_captcha"/></td>
					<td align="center" onClick="addRow('button')" id="table_button" class="field_buttons" ><img src="components/com_formmaker/images/button.png" style="margin:5px" id="img_button"/></td>			
				</tr>
            </table>
        
         </td>

         <td width="35%" height="100%" align="left"><div id="edit_table" style="padding:0px; overflow-y:scroll; height:535px" ></div></td>



		 <td align="center" valign="top" style="background:url(components/com_formmaker/images/border2.png) repeat-y;">&nbsp;</td>

         <td style="padding:15px">

         <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">

         

            <tr>

                <td align="right"><input type="radio" value="end" name="el_pos" checked="checked" id="pos_end" onclick="Disable()"/>

                  At The End

                  <input type="radio" value="begin" name="el_pos" id="pos_begin" onclick="Disable()"/>

                  At The Beginning

                  <input type="radio" value="before" name="el_pos" id="pos_before" onclick="Enable()"/>

                  Before

                  <select style="width:100px; margin-left:5px" id="sel_el_pos" onclick="change_before()" disabled="disabled">

                  </select>

                  <img alt="ADD" title="add" style="cursor:pointer; vertical-align:middle; margin:5px" src="components/com_formmaker/images/save.png" onClick="add(0, false)"/>

                  <img alt="CANCEL" title="cancel"  style=" cursor:pointer; vertical-align:middle; margin:5px" src="components/com_formmaker/images/cancel_but.png" onClick="close_window()"/>

				

                	<hr style=" margin-bottom:10px" />

                  </td>

              </tr>

              

              <tr height="100%" valign="top">

                <td  id="show_table"></td>

              </tr>

              

            </table>

         </td>

        </tr>

      </table>

    </td>

  </tr>

</table>



<input type="hidden" id="old" />

<input type="hidden" id="old_selected" />

<input type="hidden" id="element_type" />

<input type="hidden" id="editing_id" />

<div id="main_editor" style="position:absolute; display:none; z-index:140;"><?php if($is_editor) echo $editor->display('editor','','440px','350px','40','6');

else

{

?>

<textarea name="editor" id="editor" cols="40" rows="6" style="width: 440px; height: 350px; " class="mce_editable" aria-hidden="true"></textarea>

<?php



}

 ?></div>

 </div>

 

 <?php if(!$is_editor)

?>

<iframe id="tinymce" style="display:none"></iframe>

<br />

	<div style="font-size:16px; margin-left:5px; color: red; font-weight:bold;">You can use drag and drop to move the fields up/down for the change of the order and left/right for creating columns within the form.</div>

	<br/><div style="margin-left:5px;"><label for="enable_sortable" style="font-size:16px; font-weight:bold;">Enable Drag & Drop</label>

	<input type="checkbox" name="sortable" id="enable_sortable" value="<?php echo $row->sortable; ?>" onclick="enable_drag(this)" <?php if($row->sortable==1) echo 'checked="checked"'; ?> /></div>

	

	<br />



    <fieldset>



    <legend>



    <h2 style="color:#00aeef">Form</h2>



    </legend>



        <?php

		

    echo '<style>'.self::first_css.'</style>';



?>



<div id="saving" style="display:none">

	<div id="saving_text">Saving</div>

	<div id="fadingBarsG">

	<div id="fadingBarsG_1" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_2" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_3" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_4" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_5" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_6" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_7" class="fadingBarsG">

	</div>

	<div id="fadingBarsG_8" class="fadingBarsG">

	</div>

	</div>

</div>





<div style="margin:8px; display:table; width:100%" id="page_bar"><div id="page_navigation" style="display:table-row"><div align="center" id="pages" show_title="<?php echo $row->show_title; ?>" show_numbers="<?php echo $row->show_numbers; ?>" type="<?php echo $row->pagination; ?>" style="display:table-cell;  width:90%"></div><div align="left" id="edit_page_navigation" style="display:table-cell; vertical-align: middle;"></div></div></div>



    <div id="take"><?php

	

	    echo $row->form_front;

		

	 ?></div>



    </fieldset>



    <input type="hidden" name="form_front" id="form_front">

	<input type="hidden" name="form_fields" id="form_fields"/>

	  

    <input type="hidden" name="pagination" id="pagination" />

    <input type="hidden" name="show_title" id="show_title" />

    <input type="hidden" name="show_numbers" id="show_numbers" />

	

    <input type="hidden" name="public_key" id="public_key" />

    <input type="hidden" name="private_key" id="private_key" />

    <input type="hidden" name="recaptcha_theme" id="recaptcha_theme" />

	<input type="hidden" name="created_by" value="<?php echo ($row->created_by == 0 ? $user->id : $row->created_by); ?>" />

	

	<input type="hidden" id="label_order_current" name="label_order_current" value="<?php echo $row->label_order_current;?>" />

    <input type="hidden" id="label_order" name="label_order" value="<?php echo $row->label_order;?>" />

    <input type="hidden" name="counter" id="counter" value="<?php echo $row->counter;?>">



<script type="text/javascript">



function formOnload()

{

	

//enable maps

for(t=0; t<<?php echo $row->counter;?>; t++)

	if(document.getElementById(t+"_typeform_id_temp"))

	{

		if(document.getElementById(t+"_typeform_id_temp").value=="type_map" || document.getElementById(t+"_typeform_id_temp").value=="type_mark_map")

		{

			if_gmap_init(t);

			for(q=0; q<20; q++)

				if(document.getElementById(t+"_elementform_id_temp").getAttribute("long"+q))

				{

				

					w_long=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("long"+q));

					w_lat=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("lat"+q));

					w_info=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("info"+q));

					add_marker_on_map(t,q, w_long, w_lat, w_info, false);

				}

		}

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_date")

				Calendar.setup({

						inputField: t+"_elementform_id_temp",

						ifFormat: document.getElementById(t+"_buttonform_id_temp").getAttribute('format'),

						button: t+"_buttonform_id_temp",

						align: "Tl",

						singleClick: true,

						firstDay: 0

						});

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_name")

		{

				var myu = t;

				wdformjQuery(document).ready(function() {	



				wdformjQuery("#"+myu+"_mini_label_first").click(function() {		

			

				if (wdformjQuery(this).children('input').length == 0) {	



					var first = "<input type='text' id='first' class='first' style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

						wdformjQuery(this).html(first);							

						wdformjQuery("input.first").focus();			

						wdformjQuery("input.first").blur(function() {	

					

					var id_for_blur = document.getElementById('first').parentNode.id.split('_');

					var value = wdformjQuery(this).val();			

				wdformjQuery("#"+id_for_blur[0]+"_mini_label_first").text(value);		

				});	

			}	

			});	    

				

				wdformjQuery("label#"+myu+"_mini_label_last").click(function() {	

			if (wdformjQuery(this).children('input').length == 0) {	

			

				var last = "<input type='text' id='last' class='last'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(last);			

					wdformjQuery("input.last").focus();					

					wdformjQuery("input.last").blur(function() {	

					var id_for_blur = document.getElementById('last').parentNode.id.split('_');			

					var value = wdformjQuery(this).val();			

					

					wdformjQuery("#"+id_for_blur[0]+"_mini_label_last").text(value);	

				});	

				 

			}	

			});

			

				wdformjQuery("label#"+myu+"_mini_label_title").click(function() {	

			if (wdformjQuery(this).children('input').length == 0) {		

				var title_ = "<input type='text' id='title_' class='title_'  style='outline:none; border:none; background:none; width:50px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(title_);			

					wdformjQuery("input.title_").focus();					

					wdformjQuery("input.title_").blur(function() {	

					var id_for_blur = document.getElementById('title_').parentNode.id.split('_');			

					var value = wdformjQuery(this).val();			

					

					wdformjQuery("#"+id_for_blur[0]+"_mini_label_title").text(value);	

				});	

			}	

			});





			wdformjQuery("label#"+myu+"_mini_label_middle").click(function() {	

			if (wdformjQuery(this).children('input').length == 0) {		

				var middle = "<input type='text' id='middle' class='middle'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(middle);			

					wdformjQuery("input.middle").focus();					

					wdformjQuery("input.middle").blur(function() {	

					var id_for_blur = document.getElementById('middle').parentNode.id.split('_');			

					var value = wdformjQuery(this).val();			

					

					wdformjQuery("#"+id_for_blur[0]+"_mini_label_middle").text(value);	

				});	

			}	

			});

			

			});		

		}						

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_phone")

		{

				var myu = t;

			  

			wdformjQuery(document).ready(function() {	

			wdformjQuery("label#"+myu+"_mini_label_area_code").click(function() {		

			if (wdformjQuery(this).children('input').length == 0) {		



				var area_code = "<input type='text' id='area_code' class='area_code' style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";		



				wdformjQuery(this).html(area_code);		

				wdformjQuery("input.area_code").focus();		

				wdformjQuery("input.area_code").blur(function() {	

				var id_for_blur = document.getElementById('area_code').parentNode.id.split('_');

				var value = wdformjQuery(this).val();			

				wdformjQuery("#"+id_for_blur[0]+"_mini_label_area_code").text(value);		

				});		

			}	

			});	



			

			wdformjQuery("label#"+myu+"_mini_label_phone_number").click(function() {		



			if (wdformjQuery(this).children('input').length == 0) {			

				var phone_number = "<input type='text' id='phone_number' class='phone_number'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";						



				wdformjQuery(this).html(phone_number);					



				wdformjQuery("input.phone_number").focus();			

				wdformjQuery("input.phone_number").blur(function() {		

				var id_for_blur = document.getElementById('phone_number').parentNode.id.split('_');

				var value = wdformjQuery(this).val();			

				wdformjQuery("#"+id_for_blur[0]+"_mini_label_phone_number").text(value);		

				});	

			}	

			});

			

			});	

		}						

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_date_fields")

		{

				var myu = t;

			  

			wdformjQuery(document).ready(function() {	

			wdformjQuery("label#"+myu+"_day_label").click(function() {		

				if (wdformjQuery(this).children('input').length == 0) {				

					var day = "<input type='text' id='day' class='day' style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

						wdformjQuery(this).html(day);							

						wdformjQuery("input.day").focus();			

						wdformjQuery("input.day").blur(function() {	

					var id_for_blur = document.getElementById('day').parentNode.id.split('_');

					var value = wdformjQuery(this).val();			



				wdformjQuery("#"+id_for_blur[0]+"_day_label").text(value);		

				});	

			}	

			});		





			wdformjQuery("label#"+myu+"_month_label").click(function() {	

			if (wdformjQuery(this).children('input').length == 0) {		

				var month = "<input type='text' id='month' class='month' style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(month);			

					wdformjQuery("input.month").focus();					

					wdformjQuery("input.month").blur(function() {	

					var id_for_blur = document.getElementById('month').parentNode.id.split('_');			

					var value = wdformjQuery(this).val();			

					

					wdformjQuery("#"+id_for_blur[0]+"_month_label").text(value);	

				});	

			}	

			});

			

				wdformjQuery("label#"+myu+"_year_label").click(function() {	

			if (wdformjQuery(this).children('input').length == 0) {		

				var year = "<input type='text' id='year' class='year' style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(year);			

					wdformjQuery("input.year").focus();					

					wdformjQuery("input.year").blur(function() {	

				var id_for_blur = document.getElementById('year').parentNode.id.split('_');				

					var value = wdformjQuery(this).val();			

					

					wdformjQuery("#"+id_for_blur[0]+"_year_label").text(value);	

				});	

			}	

			});

			

			});	



	

		}						

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_time")

		{

			var myu = t;

      

			wdformjQuery(document).ready(function() {	

				wdformjQuery("label#"+myu+"_mini_label_hh").click(function() {		

					if (wdformjQuery(this).children('input').length == 0) {				

						var hh = "<input type='text' id='hh' class='hh' style='outline:none; border:none; background:none; width:40px;' value=\""+wdformjQuery(this).text()+"\">";	

							wdformjQuery(this).html(hh);							

							wdformjQuery("input.hh").focus();			

							wdformjQuery("input.hh").blur(function() {	

							var id_for_blur = document.getElementById('hh').parentNode.id.split('_');	

						var value = wdformjQuery(this).val();			





					wdformjQuery("#"+id_for_blur[0]+"_mini_label_hh").text(value);		

					});	

				}	

				});		





				wdformjQuery("label#"+myu+"_mini_label_mm").click(function() {	

				if (wdformjQuery(this).children('input').length == 0) {		

					var mm = "<input type='text' id='mm' class='mm' style='outline:none; border:none; background:none; width:40px;' value=\""+wdformjQuery(this).text()+"\">";	

						wdformjQuery(this).html(mm);			

						wdformjQuery("input.mm").focus();					

						wdformjQuery("input.mm").blur(function() {

						var id_for_blur = document.getElementById('mm').parentNode.id.split('_');				

						var value = wdformjQuery(this).val();			

						

						wdformjQuery("#"+id_for_blur[0]+"_mini_label_mm").text(value);	

					});	

				}	

				});

				

					wdformjQuery("label#"+myu+"_mini_label_ss").click(function() {	

				if (wdformjQuery(this).children('input').length == 0) {		

					var ss = "<input type='text' id='ss' class='ss' style='outline:none; border:none; background:none; width:40px;' value=\""+wdformjQuery(this).text()+"\">";	

						wdformjQuery(this).html(ss);			

						wdformjQuery("input.ss").focus();					

						wdformjQuery("input.ss").blur(function() {

			   var id_for_blur = document.getElementById('ss').parentNode.id.split('_');				

						var value = wdformjQuery(this).val();			

						

						wdformjQuery("#"+id_for_blur[0]+"_mini_label_ss").text(value);	

					});	

				}	

				});

				

					wdformjQuery("label#"+myu+"_mini_label_am_pm").click(function() {		

					if (wdformjQuery(this).children('input').length == 0) {				

						var am_pm = "<input type='text' id='am_pm' class='am_pm' style='outline:none; border:none; background:none; width:40px;' value=\""+wdformjQuery(this).text()+"\">";	

							wdformjQuery(this).html(am_pm);							

							wdformjQuery("input.am_pm").focus();			

							wdformjQuery("input.am_pm").blur(function() {	

						var id_for_blur = document.getElementById('am_pm').parentNode.id.split('_');	

						var value = wdformjQuery(this).val();			



					wdformjQuery("#"+id_for_blur[0]+"_mini_label_am_pm").text(value);		

					});	

				}	

				});	

				});

		

	}	

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_paypal_price")

		{

				var myu = t;

				wdformjQuery(document).ready(function() {	



				wdformjQuery("#"+myu+"_mini_label_dollars").click(function() {		

			

				if (wdformjQuery(this).children('input').length == 0) {	



					var dollars = "<input type='text' id='dollars' class='dollars' style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

						wdformjQuery(this).html(dollars);							

						wdformjQuery("input.dollars").focus();			

						wdformjQuery("input.dollars").blur(function() {	

					

					var id_for_blur = document.getElementById('dollars').parentNode.id.split('_');

					var value = wdformjQuery(this).val();			

				wdformjQuery("#"+id_for_blur[0]+"_mini_label_dollars").text(value);		

				});	

			}	

			});	    

				

				wdformjQuery("label#"+myu+"_mini_label_cents").click(function() {	

			if (wdformjQuery(this).children('input').length == 0) {	

			

				var cents = "<input type='text' id='cents' class='cents'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(cents);			

					wdformjQuery("input.cents").focus();					

					wdformjQuery("input.cents").blur(function() {	

					var id_for_blur = document.getElementById('cents').parentNode.id.split('_');			

					var value = wdformjQuery(this).val();			

					

					wdformjQuery("#"+id_for_blur[0]+"_mini_label_cents").text(value);	

				});	

				 

			}	

			});

			

			

			

			});		

		}	

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_address")

		{

			var myu = t;

       

			wdformjQuery(document).ready(function() {		

			wdformjQuery("label#"+myu+"_mini_label_street1").click(function() {			



				if (wdformjQuery(this).children('input').length == 0) {				

				var street1 = "<input type='text' id='street1' class='street1' style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";

				wdformjQuery(this).html(street1);					

				wdformjQuery("input.street1").focus();		

				wdformjQuery("input.street1").blur(function() {	

				var id_for_blur = document.getElementById('street1').parentNode.id.split('_');

				var value = wdformjQuery(this).val();			

				wdformjQuery("#"+id_for_blur[0]+"_mini_label_street1").text(value);		

				});		

				}	

				});		

			

			wdformjQuery("label#"+myu+"_mini_label_street2").click(function() {		

			if (wdformjQuery(this).children('input').length == 0) {		

			var street2 = "<input type='text' id='street2' class='street2'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";

			wdformjQuery(this).html(street2);					

			wdformjQuery("input.street2").focus();		

			wdformjQuery("input.street2").blur(function() {	

			var id_for_blur = document.getElementById('street2').parentNode.id.split('_');

			var value = wdformjQuery(this).val();			

			wdformjQuery("#"+id_for_blur[0]+"_mini_label_street2").text(value);		

			});		

			}	

			});	

			

			

			wdformjQuery("label#"+myu+"_mini_label_city").click(function() {	

				if (wdformjQuery(this).children('input').length == 0) {	

				var city = "<input type='text' id='city' class='city'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";

				wdformjQuery(this).html(city);			

				wdformjQuery("input.city").focus();				

				wdformjQuery("input.city").blur(function() {	

				var id_for_blur = document.getElementById('city').parentNode.id.split('_');		

				var value = wdformjQuery(this).val();		

				wdformjQuery("#"+id_for_blur[0]+"_mini_label_city").text(value);		

			});		

			}	

			});	

			

			wdformjQuery("label#"+myu+"_mini_label_state").click(function() {		

				if (wdformjQuery(this).children('input').length == 0) {	

				var state = "<input type='text' id='state' class='state'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(state);		

					wdformjQuery("input.state").focus();		

					wdformjQuery("input.state").blur(function() {	

				var id_for_blur = document.getElementById('state').parentNode.id.split('_');					

				var value = wdformjQuery(this).val();			

			wdformjQuery("#"+id_for_blur[0]+"_mini_label_state").text(value);	

			});	

			}

			});		



			wdformjQuery("label#"+myu+"_mini_label_postal").click(function() {		

			if (wdformjQuery(this).children('input').length == 0) {			

			var postal = "<input type='text' id='postal' class='postal'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";

			wdformjQuery(this).html(postal);			

			wdformjQuery("input.postal").focus();			

			wdformjQuery("input.postal").blur(function() {

			var id_for_blur = document.getElementById('postal').parentNode.id.split('_');	

			var value = wdformjQuery(this).val();		

			wdformjQuery("#"+id_for_blur[0]+"_mini_label_postal").text(value);		

			});	

			}

			});	

			

			

			wdformjQuery("label#"+myu+"_mini_label_country").click(function() {		

				if (wdformjQuery(this).children('input').length == 0) {		

					var country = "<input type='country' id='country' class='country'  style='outline:none; border:none; background:none; width:130px;' value=\""+wdformjQuery(this).text()+"\">";

					wdformjQuery(this).html(country);		

					wdformjQuery("input.country").focus();	

					wdformjQuery("input.country").blur(function() {	

					var id_for_blur = document.getElementById('country').parentNode.id.split('_');				

					var value = wdformjQuery(this).val();			

					wdformjQuery("#"+id_for_blur[0]+"_mini_label_country").text(value);			

					});	

				}	

			});

			});	



		}						

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_scale_rating")

		{

				var myu = t;

				wdformjQuery(document).ready(function() {	



				wdformjQuery("#"+myu+"_mini_label_worst").click(function() {		

			

				if (wdformjQuery(this).children('input').length == 0) {	



					var worst = "<input type='text' id='worst' class='worst' size='6' style='outline:none; border:none; background:none; font-size:11px; width:100px;' value=\""+wdformjQuery(this).text()+"\">";	

						wdformjQuery(this).html(worst);							

						wdformjQuery("input.worst").focus();			

						wdformjQuery("input.worst").blur(function() {	

					

					var id_for_blur = document.getElementById('worst').parentNode.id.split('_');

					var value = wdformjQuery(this).val();			

				wdformjQuery("#"+id_for_blur[0]+"_mini_label_worst").text(value);		

				});	

			}	

			});	    

				

				wdformjQuery("label#"+myu+"_mini_label_best").click(function() {	

			if (wdformjQuery(this).children('input').length == 0) {	

			

				var best = "<input type='text' id='best' class='best' style='outline:none; border:none; background:none; font-size:11px; width:100px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(best);			

					wdformjQuery("input.best").focus();					

					wdformjQuery("input.best").blur(function() {	

					var id_for_blur = document.getElementById('best').parentNode.id.split('_');			

					var value = wdformjQuery(this).val();			

					

					wdformjQuery("#"+id_for_blur[0]+"_mini_label_best").text(value);	

				});	

				 

			}	

			});

			

			

			

			});		

		}			 

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_spinner")

		{

				var spinner_value = document.getElementById(t+"_elementform_id_temp").value;

				var spinner_min_value = document.getElementById(t+"_min_valueform_id_temp").value;

				var spinner_max_value = document.getElementById(t+"_max_valueform_id_temp").value;

				var spinner_step = document.getElementById(t+"_stepform_id_temp").value;

								  

				

								wdformjQuery("#"+t+"_elementform_id_temp")[0].spin = null;

								

								spinner = wdformjQuery( "#"+t+"_elementform_id_temp" ).spinner();

								spinner.spinner( "value", spinner_value );

								wdformjQuery( "#"+t+"_elementform_id_temp" ).spinner({ min: spinner_min_value});    

								wdformjQuery( "#"+t+"_elementform_id_temp" ).spinner({ max: spinner_max_value});

								wdformjQuery( "#"+t+"_elementform_id_temp" ).spinner({ step: spinner_step});

								

	

		}

			else

			if(document.getElementById(t+"_typeform_id_temp").value=="type_slider")	

			{

 

				var slider_value = document.getElementById(t+"_slider_valueform_id_temp").value;

				var slider_min_value = document.getElementById(t+"_slider_min_valueform_id_temp").value;

				var slider_max_value = document.getElementById(t+"_slider_max_valueform_id_temp").value;

				

				var slider_element_value = document.getElementById( t+"_element_valueform_id_temp" );

				var slider_value_save = document.getElementById( t+"_slider_valueform_id_temp" );

				

				 wdformjQuery("#"+t+"_elementform_id_temp")[0].slide = null;	

			

					wdformjQuery(function() {

				wdformjQuery( "#"+t+"_elementform_id_temp").slider({

				range: "min",

				value: eval(slider_value),

				min: eval(slider_min_value),

				max: eval(slider_max_value),

				slide: function( event, ui ) {	

					slider_element_value.innerHTML = "" + ui.value ;

					slider_value_save.value = "" + ui.value; 



				}

				});

	

	

			});	

		

				

		}

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_range")

		{

				var spinner_value0 = document.getElementById(t+"_elementform_id_temp0").value;

				var spinner_step = document.getElementById(t+"_range_stepform_id_temp").value;

								  

						

					wdformjQuery("#"+t+"_elementform_id_temp0")[0].spin = null;

					wdformjQuery("#"+t+"_elementform_id_temp1")[0].spin = null;

								

							

					spinner0 = wdformjQuery( "#"+t+"_elementform_id_temp0" ).spinner();

					spinner0.spinner( "value", spinner_value0 );

					wdformjQuery( "#"+t+"_elementform_id_temp0" ).spinner({ step: spinner_step});

							

							

							

				var spinner_value1 = document.getElementById(t+"_elementform_id_temp1").value;

								

							

							spinner1 = wdformjQuery( "#"+t+"_elementform_id_temp1" ).spinner();

							spinner1.spinner( "value", spinner_value1 );

							wdformjQuery( "#"+t+"_elementform_id_temp1" ).spinner({ step: spinner_step});

							

							

						var myu = t;

			wdformjQuery(document).ready(function() {	



			wdformjQuery("#"+myu+"_mini_label_from").click(function() {		

		

			if (wdformjQuery(this).children('input').length == 0) {	



				var from = "<input type='text' id='from' class='from' style='outline:none; border:none; background:none; font-size:11px; width:100px;' value=\""+wdformjQuery(this).text()+"\">";	

					wdformjQuery(this).html(from);							

					wdformjQuery("input.from").focus();			

					wdformjQuery("input.from").blur(function() {	

				

				var id_for_blur = document.getElementById('from').parentNode.id.split('_');

				var value = wdformjQuery(this).val();			

			wdformjQuery("#"+id_for_blur[0]+"_mini_label_from").text(value);		

			});	

		}	

		});	    

			

			wdformjQuery("label#"+myu+"_mini_label_to").click(function() {	

		if (wdformjQuery(this).children('input').length == 0) {	

		

			var to = "<input type='text' id='to' class='to' size='6' style='outline:none; border:none; background:none; font-size:11px; width:100px;' value=\""+wdformjQuery(this).text()+"\">";	

				wdformjQuery(this).html(to);			

				wdformjQuery("input.to").focus();					

				wdformjQuery("input.to").blur(function() {	

				var id_for_blur = document.getElementById('to').parentNode.id.split('_');			

				var value = wdformjQuery(this).val();			

				

				wdformjQuery("#"+id_for_blur[0]+"_mini_label_to").text(value);	

			});	

			 

		}	

		});

		

		

	

	});						

	

		}			 

	



	}

	

	remove_whitespace(document.getElementById('take'));

	

	form_view=1;

	form_view_count=0;

	for(i=1; i<=20; i++)

	{

		if(document.getElementById('form_id_tempform_view'+i))

		{

			form_view_count++;

			form_view_max=i;

			



		tbody_img=document.createElement('div');

			tbody_img.setAttribute('id','form_id_tempform_view_img'+i);

			tbody_img.style.cssText = "float:right";

			

		tr_img=document.createElement('div');

			

		var	img=document.createElement('img');

			img.setAttribute('src','components/com_formmaker/images/minus.png');

			img.setAttribute('title','Show or hide the page');

			img.setAttribute("class", "page_toolbar");

			img.setAttribute('id','show_page_img_'+i);

			img.setAttribute('onClick','show_or_hide("'+i+'")');

			img.setAttribute("onmouseover", 'chnage_icons_src(this,"minus")');

			img.setAttribute("onmouseout", 'chnage_icons_src(this,"minus")');

			img.style.cssText = "margin:5px 5px 5px 0;";



			

		var img_X = document.createElement("img");

			img_X.setAttribute("src", "components/com_formmaker/images/page_delete.png");

			img_X.setAttribute('title','Delete the page');

			img_X.setAttribute("class", "page_toolbar");

			img_X.setAttribute("onclick", 'remove_page("'+i+'")');

			img_X.setAttribute("onmouseover", 'chnage_icons_src(this,"page_delete")');

			img_X.setAttribute("onmouseout", 'chnage_icons_src(this,"page_delete")');

			img_X.style.cssText = "margin:5px 5px 5px 0;";

			

		var img_X_all = document.createElement("img");

			img_X_all.setAttribute("src", "components/com_formmaker/images/page_delete_all.png");

			img_X_all.setAttribute('title','Delete the page with fields');

			img_X_all.setAttribute("class", "page_toolbar");

			img_X_all.setAttribute("onclick", 'remove_page_all("'+i+'")');

			img_X_all.setAttribute("onmouseover", 'chnage_icons_src(this,"page_delete_all")');

			img_X_all.setAttribute("onmouseout", 'chnage_icons_src(this,"page_delete_all")');

			img_X_all.style.cssText = "margin:5px 5px 5px 0;";

			

		var img_EDIT = document.createElement("img");

			img_EDIT.setAttribute("src", "components/com_formmaker/images/page_edit.png");

			img_EDIT.setAttribute('title','Edit the page');

			img_EDIT.setAttribute("class", "page_toolbar");

			img_EDIT.setAttribute("onclick", 'edit_page_break("'+i+'")');

			img_EDIT.setAttribute("onmouseover", 'chnage_icons_src(this,"page_edit")');

			img_EDIT.setAttribute("onmouseout", 'chnage_icons_src(this,"page_edit")');

			img_EDIT.style.cssText = "margin:5px 5px 5px 0;";

					

		tr_img.appendChild(img);

		tr_img.appendChild(img_X);

		tr_img.appendChild(img_X_all);

		tr_img.appendChild(img_EDIT);

		tbody_img.appendChild(tr_img);

			

		document.getElementById('form_id_tempform_view'+i).parentNode.appendChild(tbody_img);

		}

	}

	

	if(form_view_count>1)

	{

		for(i=1; i<=form_view_max; i++)

		{

			if(document.getElementById('form_id_tempform_view'+i))

			{

				first_form_view=i;

				break;

			}

		}

		form_view=form_view_max;

		need_enable=false;

		generate_page_nav(first_form_view);

		

	var img_EDIT = document.createElement("img");

			img_EDIT.setAttribute("src", "components/com_formmaker/images/edit.png");

			img_EDIT.style.cssText = "margin-left:40px; cursor:pointer";

			img_EDIT.setAttribute("onclick", 'el_page_navigation()');

			

	var td_EDIT = document.getElementById("edit_page_navigation");

			td_EDIT.appendChild(img_EDIT);

	

	document.getElementById('page_navigation').appendChild(td_EDIT);



			

	}



	document.getElementById('araqel').value=1;



}



function formAddToOnload()

{ 



	if(formOldFunctionOnLoad){ formOldFunctionOnLoad(); }

	formOnload();

}



function formLoadBody()

{

	formOldFunctionOnLoad = window.onload;

	window.onload = formAddToOnload;

}



var formOldFunctionOnLoad = null;



formLoadBody();

</script>





	<script src="<?php echo  $cmpnt_js_path ?>/formmaker_div1.js?version=1.2" type="text/javascript" style=""></script>


    <input type="hidden" name="option" value="com_formmaker" />


    <input type="hidden" name="id" value="<?php echo $row->id?>" />



    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />



    <input type="hidden" name="task" value="" />

    <input type="hidden" id="araqel" value="0" />



</form>



<?php		



       }	





public static function edit_old(&$row, &$labels){



JRequest::setVar( 'hidemainmenu', 1 );

		

		$document = JFactory::getDocument();



		$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';

		

		

		$document->addScript($cmpnt_js_path.'/if_gmap.js');

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
			$document->addScript('https://maps.google.com/maps/api/js?sensor=false');
		else	
			$document->addScript('http://maps.google.com/maps/api/js?sensor=false');
		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/css/style.css?version=1.2');

		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_formmaker/js/jquery-ui-spinner.css');

	

		

		$is_editor=false;

		

		$plugin = JPluginHelper::getPlugin('editors', 'tinymce');

		if (isset($plugin->type))

		{ 

			$editor	= JFactory::getEditor('tinymce');

			$is_editor=true;

		}

		



		JHTML::_('behavior.tooltip');	

		JHTML::_('behavior.calendar');

		JHTML::_('behavior.modal');

	?>

    

<script type="text/javascript">

if($)

if(typeof $.noConflict === 'function'){

   $.noConflict();

}

var already_submitted=false;



Joomla.submitbutton= function (pressbutton) 



{

	if (already_submitted) 

	{

		submitform( pressbutton );

		return;

	}



	var form = document.adminForm;



	if (pressbutton == 'cancel') 



	{



		submitform( pressbutton );



		return;



	}



	if(!document.getElementById('araqel'))

	{

		alert('Please wait while page loading');

		return;

	}

	else

		if(document.getElementById('araqel').value=='0')

		{

			alert('Please wait while page loading');

			return;

		}

		



	if (form.title.value == "")



	{



				alert( "The form must have a title." );	

				return;



	}		



	

		tox='';

		l_id_array=[<?php echo $labels['id']?>];

		l_label_array=[<?php echo $labels['label']?>];

		l_type_array=[<?php echo $labels['type']?>];

		l_id_removed=[];

		

		for(x=0; x< l_id_array.length; x++)

			{

				l_id_removed[l_id_array[x]]=true;

			}



for(t=1;t<=form_view_max;t++)

{

	if(document.getElementById('form_id_tempform_view'+t))

	{

		form_view_element=document.getElementById('form_id_tempform_view'+t);

		n=form_view_element.childNodes.length-2;	

		

		for(q=0;q<=n;q++)

		{

				if(form_view_element.childNodes[q].nodeType!=3)

				if(!form_view_element.childNodes[q].id)

				{

					GLOBAL_tr=form_view_element.childNodes[q];

		

					for (x=0; x < GLOBAL_tr.firstChild.childNodes.length; x++)

					{

			

						table=GLOBAL_tr.firstChild.childNodes[x];

						tbody=table.firstChild;

						for (y=0; y < tbody.childNodes.length; y++)

						{

							is_in_old=false;

							tr=tbody.childNodes[y];

							l_id=tr.id;

				

							l_label=document.getElementById( tr.id+'_element_labelform_id_temp').innerHTML;

							l_label = l_label.replace(/(\r\n|\n|\r)/gm," ");

							l_type=tr.getAttribute('type');

							for(z=0; z< l_id_array.length; z++)

							{

								if(l_id_array[z]==l_id)

								{

									if(l_type_array[z]=="type_address")

									{

										if(document.getElementById(l_id+"_mini_label_street1"))		

											l_id_removed[l_id_array[z]]=false;

							

											

																		

										if(document.getElementById(l_id+"_mini_label_street2"))		

										l_id_removed[parseInt(l_id_array[z])+1]=false;	

									

										

										if(document.getElementById(l_id+"_mini_label_city"))

										l_id_removed[parseInt(l_id_array[z])+2]=false;	

																			

										if(document.getElementById(l_id+"_mini_label_state"))

										l_id_removed[parseInt(l_id_array[z])+3]=false;	

										

										

										if(document.getElementById(l_id+"_mini_label_postal"))

										l_id_removed[parseInt(l_id_array[z])+4]=false;	

										

										

										if(document.getElementById(l_id+"_mini_label_country"))

										l_id_removed[parseInt(l_id_array[z])+5]=false;	

										

										

										z=z+5;

									}

									else

										l_id_removed[l_id]=false;



								}

							}

							

								if(tr.getAttribute('type')=="type_address")

								{

									addr_id=parseInt(tr.id);

									id_for_country= addr_id;

								

									if(document.getElementById(id_for_country+"_mini_label_street1"))

										tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_street1").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';addr_id++; 

									if(document.getElementById(id_for_country+"_mini_label_street2"))

										tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_street2").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';addr_id++; 

									if(document.getElementById(id_for_country+"_mini_label_city"))

										tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_city").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';	addr_id++; 

									if(document.getElementById(id_for_country+"_mini_label_state"))

										tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_state").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';	addr_id++;

									if(document.getElementById(id_for_country+"_mini_label_postal"))									

										tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_postal").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';	addr_id++; 

									if(document.getElementById(id_for_country+"_mini_label_country"))									

										tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_country").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#'; 

									}

								else

									tox=tox+l_id+'#**id**#'+l_label+'#**label**#'+l_type+'#****#';



							

							

						}

					}

				}

		}

	}	

}



	document.getElementById('label_order_current').value=tox;



	for(x=0; x< l_id_array.length; x++)

	{

		

		if(l_id_removed[l_id_array[x]])	{	

		

	

				

	

			tox=tox+l_id_array[x]+'#**id**#'+l_label_array[x]+'#**label**#'+l_type_array[x]+'#****#';

		}

	}

	

	

	document.getElementById('label_order').value=tox;

	

	

	refresh_()

	document.getElementById('pagination').value=document.getElementById('pages').getAttribute("type");

	document.getElementById('show_title').value=document.getElementById('pages').getAttribute("show_title");

	document.getElementById('show_numbers').value=document.getElementById('pages').getAttribute("show_numbers");

	

	already_submitted=true;

	

		submitform( pressbutton );

}



function remove_whitespace(node)

{

    var ttt;

	for (ttt=0; ttt < node.childNodes.length; ttt++)

	{

        if( node.childNodes[ttt] && node.childNodes[ttt].nodeType == '3' && !/\S/.test(  node.childNodes[ttt].nodeValue ) )

		{

			

			node.removeChild(node.childNodes[ttt]);

            ttt--;

		}

		else

		{

			if(node.childNodes[ttt].childNodes.length)

				remove_whitespace(node.childNodes[ttt]);

		}

	}

	return

}



function refresh_()

{

			

	document.getElementById('form').value=document.getElementById('take').innerHTML;

	document.getElementById('counter').value=gen;

	n=gen;

	for(i=0; i<n; i++)

	{

		if(document.getElementById(i))

		{	

			for(z=0; z<document.getElementById(i).childNodes.length; z++)

				if(document.getElementById(i).childNodes[z].nodeType==3)

					document.getElementById(i).removeChild(document.getElementById(i).childNodes[z]);



			if(document.getElementById(i).getAttribute('type')=="type_captcha" || document.getElementById(i).getAttribute('type')=="type_recaptcha")

			{

				if(document.getElementById(i).childNodes[10])

				{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				}

				else

				{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				}

				continue;

			}

						

			if(document.getElementById(i).getAttribute('type')=="type_section_break")

			{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				continue;

			}

						





			if(document.getElementById(i).childNodes[10])

			{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[2]);

			}

			else

			{

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

				document.getElementById(i).removeChild(document.getElementById(i).childNodes[1]);

			}

		}

	}

	

	for(i=0; i<=n; i++)

	{	

		if(document.getElementById(i))

		{

			type=document.getElementById(i).getAttribute("type");

				switch(type)

				{

					case "type_text":

					case "type_number":

					case "type_password":

					case "type_submitter_mail":

					case "type_own_select":

					case "type_country":

					case "type_hidden":

					case "type_map":

					case "type_mark_map":

					case "type_paypal_select":

					{

						remove_add_(i+"_elementform_id_temp");

						break;

					}

					

					case "type_submit_reset":

					{

						remove_add_(i+"_element_submitform_id_temp");

						if(document.getElementById(i+"_element_resetform_id_temp"))

							remove_add_(i+"_element_resetform_id_temp");

						break;

					}

					

					case "type_captcha":

					{

						remove_add_("_wd_captchaform_id_temp");

						remove_add_("_element_refreshform_id_temp");

						remove_add_("_wd_captcha_inputform_id_temp");

						break;

					}

					

					case "type_recaptcha":

					{

						document.getElementById("public_key").value = document.getElementById("wd_recaptchaform_id_temp").getAttribute("public_key");

						document.getElementById("private_key").value= document.getElementById("wd_recaptchaform_id_temp").getAttribute("private_key");

						document.getElementById("recaptcha_theme").value= document.getElementById("wd_recaptchaform_id_temp").getAttribute("theme");

						document.getElementById('wd_recaptchaform_id_temp').innerHTML='';

						remove_add_("wd_recaptchaform_id_temp");

						break;

					}

						

					case "type_file_upload":

						{

							remove_add_(i+"_elementform_id_temp");

							

								break;

						}

						

					case "type_textarea":

						{

						remove_add_(i+"_elementform_id_temp");



								break;

						}

						

					case "type_name":

						{

						

							if(document.getElementById(i+"_element_titleform_id_temp"))

							{

								remove_add_(i+"_element_titleform_id_temp");

								remove_add_(i+"_element_firstform_id_temp");

								remove_add_(i+"_element_lastform_id_temp");

								remove_add_(i+"_element_middleform_id_temp");

							}

							else

							{

								remove_add_(i+"_element_firstform_id_temp");

								remove_add_(i+"_element_lastform_id_temp");

							}

								break;



						}

						

					case "type_phone":

						{

						

							remove_add_(i+"_element_firstform_id_temp");

							remove_add_(i+"_element_lastform_id_temp");

							break;



						}

					case "type_paypal_price":

						{

						

							remove_add_(i+"_element_dollarsform_id_temp");

							remove_add_(i+"_element_centsform_id_temp");

							break;



						}

						case "type_address":

							{	

							if(document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('street1')=='no')

								remove_add_(i+"_street1form_id_temp");

							if(document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('street2')=='no')	

								remove_add_(i+"_street2form_id_temp");

							if(document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('city')=='no')

								remove_add_(i+"_cityform_id_temp");

							if(document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('state')=='no')

								remove_add_(i+"_stateform_id_temp");

							if(document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('postal')=='no')

								remove_add_(i+"_postalform_id_temp");

							if(document.getElementById(id_for_country+"_disable_fieldsform_id_temp").getAttribute('country')=='no')

								remove_add_(i+"_countryform_id_temp");

							

							

								break;

	

							}

							

						

					case "type_checkbox":

					case "type_radio":

					case "type_paypal_checkbox":

					case "type_paypal_radio":

					case "type_paypal_shipping":

						{

							is=true;

							for(j=0; j<100; j++)

								if(document.getElementById(i+"_elementform_id_temp"+j))

								{

									remove_add_(i+"_elementform_id_temp"+j);

								}



							/*if(document.getElementById(i+"_randomize").value=="yes")

								choises_randomize(i);*/

							

							break;

						}

					

					case "type_star_rating":

						{	

							remove_add_(i+"_elementform_id_temp");

						

								break;

						}	

						

					case "type_scale_rating":

						{	

						remove_add_(i+"_elementform_id_temp");

						

								break;

						}

					case "type_spinner":

						{	

						remove_add_(i+"_elementform_id_temp");

						

								break;

						}

					case "type_slider":

						{	

						remove_add_(i+"_elementform_id_temp");

										

								break;

						}

					case "type_range":

						{	

						remove_add_(i+"_elementform_id_temp0");

						remove_add_(i+"_elementform_id_temp1");

							

								break;

						}

					case "type_grading":

						{

							

							for(k=0; k<100; k++)

								if(document.getElementById(i+"_elementform_id_temp"+k))

								{

									remove_add_(i+"_elementform_id_temp"+k);

								}

						

							

							break;

						}

					case "type_matrix":

						{	

						remove_add_(i+"_elementform_id_temp");

						

								break;

						}

					

					case "type_button":

						{

							for(j=0; j<100; j++)

								if(document.getElementById(i+"_elementform_id_temp"+j))

								{

									remove_add_(i+"_elementform_id_temp"+j);

								}

							break;

						}

						

					case "type_time":

						{	

						if(document.getElementById(i+"_ssform_id_temp"))

							{

							remove_add_(i+"_ssform_id_temp");

							remove_add_(i+"_mmform_id_temp");

							remove_add_(i+"_hhform_id_temp");

							}

							else

							{

							remove_add_(i+"_mmform_id_temp");

							remove_add_(i+"_hhform_id_temp");



							}

							break;



						}

						

					case "type_date":

						{	

						remove_add_(i+"_elementform_id_temp");

						remove_add_(i+"_buttonform_id_temp");

						

							break;

						}

					case "type_date_fields":

						{	

						remove_add_(i+"_dayform_id_temp");

						remove_add_(i+"_monthform_id_temp");

						remove_add_(i+"_yearform_id_temp");

								break;

						}

						

						

				}	

		}

	}

	

	for(i=1; i<=form_view_max; i++)

	{

		if(document.getElementById('form_id_tempform_view'+i))

		{

			if(document.getElementById('page_next_'+i))

				document.getElementById('page_next_'+i).removeAttribute('src');

			if(document.getElementById('page_previous_'+i))

				document.getElementById('page_previous_'+i).removeAttribute('src');

			document.getElementById('form_id_tempform_view'+i).parentNode.removeChild(document.getElementById('form_id_tempform_view_img'+i));

			document.getElementById('form_id_tempform_view'+i).removeAttribute('style');

		}

	}

	

for(t=1;t<=form_view_max;t++)

{

	if(document.getElementById('form_id_tempform_view'+t))

	{

		form_view_element=document.getElementById('form_id_tempform_view'+t);		

		remove_whitespace(form_view_element);

		n=form_view_element.childNodes.length-2;

		

		for(q=0;q<=n;q++)

		{

				if(form_view_element.childNodes[q])

				if(form_view_element.childNodes[q].nodeType!=3)

				if(!form_view_element.childNodes[q].id)

				{

					del=true;

					GLOBAL_tr=form_view_element.childNodes[q];

					

					for (x=0; x < GLOBAL_tr.firstChild.childNodes.length; x++)

					{

			

						table=GLOBAL_tr.firstChild.childNodes[x];

						tbody=table.firstChild;

						

						if(tbody.childNodes.length)

							del=false;

					}

				

					if(del)

					{

						form_view_element.removeChild(form_view_element.childNodes[q]);

					}

				

				}

		}

	}	

}

	



	document.getElementById('form_front').value=document.getElementById('take').innerHTML;



}











	gen=<?php echo $row->counter; ?>;//add main form  id

    function enable()

	{

		alltypes=Array('customHTML','text','checkbox','radio','time_and_date','select','file_upload','captcha','map','button','page_break','section_break','paypal','survey');

		for(x=0; x<14;x++)

	

		{

			document.getElementById('img_'+alltypes[x]).src="components/com_formmaker/images/"+alltypes[x]+".png";

		}

	

		if(document.getElementById('formMakerDiv').style.display=='block'){jQuery('#formMakerDiv').slideToggle(200);}else{jQuery('#formMakerDiv').slideToggle(400);}

		

		if(document.getElementById('formMakerDiv').offsetWidth)

			document.getElementById('formMakerDiv1').style.width	=(document.getElementById('formMakerDiv').offsetWidth - 60)+'px';

		

		if(document.getElementById('formMakerDiv1').style.display=='block'){jQuery('#formMakerDiv1').slideToggle(200);}else{jQuery('#formMakerDiv1').slideToggle(400);}

		document.getElementById('when_edit').style.display		='none';

	}



    function enable2()

	{

		alltypes=Array('customHTML','text','checkbox','radio','time_and_date','select','file_upload','captcha','map','button','page_break','section_break','paypal','survey');

		for(x=0; x<14;x++)

		{

			document.getElementById('img_'+alltypes[x]).src="components/com_formmaker/images/"+alltypes[x]+".png";

		}

	

		if(document.getElementById('formMakerDiv').style.display=='block'){jQuery('#formMakerDiv').slideToggle(200);}else{jQuery('#formMakerDiv').slideToggle(400);}

		

		if(document.getElementById('formMakerDiv').offsetWidth)

			document.getElementById('formMakerDiv1').style.width	=(document.getElementById('formMakerDiv').offsetWidth - 60)+'px';

		

		if(document.getElementById('formMakerDiv1').style.display=='block'){jQuery('#formMakerDiv1').slideToggle(200);}else{jQuery('#formMakerDiv1').slideToggle(400);}

		document.getElementById('when_edit').style.display		='block';

		

		if(document.getElementById('field_types').offsetWidth)

			document.getElementById('when_edit').style.width	=document.getElementById('field_types').offsetWidth+'px';

		

		if(document.getElementById('field_types').offsetHeight)

			document.getElementById('when_edit').style.height	=document.getElementById('field_types').offsetHeight+'px';

		

	}

    </script>

<style>

#when_edit

{

position:absolute;

background-color:#666;

z-index:101;

display:none;

width:100%;

height:100%;

opacity: 0.7;

filter: alpha(opacity = 70);

}



#formMakerDiv

{

position:fixed;

background-color:#666;

z-index:100;

display:none;

left:0;

top:0;

width:100%;

height:100%;

opacity: 0.7;

filter: alpha(opacity = 70);

}

#formMakerDiv1

{

position:fixed;

z-index:100;

background-color:transparent;

top:0;

left:0;

display:none;

margin-left:30px;

margin-top:35px;

}



input[type="radio"], input[type="checkbox"] {

margin: 5px;

}



.pull-left

{

float:none !important;

}



.modal-body

{

max-height:100%;

}



img

{

max-width:none;

}

.wdform_date

{

margin:0px !important;

}



.formmaker_table input

{

border-radius: 3px;

padding: 2px;

}



.formmaker_table

{

border-radius:8px;

border:6px #00aeef solid;

background-color:#00aeef;

height:120px;

}



.formMakerDiv1_table

{

border:6px #00aeef solid;

background-color:#FFF;

border-radius:8px;

}



label

{

display:inline;

}

</style>



<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div  class="formmaker_table" width="100%" >

<div style="float:left; text-align:center">

 	</br>

   <img src="components/com_formmaker/images/FormMaker.png" />

	</br>

	</br>

   <img src="components/com_formmaker/images/logo.png" />



</div>



<div style="float:right">



    <span style="font-size:16.76pt; font-family:tahoma; color:#FFFFFF; vertical-align:middle;">Form title:&nbsp;&nbsp;</span>



    <input id="title" name="title" style="width:151px; height:19px; border:none; font-size:11px; " value="<?php echo $row->title; ?>"/>

   <br/>

	<a href="#" onclick="Joomla.submitbutton('form_options_temp')" style="cursor:pointer;margin:10px; float:right; color:#fff; font-size:20px"><img src="components/com_formmaker/images/formoptions.png" /></a>    

   <br/>

	<img src="components/com_formmaker/images/addanewfield.png" onclick="enable(); Enable()" style="cursor:pointer;margin:10px; float:right" />



</div>

	

  



</div>

  

<div id="formMakerDiv" onclick="close_window()"></div>  

<div id="formMakerDiv1"  align="center">

    

    

<table border="0" width="100%" cellpadding="0" cellspacing="0" height="100%" class="formMakerDiv1_table">

  <tr>

    <td style="padding:0px">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">

        <tr valign="top">

         <td width="15%" height="100%" style="border-right:dotted black 1px;" id="field_types">

            <div id="when_edit" style="display:none"></div>

			<table border="0" cellpadding="0" cellspacing="3" width="100%" style="border-collapse: separate; border-spacing: 3px;">

			<tr>

            <td align="center" onClick="addRow('customHTML')" style="cursor:pointer" id="table_editor"  class="field_buttons"><img src="components/com_formmaker/images/customHTML.png" style="margin:5px" id="img_customHTML"/></td>

            

            <td align="center" onClick="addRow('text')" style="cursor:pointer" id="table_text" class="field_buttons"><img src="components/com_formmaker/images/text.png" style="margin:5px" id="img_text"/></td>

            </tr>

            <tr>

            <td align="center" onClick="addRow('time_and_date')" style="cursor:pointer" id="table_time_and_date" class="field_buttons"><img src="components/com_formmaker/images/time_and_date.png" style="margin:5px" id="img_time_and_date"/></td>

            

            <td align="center" onClick="addRow('select')" style="cursor:pointer" id="table_select" class="field_buttons"><img src="components/com_formmaker/images/select.png" style="margin:5px" id="img_select"/></td>

            </tr>

            <tr>             

            <td align="center" onClick="addRow('checkbox')" style="cursor:pointer" id="table_checkbox" class="field_buttons"><img src="components/com_formmaker/images/checkbox.png" style="margin:5px" id="img_checkbox"/></td>

            

            <td align="center" onClick="addRow('radio')" style="cursor:pointer" id="table_radio" class="field_buttons"><img src="components/com_formmaker/images/radio.png" style="margin:5px" id="img_radio"/></td>

            </tr>

            <tr>

            <td align="center" onClick="addRow('file_upload')" style="cursor:pointer" id="table_file_upload" class="field_buttons"><img src="components/com_formmaker/images/file_upload.png" style="margin:5px" id="img_file_upload"/></td>

            

            <td align="center" onClick="addRow('captcha')" style="cursor:pointer" id="table_captcha" class="field_buttons"><img src="components/com_formmaker/images/captcha.png" style="margin:5px" id="img_captcha"/></td>

            </tr>

            <tr>

            <td align="center" onClick="addRow('page_break')" style="cursor:pointer" id="table_page_break" class="field_buttons"><img src="components/com_formmaker/images/page_break.png" style="margin:5px" id="img_page_break"/></td>  

            

            <td align="center" onClick="addRow('section_break')" style="cursor:pointer" id="table_section_break" class="field_buttons"><img src="components/com_formmaker/images/section_break.png" style="margin:5px" id="img_section_break"/></td>

            </tr>

            <tr>

            <td align="center" onClick="addRow('map')" id="table_map" class="field_buttons"><img src="components/com_formmaker/images/map.png" style="margin:5px" id="img_map"/></td>  

  			<td align="center" onClick="addRow('paypal')" id="table_paypal" class="field_buttons"><img src="components/com_formmaker/images/paypal.png" style="margin:5px" id="img_paypal" /></td>       

            </tr>		

			<tr>

            <td align="center" onClick="addRow('survey')" class="field_buttons" id="table_survey"><img src="components/com_formmaker/images/survey.png" style="margin:5px" id="img_survey"/></td>           

            <td align="center" onClick="addRow('button')" id="table_button" class="field_buttons"><img src="components/com_formmaker/images/button.png" style="margin:5px" id="img_button"/></td>

			  

            </tr>

            </table>



         </td>

         <td width="35%" height="100%" align="left"><div id="edit_table" style="padding:0px; overflow-y:scroll; height:535px" ></div></td>



		 <td align="center" valign="top" style="background:url(components/com_formmaker/images/border2.png) repeat-y;">&nbsp;</td>

         <td style="padding:15px">

         <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">

         

            <tr>

                <td align="right"><input type="radio" value="end" name="el_pos" checked="checked" id="pos_end" onclick="Disable()"/>

                  At The End

                  <input type="radio" value="begin" name="el_pos" id="pos_begin" onclick="Disable()"/>

                  At The Beginning

                  <input type="radio" value="before" name="el_pos" id="pos_before" onclick="Enable()"/>

                  Before

                  <select style="width:100px; margin-left:5px" id="sel_el_pos" disabled="disabled">

                  </select>

                  <img alt="ADD" title="add" style="cursor:pointer; vertical-align:middle; margin:5px" src="components/com_formmaker/images/save.png" onClick="add(0, false)"/>

                  <img alt="CANCEL" title="cancel"  style=" cursor:pointer; vertical-align:middle; margin:5px" src="components/com_formmaker/images/cancel_but.png" onClick="close_window()"/>

				

                	<hr style=" margin-bottom:10px" />

                  </td>

              </tr>

              

              <tr height="100%" valign="top">

                <td  id="show_table"></td>

              </tr>

              

            </table>

         </td>

        </tr>

      </table>

    </td>

  </tr>

</table>



<input type="hidden" id="old" />

<input type="hidden" id="old_selected" />

<input type="hidden" id="element_type" />

<input type="hidden" id="editing_id" />

<div id="main_editor" style="position:absolute; display:none; z-index:140;"><?php if($is_editor) echo $editor->display('editor','','440','350','40','6');

else

{

?>

<textarea name="editor" id="editor" cols="40" rows="6" style="width: 440px; height: 350px; " class="mce_editable" aria-hidden="true"></textarea>

<?php



}

 ?></div>

 </div>

 

 <?php if(!$is_editor)

?>

<iframe id="editor_ifr" style="display:none"></iframe>



<?php

?>





 

 

<br />

<br />



    <fieldset>



    <legend>



    <h2 style="color:#00aeef">Form</h2>



    </legend>



        <?php

		

    echo '<style>'.self::first_css.'</style>';



?>

<table width="100%" style="margin:8px"><tr id="page_navigation"><td align="center" width="90%" id="pages" show_title="<?php echo $row->show_title; ?>" show_numbers="<?php echo $row->show_numbers; ?>" type="<?php echo $row->pagination; ?>"></td><td align="left" id="edit_page_navigation"></td></tr></table>



    <div id="take">

    <?php

	

	    echo $row->form;

		

	 ?> </div>



    </fieldset>



    <input type="hidden" name="form" id="form">

    <input type="hidden" name="form_front" id="form_front">

    <input type="hidden" name="theme" id="theme" value="<?php echo $row->theme; ?>">

   

    <input type="hidden" name="pagination" id="pagination" />

    <input type="hidden" name="show_title" id="show_title" />

    <input type="hidden" name="show_numbers" id="show_numbers" />

	

    <input type="hidden" name="public_key" id="public_key" />

    <input type="hidden" name="private_key" id="private_key" />

    <input type="hidden" name="recaptcha_theme" id="recaptcha_theme" />



	<input type="hidden" id="label_order_current" name="label_order_current" value="<?php echo $row->label_order_current;?>" />

    <input type="hidden" id="label_order" name="label_order" value="<?php echo $row->label_order;?>" />

    <input type="hidden" name="counter" id="counter" value="<?php echo $row->counter;?>">



<script type="text/javascript">



function formOnload()

{

//enable maps

for(t=0; t<<?php echo $row->counter;?>; t++)

	if(document.getElementById(t+"_typeform_id_temp"))

	{

		if(document.getElementById(t+"_typeform_id_temp").value=="type_map" || document.getElementById(t+"_typeform_id_temp").value=="type_mark_map")

		{

			if_gmap_init(t);

			for(q=0; q<20; q++)

				if(document.getElementById(t+"_elementform_id_temp").getAttribute("long"+q))

				{

				

					w_long=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("long"+q));

					w_lat=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("lat"+q));

					w_info=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("info"+q));

					add_marker_on_map(t,q, w_long, w_lat, w_info, false);

				}

		}

		else

		if(document.getElementById(t+"_typeform_id_temp").value=="type_date")

				Calendar.setup({

						inputField: t+"_elementform_id_temp",

						ifFormat: document.getElementById(t+"_buttonform_id_temp").getAttribute('format'),

						button: t+"_buttonform_id_temp",

						align: "Tl",

						singleClick: true,

						firstDay: 0

						});

						

		else				

		if(document.getElementById(t+"_typeform_id_temp").value=="type_spinner")	{



				var spinner_value = document.getElementById(t+"_elementform_id_temp").get( "aria-valuenow" );

				var spinner_min_value = document.getElementById(t+"_min_valueform_id_temp").value;

				var spinner_max_value = document.getElementById(t+"_max_valueform_id_temp").value;

			    var spinner_step = document.getElementById(t+"_stepform_id_temp").value;

					  

					 jQuery( "#"+t+"_elementform_id_temp" ).removeClass( "ui-spinner-input" )

			.prop( "disabled", false )

			.removeAttr( "autocomplete" )

			.removeAttr( "role" )

			.removeAttr( "aria-valuemin" )

			.removeAttr( "aria-valuemax" )

			.removeAttr( "aria-valuenow" );

			

			span_ui= document.getElementById(t+"_elementform_id_temp").parentNode;

				span_ui.parentNode.appendChild(document.getElementById(t+"_elementform_id_temp"));

				span_ui.parentNode.removeChild(span_ui);

				

				jQuery("#"+t+"_elementform_id_temp")[0].spin = null;

				

				spinner = jQuery( "#"+t+"_elementform_id_temp" ).spinner();

				spinner.spinner( "value", spinner_value );

				jQuery( "#"+t+"_elementform_id_temp" ).spinner({ min: spinner_min_value});    

                jQuery( "#"+t+"_elementform_id_temp" ).spinner({ max: spinner_max_value});

                jQuery( "#"+t+"_elementform_id_temp" ).spinner({ step: spinner_step});

				

		}

				else

			if(document.getElementById(t+"_typeform_id_temp").value=="type_slider")	{

 

				var slider_value = document.getElementById(t+"_slider_valueform_id_temp").value;

				var slider_min_value = document.getElementById(t+"_slider_min_valueform_id_temp").value;

				var slider_max_value = document.getElementById(t+"_slider_max_valueform_id_temp").value;

				

				var slider_element_value = document.getElementById( t+"_element_valueform_id_temp" );

				var slider_value_save = document.getElementById( t+"_slider_valueform_id_temp" );

				

				document.getElementById(t+"_elementform_id_temp").innerHTML = "";

				document.getElementById(t+"_elementform_id_temp").removeAttribute( "class" );

				document.getElementById(t+"_elementform_id_temp").removeAttribute( "aria-disabled" );



				 jQuery("#"+t+"_elementform_id_temp")[0].slide = null;	

			

					jQuery(function() {

				jQuery( "#"+t+"_elementform_id_temp").slider({

				range: "min",

				value: eval(slider_value),

				min: eval(slider_min_value),

				max: eval(slider_max_value),

				slide: function( event, ui ) {	

					slider_element_value.innerHTML = "" + ui.value ;

					slider_value_save.value = "" + ui.value; 



		}

	});

	

	

});	

		

				

		}

		else

       if(document.getElementById(t+"_typeform_id_temp").value=="type_range"){

                var spinner_value0 = document.getElementById(t+"_elementform_id_temp0").get( "aria-valuenow" );

			    var spinner_step = document.getElementById(t+"_range_stepform_id_temp").value;

					  

					 jQuery( "#"+t+"_elementform_id_temp0" ).removeClass( "ui-spinner-input" )

			.prop( "disabled", false )

			.removeAttr( "autocomplete" )

			.removeAttr( "role" )

			.removeAttr( "aria-valuenow" );

			

			span_ui= document.getElementById(t+"_elementform_id_temp0").parentNode;

				span_ui.parentNode.appendChild(document.getElementById(t+"_elementform_id_temp0"));

				span_ui.parentNode.removeChild(span_ui);

				

				

				jQuery("#"+t+"_elementform_id_temp0")[0].spin = null;

				jQuery("#"+t+"_elementform_id_temp1")[0].spin = null;

				

				spinner0 = jQuery( "#"+t+"_elementform_id_temp0" ).spinner();

				spinner0.spinner( "value", spinner_value0 );

                jQuery( "#"+t+"_elementform_id_temp0" ).spinner({ step: spinner_step});

				

				

				

				var spinner_value1 = document.getElementById(t+"_elementform_id_temp1").get( "aria-valuenow" );

			   					  

					 jQuery( "#"+t+"_elementform_id_temp1" ).removeClass( "ui-spinner-input" )

			.prop( "disabled", false )

			.removeAttr( "autocomplete" )

			.removeAttr( "role" )

			.removeAttr( "aria-valuenow" );

			

			span_ui1= document.getElementById(t+"_elementform_id_temp1").parentNode;

				span_ui1.parentNode.appendChild(document.getElementById(t+"_elementform_id_temp1"));

				span_ui1.parentNode.removeChild(span_ui1);

					

				spinner1 = jQuery( "#"+t+"_elementform_id_temp1" ).spinner();

				spinner1.spinner( "value", spinner_value1 );

                jQuery( "#"+t+"_elementform_id_temp1" ).spinner({ step: spinner_step});

				

					var myu = t;

        jQuery(document).ready(function() {	



		jQuery("#"+myu+"_mini_label_from").click(function() {		

	

		if (jQuery(this).children('input').length == 0) {	



			var from = "<input type='text' id='from' class='from'  style='outline:none; border:none; background:none; font-size:11px; width:100px;' value=\""+jQuery(this).text()+"\">";	

				jQuery(this).html(from);							

				jQuery("input.from").focus();			

				jQuery("input.from").blur(function() {	

			

			var id_for_blur = document.getElementById('from').parentNode.id.split('_');

			var value = jQuery(this).val();			

		jQuery("#"+id_for_blur[0]+"_mini_label_from").text(value);		

		});	

	}	

	});	    

     	

		jQuery("label#"+myu+"_mini_label_to").click(function() {	

	if (jQuery(this).children('input').length == 0) {	

	

		var to = "<input type='text' id='to' class='to' style='outline:none; border:none; background:none; font-size:11px; width:100px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(to);			

			jQuery("input.to").focus();					

			jQuery("input.to").blur(function() {	

			var id_for_blur = document.getElementById('to').parentNode.id.split('_');			

			var value = jQuery(this).val();			

			

			jQuery("#"+id_for_blur[0]+"_mini_label_to").text(value);	

		});	

		 

	}	

	});

	

	

	

	});	

     }	



		else

       if(document.getElementById(t+"_typeform_id_temp").value=="type_name"){

		var myu = t;

        jQuery(document).ready(function() {	



		jQuery("#"+myu+"_mini_label_first").click(function() {		

	

		if (jQuery(this).children('input').length == 0) {	



			var first = "<input type='text' id='first' class='first' style='outline:none; border:none; background:none; width:130px;' value=\""+jQuery(this).text()+"\">";	

				jQuery(this).html(first);							

				jQuery("input.first").focus();			

				jQuery("input.first").blur(function() {	

			

			var id_for_blur = document.getElementById('first').parentNode.id.split('_');

			var value = jQuery(this).val();			

		jQuery("#"+id_for_blur[0]+"_mini_label_first").text(value);		

		});	

	}	

	});	    

     	

		jQuery("label#"+myu+"_mini_label_last").click(function() {	

	if (jQuery(this).children('input').length == 0) {	

	

		var last = "<input type='text' id='last' class='last'  style='outline:none; border:none; background:none; width:130px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(last);			

			jQuery("input.last").focus();					

			jQuery("input.last").blur(function() {	

			var id_for_blur = document.getElementById('last').parentNode.id.split('_');			

			var value = jQuery(this).val();			

			

			jQuery("#"+id_for_blur[0]+"_mini_label_last").text(value);	

		});	

		 

	}	

	});

	

		jQuery("label#"+myu+"_mini_label_title").click(function() {	

			if (jQuery(this).children('input').length == 0) {		

				var title_ = "<input type='text' id='title_' class='title_'  style='outline:none; border:none; background:none; width:50px;' value=\""+jQuery(this).text()+"\">";	

					jQuery(this).html(title_);			

					jQuery("input.title_").focus();					

					jQuery("input.title_").blur(function() {	

					var id_for_blur = document.getElementById('title_').parentNode.id.split('_');			

					var value = jQuery(this).val();			

					

					jQuery("#"+id_for_blur[0]+"_mini_label_title").text(value);	

				});	

			}	

			});





	jQuery("label#"+myu+"_mini_label_middle").click(function() {	

	if (jQuery(this).children('input').length == 0) {		

		var middle = "<input type='text' id='middle' class='middle'  style='outline:none; border:none; background:none; width:130px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(middle);			

			jQuery("input.middle").focus();					

			jQuery("input.middle").blur(function() {	

            var id_for_blur = document.getElementById('middle').parentNode.id.split('_');			

			var value = jQuery(this).val();			

			

			jQuery("#"+id_for_blur[0]+"_mini_label_middle").text(value);	

		});	

	}	

	});

	

	});		

		 }						

		else

       if(document.getElementById(t+"_typeform_id_temp").value=="type_address"){

		var myu = t;

       

	jQuery(document).ready(function() {		

	jQuery("label#"+myu+"_mini_label_street1").click(function() {			



		if (jQuery(this).children('input').length == 0) {				

		var street1 = "<input type='text' id='street1' class='street1' style='outline:none; border:none; background:none; width:150px;' value=\""+jQuery(this).text()+"\">";

		jQuery(this).html(street1);					

		jQuery("input.street1").focus();		

		jQuery("input.street1").blur(function() {	

		var id_for_blur = document.getElementById('street1').parentNode.id.split('_');

		var value = jQuery(this).val();			

		jQuery("#"+id_for_blur[0]+"_mini_label_street1").text(value);		

		});		

		}	

		});		

	

	jQuery("label#"+myu+"_mini_label_street2").click(function() {		

	if (jQuery(this).children('input').length == 0) {		

	var street2 = "<input type='text' id='street2' class='street2'  style='outline:none; border:none; background:none; width:150px;' value=\""+jQuery(this).text()+"\">";

	jQuery(this).html(street2);					

	jQuery("input.street2").focus();		

	jQuery("input.street2").blur(function() {	

	var id_for_blur = document.getElementById('street2').parentNode.id.split('_');

	var value = jQuery(this).val();			

	jQuery("#"+id_for_blur[0]+"_mini_label_street2").text(value);		

	});		

	}	

	});	

	

	

	jQuery("label#"+myu+"_mini_label_city").click(function() {	

		if (jQuery(this).children('input').length == 0) {	

		var city = "<input type='text' id='city' class='city'  style='outline:none; border:none; background:none; width:130px;' value=\""+jQuery(this).text()+"\">";

		jQuery(this).html(city);			

		jQuery("input.city").focus();				

		jQuery("input.city").blur(function() {	

		var id_for_blur = document.getElementById('city').parentNode.id.split('_');		

		var value = jQuery(this).val();		

		jQuery("#"+id_for_blur[0]+"_mini_label_city").text(value);		

	});		

	}	

	});	

	

	jQuery("label#"+myu+"_mini_label_state").click(function() {		

		if (jQuery(this).children('input').length == 0) {	

		var state = "<input type='text' id='state' class='state'  style='outline:none; border:none; background:none; width:130px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(state);		

			jQuery("input.state").focus();		

			jQuery("input.state").blur(function() {	

		var id_for_blur = document.getElementById('state').parentNode.id.split('_');					

		var value = jQuery(this).val();			

	jQuery("#"+id_for_blur[0]+"_mini_label_state").text(value);	

	});	

	}

	});		



	jQuery("label#"+myu+"_mini_label_postal").click(function() {		

	if (jQuery(this).children('input').length == 0) {			

	var postal = "<input type='text' id='postal' class='postal'  style='outline:none; border:none; background:none; width:130px;' value=\""+jQuery(this).text()+"\">";

	jQuery(this).html(postal);			

	jQuery("input.postal").focus();			

	jQuery("input.postal").blur(function() {

    var id_for_blur = document.getElementById('postal').parentNode.id.split('_');	

	var value = jQuery(this).val();		

	jQuery("#"+id_for_blur[0]+"_mini_label_postal").text(value);		

	});	

	}

	});	

	

	

	jQuery("label#"+myu+"_mini_label_country").click(function() {		

		if (jQuery(this).children('input').length == 0) {		

			var country = "<input type='text' id='country' class='country'  style='outline:none; border:none; background:none; width:130px;' value=\""+jQuery(this).text()+"\">";

			jQuery(this).html(country);		

			jQuery("input.country").focus();	

			jQuery("input.country").blur(function() {	

			var id_for_blur = document.getElementById('country').parentNode.id.split('_');				

			var value = jQuery(this).val();			

			jQuery("#"+id_for_blur[0]+"_mini_label_country").text(value);			

			});	

		}	

	});

	});	



	   }						

		else

       if(document.getElementById(t+"_typeform_id_temp").value=="type_phone"){

		var myu = t;

      

	jQuery(document).ready(function() {	

	jQuery("label#"+myu+"_mini_label_area_code").click(function() {		

	if (jQuery(this).children('input').length == 0) {		



		var area_code = "<input type='text' id='area_code' class='area_code' style='outline:none; border:none; background:none; width:100px;' value=\""+jQuery(this).text()+"\">";		



		jQuery(this).html(area_code);		

		jQuery("input.area_code").focus();		

		jQuery("input.area_code").blur(function() {	

		var id_for_blur = document.getElementById('area_code').parentNode.id.split('_');

		var value = jQuery(this).val();			

		jQuery("#"+id_for_blur[0]+"_mini_label_area_code").text(value);		

		});		

	}	

	});	



	

	jQuery("label#"+myu+"_mini_label_phone_number").click(function() {		



	if (jQuery(this).children('input').length == 0) {			

		var phone_number = "<input type='text' id='phone_number' class='phone_number'  style='outline:none; border:none; background:none; width:100px;' value=\""+jQuery(this).text()+"\">";						



		jQuery(this).html(phone_number);					



		jQuery("input.phone_number").focus();			

		jQuery("input.phone_number").blur(function() {		

		var id_for_blur = document.getElementById('phone_number').parentNode.id.split('_');

		var value = jQuery(this).val();			

		jQuery("#"+id_for_blur[0]+"_mini_label_phone_number").text(value);		

		});	

	}	

	});

	

	});	

		 }						

		else

       if(document.getElementById(t+"_typeform_id_temp").value=="type_date_fields"){

		var myu = t;

      

	jQuery(document).ready(function() {	

	jQuery("label#"+myu+"_day_label").click(function() {		

		if (jQuery(this).children('input').length == 0) {				

			var day = "<input type='text' id='day' class='day' style='outline:none; border:none; background:none; width:80px;' value=\""+jQuery(this).text()+"\">";	

				jQuery(this).html(day);							

				jQuery("input.day").focus();			

				jQuery("input.day").blur(function() {	

			var id_for_blur = document.getElementById('day').parentNode.id.split('_');

			var value = jQuery(this).val();			



		jQuery("#"+id_for_blur[0]+"_day_label").text(value);		

		});	

	}	

	});		





	jQuery("label#"+myu+"_month_label").click(function() {	

	if (jQuery(this).children('input').length == 0) {		

		var month = "<input type='text' id='month' class='month' style='outline:none; border:none; background:none; width:80px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(month);			

			jQuery("input.month").focus();					

			jQuery("input.month").blur(function() {	

			var id_for_blur = document.getElementById('month').parentNode.id.split('_');			

			var value = jQuery(this).val();			

			

			jQuery("#"+id_for_blur[0]+"_month_label").text(value);	

		});	

	}	

	});

	

		jQuery("label#"+myu+"_year_label").click(function() {	

	if (jQuery(this).children('input').length == 0) {		

		var year = "<input type='text' id='year' class='year' style='outline:none; border:none; background:none; width:80px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(year);			

			jQuery("input.year").focus();					

			jQuery("input.year").blur(function() {	

		var id_for_blur = document.getElementById('year').parentNode.id.split('_');				

			var value = jQuery(this).val();			

			

			jQuery("#"+id_for_blur[0]+"_year_label").text(value);	

		});	

	}	

	});

	

	});	



	

		 }						

			else

       if(document.getElementById(t+"_typeform_id_temp").value=="type_time"){

		var myu = t;

      

jQuery(document).ready(function() {	

	jQuery("label#"+myu+"_mini_label_hh").click(function() {		

		if (jQuery(this).children('input').length == 0) {				

			var hh = "<input type='text' id='hh' class='hh' style='outline:none; border:none; background:none; width:50px;' value=\""+jQuery(this).text()+"\">";	

				jQuery(this).html(hh);							

				jQuery("input.hh").focus();			

				jQuery("input.hh").blur(function() {	

				var id_for_blur = document.getElementById('hh').parentNode.id.split('_');	

			var value = jQuery(this).val();			





		jQuery("#"+id_for_blur[0]+"_mini_label_hh").text(value);		

		});	

	}	

	});		





	jQuery("label#"+myu+"_mini_label_mm").click(function() {	

	if (jQuery(this).children('input').length == 0) {		

		var mm = "<input type='text' id='mm' class='mm' style='outline:none; border:none; background:none; width:50px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(mm);			

			jQuery("input.mm").focus();					

			jQuery("input.mm").blur(function() {

            var id_for_blur = document.getElementById('mm').parentNode.id.split('_');				

			var value = jQuery(this).val();			

			

			jQuery("#"+id_for_blur[0]+"_mini_label_mm").text(value);	

		});	

	}	

	});

	

		jQuery("label#"+myu+"_mini_label_ss").click(function() {	

	if (jQuery(this).children('input').length == 0) {		

		var ss = "<input type='text' id='ss' class='ss' style='outline:none; border:none; background:none; width:50px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(ss);			

			jQuery("input.ss").focus();					

			jQuery("input.ss").blur(function() {

   var id_for_blur = document.getElementById('ss').parentNode.id.split('_');				

			var value = jQuery(this).val();			

			

			jQuery("#"+id_for_blur[0]+"_mini_label_ss").text(value);	

		});	

	}	

	});

	

		jQuery("label#"+myu+"_mini_label_am_pm").click(function() {		

		if (jQuery(this).children('input').length == 0) {				

			var am_pm = "<input type='text' id='am_pm' class='am_pm' size='4' style='outline:none; border:none; background:none; width:50px;' value=\""+jQuery(this).text()+"\">";	

				jQuery(this).html(am_pm);							

				jQuery("input.am_pm").focus();			

				jQuery("input.am_pm").blur(function() {	

		    var id_for_blur = document.getElementById('am_pm').parentNode.id.split('_');	

			var value = jQuery(this).val();			



		jQuery("#"+id_for_blur[0]+"_mini_label_am_pm").text(value);		

		});	

	}	

	});	

	});

		

		 }	




	else

       if(document.getElementById(t+"_typeform_id_temp").value=="type_scale_rating"){

		var myu = t;

        jQuery(document).ready(function() {	



		jQuery("#"+myu+"_mini_label_worst").click(function() {		

	

		if (jQuery(this).children('input').length == 0) {	



			var worst = "<input type='text' id='worst' class='worst' style='outline:none; border:none; background:none; font-size:11px; width:100px;' value=\""+jQuery(this).text()+"\">";	

				jQuery(this).html(worst);							

				jQuery("input.worst").focus();			

				jQuery("input.worst").blur(function() {	

			

			var id_for_blur = document.getElementById('worst').parentNode.id.split('_');

			var value = jQuery(this).val();			

		jQuery("#"+id_for_blur[0]+"_mini_label_worst").text(value);		

		});	

	}	

	});	    

     	

		jQuery("label#"+myu+"_mini_label_best").click(function() {	

	if (jQuery(this).children('input').length == 0) {	

	

		var best = "<input type='text' id='best' class='best' style='outline:none; border:none; background:none; font-size:11px; width:100px;' value=\""+jQuery(this).text()+"\">";	

			jQuery(this).html(best);			

			jQuery("input.best").focus();					

			jQuery("input.best").blur(function() {	

			var id_for_blur = document.getElementById('best').parentNode.id.split('_');			

			var value = jQuery(this).val();			

			

			jQuery("#"+id_for_blur[0]+"_mini_label_best").text(value);	

		});	

		 

	}	

	});

	

	

	

	});		

		 }			 

					



	}

	

	form_view=1;

	form_view_count=0;

	for(i=1; i<=30; i++)

	{

		if(document.getElementById('form_id_tempform_view'+i))

		{

			form_view_count++;

			form_view_max=i;

		}

	}

	

	if(form_view_count>1)

	{

		for(i=1; i<=form_view_max; i++)

		{

			if(document.getElementById('form_id_tempform_view'+i))

			{

				first_form_view=i;

				break;

			}

		}

		form_view=form_view_max;

		need_enable=false;

		

		generate_page_nav(first_form_view);

		

	var img_EDIT = document.createElement("img");

			img_EDIT.setAttribute("src", "components/com_formmaker/images/edit.png");

			img_EDIT.style.cssText = "margin-left:40px; cursor:pointer";

			img_EDIT.setAttribute("onclick", 'el_page_navigation()');

			

	var td_EDIT = document.getElementById("edit_page_navigation");

			td_EDIT.appendChild(img_EDIT);

	

	document.getElementById('page_navigation').appendChild(td_EDIT);



			

	}





//if(document.getElementById('take').innerHTML.indexOf('up_row(')==-1) location.reload(true);

//else 

document.getElementById('form').value=document.getElementById('take').innerHTML;

document.getElementById('araqel').value=1;



}



function formAddToOnload()

{ 

	if(formOldFunctionOnLoad){ formOldFunctionOnLoad(); }

	formOnload();

}



function formLoadBody()

{

	formOldFunctionOnLoad = window.onload;

	window.onload = formAddToOnload;

}



var formOldFunctionOnLoad = null;



formLoadBody();





</script>



    <input type="hidden" name="option" value="com_formmaker" />



    <input type="hidden" name="id" value="<?php echo $row->id?>" />



    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />



    <input type="hidden" name="task" value="" />

    <input type="hidden" id="araqel" value="0" />



</form>

<script>

	appWidth			=parseInt(document.body.offsetWidth);

	appHeight			=parseInt(document.body.offsetHeight);

	

	jQuery('#modal-preview').on('show', function () {

document.getElementById('modal-preview-container').innerHTML = '<div class="modal-body"><iframe class="iframe" src="index.php?option=com_formmaker&task=preview&format=raw&theme='+document.getElementById('theme').value+'" height="'+(appHeight-200)+"px"+'" width="100%" style="border:0px"></iframe></div>';

});



document.getElementById('modal-preview').style.width=(appWidth-130)+"px";

document.getElementById('modal-preview').style.height=(appHeight-90)+"px";

document.getElementById('modal-preview').style.left="50px";

document.getElementById('modal-preview').style.top="30px";

document.getElementById('modal-preview').style.margin="0px";

</script>





	

	<script src="<?php echo  $cmpnt_js_path ?>/formmaker.js" type="text/javascript" style=""></script>

	<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js'; ?>" type="text/javascript"></script>

	<script src="<?php echo  JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/jquery-ui.js'; ?>" type="text/javascript"></script>

	





<?php		

$bar= JToolBar::getInstance( 'toolbar' );

$bar->appendButton( 'popup', 'preview', 'Preview', 'index.php?option=com_formmaker&task=preview&format=raw', '617', '500' );



}	



public static function show_themes(&$rows, &$pageNav, &$lists){



	JSubMenuHelper::addEntry(JText::_('Forms'), 'index.php?option=com_formmaker&amp;task=forms' );

	JSubMenuHelper::addEntry(JText::_('Submissions'), 'index.php?option=com_formmaker&amp;task=submits' );

	JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_formmaker&amp;task=themes', true );

	JSubMenuHelper::addEntry(JText::_('Blocked IPs'), 'index.php?option=com_formmaker&amp;task=blocked_ips' );

	JSubMenuHelper::addEntry(JText::_('Form Maker Extensions'),'index.php?option=com_formmaker&task=extensions' );

	JSubMenuHelper::addEntry(JText::_('Featured Extensions'),'index.php?option=com_formmaker&task=featured_plugins' );



	JHTML::_('behavior.tooltip');	

	JHtml::_('formbehavior.chosen', 'select');

	$user = JFactory::getUser();

	?>

<script type="text/javascript">

Joomla.tableOrdering= function ( order, dir, task )  {

    var form = document.adminForm;

    form.filter_order_themes.value     = order;

    form.filter_order_Dir_themes.value = dir;

    submitform( task );

}





function SelectAll(obj) { obj.focus(); obj.select(); } 

</script>

	

   

	<form action="index.php?option=com_formmaker" method="post" name="adminForm" id="adminForm">

    

		<table width="100%">

		<tr>

			<td align="left" width="100%">

				<input type="text" name="search_theme" id="search_theme" value="<?php echo $lists['search_theme'];?>" class="text_area"  placeholder="Search theme" style="margin:0px" />

				<button class="btn tip hasTooltip" type="submit" data-original-title="Search"><i class="icon-search"></i></button>

				<button class="btn tip hasTooltip" type="button" onclick="document.id('search_theme').value='';this.form.submit();" data-original-title="Clear">

				<i class="icon-remove"></i></button>

				

				<div class="btn-group pull-right hidden-phone">

					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>

					<?php echo $pageNav->getLimitBox(); ?>

				</div>



			</td>

		</tr>

		</table>    

    

        

    <table class="table table-striped"  width="100%">

    <thead>

    	<tr>            

            <th width="30" class="title"><?php echo "#" ?></td>

			<th width="20"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"></th>

            <th width="30" class="title"><?php echo JHTML::_('grid.sort',   'ID', 'id', @$lists['order_Dir'], @$lists['order'] ); ?></td>

            <th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>

            <th><?php echo JText::_('Default'); ?></th>

        </tr>

    </thead>

	<tfoot>

		<tr>

			<td colspan="11">

			 <?php echo $pageNav->getListFooter(); ?>

			</td>

		</tr>

	</tfoot>

                

    <?php

    $k = 0;

	for($i=0, $n=count($rows); $i < $n ; $i++)

	{

		$row = &$rows[$i];

		$checked 	= JHTML::_('grid.id', $i, $row->id);

		$link 		= JRoute::_( 'index.php?option=com_formmaker&task=edit_themes&cid[]='. $row->id );

?>

        <tr class="<?php echo "row$k"; ?>">

        	<td align="center"><?php echo $i+1?></td>

        	<td><?php echo $checked?></td>

        	<td align="center"><?php echo $row->id?></td>

			<?php if($user->authorise('core.edit', 'com_formmaker')): ?>

            <td align="center"><a href="<?php echo $link; ?>"><?php echo $row->title?></a></td>

			<?php else: ?>

			<td align="center"><?php echo $row->title?></td>

			<?php endif; ?>           

			<td align="center">

				<?php if ( $row->default == 1 ) : ?>

				<i class="icon-star"></i>

				<?php else : ?>

				&nbsp;

				<?php endif; ?>

			</td>

       </tr>

        <?php

		$k = 1 - $k;

	}

	?>

    </table>

	

    <input type="hidden" name="option" value="com_formmaker">

    <input type="hidden" name="task" value="themes">    

    <input type="hidden" name="boxchecked" value="0"> 

    <input type="hidden" name="filter_order_themes" value="<?php echo $lists['order']; ?>" />

    <input type="hidden" name="filter_order_Dir_themes" value="<?php echo $lists['order_Dir']; ?>" />       

    </form>



<?php

}



public static function add_themes($def_theme){



		JRequest::setVar( 'hidemainmenu', 1 );

		

		?>

        

<script>



Joomla.submitbutton= function (pressbutton) {

	

	var form = document.adminForm;

	

	if (pressbutton == 'cancel_themes') 

	{

		submitform( pressbutton );

		return;

	}

	if(form.title.value=="")

	{

		alert('Set Theme title');

		return;

	}



	submitform( pressbutton );

}





</script>        

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >

<table class="admintable" >



 

				<tr>

					<td class="key">

						<label for="title">

							Title of theme:

						</label>

					</td>

					<td >

                                    <input type="text" name="title" id="title" size="80"/>

					</td>

				</tr>

				<tr>

					<td class="key" valign="top">

						<label for="title">

							Css:

						</label>

					</td>

					<td >

                                    <textarea name="css" id="css" rows=30 style="width:500px"><?php echo $def_theme->css ?></textarea>

					</td>

				</tr>

</table>           

    <input type="hidden" name="option" value="com_formmaker" />

    <input type="hidden" name="task" value="" />

</form>



	   <?php	

	

}



public static function edit_blocked_ips(&$row){

JRequest::setVar( 'hidemainmenu', 1 );



		

		?>

        

<script>

function check_isnum_point(e)

{

   	var chCode1 = e.which || e.keyCode;

	

	if (chCode1 ==46)

		return true;

	

	if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57))

        return false;

	return true;

}



function submitbutton(pressbutton) {

	

	var form = document.adminForm;

	

	if (pressbutton == 'cancel_blocked_ips') 

	{

		submitform( pressbutton );

		return;

	}





	submitform( pressbutton );

}





</script>        

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<table class="admintable">



 

				<tr>

					<td class="key">

						<label for="title">

							IP:

						</label>

					</td>

					<td >

                                    <input type="text" name="ip" id="ip" value="<?php echo htmlspecialchars($row->ip) ?>" onkeypress="return check_isnum_point(event);" size="60"/>

					</td>

				</tr>

				

</table>           

    <input type="hidden" name="option" value="com_formmaker" />

	<input type="hidden" name="id" value="<?php echo $row->id?>" />        

	<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />        

	<input type="hidden" name="task" value="" />        

</form>



	   <?php	





}



public static function edit_themes(&$row){



		JRequest::setVar( 'hidemainmenu', 1 );

		

		?>

        

<script>



Joomla.submitbutton= function (pressbutton) {

	

	var form = document.adminForm;

	

	if (pressbutton == 'cancel_themes') 

	{

		submitform( pressbutton );

		return;

	}

	if(form.title.value=="")

	{

		alert('Set Theme title');

		return;

	}



	submitform( pressbutton );

}





</script>        

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >

<table class="admintable" >



 

				<tr>

					<td class="key">

						<label for="title">

							Title of theme:

						</label>

					</td>

					<td >

                        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($row->title) ?>" size="80"/>

					</td>

				</tr>

				<tr>

					<td class="key" valign="top">

						<label for="title">

							Css:

						</label>

					</td>

					<td >

                        <textarea name="css" id="css" rows=30 style="width:500px"><?php echo htmlspecialchars($row->css) ?></textarea>

					</td>

				</tr>

</table>           

    <input type="hidden" name="option" value="com_formmaker" />

	<input type="hidden" name="id" value="<?php echo $row->id?>" />        

	<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />        

	<input type="hidden" name="task" value="" />        

</form>



	   <?php	

	

}



/////////////////////////////////////////////////////////////////////// THEME /////////////////////////////////





















	

public static function editSubmit($rows, $labels_id ,$labels_name,$labels_type, $ispaypal,$form,$form_theme){

JRequest::setVar( 'hidemainmenu', 1 );

$editor	= JFactory::getEditor();

		JHTML::_('behavior.tooltip');	

		JHTML::_('behavior.calendar');



		?>

<form action="index.php" method="post" name="adminForm"  id="adminForm">

<table class="admintable" style="border-spacing:5px; border-collapse: separate;">

				<tr>

					<td class="key">

						<label for="ID">

							<?php echo JText::_( 'ID' ); ?>:

						</label>

					</td>

					<td >

                       <?php echo $rows[0]->group_id;?>

					</td>

				</tr>

                

                <tr>

					<td class="key">

						<label for="Date">

							<?php echo JText::_( 'Date' ); ?>:

						</label>

					</td>

					<td >

                       <?php echo $rows[0]->date;?>

					</td>

				</tr>

                <tr>

					<td class="key">

						<label for="IP">

							<?php echo JText::_( 'IP' ); ?>:

						</label>

					</td>

					<td >

                      <?php 

					   echo $rows[0]->ip; ?>

					</td>

                </tr>	

				<tr>

					<td class="key">

						<label for="">

							<?php echo JText::_( 'Submitter\'s Username' ); ?>:

						</label>

					</td>

					<td >

                      <?php 

					  $user_id = $rows[0]->user_id; 

					   echo JFactory::getUser($user_id)->username; ?>

					</td>

                </tr>	

				<tr>

					<td class="key">

						<label for="">

							<?php echo JText::_( 'Submitter\'s Email Address' ); ?>:

						</label>

					</td>

					<td >

                      <?php 

					 $user_id = $rows[0]->user_id; 

					   echo JFactory::getUser($user_id)->email; ?>

					</td>

                </tr>	

	  </table>              

<?php 



		$input_get = JFactory::getApplication()->input;

		$document = JFactory::getDocument();

		$db = JFactory::getDBO();

	

		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js');

		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/jquery-ui.js');

		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/noconflict.js');

		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/if_gmap.js');

		$document->addScript( JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/min.js');

		$document->addScript( JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/main_div.js');

		$document->addScript( JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/file-upload.js');

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
			$document->addScript('https://maps.google.com/maps/api/js?sensor=false');
		else	
			$document->addScript('http://maps.google.com/maps/api/js?sensor=false');
		$document->addStyleSheet(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/jquery-ui-spinner.css');



		$is_editor=false;

	

		$plugin = JPluginHelper::getPlugin('editors', 'tinymce');

		if (isset($plugin->type))

		{ 

			$editor	= JFactory::getEditor('tinymce');

			$is_editor=true;

		}



		$editor	= JFactory::getEditor('tinymce');

		

		$css_rep1=array("[SITE_ROOT]");

		$css_rep2=array(JURI::root(true));

		$order   = array("\r\n", "\n", "\r");

		$form_theme=str_replace($order,'',$form_theme);

		$form_theme=str_replace($css_rep1,$css_rep2,$form_theme);

		$form_theme="#form".($form->id).' '.$form_theme;



		echo '<style>'.$form_theme.'



		.wdform-page-and-images{

		width: 50%;

		}

		

		img

		{

		max-width:none;

		}



		.mini_label

		{

		display: inline;

		}

		

		.am_pm_select

		{

		width:62px !important;

		vertical-align: middle;

		}

		</style>';

		

		$form_currency='$';

		$check_js='';

		$onload_js='';

		$onsubmit_js='';

		

		$is_type	= array();

		$id1s	 	= array();

		$types 		= array();

		$labels 	= array();

		$paramss 	= array();

		

		$fields=explode('*:*new_field*:*',$form->form_fields);

		$fields 	= array_slice($fields,0, count($fields)-1);   

	

		foreach($fields as $field)

		{

			$temp=explode('*:*id*:*',$field);

			array_push($id1s, $temp[0]);

			$temp=explode('*:*type*:*',$temp[1]);

			array_push($types, $temp[0]);

			$temp=explode('*:*w_field_label*:*',$temp[1]);

			array_push($labels, $temp[0]);

			array_push($paramss, $temp[1]);

		}

	

		$form=$form->form_front;

		$form_id = 'form_id_temp';
	

		foreach($id1s as $id1s_key => $id1)

		{	

			$label=$labels[$id1s_key];

			$type=$types[$id1s_key];

			$params=$paramss[$id1s_key];

			if($type!='type_address')
			{
				foreach($rows as $row)
				{
					if($row->element_label==$id1)
					{		
						$element_value=	$row->element_value;
						break;
					}
					else
					{
						$element_value=	'';
					}
				}
			}
			else
			{
				for($i=0; $i<6; $i++)
				{
					$address_value = '';
				
					foreach($rows as $row)
					{
						if($row->element_label==(string)((int)$id1+$i))	
							$address_value = $row->element_value;
					}
					
					$elements_of_address[$i] =	$address_value;
				}
			}		



			if( strpos($form, '%'.$id1.' - '.$label.'%'))

			{

				$rep='';

				$required=false;

				$param=array();

				$param['attributes'] = '';

				$is_type[$type]=true;

				

				switch($type)

				{

					case 'type_section_break':

					case 'type_editor':

					case 'type_file_upload':

					case 'type_captcha':		

					case 'type_recaptcha':

					case 'type_mark_map':	

					case 'type_map':

					case 'type_submit_reset':

					case 'type_button':

					case 'type_paypal_total':

					break;

					

					case 'type_text':

					{

					

							$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}



							

							$wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] : max($param['w_field_label_size'],$param['w_size']));	

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							



							$rep ='<div type="type_text" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section" style="width: '.$param['w_size'].'px;"><input type="text" class="" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$element_value.'"  style="width: 100%" '.$param['attributes'].'></div></div>';

							

						

						break;

						

					}



					case 'type_number':

					{

					

					

							$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique','w_class');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

						

							$wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] : max($param['w_field_label_size'],$param['w_size']));	

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							

											

							$rep ='<div type="type_number" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section"  class="'.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><input type="text" class="" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$element_value.'" style="width:100%;" '.$param['attributes'].'></div></div>';

						

						

						break;

					}



					case 'type_password':

					{

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_required','w_unique','w_class');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

					

							$wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] : max($param['w_field_label_size'],$param['w_size']));	

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							



							$rep ='<div type="type_password" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section"  class="'.$param['w_class'].'" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><input type="password" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$element_value.'" style="width: 100%;" '.$param['attributes'].'></div></div>';

							

						

						break;

					}



					case 'type_textarea':

					{

					

					

							$params_names=array('w_field_label_size','w_field_label_pos','w_size_w','w_size_h','w_first_val','w_title','w_required','w_unique','w_class');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

						

							

							$wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size_w'] : max($param['w_field_label_size'],$param['w_size_w']));	

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							

							$rep ='<div type="type_textarea" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size_w'].'px"><textarea class="" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'"  style="width: 100%; height: '.$param['w_size_h'].'px;" '.$param['attributes'].'>'.$element_value.'</textarea></div></div>';



						

						break;

					}

					

					case 'type_phone':

					{

					

						if($element_value=='')

							$element_value = ' ';

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_mini_labels','w_required','w_unique', 'w_class');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							

							

							$element_value = explode(' ',$element_value);

							

							$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']+65) : max($param['w_field_label_size'],($param['w_size']+65)));	

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							

							$w_mini_labels = explode('***',$param['w_mini_labels']);

					

							$rep ='<div type="type_phone" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label" >'.$label.'</span>';

							

							$rep.='

							</div>

							<div class="wdform-element-section '.$param['w_class'].'" style="width: '.($param['w_size']+65).'px;">

								<div style="display: table-cell;vertical-align: middle;">

									<div><input type="text" class="" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.$element_value[0].'"  style="width: 50px;" '.$param['attributes'].'></div>

									<div><label class="mini_label">'.$w_mini_labels[0].'</label></div>

								</div>

								<div style="display: table-cell;vertical-align: middle;">

									<div class="wdform_line" style="margin: 0px 4px 10px 4px; padding: 0px;">-</div>

								</div>

								<div style="display: table-cell;vertical-align: middle; width:100%; min-width: 100px;">

									<div><input type="text" class="" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.$element_value[1].'" style="width: 100%;" '.$param['attributes'].'></div>

									<div><label class="mini_label">'.$w_mini_labels[1].'</label></div>

								</div>

							</div>

							</div>';

						

						

						break;

					}



					case 'type_name':

					{

						if($element_value =='')

							$element_value = '@@@';

							

							$params_names=array('w_field_label_size','w_field_label_pos','w_first_val','w_title', 'w_mini_labels','w_size','w_name_format','w_required','w_unique', 'w_class');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							

							

							$w_mini_labels = explode('***',$param['w_mini_labels']);

						

							$element_value = explode('@@@',$element_value);



							

							if($param['w_name_format']=='normal')

							{

								$w_name_format = '

								<div style="display: table-cell; width:50%">

									<div><input type="text" class="" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.(count($element_value)==2 ? $element_value[0] : $element_value[1]).'" style="width: 100%;"'.$param['attributes'].'></div>

									<div><label class="mini_label">'.$w_mini_labels[1].'</label></div>

								</div>

								<div style="display:table-cell;"><div style="margin: 0px 8px; padding: 0px;"></div></div>

								<div  style="display: table-cell; width:50%">

									<div><input type="text" class="" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.(count($element_value)==2 ? $element_value[1] : $element_value[2]).'" style="width: 100%;" '.$param['attributes'].'></div>

									<div><label class="mini_label">'.$w_mini_labels[2].'</label></div>

								</div>

								';

								$w_size=2*$param['w_size'];



							}

							else

							{

								$w_name_format = '

								<div style="display: table-cell;">

									<div><input type="text" class="" id="wdform_'.$id1.'_element_title'.$form_id.'" name="wdform_'.$id1.'_element_title'.$form_id.'" value="'.(count($element_value)==2 ? "" : $element_value[0]).'" style="width: 40px;"></div>

									<div><label class="mini_label">'.$w_mini_labels[0].'</label></div>

								</div>

								<div style="display:table-cell;"><div style="margin: 0px 1px; padding: 0px;"></div></div>

								<div style="display: table-cell; width:30%">

									<div><input type="text" class="" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.(count($element_value)==2 ? $element_value[0] : $element_value[1]).'"  style="width:100%;"></div>

									<div><label class="mini_label">'.$w_mini_labels[1].'</label></div>

								</div>

								<div style="display:table-cell;"><div style="margin: 0px 4px; padding: 0px;"></div></div>

								<div style="display: table-cell; width:30%">

									<div><input type="text" class="" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.(count($element_value)==2 ? $element_value[1] : $element_value[2]).'" style="width:  100%;"></div>

									<div><label class="mini_label">'.$w_mini_labels[2].'</label></div>

								</div>

								<div style="display:table-cell;"><div style="margin: 0px 4px; padding: 0px;"></div></div>

								<div style="display: table-cell; width:30%">

									<div><input type="text" class="" id="wdform_'.$id1.'_element_middle'.$form_id.'" name="wdform_'.$id1.'_element_middle'.$form_id.'" value="'.(count($element_value)==2 ? "" : $element_value[3]).'"  style="width: 100%;"></div>

									<div><label class="mini_label">'.$w_mini_labels[3].'</label></div>

								</div>						

								';

								$w_size=3*$param['w_size']+80;

							}

				

							$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$w_size) : max($param['w_field_label_size'],$w_size));	

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	



							$rep ='<div type="type_name" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$w_size.'px;">'.$w_name_format.'</div></div>';



						

						break;

					}

					

				case 'type_address':
					{
						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_mini_labels','w_disabled_fields','w_required','w_class');
						$temp=$params;

						foreach($params_names as $params_name )
						{	
							$temp=explode('*:*'.$params_name.'*:*',$temp);
							$param[$params_name] = $temp[0];
							$temp=$temp[1];
						}
						
						
						if($temp)
						{	
							$temp	=explode('*:*w_attr_name*:*',$temp);
							$attrs	= array_slice($temp,0, count($temp)-1);   
							foreach($attrs as $attr)
								$param['attributes'] = $param['attributes'].' '.$attr;
						}
						
					
						$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
						$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
						
						$w_mini_labels = explode('***',$param['w_mini_labels']);
						$w_disabled_fields = explode('***',$param['w_disabled_fields']);
					

						$rep ='<div type="type_address" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
						
							
							
							
				
						$address_fields ='';
						$g=0;
						if($w_disabled_fields[0]=='no')
						{
						$g+=2;
						$address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="wdform_'.$id1.'_street1'.$form_id.'" name="wdform_'.$id1.'_street1'.$form_id.'" value="'.$elements_of_address[0].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[0].'</label></span>';
						}
						
						if($w_disabled_fields[1]=='no')
						{
						$g+=2;
						$address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="wdform_'.$id1.'_street2'.$form_id.'" name="wdform_'.($id1+1).'_street2'.$form_id.'" value="'.$elements_of_address[1].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[1].'</label></span>';
						}
						
						if($w_disabled_fields[2]=='no')
						{
						$g++;
						$address_fields .= '<span style="float: left; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_city'.$form_id.'" name="wdform_'.($id1+2).'_city'.$form_id.'" value="'.$elements_of_address[2].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[2].'</label></span>';
						}
						if($w_disabled_fields[3]=='no')
						{
						$g++;
						
						
						$w_states = array("","Alabama","Alaska", "Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District Of Columbia","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming");	
						$w_state_options = '';
						foreach($w_states as $w_state)
						{
						
						if($w_state == $elements_of_address[3])					
						$selected = 'selected=\"selected\"';
						else
						$selected = '';
						$w_state_options .= '<option value="'.$w_state.'" '.$selected.'>'.$w_state.'</option>';
						}
						if($w_disabled_fields[5]=='yes' && $w_disabled_fields[6]=='yes')
						{
						$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><select type="text" id="wdform_'.$id1.'_state'.$form_id.'" name="wdform_'.($id1+3).'_state'.$form_id.'" style="width: 100%;" '.$param['attributes'].'>'.$w_state_options.'</select><label class="mini_label" style="display: block;" id="'.$id1.'_mini_label_state">'.$w_mini_labels[3].'</label></span>';
						}
						else
						$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_state'.$form_id.'" name="wdform_'.($id1+3).'_state'.$form_id.'" value="'.$elements_of_address[3].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[3].'</label></span>';
						}
						if($w_disabled_fields[4]=='no')
						{
						$g++;
						$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_postal'.$form_id.'" name="wdform_'.($id1+4).'_postal'.$form_id.'" value="'.$elements_of_address[4].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[4].'</label></span>';
						}
						$w_countries = array("","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");	
						$w_options = '';
						foreach($w_countries as $w_country)
						{
							if($w_country == $elements_of_address[5])					
								$selected = 'selected="selected"';
							else
								$selected = '';
							$w_options .= '<option value="'.$w_country.'" '.$selected.'>'.$w_country.'</option>';
						}
					
						if($w_disabled_fields[5]=='no')
						{
						$g++;
						$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;display: inline-block;"><select type="text" id="wdform_'.$id1.'_country'.$form_id.'" name="wdform_'.($id1+5).'_country'.$form_id.'" style="width:100%" '.$param['attributes'].'>'.$w_options.'</select><label class="mini_label">'.$w_mini_labels[5].'</span>';
						}				

					
						$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><div>
						'.$address_fields.'</div></div></div>';
						
							
						break;
					}


					case 'type_submitter_mail':

					{

					

							$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique', 'w_class');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							

								

							$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							



							$rep ='<div type="type_submitter_mail" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><input type="text" class="" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$element_value.'"   style="width: 100%;" '.$param['attributes'].'></div></div>';

							

									

							$check_js.='

							if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0)

							{

							

							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()!="" && wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val().search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1 )

								{

									alert("'.JText::_("WDF_INVALID_EMAIL").'");

									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");

									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });

									wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();

									return false;

								}

							

							}

							';		

					

						break;

					}



					case 'type_checkbox':

					{

					

						$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');

						$temp=$params;

						if(strpos($temp, 'w_field_option_pos') > -1)

							$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_value_disabled','w_choices_value', 'w_choices_params', 'w_class');

							

						foreach($params_names as $params_name )

						{	

							$temp=explode('*:*'.$params_name.'*:*',$temp);

							$param[$params_name] = $temp[0];

							$temp=$temp[1];

						}

						

						if($temp)

						{	

							$temp	=explode('*:*w_attr_name*:*',$temp);

							$attrs	= array_slice($temp,0, count($temp)-1);   

							foreach($attrs as $attr)

								$param['attributes'] = $param['attributes'].' '.$attr;

						}

						

						$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

						if(!isset($param['w_value_disabled']))

						$param['w_value_disabled'] = 'no';

						

						if(!isset($param['w_field_option_pos']))

						$param['w_field_option_pos'] = 'left';

						

						$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");

						$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");	

						

						$param['w_choices']	= explode('***',$param['w_choices']);

						if(isset($param['w_choices_value']))

						{

							$param['w_choices_value'] = explode('***',$param['w_choices_value']);

							$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

						}

						

						$element_value	= explode('***br***',$element_value);

						$element_value 	= array_slice($element_value,0, count($element_value)-1); 

						$is_other=false;

						$other_value = '';



						foreach($element_value as $key => $value)

						{

							if(!in_array($value, ($param['w_value_disabled']=='no' ? $param['w_choices'] : (isset($param['w_choices_value']) ? $param['w_choices_value'] : array()))))

							{

								$other_value = $value;

								$is_other=true;

								break;

							}

						}



						$rep='<div type="type_checkbox" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

						$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';">';

					

						$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).'; vertical-align:top">';

						$total_queries = 0;

						foreach($param['w_choices'] as $key => $choice)

						{	

							$key1 = $key + $total_queries;

							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

							{	

							$choices_labels =array();
							$choices_values = array();

							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
						
							$db = JFactory::getDBO();
							if($db_info)
							{
								$temp		= explode('@@@wdfhostwdf@@@',$db_info);
								$host		= $temp[0];
								$temp		= explode('@@@wdfportwdf@@@',$temp[1]);
								$port		= $temp[0];
								$temp		= explode('@@@wdfusernamewdf@@@',$temp[1]);
								$username	= $temp[0];
								$temp		= explode('@@@wdfpasswordwdf@@@',$temp[1]);
								$password	= $temp[0];
								$temp		= explode('@@@wdfdatabasewdf@@@',$temp[1]);
								$database	= $temp[0];
								
								$remote = array();

								$remote['driver']   = 'mysql';           
								$remote['host']     = $host;
								$remote['user']     = $username;
								$remote['password'] = $password;
								$remote['database'] = $database;
								$remote['prefix']   = '';             
								 
								$db = JDatabase::getInstance( $remote );
							}
		

								$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

								$table = $label_table_and_column[0];

								$label_column = $label_table_and_column[1];

								if($label_column)

								{

									$db->setQuery("SELECT `".$label_column."` FROM ".$table.$where." ORDER BY ".$order_by); 

									$choices_labels = $db->loadColumn();					

									if ($db->getErrorNum())	{echo $db->stderr(); return false;}						

								}	



								$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));

								$value_column = $value_table_and_column[1];



								if($value_column)

								{

									$db->setQuery("SELECT `".$value_column."` FROM ".$table.$where." ORDER BY ".$order_by); 

									$choices_values = $db->loadColumn();

									if ($db->getErrorNum())	{echo $db->stderr(); return false;}

								}		

								$columns_count_checkbox = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

								

								if(array_filter($choices_labels) || array_filter($choices_values))

								{

									$total_queries = $total_queries + $columns_count_checkbox-1;

									

									if(!isset($post_value))

										$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');

										

									for($k=0; $k<$columns_count_checkbox; $k++)

									{

										$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';

										$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : $choice_label;

											

										if(($key1+$k)%$param['w_rowcol']==0 && ($key1+$k)>0)

											$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

										

										$checked=(in_array($choice_value, $element_value) ? 'checked="checked"' : '');

										

										$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" value="'.htmlspecialchars($choice_value).'" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'onclick="if(set_checked(&quot;wdform_'.$id1.'&quot;,&quot;'.($key1+$k).'&quot;,&quot;'.$form_id.'&quot;)) show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);"' : '').' '.$param['attributes'].' '.$checked.'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';

										

									}

								}	

							}	

							else

							{

								if($key1%$param['w_rowcol']==0 && $key1>0)

									$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

									

								$checked=(in_array($choice, $element_value) ? 'checked="checked"' : '');

								

								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key && $is_other)

									$checked = 'checked="checked"';	

									

								

								$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.''.$key1.'" value="'.htmlspecialchars($choice).'" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'onclick="if(set_checked(&quot;wdform_'.$id1.'&quot;,&quot;'.$key1.'&quot;,&quot;'.$form_id.'&quot;)) show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);"' : '').' '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';

								

								$param['w_allow_other_num'] = $param['w_allow_other_num']==$key ? $key1 : $param['w_allow_other_num'];

							}	

						}

						$rep.='</div>';



						$rep.='</div></div>';

						

						

						if($is_other)

							$onload_js .='show_other_input("wdform_'.$id1.'","'.$form_id.'"); wdformjQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val("'.$other_value.'");';

						

						$onsubmit_js.='

							wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other'.$form_id.'\" value = \"'.$param['w_allow_other'].'\" />").appendTo("#adminForm");

							wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other_num'.$form_id.'\" value = \"'.$param['w_allow_other_num'].'\" />").appendTo("#adminForm");

							';

							

						break;

					}



					case 'type_radio':

					{

					

						

						$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');

						$temp=$params;

						if(strpos($temp, 'w_field_option_pos') > -1)

						$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_value_disabled','w_choices_value', 'w_choices_params','w_class');

						

						foreach($params_names as $params_name )

						{	

							$temp=explode('*:*'.$params_name.'*:*',$temp);

							$param[$params_name] = $temp[0];

							$temp=$temp[1];

						}

						

						if($temp)

						{	

							$temp	=explode('*:*w_attr_name*:*',$temp);

							$attrs	= array_slice($temp,0, count($temp)-1);   

							foreach($attrs as $attr)

								$param['attributes'] = $param['attributes'].' '.$attr;

						}

						if(!isset($param['w_value_disabled']))

							$param['w_value_disabled'] = 'no';

					

						if(!isset($param['w_field_option_pos']))

							$param['w_field_option_pos'] = 'left';

						

						$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

						$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");

						$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");

					

						$param['w_choices']	= explode('***',$param['w_choices']);

						if(isset($param['w_choices_value']))

						{

							$param['w_choices_value'] = explode('***',$param['w_choices_value']);

							$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

						}

						

						$is_other=true;



						foreach($param['w_choices'] as $key => $choice)

						{

							$choice_value = isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice;	

							if($choice_value==$element_value)

							{

								$is_other=false;

								break;

							}

						}	

											

						$rep='<div type="type_radio" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

						$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';">';

					

						$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).'; vertical-align:top">';

						$total_queries =0;

						foreach($param['w_choices'] as $key => $choice)

						{	

							$key1 = $key + $total_queries;

							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

							{	

								$choices_labels =array();	
							$choices_values =array();	
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
							
							$db = JFactory::getDBO();
							if($db_info)
							{
								$temp		= explode('@@@wdfhostwdf@@@',$db_info);
								$host		= $temp[0];
								$temp		= explode('@@@wdfportwdf@@@',$temp[1]);
								$port		= $temp[0];
								$temp		= explode('@@@wdfusernamewdf@@@',$temp[1]);
								$username	= $temp[0];
								$temp		= explode('@@@wdfpasswordwdf@@@',$temp[1]);
								$password	= $temp[0];
								$temp		= explode('@@@wdfdatabasewdf@@@',$temp[1]);
								$database	= $temp[0];
								
								$remote = array();

								$remote['driver']   = 'mysql';           
								$remote['host']     = $host;
								$remote['user']     = $username;
								$remote['password'] = $password;
								$remote['database'] = $database;
								$remote['prefix']   = '';             
								 
								$db = JDatabase::getInstance( $remote );
							}

								$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

								$table = $label_table_and_column[0];

								$label_column = $label_table_and_column[1];

								if($label_column)

								{

									$db->setQuery("SELECT `".$label_column."` FROM ".$table.$where." ORDER BY ".$order_by); 

									$choices_labels = $db->loadColumn();					

									if ($db->getErrorNum())	{echo $db->stderr(); return false;}						

								}	



								$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));

								$value_column = $value_table_and_column[1];



								if($value_column)

								{

									$db->setQuery("SELECT `".$value_column."` FROM ".$table.$where." ORDER BY ".$order_by); 

									$choices_values = $db->loadColumn();

									if ($db->getErrorNum())	{echo $db->stderr(); return false;}

								}	

								

								$columns_count_radio = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

								if(array_filter($choices_labels) || array_filter($choices_values))

								{

									$total_queries = $total_queries + $columns_count_radio-1;

				

									for($k=0; $k<$columns_count_radio; $k++)

									{

										$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';

										$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : $choice_label;

											

										if(($key1+$k)%$param['w_rowcol']==0 && ($key1+$k)>0)

											$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';



										$checked =($choice_value==$element_value ? 'checked="checked"' : '');

										if($choice_value==$element_value)

											$is_other=false;

										

										$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.htmlspecialchars($choice_value).'" onclick="set_default(&quot;wdform_'.$id1.'&quot;,&quot;'.($key1+$k).'&quot;,&quot;'.$form_id.'&quot;); '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);' : '').'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';

					

									}

								}	

							}	

							else

							{

								if($key1%$param['w_rowcol']==0 && $key1>0)

									$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

								

								$checked =($choice==$element_value ? 'checked="checked"' : '');

								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key && $is_other==true && $element_value!='')

									$checked = 'checked="checked"';

								$choice_value = isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice;

								

								$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.htmlspecialchars($choice_value).'" onclick="set_default(&quot;wdform_'.$id1.'&quot;,&quot;'.$key1.'&quot;,&quot;'.$form_id.'&quot;); '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);' : '').'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';

								

								$param['w_allow_other_num'] = $param['w_allow_other_num']==$key ? $key1 : $param['w_allow_other_num'];

							}	

						}

								$rep.='</div>';



						$rep.='</div></div>';

					

						

						if($is_other && $element_value!='') 

							$onload_js .='show_other_input("wdform_'.$id1.'","'.$form_id.'"); wdformjQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val("'.$element_value.'");';

						

						$onsubmit_js.='

							wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other'.$form_id.'\" value = \"'.$param['w_allow_other'].'\" />").appendTo("#form'.$form_id.'");

							wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other_num'.$form_id.'\" value = \"'.$param['w_allow_other_num'].'\" />").appendTo("#adminForm");

							';

						

						break;

					}



					case 'type_own_select':

					{

					

						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_checked', 'w_choices_disabled','w_required','w_class');

						$temp=$params;

						if(strpos($temp, 'w_choices_value') > -1)

						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_checked', 'w_choices_disabled', 'w_required', 'w_value_disabled', 'w_choices_value', 'w_choices_params', 'w_class');

						

						foreach($params_names as $params_name )

						{	

							$temp=explode('*:*'.$params_name.'*:*',$temp);

							$param[$params_name] = $temp[0];

							$temp=$temp[1];

						}

						

						

						if($temp)

						{	

							$temp	=explode('*:*w_attr_name*:*',$temp);

							$attrs	= array_slice($temp,0, count($temp)-1);   

							foreach($attrs as $attr)

								$param['attributes'] = $param['attributes'].' '.$attr;

						}

						

					

						$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	

						$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	

						$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");

					

						$param['w_choices']	= explode('***',$param['w_choices']);

						$param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);

						if(isset($param['w_choices_value']))

						{

							$param['w_choices_value'] = explode('***',$param['w_choices_value']);

							$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

						}

						

						if(!isset($param['w_value_disabled']))

						$param['w_value_disabled'] = 'no';

						

						$rep='<div type="type_own_select" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

						$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.($param['w_size']).'px; "><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;"  '.$param['attributes'].'>';

						foreach($param['w_choices'] as $key => $choice)

						{

							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

							{

								$choices_labels =array();
							$choices_values = array();
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
							
							$db = JFactory::getDBO();
							if($db_info)
							{
								$temp		= explode('@@@wdfhostwdf@@@',$db_info);
								$host		= $temp[0];
								$temp		= explode('@@@wdfportwdf@@@',$temp[1]);
								$port		= $temp[0];
								$temp		= explode('@@@wdfusernamewdf@@@',$temp[1]);
								$username	= $temp[0];
								$temp		= explode('@@@wdfpasswordwdf@@@',$temp[1]);
								$password	= $temp[0];
								$temp		= explode('@@@wdfdatabasewdf@@@',$temp[1]);
								$database	= $temp[0];
								
								$remote = array();

								$remote['driver']   = 'mysql';           
								$remote['host']     = $host;
								$remote['user']     = $username;
								$remote['password'] = $password;
								$remote['database'] = $database;
								$remote['prefix']   = '';             
								 
								$db = JDatabase::getInstance( $remote );
							}
							
								$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

								$table = $label_table_and_column[0];

								$label_column = $label_table_and_column[1];

								if($label_column)

								{

									$db->setQuery("SELECT `".$label_column."` FROM ".$table.$where." ORDER BY ".$order_by); 

									$choices_labels = $db->loadColumn();					

									if ($db->getErrorNum())	{echo $db->stderr(); return false;}						

								}	



								$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));

								$value_column = $param['w_choices_disabled'][$key]=="true" ? '' : $value_table_and_column[1];



								if($value_column)

								{

									$db->setQuery("SELECT `".$value_column."` FROM ".$table.$where." ORDER BY ".$order_by); 

									$choices_values = $db->loadColumn();

									if ($db->getErrorNum())	{echo $db->stderr(); return false;}

								}	



								$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

								if(array_filter($choices_labels) || array_filter($choices_values))

									for($k=0; $k<$columns_count; $k++)

									{

										$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';

										$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : ($param['w_choices_disabled'][$key]=="true" ? '' : $choice_label);

									

										$selected=($element_value && htmlspecialchars($choice_value)==htmlspecialchars($element_value) ? 'selected="selected"' : '');



										$rep.='<option value="'.htmlspecialchars($choice_value).'" '.$selected.'>'.$choice_label.'</option>';

													

									}		

							}

							else

							{		

								$choice_value = $param['w_choices_disabled'][$key]=="true" ? '' : (isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice);

								

								$selected=($element_value && htmlspecialchars($choice_value)==htmlspecialchars($element_value) ? 'selected="selected"' : '');

	

								$rep.='<option value="'.htmlspecialchars($choice_value).'" '.$selected.'>'.$choice.'</option>';

							}  

						}

						$rep.='</select></div></div>';

						

						

						break;

					}

					

					case 'type_country':

					{

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_countries','w_required','w_class');

							$temp=$params;

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' add_'.$attr;

							}



							$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

						

							$param['w_countries']	= explode('***',$param['w_countries']);

												

							$selected='';



							$rep='<div type="type_country" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;"  '.$param['attributes'].'>';

							foreach($param['w_countries'] as $key => $choice)

							{

								

								$selected=(htmlspecialchars($choice)==htmlspecialchars($element_value) ? 'selected="selected"' : '');



								$choice_value=$choice;

								$rep.='<option value="'.$choice_value.'" '.$selected.'>'.$choice.'</option>';

							}

							$rep.='</select></div></div>';

							

						

						break;

					}

					

					case 'type_time':

					{

						if($element_value =='')

							$element_value = ':';

							

							$params_names=array('w_field_label_size','w_field_label_pos','w_time_type','w_am_pm','w_sec','w_hh','w_mm','w_ss','w_mini_labels','w_required','w_class');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

						

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							

							$w_mini_labels = explode('***',$param['w_mini_labels']);

							$element_value = explode(':',$element_value);

							

							$w_sec = '';

							$w_sec_label='';

						

							if($param['w_sec']=='1')

							{

								$w_sec = '<div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div style="display: table-cell;"><input type="text" value="'.(count($element_value)==2 ? '' : substr($element_value[2],0,strpos($element_value[2],' '))).'" class="time_box" id="wdform_'.$id1.'_ss'.$form_id.'" name="wdform_'.$id1.'_ss'.$form_id.'" onkeypress="return check_second(event, &quot;wdform_'.$id1.'_ss'.$form_id.'&quot;)" '.$param['attributes'].'></div>';

								

								$w_sec_label='<div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[2].'</label></div>';

							}



							

							if($param['w_time_type']=='12')

							{		

				

								if(strpos($element_value[2],'pm')!==false)

								{

									$am_ = "";

									$pm_ = "selected=\"selected\"";	

								}	

								else

								{

									$am_ = "selected=\"selected\"";

									$pm_ = "";	

								}	

							

							$w_time_type = '<div style="display: table-cell;"><select class="am_pm_select" name="wdform_'.$id1.'_am_pm'.$form_id.'" id="wdform_'.$id1.'_am_pm'.$form_id.'" '.$param['attributes'].'><option value="am" '.$am_.'>AM</option><option value="pm" '.$pm_.'>PM</option></select></div>';

							

							$w_time_type_label = '<div ><label class="mini_label">'.$w_mini_labels[3].'</label></div>';

							

							}

							else

							{

								$w_time_type='';

								$w_time_type_label = '';

							}

							

							

							

							$rep ='<div type="type_time" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;"><input type="text" value="'.$element_value[0].'" class="time_box" id="wdform_'.$id1.'_hh'.$form_id.'" name="wdform_'.$id1.'_hh'.$form_id.'" onkeypress="return check_hour(event, &quot;wdform_'.$id1.'_hh'.$form_id.'&quot;, &quot;23&quot;)" '.$param['attributes'].'></div><div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div style="display: table-cell;"><input type="text" value="'.$element_value[1].'" class="time_box" id="wdform_'.$id1.'_mm'.$form_id.'" name="wdform_'.$id1.'_mm'.$form_id.'" onkeypress="return check_minute(event, &quot;wdform_'.$id1.'_mm'.$form_id.'&quot;)" '.$param['attributes'].'></div>'.$w_sec.$w_time_type.'</div><div style="display: table-row;"><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[0].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[1].'</label></div>'.$w_sec_label.$w_time_type_label.'</div></div></div></div>';

							

								

						break;

					}



					case 'type_date':

					{

					

							$params_names=array('w_field_label_size','w_field_label_pos','w_date','w_required','w_class','w_format','w_but_val');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							

						

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							

						

							$rep ='<div type="type_date" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';"><input type="text" value="'.$element_value.'" class="wdform-date" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" maxlength="10" '.$param['attributes'].'><input id="wdform_'.$id1.'_button'.$form_id.'" class="wdform-calendar-button" type="reset" value="'.$param['w_but_val'].'" format="'.$param['w_format'].'" alt="calendar" '.$param['attributes'].' "></div></div>';

							

							

							$onload_js.= 'Calendar.setup({inputField: "wdform_'.$id1.'_element'.$form_id.'",	ifFormat: "'.$param['w_format'].'",button: "wdform_'.$id1.'_button'.$form_id.'",align: "Tl",singleClick: true,firstDay: 0});';

						

						break;

					}



					case 'type_date_fields':

					{

					

						if($element_value=='')

							$element_value='--';

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_day','w_month','w_year','w_day_type','w_month_type','w_year_type','w_day_label','w_month_label','w_year_label','w_day_size','w_month_size','w_year_size','w_required','w_class','w_from','w_to','w_divider');

							

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							

							$element_value = explode('-',$element_value);

							

							$param['w_day']=$input_get->getString('wdform_'.$id1."_day".$form_id, $param['w_day']);

							$param['w_month']=$input_get->getString('wdform_'.$id1."_month".$form_id, $param['w_month']);

							$param['w_year']=$input_get->getString('wdform_'.$id1."_year".$form_id, $param['w_year']);

								

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							

			

							if($param['w_day_type']=="SELECT")

							{

								$w_day_type = '<select id="wdform_'.$id1.'_day'.$form_id.'" name="wdform_'.$id1.'_day'.$form_id.'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'><option value=""></option>';

								

								for($k=0; $k<=31; $k++)

								{

								

									if($k<10)

									{

										if($element_value[0]=='0'.$k)

										$selected = "selected=\"selected\"";

										else

										$selected = "";

										

										$w_day_type .= '<option value="0'.$k.'" '.$selected.'>0'.$k.'</option>';

									}

									else

									{

									if($element_value[0]==''.$k)

										$selected = "selected=\"selected\"";

										else

										$selected = "";

										

										$w_day_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';

									}

									

								}

								$w_day_type .= '</select>';

								

							}

							else

							{

								$w_day_type = '<input type="text" value="'.$element_value[0].'" id="wdform_'.$id1.'_day'.$form_id.'" name="wdform_'.$id1.'_day'.$form_id.'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_day'.$form_id.'").blur(function() {if (wdformjQuery(this).val()=="0") wdformjQuery(this).val(""); else add_0(this)});';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_day'.$form_id.'").keypress(function() {return check_day(event, this)});';

							}

							

							

							if($param['w_month_type']=="SELECT")

							{

							

								$w_month_type = '<select id="wdform_'.$id1.'_month'.$form_id.'" name="wdform_'.$id1.'_month'.$form_id.'" style="width: '.$param['w_month_size'].'px;" '.$param['attributes'].'><option value=""></option><option value="01" '.($element_value[1]=="01" ? "selected=\"selected\"": "").'  >'.JText::_("January").'</option><option value="02" '.($element_value[1]=="02" ? "selected=\"selected\"": "").'>'.JText::_("February").'</option><option value="03" '.($element_value[1]=="03"? "selected=\"selected\"": "").'>'.JText::_("March").'</option><option value="04" '.($element_value[1]=="04" ? "selected=\"selected\"": "").' >'.JText::_("April").'</option><option value="05" '.($element_value[1]=="05" ? "selected=\"selected\"": "").' >'.JText::_("May").'</option><option value="06" '.($element_value[1]=="06" ? "selected=\"selected\"": "").' >'.JText::_("June").'</option><option value="07" '.($element_value[1]=="07" ? "selected=\"selected\"": "").' >'.JText::_("July").'</option><option value="08" '.($element_value[1]=="08" ? "selected=\"selected\"": "").' >'.JText::_("August").'</option><option value="09" '.($element_value[1]=="09" ? "selected=\"selected\"": "").' >'.JText::_("September").'</option><option value="10" '.($element_value[1]=="10" ? "selected=\"selected\"": "").' >'.JText::_("October").'</option><option value="11" '.($element_value[1]=="11" ? "selected=\"selected\"": "").'>'.JText::_("November").'</option><option value="12" '.($element_value[1]=="12" ? "selected=\"selected\"": "").' >'.JText::_("December").'</option></select>';

							

							}

							else

							{

								$w_month_type = '<input type="text" value="'.$element_value[1].'" id="wdform_'.$id1.'_month'.$form_id.'" name="wdform_'.$id1.'_month'.$form_id.'"  style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_month'.$form_id.'").blur(function() {if (wdformjQuery(this).val()=="0") wdformjQuery(this).val(""); else add_0(this)});';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_month'.$form_id.'").keypress(function() {return check_month(event, this)});';

							}

							

							

							if($param['w_year_type']=="SELECT" )

							{

								$w_year_type = '<select id="wdform_'.$id1.'_year'.$form_id.'" name="wdform_'.$id1.'_year'.$form_id.'"  from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_year_size'].'px;" '.$param['attributes'].'><option value=""></option>';

								

								for($k=$param['w_to']; $k>=$param['w_from']; $k--)

								{

									if($element_value[2]==$k)

									$selected = "selected=\"selected\"";

									else

									$selected = "";

									

									$w_year_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';

								}

								$w_year_type .= '</select>';

							}

							else

							{

								$w_year_type = '<input type="text" value="'.$element_value[2].'" id="wdform_'.$id1.'_year'.$form_id.'" name="wdform_'.$id1.'_year'.$form_id.'" from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_year'.$form_id.'").blur(function() {check_year2(this)});';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_year'.$form_id.'").keypress(function() {return check_year1(event, this)});';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_year'.$form_id.'").change(function() {change_year(this)});';

							}

							

							

							

							$rep ='<div type="type_date_fields" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;">'.$w_day_type.'</div><div style="display: table-cell;"><span class="wdform_separator">'.$param['w_divider'].'</span></div><div style="display: table-cell;">'.$w_month_type.'</div><div style="display: table-cell;"><span class="wdform_separator">'.$param['w_divider'].'</span></div><div style="display: table-cell;">'.$w_year_type.'</div></div><div style="display: table-row;"><div style="display: table-cell;"><label class="mini_label">'.$param['w_day_label'].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label" >'.$param['w_month_label'].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$param['w_year_label'].'</label></div></div></div></div></div>';

							

						

						break;

					}

	

					

					case 'type_hidden':

					{

						

							$params_names=array('w_name','w_value');

							$temp=$params;



							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							

							$rep ='<div type="type_hidden" class="wdform-field"><div class="wdform-label-section" style="display: table-cell;"></div><div class="wdform-element-section" style="display: table-cell;"><input type="text" value="'.$element_value.'" id="wdform_'.$id1.'_element'.$form_id.'" name="'.$param['w_name'].'" '.$param['attributes'].'></div></div>';

							

						break;

					}

				

					case 'type_star_rating':

					{

						if($element_value=='')

							$element_value = '/';

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_field_label_col','w_star_amount','w_required','w_class');

							$temp=$params;

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' add_'.$attr;

							}

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							

							$element_value = explode('/', $element_value);

			

							$images = '';	

							for($i=0; $i<$element_value[1]; $i++)

							{

								$images .= '<img id="wdform_'.$id1.'_star_'.$i.'_'.$form_id.'" src="components/com_formmaker/images/star.png" >';

								

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").mouseover(function() {change_src('.$i.',"wdform_'.$id1.'", "'.$form_id.'", "'.$param['w_field_label_col'].'");});';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").mouseout(function() {reset_src('.$i.',"wdform_'.$id1.'", "'.$form_id.'");});';

								$onload_js .='wdformjQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").click(function() {select_star_rating('.$i.',"wdform_'.$id1.'", "'.$form_id.'","'.$param['w_field_label_col'].'", "'.$element_value[1].'");});';

								$onload_js .='select_star_rating('.($element_value[0]-1).',"wdform_'.$id1.'", "'.$form_id.'","'.$param['w_field_label_col'].'", "'.$element_value[1].'");';

							}

							

							$rep ='<div type="type_star_rating" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" '.$param['attributes'].'>'.$images.'</div><input type="hidden" value="" id="wdform_'.$id1.'_selected_star_amount'.$form_id.'" name="wdform_'.$id1.'_selected_star_amount'.$form_id.'"></div></div>';

							

							

							$onsubmit_js.='

								wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_star_amount'.$form_id.'\" value = \"'.$param['w_star_amount'].'\" />").appendTo("#adminForm");

								';

								

						break;

					}

					case 'type_scale_rating':

					{

						if($element_value=='')

							$element_value = '/';

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_mini_labels','w_scale_amount','w_required','w_class');

							$temp=$params;

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' add_'.$attr;

							}

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

						

							$w_mini_labels = explode('***',$param['w_mini_labels']);

							$element_value = explode('/',$element_value);

							

							

							$numbers = '';	

							$radio_buttons = '';	

							$to_check=0;

							$to_check=$element_value[0];

							

							for($i=1; $i<=$element_value[1]; $i++)

							{

								$numbers.= '<div  style="text-align: center; display: table-cell;"><span>'.$i.'</span></div>';

								$radio_buttons.= '<div style="text-align: center; display: table-cell;"><div class="radio-div"><input id="wdform_'.$id1.'_scale_radio'.$form_id.'_'.$i.'" name="wdform_'.$id1.'_scale_radio'.$form_id.'" value="'.$i.'" type="radio" '.( $to_check==$i ? 'checked="checked"' : '' ).'><label for="wdform_'.$id1.'_scale_radio'.$form_id.'_'.$i.'"></label></div></div>';

							}

			

							$rep ='<div type="type_scale_rating" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

									

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" style="float: left;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[0].'</label><div  style="display: inline-table; vertical-align: middle;border-spacing: 7px;"><div style="display: table-row;">'.$numbers.'</div><div style="display: table-row;">'.$radio_buttons.'</div></div><label class="mini_label" >'.$w_mini_labels[1].'</label></div></div></div>';

							

							

							$onsubmit_js.='

								wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_scale_amount'.$form_id.'\" value = \"'.$param['w_scale_amount'].'\" />").appendTo("#adminForm");

								';

						

						break;

					}

					

					case 'type_spinner':

					{

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_field_width','w_field_min_value','w_field_max_value', 'w_field_step', 'w_field_value', 'w_required','w_class');

							$temp=$params;

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' add_'.$attr;

							}

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

						



							$rep ='<div type="type_spinner" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							 

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><input type="text" value="'.($element_value!= 'null' ? $element_value : '').'" name="wdform_'.$id1.'_element'.$form_id.'" id="wdform_'.$id1.'_element'.$form_id.'" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'></div></div>';

							

							$onload_js .='

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'")[0].spin = null;

								spinner = wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner();

								spinner.spinner( "value", "'.($element_value!= 'null' ? $element_value : '').'");

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ min: "'.$param['w_field_min_value'].'"});    

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ max: "'.$param['w_field_max_value'].'"});

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ step: "'.$param['w_field_step'].'"});

							';

						

						break;

					}

					

					case 'type_slider':

					{

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_field_width','w_field_min_value','w_field_max_value', 'w_field_value', 'w_required','w_class');

							$temp=$params;

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' add_'.$attr;

							}

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							



							$rep ='<div type="type_slider" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							 

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><input type="hidden" value="'.$element_value.'" id="wdform_'.$id1.'_slider_value'.$form_id.'" name="wdform_'.$id1.'_slider_value'.$form_id.'"><div name="'.$id1.'_element'.$form_id.'" id="wdform_'.$id1.'_element'.$form_id.'" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'"></div><div align="left" style="display: inline-block; width: 33.3%; text-align:left;"><span id="wdform_'.$id1.'_element_min'.$form_id.'" class="wdform-label">'.$param['w_field_min_value'].'</span></div><div align="right" style="display: inline-block; width: 33.3%; text-align: center;"><span id="wdform_'.$id1.'_element_value'.$form_id.'" class="wdform-label">'.$element_value.'</span></div><div align="right" style="display: inline-block; width: 33.3%; text-align:right;"><span id="wdform_'.$id1.'_element_max'.$form_id.'" class="wdform-label">'.$param['w_field_max_value'].'</span></div></div></div>';

									

									

							$onload_js .='

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'")[0].slide = null;

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").slider({

									range: "min",

									value: eval('.$element_value.'),

									min: eval('.$param['w_field_min_value'].'),

									max: eval('.$param['w_field_max_value'].'),

									slide: function( event, ui ) {	

									

										wdformjQuery("#wdform_'.$id1.'_element_value'.$form_id.'").html("" + ui.value)

										wdformjQuery("#wdform_'.$id1.'_slider_value'.$form_id.'").val("" + ui.value)



									}

									});

							';

										

						break;

					}

					

					

					case 'type_range':

					{

						if($element_value=='')

							$element_value = '-';

							

							$params_names=array('w_field_label_size','w_field_label_pos','w_field_range_width','w_field_range_step','w_field_value1', 'w_field_value2', 'w_mini_labels', 'w_required','w_class');

							$temp=$params;

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' add_'.$attr;

							}

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

													

							$element_value = explode('-',$element_value);

							$w_mini_labels = explode('***',$param['w_mini_labels']);

							

							$rep ='<div type="type_range" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						



							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><div style="display: table;"><div style="display: table-row;"><div valign="middle" align="left" style="display: table-cell;"><input type="text" value="'.($element_value[0]!= 'null' ? $element_value[0] : '').'" name="wdform_'.$id1.'_element'.$form_id.'0" id="wdform_'.$id1.'_element'.$form_id.'0" style="width: '.$param['w_field_range_width'].'px;"  '.$param['attributes'].'></div><div valign="middle" align="left" style="display: table-cell; padding-left: 4px;"><input type="text" value="'.($element_value[1]!= 'null' ? $element_value[1] : '').'" name="wdform_'.$id1.'_element'.$form_id.'1" id="wdform_'.$id1.'_element'.$form_id.'1" style="width: '.$param['w_field_range_width'].'px;" '.$param['attributes'].'></div></div><div style="display: table-row;"><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="wdform_'.$id1.'_mini_label_from">'.$w_mini_labels[0].'</label></div><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="wdform_'.$id1.'_mini_label_to">'.$w_mini_labels[1].'</label></div></div></div></div></div>';

													

							

							

							$onload_js .='

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'0")[0].spin = null;

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'1")[0].spin = null;

								

								spinner0 = wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'0").spinner();

								spinner0.spinner( "value", "'.($element_value[0]!= 'null' ? $element_value[0] : '').'");

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ step: '.$param['w_field_range_step'].'});

								

								spinner1 = wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'1").spinner();

								spinner1.spinner( "value", "'.($element_value[1]!= 'null' ? $element_value[1] : '').'");

								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ step: '.$param['w_field_range_step'].'});

							';

																			

						break;

					}

					

					case 'type_grading':

					{

						

							$params_names=array('w_field_label_size','w_field_label_pos', 'w_items', 'w_total', 'w_required','w_class');

							$temp=$params;

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' add_'.$attr;

							}

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

							

							$element_value = explode(':', $element_value);						

							$w_items = explode('***',$param['w_items']);

							$required_check='true';

							$w_items_labels =implode(':',$w_items);

							

							$grading_items ='';

							

						

							for($i=0; $i<(count($element_value)-1)/2-1; $i++)

							{

								$value=$element_value[$i];



								$grading_items .= '<div class="wdform_grading"><input type="text" id="wdform_'.$id1.'_element'.$form_id.'_'.$i.'" name="wdform_'.$id1.'_element'.$form_id.'_'.$i.'"  value="'.$value.'" '.$param['attributes'].'><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.'_'.$i.'">'.$w_items[$i].'</label></div>';

								

								$required_check.=' && wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'_'.$i.'").val()==""';

							}

								

							$rep ='<div type="type_grading" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

														

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><input type="hidden" value="'.$param['w_total'].'" name="wdform_'.$id1.'_grading_total'.$form_id.'" id="wdform_'.$id1.'_grading_total'.$form_id.'"><div id="wdform_'.$id1.'_element'.$form_id.'">'.$grading_items.'<div id="wdform_'.$id1.'_element_total_div'.$form_id.'" class="grading_div">Total: <span id="wdform_'.$id1.'_sum_element'.$form_id.'">0</span>/<span id="wdform_'.$id1.'_total_element'.$form_id.'">'.$param['w_total'].'</span><span id="wdform_'.$id1.'_text_element'.$form_id.'"></span></div></div></div></div>';

							

							$onload_js.='

							wdformjQuery("#wdform_'.$id1.'_element'.$form_id.' input").change(function() {sum_grading_values("wdform_'.$id1.'","'.$form_id.'");});';

							

							$onload_js.='

							wdformjQuery("#wdform_'.$id1.'_element'.$form_id.' input").keyup(function() {sum_grading_values("wdform_'.$id1.'","'.$form_id.'");});';

							

							$onload_js.='

							sum_grading_values("wdform_'.$id1.'","'.$form_id.'");';



								

								$check_js.='

								if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0)

								{

									if(parseInt(wdformjQuery("#wdform_'.$id1.'_sum_element'.$form_id.'").html()) > '.$param['w_total'].')

									{

										alert("'.addslashes(JText::sprintf('WDF_INVALID_GRADING', '"'.$label.'"', $param['w_total'] )).'");

										return false;

									}

								}

								';		

							

							$onsubmit_js.='

								wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_item'.$form_id.'\" value = \"'.$w_items_labels.':'.$param['w_total'].'\" />").appendTo("#adminForm");

								';

						

						break;

					}

					case 'type_matrix':

					{

						

							$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_input_type', 'w_rows', 'w_columns', 'w_required','w_class');

							$temp=$params;

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' add_'.$attr;

							}

							

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");		

							

							$w_rows = explode('***',$param['w_rows']);

							$w_columns = explode('***',$param['w_columns']);

						

							$element_value = str_replace("******matrix***","",$element_value);

							$element_value = explode($param['w_field_input_type'].'***', $element_value);

							$element_value = explode('***', $element_value[1]);

										

							$column_labels ='';

						

							for($i=1; $i<count($w_columns); $i++)

							{

								$column_labels .= '<div><label class="wdform-ch-rad-label">'.$w_columns[$i].'</label></div>';

							}

							

							$rows_columns = '';

							

							$for_matrix =0;

							

							for($i=1; $i<count($w_rows); $i++)

							{

							

								$rows_columns .= '<div class="wdform-matrix-row'.($i%2).'"><div class="wdform-matrix-column"><label class="wdform-ch-rad-label" >'.$w_rows[$i].'</label></div>';

								

							

								for($k=1; $k<count($w_columns); $k++)

								{

									$rows_columns .= '<div class="wdform-matrix-cell">';

									if($param['w_field_input_type']=='radio')

									{



										if (array_key_exists($i-1,$element_value))

											$to_check=$element_value[$i-1];

										else

											$to_check= '' ;

									

										$rows_columns .= '<div class="radio-div"><input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"  type="radio" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'" value="'.$i.'_'.$k.'" '.($to_check==$i.'_'.$k ? 'checked="checked"' : '').'><label for="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"></label></div>';

										

									}

									else

										if($param['w_field_input_type']=='checkbox')

										{

											

											if (array_key_exists($for_matrix,$element_value))

												$to_check=$element_value[$for_matrix];

											else

												$to_check= '' ;

												

											$rows_columns .= '<div class="checkbox-div"><input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" type="checkbox" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" value="1" '.($to_check=="1" ? 'checked="checked"' : '').'><label for="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"></label></div>';

											

											$for_matrix++;

										}

										else

											if($param['w_field_input_type']=='text')

											{

												$rows_columns .= '<input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" type="text" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" value="'.(array_key_exists($for_matrix,$element_value) ? $element_value[$for_matrix] : '').'">';

											

												$for_matrix++;

											}	

											else

												if($param['w_field_input_type']=='select')

												{

													$rows_columns .= '<select id="wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k.'" name="wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k.'" ><option value="" '.(array_key_exists($for_matrix,$element_value) ? ($element_value[$for_matrix]=="" ? "selected=\"selected\"": "") : '').'> </option><option value="yes" '.(array_key_exists($for_matrix,$element_value) ? ($element_value[$for_matrix]=="yes" ? "selected=\"selected\"": "") : '').'>Yes</option><option value="no" '.(array_key_exists($for_matrix,$element_value) ? ($element_value[$for_matrix]=="no" ? "selected=\"selected\"": "") : '').'>No</option></select>';

													

													$for_matrix++;

												}	

									$rows_columns.='</div>';

								}

									

								$rows_columns .= '</div>';	

							}

								

							$rep ='<div type="type_matrix" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" class="wdform-matrix-table" '.$param['attributes'].'><div style="display: table-row-group;"><div class="wdform-matrix-head"><div style="display: table-cell;"></div>'.$column_labels.'</div>'.$rows_columns.'</div></div></div></div>';

							

							$onsubmit_js.='

								wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_input_type'.$form_id.'\" value = \"'.$param['w_field_input_type'].'\" /><input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_row'.$form_id.'\" value = \"'.$param['w_rows'].'\" /><input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_column'.$form_id.'\" value = \"'.$param['w_columns'].'\" />").appendTo("#adminForm");

								';		

								

						break;

					

					}



				}

				

				$form=str_replace('%'.$id1.' - '.$labels[$id1s_key].'%', $rep, $form);

			}

			

		}



echo $form;

	?>

   <script language="javascript" type="text/javascript">



Joomla.submitbutton= function (pressbutton) {

	var form = document.adminForm;



	<?php echo $onsubmit_js; ?>;

	

	if (pressbutton == 'cancel_submit') 

	{

	submitform( pressbutton );

	return;

	}



	submitform( pressbutton );

}





	wdformjQuery("div[type='type_number'] input, div[type='type_phone'] input, div[type='type_spinner'] input, div[type='type_range'] input, .wdform-quantity").keypress(function(evt) {return check_isnum(evt)});	

	wdformjQuery("div[type='type_grading'] input").keypress(function() {return check_isnum_or_minus(event)});



JURI_ROOT ='<?php echo JURI::root(true) ?>'; 

<?php if($onload_js) { ?>

window.onload = <?php echo $onload_js; ?>;





<?php } ?>



</script>    

<input type="hidden" name="option" value="com_formmaker" />

<input type="hidden" name="id" value="<?php echo $rows[0]->group_id ?>" />        

<input type="hidden" name="form_id" value="<?php echo $rows[0]->form_id ?>" />        

<input type="hidden" name="date" value="<?php echo $rows[0]->date ?>" />        

<input type="hidden" name="ip" value="<?php echo $rows[0]->ip ?>" />        

<input type="hidden" name="task" value="save_submit" />        

</form>

        <?php		

}

	   

public static function editSubmit_old($rows, $labels_id ,$labels_name,$labels_type, $ispaypal){

JRequest::setVar( 'hidemainmenu', 1 );

$editor	= JFactory::getEditor();



$document = JFactory::getDocument();

 	$cmpnt_js_path = JURI::root(true).'/administrator/components/com_formmaker/js';

	$document->addScript($cmpnt_js_path.'/main_div.js');

		?>

        

<script language="javascript" type="text/javascript">



Joomla.submitbutton= function (pressbutton) {

	var form = document.adminForm;



	if (pressbutton == 'cancel_submit') 

	{

	submitform( pressbutton );

	return;

	}



	submitform( pressbutton );

}



</script>        



<form action="index.php" method="post" name="adminForm"  id="adminForm">

<table class="admintable" style="border-spacing:5px; border-collapse: separate;">

				<tr>

					<td class="key">

						<label for="ID">

							<?php echo JText::_( 'ID' ); ?>:

						</label>

					</td>

					<td >

                       <?php echo $rows[0]->group_id;?>

					</td>

				</tr>

                

                <tr>

					<td class="key">

						<label for="Date">

							<?php echo JText::_( 'Date' ); ?>:

						</label>

					</td>

					<td >

                       <?php echo $rows[0]->date;?>

					</td>

				</tr>

                <tr>

					<td class="key">

						<label for="IP">

							<?php echo JText::_( 'IP' ); ?>:

						</label>

					</td>

					<td >

                       <?php echo $rows[0]->ip;?>

					</td>

                </tr>

                

<?php 

foreach($labels_id as $key => $label_id)

{

	if($labels_type[$key]!='' and $labels_type[$key]!='type_editor' and $labels_type[$key]!='type_submit_reset' and $labels_type[$key]!='type_map' and $labels_type[$key]!='type_mark_map' and $labels_type[$key]!='type_captcha' and $labels_type[$key]!='type_recaptcha' and $labels_type[$key]!='type_button')

	{

		$element_value='';

		foreach($rows as $row)

		{

			if($row->element_label==$label_id)

			{		

				$element_value=	$row->element_value;

				break;

			}

			else

			{	

				$element_value=	'element_valueelement_valueelement_value';

				

			}

			

		}

		

		if($element_value=="element_valueelement_valueelement_value")

			continue;

		

		switch ($labels_type[$key])

		{

			case 'type_checkbox':

			{

			$choices	= explode('***br***',$element_value);

			$choices 	= array_slice($choices,0, count($choices)-1);   

		echo '		<tr>

						<td class="key" rowspan="'.count($choices).'">

							<label for="title">

								'.$labels_name[$key].'

							</label>

						</td>';

			foreach($choices as $choice_key => $choice)

		echo '

						<td >

							<input type="text" name="submission_'.$label_id.'_'.$choice_key.'" id="submission_'.$label_id.'_'.$choice_key.'" value="'.$choice.'" size="80" />

						</td>

					</tr>

					';

				

			break;

			}

			case 'type_paypal_payment_status':

			{

			echo '		<tr>

							<td class="key">

								<label for="title">

									'.$labels_name[$key].'

								</label>

							</td>

							<td >'

							

							?>

							

<select name="submission_0" id="submission_0" >

	<option value="" ></option>

	<option value="Canceled" >Canceled</option>

	<option value="Cleared" >Cleared</option>

	<option value="Cleared by payment review" >Cleared by payment review</option>

	<option value="Completed" >Completed</option>

	<option value="Denied" >Denied</option>

	<option value="Failed" >Failed</option>

	<option value="Held" >Held</option>

	<option value="In progress" >In progress</option>

	<option value="On hold" >On hold</option>

	<option value="Paid" >Paid</option>

	<option value="Partially refunded" >Partially refunded</option>

	<option value="Pending verification" >Pending verification</option>

	<option value="Placed" >Placed</option>

	<option value="Processing" >Processing</option>

	<option value="Refunded" >Refunded</option>

	<option value="Refused" >Refused</option>

	<option value="Removed" >Removed</option>

	<option value="Returned" >Returned</option>

	<option value="Reversed" >Reversed</option>

	<option value="Temporary hold" >Temporary hold</option>

	<option value="Unclaimed" >Unclaimed</option>

</select>	

<script> 

    var element = document.getElementById('submission_0');

    element.value = '<?php echo $element_value; ?>';

</script>

							

							<?php

							echo '

							</td>

						</tr>

						';



			

			break;

			}

			 case 'type_star_rating':   

		   {

                        $edit_stars="";	

						$element_value1 = str_replace("***star_rating***",'',$element_value);

						$stars_value=explode('***', $element_value1);

	                    

						for( $j=0;$j<$stars_value[1];$j++)

							$edit_stars.='<img id="'.$label_id.'_star_'.$j.'" onclick="edit_star_rating('.$j.','.$label_id.')" src="components/com_formmaker/images/star_yellow.png" /> ';

						

						for( $k=$stars_value[1];$k<$stars_value[0];$k++)

							$edit_stars.='<img id="'.$label_id.'_star_'.$k.'" onclick="edit_star_rating('.$k.','.$label_id.')" src="components/com_formmaker/images/star.png" /> ';

					

								

				echo '		<tr>

						<td class="key">

							<label for="title">

								'.$labels_name[$key].'

							</label>

						</td>

						<td >

								

				<input type="hidden"  id="'.$label_id.'_star_amountform_id_temp" name="'.$label_id.'_star_amountform_id_temp" value="'.$stars_value[0].'">

				

				<input type="hidden"  id="'.$label_id.'_selected_star_amountform_id_temp" name="'.$label_id.'_selected_star_amountform_id_temp" value="'.$stars_value[1].'">

								'.$edit_stars.'

								

								<input type="hidden" name="submission_'.$label_id.'" id="submission_'.$label_id.'" value="'.$element_value.'" size="80" />

								</td>

							</tr>

							';

			break;							

		}

		

			 case "type_scale_rating": 

		        {           					

							$scale_radio = explode('/', $element_value);

	                        $scale_value = $scale_radio[0]; 

							

							$scale ='<table><tr>';

						for( $k=1;$k<=$scale_radio[1];$k++)

						$scale .= '<td style="text-align:center"><span>'.$k.'</span></td>';

						$scale .='<tr></tr>';

						for( $l=1;$l<=$scale_radio[1];$l++){

						if($l==$scale_radio[0])

						$checked="checked";

						else

						$checked="";

						$scale .= '<td><input type="radio" name = "'.$label_id.'_scale_rating_radio" id = "'.$label_id.'_scale_rating_radio_'.$l.'" value="'.$l.'" '.$checked.' onClick="edit_scale_rating(this.value,'.$label_id.')" /></td>';

						}	

                $scale .= '</tr></table>';

				

		echo '		<tr>

						<td class="key">

							<label for="title">

								'.$labels_name[$key].'

							</label>

						</td>

						<td >

						

		<input type="hidden"  id="'.$label_id.'_scale_checkedform_id_temp" name="'.$label_id.'_scale_checkedform_id_temp" value="'.$scale_radio[1].'">

		

						'.$scale.'

						

						<input type="hidden" name="submission_'.$label_id.'" id="submission_'.$label_id.'" value="'.$element_value.'" size="80" />

						</td>

					</tr>

					';

		

	    break;

		}

		case 'type_range':

            {                						

							$range_value = explode('-', $element_value);		

             $range = '<input name="'.$label_id.'_element0"  id="'.$label_id.'_element0" type="text" value="'.$range_value[0].'" onChange="edit_range(this.value,'.$label_id.',0)" style="width:90px;"/> - <input name="'.$label_id.'_element1"  id="'.$label_id.'_element1" type="text" value="'.$range_value[1].'" onChange="edit_range(this.value,'.$label_id.',1)" style="width:90px;"/>';							

		

		echo '		<tr>

						<td class="key">

							<label for="title">

								'.$labels_name[$key].'

							</label>

						</td>

						<td >

							'.$range.'

						

						<input type="hidden" name="submission_'.$label_id.'" id="submission_'.$label_id.'" value="'.$element_value.'"  />

						</td>

					</tr>

					';

        break;

		}

		case 'type_spinner':

            {                						

							echo '		<tr>

							<td class="key">

								<label for="title">

									'.$labels_name[$key].'

								</label>

							</td>

							<td >

								<input type="text" name="submission_'.$label_id.'" id="submission_'.$label_id.'" value="'.str_replace("*@@url@@*",'',$element_value).'" style="width:90px;" />

							</td>

						</tr>

						';



        break;

		}

		case 'type_grading':

		{

            				$element_value1 = str_replace("***grading***",'',$element_value);					

							$garding_value = explode(':', $element_value1);

							

							$items_count = sizeof($garding_value)-1;

							$garding = "";

							$sum = "";

							for($k=0;$k<$items_count/2;$k++)

							{						

             $garding .= '<input name="'.$label_id.'_element'.$k.'"  id="'.$label_id.'_element'.$k.'" type="text" value="'.$garding_value[$k].'" onKeyUp="edit_grading('.$label_id.','.$items_count.')" style="width:90px;"/> '.$garding_value[$items_count/2+$k].'</br>';							

		     $sum += $garding_value[$k];

			  }

		echo '		<tr>

						<td class="key">

							<label for="title">

								'.$labels_name[$key].'

							</label>

						</td>

						<td >

							'.$garding.'<div><span id="'.$label_id.'_grading_sumform_id_temp">'.$sum.'</span>/<span id="'.$label_id.'_grading_totalform_id_temp">'.$garding_value[$items_count].'</span><span id="'.$label_id.'_text_elementform_id_temp"></span>

						<input type="hidden"  id="'.$label_id.'_element_valueform_id_temp" name="'.$label_id.'_element_valueform_id_temp" value="'.$element_value1.'">

						<input type="hidden"  id="'.$label_id.'_grading_totalform_id_temp" name="'.$label_id.'_grading_totalform_id_temp" value="'.$garding_value[$items_count].'">

						<input type="hidden" name="submission_'.$label_id.'" id="submission_'.$label_id.'" value="'.$element_value.'" size="80" />

						</td>

					</tr>

					';

        break;

		}

        case 'type_matrix':

		{

                     				

                                  $new_filename= str_replace("***matrix***",'', $element_value);	

									$matrix_value=explode('***', $new_filename);

	                        $matrix_value = array_slice($matrix_value,0, count($matrix_value)-1);   

					

							

					

        $mat_rows=$matrix_value[0];

		$mat_columns=$matrix_value[$mat_rows+1];

					

				$matrix="<table>";

							

	    

							$matrix .='<tr><td></td>';

					

							for( $k=1;$k<=$mat_columns;$k++)

							$matrix .='<td style="background-color:#BBBBBB; padding:5px; border:1px; ">'.$matrix_value[$mat_rows+1+$k].'</td>';

							$matrix .='</tr>';

							

							$aaa=Array();

							   $var_checkbox=1;

							   $selected_value="";

							   $selected_value_yes="";

							   $selected_value_no="";

							for( $k=1;$k<=$mat_rows;$k++)

							{

								$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px; border:1px;">'.$matrix_value[$k].'</td>';

									if($matrix_value[$mat_rows+$mat_columns+2]=="radio")

									{

								  

										if($matrix_value[$mat_rows+$mat_columns+2+$k]==0)

										{

											$checked="";

											$aaa[1]="";

										}

										else	

										$aaa=explode("_",$matrix_value[$mat_rows+$mat_columns+2+$k]);

										

										

									   

										

										for( $l=1;$l<=$mat_columns;$l++)

										{

											if($aaa[1]==$l){

											$checked='checked';

											

											}

											else

											$checked="";

											$index = "'".$k.'_'.$l."'";

											

										$matrix .='<td style="text-align:center;"><input name="'.$label_id.'_input_elementform_id_temp'.$k.'"  id="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'" type="'.$matrix_value[$mat_rows+$mat_columns+2].'" '.$checked.' onClick="change_radio_values('.$index.','.$label_id.','.$mat_rows.','.$mat_columns.')" /></td>';

										

										}

										

									} 

									else

									{

										if($matrix_value[$mat_rows+$mat_columns+2]=="checkbox")

										{

																  

											for( $l=1;$l<=$mat_columns;$l++)

											{

												if( $matrix_value[$mat_rows+$mat_columns+2+$var_checkbox]==1)

												 $checked ='checked';

												 else

												 $checked ='';

												$index = "'".$k.'_'.$l."'";	

												$matrix .='<td style="text-align:center;"><input name="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'"  id="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'" type="'.$matrix_value[$mat_rows+$mat_columns+2].'" '.$checked.' onClick="change_checkbox_values('.$index.','.$label_id.','.$mat_rows.','.$mat_columns.')"/></td>';

												

											 $var_checkbox++;

											}

										

										}

										else

										{

										

											if($matrix_value[$mat_rows+$mat_columns+2]=="text")

											{

															  

												for( $l=1;$l<=$mat_columns;$l++)

												{

													$text_value = $matrix_value[$mat_rows+$mat_columns+2+$var_checkbox];

												

													$index = "'".$k.'_'.$l."'";									

													$matrix .='<td style="text-align:center;"><input name="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'"  id="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'" type="'.$matrix_value[$mat_rows+$mat_columns+2].'" 

													value="'.$text_value.'" onKeyUp="change_text_values('.$index.','.$label_id.','.$mat_rows.','.$mat_columns.')" style="width:120px; margin:2px 4px;"/></td>';

													$var_checkbox++;

												}

									

											}	

											else

											{

									  

												for( $l=1;$l<=$mat_columns;$l++)

												{

												  $selected_text = $matrix_value[$mat_rows+$mat_columns+2+$var_checkbox];

													if($selected_text=='yes')

													{

														$selected_value_yes ='selected';

														$selected_value_no ='';

														$selected_value ='';

													}									 

													else

													{

														if($selected_text=='no')

														{

														

															$selected_value_yes ='';

															$selected_value_no ='selected';

															$selected_value ='';

														}

														else

														{

															$selected_value_yes ='';

															$selected_value_no ='';

															$selected_value ='selected';

														}

													}

										 

										 

													$index = "'".$k.'_'.$l."'";	

													$matrix .='<td style="text-align:center;"><select name="'.$label_id.'_select_yes_noform_id_temp'.$k.'_'.$l.'"  id="'.$label_id.'_select_yes_noform_id_temp'.$k.'_'.$l.'" onChange="change_option_values('.$index.','.$label_id.','.$mat_rows.','.$mat_columns.')" style="width:90px; margin:2px 4px;"><option value="" '.$selected_value.'></option><option value="yes" '.$selected_value_yes.' >Yes</option><option value="no" '.$selected_value_no.'>No</option></select></td>';

													$var_checkbox++;

									

												}

											}

									

										}

									

									}

									$matrix .='</tr>';

							}

							 $matrix .='</table>';

									

		echo '		<tr>

						<td class="key">

							<label for="title">

								'.$labels_name[$key].'

							</label>

						</td>

						<td >

						<input type="hidden"  id="'.$label_id.'_matrixform_id_temp" name="'.$label_id.'_matrixform_id_temp" value="'.$new_filename.'">

	                     '.$matrix.'

						

						<input type="hidden" name="submission_'.$label_id.'" id="submission_'.$label_id.'" value="'.$element_value.'" size="80" />

						</td>

					</tr>

					';

		

        break;

		}

			default:

			{

			echo '		<tr>

							<td class="key">

								<label for="title">

									'.$labels_name[$key].'

								</label>

							</td>

							<td >

								<input type="text" name="submission_'.$label_id.'" id="submission_'.$label_id.'" value="'.str_replace("*@@url@@*",'',$element_value).'" size="80" />

							</td>

						</tr>

						';



			}

			break;

		}



		

	}

}



?>

 </table>        

<input type="hidden" name="option" value="com_formmaker" />

<input type="hidden" name="id" value="<?php echo $rows[0]->group_id?>" />        

<input type="hidden" name="form_id" value="<?php echo $rows[0]->form_id?>" />        

<input type="hidden" name="date" value="<?php echo $rows[0]->date?>" />        

<input type="hidden" name="ip" value="<?php echo $rows[0]->ip?>" />        

<input type="hidden" name="task" value="save_submit" />        

</form>

        <?php		

       



}	   

	   

public static function forchrome($id){

?>

<script type="text/javascript">





window.onload=val; 



function val()

{

var form = document.adminForm;

	submitform();

}



</script>

<form action="index.php" method="post" name="adminForm"  id="adminForm">



    <input type="hidden" name="option" value="com_formmaker" />



    <input type="hidden" name="id" value="<?php echo $id;?>" />



    <input type="hidden" name="cid[]" value="<?php echo $id; ?>" />



    <input type="hidden" name="task" value="gotoedit" />

</form>

<?php

}



public static function editCss(&$theme, &$form){

JRequest::setVar( 'hidemainmenu', 1 );

	

$user = JFactory::getUser();

$new = JRequest::getVar('new',0);

	

		?>

  



<script>

if(<?php echo $new ?> == 1)

{

window.parent.location.reload();



}



</script>  



<style>

label {display:inline-block;}

</style>



<?php

if($new == 1 )

return;  ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

    <table class="adminform">

        

		<tr>

            <td >

			<label for="message"> <?php echo JText::_( 'Theme Title' ); ?> </label>  

			<input type="text" name="title" id="title" value="<?php echo $theme->title; ?>" size="40"/>

			</td >

		</tr>

        <tr>

            <td >

                <textarea style="margin: 0px; width:100%" cols="100" rows="18" name="css" id="css" ><?php echo $theme->css;?></textarea>

            </td>

        </tr>

		

		<tr>

            <td>

			<?php if($user->authorise('core.edit', 'com_formmaker')): ?>

			<input type="submit" value="Save " onclick="document.getElementById('task').value = 'save_for_edit';  this.form.submit(); ">

			 <input type="submit" value="Apply" onclick="document.getElementById('task').value = 'apply_for_edit'; this.form.submit(); ">

			 <?php endif; ?>

			 <?php if($user->authorise('core.create', 'com_formmaker')): ?>

             <input type="submit" value="Save as new" onclick="document.getElementById('task').value = 'save_new_theme';  document.getElementById('id').value = ''; this.form.submit(); ">

			 <?php endif; ?>

                <button onclick="document.getElementById('css').value=document.getElementById('main_theme').innerHTML; return false;" style="margin-left:15px;">Reset</button>

            </td>

        </tr>

    </table>

	<div style="display:none;" id="main_theme"><?php echo str_replace('"','\"',$theme->css); ?></div>

    <input type="hidden" name="option" value="com_formmaker" />

    <input type="hidden" name="task" id="task" value="" />

	<input type="hidden" name="id" id="id" value="<?php echo $theme->id; ?>" />

	<input type="hidden" name="form_id" id="form_id" value="<?php echo $form->id; ?>" />

	

</form>





<?php		



       }







public static function select_article(&$rows, &$pageNav, &$lists)

{







		JHTML::_('behavior.tooltip');	



	?>



<form action="index.php?option=com_formmaker" method="post" name="adminForm" id="adminForm">



    <table width="100%">



        <tr>



            <td align="left" width="100%"> <?php echo JText::_( 'Filter' ); ?>:



                <input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />



                <button onclick="this.form.submit();"> <?php echo JText::_( 'Go' ); ?></button>



                <button onclick="document.getElementById('search').value='';this.form.submit();"> <?php echo JText::_( 'Reset' ); ?></button>



            </td>



        </tr>



    </table>



    <table class="adminlist" width="100%">



        <thead>



            <tr>



                <th width="4%"><?php echo '#'; ?></th>



                <th width="8%">



                    <input type="checkbox" name="toggle"



 value="" onclick="checkAll(<?php echo count($rows)?>)">



                </th>



                <th width="50%"><?php echo JHTML::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order'] ); ?></th>



                <th width="38%"><?php echo JHTML::_('grid.sort', 'Email to Send Submissions to', 'mail', @$lists['order_Dir'], @$lists['order'] ); ?></th>



            </tr>



        </thead>



        <tfoot>



            <tr>



                <td colspan="50"> <?php echo $pageNav->getListFooter(); ?> </td>



            </tr>



        </tfoot>



        <?php



	



    $k = 0;



	for($i=0, $n=count($rows); $i < $n ; $i++)



	{



		$row = &$rows[$i];



		$checked 	= JHTML::_('grid.id', $i, $row->id);



		$published 	= JHTML::_('grid.published', $row, $i); 



		// prepare link for id column



		$link 		= JRoute::_( 'index.php?option=com_formmaker&task=edit&cid[]='. $row->id );



		?>



        <tr class="<?php echo "row$k"; ?>">



              <td align="center"><?php echo $row->id?></td>



          <td align="center"><?php echo $checked?></td>



            <td align="center"><a href="<?php echo $link; ?>"><?php echo $row->title?></a></td>



            <td align="center"><?php echo $row->mail?></td>



        </tr>



        <?php



		$k = 1 - $k;



	}



	?>



    </table>



    <input type="hidden" name="option" value="com_formmaker">

    <input type="hidden" name="task" value="forms">

    <input type="hidden" name="boxchecked" value="0">

    <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />

    <input type="hidden" name="filter_order_Dir" value="" />



</form>



<?php



}















//////////////////////////////glxavor 

}

?>