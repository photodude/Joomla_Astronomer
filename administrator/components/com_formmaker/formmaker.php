<?php 
  
 /**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/admin.formmaker.html.php';
require_once JPATH_COMPONENT . '/toolbar.formmaker.html.php';
require_once JPATH_COMPONENT . '/controller.php' ;

JTable::addIncludePath( JPATH_COMPONENT.'/tables' );

$user = JFactory::getUser();
if (!$user->authorise('core.manage', 'com_formmaker')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = new FormmakerController();


$task	= JRequest::getCmd('task'); 
$id = JRequest::getVar('id', 0, 'get', 'int');
$mainframe = JFactory::getApplication();

$bar= JToolBar::getInstance( 'toolbar' );
$bar->appendButton('Custom','<a href="http://web-dorado.com/products/joomla-form.html" target="_blank" style=""><img src="components/com_formmaker/images/buyme.png" border="0" alt="" style="height: 50px;"></a>', 'custom');

// checks the $task variable and 
// choose an appropiate function


switch ( $task )
{

	case 'themes'  :
	{
		TOOLBAR_formmaker::_THEMES();
	}
		break;
		
	case 'add_themes'  :
	case 'edit_themes'  :
	{
		TOOLBAR_formmaker::_NEW_THEMES();
	}
		break;
		
	case 'blocked_ips'  :

	{

		TOOLBAR_formmaker::_Blocked_ips();

	}	
		break;

		

	case 'add_blocked_ips'  :

	case 'edit_blocked_ips'  :

	{

		TOOLBAR_formmaker::_NEW_Blocked_ips();

	}

		break;
		
	case 'submits'  :
	{
		TOOLBAR_formmaker::_SUBMITS();
	}
		break;
		
	case 'edit_submit'  :
	{
		TOOLBAR_formmaker::EDIT_SUBMITS();
	}
		break;
		
	case 'add'  :
	case 'edit'  :	
	{
		TOOLBAR_formmaker::_NEW();
	}
		break;

	case 'form_options'  :
	{
	TOOLBAR_formmaker::_NEW_Form_options();
	}
	break;

	case 'form_layout':
	{
	TOOLBAR_formmaker::_NEW_Form_form_layout();
	}
	break;

	case 'featured_plugins':
	case 'extensions':
	{
		TOOLBAR_formmaker::_featured_plugins();
	}
	break;
	default:
		TOOLBAR_formmaker::_DEFAULT();
		break;

}

switch($task){

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	case 'show_conditions':
		show_conditions();
		break;

	case 'change_choices':
		change_choices();
		break;
		
	case 'add_condition_fields':
		add_condition_fields();
		break;
	
	case 'add_condition':
		add_condition();
		break;
		
	case 'select_data_from_db':
		select_data_from_db();
		break;
		
	case 'show_ip_info':
		show_ip_info();
		break;

	case 'view_submissions':
		view_submissions();
		break;
	
	case 'featured_plugins':
		featured_plugins();
		break;
		
	case 'extensions':
		extensions();
		break;

	case 'remove_query':
		remove_query();
		break;
		
	case 'save_query':
		save_query();
		break;
		
	case 'db_table_struct':
		db_table_struct();
		break;
		
	case 'db_table_struct_select':
		db_table_struct_select();
		break;	
		
	case 'add_query':
		add_query();
		break;
		
	case 'edit_query':
		edit_query();
		break;
		
	case 'db_tables':
		db_tables();
		break;

	case 'show_stats':
		show_stats();
		break;

	case 'generate_xml':
			generate_xml();
		break;
		
	case 'generate_csv':
			generate_csv();
		break;

	case 'paypal_info':
			paypal_info();

		break;
		
	case 'default':
		setdefault();
		break;
		
	case 'product_option':
		product_option();
		break;
			
	case 'preview':
			preview_formmaker();
		break;
		
	case 'edit_css':
		edit_css();
		break;		
	
	case 'themes':
			show_themes();	
		break;
		
	case 'add_themes':	
		add_themes();
	
		break;
		
	case 'edit_themes':
		edit_themes();
		break;

	case 'apply_blocked_ips':	
	case 'save_blocked_ips':		
		save_blocked_ips($task);

		break;			
	
	case 'apply_themes':	
	case 'save_themes':		
		save_themes($task);
		break;
		 
	case 'save_for_edit':	
	case 'apply_for_edit':
	case 'save_new_theme':	
		save_new_theme($task);
	
		break;		 
	
	case 'remove_themes':
			remove_themes();

		break;
		
	case 'cancel_themes':
		cancel_themes();
		break;
		
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	case 'cancel_blocked_ips':
		cancel_blocked_ips();
		break;	

	case 'forms':		
		show();
	
		break;
		
	case 'submits':
			show_submits();
		break;

	case 'element':
	
		$controller->execute( $task );
		$controller->redirect();

		break;

	case 'select_article':
		select_article();
		break;

	case 'add':
			add();
	
		break;

	case 'add_blocked_ips':
		add_blocked_ips();
	
		break;	

	case 'cancel':		
		cancel();

		break;

	case 'apply':	
	case 'save_and_new':	
	case 'save':		

		save($task);

		break;

	case 'edit':	
			edit();		
    		break;
	case 'edit_blocked_ips':
		edit_blocked_ips();	
		
    	break;		
		
	case 'save_as_copy':

    		save_as_copy();
    		break;
			
	case 'copy':
		copy_form();
	
    		break;
			
	case 'form_options':
		form_options();
		
			break;
	case 'form_options_temp':
		form_options_temp();
			
    		break;
			
	case 'form_layout':		
		form_layout();

			break;	
	case 'form_layout_temp':
		form_layout_temp();
			
			break;	
	case 'blocked_ips':
			show_blocked_ips();
	
		break;			
//////////////////////////////////////////////////////////////////		
	case 'apply_form_options':
	case 'save_form_options':
    		save_form_options($task);
    		break;
	case 'apply_form_layout':
	case 'save_form_layout':
    		save_form_layout($task);
    		break;
//////////////////////////////////////////////////////////////////		
	case 'cancel_secondary':
    		cancelSecondary();
    		break;
		

//////////////////////////////////////////////////////////////////		
	case 'remove':		
		remove();

		break;

	case 'remove_blocked_ips':		
			remove_blocked_ips();
		
		break;	
		
	case 'remove_submit':

		removeSubmit();
	
		break;
	
	case 'block_ip':
		
		blockIP();

		break;	
		
	case 'edit_submit':

		editSubmit();

		break;

	case 'save_submit':
	case 'apply_submit':
		saveSubmit($task);
		break;

	case 'cancel_submit':
		cancelSubmit();
		break;

 	 case 'publish':
   		change(1 );
    		break;

	 case 'unpublish':
	   	change(0 );
	    	break;				

	 case 'gotoedit':
	   	gotoedit();
	    	break;	

	case 'country_list':
	   	country_list();
	    	break;
			
	 case 'show_map':
	   	show_map();
	    	break;

	case 'show_matrix':
	   	show_matrix();
	    	break;	
					
	default:
			showredirect();
	
		break;



}


function show_conditions() 
{

	$form_id 		= JRequest::getVar('form_id');
	$row = JTable::getInstance('formmaker', 'Table');
	$row->load( $form_id);
	
	$ids	 	= array();
	$types 		= array();
	$labels 	= array();
	$paramss 	= array();
	$all_ids	= array();
	$all_labels = array();
	
	$select_and_input = array("type_text", "type_password", "type_textarea", "type_name", "type_number", "type_phone", "type_submitter_mail", "type_country", "type_address", "type_spinner", "type_checkbox", "type_radio", "type_own_select", "type_paypal_price", "type_paypal_select", "type_paypal_checkbox", "type_paypal_radio", "type_paypal_shipping");
	
	$fields=explode('*:*new_field*:*',$row->form_fields);
	$fields 	= array_slice($fields,0, count($fields)-1);   
	foreach($fields as $field)
	{
		$temp=explode('*:*id*:*',$field);
		array_push($ids, $temp[0]);
		array_push($all_ids, $temp[0]);
		$temp=explode('*:*type*:*',$temp[1]);
		array_push($types, $temp[0]);
		$temp=explode('*:*w_field_label*:*',$temp[1]);
		array_push($labels, $temp[0]);
		array_push($all_labels, $temp[0]);
		array_push($paramss, $temp[1]);
	}
	
	foreach($types as $key=>$value)
	{
		if(!in_array($types[$key],$select_and_input))
		{					
			unset($ids[$key]);						
			unset($labels[$key]);					
			unset($types[$key]);
			unset($paramss[$key]);					
		}
	}	

	$ids = array_values($ids);
	$labels = array_values($labels);
	$types = array_values($types);
	$paramss = array_values($paramss);
	
	$show_hide	= array();
	$field_label	= array();
	$all_any 	= array();
	$condition_params 	= array();

	$count_of_conditions=0;	
	echo '<input type="hidden" id="condition" name="condition" value="'.$row->condition.'" />';	
	if($row->condition!="")
	{
		$conditions=explode('*:*new_condition*:*',$row->condition);
		$conditions 	= array_slice($conditions,0, count($conditions)-1); 
		$count_of_conditions = count($conditions);					

		foreach($conditions as $condition)
		{
			$temp=explode('*:*show_hide*:*',$condition);
			array_push($show_hide, $temp[0]);
			$temp=explode('*:*field_label*:*',$temp[1]);
			array_push($field_label, $temp[0]);
			$temp=explode('*:*all_any*:*',$temp[1]);
			array_push($all_any, $temp[0]);
			array_push($condition_params, $temp[1]);
		}
	
	
		HTML_contact::show_conditions($ids, $all_ids, $types, $labels, $all_labels, $paramss, $count_of_conditions, $show_hide, $field_label, $all_any, $condition_params, $form_id);
	
	}
}


function change_choices() 
{
	$form_id 		= JRequest::getVar('form_id');
	$num 		= JRequest::getVar('num');
	$field_id 		= JRequest::getVar('field_id');
	$row = JTable::getInstance('formmaker', 'Table');
	$row->load( $form_id);
	
	$ids = array();
	$types = array();
	$labels = array();
	$paramss = array();

	$fields=explode('*:*new_field*:*',$row->form_fields);
	$fields 	= array_slice($fields,0, count($fields)-1);   
	foreach($fields as $field)
	{
		$temp=explode('*:*id*:*',$field);
		array_push($ids, $temp[0]);
		$temp=explode('*:*type*:*',$temp[1]);
		array_push($types, $temp[0]);
		$temp=explode('*:*w_field_label*:*',$temp[1]);
		array_push($labels, $temp[0]);
		array_push($paramss, $temp[1]);
	}

	$key = array_search($field_id, $ids);		
	switch($types[$key])
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
			if($types[$key]=="type_number" || $types[$key]=="type_phone")
				$keypress_function = "return check_isnum_space(event)";
			else
				if($types[$key]=="type_paypal_price")
					$keypress_function = "return check_isnum_point(event)";

			echo '<input id="field_value'.$num.'" type="text" value="" onkeypress="'.$keypress_function.'" style="vertical-align: top; width: 128px;">';
		break;
		
		case "type_address":
			$w_countries = array("","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");
			echo '<select id="field_value'.$num.'" style="width: 142px;">';		
				foreach($w_countries as $choise)
					echo '<option value="'.$choise.'" >'.$choise.'</option>';	
			echo '</select>';	
			
		break;
		case "type_country":
			$temp=$paramss[$key];
			$temp = explode('*:*w_size*:*',$temp);
			$temp = explode('*:*w_countries*:*',$temp[1]);
			$w_countries =  explode('***',$temp[0]);
			echo '<select id="field_value'.$num.'" style="width: 142px;">';		
				foreach($w_countries as $choise)
					echo '<option value="'.$choise.'" >'.$choise.'</option>';	
			echo '</select>';	
		break;
		case "type_checkbox":
		case "type_radio":
		case "type_own_select":
		case "type_paypal_select":
			$temp=$paramss[$key];
			$exp_par = (($types[$key]== 'type_checkbox' || $types[$key]== 'type_radio') ? '*:*w_flow*:*' : '*:*w_size*:*');
		
			$temp = explode($exp_par,$temp);
			$temp = explode('*:*w_choices*:*',$temp[1]);
			$param['w_choices'] = $temp[0];
			$param['w_choices']	= explode('***',$param['w_choices']);	
			if($types[$key] != 'type_paypal_select')
			{
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
			
			$multiple = ($types[$key]=='type_checkbox' ? 'multiple="multiple"' : '');	
			echo '<select id="field_value'.$num.'" style="width: 142px;" '.$multiple.'>';		
				foreach($param['w_choices'] as $key1=>$choise_label)
				{
					if(strpos($choise_label, '[') === false && strpos($choise_label, ']') === false && strpos($choise_label, ':') === false)
						echo '<option value="'.$param['w_choices_value'][$key1].'" >'.$choise_label.'</option>';	
				}	
			echo '</select>';				
		break;
	
		case "type_paypal_checkbox":
		case "type_paypal_radio":
		case "type_paypal_shipping":
			$temp=$paramss[$key];
			$temp = explode('*:*w_flow*:*',$temp);
			$temp = explode('*:*w_choices*:*',$temp[1]);
			$param['w_choices'] = $temp[0];
			$temp= explode('*:*w_choices_price*:*',$temp[1]);
			$param['w_choices_price'] = $temp[0];

			$param['w_choices']	= explode('***',$param['w_choices']);
			$param['w_choices_price'] = explode('***',$param['w_choices_price']);

			$multiple = ($types[$key]=='type_paypal_checkbox' ? 'multiple="multiple"' : '');	
			echo '<select id="field_value'.$num.'" style="width: 142px;" '.$multiple.'>';		
				foreach($param['w_choices'] as $key1=>$choise_label)
				{
					$choise_value = ($types[$key]=='type_paypal_checkbox' ? $choise_label.'*:*value*:*'.$param['w_choices_price'][$key1] : $param['w_choices_price'][$key1]);		
					if(strpos($choise_label, '[') === false && strpos($choise_label, ']') === false && strpos($choise_label, ':') === false)
						echo '<option value="'.$choise_value.'" >'.$choise_label.'</option>';	
				}	
			echo '</select>';			
		break;			
	}

	echo '<script>
		jQuery("#condition_div'.$num.'").show();
		jQuery("select").chosen({
				disable_search_threshold : 10,
				allow_single_deselect : true
			});</script>';
}

function add_condition_fields() 
{
	$form_id 		= JRequest::getVar('form_id');
	$cond_index		= JRequest::getVar('cond_index');
	$cond_fieldindex	= JRequest::getVar('cond_fieldindex');
	$cond_fieldid 		= JRequest::getVar('cond_fieldid');

	$row = JTable::getInstance('formmaker', 'Table');
	$row->load( $form_id);

	$ids = array();
	$types = array();
	$labels = array();
	$paramss = array();
	
	$select_and_input = array("type_text", "type_password", "type_textarea", "type_name", "type_number", "type_phone", "type_submitter_mail", "type_address", "type_country", "type_spinner", "type_checkbox", "type_radio", "type_own_select", "type_paypal_price", "type_paypal_select", "type_paypal_checkbox", "type_paypal_radio", "type_paypal_shipping");
	
	$fields=explode('*:*new_field*:*',$row->form_fields);
	$fields 	= array_slice($fields,0, count($fields)-1);   
	foreach($fields as $field)
	{
		$temp=explode('*:*id*:*',$field);
		array_push($ids, $temp[0]);
		$temp=explode('*:*type*:*',$temp[1]);
		array_push($types, $temp[0]);
		$temp=explode('*:*w_field_label*:*',$temp[1]);
		array_push($labels, $temp[0]);
		array_push($paramss, $temp[1]);
	}

	foreach($types as $key=>$value)
	{
		if(!in_array($types[$key],$select_and_input))
		{					
			unset($ids[$key]);						
			unset($labels[$key]);					
			unset($types[$key]);
			unset($paramss[$key]);					
		}
	}
	
	$ids = array_values($ids);
	$labels = array_values($labels);
	$types = array_values($types);
	$paramss = array_values($paramss);

	HTML_contact::add_condition_fields($cond_index, $cond_fieldindex, $cond_fieldid, $ids, $types, $labels, $paramss, $form_id);
}

function add_condition() 
{
	$form_id 		= JRequest::getVar('form_id');
	$cond_index 		= JRequest::getVar('cond_index');
	$row = JTable::getInstance('formmaker', 'Table');
	$row->load( $form_id);

	$ids = array();
	$types = array();
	$labels = array();
	
	
	$fields=explode('*:*new_field*:*',$row->form_fields);
	$fields 	= array_slice($fields,0, count($fields)-1);   
	foreach($fields as $field)
	{
		$temp=explode('*:*id*:*',$field);
		array_push($ids, $temp[0]);
		$temp=explode('*:*type*:*',$temp[1]);
		array_push($types, $temp[0]);
		$temp=explode('*:*w_field_label*:*',$temp[1]);
		array_push($labels, $temp[0]);
	}
	
	HTML_contact::add_condition($cond_index, $ids, $types, $labels, $form_id);
	
}


function select_data_from_db() 
{
	$id 		= JRequest::getVar('id');
	$field_id 	= JRequest::getVar('field_id');
	$field_type = JRequest::getVar('field_type');
	$value_disabled = JRequest::getVar('value_disabled','yes');
	HTML_contact::select_data_from_db($id, $field_id, $field_type, $value_disabled);
}

function show_ip_info(){
	$ip 	= JRequest::getVar('ip');	
	HTML_contact::show_ip_info($ip);
}

function view_submissions()
{

    $db = JFactory::getDBO();	
	$form_id= JRequest::getVar('form_id');
	$after_save = JRequest::getVar('after_save',0);
	$paypal =0;
	
	$query = "SELECT DISTINCT `element_label` FROM #__formmaker_submits WHERE form_id='".$db->escape((int)$form_id)."'";
	$db->setQuery( $query);
	$labels = $db->loadColumn();
	if($db->getErrorNum()){echo $db->stderr();return false;}
	
	$query = "SELECT id FROM #__formmaker_sessions where form_id=".$db->escape((int)$form_id);
	$db->setQuery($query);	
	$paypal_info = $db->loadObject();	
	if($db->getErrorNum()){echo $db->stderr();return false;}
	
	if($paypal_info)
		$paypal =1;
		
	$sorted_labels_id= array();
	$label_titles=array();
	$sorted_labels_type= array();
	
	if($labels)
	{
		
		$label_id= array();
		$label_order= array();
		$label_order_original= array();
		$label_type= array();
		
		$this_form = JTable::getInstance('formmaker', 'Table');
		$this_form->load($form_id);
		
		if(strpos($this_form->label_order, 'type_paypal_'))
		{
			$this_form->label_order=$this_form->label_order."item_total#**id**#Item Total#**label**#type_paypal_payment_total#****#total#**id**#Total#**label**#type_paypal_payment_total#****#0#**id**#Payment Status#**label**#type_paypal_payment_status#****#";
		}
			
		$label_all	= explode('#****#',$this_form->label_order);
		$label_all 	= array_slice($label_all,0, count($label_all)-1);   
		

		foreach($label_all as $key => $label_each) 
		{
			$label_id_each=explode('#**id**#',$label_each);
			array_push($label_id, $label_id_each[0]);
			
			$label_order_each=explode('#**label**#', $label_id_each[1]);
			
			array_push($label_order_original, $label_order_each[0]);
			array_push($label_type, $label_order_each[1]);
		}
		
		foreach($label_id as $key => $label) 
			if(in_array($label, $labels))
			{
				array_push($sorted_labels_type, $label_type[$key]);
				array_push($sorted_labels_id, $label);
				array_push($label_titles, $label_order_original[$key]);
			}
	}

	HTML_contact::view_submissions($label_titles, $form_id, $sorted_labels_id, $sorted_labels_type, $after_save, $paypal);

}

function remove_query()
{
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();

	$db = JFactory::getDBO();
	$cid = JRequest::getVar( 'cid' , array() , '' , 'array' );
	$id = JRequest::getVar( 'id' );
	JArrayHelper::toInteger($cid);
	
	$query = "SELECT `created_by` FROM #__formmaker where id=".$db->escape((int)$id);
	$db->setQuery( $query);
	$created_by = $db->loadResult();
	
	$canEdit = $user->authorise('core.edit', 'com_formmaker');
	$canEditOwn = $user->authorise('core.edit.own', 'com_formmaker');
	if(!$canEdit)
		if(!$canEditOwn || $created_by != $user->id)
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JACTION_NOT_PERMITTED'),'error');

  if (count( $cid )) {
    $cids = implode( ',', $cid );
    $query = 'DELETE FROM #__formmaker_query' . ' WHERE id IN ( '. $cids .' )'  ;
    $db->setQuery( $query );
    if (!$db->query()) {
      echo "<script> alert('".$db->getErrorMsg(true)."'); 
      window.history.go(-1); </script>\n";
    }
  }
	$msg = 'The query(ies) has been successfully deleted.';
	$mainframe->redirect( "index.php?option=com_formmaker&task=form_options&cid[]=".$id, $msg );
}

function save_query(){

	$mainframe = JFactory::getApplication();
	$row = JTable::getInstance('formmaker_query', 'Table');

	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	
	$row->query = JRequest::getVar( 'query', '','post', 'string', JREQUEST_ALLOWRAW );
	
	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}
}


function db_table_struct()
{
	$db		= JFactory::getDBO();

	$id 		= JRequest::getVar('id');
	$name 		= JRequest::getVar('name');
	if(!$name)
		return;
	$con_method 		= JRequest::getVar('con_method');
	$con_type 		= JRequest::getVar('con_type');
	if($con_type == 'remote')
	{
		$remote = array(); //prevent problems
 
		$remote['driver']   = 'mysql';           
		$remote['host']     = JRequest::getVar('host');
		$remote['user']     = JRequest::getVar('username');
		$remote['password'] = JRequest::getVar('password');
		$remote['database'] = JRequest::getVar('database');
		$remote['prefix']   = '';             
		 
		$db = JDatabase::getInstance( $remote );
		if($db->getErrorNum()){
			echo '<div style="font-size: 22px; text-align: center; padding-top: 15px;">'.$db->stderr().'</div>';
			return false;
		}
	}

	$query = "SHOW COLUMNS FROM ".$name;
	$db->setQuery( $query);
	$table_struct = $db->loadObjectList();

	$db		= JFactory::getDBO();

	$query = "SELECT label_order_current FROM #__formmaker where id=".$db->escape((int)$id);
	$db->setQuery( $query);
	$label = $db->loadResult();

		//	print_r($table_struct);
	
	HTML_contact::db_table_struct($table_struct,$label,$con_method);
}

function db_table_struct_select()
{
	$db		= JFactory::getDBO();

	$field_type 		= JRequest::getVar('field_type');
	$name 		= JRequest::getVar('name');

	if(!$name)
		return;
	$con_method 		= JRequest::getVar('con_method');
	$con_type 		= JRequest::getVar('con_type');
	if($con_type == 'remote')
	{
		$remote = array(); //prevent problems
 
		$remote['driver']   = 'mysql';           
		$remote['host']     = JRequest::getVar('host');
		$remote['user']     = JRequest::getVar('username');
		$remote['password'] = JRequest::getVar('password');
		$remote['database'] = JRequest::getVar('database');
		$remote['prefix']   = '';             
		 
		$db = JDatabase::getInstance( $remote );
		if($db->getErrorNum()){
			echo '<div style="font-size: 22px; text-align: center; padding-top: 15px;">'.$db->stderr().'</div>';
			return false;
		}
	}

	$query = "SHOW COLUMNS FROM ".$name;
	$db->setQuery( $query);
	$table_struct = $db->loadObjectList();

	HTML_contact::db_table_struct_select($table_struct, $field_type);
}


function db_tables()
{

	$db		= JFactory::getDBO();

	$con_type 		= JRequest::getVar('con_type');

	if($con_type == 'local')
	{
		$query = "SHOW TABLES";
		$db->setQuery( $query);
		$tables = $db->loadColumn();

	}
		
	if($con_type == 'remote')
	{
		$remote = array(); 
 
		$remote['driver']   = 'mysql';           
		$remote['host']     = JRequest::getVar('host');
		$remote['user']     = JRequest::getVar('username');
		$remote['password'] = JRequest::getVar('password');
		$remote['database'] = JRequest::getVar('database');
		$remote['prefix']   = '';             
		 
		$db = JDatabase::getInstance( $remote );

		if($remote['host']=="")
		{
			echo '1';
			return false;
		}		

		try
		{
			$query = "SHOW TABLES";
			$db->setQuery( $query);
			$tables = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			echo 1;
			return false;
		}

	}

	HTML_contact::db_tables($tables);
}

function edit_query()
{
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();

	$db			= JFactory::getDBO();
	$id 		= JRequest::getVar('id');
	$query_id 	= JRequest::getVar('query_id');

	$query = "SELECT `created_by` FROM #__formmaker where id=".$db->escape((int)$id);
	$db->setQuery( $query);
	$created_by = $db->loadResult();
	
	$canEdit = $user->authorise('core.edit', 'com_formmaker');
	$canEditOwn = $user->authorise('core.edit.own', 'com_formmaker');
	if(!$canEdit)
		if(!$canEditOwn || $created_by != $user->id)
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JACTION_NOT_PERMITTED'),'error');
	
	$query = "SELECT label_order_current FROM #__formmaker where id=".$db->escape((int)$id);
	$db->setQuery( $query);
	$label = $db->loadResult();

	$query = "SELECT * FROM #__formmaker_query where id=".$db->escape((int)$query_id);
	$db->setQuery( $query);
	$query_obj = $db->loadObject();

	$temp		= explode('***wdfcon_typewdf***',$query_obj->details);
	$con_type	= $temp[0];
	$temp		= explode('***wdfcon_methodwdf***',$temp[1]);
	$con_method	= $temp[0];
	$temp		= explode('***wdftablewdf***',$temp[1]);
	$table_cur	= $temp[0];
	$temp		= explode('***wdfhostwdf***',$temp[1]);
	$host		= $temp[0];
	$temp		= explode('***wdfportwdf***',$temp[1]);
	$port		= $temp[0];
	$temp		= explode('***wdfusernamewdf***',$temp[1]);
	$username	= $temp[0];
	$temp		= explode('***wdfpasswordwdf***',$temp[1]);
	$password	= $temp[0];
	$temp		= explode('***wdfdatabasewdf***',$temp[1]);
	$database	= $temp[0];
	
	$db		= JFactory::getDBO();

	if($con_type == 'local')
	{
		$query = "SHOW TABLES";
		$db->setQuery( $query);
		$tables = $db->loadColumn();

	}
	
	if($con_type == 'remote')
	{
		$remote = array(); //prevent problems

		$remote['driver']   = 'mysql';           
		$remote['host']     = $host;
		$remote['user']     = $username;
		$remote['password'] = $password;
		$remote['database'] = $database;
		$remote['prefix']   = '';             
		 
		$db = JDatabase::getInstance( $remote );
		if($db->getErrorNum()){
			echo '<div style="font-size: 22px; text-align: center; padding-top: 15px;">'.$db->stderr().'</div>';
			return false;
		}

		$query = "SHOW TABLES";
		$db->setQuery( $query);
		$tables = $db->loadColumn();

	}
	
	$query = "SHOW COLUMNS FROM ".$table_cur;
	$db->setQuery( $query);
	$table_struct = $db->loadObjectList();
	HTML_contact::edit_query( $id, $label, $query_obj, $tables, $table_struct, $con_type, $con_method, $table_cur, $temp[1], $host,	$port, $username, $password, $database);

}

function add_query()
{
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	
	$db			= JFactory::getDBO();
	$id 		= JRequest::getVar('id');
	
	$query = "SELECT `created_by` FROM #__formmaker where id=".$db->escape((int)$id);
	$db->setQuery( $query);
	$created_by = $db->loadResult();
	
	$canEdit = $user->authorise('core.edit', 'com_formmaker');
	$canEditOwn = $user->authorise('core.edit.own', 'com_formmaker');
	if(!$canEdit)
		if(!$canEditOwn || $created_by != $user->id)
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JACTION_NOT_PERMITTED'),'error');

	$query = "SELECT label_order_current FROM #__formmaker where id=".$db->escape((int)$id);
	$db->setQuery( $query);
	$label = $db->loadResult();

	HTML_contact::add_query($id,$label);

}

function featured_plugins()
{
	HTML_contact::featured_plugins();
}

function extensions()
{
	HTML_contact::extensions();
}

function show_stats()
{
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
    $db = JFactory::getDBO();
	$form_id 	= JRequest::getVar('form_id');
	$row = JTable::getInstance('formmaker', 'Table');
	$row->load($form_id);
	
	if(!$user->authorise('core.manage.submits', 'com_formmaker'))
	{
		if($user->authorise('core.manage.submits.own', 'com_formmaker') && $row->created_by != $user->id)	
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JACCESS_NOT_PERMITTED'),'error');
	}
	
	$id 	= JRequest::getVar('id');
	$from 	= JRequest::getVar('from');
	$to 	= JRequest::getVar('to');
	$where=' AND form_id='.$form_id;
	
	if($from!='')
		$where.=" AND `date`>='".$from." 00:00:00' ";
	if($to!='')
		$where.=" AND `date`<='".$to." 23:59:59' ";

	$query = "SELECT element_value FROM #__formmaker_submits WHERE element_label='".$db->escape((int)$id)."'".$where;
	$db->setQuery( $query);
	$choices = $db->loadObjectList();

	if($db->getErrorNum()){
		echo $db->stderr();
		return false;}

	HTML_contact::show_stats($choices);
}



function form_layout()
{
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	
	$db		= JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));
	$id 	= $cid[0];
	$row = JTable::getInstance('formmaker', 'Table');
	// load the row from the db table
	$row->load( $id);
	
	$canEdit = $user->authorise('core.edit', 'com_formmaker');
	$canEditOwn = $user->authorise('core.edit.own', 'com_formmaker');
	if(!$canEdit)
		if(!$canEditOwn || $row->created_by != $user->id)
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),'error');

	$ids = array();
	$types = array();
	$labels = array();
	
	
	$fields=explode('*:*new_field*:*',$row->form_fields);
	$fields 	= array_slice($fields,0, count($fields)-1);   
	foreach($fields as $field)
	{
		$temp=explode('*:*id*:*',$field);
		array_push($ids, $temp[0]);
		$temp=explode('*:*type*:*',$temp[1]);
		array_push($types, $temp[0]);
		$temp=explode('*:*w_field_label*:*',$temp[1]);
		array_push($labels, $temp[0]);
	}
	
	
	$fields = array('ids' => $ids, 'types' => $types, 'labels' => $labels);
	
	HTML_contact::form_layout($row, $fields);
}


function form_options(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	
	$option='com_formmaker';
	$db		= JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));

	$id 	= $cid[0];
	$row = JTable::getInstance('formmaker', 'Table');
	// load the row from the db table
	$row->load( $id);

	$canEdit = $user->authorise('core.edit', 'com_formmaker');
	$canEditOwn = $user->authorise('core.edit.own', 'com_formmaker');
	if(!$canEdit)
		if(!$canEditOwn || $row->created_by != $user->id)
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),'error');
	
	$tabs= $mainframe-> getUserStateFromRequest( $option.'tabs', 'tabs','general_op','string' );

	$query = "SELECT * FROM #__formmaker_themes WHERE css LIKE '%.wdform_section%' ORDER BY title";
	$db->setQuery( $query);
	$themes_new = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	$query = "SELECT * FROM #__formmaker_themes WHERE css NOT LIKE '%.wdform_section%' ORDER BY title";
	$db->setQuery( $query);
	$themes_old = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	$query = "SELECT * FROM #__formmaker_query WHERE form_id=".$db->escape((int)$id)." ORDER BY id ASC";
	$db->setQuery( $query);
	$queries = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
	
	$query = "SELECT * FROM #__usergroups";
	$db->setQuery( $query );
	$userGroups = $db->loadObjectList();
	
	$old = false;
	
	if(isset($row->form))
		$old = true;
	

	
	if($old == false || ($old == true && $row->form=='')) 
	{
		HTML_contact::form_options($row, $themes_new, $queries, $tabs, $userGroups);
	}
	else
		HTML_contact::form_options_old($row, $themes_old);


}


function paypal_info(){

	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
    $db = JFactory::getDBO();
	$id 	= JRequest::getVar('id');
	$row = JTable::getInstance('formmaker', 'Table');
	$row->load($id);

	if(!$user->authorise('core.manage.submits', 'com_formmaker'))
	{
		if($user->authorise('core.manage.submits.own', 'com_formmaker') && $row->created_by != $user->id)	
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JACCESS_NOT_PERMITTED'),'error');
	}

	$query = "SELECT * FROM #__formmaker_sessions where group_id=".$db->escape((int)$id);
	$db->setQuery( $query);
	$row = $db->loadObject();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}

	HTML_contact::paypal_info($row);

}

function show_map(){

	$long 	= JRequest::getVar('long');
	$lat 	= JRequest::getVar('lat');

	HTML_contact::show_map($long,$lat);
}


function show_matrix(){

	$matrix_params 	= JRequest::getVar('matrix_params');
    
	HTML_contact::show_matrix($matrix_params);

}

function country_list(){

	$id 	= JRequest::getVar('field_id');

	HTML_contact::country_list($id);

}

function product_option(){

	$id 	= JRequest::getVar('field_id');
	$property_id= JRequest::getVar('property_id');
	HTML_contact::product_option($id,$property_id);

}


//////////////////////////////////////////////////////////////
function gotoedit(){
	$mainframe = JFactory::getApplication();

	$id 	= JRequest::getVar('id');

	$msg ="The form was saved successfully.";
	$link ='index.php?option=com_formmaker&task=edit&cid[]='.$id;

	$mainframe->redirect($link, $msg, 'message');

}

function showredirect(){
	$mainframe = JFactory::getApplication();

	$link = 'index.php?option=com_formmaker&task=forms';

	$mainframe->redirect($link);

}

function add(){

	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.create', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),'error');
		
	$db = JFactory::getDBO();
	$query = "SELECT * FROM #__formmaker_themes ORDER BY title";
	$db->setQuery( $query);
	$themes = $db->loadObjectList();
	if($db->getErrorNum()){
		echo $db->stderr();
		return false;
	}
// display function

	HTML_contact::add($themes);
}

function add_blocked_ips(){
	
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.create', 'com_formmaker') || !$user->authorise('core.block', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=blocked_ips", JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),'error');
    $db = JFactory::getDBO();

	$query = "SELECT * FROM #__formmaker_blocked ORDER BY id";

	$db->setQuery( $query);

	$rows = $db->loadObjectList();

	if($db->getErrorNum()){

		echo $db->stderr();

		return false;

	}

	HTML_contact::add_blocked_ips($rows);
}


function show_submits(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
    $db = JFactory::getDBO();
	$where = array();
	$own_manage = false; 
	
	if(!$user->authorise('core.manage.submits', 'com_formmaker'))
	{
		if($user->authorise('core.manage.submits.own', 'com_formmaker'))
			$own_manage = true; 
		else
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JACCESS_NOT_PERMITTED'),'error');
	}


	jimport('joomla.html.pagination');
	if($own_manage)
		$query = "SELECT id, title FROM #__formmaker WHERE created_by = '".$user->id."' order by title";		
	else
		$query = "SELECT id, title FROM #__formmaker order by title";
	$db->setQuery( $query);
	$forms = $db->loadObjectList();
	if($db->getErrorNum()){	echo $db->stderr();	return false;}

	$option='com_formmaker';
	$task	= JRequest::getCmd('task'); 
	$form_id= $mainframe-> getUserStateFromRequest( $option.'form_id', 'form_id','id','cmd' );

	if($form_id)
	{
		if($own_manage)
			$query = "SELECT id FROM #__formmaker where created_by = '".$user->id."' AND id=".$db->escape((int)$form_id);
		else
			$query = "SELECT id FROM #__formmaker where id=".$db->escape((int)$form_id);
		$db->setQuery( $query);
		$exists = $db->LoadResult();
		if(!$exists)
			$form_id=0;
	}	

	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order2', 'filter_order2','id','cmd' );
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir2', 'filter_order_Dir2','','word' );

	$id_search = $mainframe-> getUserStateFromRequest( $option.'id_search', 'id_search','','string' );
	$id_search = JString::strtolower( $id_search );
	
	$ip_search = $mainframe-> getUserStateFromRequest( $option.'ip_search', 'ip_search','','string' );
	$ip_search = JString::strtolower( $ip_search );
	
	$username_search = $mainframe-> getUserStateFromRequest( $option.'username_search', 'username_search','','string' );
	$username_search = JString::strtolower( $username_search );
	
	$useremail_search = $mainframe-> getUserStateFromRequest( $option.'useremail_search', 'useremail_search','','string' );
	$useremail_search = JString::strtolower( $useremail_search );
	
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

	
	$lists['startdate']= JRequest::getVar('startdate', "");
	$lists['enddate']= JRequest::getVar('enddate', "");
	$lists['hide_label_list']= JRequest::getVar('hide_label_list', "");
	
	if ( $id_search ) {
		$where[] = 'group_id ='.$db->escape($id_search);
	}	
	
	if ( $ip_search ) {
		$where[] = 'ip LIKE "%'.$db->escape($ip_search).'%"';
	}	
	
	if ( $username_search ) {
		$where[] = 'user_id IN (SELECT `id` FROM `#__users` WHERE `username` LIKE "%'.$db->escape($username_search).'%")';
	}

	if ( $useremail_search ) {
		$where[] = 'user_id IN (SELECT `id` FROM `#__users` WHERE `email` LIKE "%'.$db->escape($useremail_search).'%")';
	}		
	
	if($lists['startdate']!='')
		$where[] ="  `date`>='".$lists['startdate']." 00:00:00' ";
	if($lists['enddate']!='')
		$where[] ="  `date`<='".$lists['enddate']." 23:59:59' ";

	if ($form_id == '')
		if($forms)
		$form_id=$forms[0]->id;

	$where[] = 'form_id="'.$form_id.'"';

	$where 		= ( count( $where ) ? '  ' . implode( ' AND ', $where ) : '' );

	$orderby 	= ' ';
	if ($filter_order == 'id' or $filter_order == 'title' or $filter_order == 'mail')
	{
		$orderby 	= ' ORDER BY `date` desc';
	} 
	else 
		if ($filter_order == 'group_id' or $filter_order == 'date' or $filter_order == 'ip')
		{
				$orderby 	= ' ORDER BY '.$filter_order .' '. $filter_order_Dir .'';
		} 
		else
			if ($filter_order == 'username' or $filter_order == 'email')
			{
				$orderby 	= ' ORDER BY (SELECT `'.$filter_order.'` FROM `#__users` WHERE id=user_id) '. $filter_order_Dir .'';
			} 
		
	$query = "SELECT distinct element_label FROM #__formmaker_submits WHERE ". $where;
	$db->setQuery( $query);
	$labels = $db->loadColumn();
	if($db->getErrorNum()){echo $db->stderr();return false;}

	$query = "SELECT id FROM #__formmaker_submits WHERE form_id=".$form_id." and element_label=0 limit 0, 1";
	$db->setQuery( $query);
	$ispaypal = $db->loadResult();
	if($db->getErrorNum()){echo $db->stderr();return false;}

	$query = 'SELECT count(distinct group_id) FROM #__formmaker_submits where form_id ="'.$form_id.'"';
	$db->setQuery( $query );
	$total_entries=$db->loadResult();
	if($db->getErrorNum()){echo $db->stderr();return false;}
	
	$sorted_labels_type= array();
	$sorted_labels_id= array();
	$sorted_labels= array();
	$label_titles=array();
	$rows_ord = array();
	$rows = array();
	$total = 0;
	$join_count='';
	
	if($labels)
	{
		$label_id= array();
		$label_order= array();
		$label_order_original= array();
		$label_type= array();
		
		$this_form = JTable::getInstance('formmaker', 'Table');
		$this_form->load( $form_id);
		
		if(strpos($this_form->label_order, 'type_paypal_'))
		{
			$this_form->label_order=$this_form->label_order."item_total#**id**#Item Total#**label**#type_paypal_payment_total#****#total#**id**#Total#**label**#type_paypal_payment_total#****#0#**id**#Payment Status#**label**#type_paypal_payment_status#****#";
		}
	
		
		$label_all	= explode('#****#',$this_form->label_order);
		$label_all 	= array_slice($label_all,0, count($label_all)-1);   
		
		
		
		foreach($label_all as $key => $label_each) 
		{
			$label_id_each=explode('#**id**#',$label_each);
			array_push($label_id, $label_id_each[0]);
			
			$label_order_each=explode('#**label**#', $label_id_each[1]);
			
			array_push($label_order_original, $label_order_each[0]);
			
			$ptn = "/[^a-zA-Z0-9_]/";
			$rpltxt = "";
			$label_temp=preg_replace($ptn, $rpltxt, $label_order_each[0]);
			array_push($label_order, $label_temp);
			
			array_push($label_type, $label_order_each[1]);
		}
		
		$join_query=array();
		$join_where=array();
		$join='';
		$is_first=true;

		foreach($label_id as $key => $label) 
			if(in_array($label, $labels))
			{
				array_push($sorted_labels_type, $label_type[$key]);
				array_push($sorted_labels, $label_order[$key]);
				array_push($sorted_labels_id, $label);
				array_push($label_titles, $label_order_original[$key]);
				$search_temp = $mainframe-> getUserStateFromRequest( $option.$form_id.'_'.$label.'_search', $form_id.'_'.$label.'_search','','string' );
				$search_temp = JString::strtolower( $search_temp );
				$lists[$form_id.'_'.$label.'_search']	 = $search_temp;
				
				if ( $search_temp ) {
					$join_query[]	='search';
					$join_where[]	=array('label'=>$label, 'search'=>$db->escape($search_temp));
				}	
			}
			
		if(strpos($filter_order,"_field"))
			if (in_array(str_replace("_field", "", $filter_order), $labels))
			{
				$join_query[]	='sort';
				$join_where[]	=array('label'=>str_replace("_field", "", $filter_order));

			}
		
		$cols 	= 'group_id';
		if ($filter_order == 'date' or $filter_order == 'ip')
		{
				$cols 	= 'group_id, date, ip';
		} 
		
		switch(count($join_query))
		{
			case 0:
				$join='SELECT distinct group_id FROM #__formmaker_submits WHERE '. $where;
			break;

			case 1:
				if($join_query[0]=='sort')
				{
					$join		=	'SELECT group_id FROM #__formmaker_submits WHERE '.$where.' AND element_label="'.$join_where[0]['label'].'" ';
					$join_count	=	'SELECT count(group_id) FROM #__formmaker_submits WHERE form_id="'.$form_id.'" AND element_label="'.$join_where[0]['label'].'" ';
					$orderby 	= 	' ORDER BY `element_value` '. $filter_order_Dir .'';
				}
				else
					$join='SELECT group_id FROM #__formmaker_submits WHERE element_label="'.$join_where[0]['label'].'" AND  element_value LIKE "%'.$join_where[0]['search'].'%" AND '. $where;
			break;
					
			default:
				$join='SELECT t.group_id FROM (SELECT '.$cols.'  FROM #__formmaker_submits WHERE '.$where.' AND element_label="'.$join_where[0]['label'].'" AND  element_value LIKE "%'.$join_where[0]['search'].'%" ) as t ';
				
				for($key=1; $key< count($join_query); $key++)
					if($join_query[$key]=='sort')
					{
						$join.='LEFT JOIN (SELECT group_id as group_id'.$key.', element_value   FROM #__formmaker_submits WHERE '.$where.' AND element_label="'.$join_where[$key]['label'].'") as t'.$key.' ON t'.$key.'.group_id'.$key.'=t.group_id ';
						$orderby 	= 	' ORDER BY t'.$key.'.`element_value` '. $filter_order_Dir .'';
					}
					else
						$join.='INNER JOIN (SELECT group_id as group_id'.$key.' FROM #__formmaker_submits WHERE '.$where.' AND element_label="'.$join_where[$key]['label'].'" AND  element_value LIKE "%'.$join_where[$key]['search'].'%" ) as t'.$key.' ON t'.$key.'.group_id'.$key.'=t.group_id ';
			break;

		}		
		
		$pos = strpos($join, 'SELECT t.group_id');
		
		if ($pos === false) 
			$query=str_replace(array('SELECT group_id','SELECT distinct group_id'), array('SELECT count(distinct group_id)','SELECT count(distinct group_id)'),  $join);
		else
			$query=str_replace('SELECT t.group_id', 'SELECT count(t.group_id)',  $join);
		$db->setQuery( $query );
		$total=$db->loadResult();
	
		$pageNav = new JPagination( $total, $limitstart, $limit );	

		$query=$join.' '.$orderby.' ';

		$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit);
		$rows_ord = $db->loadColumn();
		if($db->getErrorNum()){	echo $db->stderr();	return false;}
		
		$where2 = array();
			$where2 [] ="group_id='0'";
		
		foreach($rows_ord as $rows_ordd)
		{
			$where2 [] ="group_id='".$rows_ordd."'";
		}
		
		$where2 = ( count( $where2 ) ? ' WHERE ' . implode( ' OR ', $where2 ).'' : '' );
		$query = "SELECT * FROM #__formmaker_submits ".$where2.'';

		$db->setQuery( $query);
		$rows = $db->loadObjectList();
		if($db->getErrorNum()){
			echo $db->stderr();
			return false;
		}
		
		if($join_count)
		{
			$db->setQuery( $join_count );
			$total_sort=$db->loadResult();
			if($total_sort!=$total_entries)
				$join_count=$total_sort;
			else
				$join_count='';
		}
		
	}
	
	$query = 'SELECT views FROM #__formmaker_views WHERE form_id="'.$db->escape((int)$form_id).'"'	;
	$db->setQuery( $query );
	$total_views = $db->loadResult();	
	
	$pageNav = new JPagination( $total, $limitstart, $limit );	
	
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	
	// search filter
	$lists['id_search']=$id_search;	
	$lists['ip_search']=$ip_search;
	$lists['username_search']=$username_search;
	$lists['useremail_search']=$useremail_search;
    // display function

	HTML_contact::show_submits($rows, $forms, $lists, $pageNav, $sorted_labels, $label_titles, $rows_ord, $form_id, $sorted_labels_id, $sorted_labels_type, $total_entries, $total_views, $join_count);

}

function show(){

	$mainframe = JFactory::getApplication();
    $db = JFactory::getDBO();
	$option='com_formmaker';
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order', 'filter_order','id','cmd' );
	

	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir','','word' );
	$filter_state = $mainframe->getUserStateFromRequest( $option.'filter_state', 'filter_state', '','word' );
	$search = $mainframe-> getUserStateFromRequest( $option.'search', 'search','','string' );
	
	
	$search = JString::strtolower( $search );
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

	$where = array();
	if ( $search ) {

		$where[] = 'title LIKE "%'.$db->escape($search).'%"';

	}	


	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

	if ($filter_order == 'id' or $filter_order == 'group_id' or $filter_order == 'date' or $filter_order == 'ip'){

		$orderby 	= ' ORDER BY id';

	} else {

		$orderby 	= ' ORDER BY '. 

         $filter_order .' '. $filter_order_Dir .', id';

	}	

	

	// get the total number of records

	$query = 'SELECT COUNT(*)'

	. ' FROM #__formmaker'

	. $where

	;
	$db->setQuery( $query );

	$total = $db->loadResult();



	jimport('joomla.html.pagination');

	$pageNav = new JPagination( $total, $limitstart, $limit );	

	$query = "SELECT * FROM #__formmaker". $where. $orderby;

	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();

	if($db->getErrorNum()){

		echo $db->stderr();

		return false;

	}



	// table ordering

	$lists['order_Dir']	= $filter_order_Dir;

	$lists['order']		= $filter_order;	



	// search filter	

        $lists['search']= $search;	

	

    // display function

	HTML_contact::show($rows, $pageNav, $lists);

}

function show_blocked_ips(){


	$mainframe = JFactory::getApplication();

	

    $db = JFactory::getDBO();



	$option='com_formmaker';



	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_ips', 'filter_order_ips','id','cmd' );

	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_ips', 'filter_order_Dir_ips','desc','word' );

	$filter_state = $mainframe->getUserStateFromRequest( $option.'filter_state', 'filter_state', '','word' );

	$search_ip = $mainframe-> getUserStateFromRequest( $option.'search_ip', 'search_ip','','string' );

	$search_ip = JString::strtolower( $search_ip );

	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

	$where = array();


	if ( $search_ip ) {

		$where[] = 'ip LIKE "%'.$db->escape($search_ip).'%"';

	}	


	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

	if ($filter_order == 'id'){

		$orderby 	= ' ORDER BY id';

	} else {

		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', id';

	}	

	// get the total number of records


	$query = 'SELECT COUNT(*) FROM #__formmaker_blocked'. $where;

	$db->setQuery( $query );

	$total = $db->loadResult();


	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );	


	$query = "SELECT * FROM #__formmaker_blocked". $where. $orderby;


	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );


	$rows = $db->loadObjectList();


	if($db->getErrorNum()){


		echo $db->stderr();


		return false;

	}

	// table ordering

	$lists['order_Dir']	= $filter_order_Dir;

	$lists['order']		= $filter_order;	

	// search filter	

        $lists['search_ip']= $search_ip;	


    // display function

	HTML_contact::show_blocked_ips($rows, $pageNav, $lists);

}


function show_themes(){

	$mainframe = JFactory::getApplication();
	$option='com_formmaker';
	
    $db = JFactory::getDBO();
	
	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order_themes', 'filter_order_themes','id','cmd' );
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir_themes', 'filter_order_Dir_themes','desc','word' );
	$filter_state = $mainframe-> getUserStateFromRequest( $option.'filter_state', 'filter_state', '','word' );
	$search_theme = $mainframe-> getUserStateFromRequest( $option.'search_theme', 'search_theme','','string' );
	$search_theme = JString::strtolower( $search_theme );
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	$where = array();

	if ( $search_theme ) {
		$where[] = '#__formmaker_themes.title LIKE "%'.$db->escape($search_theme).'%"';
	}	
	
	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	if ($filter_order == 'id'){
		$orderby 	= ' ORDER BY id '.$filter_order_Dir;
	} else {
		$orderby 	= ' ORDER BY '. 
         $filter_order .' '. $filter_order_Dir .', id';
	}	
	
	// get the total number of records
	$query = 'SELECT COUNT(*)'. ' FROM #__formmaker_themes'. $where;
	$db->setQuery( $query );
	$total = $db->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );	
	
	$query = "SELECT * FROM #__formmaker_themes". $where. $orderby;
	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){		echo $db->stderr();		return false;	}

	// table ordering

	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;	

	// search filter	
        $lists['search_theme']= $search_theme;	

    // display function

	HTML_contact::show_themes($rows, $pageNav, $lists);

}

function preview_formmaker()
{
	$getparams=JRequest::get('get');
	
    $db = JFactory::getDBO();
	
	$query = "SELECT css FROM #__formmaker_themes WHERE id=".$getparams['theme'];
	$db->setQuery( $query);
	$css = $db->loadResult();
	if($db->getErrorNum()){		$css='';	}

	HTML_contact::preview_formmaker($css);
}

function edit_css(){

	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();

	$getparams=JRequest::get('get');
	$canCreate = $user->authorise('core.create', 'com_formmaker');
	$canEdit = $user->authorise('core.edit', 'com_formmaker');
	
	if(!$canCreate && !$canEdit) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=form_options&cid[]=".$getparams['form_id'], JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),'error');

    $db = JFactory::getDBO();

	$query = "SELECT * FROM #__formmaker_themes WHERE id=".$getparams['theme'];

	$db->setQuery($query);

	$theme = $db->loadObject();
	
	
	$query = "SELECT * FROM #__formmaker WHERE id=".$getparams['form_id'];

	$db->setQuery($query);

	$form = $db->loadObject();	


	HTML_contact::editCss($theme,$form);


}


function setdefault()
{
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.edit', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=themes", JText::_('JACTION_NOT_PERMITTED'),'error');	
	$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
	JArrayHelper::toInteger($cid);
	
	if (isset($cid[0]) && $cid[0]) 
		$id = $cid[0];
	else 
	{
		$mainframe->redirect(  'index.php?option=com_formmaker&task=themes',JText::_('No Items Selected'), 'message' );
		return false;
	}
	
	$db = JFactory::getDBO();

	// Clear home field for all other items
	$query = 'UPDATE #__formmaker_themes SET `default` = 0 WHERE 1';
	$db->setQuery( $query );
	if ( !$db->query() ) {
		$msg =$db->getErrorMsg();
		echo $msg;
		return false;
	}

	// Set the given item to home
	$query = 'UPDATE #__formmaker_themes SET `default` = 1 WHERE id = '.(int) $id;
	$db->setQuery( $query );
	if ( !$db->query() ) {
		$msg = $db->getErrorMsg();
		return false;
	}
		
	$msg = JText::_( 'Default Theme Seted' );
	$mainframe->redirect( 'index.php?option=com_formmaker&task=themes' ,$msg, 'message');
}

function add_themes(){

	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.create', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=themes", JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),'error');		
	$db		= JFactory::getDBO();
	$query = "SELECT * FROM #__formmaker_themes where `default`=1";
	$db->setQuery($query);
	$def_theme = $db->loadObject();
// display function
		
	HTML_contact::add_themes($def_theme);
}

function edit_themes(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.edit', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=themes", JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),'error');	
	$db		= JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));

	$id 	= $cid[0];
	$row = JTable::getInstance('formmaker_themes', 'Table');
	// load the row from the db table
	$row->load( $id);
	
	// display function 
	HTML_contact::edit_themes( $row);
}

function edit_blocked_ips(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.edit', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=blocked_ips", JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),'error');	
	$db		= JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));

	$id 	= $cid[0];
	$row = JTable::getInstance('formmaker_blocked', 'Table');

	// load the row from the db table
	$row->load( $id);

	// display function 

	HTML_contact::edit_blocked_ips($row);
}

function remove_themes(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.delete', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=themes", JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'),'error');	
  // Initialize variables	
  $db = JFactory::getDBO();
  // Define cid array variable
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );
  // Make sure cid array variable content integer format
  JArrayHelper::toInteger($cid);
  $query = 'SELECT id FROM #__formmaker_themes WHERE `default`=1 LIMIT 1';
  $db->setQuery( $query );
  $def = $db->loadResult();
  if($db->getErrorNum()){
	  echo $db->stderr();
	  return false;
  }
  $msg = 'The theme(s) has been successfully deleted.';
  $k=array_search($def, $cid);
  if ($k>0)
  {
	  $cid[$k]=0;
	  $msg="You can't delete default theme";
  }
  
  if ($cid[0]==$def)
  {
	  $cid[0]=0;
	  $msg="You can't delete default theme";
  }
  
  // If any item selected
  if (count( $cid )) {
    // Prepare sql statement, if cid array more than one, 
    // will be "cid1, cid2, ..."
    $cids = implode( ',', $cid );
    // Create sql statement

    $query = 'DELETE FROM #__formmaker_themes'.' WHERE id IN ( '. $cids .' )';
    // Execute query
    $db->setQuery( $query );
    if (!$db->query()) {
      echo "<script> alert('".$db->getErrorMsg(true)."'); 
      window.history.go(-1); </script>\n";
    }
	
  }
  // After all, redirect again to frontpage
  if($msg)
  $mainframe->redirect( "index.php?option=com_formmaker&task=themes",  $msg, 'message');
  else
  $mainframe->redirect( "index.php?option=com_formmaker&task=themes");
}

function save_as_copy(){
	$old = false;
	$mainframe = JFactory::getApplication();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));
	$id 	= $cid[0];
	$row = JTable::getInstance('formmaker', 'Table');
	// load the row from the db table
	$row->load( $id);
	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	

	if(isset($row->form)) 
		$old = true;

	$fid=$row->id;
	if($fid && ($old == true && $row->form!=''))
		$row->form = JRequest::getVar( 'form', '','post', 'string', JREQUEST_ALLOWRAW );
	else
	{
		$row->form_fields = JRequest::getVar( 'form_fields', '','post', 'string', JREQUEST_ALLOWRAW );
	}
	
	$row->id='';
	$new=true;
	
	$row->form_front = JRequest::getVar( 'form_front', '','post', 'string', JREQUEST_ALLOWRAW );
	$fid = JRequest::getVar( 'id',0 );
	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}
	
	if($new)
	{
		$db = JFactory::getDBO();
		$db->setQuery("INSERT INTO #__formmaker_views (form_id, views) VALUES('".$row->id."', 0)" ); 
		$db->query();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
	}
		$msg = 'The form has been saved successfully.';
		$link = 'index.php?option=com_formmaker';
		$mainframe->redirect($link, $msg, 'message');
		
		
}

function save($task){

    $old = false;
	$mainframe = JFactory::getApplication();
	$row = JTable::getInstance('formmaker', 'Table');

	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	$new=(!isset($row->id));
	
	if(isset($row->form)) 
		$old = true;

	$fid=$row->id;
	if($fid && ($old == true && $row->form!=''))
		$row->form = JRequest::getVar( 'form', '','post', 'string', JREQUEST_ALLOWRAW );
	else
	{
		$row->form_fields = JRequest::getVar( 'form_fields', '','post', 'string', JREQUEST_ALLOWRAW );
	}
	
	$row->form_front = JRequest::getVar( 'form_front', '','post', 'string', JREQUEST_ALLOWRAW );
	$row->sortable = JRequest::getVar( 'sortable', '','post', 'string', JREQUEST_ALLOWRAW );

	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}
	
	if($new)
	{
		$db = JFactory::getDBO();
		$db->setQuery("INSERT INTO #__formmaker_views (form_id, views) VALUES('".$row->id."', 0)" ); 
		$db->query();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
	}
	
	
	switch ($task)
	{
		case 'apply':
		HTML_contact::forchrome($row->id);
		break;
		case 'save_and_new':
		$msg = 'The form has been saved successfully.';
		$link = 'index.php?option=com_formmaker&task=add';
		$mainframe->redirect($link, $msg, 'message');
		break;
		
		case 'save':
		$msg = 'The form has been saved successfully.';
		$link = 'index.php?option=com_formmaker';
		$mainframe->redirect($link, $msg, 'message');
		break;
		
		case 'return_id':
			return $row->id;
		break;
		default:
		break;
	}
}

function save_blocked_ips($task){



	$mainframe = JFactory::getApplication();

	$row = JTable::getInstance('formmaker_blocked', 'Table');

	if(!$row->bind(JRequest::get('post')))

	{

		JError::raiseError(500, $row->getError() );

	}



	if(!$row->store()){

		JError::raiseError(500, $row->getError() );

	}

	switch ($task)

	{

		case 'apply_blocked_ips':

		$msg ='IP has been saved successfully.';

		$link ='index.php?option=com_formmaker&task=edit_blocked_ips&cid[]='.$row->id;

		break;

		default:

		$msg = 'IP has been saved successfully.';

		$link ='index.php?option=com_formmaker&task=blocked_ips';

		break;
	}


	$mainframe->redirect($link, $msg, 'message');

}



function save_themes($task){

	$mainframe = JFactory::getApplication();
	$row = JTable::getInstance('formmaker_themes', 'Table');
	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}

	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}

	switch ($task)

	{
		case 'apply_themes':
		$msg ='Theme has been saved successfully.';
		$link ='index.php?option=com_formmaker&task=edit_themes&cid[]='.$row->id;
		break;

		default:
		$msg = 'Theme has been saved successfully.';
		$link ='index.php?option=com_formmaker&task=themes';
		break;
	}
	
	$mainframe->redirect($link, $msg, 'message');

}

function save_new_theme($task){

	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	
	$id 	= JRequest::getVar('form_id');

	$form = JTable::getInstance('formmaker', 'Table');
	$form->load($id);
	
	if(!$user->authorise('core.create', 'com_formmaker') && $task=='save_new_theme') 
		$mainframe->redirect( "index.php?option=com_formmaker&task=edit_css&tmpl=component&theme=".$form->theme."&form_id=".$id."&new=0", JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),'error');
	
	if(!$user->authorise('core.edit', 'com_formmaker') && ($task=='save_for_edit' || $task=='apply_for_edit')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=edit_css&tmpl=component&theme=".$form->theme."&form_id=".$id."&new=0", JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),'error');
	
	$row = JTable::getInstance('formmaker_themes', 'Table');

	if(!$row->bind(JRequest::get('post')))

	{
		JError::raiseError(500, $row->getError() );

	}

	if($task=='save_new_theme')
	$row->title = ($row->title).' '.($form->title);

	if(!$row->store()){

		JError::raiseError(500, $row->getError() );

	}
	
	$form->theme = $row->id;
	
	if(!$form->store()){

		JError::raiseError(500, $row->getError() );

	}
		
		switch ($task)
		{
			case 'save_new_theme':	
			$msg = '';
			$link ='index.php?option=com_formmaker&task=edit_css&tmpl=component&theme='.$row->id.'&form_id='.$id.'&new=1';

			break;
		
			case 'save_for_edit':
			$msg = '';
			$link ='index.php?option=com_formmaker&task=edit_css&tmpl=component&theme='.$row->id.'&form_id='.$id.'&new=1';
			
			break;
			
			case 'apply_for_edit':
			$msg = '';
			$link ='index.php?option=com_formmaker&task=edit_css&tmpl=component&theme='.$row->id.'&form_id='.$id.'&new=0';

			break;
		}
	$mainframe->redirect($link, $msg);


}



function save_form_options($task){

	$mainframe = JFactory::getApplication();
	$option='com_formmaker';
	$tabs= $mainframe-> getUserStateFromRequest( $option.'tabs', 'tabs','','string' );

	$row = JTable::getInstance('formmaker', 'Table');
	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	
	if($row->mail_from=="other")
		$row->mail_from=JRequest::getVar( 'mail_from_other');
	if($row->reply_to=="other")
		$row->reply_to=JRequest::getVar( 'reply_to_other');

	$row->script_mail = JRequest::getVar( 'script_mail', '','post', 'string', JREQUEST_ALLOWRAW );
	$row->submit_text = JRequest::getVar( 'submit_text', '','post', 'string', JREQUEST_ALLOWRAW );
	$row->script_mail_user = JRequest::getVar( 'script_mail_user', '','post', 'string', JREQUEST_ALLOWRAW );

	$row->send_to="";
	for($i=0; $i<20; $i++)
	{
		$send_to=JRequest::getVar( 'send_to'.$i);
		if(isset($send_to))
		{
			$row->send_to.='*'.$send_to.'*';
		}
	}

	
	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}

	switch ($task)

	{
		case 'apply_form_options':
		$msg ='Form options have been saved successfully.';
		$link ='index.php?option=com_formmaker&task=form_options&cid[]='.$row->id;
		break;
		case 'save_form_options':
		default:
		$msg = 'Form options have been saved successfully.';
		$link ='index.php?option=com_formmaker&task=edit&cid[]='.$row->id;
		break;
	}
	
	$mainframe->redirect($link, $msg, 'message');

}

function save_form_layout($task){
	$mainframe = JFactory::getApplication();
	$row = JTable::getInstance('formmaker', 'Table');

	if(!$row->bind(JRequest::get('post')))
	{
		JError::raiseError(500, $row->getError() );
	}
	$row->custom_front = JRequest::getVar( 'custom_front', '','post', 'string', JREQUEST_ALLOWRAW );
	$autogen_layout=JRequest::getVar( 'autogen_layout');
	if(!isset($autogen_layout))
		$row->autogen_layout = 0;
		
	if(!$row->store()){
		JError::raiseError(500, $row->getError() );
	}
	switch ($task)
	{
		case 'apply_form_layout':
		$msg ='Form layout have been saved successfully.';
		$link ='index.php?option=com_formmaker&task=form_layout&cid[]='.$row->id;
		break;
		case 'save_form_layout':
		default:
		$msg = 'Form layout have been saved successfully.';
		$link ='index.php?option=com_formmaker&task=edit&cid[]='.$row->id;
		break;
	}

	$mainframe->redirect($link, $msg, 'message');
}

function edit(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	$old = false;
	$db		= JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));
	$id 	= $cid[0];
	$row = JTable::getInstance('formmaker', 'Table');
	// load the row from the db table
	$row->load( $id);
	
	$canEdit = $user->authorise('core.edit', 'com_formmaker');
	$canEditOwn = $user->authorise('core.edit.own', 'com_formmaker');
	if(!$canEdit)
		if(!$canEditOwn || $row->created_by != $user->id)
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),'error');
	
		$labels2= array();
		
		$label_id= array();
		$label_order_original= array();
		$label_type= array();
		
		$label_all	= explode('#****#',$row->label_order);
		$label_all 	= array_slice($label_all,0, count($label_all)-1);   

		foreach($label_all as $key => $label_each) 
		{
			$label_id_each=explode('#**id**#',$label_each);
			array_push($label_id, $label_id_each[0]);
			
			$label_oder_each=explode('#**label**#', $label_id_each[1]);
			array_push($label_order_original, addslashes($label_oder_each[0]));
			array_push($label_type, $label_oder_each[1]);
		
			
		}
		
	$labels2['id']='"'.implode('","',$label_id).'"';
	$labels2['label']='"'.implode('","',$label_order_original).'"';
	$labels2['type']='"'.implode('","',$label_type).'"';
	
	if(isset($row->form)) 
		$old = true;

	
	if($old == false || ($old == true && $row->form=='')) 
	{
	$ids = array();
	$types = array();
	$labels = array();
	$paramss = array();
	$fields=explode('*:*new_field*:*',$row->form_fields);
	$fields 	= array_slice($fields,0, count($fields)-1);   
	foreach($fields as $field)
	{
		$temp=explode('*:*id*:*',$field);
		array_push($ids, $temp[0]);
		$temp=explode('*:*type*:*',$temp[1]);
		array_push($types, $temp[0]);
		$temp=explode('*:*w_field_label*:*',$temp[1]);
		array_push($labels, $temp[0]);
		array_push($paramss, $temp[1]);
	}
	
	$form=$row->form_front;
	foreach($ids as $ids_key => $id)
	{	
		$label=$labels[$ids_key];
		$type=$types[$ids_key];
		$params=$paramss[$ids_key];
		if( strpos($form, '%'.$id.' - '.$label.'%') || strpos($form, '%'.$id.' -'.$label.'%'))
		{
			$rep='';
			$arrows='';	
			$param=array();
			$param['attributes'] = '';
			
			switch($type)
			{
				case 'type_section_break':
				{
					$arrows =$arrows.'<div id="wdform_arrows'.$id.'" class="wdform_arrows"><div id="X_'.$id.'" class="element_toolbar"><img src="components/com_formmaker/images/delete_el.png" title="Remove the field" onclick="remove_section_break(&quot;'.$id.'&quot;)"></div><div id="edit_'.$id.'" class="element_toolbar"><img src="components/com_formmaker/images/edit.png" title="Edit the field" onclick="edit(&quot;'.$id.'&quot;)"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">custom_'.$id.'</span></div><div id="dublicate_'.$id.'" class="element_toolbar"><img src="components/com_formmaker/images/dublicate.png" title="Duplicate the field" onclick="dublicate(&quot;'.$id.'&quot;)"></div></div>';
					break;
				}
				case 'type_editor':
				{
					$arrows =$arrows.'<div id="wdform_arrows'.$id.'" class="wdform_arrows" type="type_editor" style=""><div id="X_'.$id.'" valign="middle" align="right" class="element_toolbar"><img src="components/com_formmaker/images/delete_el.png" title="Remove the field" onclick="remove_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;delete_el&quot;)" onmouseout="chnage_icons_src(this,&quot;delete_el&quot;)"></div><div id="left_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/left.png" title="Move the field to the left" onclick="left_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;left&quot;)" onmouseout="chnage_icons_src(this,&quot;left&quot;)"></div><div id="up_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/up.png" title="Move the field up" onclick="up_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;up&quot;)" onmouseout="chnage_icons_src(this,&quot;up&quot;)"></div><div id="down_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/down.png" title="Move the field down" onclick="down_row(&quot;'.$id.'&quot;)"  onmouseover="chnage_icons_src(this,&quot;down&quot;)" onmouseout="chnage_icons_src(this,&quot;down&quot;)"></div><div id="right_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/right.png" title="Move the field to the right" onclick="right_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;right&quot;)" onmouseout="chnage_icons_src(this,&quot;right&quot;)"></div><div id="edit_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/edit.png" title="Edit the field" onclick="edit(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;edit&quot;)" onmouseout="chnage_icons_src(this,&quot;edit&quot;)"></div><div id="dublicate_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/dublicate.png" title="Duplicate the field" onclick="dublicate(&quot;'.$id.'&quot;)"  onmouseover="chnage_icons_src(this,&quot;dublicate&quot;)" onmouseout="chnage_icons_src(this,&quot;dublicate&quot;)"></div><div id="page_up_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/page_up.png" title="Move the field to the upper page" onclick="page_up(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_up&quot;)" onmouseout="chnage_icons_src(this,&quot;page_up&quot;)"></div><div id="page_down_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/page_down.png" title="Move the field to the lower page" onclick="page_down(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_down&quot;)" onmouseout="chnage_icons_src(this,&quot;page_down&quot;)"></div></div>';
					break;
				}
				case 'type_send_copy':
				case 'type_captcha':
				case 'type_recaptcha':
				{
					$arrows =$arrows.'<div id="wdform_arrows'.$id.'" class="wdform_arrows"><div id="X_'.$id.'" valign="middle" align="right" class="element_toolbar"><img src="components/com_formmaker/images/delete_el.png" title="Remove the field" onclick="remove_row(&quot;'.$id.'&quot;)"></div><div id="left_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/left.png" title="Move the field to the left" onclick="left_row(&quot;'.$id.'&quot;)"></div><div id="up_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/up.png" title="Move the field up" onclick="up_row(&quot;'.$id.'&quot;)"></div><div id="down_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/down.png" title="Move the field down" onclick="down_row(&quot;'.$id.'&quot;)"></div><div id="right_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/right.png" title="Move the field to the right" onclick="right_row(&quot;'.$id.'&quot;)"></div><div id="edit_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/edit.png" title="Edit the field" onclick="edit(&quot;'.$id.'&quot;)"></div><div id="page_up_'.$id.'" valign="middle" class="element_toolbar"></div><div id="page_up_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/page_up.png" title="Move the field to the upper page" onclick="page_up(&quot;'.$id.'&quot;)"></div><div id="page_down_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/page_down.png" title="Move the field to the lower page" onclick="page_down(&quot;'.$id.'&quot;)"></div></div>';
					break;
				}
				
				default :
				{
					$arrows =$arrows.'<div id="wdform_arrows'.$id.'" class="wdform_arrows"><div id="X_'.$id.'" valign="middle" align="right" class="element_toolbar"><img src="components/com_formmaker/images/delete_el.png" title="Remove the field" onclick="remove_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;delete_el&quot;)" onmouseout="chnage_icons_src(this,&quot;delete_el&quot;)"></div><div id="left_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/left.png" title="Move the field to the left" onclick="left_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;left&quot;)" onmouseout="chnage_icons_src(this,&quot;left&quot;)"></div><div id="up_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/up.png" title="Move the field up" onclick="up_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;up&quot;)" onmouseout="chnage_icons_src(this,&quot;up&quot;)"></div><div id="down_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/down.png" title="Move the field down" onclick="down_row(&quot;'.$id.'&quot;)"  onmouseover="chnage_icons_src(this,&quot;down&quot;)" onmouseout="chnage_icons_src(this,&quot;down&quot;)"></div><div id="right_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/right.png" title="Move the field to the right" onclick="right_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;right&quot;)" onmouseout="chnage_icons_src(this,&quot;right&quot;)"></div><div id="edit_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/edit.png" title="Edit the field" onclick="edit(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;edit&quot;)" onmouseout="chnage_icons_src(this,&quot;edit&quot;)"></div><div id="dublicate_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/dublicate.png" title="Duplicate the field" onclick="dublicate(&quot;'.$id.'&quot;)"  onmouseover="chnage_icons_src(this,&quot;dublicate&quot;)" onmouseout="chnage_icons_src(this,&quot;dublicate&quot;)"></div><div id="page_up_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/page_up.png" title="Move the field to the upper page" onclick="page_up(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_up&quot;)" onmouseout="chnage_icons_src(this,&quot;page_up&quot;)"></div><div id="page_down_'.$id.'" valign="middle" class="element_toolbar"><img src="components/com_formmaker/images/page_down.png" title="Move the field to the lower page" onclick="page_down(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_down&quot;)" onmouseout="chnage_icons_src(this,&quot;page_down&quot;)"></div></div>';
					break;
				}
				
			}
			
			switch($type)
			{
				case 'type_section_break':
				{
					$params_names=array('w_editor');
					$temp=$params;
					foreach($params_names as $params_name )
					{	
						$temp=explode('*:*'.$params_name.'*:*',$temp);
						$param[$params_name] = $temp[0];
						$temp=$temp[1];
					}
					$rep ='<div id="wdform_field'.$id.'" type="type_section_break" class="wdform_field_section_break">'.$arrows.'<div id="'.$id.'_element_sectionform_id_temp" align="left" class="wdform_section_break">'.$param['w_editor'].'</div></div><div id="'.$id.'_element_labelform_id_temp" style="color:red;">custom_'.$id.'</div>';
					break;
				}
				
				case 'type_editor':
				{
					$params_names=array('w_editor');
					$temp=$params;
					foreach($params_names as $params_name )
					{	
						$temp=explode('*:*'.$params_name.'*:*',$temp);
						$param[$params_name] = $temp[0];
						$temp=$temp[1];
					}
					
					$rep =$arrows.'<div id="wdform_field'.$id.'" type="type_editor" class="wdform_field">'.$param['w_editor'].'</div><span id="'.$id.'_element_labelform_id_temp" style="color: red;">custom_'.$id.'</span>';
					break;
				}
				case 'type_send_copy':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_first_val','w_required');
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

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$input_active = ($param['w_first_val']=='true' ? "checked='checked'" : "");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
					$rep ='<div id="wdform_field'.$id.'" type="type_send_copy" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" style="display: '.$param['w_field_label_pos'].'"><input type="hidden" value="type_send_copy" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="checkbox" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" onclick="set_checked(&quot;'.$id.'&quot;,&quot;&quot;,&quot;form_id_temp&quot;)" '.$input_active.' '.$param['attributes'].' disabled /></div></div>';
				
					break;
				}

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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
					$rep ='<div id="wdform_field'.$id.'" type="type_text" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_text" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><input type="text" class="'.$input_active.'" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'" onfocus="delete_value(&quot;'.$id.'_elementform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_elementform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div>';
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
									
					$rep ='<div id="wdform_field'.$id.'" type="type_number" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp"  class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'"><input type="hidden" value="type_number" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><input type="text" class="'.$input_active.'" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'" onkeypress="return check_isnum(event)" onfocus="delete_value(&quot;'.$id.'_elementform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_elementform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div>';
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$rep ='<div id="wdform_field'.$id.'" type="type_password" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp"  class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_password" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><input type="password" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div>';
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}
					
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
					$rep ='<div id="wdform_field'.$id.'" type="type_textarea" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display:'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><input type="hidden" value="type_textarea" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><textarea class="'.$input_active.'" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" title="'.$param['w_title'].'"  onfocus="delete_value(&quot;'.$id.'_elementform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_elementform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" style="width: '.$param['w_size_w'].'px; height: '.$param['w_size_h'].'px;" '.$param['attributes'].' disabled>'.$param['w_first_val'].'</textarea></div></div>';
					break;
				}
			
				case 'type_phone':
				{
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}
					
					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
					$rep ='<div id="wdform_field'.$id.'" type="type_phone" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_phone" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><div id="'.$id.'_table_name" style="display: table;"><div id="'.$id.'_tr_name1" style="display: table-row;"><div id="'.$id.'_td_name_input_first" style="display: table-cell;"><input type="text" class="'.$input_active.'" id="'.$id.'_element_firstform_id_temp" name="'.$id.'_element_firstform_id_temp" value="'.$w_first_val[0].'" title="'.$w_title[0].'" onfocus="delete_value(&quot;'.$id.'_element_firstform_id_temp&quot;)"onblur="return_value(&quot;'.$id.'_element_firstform_id_temp&quot;)"onchange="change_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" onkeypress="return check_isnum(event)"style="width: 50px;" '.$param['attributes'].' disabled /><span class="wdform_line" style="margin: 0px 4px; padding: 0px;">-</span></div><div id="'.$id.'_td_name_input_last" style="display: table-cell;"><input type="text" class="'.$input_active.'" id="'.$id.'_element_lastform_id_temp" name="'.$id.'_element_lastform_id_temp" value="'.$w_first_val[1].'" title="'.$w_title[1].'" onfocus="delete_value(&quot;'.$id.'_element_lastform_id_temp&quot;)"onblur="return_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" onkeypress="return check_isnum(event)"style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div><div id="'.$id.'_tr_name2" style="display: table-row;"><div id="'.$id.'_td_name_label_first" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_area_code">'.$w_mini_labels[0].'</label></div><div id="'.$id.'_td_name_label_last" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_phone_number">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
					break;
				}
				case 'type_name':
				{
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}
					
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					

					if($param['w_name_format']=='normal')
					{
						$w_name_format = '<div id="'.$id.'_td_name_input_first" style="display: table-cell;"><input type="text" class="'.($w_first_val[0]==$w_title[0] ? "input_deactive" : "input_active").'" id="'.$id.'_element_firstform_id_temp" name="'.$id.'_element_firstform_id_temp" value="'.$w_first_val[0].'" title="'.$w_title[0].'" onfocus="delete_value(&quot;'.$id.'_element_firstform_id_temp&quot;)"onblur="return_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" style="margin-right: 10px; width: '.$param['w_size'].'px;"'.$param['attributes'].' disabled /></div><div id="'.$id.'_td_name_input_last" style="display: table-cell;"><input type="text" class="'.($w_first_val[1]==$w_title[1] ? "input_deactive" : "input_active").'" id="'.$id.'_element_lastform_id_temp" name="'.$id.'_element_lastform_id_temp" value="'.$w_first_val[1].'" title="'.$w_title[1].'" onfocus="delete_value(&quot;'.$id.'_element_lastform_id_temp&quot;)"onblur="return_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" style="margin-right: 10px; width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled/></div>';
						$w_name_format_mini_labels = '<div id="'.$id.'_tr_name2" style="display: table-row;"><div id="'.$id.'_td_name_label_first" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_first">'.$w_mini_labels[1].'</label></div><div id="'.$id.'_td_name_label_last" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_last">'.$w_mini_labels[2].'</label></div></div>';
					}
					else
					{
						$w_name_format = '<div id="'.$id.'_td_name_input_title" style="display: table-cell;"><input type="text" class="'.($w_first_val[0]==$w_title[0] ? "input_deactive" : "input_active").'" id="'.$id.'_element_titleform_id_temp" name="'.$id.'_element_titleform_id_temp" value="'.$w_first_val[0].'" title="'.$w_title[0].'" onfocus="delete_value(&quot;'.$id.'_element_titleform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_titleform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_titleform_id_temp&quot;)" style="margin: 0px 10px 0px 0px; width: 40px;" disabled /></div><div id="'.$id.'_td_name_input_first" style="display: table-cell;"><input type="text" class="'.($w_first_val[1]==$w_title[1] ? "input_deactive" : "input_active").'" id="'.$id.'_element_firstform_id_temp" name="'.$id.'_element_firstform_id_temp" value="'.$w_first_val[1].'" title="'.$w_title[1].'" onfocus="delete_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" style="margin-right: 10px; width: '.$param['w_size'].'px;" disabled /></div><div id="'.$id.'_td_name_input_last" style="display: table-cell;"><input type="text" class="'.($w_first_val[2]==$w_title[2] ? "input_deactive" : "input_active").'" id="'.$id.'_element_lastform_id_temp" name="'.$id.'_element_lastform_id_temp" value="'.$w_first_val[2].'" title="'.$w_title[2].'" onfocus="delete_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" style="margin-right: 10px; width: '.$param['w_size'].'px;" disabled /></div><div id="'.$id.'_td_name_input_middle" style="display: table-cell;"><input type="text" class="'.($w_first_val[3]==$w_title[3] ? "input_deactive" : "input_active").'" id="'.$id.'_element_middleform_id_temp" name="'.$id.'_element_middleform_id_temp" value="'.$w_first_val[3].'" title="'.$w_title[3].'" onfocus="delete_value(&quot;'.$id.'_element_middleform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_middleform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_middleform_id_temp&quot;)" style="width: '.$param['w_size'].'px;" disabled/></div>';
						$w_name_format_mini_labels ='<div id="'.$id.'_tr_name2" style="display: table-row;"><div id="'.$id.'_td_name_label_title" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_title">'.$w_mini_labels[0].'</label></div><div id="'.$id.'_td_name_label_first" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_first">'.$w_mini_labels[1].'</label></div><div id="'.$id.'_td_name_label_last" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_last">'.$w_mini_labels[2].'</label></div><div id="'.$id.'_td_name_label_middle" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_middle">'.$w_mini_labels[3].'</label></div></div>';
					}
		
					$rep ='<div id="wdform_field'.$id.'" type="type_name" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_name" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><div id="'.$id.'_table_name" cellpadding="0" cellspacing="0" style="display: table;"><div id="'.$id.'_tr_name1" style="display: table-row;">'.$w_name_format.'    </div>'.$w_name_format_mini_labels.'   </div></div></div>';
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					
					
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					$w_disabled_fields = explode('***',$param['w_disabled_fields']);
					
					$hidden_inputs = '';

					$labels_for_id = array('street1', 'street2', 'city', 'state', 'postal', 'country');
					foreach($w_disabled_fields as $key=>$w_disabled_field)
					{
						if($key!=6)
						{
							if($w_disabled_field=='yes')
							$hidden_inputs .= '<input type="hidden" id="'.$id.'_'.$labels_for_id[$key].'form_id_temp" value="'.$w_mini_labels[$key].'" id_for_label="'.($id+$key).'">';
						}
					}
					
										
					$address_fields ='';
					$g=0;
					if($w_disabled_fields[0]=='no')
					{
					$g+=2;
					$address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="'.$id.'_street1form_id_temp" name="'.$id.'_street1form_id_temp" onchange="change_value(&quot;'.$id.'_street1form_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled/><label class="mini_label" id="'.$id.'_mini_label_street1" style="display: block;">'.$w_mini_labels[0].'</label></span>';
					}
					
					if($w_disabled_fields[1]=='no')
					{
					$g+=2;
					$address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="'.$id.'_street2form_id_temp" name="'.($id+1).'_street2form_id_temp" onchange="change_value(&quot;'.$id.'_street2form_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled/><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_street2">'.$w_mini_labels[1].'</label></span>';
					}
					
					if($w_disabled_fields[2]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: left; width: 48%; padding-bottom: 8px;"><input type="text" id="'.$id.'_cityform_id_temp" name="'.($id+2).'_cityform_id_temp" onchange="change_value(&quot;'.$id.'_cityform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled/><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_city">'.$w_mini_labels[2].'</label></span>';
					}
					if($w_disabled_fields[3]=='no')
					{
					$g++;
					if($w_disabled_fields[5]=='yes' && $w_disabled_fields[6]=='yes')
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><select type="text" id="'.$id.'_stateform_id_temp" name="'.($id+3).'_stateform_id_temp" onchange="change_value(&quot;'.$id.'_stateform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled><option value=""></option><option value="Alabama">Alabama</option><option value="Alaska">Alaska</option><option value="Arizona">Arizona</option><option value="Arkansas">Arkansas</option><option value="California">California</option><option value="Colorado">Colorado</option><option value="Connecticut">Connecticut</option><option value="Delaware">Delaware</option><option value="Florida">Florida</option><option value="Georgia">Georgia</option><option value="Hawaii">Hawaii</option><option value="Idaho">Idaho</option><option value="Illinois">Illinois</option><option value="Indiana">Indiana</option><option value="Iowa">Iowa</option><option value="Kansas">Kansas</option><option value="Kentucky">Kentucky</option><option value="Louisiana">Louisiana</option><option value="Maine">Maine</option><option value="Maryland">Maryland</option><option value="Massachusetts">Massachusetts</option><option value="Michigan">Michigan</option><option value="Minnesota">Minnesota</option><option value="Mississippi">Mississippi</option><option value="Missouri">Missouri</option><option value="Montana">Montana</option><option value="Nebraska">Nebraska</option><option value="Nevada">Nevada</option><option value="New Hampshire">New Hampshire</option><option value="New Jersey">New Jersey</option><option value="New Mexico">New Mexico</option><option value="New York">New York</option><option value="North Carolina">North Carolina</option><option value="North Dakota">North Dakota</option><option value="Ohio">Ohio</option><option value="Oklahoma">Oklahoma</option><option value="Oregon">Oregon</option><option value="Pennsylvania">Pennsylvania</option><option value="Rhode Island">Rhode Island</option><option value="South Carolina">South Carolina</option><option value="South Dakota">South Dakota</option><option value="Tennessee">Tennessee</option><option value="Texas">Texas</option><option value="Utah">Utah</option><option value="Vermont">Vermont</option><option value="Virginia">Virginia</option><option value="Washington">Washington</option><option value="West Virginia">West Virginia</option><option value="Wisconsin">Wisconsin</option><option value="Wyoming">Wyoming</option></select><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_state">'.$w_mini_labels[3].'</label></span>';
					else
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="'.$id.'_stateform_id_temp" name="'.($id+3).'_stateform_id_temp" onchange="change_value(&quot;'.$id.'_stateform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled /><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_state">'.$w_mini_labels[3].'</label></span>';
					}
					if($w_disabled_fields[4]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="'.$id.'_postalform_id_temp" name="'.($id+4).'_postalform_id_temp" onchange="change_value(&quot;'.$id.'_postalform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled/><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_postal">'.$w_mini_labels[4].'</label></span>';
					}
					if($w_disabled_fields[5]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><select type="text" id="'.$id.'_countryform_id_temp" name="'.($id+5).'_countryform_id_temp" onchange="change_state_input(&quot;'.$id.'_countryform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled><option value=""></option><option value="Afghanistan">Afghanistan</option><option value="Albania">Albania</option><option value="Algeria">Algeria</option><option value="Andorra">Andorra</option><option value="Angola">Angola</option><option value="Antigua and Barbuda">Antigua and Barbuda</option><option value="Argentina">Argentina</option><option value="Armenia">Armenia</option><option value="Australia">Australia</option><option value="Austria">Austria</option><option value="Azerbaijan">Azerbaijan</option><option value="Bahamas">Bahamas</option><option value="Bahrain">Bahrain</option><option value="Bangladesh">Bangladesh</option><option value="Barbados">Barbados</option><option value="Belarus">Belarus</option><option value="Belgium">Belgium</option><option value="Belize">Belize</option><option value="Benin">Benin</option><option value="Bhutan">Bhutan</option><option value="Bolivia">Bolivia</option><option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option><option value="Botswana">Botswana</option><option value="Brazil">Brazil</option><option value="Brunei">Brunei</option><option value="Bulgaria">Bulgaria</option><option value="Burkina Faso">Burkina Faso</option><option value="Burundi">Burundi</option><option value="Cambodia">Cambodia</option><option value="Cameroon">Cameroon</option><option value="Canada">Canada</option><option value="Cape Verde">Cape Verde</option><option value="Central African Republic">Central African Republic</option><option value="Chad">Chad</option><option value="Chile">Chile</option><option value="China">China</option><option value="Colombi">Colombi</option><option value="Comoros">Comoros</option><option value="Congo (Brazzaville)">Congo (Brazzaville)</option><option value="Congo">Congo</option><option value="Costa Rica">Costa Rica</option><option value="Cote d\'Ivoire">Cote d\'Ivoire</option><option value="Croatia">Croatia</option><option value="Cuba">Cuba</option><option value="Cyprus">Cyprus</option><option value="Czech Republic">Czech Republic</option><option value="Denmark">Denmark</option><option value="Djibouti">Djibouti</option><option value="Dominica">Dominica</option><option value="Dominican Republic">Dominican Republic</option><option value="East Timor (Timor Timur)">East Timor (Timor Timur)</option><option value="Ecuador">Ecuador</option><option value="Egypt">Egypt</option><option value="El Salvador">El Salvador</option><option value="Equatorial Guinea">Equatorial Guinea</option><option value="Eritrea">Eritrea</option><option value="Estonia">Estonia</option><option value="Ethiopia">Ethiopia</option><option value="Fiji">Fiji</option><option value="Finland">Finland</option><option value="France">France</option><option value="Gabon">Gabon</option><option value="Gambia, The">Gambia, The</option><option value="Georgia">Georgia</option><option value="Germany">Germany</option><option value="Ghana">Ghana</option><option value="Greece">Greece</option><option value="Grenada">Grenada</option><option value="Guatemala">Guatemala</option><option value="Guinea">Guinea</option><option value="Guinea-Bissau">Guinea-Bissau</option><option value="Guyana">Guyana</option><option value="Haiti">Haiti</option><option value="Honduras">Honduras</option><option value="Hungary">Hungary</option><option value="Iceland">Iceland</option><option value="India">India</option><option value="Indonesia">Indonesia</option><option value="Iran">Iran</option><option value="Iraq">Iraq</option><option value="Ireland">Ireland</option><option value="Israel">Israel</option><option value="Italy">Italy</option><option value="Jamaica">Jamaica</option><option value="Japan">Japan</option><option value="Jordan">Jordan</option><option value="Kazakhstan">Kazakhstan</option><option value="Kenya">Kenya</option><option value="Kiribati">Kiribati</option><option value="Korea, North">Korea, North</option><option value="Korea, South">Korea, South</option><option value="Kuwait">Kuwait</option><option value="Kyrgyzstan">Kyrgyzstan</option><option value="Laos">Laos</option><option value="Latvia">Latvia</option><option value="Lebanon">Lebanon</option><option value="Lesotho">Lesotho</option><option value="Liberia">Liberia</option><option value="Libya">Libya</option><option value="Liechtenstein">Liechtenstein</option><option value="Lithuania">Lithuania</option><option value="Luxembourg">Luxembourg</option><option value="Macedonia">Macedonia</option><option value="Madagascar">Madagascar</option><option value="Malawi">Malawi</option><option value="Malaysia">Malaysia</option><option value="Maldives">Maldives</option><option value="Mali">Mali</option><option value="Malta">Malta</option><option value="Marshall Islands">Marshall Islands</option><option value="Mauritania">Mauritania</option><option value="Mauritius">Mauritius</option><option value="Mexico">Mexico</option><option value="Micronesia">Micronesia</option><option value="Moldova">Moldova</option><option value="Monaco">Monaco</option><option value="Mongolia">Mongolia</option><option value="Morocco">Morocco</option><option value="Mozambique">Mozambique</option><option value="Myanmar">Myanmar</option><option value="Namibia">Namibia</option><option value="Nauru">Nauru</option><option value="Nepa">Nepa</option><option value="Netherlands">Netherlands</option><option value="New Zealand">New Zealand</option><option value="Nicaragua">Nicaragua</option><option value="Niger">Niger</option><option value="Nigeria">Nigeria</option><option value="Norway">Norway</option><option value="Oman">Oman</option><option value="Pakistan">Pakistan</option><option value="Palau">Palau</option><option value="Panama">Panama</option><option value="Papua New Guinea">Papua New Guinea</option><option value="Paraguay">Paraguay</option><option value="Peru">Peru</option><option value="Philippines">Philippines</option><option value="Poland">Poland</option><option value="Portugal">Portugal</option><option value="Qatar">Qatar</option><option value="Romania">Romania</option><option value="Russia">Russia</option><option value="Rwanda">Rwanda</option><option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option><option value="Saint Lucia">Saint Lucia</option><option value="Saint Vincent">Saint Vincent</option><option value="Samoa">Samoa</option><option value="San Marino">San Marino</option><option value="Sao Tome and Principe">Sao Tome and Principe</option><option value="Saudi Arabia">Saudi Arabia</option><option value="Senegal">Senegal</option><option value="Serbia and Montenegro">Serbia and Montenegro</option><option value="Seychelles">Seychelles</option><option value="Sierra Leone">Sierra Leone</option><option value="Singapore">Singapore</option><option value="Slovakia">Slovakia</option><option value="Slovenia">Slovenia</option><option value="Solomon Islands">Solomon Islands</option><option value="Somalia">Somalia</option><option value="South Africa">South Africa</option><option value="Spain">Spain</option><option value="Sri Lanka">Sri Lanka</option><option value="Sudan">Sudan</option><option value="Suriname">Suriname</option><option value="Swaziland">Swaziland</option><option value="Sweden">Sweden</option><option value="Switzerland">Switzerland</option><option value="Syria">Syria</option><option value="Taiwan">Taiwan</option><option value="Tajikistan">Tajikistan</option><option value="Tanzania">Tanzania</option><option value="Thailand">Thailand</option><option value="Togo">Togo</option><option value="Tonga">Tonga</option><option value="Trinidad and Tobago">Trinidad and Tobago</option><option value="Tunisia">Tunisia</option><option value="Turkey">Turkey</option><option value="Turkmenistan">Turkmenistan</option><option value="Tuvalu">Tuvalu</option><option value="Uganda">Uganda</option><option value="Ukraine">Ukraine</option><option value="United Arab Emirates">United Arab Emirates</option><option value="United Kingdom">United Kingdom</option><option value="United States">United States</option><option value="Uruguay">Uruguay</option><option value="Uzbekistan">Uzbekistan</option><option value="Vanuatu">Vanuatu</option><option value="Vatican City">Vatican City</option><option value="Venezuela">Venezuela</option><option value="Vietnam">Vietnam</option><option value="Yemen">Yemen</option><option value="Zambia">Zambia</option><option value="Zimbabwe">Zimbabwe</option></select><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_country">'.$w_mini_labels[5].'</span>';
					}				
				
					$rep ='<div id="wdform_field'.$id.'" type="type_address" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px; vertical-align:top;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_address" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" name="'.$id.'_disable_fieldsform_id_temp" id="'.$id.'_disable_fieldsform_id_temp" street1="'.$w_disabled_fields[0].'" street2="'.$w_disabled_fields[1].'" city="'.$w_disabled_fields[2].'" state="'.$w_disabled_fields[3].'" postal="'.$w_disabled_fields[4].'" country="'.$w_disabled_fields[5].'" us_states="'.$w_disabled_fields[6].'"><div id="'.$id.'_div_address" style="width: '.$param['w_size'].'px;">'.$address_fields.$hidden_inputs.'</div></div></div>';
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
					$rep ='<div id="wdform_field'.$id.'" type="type_submitter_mail" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_submitter_mail" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><input type="text" class="'.$input_active.'" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'" onfocus="delete_value(&quot;'.$id.'_elementform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_elementform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled/></div></div>';
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}
					
					if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
					
					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					
					if(isset($param['w_choices_value']))
					{
						$param['w_choices_value'] = explode('***',$param['w_choices_value']);
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
					}
					
					foreach($param['w_choices_checked'] as $key => $choices_checked )
					{
						if($choices_checked=='true')
							$param['w_choices_checked'][$key]='checked="checked"';
						else
							$param['w_choices_checked'][$key]='';
					}
			
					$rep='<div id="wdform_field'.$id.'" type="type_checkbox" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_checkbox" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_rowcol'].'" name="'.$id.'_rowcol_numform_id_temp" id="'.$id.'_rowcol_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><input type="hidden" value="'.$param['w_value_disabled'].'" name="'.$id.'_value_disabledform_id_temp" id="'.$id.'_value_disabledform_id_temp"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;" '.($param['w_flow']=='hor' ? 'for_hor="'.$id.'_hor"' : '').'>';
				
				if($param['w_flow']=='hor')
				{
					$j = 0;
					for($i=0; $i<(int)$param['w_rowcol']; $i++)
					{
						$rep.='<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;">';
						
							for($l=0; $l<=(int)(count($param['w_choices'])/$param['w_rowcol']); $l++)
							{
								if($j >= count($param['w_choices'])%$param['w_rowcol'] && $l==(int)(count($param['w_choices'])/$param['w_rowcol']))
								continue;
								
								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$l+$i)
									$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$l+$i).'" idi="'.((int)$param['w_rowcol']*$l+$i).'" style="display: table-cell;"><input type="checkbox" value="" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" other="1" onclick="if(set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$l+$i).'&quot;,&quot;form_id_temp&quot;)) show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$l+$i].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled /><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$l+$i).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'">'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'</label></div>';
								else
								{	
									$where = '';
									$order_by = '';
									$db_info = '';
									if(isset($param['w_choices_value']))
										$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$l+$i];
									else
										$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$l+$i];
																	
									if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$l+$i])
									{
										$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$l+$i]);
										$where = "where='".$w_choices_params[0]."'";
										$w_choices_params = explode('[db_info]',$w_choices_params[1]);
										$order_by = "order_by='".$w_choices_params[0]."'";
										$db_info = "db_info='".$w_choices_params[1]."'";
									}
									
									$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$l+$i).'" idi="'.((int)$param['w_rowcol']*$l+$i).'" style="display: table-cell;"><input type="checkbox" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$l+$i).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$l+$i].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").'  disabled /><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$l+$i).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'</label></div>';			
								}	
							}
						
						$j++;
						$rep.='</div>';	
						
					}	
				}
				else
				{
					for($i=0; $i<(int)(count($param['w_choices'])/$param['w_rowcol']); $i++)
					{
						$rep.='<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;">';
						
						if(count($param['w_choices']) > (int)$param['w_rowcol'])
							for($l=0; $l<$param['w_rowcol']; $l++)
							{
								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
									$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="checkbox" value="" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" other="1" onclick="if(set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)) show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled /><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'">'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
								else
								{
									$where = '' ;
									$order_by = '' ;
									$db_info = '' ;
									if(isset($param['w_choices_value']))
										$choise_value =	$param['w_choices_value'][(int)$param['w_rowcol']*$i+$l]; 
									else
										$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$i+$l];

									if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$i+$l])
									{
										$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
										$where = "where='".$w_choices_params[0]."'";
										$w_choices_params = explode('[db_info]',$w_choices_params[1]);
										$order_by = "order_by='".$w_choices_params[0]."'";
										$db_info = "db_info='".$w_choices_params[1]."'";
									}
									
									$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="checkbox" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").'  disabled /><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
								}	
							}
						else
							for($l=0; $l<count($param['w_choices']); $l++)
							{
								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
									$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="checkbox" value="" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" other="1" onclick="if(set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)) show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'">'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
								else
								{
									$where = '' ;
									$order_by = '' ;
									$db_info = '' ;
									if(isset($param['w_choices_value']))
										$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$i+$l];
									else
										$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$i+$l];
						
									if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$i+$l])
									{
										$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
										$where = "where='".$w_choices_params[0]."'";
										$w_choices_params = explode('[db_info]',$w_choices_params[1]);
										$order_by = "order_by='".$w_choices_params[0]."'";
										$db_info = "db_info='".$w_choices_params[1]."'";
									}
									
									$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="checkbox" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
								}	
							}
						
						$rep.='</div>';	
					}
					
					if(count($param['w_choices'])%$param['w_rowcol']!=0)
					{
						$rep.='<div id="'.$id.'_element_tr'.((int)(count($param['w_choices'])/(int)$param['w_rowcol'])).'" style="display: table-row;">';
						
						for($k=0; $k<count($param['w_choices'])%$param['w_rowcol']; $k++)
						{
							$l = count($param['w_choices']) - count($param['w_choices'])%$param['w_rowcol'] + $k;
							if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$l)
								$rep.='<div valign="top" id="'.$id.'_td_little'.$l.'" idi="'.$l.'" style="display: table-cell;"><input type="checkbox" value="" id="'.$id.'_elementform_id_temp'.$l.'" name="'.$id.'_elementform_id_temp'.$l.'" other="1" onclick="if(set_checked(&quot;'.$id.'&quot;,&quot;'.$l.'&quot;,&quot;form_id_temp&quot;)) show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled /><label id="'.$id.'_label_element'.$l.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$l.'">'.$param['w_choices'][$l].'</label></div>';
							else
							{
								$where = '' ;
								$order_by = '' ;
								$db_info = '' ;
								if(isset($param['w_choices_value']))
									$choise_value = $param['w_choices_value'][$l];
								else
									$choise_value = $param['w_choices'][$l];
						
								if(isset($param['w_choices_params']) && $param['w_choices_params'][$l])
								{
									$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$l]);
									$where = "where='".$w_choices_params[0]."'";
									$w_choices_params = explode('[db_info]',$w_choices_params[1]);
									$order_by = "order_by='".$w_choices_params[0]."'";
									$db_info = "db_info='".$w_choices_params[1]."'";
								}
								
								$rep.='<div valign="top" id="'.$id.'_td_little'.$l.'" idi="'.$l.'" style="display: table-cell;"><input type="checkbox" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.$l.'" name="'.$id.'_elementform_id_temp'.$l.'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.$l.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled /><label id="'.$id.'_label_element'.$l.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$l.'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][$l].'</label></div>';
							}							
						}
						
						$rep.='</div>';	
					}	
				}
				$rep.='</div></div></div></div>';
					break;
				}
				case 'type_radio':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
					$temp=$params;
	
					if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_value_disabled', 'w_choices_value', 'w_choices_params','w_class');	
					
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
					
					if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
					
					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
					
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					
					if(isset($param['w_choices_value']))
					{
						$param['w_choices_value'] = explode('***',$param['w_choices_value']);
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
					}
					
					foreach($param['w_choices_checked'] as $key => $choices_checked )
					{
						if($choices_checked=='true')
							$param['w_choices_checked'][$key]='checked="checked"';
						else
							$param['w_choices_checked'][$key]='';
					}	
					
					$rep='<div id="wdform_field'.$id.'" type="type_radio" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_radio" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_rowcol'].'" name="'.$id.'_rowcol_numform_id_temp" id="'.$id.'_rowcol_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><input type="hidden" value="'.$param['w_value_disabled'].'" name="'.$id.'_value_disabledform_id_temp" id="'.$id.'_value_disabledform_id_temp"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;" '.($param['w_flow']=='hor' ? 'for_hor="'.$id.'_hor"' : '').'>';

					if($param['w_flow']=='hor')
					{

						$j = 0;
						for($i=0; $i<(int)$param['w_rowcol']; $i++)
						{
							$rep.='<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;">';
							
								for($l=0; $l<=(int)(count($param['w_choices'])/$param['w_rowcol']); $l++)
								{
									if($j >= count($param['w_choices'])%$param['w_rowcol'] && $l==(int)(count($param['w_choices'])/$param['w_rowcol']))
									continue;
									
									if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
											$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$l+$i).'" idi="'.((int)$param['w_rowcol']*$l+$i).'" style="display: table-cell;"><input type="radio" value="'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" name="'.$id.'_elementform_id_temp" other="1" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$l+$i).'&quot;,&quot;form_id_temp&quot;); show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$l+$i].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$l+$i).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'">'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'</label></div>';
										else
										{		
											$where = '' ;
											$order_by = '' ;
											$db_info = '' ;
											if(isset($param['w_choices_value']))
												$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$l+$i];	
											else
												$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$l+$i];
												
											if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$l+$i])
											{
												$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
												$where = "where='".$w_choices_params[0]."'";
												$w_choices_params = explode('[db_info]',$w_choices_params[1]);
												$order_by = "order_by='".$w_choices_params[0]."'";
												$db_info = "db_info='".$w_choices_params[1]."'";
											}	
										
											$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$l+$i).'" idi="'.((int)$param['w_rowcol']*$l+$i).'" style="display: table-cell;"><input type="radio" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" name="'.$id.'_elementform_id_temp" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$l+$i).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$l+$i].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$l+$i).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'</label></div>';
										}	
								}
								
							$j++;
							$rep.='</div>';	
							
						}
		
					}
					else
					{
						for($i=0; $i<(int)(count($param['w_choices'])/$param['w_rowcol']); $i++)
						{
							$rep.='<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;">';
							
							if(count($param['w_choices']) > (int)$param['w_rowcol'])
								for($l=0; $l<$param['w_rowcol']; $l++)
								{
									if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="radio" value="'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp" other="1" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;); show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'">'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									else
									{
										$where = '' ;
										$order_by = '' ;
										$db_info = '' ;
										if(isset($param['w_choices_value']))
											$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$i+$l];	
										else
											$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$i+$l];

										if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$i+$l])
										{
											$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
											$where = "where='".$w_choices_params[0]."'";
											$w_choices_params = explode('[db_info]',$w_choices_params[1]);
											$order_by = "order_by='".$w_choices_params[0]."'";
											$db_info = "db_info='".$w_choices_params[1]."'";
										}		

										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="radio" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									}	
								}
							else
								for($l=0; $l<count($param['w_choices']); $l++)
								{
									if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="radio" value="'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp" other="1" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;); show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled /><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'">'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									else
									{
										$where = '' ;
										$order_by = '' ;
										$db_info = '' ;
										if(isset($param['w_choices_value']))
											$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$i+$l];	
										else
											$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$i+$l];
									
										if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$i+$l])
										{
											$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
											$where = "where='".$w_choices_params[0]."'";
											$w_choices_params = explode('[db_info]',$w_choices_params[1]);
											$order_by = "order_by='".$w_choices_params[0]."'";
											$db_info = "db_info='".$w_choices_params[1]."'";
										}
										
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="radio" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									}	
								}
							
							$rep.='</div>';	
						}
						
						if(count($param['w_choices'])%$param['w_rowcol']!=0)
						{
							$rep.='<div id="'.$id.'_element_tr'.((int)(count($param['w_choices'])/(int)$param['w_rowcol'])).'" style="display: table-row;">';
							
							for($k=0; $k<count($param['w_choices'])%$param['w_rowcol']; $k++)
							{
								$l = count($param['w_choices']) - count($param['w_choices'])%$param['w_rowcol'] + $k;
								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$l)
									$rep.='<div valign="top" id="'.$id.'_td_little'.$l.'" idi="'.$l.'" style="display: table-cell;"><input type="radio" value="'.$param['w_choices'][$l].'" id="'.$id.'_elementform_id_temp'.$l.'" name="'.$id.'_elementform_id_temp" other="1" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$l.'&quot;,&quot;form_id_temp&quot;); show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$l.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$l.'">'.$param['w_choices'][$l].'</label></div>';
								else
								{
									$where = '' ;
									$order_by = '' ;
									$db_info = '' ;
									if(isset($param['w_choices_value']))
										$choise_value = $param['w_choices_value'][$l];
									else
										$choise_value = $param['w_choices'][$l];
									
									if(isset($param['w_choices_params']) && $param['w_choices_params'][$l])
										{
											$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$l]);
											$where = "where='".$w_choices_params[0]."'";
											$w_choices_params = explode('[db_info]',$w_choices_params[1]);
											$order_by = "order_by='".$w_choices_params[0]."'";
											$db_info = "db_info='".$w_choices_params[1]."'";
										}
									
									$rep.='<div valign="top" id="'.$id.'_td_little'.$l.'" idi="'.$l.'" style="display: table-cell;"><input type="radio" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.$l.'" name="'.$id.'_elementform_id_temp" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$l.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$l.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$l.'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][$l].'</label></div>';
								}	
							}
							
							$rep.='</div>';	
						}
						
					}
							
					
		
				$rep.='</div></div></div></div>';
				
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}
		
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					$param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);
					
					if(isset($param['w_choices_value']))
					{
						$param['w_choices_value'] = explode('***',$param['w_choices_value']);
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
					}	
							
					if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
					
					foreach($param['w_choices_checked'] as $key => $choices_checked )
					{
						if($choices_checked=='true')
							$param['w_choices_checked'][$key]='selected="selected"';
						else
							$param['w_choices_checked'][$key]='';
					}
					
					$rep='<div id="wdform_field'.$id.'" type="type_own_select" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_own_select" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_value_disabled'].'" name="'.$id.'_value_disabledform_id_temp" id="'.$id.'_value_disabledform_id_temp"><select id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" onchange="set_select(this)" style="width: '.$param['w_size'].'px;"  '.$param['attributes'].' disabled>';

					foreach($param['w_choices'] as $key => $choice)
					{
						$where = '';
						$order_by = '';
						$db_info = '';
						$choice_value = $param['w_choices_disabled'][$key]=='true' ? '' : (isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice);
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
						
						$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");
						
					  $rep.='<option id="'.$id.'_option'.$key.'" value="'.$choice_value.'" onselect="set_select(&quot;'.$id.'_option'.$key.'&quot;)" '.$param['w_choices_checked'][$key].' '.$where.' '.$order_by.' '.$db_info.' >'.$choice.'</option>';
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

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$param['w_countries']	= explode('***',$param['w_countries']);
				
					$rep='<div id="wdform_field'.$id.'" type="type_country" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_country" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><select id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" style="width: '.$param['w_size'].'px;"  '.$param['attributes'].' disabled>';
					foreach($param['w_countries'] as $key => $choice)
					{
					  $choice_value=$choice;
					  $rep.='<option value="'.$choice_value.'">'.$choice.'</option>';
					}
					$rep.='</select></div></div>';
					break;
				}
				
				case 'type_time':
				{
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					
				
					if($param['w_sec']=='1')
					{
						$w_sec = '<div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div id="'.$id.'_td_time_input3" style="width: 32px; display: table-cell;"><input type="text" value="'.$param['w_ss'].'" class="time_box" id="'.$id.'_ssform_id_temp" name="'.$id.'_ssform_id_temp" onkeypress="return check_second(event, &quot;'.$id.'_ssform_id_temp&quot;)" onkeyup="change_second(&quot;'.$id.'_ssform_id_temp&quot;)" onblur="add_0(&quot;'.$id.'_ssform_id_temp&quot;)" '.$param['attributes'].' disabled /></div>';
						$w_sec_label='<div style="display: table-cell;"></div><div id="'.$id.'_td_time_label3" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_ss">'.$w_mini_labels[2].'</label></div>';
					}
					else
					{
						$w_sec = '';
						$w_sec_label='';
					}
					
					if($param['w_time_type']=='12')
					{
						if($param['w_am_pm']=='am')
						{
							$am_ = "selected=\"selected\"";
							$pm_ = "";
						}	
						else
						{
							$am_ = "";
							$pm_ = "selected=\"selected\"";
							
						}	
					
					$w_time_type = '<div id="'.$id.'_am_pm_select" class="td_am_pm_select" style="display: table-cell;"><select class="am_pm_select" name="'.$id.'_am_pmform_id_temp" id="'.$id.'_am_pmform_id_temp" onchange="set_sel_am_pm(this)" '.$param['attributes'].' disabled><option value="am" '.$am_.'>AM</option><option value="pm" '.$pm_.'>PM</option></select></div>';
					
					$w_time_type_label = '<div id="'.$id.'_am_pm_label" class="td_am_pm_select" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_am_pm">'.$w_mini_labels[3].'</label></div>';
					
					}
					else
					{
					$w_time_type='';
					$w_time_type_label = '';
					}

					
					$rep ='<div id="wdform_field'.$id.'" type="type_time" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_time" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><div id="'.$id.'_table_time" style="display: table;"><div id="'.$id.'_tr_time1" style="display: table-row;"><div id="'.$id.'_td_time_input1" style="width: 32px; display: table-cell;"><input type="text" value="'.$param['w_hh'].'" class="time_box" id="'.$id.'_hhform_id_temp" name="'.$id.'_hhform_id_temp" onkeypress="return check_hour(event, &quot;'.$id.'_hhform_id_temp&quot;, &quot;23&quot;)" onkeyup="change_hour(event, &quot;'.$id.'_hhform_id_temp&quot;,&quot;23&quot;)" onblur="add_0(&quot;'.$id.'_hhform_id_temp&quot;)" '.$param['attributes'].' disabled/></div><div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div id="'.$id.'_td_time_input2" style="width: 32px; display: table-cell;"><input type="text" value="'.$param['w_mm'].'" class="time_box" id="'.$id.'_mmform_id_temp" name="'.$id.'_mmform_id_temp" onkeypress="return check_minute(event, &quot;'.$id.'_mmform_id_temp&quot;)" onkeyup="change_minute(event, &quot;'.$id.'_mmform_id_temp&quot;)" onblur="add_0(&quot;'.$id.'_mmform_id_temp&quot;)" '.$param['attributes'].' disabled/></div>'.$w_sec.$w_time_type.'</div><div id="'.$id.'_tr_time2" style="display: table-row;"><div id="'.$id.'_td_time_label1" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_hh">'.$w_mini_labels[0].'</label></div><div style="display: table-cell;"></div><div id="'.$id.'_td_time_label2" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_mm">'.$w_mini_labels[1].'</label></div>'.$w_sec_label.$w_time_type_label.'</div></div></div></div>';
					
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
	

					$rep ='<div id="wdform_field'.$id.'" type="type_date" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_date" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="text" value="'.$param['w_date'].'" class="wdform-date" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp"  onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" '.$param['attributes'].' style="vertical-align: top; width: 80px;" disabled/><button id="'.$id.'_buttonform_id_temp" class="btn" type="reset" value="'.$param['w_but_val'].'" format="'.$param['w_format'].'" title="'.$param['w_but_val'].'"  '.$param['attributes'].' onclick="return showCalendar(&quot;'.$id.'_elementform_id_temp&quot; , &quot;'.$param['w_format'].'&quot;)"><i class="icon-calendar"></i></button></div></div>';
					
					break;
				}
				case 'type_date_fields':
				{
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
				
	
					if($param['w_day_type']=="SELECT")
					{

					
						$w_day_type = '<select id="'.$id.'_dayform_id_temp" name="'.$id.'_dayform_id_temp" onchange="set_select(this)" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].' disabled><option value=""></option>';
						for($k=0; $k<=31; $k++)
						{
							if($k<10)
							{
								if($param['w_day']=='0'.$k)
								$selected = "selected=\"selected\"";
								else
								$selected = "";
								
								$w_day_type .= '<option value="0'.$k.'" '.$selected.'>0'.$k.'</option>';
							}
							else
							{
							if($param['w_day']==''.$k)
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
						$w_day_type = '<input type="text" value="'.$param['w_day'].'" id="'.$id.'_dayform_id_temp" name="'.$id.'_dayform_id_temp" onchange="change_value(&quot;'.$id.'_dayform_id_temp&quot;)" onkeypress="return check_day(event, &quot;'.$id.'_dayform_id_temp&quot;)" onblur="if (this.value==&quot;0&quot;) this.value=&quot;&quot;; else add_0(&quot;'.$id.'_dayform_id_temp&quot;)" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].' disabled/>';
					}

					if($param['w_month_type']=="SELECT")
					{
					
						$w_month_type = '<select id="'.$id.'_monthform_id_temp" name="'.$id.'_monthform_id_temp" onchange="set_select(this)" style="width: '.$param['w_month_size'].'px;" '.$param['attributes'].' disabled><option value=""></option><option value="01" '.($param['w_month']=="01" ? "selected=\"selected\"": "").'  ><!--repstart-->January<!--repend--></option><option value="02" '.($param['w_month']=="02" ? "selected=\"selected\"": "").'><!--repstart-->February<!--repend--></option><option value="03" '.($param['w_month']=="03"? "selected=\"selected\"": "").'><!--repstart-->March<!--repend--></option><option value="04" '.($param['w_month']=="04" ? "selected=\"selected\"": "").' ><!--repstart-->April<!--repend--></option><option value="05" '.($param['w_month']=="05" ? "selected=\"selected\"": "").' ><!--repstart-->May<!--repend--></option><option value="06" '.($param['w_month']=="06" ? "selected=\"selected\"": "").' ><!--repstart-->June<!--repend--></option><option value="07" '.($param['w_month']=="07" ? "selected=\"selected\"": "").' ><!--repstart-->July<!--repend--></option><option value="08" '.($param['w_month']=="08" ? "selected=\"selected\"": "").' ><!--repstart-->August<!--repend--></option><option value="09" '.($param['w_month']=="09" ? "selected=\"selected\"": "").' ><!--repstart-->September<!--repend--></option><option value="10" '.($param['w_month']=="10" ? "selected=\"selected\"": "").' ><!--repstart-->October<!--repend--></option><option value="11" '.($param['w_month']=="11" ? "selected=\"selected\"": "").'><!--repstart-->November<!--repend--></option><option value="12" '.($param['w_month']=="12" ? "selected=\"selected\"": "").' ><!--repstart-->December<!--repend--></option></select>';
					
					}
					else
					{
					$w_month_type = '<input type="text" value="'.$param['w_month'].'" id="'.$id.'_monthform_id_temp" name="'.$id.'_monthform_id_temp" onkeypress="return check_month(event, &quot;'.$id.'_monthform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_monthform_id_temp&quot;)" onblur="if (this.value==&quot;0&quot;) this.value=&quot;&quot;; else add_0(&quot;'.$id.'_monthform_id_temp&quot;)" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].' disabled/>';
					
					}

					if($param['w_year_type']=="SELECT")
					{
						$w_year_type = '<select id="'.$id.'_yearform_id_temp" name="'.$id.'_yearform_id_temp" onchange="set_select(this)" from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_year_size'].'px;" '.$param['attributes'].' disabled><option value=""></option>';
						for($k=$param['w_to']; $k>=$param['w_from']; $k--)
						{
							if($param['w_year']==$k)
							$selected = "selected=\"selected\"";
							else
							$selected = "";
							
							$w_year_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
						}
						$w_year_type .= '</select>';
					
					}
					else
					{
						$w_year_type = '<input type="text" value="'.$param['w_year'].'" id="'.$id.'_yearform_id_temp" name="'.$id.'_yearform_id_temp" onchange="change_year(&quot;'.$id.'_yearform_id_temp&quot;)" onkeypress="return check_year1(event, &quot;'.$id.'_yearform_id_temp&quot;)" onblur="check_year2(&quot;'.$id.'_yearform_id_temp&quot;)" from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].' disabled/>';
					}

					
					$rep ='<div id="wdform_field'.$id.'" type="type_date_fields" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_date_fields" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><div id="'.$id.'_table_date" style="display: table;"><div id="'.$id.'_tr_date1" style="display: table-row;"><div id="'.$id.'_td_date_input1" style="display: table-cell;">
					'.$w_day_type.'
					
					</div><div id="'.$id.'_td_date_separator1" style="display: table-cell;"><span id="'.$id.'_separator1" class="wdform_separator">'.$param['w_divider'].'</span></div><div id="'.$id.'_td_date_input2" style="display: table-cell;">'.$w_month_type.'</div><div id="'.$id.'_td_date_separator2" style="display: table-cell;"><span id="'.$id.'_separator2" class="wdform_separator">'.$param['w_divider'].'</span></div><div id="'.$id.'_td_date_input3" style="display: table-cell;">'.$w_year_type.'</div></div><div id="'.$id.'_tr_date2" style="display: table-row;"><div id="'.$id.'_td_date_label1" style="display: table-cell;"><label class="mini_label" id="'.$id.'_day_label">'.$param['w_day_label'].'</label></div><div style="display: table-cell;"></div><div id="'.$id.'_td_date_label2" style="display: table-cell;"><label class="mini_label" id="'.$id.'_month_label">'.$param['w_month_label'].'</label></div><div style="display: table-cell;"></div><div id="'.$id.'_td_date_label3" style="display: table-cell;"><label class="mini_label" id="'.$id.'_year_label">'.$param['w_year_label'].'</label></div></div></div></div></div>';
					
					break;
				}
				case 'type_file_upload':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_destination','w_extension','w_max_size','w_required','w_multiple','w_class');
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

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$multiple = ($param['w_multiple']=="yes" ? "multiple='multiple'" : "");	

					$rep ='<div id="wdform_field'.$id.'" type="type_file_upload" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_file_upload" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="***max_sizeskizb'.$id.'***'.$param['w_max_size'].'***max_sizeverj'.$id.'***" id="'.$id.'_max_size" name="'.$id.'_max_size"><input type="hidden" value="***destinationskizb'.$id.'***'.$param['w_destination'].'***destinationverj'.$id.'***" id="'.$id.'_destination" name="'.$id.'_destination"><input type="hidden" value="***extensionskizb'.$id.'***'.$param['w_extension'].'***extensionverj'.$id.'***" id="'.$id.'_extension" name="'.$id.'_extension"><input type="file" class="file_upload" id="'.$id.'_elementform_id_temp" name="'.$id.'_fileform_id_temp" '.$multiple.' '.$param['attributes'].' disabled/></div></div>';
					
					break;
				}
				case 'type_captcha':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_digit','w_class');
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

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					
					$rep ='<div id="wdform_field'.$id.'" type="type_captcha" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display:'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_captcha" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div style="display: table;"><div style="display: table-row;"><div valign="middle" style="display: table-cell; vertical-align:top;"><img type="captcha" digit="'.$param['w_digit'].'" src="../index.php?option=com_formmaker&amp;view=wdcaptcha&amp;format=raw&amp;tmpl=component&amp;digit='.$param['w_digit'].'&amp;i=form_id_temp" id="_wd_captchaform_id_temp" class="captcha_img" onclick="captcha_refresh(&quot;_wd_captcha&quot;,&quot;form_id_temp&quot;)" '.$param['attributes'].'></div><div valign="middle" style="display: table-cell;"><div class="captcha_refresh" id="_element_refreshform_id_temp" onclick="captcha_refresh(&quot;_wd_captcha&quot;,&quot;form_id_temp&quot;)" '.$param['attributes'].'></div></div></div><div style="display: table-row;"><div style="display: table-cell;"><input type="text" class="captcha_input" id="_wd_captcha_inputform_id_temp" name="captcha_input" style="width: '.($param['w_digit']*10+15).'px;" '.$param['attributes'].' disabled/></div></div></div></div></div>';
					
					break;
				}
				case 'type_recaptcha':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_public','w_private','w_theme','w_class');
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

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					
					$rep ='<div id="wdform_field'.$id.'" type="type_recaptcha" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_recaptcha" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div id="wd_recaptchaform_id_temp" public_key="'.$param['w_public'].'" private_key="'.$param['w_private'].'" theme="'.$param['w_theme'].'" '.$param['attributes'].'><span style="color: red; font-style: italic;">Recaptcha doesn\'t display in back end</span></div></div></div>';
					
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
							$param['attributes'] = $param['attributes'].' add_'.$attr;
					}
					
					$rep ='<div id="wdform_field'.$id.'" type="type_hidden" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" style="display: table-cell;"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">'.$param['w_name'].'</span><span style="color: red; font-size: 14px;">Hidden field</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" style="display: table-cell; padding-left:7px;"><input type="hidden" value="'.$param['w_value'].'" id="'.$id.'_elementform_id_temp" name="'.$param['w_name'].'" '.$param['attributes'].'><input type="hidden" value="type_hidden" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div><span align="left">Name: </span><span align="left" id="'.$id.'_hidden_nameform_id_temp">'.$param['w_name'].'</span></div><div><span align="left">Value: </span><span align="left" id="'.$id.'_hidden_valueform_id_temp">'.$param['w_value'].'</span></div></div></div>';
					
					break;
				}
				case 'type_mark_map':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_center_x','w_center_y','w_long','w_lat','w_zoom','w_width','w_height','w_info','w_class');
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
				
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
				
					$rep ='<div id="wdform_field'.$id.'" type="type_mark_map" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px; vertical-align:top;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_mark_map" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div id="'.$id.'_elementform_id_temp" long0="'.$param['w_long'].'" lat0="'.$param['w_lat'].'" zoom="'.$param['w_zoom'].'" info0="'.$param['w_info'].'" center_x="'.$param['w_center_x'].'" center_y="'.$param['w_center_y'].'" style="width: '.$param['w_width'].'px; height: '.$param['w_height'].'px;" '.$param['attributes'].'></div></div></div>	';
					
					break;
				}
				
				case 'type_map':
				{
					$params_names=array('w_center_x','w_center_y','w_long','w_lat','w_zoom','w_width','w_height','w_info','w_class');
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
					
					$marker='';
					
					$param['w_long']	= explode('***',$param['w_long']);
					$param['w_lat']	= explode('***',$param['w_lat']);
					$param['w_info']	= explode('***',$param['w_info']);
					foreach($param['w_long'] as $key => $w_long )
					{
						$marker.='long'.$key.'="'.$w_long.'" lat'.$key.'="'.$param['w_lat'][$key].'" info'.$key.'="'.$param['w_info'][$key].'"';
					}
				
					$rep ='<div id="wdform_field'.$id.'" type="type_map" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><input type="hidden" value="type_map" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div id="'.$id.'_elementform_id_temp" zoom="'.$param['w_zoom'].'" center_x="'.$param['w_center_x'].'" center_y="'.$param['w_center_y'].'" style="width: '.$param['w_width'].'px; height: '.$param['w_height'].'px;" '.$marker.' '.$param['attributes'].'></div></div></div>';
					
					break;
				}
				case 'type_paypal_price':
				{
										
					$params_names=array('w_field_label_size','w_field_label_pos','w_first_val','w_title', 'w_mini_labels','w_size','w_required','w_hide_cents','w_class','w_range_min','w_range_max');
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

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$hide_cents = ($param['w_hide_cents']=="yes" ? "none;" : "table-cell;");	
				

					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					
					$rep ='<div id="wdform_field'.$id.'" type="type_paypal_price" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required"style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_paypal_price" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_range_min'].'" name="'.$id.'_range_minform_id_temp" id="'.$id.'_range_minform_id_temp"><input type="hidden" value="'.$param['w_range_max'].'" name="'.$id.'_range_maxform_id_temp" id="'.$id.'_range_maxform_id_temp"><div id="'.$id.'_table_price" style="display: table;"><div id="'.$id.'_tr_price1" style="display: table-row;"><div id="'.$id.'_td_name_currency" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;"><!--repstart-->&nbsp;$&nbsp;<!--repend--></span></div><div id="'.$id.'_td_name_dollars" style="display: table-cell;"><input type="text" class="'.$input_active.'" id="'.$id.'_element_dollarsform_id_temp" name="'.$id.'_element_dollarsform_id_temp" value="'.$w_first_val[0].'" title="'.$w_title[0].'"onfocus="delete_value(&quot;'.$id.'_element_dollarsform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_dollarsform_id_temp&quot;)"onchange="change_value(&quot;'.$id.'_element_dollarsform_id_temp&quot;)" onkeypress="return check_isnum(event)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled/></div><div id="'.$id.'_td_name_divider" style="display: '.$hide_cents.';"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;.&nbsp;</span></div><div id="'.$id.'_td_name_cents" style="display: '.$hide_cents.'"><input type="text" class="'.$input_active.'" id="'.$id.'_element_centsform_id_temp" name="'.$id.'_element_centsform_id_temp" value="'.$w_first_val[1].'" title="'.$w_title[1].'"onfocus="delete_value(&quot;'.$id.'_element_centsform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_centsform_id_temp&quot;); add_0(&quot;'.$id.'_element_centsform_id_temp&quot;)"onchange="change_value(&quot;'.$id.'_element_centsform_id_temp&quot;)" onkeypress="return check_isnum_interval(event,&quot;'.$id.'_element_centsform_id_temp&quot;,0,99)"style="width: 30px;" '.$param['attributes'].' disabled/></div></div><div id="'.$id.'_tr_price2" style="display: table-row;"><div style="display: table-cell;"><label class="mini_label"></label></div><div align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_dollars">'.$w_mini_labels[0].'</label></div><div id="'.$id.'_td_name_label_divider" style="display: '.$hide_cents.'"><label class="mini_label"></label></div><div align="left" id="'.$id.'_td_name_label_cents" style="display: '.$hide_cents.'"><label class="mini_label" id="'.$id.'_mini_label_cents">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
					break;
				}
				
				case 'type_paypal_select':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity', 'w_quantity_value','w_class','w_property','w_property_values');
					$temp=$params;
					
					if(strpos($temp, 'w_choices_params') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity', 'w_quantity_value', 'w_choices_params', 'w_class', 'w_property', 'w_property_values');

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

					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$param['w_choices']	= explode('***',$param['w_choices']);
					   
					$param['w_choices_price']	= explode('***',$param['w_choices_price']);
					   
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					  
					$param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);
					$param['w_property']	= explode('***',$param['w_property']);
					$param['w_property_values']	= explode('***',$param['w_property_values']);
			
					if(isset($param['w_choices_params']))
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
			
					foreach($param['w_choices_checked'] as $key => $choices_checked )
					{
						if($choices_checked=='true')
							$param['w_choices_checked'][$key]='selected="selected"';
						else
							$param['w_choices_checked'][$key]='';
					}

					
					$rep='<div id="wdform_field'.$id.'" type="type_paypal_select" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_paypal_select" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><select id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" onchange="set_select(this)" style="width: '.$param['w_size'].'px;"  '.$param['attributes'].' disabled>';
					foreach($param['w_choices'] as $key => $choice)
					{
						$where = '';
						$order_by = '';
						$db_info = '';
						$choice_value = $param['w_choices_disabled'][$key]=="true" ? '' : $param['w_choices_price'][$key];
							
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}	
							
					  $rep.='<option id="'.$id.'_option'.$key.'" value="'.$choice_value.'" onselect="set_select(&quot;'.$id.'_option'.$key.'&quot;)" '.$param['w_choices_checked'][$key].' '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</option>';
					}
					$rep.='</select><div id="'.$id.'_divform_id_temp">';
					if($param['w_quantity']=="yes")
						$rep.='<span id="'.$id.'_element_quantity_spanform_id_temp" style="margin-right: 15px;"><label class="mini_label" id="'.$id.'_element_quantity_label_form_id_temp" style="margin-right: 5px;"><!--repstart-->Quantity<!--repend--></label><input type="text" value="'.$param['w_quantity_value'].'" id="'.$id.'_element_quantityform_id_temp" name="'.$id.'_element_quantityform_id_temp" onkeypress="return check_isnum(event)" onchange="change_value(&quot;'.$id.'_element_quantityform_id_temp&quot;, this.value)" style="width: 30px; margin: 2px 0px;" disabled /></span>';
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{
	
					$rep.='
					<span id="'.$id.'_property_'.$key.'" style="margin-right: 15px;">
					
					<label class="mini_label" id="'.$id.'_property_label_form_id_temp'.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="'.$id.'_propertyform_id_temp'.$key.'" name="'.$id.'_propertyform_id_temp'.$key.'" style="width: auto; margin: 2px 0px;" disabled>';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="'.$id.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'">'.$property_value.'</option>';
					}
					$rep.='</select></span>';
					}
					
					$rep.='</div></div></div>';
					break;
				}
				
				case 'type_paypal_checkbox':
				{
										
					$params_names=array('w_field_label_size','w_field_label_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity','w_quantity_value');
					
					$temp=$params;
					if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class','w_property','w_property_values','w_quantity','w_quantity_value');
					
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

					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$param['w_choices']	= explode('***',$param['w_choices']);				   
					$param['w_choices_price']	= explode('***',$param['w_choices_price']);					
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);		  
					$param['w_property']	= explode('***',$param['w_property']);
					$param['w_property_values']	= explode('***',$param['w_property_values']);
					
					if(isset($param['w_choices_params']))
						$param['w_choices_params']	= explode('***',$param['w_choices_params']);
					
					foreach($param['w_choices_checked'] as $key => $choices_checked )
					{
						if($choices_checked=='true')
							$param['w_choices_checked'][$key]='checked="checked"';
						else
							$param['w_choices_checked'][$key]='';
					}
					
					$rep='<div id="wdform_field'.$id.'" type="type_paypal_checkbox" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_paypal_checkbox" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;">';
				
					if($param['w_flow']=='hor')
					{
						$rep.= '<div id="'.$id.'_hor" style="display: table-row;">';
						foreach($param['w_choices'] as $key => $choice)
						{
							$where ='';
							$order_by ='';
							$db_info = '';
							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
							{
								$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
								$where = "where='".$w_choices_params[0]."'";
								$w_choices_params = explode('[db_info]',$w_choices_params[1]);
								$order_by = "order_by='".$w_choices_params[0]."'";
								$db_info = "db_info='".$w_choices_params[1]."'";
							}
						
							$rep.='<div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="checkbox" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp'.$key.'" value="'.$param['w_choices_price'][$key].'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div>';
						}
						$rep.= '</div>';
					}
					else
					{		
						foreach($param['w_choices'] as $key => $choice)
						{
							$where ='';
							$order_by ='';
							$db_info ='';
							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
							{
								$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
								$where = "where='".$w_choices_params[0]."'";
								$w_choices_params = explode('[db_info]',$w_choices_params[1]);
								$order_by = "order_by='".$w_choices_params[0]."'";
								$db_info = "db_info='".$w_choices_params[1]."'";
							}
						
							$rep.='<div id="'.$id.'_element_tr'.$key.'" style="display: table-row;"><div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="checkbox" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp'.$key.'" value="'.$param['w_choices_price'][$key].'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div></div>';					}
					
					}
					$rep.='</div></div>';

					$rep.='<div id="'.$id.'_divform_id_temp">';
					if($param['w_quantity']=="yes")
						$rep.='<span id="'.$id.'_element_quantity_spanform_id_temp" style="margin-right: 15px;"><label class="mini_label" id="'.$id.'_element_quantity_label_form_id_temp" style="margin-right: 5px;"><!--repstart-->Quantity<!--repend--></label><input type="text" value="'.$param['w_quantity_value'].'" id="'.$id.'_element_quantityform_id_temp" name="'.$id.'_element_quantityform_id_temp" onkeypress="return check_isnum(event)" onchange="change_value(&quot;'.$id.'_element_quantityform_id_temp&quot;, this.value)" style="width: 30px; margin: 2px 0px;" disabled/></span>';
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{
					$rep.='
					<span id="'.$id.'_property_'.$key.'" style="margin-right: 15px;">
					
					<label class="mini_label" id="'.$id.'_property_label_form_id_temp'.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="'.$id.'_propertyform_id_temp'.$key.'" name="'.$id.'_propertyform_id_temp'.$key.'" style="width: auto; margin: 2px 0px;" disabled>';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="'.$id.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'">'.$property_value.'</option>';
					}
					$rep.='</select></span>';
					}
					
					$rep.='</div></div></div>';
					break;
				}
				case 'type_paypal_radio':
				{
					
					$params_names=array('w_field_label_size','w_field_label_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_class','w_property','w_property_values','w_quantity','w_quantity_value');
					
					$temp=$params;
					if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class','w_property','w_property_values','w_quantity','w_quantity_value');

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

					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$param['w_choices']	= explode('***',$param['w_choices']);
					   
					$param['w_choices_price']	= explode('***',$param['w_choices_price']);
					
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					  
					$param['w_property']	= explode('***',$param['w_property']);
					$param['w_property_values']	= explode('***',$param['w_property_values']);
					if(isset($param['w_choices_params']))
						$param['w_choices_params']	= explode('***',$param['w_choices_params']);
					
					foreach($param['w_choices_checked'] as $key => $choices_checked )
					{
						if($choices_checked=='true')
							$param['w_choices_checked'][$key]='checked="checked"';
						else
							$param['w_choices_checked'][$key]='';
					}
					
					$rep='<div id="wdform_field'.$id.'" type="type_paypal_radio" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_paypal_radio" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;">';
				
				if($param['w_flow']=='hor')
				{
					$rep.= '<div id="'.$id.'_hor" style="display: table-row;">';
					foreach($param['w_choices'] as $key => $choice)
					{
						$where ='';
						$order_by ='';
						$db_info ='';
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
						
						$rep.='<div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="radio" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp" value="'.$param['w_choices_price'][$key].'" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div>';
					}
					$rep.= '</div>';
				}
				else
				{
					foreach($param['w_choices'] as $key => $choice)
					{
						$where ='';
						$order_by ='';
						$db_info ='';
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
						
						$rep.='<div id="'.$id.'_element_tr'.$key.'" style="display: table-row;"><div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="radio" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp" value="'.$param['w_choices_price'][$key].'" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div></div>';
					}
				}
					$rep.='</div></div>';

					$rep.='<div id="'.$id.'_divform_id_temp">';
					if($param['w_quantity']=="yes")
						$rep.='<span id="'.$id.'_element_quantity_spanform_id_temp" style="margin-right: 15px;"><label class="mini_label" id="'.$id.'_element_quantity_label_form_id_temp" style="margin-right: 5px;"><!--repstart-->Quantity<!--repend--></label><input type="text" value="'.$param['w_quantity_value'].'" id="'.$id.'_element_quantityform_id_temp" name="'.$id.'_element_quantityform_id_temp" onkeypress="return check_isnum(event)" onchange="change_value(&quot;'.$id.'_element_quantityform_id_temp&quot;, this.value)" style="width: 30px; margin: 2px 0px;" disabled/></span>';
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{
					$rep.='
					<span id="'.$id.'_property_'.$key.'" style="margin-right: 15px;">
					
					<label class="mini_label" id="'.$id.'_property_label_form_id_temp'.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="'.$id.'_propertyform_id_temp'.$key.'" name="'.$id.'_propertyform_id_temp'.$key.'" style="width: auto; margin: 2px 0px;" disabled>';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="'.$id.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'">'.$property_value.'</option>';
					}
					$rep.='</select></span>';
					}
					
					$rep.='</div></div></div>';
				
					break;
				}
				case 'type_paypal_shipping':
				{		
					$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
					
					$temp=$params;
					if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_choices_params','w_class');
					
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

					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");	
					$param['w_choices']	= explode('***',$param['w_choices']);
					   
					$param['w_choices_price']	= explode('***',$param['w_choices_price']);
					
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					if(isset($param['w_choices_params']))
						$param['w_choices_params']	= explode('***',$param['w_choices_params']); 
					
					foreach($param['w_choices_checked'] as $key => $choices_checked )
					{
						if($choices_checked=='true')
							$param['w_choices_checked'][$key]='checked="checked"';
						else
							$param['w_choices_checked'][$key]='';
					}
					
					$rep='<div id="wdform_field'.$id.'" type="type_paypal_shipping" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_paypal_shipping" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;">';
				
				if($param['w_flow']=='hor')
				{
					$rep.= '<div id="'.$id.'_hor" style="display: table-row;">';
					foreach($param['w_choices'] as $key => $choice)
					{
						$where ='';
						$order_by ='';
						$db_info ='';
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
						
						$rep.='<div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="radio" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp" value="'.$param['w_choices_price'][$key].'" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div>';
					}
					$rep.= '</div>';
				}
				else
				{
					foreach($param['w_choices'] as $key => $choice)
					{
						$where ='';
						$order_by ='';
						$db_info =''; 
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
					
						$rep.='<div id="'.$id.'_element_tr'.$key.'" style="display: table-row;"><div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="radio" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp" value="'.$param['w_choices_price'][$key].'" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div></div>';
					}

				}
					$rep.='</div></div>';

					$rep.='</div></div>';
				
					break;
				}
				case 'type_paypal_total':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_class');
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
					
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
				
								
									
					$rep='<div id="wdform_field'.$id.'" type="type_paypal_total" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_paypal_total" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div id="'.$id.'paypal_totalform_id_temp" class="wdform_paypal_total paypal_totalform_id_temp"><input type="hidden" value="" name="'.$id.'_paypal_totalform_id_temp" class="input_paypal_totalform_id_temp"><div id="'.$id.'div_totalform_id_temp" class="div_totalform_id_temp" style="margin-bottom: 10px;"><!--repstart-->$300<!--repend--></div><div id="'.$id.'paypal_productsform_id_temp" class="paypal_productsform_id_temp" style="border-spacing: 2px;"><div style="border-spacing: 2px;"><!--repstart-->product 1 $100<!--repend--></div><div style="border-spacing: 2px;"><!--repstart-->product 2 $200<!--repend--></div></div><div id="'.$id.'paypal_taxform_id_temp" class="paypal_taxform_id_temp" style="border-spacing: 2px; margin-top: 7px;"></div></div></div></div>';
				
				
					break;
				}

				case 'type_star_rating':
				{
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
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");
					
	
				$images = '';	
					for($i=0; $i<$param['w_star_amount']; $i++)
					{
						$images .= '<img id="'.$id.'_star_'.$i.'" src="components/com_formmaker/images/star.png" onmouseover="change_src('.$i.','.$id.',&quot;form_id_temp&quot;)" onmouseout="reset_src('.$i.','.$id.')" onclick="select_star_rating('.$i.','.$id.', &quot;form_id_temp&quot;)">';
					}
					
					$rep ='<div id="wdform_field'.$id.'" type="type_star_rating" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_star_rating" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_star_amount'].'" id="'.$id.'_star_amountform_id_temp" name="'.$id.'_star_amountform_id_temp"><input type="hidden" value="'.$param['w_field_label_col'].'" name="'.$id.'_star_colorform_id_temp" id="'.$id.'_star_colorform_id_temp"><div id="'.$id.'_elementform_id_temp" class="wdform_stars" '.$param['attributes'].'>'.$images.'</div></div></div>';
					
					
					break;
				}
				case 'type_scale_rating':
				{
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
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");
					
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					
					$numbers = '';	
					for($i=1; $i<=$param['w_scale_amount']; $i++)
					{
						$numbers .= '<div id="'.$id.'_scale_td1_'.$i.'form_id_temp" style="text-align: center; display: table-cell;"><span>'.$i.'</span></div>';
					}
					
										
					$radio_buttons = '';	
					for($k=1; $k<=$param['w_scale_amount']; $k++)
					{
						$radio_buttons .= '<div id="'.$id.'_scale_td2_'.$k.'form_id_temp" style="display: table-cell;"><input id="'.$id.'_scale_radioform_id_temp_'.$k.'" name="'.$id.'_scale_radioform_id_temp" value="'.$k.'" type="radio" disabled/></div>';
					}
					
					$rep ='<div id="wdform_field'.$id.'" type="type_scale_rating" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align: top; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_scale_rating" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_scale_amount'].'" id="'.$id.'_scale_amountform_id_temp" name="'.$id.'_scale_amountform_id_temp"><div id="'.$id.'_elementform_id_temp" '.$param['attributes'].'><label class="mini_label" id="'.$id.'_mini_label_worst" style="position: relative; top: 6px; font-size: 11px; display: inline-table;">'.$w_mini_labels[0].'</label><div id="'.$id.'_scale_tableform_id_temp" style="display: inline-table;"><div id="'.$id.'_scale_tr1form_id_temp" style="display: table-row;">'.$numbers.'</div><div id="'.$id.'_scale_tr2form_id_temp" style="display: table-row;">'.$radio_buttons.'</div></div><label class="mini_label" id="'.$id.'_mini_label_best" style="position: relative; top: 6px; font-size: 11px; display: inline-table;">'.$w_mini_labels[1].'</label></div></div></div>';
							
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
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");
					
					 
					$rep ='<div id="wdform_field'.$id.'" type="type_spinner" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_spinner" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_field_width'].'" name="'.$id.'_spinner_widthform_id_temp" id="'.$id.'_spinner_widthform_id_temp"><input type="hidden" value="'.$param['w_field_min_value'].'" id="'.$id.'_min_valueform_id_temp" name="'.$id.'_min_valueform_id_temp"><input type="hidden" value="'.$param['w_field_max_value'].'" name="'.$id.'_max_valueform_id_temp" id="'.$id.'_max_valueform_id_temp"><input type="hidden" value="'.$param['w_field_step'].'" name="'.$id.'_stepform_id_temp" id="'.$id.'_stepform_id_temp"><input type="" value="'.($param['w_field_value']!= 'null' ? $param['w_field_value'] : '').'" name="'.$id.'_elementform_id_temp" id="'.$id.'_elementform_id_temp" onkeypress="return check_isnum_or_minus(event)" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].' disabled/></div></div>';
					
					
							
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
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");
					
					 
					$rep ='<div id="wdform_field'.$id.'" type="type_slider" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align: top; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_slider" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_field_width'].'" name="'.$id.'_slider_widthform_id_temp" id="'.$id.'_slider_widthform_id_temp"><input type="hidden" value="'.$param['w_field_min_value'].'" id="'.$id.'_slider_min_valueform_id_temp" name="'.$id.'_slider_min_valueform_id_temp"><input type="hidden" value="'.$param['w_field_max_value'].'" id="'.$id.'_slider_max_valueform_id_temp" name="'.$id.'_slider_max_valueform_id_temp"><input type="hidden" value="'.$param['w_field_value'].'" id="'.$id.'_slider_valueform_id_temp" name="'.$id.'_slider_valueform_id_temp"><div id="'.$id.'_slider_tableform_id_temp"><div><div id="'.$id.'_slider_td1form_id_temp"><div name="'.$id.'_elementform_id_temp" id="'.$id.'_elementform_id_temp" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'"></div></div></div><div><div align="left" id="'.$id.'_slider_td2form_id_temp" style="display: inline-table; width: 33.3%; text-align:left;"><span id="'.$id.'_element_minform_id_temp" class="wd_form_label">'.$param['w_field_min_value'].'</span></div><div align="right" id="'.$id.'_slider_td3form_id_temp" style="display: inline-table; width: 33.3%; text-align: center;"><span id="'.$id.'_element_valueform_id_temp" class="wd_form_label">'.$param['w_field_value'].'</span></div><div align="right" id="'.$id.'_slider_td4form_id_temp" style="display: inline-table; width: 33.3%; text-align:right;"><span id="'.$id.'_element_maxform_id_temp" class="wd_form_label">'.$param['w_field_max_value'].'</span></div></div></div></div></div>';
							
					break;
				}
				case 'type_range':
				{
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
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");
					
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					
					$rep ='<div id="wdform_field'.$id.'" type="type_range" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_range" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_field_range_width'].'" name="'.$id.'_range_widthform_id_temp" id="'.$id.'_range_widthform_id_temp"><input type="hidden" value="'.$param['w_field_range_step'].'" name="'.$id.'_range_stepform_id_temp" id="'.$id.'_range_stepform_id_temp"><div id="'.$id.'_elemet_table_littleform_id_temp" style="display: table;"><div style="display: table-row;"><div valign="middle" align="left" style="display: table-cell;"><input type="" value="'.($param['w_field_value1']!= 'null' ? $param['w_field_value1'] : '').'" name="'.$id.'_elementform_id_temp0" id="'.$id.'_elementform_id_temp0" onkeypress="return check_isnum_or_minus(event)" style="width: '.$param['w_field_range_width'].'px;"  '.$param['attributes'].' disabled/></div><div valign="middle" align="left" style="display: table-cell; padding-left: 4px;"><input type="" value="'.($param['w_field_value2']!= 'null' ? $param['w_field_value2'] : '').'" name="'.$id.'_elementform_id_temp1" id="'.$id.'_elementform_id_temp1" onkeypress="return check_isnum_or_minus(event)" style="width: '.$param['w_field_range_width'].'px;" '.$param['attributes'].' disabled/></div></div><div style="display: table-row;"><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_from">'.$w_mini_labels[0].'</label></div><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_to">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
											
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
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");
					
					$w_items = explode('***',$param['w_items']);
					
					
					$grading_items ='';
					
					for($i=0; $i<count($w_items); $i++)
					{
						$grading_items .= '<div id="'.$id.'_element_div'.$i.'" class="grading"><input type="text" id="'.$id.'_elementform_id_temp'.$i.'" name="'.$id.'_elementform_id_temp'.$i.'" onkeypress="return check_isnum_or_minus(event)" value=""  onkeyup="sum_grading_values('.$id.',&quot;form_id_temp&quot;)" onchange="sum_grading_values('.$id.',&quot;form_id_temp&quot;)" '.$param['attributes'].' style="width: 80px !important; margin-bottom: 5px;" disabled/><label id="'.$id.'_label_elementform_id_temp'.$i.'" class="ch-rad-label">'.$w_items[$i].'</label></div>';
					}
									
					$rep ='<div id="wdform_field'.$id.'" type="type_grading" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align: top; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_grading" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_total'].'" name="'.$id.'_grading_totalform_id_temp" id="'.$id.'_grading_totalform_id_temp"><div id="'.$id.'_elementform_id_temp">'.$grading_items.'<div id="'.$id.'_element_total_divform_id_temp" class="grading_div">Total:<span id="'.$id.'_sum_elementform_id_temp" name="'.$id.'_sum_elementform_id_temp">0</span>/<span id="'.$id.'_total_elementform_id_temp" name="'.$id.'_total_elementform_id_temp">'.$param['w_total'].'</span><span id="'.$id.'_text_elementform_id_temp" name="'.$id.'_text_elementform_id_temp"></span></div></div></div></div>';
											
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
					
					$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
					$required_sym = ($param['w_required']=="yes" ? " *" : "");
					
					$w_rows = explode('***',$param['w_rows']);
					$w_columns = explode('***',$param['w_columns']);
					
					
					$column_labels ='';
				
					for($i=1; $i<count($w_columns); $i++)
					{
						$column_labels .= '<div id="'.$id.'_element_td0_'.$i.'" class="matrix_" style="display: table-cell;"><label id="'.$id.'_label_elementform_id_temp0_'.$i.'" name="'.$id.'_label_elementform_id_temp0_'.$i.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$i.'" value="'.$w_columns[$i].'">'.$w_columns[$i].'</label></div>';
					}
					
					$rows_columns = '';
					
					for($i=1; $i<count($w_rows); $i++)
					{
					
						$rows_columns .= '<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;"><div id="'.$id.'_element_td'.$i.'_0" class="matrix_" style="display: table-cell;"><label id="'.$id.'_label_elementform_id_temp'.$i.'_0" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$i.'" value="'.$w_rows[$i].'">'.$w_rows[$i].'</label></div>';
						
						
						for($k=1; $k<count($w_columns); $k++)
						{
							if($param['w_field_input_type']=='radio')
								$rows_columns .= '<div id="'.$id.'_element_td'.$i.'_'.$k.'" style="text-align: center; display: table-cell;  padding: 5px 0 0 5px;"><input id="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" align="center" size="14" type="radio" name="'.$id.'_input_elementform_id_temp'.$i.'" value="'.$i.'_'.$k.'" disabled/></div>';
							else
								if($param['w_field_input_type']=='checkbox')
									$rows_columns .= '<div id="'.$id.'_element_td'.$i.'_'.$k.'" style="text-align: center; display: table-cell;  padding: 5px 0 0 5px;"><input id="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" align="center" size="14" type="checkbox" name="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" value="1" disabled/></div>';
								else
									if($param['w_field_input_type']=='text')
										$rows_columns .= '<div id="'.$id.'_element_td'.$i.'_'.$k.'" style="text-align: center; display: table-cell; padding: 5px 0 0 5px;"><input id="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" align="center" type="text" name="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" value="" style="width:100px" disabled/></div>';
									else
										if($param['w_field_input_type']=='select')
											$rows_columns .= '<div id="'.$id.'_element_td'.$i.'_'.$k.'" style="text-align: center; display: table-cell; padding: 5px 0 0 5px;"><select id="'.$id.'_select_yes_noform_id_temp'.$i.'_'.$k.'" name="'.$id.'_select_yes_noform_id_temp'.$i.'_'.$k.'" style="width:80px" disabled><option value=""> </option><option value="yes">Yes</option><option value="no">No</option></select></div>';
								
						}
							
						$rows_columns .= '</div>';	
					}
						
					
						
					$rep ='<div id="wdform_field'.$id.'" type="type_matrix" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_matrix" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_field_input_type'].'" name="'.$id.'_input_typeform_id_temp" id="'.$id.'_input_typeform_id_temp"><div id="'.$id.'_elementform_id_temp" style="display: table;" '.$param['attributes'].'><div id="'.$id.'_table_little" style="display: table-row-group;"><div id="'.$id.'_element_tr0" style="display: table-row;"><div id="'.$id.'_element_td0_0" style="display: table-cell;"></div>'.$column_labels.'</div>'.$rows_columns.'</div></div></div></div>';
											
					break;
				}
			
				case 'type_submit_reset':
				{
					
					$params_names=array('w_submit_title','w_reset_title','w_class','w_act');
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

					$param['w_act'] = ($param['w_act']=="false" ? 'style="display: none;"' : "");	
					
					$rep='<div id="wdform_field'.$id.'" type="type_submit_reset" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">type_submit_reset_'.$id.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><input type="hidden" value="type_submit_reset" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><button type="button" class="button-submit" id="'.$id.'_element_submitform_id_temp" value="'.$param['w_submit_title'].'" onclick="check_required(&quot;submit&quot;, &quot;form_id_temp&quot;);" '.$param['attributes'].'>'.$param['w_submit_title'].'</button><button type="button" class="button-reset" id="'.$id.'_element_resetform_id_temp" value="'.$param['w_reset_title'].'" onclick="check_required(&quot;reset&quot;);" '.$param['w_act'].' '.$param['attributes'].'>'.$param['w_reset_title'].'</button></div></div>';
				
					break;
				}
				case 'type_button':
				{
					
					$params_names=array('w_title','w_func','w_class');
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
					
					$param['w_title']	= explode('***',$param['w_title']);
					$param['w_func']	= explode('***',$param['w_func']);

					$rep.='<div id="wdform_field'.$id.'" type="type_button" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">button_'.$id.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><input type="hidden" value="type_button" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp">';
					foreach($param['w_title'] as $key => $title)
					{
					$rep.='<button type="button" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp'.$key.'" value="'.$title.'" onclick="'.$param['w_func'][$key].'" '.$param['attributes'].'>'.$title.'</button>';				
					}				
					$rep.='</div></div>';
					break;
				}
			}
					
			$form=str_replace('%'.$id.' - '.$labels[$ids_key].'%', $rep, $form);
			$form=str_replace('%'.$id.' -'.$labels[$ids_key].'%', $rep, $form);
		}
		
	}
	$row->form_front=$form;
	HTML_contact::edit($row, $labels2);
	
	}
	else		
	HTML_contact::edit_old($row, $labels2);
}


function copy_form()
{
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.create', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),'error');
	$db		= JFactory::getDBO();
	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));
	$id 	= $cid[0];
	$row = JTable::getInstance('formmaker', 'Table');

	// load the row from the db table
	$row->load( $id);
		
	$mainframe = JFactory::getApplication();
	
	$row->id='';
	$row->created_by = $user->id;
	$new=true;

	if(!$row->store()){

		JError::raiseError(500, $row->getError() );

	}
	
	if($new)
	{
		$db = JFactory::getDBO();
		$db->setQuery("INSERT INTO #__formmaker_views (form_id, views) VALUES('".$row->id."', 0)" ); 
		$db->query();
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

	}
	
		$msg = 'The form has been saved successfully.';

		$link = 'index.php?option=com_formmaker';
		$mainframe->redirect($link, $msg, 'message');
}

function editSubmit(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
    $db = JFactory::getDBO();

	$cid 	= JRequest::getVar('cid', array(0), '', 'array');
	JArrayHelper::toInteger($cid, array(0));
	$id 	= $cid[0];

	$query = "SELECT * FROM #__formmaker_submits WHERE group_id=".$id;
	$db->setQuery( $query);
	$rows = $db->loadObjectList();
	if($db->getErrorNum()){echo $db->stderr();return false;}

	$form = JTable::getInstance('formmaker', 'Table');
	$form->load( $rows[0]->form_id);

	if(!$user->authorise('core.manage.submits', 'com_formmaker'))
	{
		if($user->authorise('core.manage.submits.own', 'com_formmaker') && $form->created_by != $user->id)	
			$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),'error');
	}
	
	$query = "SELECT css FROM #__formmaker_themes WHERE id='".($form->theme)."'";

	$db->setQuery( $query);

	$form_theme = $db->loadResult();
	
		$label_id= array();
		$label_order_original= array();
		$label_type= array();
		$ispaypal=strpos($form->label_order, 'type_paypal_');
		if($form->paypal_mode==1)
			if($ispaypal)
				$form->label_order=$form->label_order."0#**id**#Payment Status#**label**#type_paypal_payment_status#****#";
		
		$label_all	= explode('#****#',$form->label_order);
		$label_all 	= array_slice($label_all,0, count($label_all)-1);   
		
		
		
		foreach($label_all as $key => $label_each) 
		{
			$label_id_each=explode('#**id**#',$label_each);
			array_push($label_id, $label_id_each[0]);
			
			$label_oder_each=explode('#**label**#', $label_id_each[1]);
			array_push($label_order_original, $label_oder_each[0]);
			array_push($label_type, $label_oder_each[1]);

		
			
		}
		
	$new_form = false;
	
	if(strpos($form_theme,'.wdform_section')>-1)
		$new_form = true;

	if($new_form == false)	
		HTML_contact::editSubmit_old($rows, $label_id ,$label_order_original,$label_type,$ispaypal);
	else
		HTML_contact::editSubmit($rows, $label_id ,$label_order_original,$label_type,$ispaypal,$form,$form_theme); 
	 
	// display function 

}

function saveSubmit($task){

	$mainframe = JFactory::getApplication();
	$db		= JFactory::getDBO();
	$group_id 	= JRequest::getVar('id');
	$date 	= JRequest::getVar('date');
	$ip 	= JRequest::getVar('ip');
	
	$form_id 	= JRequest::getVar('form_id');
	$form = JTable::getInstance('formmaker', 'Table');
	$form->load($form_id);
		
		$label_id= array();
		$label_label= array();
		$label_type= array();
		
		$form_currency='$';

		$currency_code=array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');

		$currency_sign=array('$'  , '€'  , '£'  , '¥'  , 'C$', 'Mex$', 'HK$', 'Ft' , 'kr' , 'NZ$', 'S$' , 'kr' , 'zł' , 'A$' , 'kr' , 'CHF' , 'Kč', '₪'  , 'R$' , 'NT$', 'RM' , '₱'  , '฿'  );

	
		if($form->payment_currency)
		$form_currency=	$currency_sign[array_search($form->payment_currency, $currency_code)];
		
		
		if(strpos($form->label_order, 'type_paypal_'))
		{
			$form->label_order=$form->label_order."0#**id**#Payment Status#**label**#type_paypal_payment_status#****#";
		}

		
		$label_all	= explode('#****#',$form->label_order);
		$label_all 	= array_slice($label_all,0, count($label_all)-1);   
		
		$input_get = JFactory::getApplication()->input;
		
		foreach($label_all as $key => $label_each) 
		{
			$label_id_each=explode('#**id**#',$label_each);
			array_push($label_id, $label_id_each[0]);
			
			$label_oder_each=explode('#**label**#', $label_id_each[1]);
			array_push($label_label, $label_oder_each[0]);
			array_push($label_type, $label_oder_each[1]);

		
			
		}
	
		foreach($label_type as $key => $type)
			{

				$value='';
				if($type=="type_submit_reset" or $type=="type_map" or $type=="type_editor" or  $type=="type_captcha" or  $type=="type_recaptcha" or  $type=="type_button" or $type=="type_paypal_total")
					continue;

				$i=$label_id[$key];
				$id = 'form_id_temp';		
				switch ($type)
				{
					case 'type_text':
					case 'type_password':
					case 'type_textarea':
					case "type_submitter_mail":
					case "type_date":
					case "type_own_select":					
					case "type_country":				
					case "type_number":				
					{
						$value=$input_get->getString('wdform_'.$i."_element".$id);
						break;
					}
					case "type_wdeditor":				
					{
					$value=$input_get->getString( 'wdform_'.$i.'_wd_editor'.$id, '', 'post', 'string', JREQUEST_ALLOWRAW );
					break;
					}
					
										
					case "type_date_fields":
					{
						$value=$input_get->getString('wdform_'.$i."_day".$id).'-'.$input_get->getString('wdform_'.$i."_month".$id).'-'.$input_get->getString('wdform_'.$i."_year".$id);
						break;
					}
					
					case "type_time":
					{
						$ss=$input_get->getString('wdform_'.$i."_ss".$id);
						if(isset($ss))
							$value=$input_get->getString('wdform_'.$i."_hh".$id).':'.$input_get->getString('wdform_'.$i."_mm".$id).':'.$input_get->getString('wdform_'.$i."_ss".$id);
						else
							$value=$input_get->getString('wdform_'.$i."_hh".$id).':'.$input_get->getString('wdform_'.$i."_mm".$id);
								
						$am_pm=$input_get->getString('wdform_'.$i."_am_pm".$id);
						if(isset($am_pm))
							$value=$value.' '.$input_get->getString('wdform_'.$i."_am_pm".$id);
							
						break;
					}
					
					case "type_phone":
					{
						$value=$input_get->getString('wdform_'.$i."_element_first".$id).' '.$input_get->getString('wdform_'.$i."_element_last".$id);
							
						break;
					}
		
					case "type_name":
					{
				
						$element_title=$input_get->getString('wdform_'.$i."_element_title".$id);
						if(isset($element_title))
							$value=$input_get->getString('wdform_'.$i."_element_title".$id).'@@@'.$input_get->getString('wdform_'.$i."_element_first".$id).'@@@'.$input_get->getString('wdform_'.$i."_element_last".$id).'@@@'.$input_get->getString('wdform_'.$i."_element_middle".$id);
						else
							$value=$input_get->getString('wdform_'.$i."_element_first".$id).'@@@'.$input_get->getString('wdform_'.$i."_element_last".$id);
							
						break;
					}
		
					case "type_file_upload":
					{
						
						
						break;
					}
					
					case 'type_address':
					{
						$value='*#*#*#';
						$element=$input_get->getString('wdform_'.$i."_street1".$id);
						if(isset($element))
						{
							$value=$input_get->getString('wdform_'.$i."_street1".$id);
							break;
						}
						
						$element=$input_get->getString('wdform_'.$i."_street2".$id);
						if(isset($element))
						{
							$value=$input_get->getString('wdform_'.$i."_street2".$id);
							break;
						}
						
						$element=$input_get->getString('wdform_'.$i."_city".$id);
						if(isset($element))
						{
							$value=$input_get->getString('wdform_'.$i."_city".$id);
							break;
						}
						
						$element=$input_get->getString('wdform_'.$i."_state".$id);
						if(isset($element))
						{
							$value=$input_get->getString('wdform_'.$i."_state".$id);
							break;
						}
						
						$element=$input_get->getString('wdform_'.$i."_postal".$id);
						if(isset($element))
						{
							$value=$input_get->getString('wdform_'.$i."_postal".$id);
							break;
						}
						
						$element=$input_get->getString('wdform_'.$i."_country".$id);
						if(isset($element))
						{
							$value=$input_get->getString('wdform_'.$i."_country".$id);
							break;
						}
						
						break;
					}
					
					case "type_hidden":				
					{
						$value=JRequest::getVar($label_order_original[$key]);
						break;
					}
					
					case "type_radio":				
					{
						$element=$input_get->getString('wdform_'.$i."_other_input".$id);
						if(isset($element))
						{
							$value=$element;	
							break;
						}
						
						$value=$input_get->getString('wdform_'.$i."_element".$id);
						break;
					}
					
					case "type_checkbox":				
					{
						$start=-1;
						$value='';
						for($j=0; $j<100; $j++)
						{
						
							$element=$input_get->getString('wdform_'.$i."_element".$id.$j);
			
							if(isset($element))
							{
								$start=$j;
								break;
							}
						}
							
						$other_element_id=-1;
						$is_other=$input_get->getString('wdform_'.$i."_allow_other".$id);
						if($is_other=="yes")
						{
							$other_element_id=$input_get->getString('wdform_'.$i."_allow_other_num".$id);
						}
						
						if($start!=-1)
						{
							for($j=$start; $j<100; $j++)
							{
								$element=$input_get->getString('wdform_'.$i."_element".$id.$j);
								if(isset($element))
								if($j==$other_element_id)
								{
									$value=$value.$input_get->getString('wdform_'.$i."_other_input".$id).'***br***';
								}
								else
								
									$value=$value.$input_get->getString('wdform_'.$i."_element".$id.$j).'***br***';
							}
						}
						
						break;
					}
					
					case "type_paypal_price":	
					{		
						$value=0;
						if($input_get->getString('wdform_'.$i."_element_dollars".$id))
							$value=$input_get->getString('wdform_'.$i."_element_dollars".$id);
							
						$value = (int) preg_replace('/\D/', '', $value);
						
						if($input_get->getString('wdform_'.$i."_element_cents".$id))
						{
							$value=$value.'.'.( preg_replace('/\D/', '', $input_get->getString('wdform_'.$i."_element_cents".$id)));
						
						
						}
						$value=$value.$form_currency;
					
						break;
					}			
					
					case "type_paypal_select":	
					{	
			
						if($input_get->getString('wdform_'.$i."_element_label".$id))
							$value=$input_get->getString('wdform_'.$i."_element_label".$id).' : '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
						else
							$value='';
					
						
						$element_quantity=$input_get->getString('wdform_'.$i."_element_quantity".$id);
						if(isset($element_quantity) && $value!='')
							$value.='***br***'.$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id).'***quantity***';
						
						

						for($k=0; $k<50; $k++)
						{
							$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
							if(isset($temp_val) && $value!='')
							{			
								
								$value.='***br***'.$input_get->getString('wdform_'.$i."_element_property_label".$id).': '.$input_get->getString('wdform_'.$i."_property".$id.$k).'***property***';
							}
						}
						
						break;
					}
					
					case "type_paypal_radio":				
					{
						
						if($input_get->getString('wdform_'.$i."_element_label".$id))
							$value=$input_get->getString('wdform_'.$i."_element_label".$id).' : '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
						else
							$value='';

						
						$element_quantity=$input_get->getString('wdform_'.$i."_element_quantity".$id);
						if(isset($element_quantity) && $value!='')
							$value.='***br***'.$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id).'***quantity***';
						
					
						for($k=0; $k<50; $k++)
						{
							$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
							if(isset($temp_val) && $value!='')
							{			
								$value.='***br***'.$input_get->getString('wdform_'.$i."_element_property_label".$id).': '.$input_get->getString('wdform_'.$i."_property".$id.$k).'***property***';
							}
						}
					
						break;
					}

					case "type_paypal_shipping":				
					{
						if($input_get->getString('wdform_'.$i."_element_label".$id))
							$value=$input_get->getString('wdform_'.$i."_element_label".$id).' : '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
						else
							$value='';
						$value=$input_get->getString('wdform_'.$i."_element_label".$id).' - '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
											
						break;
					}

					case "type_paypal_checkbox":				
					{
						$start=-1;
						$value='';
						for($j=0; $j<100; $j++)
						{
						
							$element=$input_get->getString('wdform_'.$i."_element".$id.$j);
			
							if(isset($element))
							{
								$start=$j;
								break;
							}
						}
						
						$other_element_id=-1;
						$is_other=$input_get->getString('wdform_'.$i."_allow_other".$id);
						if($is_other=="yes")
						{
							$other_element_id=$input_get->getString('wdform_'.$i."_allow_other_num".$id);
						}
						
						if($start!=-1)
						{
							for($j=$start; $j<100; $j++)
							{
								$element=$input_get->getString('wdform_'.$i."_element".$id.$j);
								if(isset($element))
								if($j==$other_element_id)
								{
									$value=$value.$input_get->getString('wdform_'.$i."_other_input".$id).'***br***';
									
								}
								else
								{
							
									$value=$value.$input_get->getString('wdform_'.$i."_element".$id.$j."_label").' - '.($input_get->getString('wdform_'.$i."_element".$id.$j)=='' ? '0' : $input_get->getString('wdform_'.$i."_element".$id.$j)).$form_currency.'***br***';
																		
								}
							}
							
							$element_quantity=$input_get->getString('wdform_'.$i."_element_quantity".$id);
							if(isset($element_quantity))
								$value.=$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id).'***quantity***';
							
							for($k=0; $k<50; $k++)
							{
								$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
								if(isset($temp_val))
								{			
									$value.='***br***'.$input_get->getString('wdform_'.$i."_element_property_label".$id).': '.$input_get->getString('wdform_'.$i."_property".$id.$k).'***property***';
								}
							}
							
						}
						
						
						break;
					}
					
					case "type_star_rating":				
					{
					
						if($input_get->getString('wdform_'.$i."_selected_star_amount".$id)=="")
						$selected_star_amount=0;
						else
						$selected_star_amount=$input_get->getString('wdform_'.$i."_selected_star_amount".$id);
						
						$value=$selected_star_amount.'/'.$input_get->getString('wdform_'.$i."_star_amount".$id);									
						break;
					}
				
					case "type_scale_rating":				
					{
																	
						$value=$input_get->getString('wdform_'.$i."_scale_radio".$id,0).'/'.$input_get->getString('wdform_'.$i."_scale_amount".$id);									
						break;
					}
					
					case "type_spinner":				
					{
						$value=$input_get->getString('wdform_'.$i."_element".$id);		
					
						break;
					}
					
					case "type_slider":				
					{
						$value=$input_get->getString('wdform_'.$i."_slider_value".$id);		
					
						break;
					}
					case "type_range":				
					{
						$value = $input_get->getString('wdform_'.$i."_element".$id.'0').'-'.$input_get->getString('wdform_'.$i."_element".$id.'1');	
					
						break;
					}
					case "type_grading":				
					{
						$value ="";
						$items = explode(":",$input_get->getString('wdform_'.$i."_hidden_item".$id));
						for($k=0; $k<sizeof($items)-1; $k++)
						$value .= $input_get->getString('wdform_'.$i."_element".$id.'_'.$k).':';
						$value .= $input_get->getString('wdform_'.$i."_hidden_item".$id).'***grading***';
				
						break;
					}
					
					case "type_matrix":				
					{
					
						$rows_of_matrix=explode("***",$input_get->getString('wdform_'.$i."_hidden_row".$id));
						$rows_count= sizeof($rows_of_matrix)-1;
						$column_of_matrix=explode("***",$input_get->getString('wdform_'.$i."_hidden_column".$id));
						$columns_count= sizeof($column_of_matrix)-1;
						
					
						if($input_get->getString('wdform_'.$i."_input_type".$id)=="radio")
						{
							$input_value="";

							for($k=1; $k<=$rows_count; $k++)
							$input_value.=$input_get->getString('wdform_'.$i."_input_element".$id.$k,0)."***";
							
						}
						if($input_get->getString('wdform_'.$i."_input_type".$id)=="checkbox")
						{
							$input_value="";
							
							for($k=1; $k<=$rows_count; $k++)
							for($j=1; $j<=$columns_count; $j++)
							$input_value.=$input_get->getString('wdform_'.$i."_input_element".$id.$k.'_'.$j,0)."***";
						}
						
						if($input_get->getString('wdform_'.$i."_input_type".$id)=="text")
						{
							$input_value="";
							for($k=1; $k<=$rows_count; $k++)
							for($j=1; $j<=$columns_count; $j++)
							$input_value.=$input_get->getString('wdform_'.$i."_input_element".$id.$k.'_'.$j)."***";
						}
						
						if($input_get->getString('wdform_'.$i."_input_type".$id)=="select")
						{
							$input_value="";
							for($k=1; $k<=$rows_count; $k++)
							for($j=1; $j<=$columns_count; $j++)
							$input_value.=$input_get->getString('wdform_'.$i."_select_yes_no".$id.$k.'_'.$j)."***";	
						}
						
						$value=$rows_count.$input_get->getString('wdform_'.$i."_hidden_row".$id).'***'.$columns_count.$input_get->getString('wdform_'.$i."_hidden_column".$id).'***'.$input_get->getString('wdform_'.$i."_input_type".$id).'***'.$input_value.'***matrix***';	
					
						break;
					}
					
				}

				if($type=="type_address")
					if(	$value=='*#*#*#')
						continue;

				if($value)
				{
					$query = "SELECT id FROM #__formmaker_submits WHERE group_id='".$group_id."' AND element_label='".$i."'";
					$db->setQuery( $query);
					$result=$db->loadResult();
					if($db->getErrorNum()){	echo $db->stderr();	return false;}
					
				
					if($result)
					{
						$query = "UPDATE #__formmaker_submits SET `element_value`='".$value."' WHERE group_id='".$group_id."' AND element_label='".$i."'";
						$db->setQuery( $query);
						$db->query();
						if($db->getErrorNum()){	echo $db->stderr();	return false;}
					}
					else
					{
						$query = "INSERT INTO #__formmaker_submits (form_id, element_label, element_value, group_id, date, ip) VALUES('".$form_id."', '".$i."', '".$element_value."','".$group_id."', '".$date."', '".$ip."')" ;
						$db->setQuery( $query);
						$db->query();
						if($db->getErrorNum()){	echo $db->stderr();	return false;}
					}
				}		
	
			}
			
	
	switch ($task)
	{
		case 'save_submit':
		$msg = 'Submission has been saved successfully.';
		$link ='index.php?option=com_formmaker&task=submits';
		break;
		case 'apply_submit':
		default:
		$msg = 'Submission has been saved successfully.';
		$link ='index.php?option=com_formmaker&task=edit_submit&cid[]='.$group_id;
		break;
	}
	
	$mainframe->redirect($link, $msg, 'message');

}

function form_options_temp(){

	$mainframe = JFactory::getApplication();
	$row_id=save('return_id');
	$link = 'index.php?option=com_formmaker&task=form_options&cid[]='.$row_id;

	$mainframe->redirect($link);

}
function form_layout_temp(){
	
	$mainframe = JFactory::getApplication();
	$row_id=save('return_id');
	$link = 'index.php?option=com_formmaker&task=form_layout&cid[]='.$row_id;
	$mainframe->redirect($link);
}

function removeSubmit(){

	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	
	if(!$user->authorise('core.delete.submits', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=submits", JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'),'error');	

  $db = JFactory::getDBO();

  // Define cid array variable

  $form_id = JRequest::getVar( 'form_id');
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );

  // Make sure cid array variable content integer format

  JArrayHelper::toInteger($cid);

  // If any item selected

  if (count( $cid )) {


    $cids = implode( ',', $cid );

    // Create sql statement

    $query = 'DELETE FROM #__formmaker_submits'

    . ' WHERE group_id IN ( '. $cids .' )'

    ;

    // Execute query

    $db->setQuery( $query );

    if (!$db->query()) {

      echo "<script> alert('".$db->getErrorMsg(true)."'); 

      window.history.go(-1); </script>\n";

    }
	
    $query = 'DELETE FROM #__formmaker_sessions'

    . ' WHERE group_id IN ( '. $cids .' )'

    ;

    // Execute query

    $db->setQuery( $query );

    if (!$db->query()) {

      echo "<script> alert('".$db->getErrorMsg(true)."'); 

      window.history.go(-1); </script>\n";

    }
	
	

  }

  // After all, redirect again to frontpage
  $msg = 'The submission(s) has been successfully deleted.';
  $mainframe->redirect( "index.php?option=com_formmaker&task=submits&form_id=".$form_id, $msg );


}

function blockIP(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.block', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=submits", JText::_('JACTION_NOT_PERMITTED'),'error');	

	$db		= JFactory::getDBO();
	$id 	= JRequest::getVar('id');

  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );



  // Make sure cid array variable content integer format



  JArrayHelper::toInteger($cid);



  // If any item selected



  if (count( $cid )) {

    $cids = implode( ',', $cid );


    // Create sql statement


    $query = 'SELECT * FROM #__formmaker_submits'


    . ' WHERE group_id IN ( '. $cids .' )';

			$db->setQuery($query); 
						
		$rows = $db->loadObjectList();	


    if($db->getErrorNum()){	echo $db->stderr();	return false;}
	


			foreach($rows as $row)
			{
				
				$query = 'SELECT ip FROM #__formmaker_blocked WHERE ip="'.($row->ip).'"';
				$db->setQuery($query); 			
				$ips = $db->loadObjectList();

				if (!$ips)
				{
				
					$query = "INSERT INTO #__formmaker_blocked (ip) VALUES('".$row->ip."')" ;
					$db->setQuery( $query);
					$db->query();
					if($db->getErrorNum()){	echo $db->stderr();	return false;}		
					
				}
				
			}


	}


		$link ='index.php?option=com_formmaker&task=submits';

	
	$msg = 'The IP(s) has been successfully blocked.';
	$mainframe->redirect($link, $msg);
	
	
}

function remove_blocked_ips(){
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.delete', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker&task=blocked_ips", JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'),'error');	
 
  $db = JFactory::getDBO();
  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );


  JArrayHelper::toInteger($cid);


  if (count( $cid )) {



    // Prepare sql statement, if cid array more than one, 



    // will be "cid1, cid2, ..."



    $cids = implode( ',', $cid );



    // Create sql statement



    $query = 'DELETE FROM #__formmaker_blocked' . ' WHERE id IN ( '. $cids .' )'  ;



    // Execute query



    $db->setQuery( $query );



    if (!$db->query()) {



      echo "<script> alert('".$db->getErrorMsg(true)."'); 



      window.history.go(-1); </script>\n";



    }




  }


  // After all, redirect again to frontpage

  $msg = 'The IP(s) has been successfully deleted.';
  $mainframe->redirect( "index.php?option=com_formmaker&task=blocked_ips", $msg );



}



function remove(){

	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	if(!$user->authorise('core.delete', 'com_formmaker')) 
		$mainframe->redirect( "index.php?option=com_formmaker", JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'),'error');	
  // Initialize variables	

  $db = JFactory::getDBO();

  // Define cid array variable

  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );

  // Make sure cid array variable content integer format

  JArrayHelper::toInteger($cid);



  // If any item selected

  if (count( $cid )) {

    // Prepare sql statement, if cid array more than one, 

    // will be "cid1, cid2, ..."

    $cids = implode( ',', $cid );

    // Create sql statement

    $query = 'DELETE FROM #__formmaker' . ' WHERE id IN ( '. $cids .' )'  ;

    // Execute query

    $db->setQuery( $query );

    if (!$db->query()) {

      echo "<script> alert('".$db->getErrorMsg(true)."'); 

      window.history.go(-1); </script>\n";

    }
	
    $query = 'DELETE FROM #__formmaker_views' . ' WHERE form_id IN ( '. $cids .' )'  ;

    // Execute query

    $db->setQuery( $query );

    if (!$db->query()) {

      echo "<script> alert('".$db->getErrorMsg(true)."'); 

      window.history.go(-1); </script>\n";

    }
	

  }

	remove_submits_all( $cids );

  // After all, redirect again to frontpage
  $msg = 'The form(s) has been successfully deleted.';
  $mainframe->redirect( "index.php?option=com_formmaker", $msg );

}

function remove_submits_all( $cids ){
  $db = JFactory::getDBO();
	$query = 'DELETE FROM #__formmaker_submits'

    . ' WHERE form_id IN ( '. $cids .' )'

    ;

    // Execute query

    $db->setQuery( $query );

    if (!$db->query()) {

      echo "<script> alert('".$db->getErrorMsg(true)."'); 

      window.history.go(-1); </script>\n";

    }

}

function change( $state=0 ){

  $mainframe = JFactory::getApplication();



  // Initialize variables

  $db 	= JFactory::getDBO();



  // define variable $cid from GET

  $cid = JRequest::getVar( 'cid' , array() , '' , 'array' );	

  JArrayHelper::toInteger($cid);



  // Check there is/are item that will be changed. 

  //If not, show the error.

  if (count( $cid ) < 1) {

    $action = $state ? 'publish' : 'unpublish';

    JError::raiseError(500, JText::_( 'Select an item 

    to' .$action, true ) );

  }



  // Prepare sql statement, if cid more than one, 

  // it will be "cid1, cid2, cid3, ..."

  $cids = implode( ',', $cid );



  $query = 'UPDATE #__formmaker'

  . ' SET published = ' . (int) $state

  . ' WHERE id IN ( '. $cids .' )'

  ;

  // Execute query

  $db->setQuery( $query );

  if (!$db->query()) {

    JError::raiseError(500, $db->getErrorMsg() );

  }



  if (count( $cid ) == 1) {

    $row = JTable::getInstance('formmaker', 'Table');

    $row->checkin( intval( $cid[0] ) );

  }



  // After all, redirect to front page

  $mainframe->redirect( 'index.php?option=com_formmaker' );

}

function cancel(){

  $mainframe = JFactory::getApplication();

  $mainframe->redirect( 'index.php?option=com_formmaker&task=forms' );



}

function cancel_themes(){

  $mainframe = JFactory::getApplication();
  $mainframe->redirect( 'index.php?option=com_formmaker&task=themes' );
}

function cancel_blocked_ips(){
  $mainframe = JFactory::getApplication();
  $mainframe->redirect( 'index.php?option=com_formmaker&task=blocked_ips' );
}
	


function cancelSecondary(){

	$mainframe = JFactory::getApplication();

	if(JRequest::getVar('id')==0)

	$link = 'index.php?option=com_formmaker&task=add';

	else

	$link = 'index.php?option=com_formmaker&task=edit&cid[]='.JRequest::getVar('id');

	$mainframe->redirect($link);

}

function cancelSubmit(){
	$mainframe = JFactory::getApplication();
	$link = 'index.php?option=com_formmaker&task=submits';
	$mainframe->redirect($link);

}
		
function select_article(){


	$mainframe = JFactory::getApplication();
	
	$db = JFactory::getDBO();

	$option='com_formmaker';

	$filter_order= $mainframe-> getUserStateFromRequest( $option.'filter_order', 'filter_order','id','cmd' );
	$filter_order_Dir= $mainframe-> getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir','','word' );
	$filter_state = $mainframe->getUserStateFromRequest( $option.'filter_state', 'filter_state', '','word' );
	$search = $mainframe-> getUserStateFromRequest( $option.'search', 'search','','string' );
	$search = JString::strtolower( $search );
	$limit= $mainframe-> getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart= $mainframe-> getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

	$where = array();

	if ( $search ) {

		$where[] = 'title LIKE "%'.$db->escape($search).'%"';

	}	

	

	$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

	if ($filter_order == 'id' or $filter_order == 'group_id' or $filter_order == 'group_id' or $filter_order == 'date' or $filter_order == 'ip'){

		$orderby 	= ' ORDER BY id';

	} else {

		$orderby 	= ' ORDER BY '. 

         $filter_order .' '. $filter_order_Dir .', id';

	}	

	

	// get the total number of records

	$query = 'SELECT COUNT(*)'

	. ' FROM #__content'

	. $where

	;

	$db->setQuery( $query );

	$total = $db->loadResult();



	jimport('joomla.html.pagination');

	$pageNav = new JPagination( $total, $limitstart, $limit );	

	$query = "SELECT * FROM #__content". $where. $orderby;

	$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

	$rows = $db->loadObjectList();

	if($db->getErrorNum()){

		echo $db->stderr();

		return false;

	}



	// table ordering

	$lists['order_Dir']	= $filter_order_Dir;

	$lists['order']		= $filter_order;	



	// search filter	

        $lists['search']= $search;	

	

    // display function

	HTML_contact::select_article($rows, $pageNav, $lists);

}
?>