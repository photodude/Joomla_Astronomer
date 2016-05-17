<?php
 /**
 * @package Form Maker Module
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 

// no direct access
defined('_JEXEC') or die('Restricted access');


jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

$lang =  JFactory::getLanguage();
$lang->load('com_formmaker',JPATH_BASE);

class modFormmaker
{	
	public static function load($form)
	{
		$result = modFormmaker::showform( $form);
		if(!$result)
			return;
			
		$ok		= modFormmaker::savedata($form, $result[0] );

		if(is_numeric($ok))		
			modFormmaker::remove($ok);
		
		return modFormmaker::defaultphp($result[0], $result[1], $result[2], $result[3], $result[4], $form,$ok);
			
	}
	// This is always going to get the first instance of the module type unless
	// there is a title.
	public static function showform($id)
	{
			$input_get = JFactory::getApplication()->input;

		$Itemid=$input_get->getString('Itemid'.$id);

		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__formmaker WHERE id=".$db->escape((int)$id) ); 
		$row = $db->loadObject();
		if ($db->getErrorNum())	{echo $db->stderr();return false;}	
		
		if(!$row)
			return false;
			
		if($row->published!=1)
			return false;	
		
		$test_theme = $input_get->getString('test_theme');
		$row->theme = (isset($test_theme) ? $test_theme : $row->theme);		
		$db->setQuery("SELECT css FROM #__formmaker_themes WHERE id=".$db->escape((int)$row->theme) ); 
		$form_theme = $db->loadResult();
		if ($db->getErrorNum())	{echo $db->stderr(); return false;}	
		
		$label_id= array();
		$label_type= array();
			
		$label_all	= explode('#****#',$row->label_order);
		$label_all 	= array_slice($label_all,0, count($label_all)-1);   
		
		foreach($label_all as $key => $label_each) 
		{
			$label_id_each=explode('#**id**#',$label_each);
			array_push($label_id, $label_id_each[0]);
			
			$label_order_each=explode('#**label**#', $label_id_each[1]);
			
			array_push($label_type, $label_order_each[1]);
		}
		
		return array($row, $Itemid, $label_id, $label_type ,$form_theme);
	}

	public static function savedata($id, $form)
	{
		$mainframe = JFactory::getApplication();
		$input_get = JFactory::getApplication()->input;
		$all_files=array();
		$correct=false;
		$db = JFactory::getDBO();
		@session_start();

		$captcha_input=$input_get->getString("captcha_input");
		$recaptcha_response_field=$input_get->getString("recaptcha_response_field");
		$recaptcha_response_field_new=$input_get->getString("g-recaptcha-response");
		$counter=$input_get->getString("counter".$id);
		if(isset($counter))
		{	
			if (isset($captcha_input))
			{				
				$session_wd_captcha_code=isset($_SESSION[$id.'_wd_captcha_code'])?$_SESSION[$id.'_wd_captcha_code']:'-';

				if($captcha_input==$session_wd_captcha_code)
				{
					$correct=true;
				}
				else
				{
							echo "<script> alert('".JText::_('WDF_INCORRECT_SEC_CODE')."');
						</script>";
				}
			}	
			
			else
				if(isset($recaptcha_response_field))
				{	
				$privatekey = $form->private_key;
	
					$resp = recaptcha_check_answer ($privatekey,
													$_SERVER["REMOTE_ADDR"],
													$_POST["recaptcha_challenge_field"],
													$recaptcha_response_field);
					if($resp->is_valid)
					{
						$correct=true;
	
					}
					else
					{
								echo "<script> alert('".JText::_('WDF_INCORRECT_SEC_CODE')."');
							</script>";
					}
				}	
				elseif (isset($recaptcha_response_field_new)){
					$privatekey = $form->private_key;
					$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$recaptcha_response_field_new);
					$response = json_decode($response, true);
					if($response["success"] === true)
						$correct = TRUE;
					else
						echo "<script> alert('".JText::_('WDF_INCORRECT_SEC_CODE')."');
							</script>";
				  
				}
				else	
				$correct=true;				
				
				if($correct)
				{
				
					$ip=$_SERVER['REMOTE_ADDR'];

					$db->setQuery("SELECT ip FROM #__formmaker_blocked WHERE ip LIKE '%".$ip."%'"); 
					$db->query();
					$blocked_ip = $db->loadResult();

					if($blocked_ip)		
					$mainframe->redirect($_SERVER["REQUEST_URI"], addslashes(JText::_('WDF_BLOCKED_IP')));
				
					$result_temp=modFormmaker::save_db($counter, $id);
					
					$all_files=$result_temp[0];
					if(is_numeric($all_files))		
						modFormmaker::remove($all_files);
					else
						if(isset($counter))
							modFormmaker::gen_mail($counter, $all_files,$result_temp[1], $id);
				}
			return $all_files;
		}

		return $all_files;
			
			
	}
	
	 public static function save_db($counter,$id)
	{
		$input_get = JFactory::getApplication()->input;
		$user = JFactory::getUser();
		if ($user->id != 0){
			$userid =  $user->id;
			$username =  $user->username;
			$useremail =  $user->email;
		}
		else {
			$userid = '';
			$username = '';
			$useremail = '';
		}
		$chgnac=true;	
		$all_files=array();
		$paypal=array();
		$paypal['item_name']=array();
		$paypal['quantity']=array();
		$paypal['amount']=array();
		$is_amount=false;
		$paypal['on_os']=array();
		$total=0;
		$form_currency='$';
		$currency_code=array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
		$currency_sign=array('$'  , '€'  , '£'  , '¥'  , 'C$', 'Mex$', 'HK$', 'Ft' , 'kr' , 'NZ$', 'S$' , 'kr' , 'zł' , 'A$' , 'kr' , 'CHF' , 'Kč', '₪'  , 'R$' , 'NT$', 'RM' , '₱'  , '฿'  );
			
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_formmaker'.DS.'tables');
		$form = JTable::getInstance('formmaker', 'Table');
		$form->load( $id);
		
		if($form->payment_currency)
			$form_currency=	$currency_sign[array_search($form->payment_currency, $currency_code)];
		
		$old = false;
		
		if(isset($form->form))
			$old = true;
		
		$label_id= array();
		$label_label= array();
		$label_type= array();
		
		$disabled_fields	= explode(',',$input_get->getString("disabled_fields".$id));		
		$disabled_fields 	= array_slice($disabled_fields,0, count($disabled_fields)-1);   
		
		if($old == false || ($old == true && $form->form=='')) 
		$label_all	= explode('#****#',$form->label_order_current);
		else
		$label_all	= explode('#****#',$form->label_order);
		$label_all 	= array_slice($label_all,0, count($label_all)-1);   
		
		foreach($label_all as $key => $label_each) 
		{
			$label_id_each=explode('#**id**#',$label_each);
			array_push($label_id, $label_id_each[0]);
			
			$label_order_each=explode('#**label**#', $label_id_each[1]);
			
			array_push($label_label, $label_order_each[0]);
			array_push($label_type, $label_order_each[1]);
		}
		
		$ip=$_SERVER['REMOTE_ADDR'];
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT MAX( group_id ) FROM #__formmaker_submits" ); 
		$db->query();
		$max = $db->loadResult();
		
		$fvals=array();
		
		if($old == false || ($old == true && $form->form=='')) 
		{
			$invalid_submit = false;
			if($input_get->getString("hidden_field_for_validation".$id)!='')
				$invalid_submit = true;
				
			foreach($label_type as $key => $type)
			{
				$i=$label_id[$key];
				if($type == "type_submitter_mail")
				{
					if ($input_get->getString('wdform_'.$i."_element".$id) && !filter_var($input_get->getString('wdform_'.$i."_element".$id), FILTER_VALIDATE_EMAIL))
					{
						$invalid_submit = true;
						break;
					}	
				}
			}
			
			$mainframe = JFactory::getApplication();
			if($invalid_submit)	
				$mainframe->redirect($_SERVER["REQUEST_URI"]);
			
			foreach($label_type as $key => $type)
			{

				$value='';
				if($type=="type_submit_reset" or $type=="type_map" or $type=="type_editor" or  $type=="type_captcha" or  $type=="type_recaptcha" or  $type=="type_button" or $type=="type_paypal_total" or $type=="type_send_copy")
					continue;

				$i=$label_id[$key];
				if(!in_array($i,$disabled_fields))			
				{				
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
						case "type_mark_map":				
						{
							$value=$input_get->getString('wdform_'.$i."_long".$id).'***map***'.$input_get->getString('wdform_'.$i."_lat".$id);
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
							$files = JRequest::getVar('wdform_'.$i.'_file'.$id, null, 'files', 'array');
							foreach($files['name'] as $file_key => $file_name)
								if($file_name)
								{	
								
									$untilupload = $form->form_fields;

									$untilupload = substr($untilupload, strpos($untilupload,$i.'*:*id*:*type_file_upload'), -1);
									$untilupload = substr($untilupload, 0, strpos($untilupload,'*:*new_field*:'));
									$untilupload = explode('*:*w_field_label_pos*:*',$untilupload);
									$untilupload = $untilupload[1];
									$untilupload = explode('*:*w_destination*:*',$untilupload);
									$destination = $untilupload[0];
									$untilupload = $untilupload[1];
									$untilupload = explode('*:*w_extension*:*',$untilupload);
									$extension 	 = $untilupload[0];
									$untilupload = $untilupload[1];
									$untilupload = explode('*:*w_max_size*:*',$untilupload);
									$max_size 	 = $untilupload[0];
									$untilupload = $untilupload[1];
									
									$fileName = $files['name'][$file_key];

									$fileSize = $files['size'][$file_key];

									if($fileSize > $max_size*1024)
									{
										echo "<script> alert('".JText::sprintf('WDF_FILE_SIZE_ERROR',$max_size)."');</script>";
										return array($max+1);
									}
									
									$uploadedFileNameParts = explode('.',$fileName);
									$uploadedFileExtension = array_pop($uploadedFileNameParts);
									$to=strlen($fileName)-strlen($uploadedFileExtension)-1;
									
									$fileNameFree= substr($fileName,0, $to);
									$invalidFileExts = explode(',', $extension);
									$extOk = false;

									foreach($invalidFileExts as $key => $valuee)
									{
									if(  is_numeric(strpos(strtolower($valuee), strtolower($uploadedFileExtension) )) )
										{
											$extOk = true;
										}
									}
									 
									if ($extOk == false) 
									{
										echo "<script> alert('".JText::_('WDF_FILE_TYPE_ERROR')."');</script>";
										return array($max+1);
									}
									
									$fileTemp = $files['tmp_name'][$file_key];
									$p=1;
									while(file_exists( $destination.DS.$fileName))
									{
									$to=strlen($files['name'][$file_key])-strlen($uploadedFileExtension)-1;
									$fileName= substr($fileName,0, $to).'('.$p.').'.$uploadedFileExtension;
									$p++;
									}
									
									if(!JFile::upload($fileTemp, $destination.DS.$fileName)) 
									{	
										echo "<script> alert('".JText::_('WDF_FILE_MOVING_ERROR')."');</script>";
										return array($max+1);
									}

									$value.= JURI::root(true).'/'.$destination.'/'.$fileName.'*@@url@@*';
					
									$files['tmp_name'][$file_key]=$destination.DS.$fileName;
									$temp_file=array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key]);
									array_push($all_files,$temp_file);
								}
					
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
							$value=$input_get->getString($label_label[$key]);
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
								$value=$value.'.'.( preg_replace('/\D/', '', $input_get->getString('wdform_'.$i."_element_cents".$id))    );
											
							$total+=(float)($value);
							
							$paypal_option=array();

							if($value!=0)
							{
								array_push ($paypal['item_name'], $label_label[$key]);
								array_push ($paypal['quantity'], $input_get->getString('wdform_'.$i."_element_quantity".$id,1));
								array_push ($paypal['amount'], $value);
								$is_amount=true;
								array_push ($paypal['on_os'], $paypal_option);
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
							$total+=(float)($input_get->getString('wdform_'.$i."_element".$id))*(float)($input_get->getString('wdform_'.$i."_element_quantity".$id,1));
							array_push ($paypal['item_name'],$label_label[$key].' '.$input_get->getString('wdform_'.$i."_element_label".$id));
							array_push ($paypal['quantity'], $input_get->getString('wdform_'.$i."_element_quantity".$id,1));
							array_push ($paypal['amount'], $input_get->getString('wdform_'.$i."_element".$id));
							if($input_get->getString('wdform_'.$i."_element".$id))
								$is_amount=true;
							
							$element_quantity=$input_get->getString('wdform_'.$i."_element_quantity".$id);
							if(isset($element_quantity) && $value!='')
								$value.='***br***'.$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id).'***quantity***';
							
							$paypal_option=array();
							$paypal_option['on']=array();
							$paypal_option['os']=array();

							for($k=0; $k<50; $k++)
							{
								$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
								if(isset($temp_val) && $value!='')
								{			
									array_push ($paypal_option['on'], $input_get->getString('wdform_'.$i."_element_property_label".$id.$k));
									array_push ($paypal_option['os'], $input_get->getString('wdform_'.$i."_property".$id.$k));
									$value.='***br***'.$input_get->getString('wdform_'.$i."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$i."_property".$id.$k).'***property***';
								}
							}
							array_push ($paypal['on_os'], $paypal_option);
							break;
						}
						
						case "type_paypal_radio":				
						{
							
							if($input_get->getString('wdform_'.$i."_element_label".$id))
								$value=$input_get->getString('wdform_'.$i."_element_label".$id).' : '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
							else
								$value='';

							$total+=(float)($input_get->getString('wdform_'.$i."_element".$id))*(float)($input_get->getString('wdform_'.$i."_element_quantity".$id,1));
							array_push ($paypal['item_name'], $label_label[$key].' '.$input_get->getString('wdform_'.$i."_element_label".$id));
							array_push ($paypal['quantity'], $input_get->getString('wdform_'.$i."_element_quantity".$id,1));
							array_push ($paypal['amount'], $input_get->getString('wdform_'.$i."_element".$id));
							if($input_get->getString('wdform_'.$i."_element".$id))
								$is_amount=true;
							
							
							$element_quantity=$input_get->getString('wdform_'.$i."_element_quantity".$id);
							if(isset($element_quantity) && $value!='')
								$value.='***br***'.$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id).'***quantity***';
							
						
							
							$paypal_option=array();
							$paypal_option['on']=array();
							$paypal_option['os']=array();

							for($k=0; $k<50; $k++)
							{
								$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
								if(isset($temp_val) && $value!='')
								{			
									array_push ($paypal_option['on'], $input_get->getString('wdform_'.$i."_element_property_label".$id.$k));
									array_push ($paypal_option['os'], $input_get->getString('wdform_'.$i."_property".$id.$k));
									$value.='***br***'.$input_get->getString('wdform_'.$i."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$i."_property".$id.$k).'***property***';
								}
							}
							array_push ($paypal['on_os'], $paypal_option);
							break;
						}

						case "type_paypal_shipping":				
						{
							
							if($input_get->getString('wdform_'.$i."_element_label".$id))
								$value=$input_get->getString('wdform_'.$i."_element_label".$id).' : '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
							else
								$value='';
							$value=$input_get->getString('wdform_'.$i."_element_label".$id).' - '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
							
							$paypal['shipping']=$input_get->getString('wdform_'.$i."_element".$id);

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
										$total+=(float)($input_get->getString('wdform_'.$i."_element".$id.$j))*(float)($input_get->getString('wdform_'.$i."_element_quantity".$id,1));
										array_push ($paypal['item_name'], $label_label[$key].' '.$input_get->getString('wdform_'.$i."_element".$id.$j."_label"));
										array_push ($paypal['quantity'], $input_get->getString('wdform_'.$i."_element_quantity".$id,1));
										array_push ($paypal['amount'], $input_get->getString('wdform_'.$i."_element".$id.$j)=='' ? '0' : $input_get->getString('wdform_'.$i."_element".$id.$j));
										if($input_get->getString('wdform_'.$i."_element".$id.$j))
											$is_amount=true;
										$paypal_option=array();
										$paypal_option['on']=array();
										$paypal_option['os']=array();

										for($k=0; $k<50; $k++)
										{
											$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
											if(isset($temp_val))
											{			
												array_push ($paypal_option['on'], $input_get->getString('wdform_'.$i."_element_property_label".$id.$k));
												array_push ($paypal_option['os'], $input_get->getString('wdform_'.$i."_property".$id.$k));
											}
										}
										array_push ($paypal['on_os'], $paypal_option);
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
										$value.='***br***'.$input_get->getString('wdform_'.$i."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$i."_property".$id.$k).'***property***';
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

					if($type=="type_text" or $type=="type_password" or $type=="type_textarea" or $type=="type_name" or $type=="type_submitter_mail" or $type=="type_number" or $type=="type_phone")
					{
						
						$untilupload = $form->form_fields;

						$untilupload = substr($untilupload, strpos($untilupload,$i.'*:*id*:*'.$type), -1);
						$untilupload = substr($untilupload, 0, strpos($untilupload,'*:*new_field*:'));
						$untilupload = explode('*:*w_required*:*',$untilupload);
						$untilupload = $untilupload[1];
						$untilupload = explode('*:*w_unique*:*',$untilupload);
						$unique_element = $untilupload[0];
				
						if($unique_element=='yes')
						{
							$db->setQuery("SELECT id FROM #__formmaker_submits WHERE form_id='".$db->escape((int)$id)."' and element_label='".$db->escape($i)."' and element_value='".$db->escape($value)."'");					
							$unique = $db->loadResult();
							if ($db->getErrorNum()){echo $db->stderr();	return false;}
				
							if ($unique) 
							{
								echo "<script> alert('".JText::sprintf('WDF_UNIQUE', '"'.$label_label[$key].'"')	."');</script>";		
								return array($max+1);
							}
						}
					}
					
					
					
					$fvals['{'.$i.'}']=str_replace(array("***map***", "*@@url@@*", "@@@@@@@@@", "@@@", "***grading***", "***br***"), array(" ", "", " ", " ", " ", ", "),$db->escape($value));

					if($form->savedb)
					{
					$db->setQuery("INSERT INTO #__formmaker_submits (form_id, element_label, element_value, group_id, date, ip, user_id) VALUES('".$id."', '".$i."', '".addslashes($value)."','".($max+1)."', now(), '".$ip."', '".$user->id."')" ); 
					$rows = $db->query();
					if ($db->getErrorNum()){echo $db->stderr();	return false;}
					}
					$chgnac=false;
				}
			}
			
		}
		else
		{
			foreach($label_type as $key => $type)
			{
				$value='';
				if($type=="type_submit_reset" or $type=="type_map" or $type=="type_editor" or  $type=="type_captcha" or  $type=="type_recaptcha" or  $type=="type_button" or $type=="type_paypal_total")
					continue;

				$i=$label_id[$key];
				
				if($type!="type_address")
				{
					$deleted=$input_get->getString($i."_type".$id);
					if(!isset($deleted))
						break;
				}
					
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
						$value=$input_get->getString($i."_element".$id);
						break;
					}
					
					case "type_mark_map":				
					{
						$value=$input_get->getString($i."_long".$id).'***map***'.$input_get->getString($i."_lat".$id);
						break;
					}
										
					case "type_date_fields":
					{
						$value=$input_get->getString($i."_day".$id).'-'.$input_get->getString($i."_month".$id).'-'.$input_get->getString($i."_year".$id);
						break;
					}
					
					case "type_time":
					{
						$ss=$input_get->getString($i."_ss".$id);
						if(isset($ss))
							$value=$input_get->getString($i."_hh".$id).':'.$input_get->getString($i."_mm".$id).':'.$input_get->getString($i."_ss".$id);
						else
							$value=$input_get->getString($i."_hh".$id).':'.$input_get->getString($i."_mm".$id);
								
						$am_pm=$input_get->getString($i."_am_pm".$id);
						if(isset($am_pm))
							$value=$value.' '.$input_get->getString($i."_am_pm".$id);
							
						break;
					}
					
					case "type_phone":
					{
						$value=$input_get->getString($i."_element_first".$id).' '.$input_get->getString($i."_element_last".$id);
							
						break;
					}
		
					case "type_name":
					{
						$element_title=$input_get->getString($i."_element_title".$id);
						if(isset($element_title))
							$value=$input_get->getString($i."_element_title".$id).' '.$input_get->getString($i."_element_first".$id).' '.$input_get->getString($i."_element_last".$id).' '.$input_get->getString($i."_element_middle".$id);
						else
							$value=$input_get->getString($i."_element_first".$id).' '.$input_get->getString($i."_element_last".$id);
							
						break;
					}
		
					case "type_file_upload":
					{
						$file = JRequest::getVar($i.'_file'.$id, null, 'files', 'array');
						if($file['name'])
						{	
							$untilupload = $form->form;
							
							$pos1 = strpos($untilupload, "***destinationskizb".$i."***");
							$pos2 = strpos($untilupload, "***destinationverj".$i."***");
							$destination = substr($untilupload, $pos1+(23+(strlen($i)-1)), $pos2-$pos1-(23+(strlen($i)-1)));
							$pos1 = strpos($untilupload, "***extensionskizb".$i."***");
							$pos2 = strpos($untilupload, "***extensionverj".$i."***");
							$extension = substr($untilupload, $pos1+(21+(strlen($i)-1)), $pos2-$pos1-(21+(strlen($i)-1)));
							$pos1 = strpos($untilupload, "***max_sizeskizb".$i."***");
							$pos2 = strpos($untilupload, "***max_sizeverj".$i."***");
							$max_size = substr($untilupload, $pos1+(20+(strlen($i)-1)), $pos2-$pos1-(20+(strlen($i)-1)));
							
							$fileName = $file['name'];
							/*$destination = JPATH_SITE.DS.$input_get->getString($i.'_destination');
							$extension = $input_get->getString($i.'_extension');
							$max_size = $input_get->getString($i.'_max_size');*/
						
							$fileSize = $file['size'];

							if($fileSize > $max_size*1024)
							{
								echo "<script> alert('".JText::sprintf('WDF_FILE_SIZE_ERROR',$max_size)."');</script>";
								return array($max+1);;
							}
							
							$uploadedFileNameParts = explode('.',$fileName);
							$uploadedFileExtension = array_pop($uploadedFileNameParts);
							$to=strlen($fileName)-strlen($uploadedFileExtension)-1;
							
							$fileNameFree= substr($fileName,0, $to);
							$invalidFileExts = explode(',', $extension);
							$extOk = false;

							foreach($invalidFileExts as $key => $value)
							{
							if(  is_numeric(strpos(strtolower($value), strtolower($uploadedFileExtension) )) )
								{
									$extOk = true;
								}
							}
							 
							if ($extOk == false) 
							{
								echo "<script> alert('".JText::_('WDF_FILE_TYPE_ERROR')."');</script>";
								return array($max+1);;
							}
							
							$fileTemp = $file['tmp_name'];
							$p=1;
							while(file_exists( $destination.DS.$fileName))
							{
							$to=strlen($file['name'])-strlen($uploadedFileExtension)-1;
							$fileName= substr($fileName,0, $to).'('.$p.').'.$uploadedFileExtension;
							$p++;
							}
							
							if(!JFile::upload($fileTemp, $destination.DS.$fileName)) 
							{	
								echo "<script> alert('".JText::_('WDF_FILE_MOVING_ERROR')."');</script>";
								return array($max+1);;
							}

							$value= JURI::root(true).'/'.$destination.'/'.$fileName.'*@@url@@*';
			
							$file['tmp_name']=$destination.DS.$fileName;
							array_push($all_files,$file);

						}
						break;
					}
					
					case 'type_address':
					{
						$value='*#*#*#';
						$element=$input_get->getString($i."_street1".$id);
						if(isset($element))
						{
							$value=$input_get->getString($i."_street1".$id);
							break;
						}
						
						$element=$input_get->getString($i."_street2".$id);
						if(isset($element))
						{
							$value=$input_get->getString($i."_street2".$id);
							break;
						}
						
						$element=$input_get->getString($i."_city".$id);
						if(isset($element))
						{
							$value=$input_get->getString($i."_city".$id);
							break;
						}
						
						$element=$input_get->getString($i."_state".$id);
						if(isset($element))
						{
							$value=$input_get->getString($i."_state".$id);
							break;
						}
						
						$element=$input_get->getString($i."_postal".$id);
						if(isset($element))
						{
							$value=$input_get->getString($i."_postal".$id);
							break;
						}
						
						$element=$input_get->getString($i."_country".$id);
						if(isset($element))
						{
							$value=$input_get->getString($i."_country".$id);
							break;
						}
						
						break;
					}
					
					case "type_hidden":				
					{
						$value=$input_get->getString($label_label[$key]);
						break;
					}
					
					case "type_radio":				
					{
						$element=$input_get->getString($i."_other_input".$id);
						if(isset($element))
						{
							$value=$element;	
							break;
						}
						
						$value=$input_get->getString($i."_element".$id);
						break;
					}
					
					case "type_checkbox":				
					{
						$start=-1;
						$value='';
						for($j=0; $j<100; $j++)
						{
						
							$element=$input_get->getString($i."_element".$id.$j);
			
							if(isset($element))
							{
								$start=$j;
								break;
							}
						}
							
						$other_element_id=-1;
						$is_other=$input_get->getString($i."_allow_other".$id);
						if($is_other=="yes")
						{
							$other_element_id=$input_get->getString($i."_allow_other_num".$id);
						}
						
						if($start!=-1)
						{
							for($j=$start; $j<100; $j++)
							{
								$element=$input_get->getString($i."_element".$id.$j);
								if(isset($element))
								if($j==$other_element_id)
								{
									$value=$value.$input_get->getString($i."_other_input".$id).'***br***';
								}
								else
								
									$value=$value.$input_get->getString($i."_element".$id.$j).'***br***';
							}
						}
						
						break;
					}
						case "type_paypal_price":	
					{		
						$value=0;
						if($input_get->getString($i."_element_dollars".$id))
							$value=$input_get->getString($i."_element_dollars".$id);
							
						$value = (int) preg_replace('/\D/', '', $value);
						
						if($input_get->getString($i."_element_cents".$id))
							$value=$value.'.'.( preg_replace('/\D/', '', $input_get->getString($i."_element_cents".$id))    );
										
						$total+=(float)($value);
						
						$paypal_option=array();

						if($value!=0)
						{
							array_push ($paypal['item_name'], $label_label[$key]);
							array_push ($paypal['quantity'], $input_get->getString($i."_element_quantity".$id,1));
							array_push ($paypal['amount'], $value);
							$is_amount=true;
							array_push ($paypal['on_os'], $paypal_option);
						}
						$value=$value.$form_currency;
						break;
					}			
					
					case "type_paypal_select":	
					{	
			
						$value=$input_get->getString($i."_element_label".$id).' : '.$input_get->getString($i."_element".$id).$form_currency;
						$total+=(float)($input_get->getString($i."_element".$id))*(float)($input_get->getString($i."_element_quantity".$id,1));
						array_push ($paypal['item_name'],$label_label[$key].' '.$input_get->getString($i."_element_label".$id));
						array_push ($paypal['quantity'], $input_get->getString($i."_element_quantity".$id,1));
						array_push ($paypal['amount'], $input_get->getString($i."_element".$id));
						if($input_get->getString($i."_element".$id))
							$is_amount=true;
						
						$element_quantity_label=$input_get->getString($i."_element_quantity_label".$id);
						if(isset($element_quantity_label))
							$value.='***br***'.$input_get->getString($i."_element_quantity_label".$id).': '.$input_get->getString($i."_element_quantity".$id);
						
						$paypal_option=array();
						$paypal_option['on']=array();
						$paypal_option['os']=array();

						for($k=0; $k<50; $k++)
						{
							$temp_val=$input_get->getString($i."_element_property_value".$id.$k);
							if(isset($temp_val))
							{			
								array_push ($paypal_option['on'], $input_get->getString($i."_element_property_label".$id.$k));
								array_push ($paypal_option['os'], $input_get->getString($i."_element_property_value".$id.$k));
								$value.='***br***'.$input_get->getString($i."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k);
							}
						}
						array_push ($paypal['on_os'], $paypal_option);
						break;
					}
					
					case "type_paypal_radio":				
					{
						
						$value=$input_get->getString($i."_element_label".$id).' - '.$input_get->getString($i."_element".$id).$form_currency;
						$total+=(float)($input_get->getString($i."_element".$id))*(float)($input_get->getString($i."_element_quantity".$id,1));
						array_push ($paypal['item_name'], $label_label[$key].' '.$input_get->getString($i."_element_label".$id));
						array_push ($paypal['quantity'], $input_get->getString($i."_element_quantity".$id,1));
						array_push ($paypal['amount'], $input_get->getString($i."_element".$id));
						
						if($input_get->getString($i."_element".$id))
							$is_amount=true;
						
						$element_quantity_label=$input_get->getString($i."_element_quantity_label".$id);
						if(isset($element_quantity_label))
							$value.='***br***'.$input_get->getString($i."_element_quantity_label".$id).': '.$input_get->getString($i."_element_quantity".$id);
						
						
						
						
						
						$paypal_option=array();
						$paypal_option['on']=array();
						$paypal_option['os']=array();

						for($k=0; $k<50; $k++)
						{
							$temp_val=$input_get->getString($i."_element_property_value".$id.$k);
							if(isset($temp_val))
							{			
								array_push ($paypal_option['on'], $input_get->getString($i."_element_property_label".$id.$k));
								array_push ($paypal_option['os'], $input_get->getString($i."_element_property_value".$id.$k));
								$value.='***br***'.$input_get->getString($i."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k);
							}
						}
						array_push ($paypal['on_os'], $paypal_option);
						break;
					}

					case "type_paypal_shipping":				
					{
						
						$value=$input_get->getString($i."_element_label".$id).' - '.$input_get->getString($i."_element".$id).$form_currency;
						
						$paypal['shipping']=$input_get->getString($i."_element".$id);

						break;
					}

					case "type_paypal_checkbox":				
					{
						$start=-1;
						$value='';
						for($j=0; $j<100; $j++)
						{
						
							$element=$input_get->getString($i."_element".$id.$j);
			
							if(isset($element))
							{
								$start=$j;
								break;
							}
						}
						
						$other_element_id=-1;
						$is_other=$input_get->getString($i."_allow_other".$id);
						if($is_other=="yes")
						{
							$other_element_id=$input_get->getString($i."_allow_other_num".$id);
						}
						
						if($start!=-1)
						{
							for($j=$start; $j<100; $j++)
							{
								$element=$input_get->getString($i."_element".$id.$j);
								if(isset($element))
								if($j==$other_element_id)
								{
									$value=$value.$input_get->getString($i."_other_input".$id).'***br***';
									
								}
								else
								{
							
									$value=$value.$input_get->getString($i."_element".$id.$j."_label").' - '.($input_get->getString($i."_element".$id.$j)=='' ? '0' : $input_get->getString($i."_element".$id.$j)).$form_currency.'***br***';
									$total+=(float)($input_get->getString($i."_element".$id.$j))*(float)($input_get->getString($i."_element_quantity".$id,1));
									array_push ($paypal['item_name'], $label_label[$key].' '.$input_get->getString($i."_element".$id.$j."_label"));
									array_push ($paypal['quantity'], $input_get->getString($i."_element_quantity".$id,1));
									array_push ($paypal['amount'], $input_get->getString($i."_element".$id.$j)=='' ? '0' : $input_get->getString($i."_element".$id.$j));
									if($input_get->getString($i."_element".$id.$j))
										$is_amount=true;
									$paypal_option=array();
									$paypal_option['on']=array();
									$paypal_option['os']=array();

									for($k=0; $k<50; $k++)
									{
										$temp_val=$input_get->getString($i."_element_property_value".$id.$k);
										if(isset($temp_val))
										{			
											array_push ($paypal_option['on'], $input_get->getString($i."_element_property_label".$id.$k));
											array_push ($paypal_option['os'], $input_get->getString($i."_element_property_value".$id.$k));
										}
									}
									array_push ($paypal['on_os'], $paypal_option);
								}
							}
							
							$element_quantity_label=$input_get->getString($i."_element_quantity_label".$id);
							if(isset($element_quantity_label))
								$value.=$input_get->getString($i."_element_quantity_label".$id).': '.$input_get->getString($i."_element_quantity".$id).'***br***';
							
							for($k=0; $k<50; $k++)
							{
								$temp_val=$input_get->getString($i."_element_property_value".$id.$k);
								if(isset($temp_val))
								{			
									$value.=$input_get->getString($i."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k).'***br***';
								}
							}
							
						}
						
						
						break;
					}
					
					case "type_star_rating":				
					{
					
						if($input_get->getString($i."_selected_star_amount".$id)=="")
						$selected_star_amount=0;
						else
						$selected_star_amount=$input_get->getString($i."_selected_star_amount".$id);
						
						$value=$input_get->getString($i."_star_amount".$id).'***'.$selected_star_amount.'***'.$input_get->getString($i."_star_color".$id).'***star_rating***';									
						break;
					}
					

						case "type_scale_rating":				
					{
																	
						$value=$input_get->getString($i."_scale_radio".$id,0).'/'.$input_get->getString($i."_scale_amount".$id);									
						break;
					}
					
						case "type_spinner":				
					{
						$value=$input_get->getString($i."_element".$id);		
					
						break;
					}
					
						case "type_slider":				
					{
						$value=$input_get->getString($i."_slider_value".$id);		
					
						break;
					}
					case "type_range":				
					{
						$value = $input_get->getString($i."_element".$id.'0').'-'.$input_get->getString($i."_element".$id.'1');	
					
						break;
					}
					case "type_grading":				
					{
						$value ="";
						$items = explode(":",$input_get->getString($i."_hidden_item".$id));
						for($k=0; $k<sizeof($items)-1; $k++)
						$value .= $input_get->getString($i."_element".$id.$k).':';
						$value .= $input_get->getString($i."_hidden_item".$id).'***grading***';
				
						break;
					}
					
					
					case "type_matrix":				
					{
						$rows_of_matrix=explode("***",$input_get->getString($i."_hidden_row".$id));
						$rows_count= sizeof($rows_of_matrix)-1;
						$column_of_matrix=explode("***",$input_get->getString($i."_hidden_column".$id));
						$columns_count= sizeof($column_of_matrix)-1;
						$row_ids=explode(",",substr($input_get->getString($i."_row_ids".$id), 0, -1));
						$column_ids=explode(",",substr($input_get->getString($i."_column_ids".$id), 0, -1));
						
					
						if($input_get->getString($i."_input_type".$id)=="radio")
							{
							$input_value="";
							foreach($row_ids as $row_id)
							$input_value.=$input_get->getString($i."_input_element".$id.$row_id,0)."***";
							
							}
						if($input_get->getString($i."_input_type".$id)=="checkbox")
							{
							
							$input_value="";
							foreach($row_ids as $row_id)
							foreach($column_ids as $column_id)
							$input_value.=$input_get->getString($i."_input_element".$id.$row_id.'_'.$column_id,0)."***";
							
						
							}
						
						if($input_get->getString($i."_input_type".$id)=="text")
							{
							$input_value="";
							foreach($row_ids as $row_id)
							foreach($column_ids as $column_id)
							$input_value.=$input_get->getString($i."_input_element".$id.$row_id.'_'.$column_id)."***";
							}
						
						if($input_get->getString($i."_input_type".$id)=="select")
							{
							$input_value="";
							foreach($row_ids as $row_id)
							foreach($column_ids as $column_id)
							$input_value.=$input_get->getString($i."_select_yes_no".$id.$row_id.'_'.$column_id)."***";	
							}
						
						
						$value=$rows_count.'***'.$input_get->getString($i."_hidden_row".$id).$columns_count.'***'.$input_get->getString($i."_hidden_column".$id).$input_get->getString($i."_input_type".$id).'***'.$input_value.'***matrix***';	
					
						break;
					}
			
				}
		
				if($type=="type_address")
					if(	$value=='*#*#*#')
						continue;

				$unique_element=$input_get->getString($i."_unique".$id);
				if($unique_element=='yes')
				{
					$db->setQuery("SELECT id FROM #__formmaker_submits WHERE form_id='".$db->escape((int)$id)."' and element_label='".$db->escape($i)."' and element_value='".$db->escape(addslashes($value))."'");					
					$unique = $db->loadResult();
					if ($db->getErrorNum()){echo $db->stderr();	return false;}
		
					if ($unique) 
					{
						echo "<script> alert('".JText::sprintf('WDF_UNIQUE', '"'.$label_label[$key].'"')	."');</script>";		
						return array($max+1);;
					}
				}

				$ip=$_SERVER['REMOTE_ADDR'];
				
				$db->setQuery("INSERT INTO #__formmaker_submits (form_id, element_label, element_value, group_id, date, ip, user_id) VALUES('".$id."', '".$i."', '".addslashes($value)."','".($max+1)."', now(), '".$ip."', '".$user->id."')" ); 
				$rows = $db->query();
				if ($db->getErrorNum()){echo $db->stderr();	return false;}
				$chgnac=false;
			}
		}
		$db->setQuery("SELECT MAX( group_id ) FROM #__formmaker_submits"); 
		$subid = $db->loadResult();
		
		$user_fields = array("subid"=>$subid, "ip"=>$ip, "userid"=>$userid, "username"=>$username, "useremail"=>$useremail);
		$db->setQuery("SELECT * FROM #__formmaker_query WHERE form_id=".$id ); 
		$queries = $db->loadObjectList();
		if($queries)
		{
			foreach($queries as $query)
			{
				$db		= JFactory::getDBO();
				$temp		= explode('***wdfcon_typewdf***',$query->details);
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

				}
				
				$query=str_replace(array_keys($fvals), $fvals ,$query->query);
				foreach($user_fields as $user_key=>$user_field)
					$query=str_replace('{'.$user_key.'}', $user_field , $query);
				$db->setQuery($query);
				$db->query();
				

			}
		}
			
		$db		= JFactory::getDBO();
		$str='';	


			

		if($form->paypal_mode)	

		if($paypal['item_name'])	

		if($is_amount)	

		{

						

			$tax=$form->tax;

			$currency=$form->payment_currency;

			$business=$form->paypal_email;

			

			

			$ip=$_SERVER['REMOTE_ADDR'];

			

			$total2=round($total, 2);



			$db->setQuery("INSERT INTO #__formmaker_submits (form_id, element_label, element_value, group_id, date, ip, user_id) VALUES('".$id."', 'item_total', '".$total2.$form_currency."','".($max+1)."', now(), '".$ip."', '".$user->id."')" );

			$rows = $db->query();

			

			

			

			$total=$total+($total*$tax)/100;

			

			if(isset($paypal['shipping']))

			{

				$total=$total+$paypal['shipping'];

			}



			$total=round($total, 2);

			

			if ($db->getErrorNum()){echo $db->stderr();	return false;}

		

			$db->setQuery("INSERT INTO #__formmaker_submits (form_id, element_label, element_value, group_id, date, ip, user_id) VALUES('".$id."', 'total', '".$total.$form_currency."','".($max+1)."', now(), '".$ip."', '".$user->id."')" );

			$rows = $db->query();

			

			if ($db->getErrorNum()){echo $db->stderr();	return false;}

		

			

			$db->setQuery("INSERT INTO #__formmaker_submits (form_id, element_label, element_value, group_id, date, ip, user_id) VALUES('".$id."', '0', 'In progress','".($max+1)."', now(), '".$ip."', '".$user->id."')" );

			$rows = $db->query();

			

			if ($db->getErrorNum()){echo $db->stderr();	return false;}

		

			

			

			$str='';

			

			if($form->checkout_mode=="production")

				$str.="https://www.paypal.com/cgi-bin/webscr?";

			else		

				$str.="https://www.sandbox.paypal.com/cgi-bin/webscr?";

				
			$str.="charset=utf-8";
			$str.="&currency_code=".$currency;

			$str.="&business=".$business;

			$str.="&cmd="."_cart";
			$str.="&charset=utf8";

			$str.="&notify_url=".JUri::root().'index.php?option=com_formmaker%26view=checkpaypal%26form_id='.$id.'%26group_id='.($max+1);

			$str.="&upload="."1";



			if(isset($paypal['shipping']))

			{

					$str=$str."&shipping_1=".$paypal['shipping'];

				//	$str=$str."&weight_cart=".$paypal['shipping'];

				//	$str=$str."&shipping2=3".$paypal['shipping'];

					$str=$str."&no_shipping=2";

			}



			

			$i=0;
			foreach($paypal['item_name'] as $pkey => $pitem_name)
			if($paypal['amount'][$pkey])
			{
				$i++;
				$str=$str."&item_name_".$i."=".$pitem_name;
				$str=$str."&amount_".$i."=".$paypal['amount'][$pkey];
				$str=$str."&quantity_".$i."=".$paypal['quantity'][$pkey];
				
				if($tax)
					$str=$str."&tax_rate_".$i."=".$tax;
					
				if($paypal['on_os'][$pkey])
				{
					foreach($paypal['on_os'][$pkey]['on'] as $on_os_key => $on_item_name)
					{
							
						$str=$str."&on".$on_os_key."_".$i."=".$on_item_name;
						$str=$str."&os".$on_os_key."_".$i."=".$paypal['on_os'][$pkey]['os'][$on_os_key];
					}
				}
			
			}
		}
		
		if($chgnac)
		{		$mainframe = JFactory::getApplication();
	
				if(count($all_files)==0)
					$mainframe->redirect($_SERVER["REQUEST_URI"], addslashes(JText::_('WDF_EMPTY_SUBMIT')));
		}
		
		return array($all_files, $str);
	}
	
	 public static function gen_mail($counter, $all_files, $str, $id)
	{
		$input_get = JFactory::getApplication()->input;
		@session_start();
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$Itemid=$input_get->getString('Itemid'.$id);
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_formmaker'.DS.'tables');
		$row = JTable::getInstance('formmaker', 'Table');
		$row->load( $id);
		$ip=$_SERVER['REMOTE_ADDR'];
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT MAX( group_id ) FROM #__formmaker_submits" ); 
		$db->query();
		$subid = $db->loadResult();
		
		if ($user->id != 0)
		{
			$username =  $user->username;
			$useremail =  $user->email;
		}
		else
		{
			$username = '';
			$useremail = '';
		}
		
		$total=0;
		$form_currency='$';
		$currency_code=array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
		$currency_sign=array('$'  , '€'  , '£'  , '¥'  , 'C$', 'Mex$', 'HK$', 'Ft' , 'kr' , 'NZ$', 'S$' , 'kr' , 'zł' , 'A$' , 'kr' , 'CHF' , 'Kč', '₪'  , 'R$' , 'NT$', 'RM' , '₱'  , '฿'  );
		
		$custom_fields = array('ip', 'useremail', 'username', 'subid', 'all' );
		
		if($row->payment_currency)
			$form_currency=	$currency_sign[array_search($row->payment_currency, $currency_code)];
		
		$old = false;
		
		if(isset($row->form) )
			$old = true;
		
			$cc=array();
			$label_order_original= array();
			$label_order_ids= array();
			$label_type= array();
			
			
			
			if($old == false || ($old == true && $row->form=='')) 
			$label_all	= explode('#****#',$row->label_order_current);
			else
			$label_all	= explode('#****#',$row->label_order);
			$label_all 	= array_slice($label_all,0, count($label_all)-1);   
			foreach($label_all as $key => $label_each) 
			{
				$label_id_each=explode('#**id**#',$label_each);
				$label_id=$label_id_each[0];
				array_push($label_order_ids,$label_id);
				
				$label_oder_each=explode('#**label**#', $label_id_each[1]);							
				$label_order_original[$label_id]=$label_oder_each[0];
				$label_type[$label_id]=$label_oder_each[1];
			}
		
			$disabled_fields	= explode(',',$input_get->getString("disabled_fields".$id));		
			$disabled_fields 	= array_slice($disabled_fields,0, count($disabled_fields)-1);   
		
			$list='<table border="1" cellpadding="3" cellspacing="0" style="width:600px;">';
			$list_text_mode = '';
			
		if($old == false || ($old == true && $row->form=='')) 
		{

			foreach($label_order_ids as $key => $label_order_id)
			{
				$i=$label_order_id;
				$type=$label_type[$i];

				if($type!="type_map" and  $type!="type_submit_reset" and  $type!="type_editor" and  $type!="type_captcha" and  $type!="type_recaptcha" and  $type!="type_button")
				{	
					$element_label=$label_order_original[$i];
					if(!in_array($i,$disabled_fields))					
					switch ($type)
						{
							case 'type_text':
							case 'type_password':
							case 'type_textarea':
							case "type_date":
							case "type_own_select":					
							case "type_country":				
							case "type_number":	
							{
								$element=$input_get->getString('wdform_'.$i."_element".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';	
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";				
								}
								break;
							
							
							}
							case "type_wdeditor":				
							{

								$element = $input_get->getString('wdform_'.$i.'_wd_editor'.$id, '', 'post', 'string', JREQUEST_ALLOWRAW );
								
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';	
								
								$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";						

								break;

							}
							case "type_hidden":				
							{
								$element=$input_get->getString($element_label);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';	

									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";									
								}
								break;
							}
							
							
							case "type_mark_map":
							{
								$element=$input_get->getString('wdform_'.$i."_long".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >Longitude:'.$input_get->getString('wdform_'.$i."_long".$id).'<br/>Latitude:'.$input_get->getString('wdform_'.$i."_lat".$id).'</td></tr>';
									
									$list_text_mode=$list_text_mode.$element_label.' - Longitude:'.$input_get->getString('wdform_'.$i."_long".$id).' Latitude:'.$input_get->getString('wdform_'.$i."_lat".$id)."\r\n";
								}
								break;		
							}
												
							case "type_submitter_mail":
							{
								$element=$input_get->getString('wdform_'.$i."_element".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';	
									
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";									
								}
								break;		
							}
							
							case "type_time":
							{
								
								$hh=$input_get->getString('wdform_'.$i."_hh".$id);
								if(isset($hh))
								{
									$ss=$input_get->getString('wdform_'.$i."_ss".$id);
									if(isset($ss))
									{
										$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString('wdform_'.$i."_hh".$id).':'.$input_get->getString('wdform_'.$i."_mm".$id).':'.$input_get->getString('wdform_'.$i."_ss".$id);
										$list_text_mode=$list_text_mode.$element_label.' - '.$input_get->getString('wdform_'.$i."_hh".$id).':'.$input_get->getString('wdform_'.$i."_mm".$id).':'.$input_get->getString('wdform_'.$i."_ss".$id);
									}	
									else
									{
										$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString('wdform_'.$i."_hh".$id).':'.$input_get->getString('wdform_'.$i."_mm".$id);
										$list_text_mode=$list_text_mode.$element_label.' - '.$input_get->getString('wdform_'.$i."_hh".$id).':'.$input_get->getString('wdform_'.$i."_mm".$id);
									}	
									$am_pm=$input_get->getString('wdform_'.$i."_am_pm".$id);
									if(isset($am_pm))
									{
										$list=$list.' '.$input_get->getString('wdform_'.$i."_am_pm".$id).'</td></tr>';
										$list_text_mode=$list_text_mode.$input_get->getString('wdform_'.$i."_am_pm".$id)."\r\n";
									}	
									else
									{
										$list=$list.'</td></tr>';
										$list_text_mode=$list_text_mode."\r\n";
									}	
								}
									
								break;
							}
							
							case "type_phone":
							{
								$element_first=$input_get->getString('wdform_'.$i."_element_first".$id);
								if(isset($element_first))
								{
										$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString('wdform_'.$i."_element_first".$id).' '.$input_get->getString('wdform_'.$i."_element_last".$id).'</td></tr>';
										$list_text_mode=$list_text_mode.$element_label.' - '.$input_get->getString('wdform_'.$i."_element_first".$id).' '.$input_get->getString('wdform_'.$i."_element_last".$id)."\r\n";
								}	
								break;
							}
							
							case "type_name":
							{
								$element_first=$input_get->getString('wdform_'.$i."_element_first".$id);
								if(isset($element_first))
								{
									$element_title=$input_get->getString('wdform_'.$i."_element_title".$id);
									if(isset($element_title))
									{
										$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString('wdform_'.$i."_element_title".$id).' '.$input_get->getString('wdform_'.$i."_element_first".$id).' '.$input_get->getString('wdform_'.$i."_element_last".$id).' '.$input_get->getString('wdform_'.$i."_element_middle".$id).'</td></tr>';
										$list_text_mode=$list_text_mode.$element_label.' - '.$input_get->getString('wdform_'.$i."_element_title".$id).' '.$input_get->getString('wdform_'.$i."_element_first".$id).' '.$input_get->getString('wdform_'.$i."_element_last".$id).' '.$input_get->getString('wdform_'.$i."_element_middle".$id)."\r\n";
									}	
									else
									{
										$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString('wdform_'.$i."_element_first".$id).' '.$input_get->getString('wdform_'.$i."_element_last".$id).'</td></tr>';
										$list_text_mode=$list_text_mode.$element_label.' - '.$input_get->getString('wdform_'.$i."_element_first".$id).' '.$input_get->getString('wdform_'.$i."_element_last".$id)."\r\n";
									}	
								}	   
								break;		
							}
							
							case "type_address":
							{
								$element=$input_get->getString('wdform_'.$i."_street1".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString('wdform_'.$i."_street1".$id).'</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$input_get->getString('wdform_'.$i."_street1".$id)."\r\n";
									break;
								}
								
								$element=$input_get->getString('wdform_'.$i."_street2".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString('wdform_'.$i."_street2".$id).'</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$input_get->getString('wdform_'.$i."_street2".$id)."\r\n";
									break;
								}
								
								$element=$input_get->getString('wdform_'.$i."_city".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString('wdform_'.$i."_city".$id).'</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$input_get->getString('wdform_'.$i."_city".$id)."\r\n";
									break;
								}
								
								$element=$input_get->getString('wdform_'.$i."_state".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString('wdform_'.$i."_state".$id).'</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$input_get->getString('wdform_'.$i."_state".$id)."\r\n";
									break;
								}
								
								$element=$input_get->getString('wdform_'.$i."_postal".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString('wdform_'.$i."_postal".$id).'</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$input_get->getString('wdform_'.$i."_postal".$id)."\r\n";
									break;
								}
								
								$element=$input_get->getString('wdform_'.$i."_country".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString('wdform_'.$i."_country".$id).'</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$input_get->getString('wdform_'.$i."_country".$id)."\r\n";
									break;
								}
								
								break;
								
							}


							case "type_date_fields":
							{
								$day=$input_get->getString('wdform_'.$i."_day".$id);
								if(isset($day))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.((JRequest::getVar('wdform_'.$i."_day".$id) || JRequest::getVar('wdform_'.$i."_month".$id) || JRequest::getVar('wdform_'.$i."_year".$id)) ? JRequest::getVar('wdform_'.$i."_day".$id).'-'.JRequest::getVar('wdform_'.$i."_month".$id).'-'.JRequest::getVar('wdform_'.$i."_year".$id) : '').'</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.((JRequest::getVar('wdform_'.$i."_day".$id) || JRequest::getVar('wdform_'.$i."_month".$id) || JRequest::getVar('wdform_'.$i."_year".$id)) ? JRequest::getVar('wdform_'.$i."_day".$id).'-'.JRequest::getVar('wdform_'.$i."_month".$id).'-'.JRequest::getVar('wdform_'.$i."_year".$id) : '')."\r\n";
								}
								break;
							}
							
							case "type_radio":
							{
								$element=$input_get->getString('wdform_'.$i."_other_input".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString('wdform_'.$i."_other_input".$id).'</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$input_get->getString('wdform_'.$i."_other_input".$id)."\r\n";
									break;
								}	
								
								$element=$input_get->getString('wdform_'.$i."_element".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';	$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";				
								}
								break;	
							}
							
							case "type_checkbox":
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >';
								$list_text_mode=$list_text_mode.$element_label.' - ';	
								$start=-1;
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
											$list=$list.$input_get->getString('wdform_'.$i."_other_input".$id).'<br>';
											$list_text_mode=$list_text_mode.$input_get->getString('wdform_'.$i."_other_input".$id).', ';	
										}
										else
										{
											$list=$list.$input_get->getString('wdform_'.$i."_element".$id.$j).'<br>';
											$list_text_mode=$list_text_mode.$input_get->getString('wdform_'.$i."_element".$id.$j).', ';
										}	
									}
									
									$list=$list.'</td></tr>';
									$list_text_mode=$list_text_mode."\r\n";
								}
											
								
								break;
							}
							case "type_paypal_price":	
							{		
								$value=0;
								if($input_get->getString('wdform_'.$i."_element_dollars".$id))
									$value=$input_get->getString('wdform_'.$i."_element_dollars".$id);
								
								if($input_get->getString('wdform_'.$i."_element_cents".$id))
									$value=$value.'.'.$input_get->getString('wdform_'.$i."_element_cents".$id);
							
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$value.$form_currency.'</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$value.$form_currency."\r\n";		
								break;

							}			
					
							case "type_paypal_select":	
							{	
								if($input_get->getString('wdform_'.$i."_element_label".$id))
									$value=$input_get->getString('wdform_'.$i."_element_label".$id).' : '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
								else
									$value='';
								$element_quantity_label=$input_get->getString('wdform_'.$i."_element_quantity_label".$id);
									if(isset($element_quantity_label))
										$value.='<br/>'.$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id);
						
								for($k=0; $k<50; $k++)
								{
									$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
									if(isset($temp_val))
									{			
										$value.='<br/>'.$input_get->getString('wdform_'.$i."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$i."_property".$id.$k);
									}
								}
								
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$value.'</pre></td></tr>';$list_text_mode=$list_text_mode.$element_label.' - '.str_replace('<br/>',', ',$value)."\r\n";					
								break;
							}
					
							case "type_paypal_radio":				
							{
										
								if($input_get->getString('wdform_'.$i."_element_label".$id))
									$value=$input_get->getString('wdform_'.$i."_element_label".$id).' : '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
								else
									$value='';
								$element_quantity_label=$input_get->getString('wdform_'.$i."_element_quantity_label".$id);
									if(isset($element_quantity_label))
										$value.='<br/>'.$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id);
						
								for($k=0; $k<50; $k++)
								{
									$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
									if(isset($temp_val))
									{			
										$value.='<br/>'.$input_get->getString('wdform_'.$i."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$i."_property".$id.$k);
									}
								}
								
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$value.'</pre></td></tr>';	$list_text_mode=$list_text_mode.$element_label.' - '.str_replace('<br/>',', ',$value)."\r\n";			
								
								break;	
							}

							case "type_paypal_shipping":				
							{
								
								if($input_get->getString('wdform_'.$i."_element_label".$id))
									$value=$input_get->getString('wdform_'.$i."_element_label".$id).' : '.$input_get->getString('wdform_'.$i."_element".$id).$form_currency;
								else
									$value='';
								
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$value.'</pre></td></tr>';	$list_text_mode=$list_text_mode.$element_label.' - '.$value."\r\n";						

								break;
							}

							case "type_paypal_checkbox":				
							{
							
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >';
								$list_text_mode=$list_text_mode.$element_label.' - ';	
								$start=-1;
								for($j=0; $j<100; $j++)
								{
									$element=$input_get->getString('wdform_'.$i."_element".$id.$j);
									if(isset($element))
									{
										$start=$j;
										break;
									}
								}	
								
								if($start!=-1)
								{
									for($j=$start; $j<100; $j++)
									{
										
										$element=$input_get->getString('wdform_'.$i."_element".$id.$j);
										if(isset($element))
										{
											$list=$list.$input_get->getString('wdform_'.$i."_element".$id.$j."_label").' - '.($input_get->getString('wdform_'.$i."_element".$id.$j)=='' ? '0'.$form_currency : $input_get->getString('wdform_'.$i."_element".$id.$j)).$form_currency.'<br>';
											$list_text_mode=$list_text_mode.$input_get->getString('wdform_'.$i."_element".$id.$j."_label").' - '.($input_get->getString('wdform_'.$i."_element".$id.$j)=='' ? '0'.$form_currency : $input_get->getString('wdform_'.$i."_element".$id.$j)).$form_currency.', ';	
										}
									}
									
									
								}
								$element_quantity_label=$input_get->getString('wdform_'.$i."_element_quantity_label".$id);
									if(isset($element_quantity_label))
									{
										$list=$list.'<br/>'.$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id);
										$list_text_mode=$list_text_mode.$input_get->getString('wdform_'.$i."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$i."_element_quantity".$id).', ';		
									}	
						
								for($k=0; $k<50; $k++)
								{
									$temp_val=$input_get->getString('wdform_'.$i."_property".$id.$k);
									if(isset($temp_val))
									{			
										$list=$list.'<br/>'.$input_get->getString('wdform_'.$i."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$i."_property".$id.$k);
										$list_text_mode=$list_text_mode.$input_get->getString('wdform_'.$i."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$i."_property".$id.$k).', ';	
									}
								}
															
								$list=$list.'</td></tr>';
								$list_text_mode=$list_text_mode."\r\n";		
								break;
							}
							
							case "type_paypal_total":				
							{
							
							
								$element=$input_get->getString('wdform_'.$i."_paypal_total".$id);

								
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';				
								$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";		


								break;

							}


							case "type_star_rating":
							{
								$element=$input_get->getString('wdform_'.$i."_star_amount".$id);
								$selected=$input_get->getString('wdform_'.$i."_selected_star_amount".$id,0);
								
								
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$selected.'/'.$element.'</pre></td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$selected.'/'.$element."\r\n";									
								}
								break;
							}
							

							case "type_scale_rating":
							{
							$element=$input_get->getString('wdform_'.$i."_scale_amount".$id);
							$selected=$input_get->getString('wdform_'.$i."_scale_radio".$id,0);
							
								
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$selected.'/'.$element.'</pre></td></tr>';	
									$list_text_mode=$list_text_mode.$element_label.' - '.$selected.'/'.$element."\r\n";									
								}
								break;
							}
							
							case "type_spinner":
							{

								$element=$input_get->getString('wdform_'.$i."_element".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";														
								}
								break;
							}
							
							case "type_slider":
							{

								$element=$input_get->getString('wdform_'.$i."_slider_value".$id);
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";					
								}
								break;
							}
							case "type_range":
							{

								$element0=$input_get->getString('wdform_'.$i."_element".$id.'0');
								$element1=$input_get->getString('wdform_'.$i."_element".$id.'1');
								if(isset($element0) || isset($element1))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">From:'.$element0.'<span style="margin-left:6px">To</span>:'.$element1.'</pre></td></tr>';	
									$list_text_mode=$list_text_mode.$element_label.' - From:'.$element0.' To:'.$element1."\r\n";										
								}
								break;
							}
							
							case "type_grading":
							{
								$element=$input_get->getString('wdform_'.$i."_hidden_item".$id);
								$grading = explode(":",$element);
								$items_count = sizeof($grading)-1;
								
								$element = "";
								$total = "";
								
								for($k=0;$k<$items_count;$k++)

								{
									$element .= $grading[$k].":".$input_get->getString('wdform_'.$i."_element".$id.'_'.$k)." ";
									$total += $input_get->getString('wdform_'.$i."_element".$id.'_'.$k);
								}

								$element .="Total:".$total;

																					
								if(isset($element))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';	$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";					
								}
								break;
							}
							
							case "type_matrix":
							{
							
								
								$input_type=$input_get->getString('wdform_'.$i."_input_type".$id); 
						
								$mat_rows=explode("***",$input_get->getString('wdform_'.$i."_hidden_row".$id));
								$rows_count= sizeof($mat_rows)-1;
								$mat_columns=explode("***",$input_get->getString('wdform_'.$i."_hidden_column".$id));
								$columns_count= sizeof($mat_columns)-1;
								
						
											
								$matrix="<table>";
											
									$matrix .='<tr><td></td>';
								
								for( $k=1;$k< count($mat_columns) ;$k++)
									$matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
									$matrix .='</tr>';
								
								$aaa=Array();
								
								for($k=1; $k<=$rows_count; $k++)
								{
								$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
								
									if($input_type=="radio")
									{
								
								
										$mat_radio = $input_get->getString('wdform_'.$i."_input_element".$id.$k,0);											
										if($mat_radio==0)
										{
											$checked="";
											$aaa[1]="";
										}
										else
										$aaa=explode("_",$mat_radio);
										
										
										for($j=1; $j<=$columns_count; $j++)
										{
											if($aaa[1]==$j)
											$checked="checked";
											else
											$checked="";
											
											$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
										
										}
								
									} 
									else
									{
										if($input_type=="checkbox")
										{                
											for($j=1; $j<=$columns_count; $j++)
											{
												$checked = $input_get->getString('wdform_'.$i."_input_element".$id.$k.'_'.$j);                     
												if($checked==1)						
												$checked = "checked";				
												else								
												$checked = "";

												$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
										
											}
									
										}
										else
										{
											if($input_type=="text")
											{
																	  
												for($j=1; $j<=$columns_count; $j++)
												{
													$checked = $input_get->getString('wdform_'.$i."_input_element".$id.$k.'_'.$j);
													$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
									
												}
											
											}
											else{
												for($j=1; $j<=$columns_count; $j++)
												{
													$checked = $input_get->getString('wdform_'.$i."_select_yes_no".$id.$k.'_'.$j);
													$matrix .='<td style="text-align:center">'.$checked.'</td>';
												
								
											
												}
											}
										
										}
										
									}
									$matrix .='</tr>';
								
								}
								 $matrix .='</table>';

			
			
			
																				
								if(isset($matrix))
								{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$matrix.'</pre></td></tr>';					
								}
							
								break;
							}
						
							
							default: break;
						}
				
				}	
				
			}
			
			$list=$list.'</table>';


			$config = JFactory::getConfig();
			if($row->mail_from)
				$site_mailfrom = $row->mail_from;
			else
				$site_mailfrom=$config->get( 'mailfrom' );								

			if($row->mail_from_name)
				$site_fromname = $row->mail_from_name;
			else
				$site_fromname=$config->get( 'fromname' );	
				
			
			if($row->sendemail)
			if($row->send_to)
			{
				$recipient='';
				$cca = $row->mail_cc_user;
				$bcc = $row->mail_bcc_user;
				$send_tos=explode('**',$row->send_to);
				if($row->mail_from_user)
					$from = $row->mail_from_user;
				else
					$from=$config->get( 'mailfrom' );								

				if($row->mail_from_name_user)
					$fromname = $row->mail_from_name_user;
				else
					$fromname=$config->get( 'fromname' );								

				if($row->mail_subject_user)	
					$subject 	= $row->mail_subject_user;
				else
					$subject 	= $row->title;
					
				if($row->reply_to_user)
					$replyto	= $row->reply_to_user;
				
				if($row->mail_attachment_user)
					for($k=0;$k<count($all_files);$k++)
					{
						if(isset($all_files[$k]['tmp_name'][$k]))
						$attachment_user[]=array($all_files[$k]['tmp_name'], $all_files[$k]['name'], $all_files[$k]['name']);
					}
				else
					$attachment_user[] = array(); 	
				
				if($row->mail_mode_user)
				{
					$mode = 1; 
					$list_user = wordwrap($list, 70, "\n", true);
					$new_script = $row->script_mail_user;
				}	
				else
				{
					$mode = 0; 
					$list_user = wordwrap($list_text_mode, 1000, "\n", true);
					$new_script = str_replace(array('<p>','</p>'),'',$row->script_mail_user);
				}	
				
				
				foreach($label_order_original as $key => $label_each) 
				{	
					$type=$label_type[$key];
						if(strpos($row->script_mail_user, "%".$label_each."%"))	
						{	
							$new_value = modFormmaker::custom_fields_mail($type, $key, $id, $attachment_user);				
							$new_script = str_replace("%".$label_each."%", $new_value, $new_script);							
						}
					
						if(strpos($fromname, "%".$label_each."%")>-1)	
						{	
							$new_value = str_replace('<br>',', ',modFormmaker::custom_fields_mail($type, $key, $id,''));		
							if(substr($new_value, -2)==', ')	
								$new_value = substr($new_value, 0, -2);									
							$fromname = str_replace("%".$label_each."%", $new_value, $fromname);							
						}
						
						if(strpos($subject, "%".$label_each."%")>-1)	
						{	
							$new_value = str_replace('<br>',', ',modFormmaker::custom_fields_mail($type, $key, $id,''));		
							if(substr($new_value, -2)==', ')	
								$new_value = substr($new_value, 0, -2);							
							$subject = str_replace("%".$label_each."%", $new_value, $subject);							
						}
				}	
				
				$custom_fields_value = array( $ip, $useremail, $username, $subid, $list );
				foreach($custom_fields as $key=>$custom_field)
				{
					if(strpos($new_script, "%".$custom_field."%")>-1)
					$new_script = str_replace("%".$custom_field."%", $custom_fields_value[$key], $new_script);

					if($key==2 || $key==3)
					{
						if(strpos($fromname, "%".$custom_field."%")>-1)
							$fromname = str_replace("%".$custom_field."%", $custom_fields_value[$key], $fromname);
							
						if(strpos($subject, "%".$custom_field."%")>-1)
							$subject = str_replace("%".$custom_field."%", $custom_fields_value[$key], $subject);
					}
				}
	
				$body      = $new_script;

				$send_copy=$input_get->getString("wdform_send_copy_".$id);
			
				if(isset($send_copy))
					$send=true;
				else
				{
					foreach($send_tos as $send_to)
					{
						$recipient=$input_get->getString('wdform_'.str_replace('*', '', $send_to)."_element".$id);
						if($recipient)
							$send=modFormmaker::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cca, $bcc, $attachment_user, $replyto, $replytoname); 
					}
				}
				
			}				
					
			if($row->sendemail)
			if($row->mail)
			{
				if($row->mail_from)
				{
					$from      = $input_get->getString('wdform_'.$row->mail_from."_element".$id);
					if(!isset($from))
						$from = $row->mail_from;
				}
				else
				{
					$from      = $config->get( 'mailfrom' );
				}
				
				if($row->mail_from_name)
					$fromname     = $row->mail_from_name;
				else
					$fromname      = $config->get( 'fromname' );
				
				if($row->reply_to)
				{
					$replyto      = $input_get->getString('wdform_'.$row->reply_to."_element".$id);
					if(!isset($replyto))
						$replyto = $row->reply_to;
				}
				$recipient = $row->mail;
				$cca = $row->mail_cc;
				$bcc = $row->mail_bcc;
			
				if($row->mail_subject)	
					$subject 	= $row->mail_subject;
				else
					$subject 	= $row->title;
					
				if($row->mail_attachment)
				for($k=0;$k<count($all_files);$k++)
				{
					if(isset($all_files[$k]['tmp_name'][$k]))
					$attachment[]=array($all_files[$k]['tmp_name'], $all_files[$k]['name'], $all_files[$k]['name']);
				}
				else
				$attachment[] = array(); 		
					
				if($row->mail_mode)
				{
					$mode = 1; 
					$list = wordwrap($list, 70, "\n", true);
					$new_script = $row->script_mail;
				}	
				else
				{
					$mode = 0; 
					$list = $list_text_mode;
					$list = wordwrap($list, 1000, "\n", true);
					$new_script = str_replace(array('<p>','</p>'),'',$row->script_mail);
				}	

				foreach($label_order_original as $key => $label_each) 
				{	
					$type=$label_type[$key];
						if(strpos($row->script_mail, "%".$label_each."%"))	
						{	
							$new_value = modFormmaker::custom_fields_mail($type, $key, $id, $attachment);				
							$new_script = str_replace("%".$label_each."%", $new_value, $new_script);							
						}
					
						if(strpos($fromname, "%".$label_each."%")>-1)	
						{	
							$new_value = str_replace('<br>',', ',modFormmaker::custom_fields_mail($type, $key, $id,''));		
							if(substr($new_value, -2)==', ')	
								$new_value = substr($new_value, 0, -2);								
							$fromname = str_replace("%".$label_each."%", $new_value, $fromname);							
						}
						
						if(strpos($subject, "%".$label_each."%")>-1)	
						{	
							$new_value = str_replace('<br>',', ',modFormmaker::custom_fields_mail($type, $key, $id,''));		
							if(substr($new_value, -2)==', ')	
								$new_value = substr($new_value, 0, -2);						
							$subject = str_replace("%".$label_each."%", $new_value, $subject);							
						}
				}	
				
				$custom_fields_value = array( $ip, $useremail, $username, $subid, $list );
				foreach($custom_fields as $key=>$custom_field)
				{
					if(strpos($new_script, "%".$custom_field."%")>-1)
					$new_script = str_replace("%".$custom_field."%", $custom_fields_value[$key], $new_script);

					if($key==2 || $key==3)
					{
						if(strpos($fromname, "%".$custom_field."%")>-1)
							$fromname = str_replace("%".$custom_field."%", $custom_fields_value[$key], $fromname);
							
						if(strpos($subject, "%".$custom_field."%")>-1)
							$subject = str_replace("%".$custom_field."%", $custom_fields_value[$key], $subject);
					}
				}


				$body      = $new_script;
				
				if($row->sendemail)
				{
				$send=modFormmaker::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cca, $bcc, $attachment, $replyto, $replytoname);  
				}
			}
		
		//	$msg =JFactory::getApplication()->enqueueMessage(JText::_('WDF_SUBMITTED'),'Success');
			$msg=JText::_('WDF_SUBMITTED');
			$succes = 1;

			if($row->sendemail)
			if($row->mail || $row->send_to)
			{
				if ( $send) 
				{
					if ( $send !== true ) 
					{
						$msg=JText::_('WDF_MAIL_SEND_ERROR');
						$succes = 0;
					}
					else 
						$msg=JText::_('WDF_MAIL_SENT');
				}
			}
			
		}
		
		else
		{
			foreach($label_order_ids as $key => $label_order_id)
			{
				$i=$label_order_id;
				$type=$input_get->getString($i."_type".$id);
				if(isset($type))
				if($type!="type_map" and  $type!="type_submit_reset" and  $type!="type_editor" and  $type!="type_captcha" and  $type!="type_recaptcha" and  $type!="type_button")
				{	
					$element_label=$label_order_original[$i];
							
					switch ($type)
					{
						case 'type_text':
						case 'type_password':
						case 'type_textarea':
						case "type_date":
						case "type_own_select":					
						case "type_country":				
						case "type_number":	
						{
							$element=$input_get->getString($i."_element".$id);
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';					
							}
							break;
						
						
						}
						
						case "type_hidden":				
						{
							$element=$input_get->getString($element_label);
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';					
							}
							break;
						}
						
						case "type_mark_map":
						{
							$element=$input_get->getString($i."_long".$id);
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >Longitude:'.$input_get->getString($i."_long".$id).'<br/>Latitude:'.$input_get->getString($i."_lat".$id).'</td></tr>';
							}
							break;		
						}
											
						case "type_submitter_mail":
						{
							$element=$input_get->getString($i."_element".$id);
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';					
								if($input_get->getString($i."_send".$id)=="yes")
									array_push($cc, $element);
							}
							break;		
						}
						
						case "type_time":
						{
							
							$hh=$input_get->getString($i."_hh".$id);
							if(isset($hh))
							{
								$ss=$input_get->getString($i."_ss".$id);
								if(isset($ss))
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString($i."_hh".$id).':'.$input_get->getString($i."_mm".$id).':'.$input_get->getString($i."_ss".$id);
								else
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString($i."_hh".$id).':'.$input_get->getString($i."_mm".$id);
								$am_pm=$input_get->getString($i."_am_pm".$id);
								if(isset($am_pm))
									$list=$list.' '.$input_get->getString($i."_am_pm".$id).'</td></tr>';
								else
									$list=$list.'</td></tr>';
							}
								
							break;
						}
						
						case "type_phone":
						{
							$element_first=$input_get->getString($i."_element_first".$id);
							if(isset($element_first))
							{
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString($i."_element_first".$id).' '.$input_get->getString($i."_element_last".$id).'</td></tr>';
							}	
							break;
						}
						
						case "type_name":
						{
							$element_first=$input_get->getString($i."_element_first".$id);
							if(isset($element_first))
							{
								$element_title=$input_get->getString($i."_element_title".$id);
								if(isset($element_title))
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString($i."_element_title".$id).' '.$input_get->getString($i."_element_first".$id).' '.$input_get->getString($i."_element_last".$id).' '.$input_get->getString($i."_element_middle".$id).'</td></tr>';
								else
									$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString($i."_element_first".$id).' '.$input_get->getString($i."_element_last".$id).'</td></tr>';
							}	   
							break;		
						}
						
						case "type_address":
						{
							$street1=$input_get->getString($i."_street1".$id);

							if(isset($street1))

							

								$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString($i."_street1".$id).'</td></tr>';

								$i++;

						$street2=$input_get->getString($i."_street2".$id);

							if(isset($street2))							

							$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString($i."_street2".$id).'</td></tr>';

								$i++;

								
						$city=$input_get->getString($i."_city".$id);

							if(isset($city))		
							
								$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString($i."_city".$id).'</td></tr>';

								$i++;

							$state=$input_get->getString($i."_state".$id);

							if(isset($state))	
							
								$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString($i."_state".$id).'</td></tr>';

								$i++;

							$postal=$input_get->getString($i."_postal".$id);

							if(isset($postal))	
							
								$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString($i."_postal".$id).'</td></tr>';

								$i++;
								
							$country = $input_get->getString($i."_country".$id);
							
							if(isset($country))
								
								$list=$list.'<tr valign="top"><td >'.$label_order_original[$i].'</td><td >'.$input_get->getString($i."_country".$id).'</td></tr>';

								$i++;			
								
							break;
						}
						
						case "type_date_fields":
						{
							$day=$input_get->getString($i."_day".$id);
							if(isset($day))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString($i."_day".$id).'-'.$input_get->getString($i."_month".$id).'-'.$input_get->getString($i."_year".$id).'</td></tr>';
							}
							break;
						}
						
						case "type_radio":
						{
							$element=$input_get->getString($i."_other_input".$id);
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$input_get->getString($i."_other_input".$id).'</td></tr>';
								break;
							}	
							
							$element=$input_get->getString($i."_element".$id);
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';					
							}
							break;	
						}
						
						case "type_checkbox":
						{
							$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >';
						
							$start=-1;
							for($j=0; $j<100; $j++)
							{
								$element=$input_get->getString($i."_element".$id.$j);
								if(isset($element))
								{
									$start=$j;
									break;
								}
							}	
							
							$other_element_id=-1;
							$is_other=$input_get->getString($i."_allow_other".$id);
							if($is_other=="yes")
							{
								$other_element_id=$input_get->getString($i."_allow_other_num".$id);
							}
							
					
							if($start!=-1)
							{
								for($j=$start; $j<100; $j++)
								{
									
									$element=$input_get->getString($i."_element".$id.$j);
									if(isset($element))
									if($j==$other_element_id)
									{
										$list=$list.$input_get->getString($i."_other_input".$id).'<br>';
									}
									else
									
										$list=$list.$input_get->getString($i."_element".$id.$j).'<br>';
								}
								$list=$list.'</td></tr>';
							}
										
							
							break;
						}
					
					case "type_paypal_price":	
						{		
							$value=0;
							if($input_get->getString($i."_element_dollars".$id))
								$value=$input_get->getString($i."_element_dollars".$id);
							
							if($input_get->getString($i."_element_cents".$id))
								$value=$value.'.'.$input_get->getString($i."_element_cents".$id);
						
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$value.$form_currency.'</td></tr>';
							break;

						}			
				
						case "type_paypal_select":	
						{	
							$value=$input_get->getString($i."_element_label".$id).':'.$input_get->getString($i."_element".$id).$form_currency;
							$element_quantity_label=$input_get->getString($i."_element_quantity_label".$id);
								if(isset($element_quantity_label))
									$value.='<br/>'.$input_get->getString($i."_element_quantity_label".$id).': '.$input_get->getString($i."_element_quantity".$id);
					
							for($k=0; $k<50; $k++)
							{
								$temp_val=$input_get->getString($i."_element_property_value".$id.$k);
								if(isset($temp_val))
								{			
									$value.='<br/>'.$input_get->getString($i."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k);
								}
							}

							$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$value.'</pre></td></tr>';					
							break;
							
						}
				
						case "type_paypal_radio":				
						{
									
							$value=$input_get->getString($i."_element_label".$id).' - '.$input_get->getString($i."_element".$id).$form_currency;
							
							$element_quantity_label=$input_get->getString($i."_element_quantity_label".$id);
								if(isset($element_quantity_label))
									$value.='<br/>'.$input_get->getString($i."_element_quantity_label".$id).': '.$input_get->getString($i."_element_quantity".$id);
					
							for($k=0; $k<50; $k++)
							{
								$temp_val=$input_get->getString($i."_element_property_value".$id.$k);
								if(isset($temp_val))
								{			
									$value.='<br/>'.$input_get->getString($i."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k);
								}
							}

							$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$value.'</pre></td></tr>';				
							
							break;	
						}

						case "type_paypal_shipping":				
						{
							
							$value=$input_get->getString($i."_element_label".$id).' - '.$input_get->getString($i."_element".$id).$form_currency;		
							$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$value.'</pre></td></tr>';				

							break;
						}

						case "type_paypal_checkbox":				
						{
							$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td >';
						
							$start=-1;
							for($j=0; $j<100; $j++)
							{
								$element=$input_get->getString($i."_element".$id.$j);
								if(isset($element))
								{
									$start=$j;
									break;
								}
							}	
							
							if($start!=-1)
							{
								for($j=$start; $j<100; $j++)
								{
									
									$element=$input_get->getString($i."_element".$id.$j);
									if(isset($element))
									{
										$list=$list.$input_get->getString($i."_element".$id.$j."_label").' - '.($input_get->getString($i."_element".$id.$j)=='' ? '0'.$form_currency : $input_get->getString($i."_element".$id.$j)).$form_currency.'<br>';
									}
								}
							}
							
							$element_quantity_label=$input_get->getString($i."_element_quantity_label".$id);
								if(isset($element_quantity_label))
									$list=$list.'<br/>'.$input_get->getString($i."_element_quantity_label".$id).': '.$input_get->getString($i."_element_quantity".$id);
					
							for($k=0; $k<50; $k++)
							{
								$temp_val=$input_get->getString($i."_element_property_value".$id.$k);
								if(isset($temp_val))
								{			
									$list=$list.'<br/>'.$input_get->getString($i."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k);
								}
							}

							$list=$list.'</td></tr>';
														
					
							break;
						}
				
						case "type_paypal_total":				

						{
						
						
							$element=$input_get->getString($i."_paypal_total".$id);

							
							$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';				



							break;

						}
				
							case "type_star_rating":
						{
							$element=$input_get->getString($i."_star_amount".$id);
							$selected=$input_get->getString($i."_selected_star_amount".$id,0);
							//$star_color=$input_get->getString($i."_star_color_id_temp");
							
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$selected.'/'.$element.'</pre></td></tr>';					
							}
							break;
						}
						

						case "type_scale_rating":
						{
						$element=$input_get->getString($i."_scale_amount".$id);
						$selected=$input_get->getString($i."_scale_radio".$id,0);
						
							
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$selected.'/'.$element.'</pre></td></tr>';					
							}
							break;
						}
						
						case "type_spinner":
						{

							$element=$input_get->getString($i."_element".$id);
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';					
							}
							break;
						}
						
						case "type_slider":
						{

							$element=$input_get->getString($i."_slider_value".$id);
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';					
							}
							break;
						}
						case "type_range":
						{

							$element0=$input_get->getString($i."_element".$id.'0');
						    $element1=$input_get->getString($i."_element".$id.'1');
							if(isset($element0) || isset($element1))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">From:'.$element0.'<span style="margin-left:6px">To</span>:'.$element1.'</pre></td></tr>';					
							}
							break;
						}
						
						case "type_grading":
						{
							$element=$input_get->getString($i."_hidden_item".$id);
							$grading = explode(":",$element);
							$items_count = sizeof($grading)-1;
							
							$element = "";
							$total = "";
							
							for($k=0;$k<$items_count;$k++)

							{
								$element .= $grading[$k].":".$input_get->getString($i."_element".$id.$k)." ";
								$total += $input_get->getString($i."_element".$id.$k);
							}

							$element .="Total:".$total;

																				
							if(isset($element))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$element.'</pre></td></tr>';					
							}
							break;
						}
						
							case "type_matrix":
						{
						
							
							$input_type=$input_get->getString($i."_input_type".$id); 
													
							$mat_rows = $input_get->getString($i."_hidden_row".$id);
							$mat_rows = explode('***', $mat_rows);
							$mat_rows = array_slice($mat_rows,0, count($mat_rows)-1);
							
							$mat_columns = $input_get->getString($i."_hidden_column".$id);
							$mat_columns = explode('***', $mat_columns);
							$mat_columns = array_slice($mat_columns,0, count($mat_columns)-1);
					  
							$row_ids=explode(",",substr($input_get->getString($i."_row_ids".$id), 0, -1));
							$column_ids=explode(",",substr($input_get->getString($i."_column_ids".$id), 0, -1)); 
										
							$matrix="<table>";
										
								$matrix .='<tr><td></td>';
							
							for( $k=0;$k< count($mat_columns) ;$k++)
								$matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
								$matrix .='</tr>';
							
							$aaa=Array();
							   $k=0;
							foreach($row_ids as $row_id)
							{
							$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
							
								if($input_type=="radio")
								{
							
							
									$mat_radio = $input_get->getString($i."_input_element".$id.$row_id,0);											
									if($mat_radio==0)
									{
										$checked="";
										$aaa[1]="";
									}
								    else
									$aaa=explode("_",$mat_radio);
									
							        foreach($column_ids as $column_id)
									{
										if($aaa[1]==$column_id)
									    $checked="checked";
									    else
									    $checked="";
										
										$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
                                    
							        }
							
							    } 
								else
								{
									if($input_type=="checkbox")
									{                
										foreach($column_ids as $column_id)
										{
											$checked = $input_get->getString($i."_input_element".$id.$row_id.'_'.$column_id);                     
											if($checked==1)						
											$checked = "checked";				
											else								
											$checked = "";

											$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
									
										}
								
									}
									else
									{
										if($input_type=="text")
										{
																  
											foreach($column_ids as $column_id)
											{
												$checked = $input_get->getString($i."_input_element".$id.$row_id.'_'.$column_id);
												$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
								
											}
										
										}
										else{
											foreach($column_ids as $column_id)
											{
												$checked = $input_get->getString($i."_select_yes_no".$id.$row_id.'_'.$column_id);
												$matrix .='<td style="text-align:center">'.$checked.'</td>';
											
							
										
											}
										}
									
									}
									
								}
							    $matrix .='</tr>';
								$k++;
							}
							 $matrix .='</table>';

		
		
		
																			
							if(isset($matrix))
							{
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="font-family:inherit; margin:0px; padding:0px">'.$matrix.'</pre></td></tr>';					
							}
						
							break;
						}
					
				
						default: break;
					}
				}
					
						
					
				
				
			}
			
			$list=$list.'</table>';
			$list = wordwrap($list, 70, "\n", true);

							$config = JFactory::getConfig();
							if($row->mail_from)
							$site_mailfrom = $row->mail_from;
							else
							$site_mailfrom=$config->get( 'mailfrom' );								

							if($row->mail_from_name)
							$site_fromname = $row->mail_from_name;
							else
							$site_fromname=$config->get( 'fromname' );				
							for($k=0;$k<count($all_files);$k++)
								$attachment[]=array($all_files[$k]['tmp_name'], $all_files[$k]['name'], $all_files[$k]['name'] );
							
							if(isset($cc[0]))
							{
								foreach	($cc as $c)	
								{	
									if($c)
									{
										 $from      = $site_mailfrom;
										 $fromname  = $site_fromname; 
										 $recipient = $c;
										 $subject   = $row->title;
										 
//////////////////////////////////////////////////////////////////////////////////////////////////////////										 

										$new_script = $row->script_mail_user;

										foreach($label_order_original as $key => $label_each) 

										{	

											if(strpos($row->script_mail_user, "%".$label_each."%")!=-1)

											{				

												$type = $input_get->getString($key."_type".$id);

												if($type!="type_submit_reset" or $type!="type_map" or $type!="type_editor" or  $type!="type_captcha" or  $type!="type_recaptcha" or  $type!="type_button")

												{

													$new_value ="";

													switch ($type)

														{

															case 'type_text':

															case 'type_password':

															case 'type_textarea':

															case "type_date":

															case "type_own_select":					

															case "type_country":				

															case "type_number":	

															{

																$element=$input_get->getString($key."_element".$id);

																if(isset($element))

																{

																	$new_value = $element;					

																}

																break;

															

															

															}

															

															case "type_hidden":				

															{

																$element=$input_get->getString($element_label);

																if(isset($element))

																{

																	$new_value = $element;	

																}

																break;

															}

															

															

															case "type_mark_map":

															{

																$element=$input_get->getString($key."_long".$id);

																if(isset($element))

																{

																	$new_value = 'Longitude:'.$input_get->getString($key."_long".$id).'<br/>Latitude:'.$input_get->getString($key."_lat".$id);

																}

																break;		

															}

																				

															case "type_submitter_mail":

															{

																$element=$input_get->getString($key."_element".$id);

																if(isset($element))

																{

																	$new_value = $element;					

																}

																break;		

															}

															

															case "type_time":

															{

																

																$hh=$input_get->getString($key."_hh".$id);

																if(isset($hh))

																{

																	$ss=$input_get->getString($key."_ss".$id);

																	if(isset($ss))

																		$new_value = $input_get->getString($key."_hh".$id).':'.$input_get->getString($key."_mm".$id).':'.$input_get->getString($key."_ss".$id);

																	else

																		$new_value = $input_get->getString($key."_hh".$id).':'.$input_get->getString($key."_mm".$id);

																	$am_pm=$input_get->getString($key."_am_pm".$id);

																	if(isset($am_pm))

																		$new_value=$new_value.' '.$input_get->getString($key."_am_pm".$id);

																	

																}

																	

																break;

															}

															

															case "type_phone":

															{

																$element_first=$input_get->getString($key."_element_first".$id);

																if(isset($element_first))

																{

																		$new_value = $input_get->getString($key."_element_first".$id).' '.$input_get->getString($key."_element_last".$id);

																}	

																break;

															}

															

															case "type_name":

															{

																$element_first=$input_get->getString($key."_element_first".$id);

																if(isset($element_first))

																{

																	$element_title=$input_get->getString($key."_element_title".$id);

																	if(isset($element_title))

																		$new_value = $input_get->getString($key."_element_title".$id).' '.$input_get->getString($key."_element_first".$id).' '.$input_get->getString($i."_element_last".$id).' '.$input_get->getString($i."_element_middle".$id);

																	else

																		$new_value = $input_get->getString($key."_element_first".$id).' '.$input_get->getString($key."_element_last".$id);

																}	   

																break;		

															}

															

															case "type_address":
															{
																$street1=$input_get->getString($key."_street1".$id);

																	if(isset($street1))

																	{

																		$new_value=$input_get->getString($key."_street1".$id);

																		break;

																	}
																	
																	$street2=$input_get->getString($key."_street2".$id);

																	if(isset($street2))

																	{

																		$new_value=$input_get->getString($key."_street2".$id);

																		break;

																	}	
																	
																	$city=$input_get->getString($key."_city".$id);
																	
																	if(isset($city))

																	{

																		$new_value=$input_get->getString($key."_city".$id);

																	break;

																	}
																	
																	$state=$input_get->getString($key."_state".$id);
																	
																	if(isset($state))

																	{

																		$new_value=$input_get->getString($key."_state".$id);

																		break;

																	}
																		
																		$postal=$input_get->getString($key."_postal".$id);
																	
																	if(isset($postal))

																	{

																		$new_value=$input_get->getString($key."_postal".$id);

																		break;
																	}	

																	
																	$country = $input_get->getString($key."_country".$id);
																	
																		if(isset($country))
																		{

																		$new_value=$input_get->getString($key."_country".$id);

																		break;
																		}
																	

																	break;
															}
															

															case "type_date_fields":

															{

																$day=$input_get->getString($key."_day".$id);

																if(isset($day))

																{

																	$new_value = $input_get->getString($key."_day".$id).'-'.$input_get->getString($key."_month".$id).'-'.$input_get->getString($key."_year".$id);

																}

																break;

															}

															

															case "type_radio":

															{

																$element=$input_get->getString($key."_other_input".$id);

																if(isset($element))

																{

																	$new_value = $input_get->getString($key."_other_input".$id);

																	break;

																}	

																

																$element=$input_get->getString($key."_element".$id);

																if(isset($element))

																{

																	$new_value = $element;					

																}

																break;	

															}

															

															case "type_checkbox":

															{

																						

																$start=-1;

																for($j=0; $j<100; $j++)

																{

																	$element=$input_get->getString($key."_element".$id.$j);

																	if(isset($element))

																	{

																		$start=$j;

																		break;

																	}

																}	

																

																$other_element_id=-1;

																$is_other=$input_get->getString($key."_allow_other".$id);

																if($is_other=="yes")

																{

																	$other_element_id=$input_get->getString($key."_allow_other_num".$id);

																}

																

														

																if($start!=-1)

																{

																	for($j=$start; $j<100; $j++)

																	{

																		

																		$element=$input_get->getString($key."_element".$id.$j);

																		if(isset($element))

																		if($j==$other_element_id)

																		{

																			$new_value = $new_value.$input_get->getString($key."_other_input".$id).'<br>';

																		}

																		else

																		

																			$new_value = $new_value.$input_get->getString($key."_element".$id.$j).'<br>';

																	}

																	

																}

																			

																

																break;

															}

															

															

															case "type_paypal_price":	

															{		

																$new_value=0;

																if($input_get->getString($key."_element_dollars".$id))

																	$new_value=$input_get->getString($key."_element_dollars".$id);

																

																if($input_get->getString($key."_element_cents".$id))

																	$new_value=$new_value.'.'.$input_get->getString($key."_element_cents".$id);

															

																$new_value=$new_value.$form_currency;





																	

										

																break;

															}			

															

															case "type_paypal_select":	

															{	

													

																$new_value=$input_get->getString($key."_element_label".$id).':'.$input_get->getString($key."_element".$id).$form_currency;

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																if(isset($element_quantity_label))

																	$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k);

																	}

																}



									

																break;

															}

															

															case "type_paypal_radio":				

															{

																

															$new_value=$input_get->getString($key."_element_label".$id).' - '.$input_get->getString($key."_element".$id).$form_currency;

																

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																	if(isset($element_quantity_label))

																		$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($key."_element_property_value".$id.$k);

																	}

																}

														

																break;

															}



															case "type_paypal_shipping":				

															{

																

																$new_value=$input_get->getString($key."_element_label".$id).' : '.$input_get->getString($key."_element".$id).$form_currency;		



																break;

															}



															case "type_paypal_checkbox":				

															{

																$start=-1;

																for($j=0; $j<100; $j++)

																{

																	$element=$input_get->getString($key."_element".$id.$j);

																	if(isset($element))

																	{

																		$start=$j;

																		break;

																	}

																}	

																

																if($start!=-1)

																{

																	for($j=$start; $j<100; $j++)

																	{

																		

																		$element=$input_get->getString($key."_element".$id.$j);

																		if(isset($element))

																		{

																			$new_value=$new_value.$input_get->getString($key."_element".$id.$j."_label").' - '.($input_get->getString($key."_element".$id.$j)=='' ? '0'.$form_currency : $input_get->getString($key."_element".$id.$j)).$form_currency.'<br>';

																		}

																	}

																}

																

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																	if(isset($element_quantity_label))

																		$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($key."_element_property_value".$id.$k);

																	}

																}

																

																break;

															}

															case "type_paypal_total":				

															{
															
															
																$element=$input_get->getString($key."_paypal_total".$id);

																
																$new_value=$new_value.$element;				



																break;

															}
															
															case "type_star_rating":
															{
																$element=$input_get->getString($key."_star_amount".$id);
																$selected=$input_get->getString($key."_selected_star_amount".$id,0);
																
																
																if(isset($element))
																{
																	$new_value=$new_value.$selected.'/'.$element;					
																}
																break;
															}
															

															case "type_scale_rating":
															{
															$element=$input_get->getString($key."_scale_amount".$id);
															$selected=$input_get->getString($key."_scale_radio".$id,0);
															
																
																if(isset($element))
																{
																	$new_value=$new_value.$selected.'/'.$element;					
																}
																break;
															}
															
															case "type_spinner":
															{

																$element=$input_get->getString($key."_element".$id);
																if(isset($element))
																{
																	$new_value=$new_value.$element;					
																}
																break;
															}
															
															case "type_slider":
															{

																$element=$input_get->getString($key."_slider_value".$id);
																if(isset($element))
																{
																	$new_value=$new_value.$element;					
																}
																break;
															}
															case "type_range":
															{

																$element0=$input_get->getString($key."_element".$id.'0');
																$element1=$input_get->getString($key."_element".$id.'1');
																if(isset($element0) || isset($element1))
																{
																	$new_value=$new_value.$element0.'-'.$element1;					
																}
																break;
															}
															
															case "type_grading":
															{
																$element=$input_get->getString($key."_hidden_item".$id);
																$grading = explode(":",$element);
																$items_count = sizeof($grading)-1;
																
																$element = "";
																$total = "";
																
																for($k=0;$k<$items_count;$k++)

																{
																	$element .= $grading[$k].":".$input_get->getString($key."_element".$id.$k)." ";
															$total += $input_get->getString($key."_element".$id.$k);
														}

														$element .="Total:".$total;

																											
														if(isset($element))
														{
															$new_value=$new_value.$element;					
														}
														break;
													}
													
														case "type_matrix":
													{
													
														
														$input_type=$input_get->getString($key."_input_type".$id); 
																				
														$mat_rows = $input_get->getString($key."_hidden_row".$id);
														$mat_rows = explode('***', $mat_rows);
														$mat_rows = array_slice($mat_rows,0, count($mat_rows)-1);
														
														$mat_columns = $input_get->getString($key."_hidden_column".$id);
														$mat_columns = explode('***', $mat_columns);
														$mat_columns = array_slice($mat_columns,0, count($mat_columns)-1);
												  
														$row_ids=explode(",",substr($input_get->getString($key."_row_ids".$id), 0, -1));
														$column_ids=explode(",",substr($input_get->getString($key."_column_ids".$id), 0, -1)); 
								
																	
														$matrix="<table>";
																	
															$matrix .='<tr><td></td>';
														
														for( $k=0;$k< count($mat_columns) ;$k++)
															$matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
															$matrix .='</tr>';
														
														$aaa=Array();
														   $k=0;
														foreach( $row_ids as $row_id){
														$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
														
														  if($input_type=="radio"){
														 
														$mat_radio = $input_get->getString($key."_input_element".$id.$row_id,0);											
														  if($mat_radio==0){
																$checked="";
																$aaa[1]="";
																}
																else{
																$aaa=explode("_",$mat_radio);
																}
																
																foreach( $column_ids as $column_id){
																	if($aaa[1]==$column_id)
																	$checked="checked";
																	else
																	$checked="";
																$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
																
																}
																
															} 
															else{
															if($input_type=="checkbox")
															{                
																foreach( $column_ids as $column_id){
																 $checked = $input_get->getString($key."_input_element".$id.$row_id.'_'.$column_id);                              
																 if($checked==1)							
																 $checked = "checked";						
																 else									 
																 $checked = "";

																$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
															
															}
															
															}
															else
															{
															if($input_type=="text")
															{
																					  
																foreach( $column_ids as $column_id){
																 $checked = $input_get->getString($key."_input_element".$id.$row_id.'_'.$column_id);
																	
																$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
													
															}
															
															}
															else{
																foreach( $column_ids as $column_id){
																 $checked = $input_get->getString($key."_select_yes_no".$id.$row_id.'_'.$column_id);
																	 $matrix .='<td style="text-align:center">'.$checked.'</td>';
																
												
															
																}
															}
															
															}
															
															}
															$matrix .='</tr>';
															$k++;
														}
														 $matrix .='</table>';

									
									
									
																										
														if(isset($matrix))
														{
															$new_value=$new_value.$matrix;					
														}
													
														break;
													}
												
															

															

															default: break;

														}

														

													$new_script = str_replace("%".$label_each."%", $new_value, $new_script);	

													}



												

											

											}

										}	

										if(strpos($new_script, "%ip%")>-1)
											$new_script = str_replace("%ip%", $ip, $new_script);	

										if(strpos($new_script, "%all%")!=-1)

											$new_script = str_replace("%all%", $list, $new_script);	



										$body      = $new_script;

//////////////////////////////////////////////////////////////////////////////////////////////////////////										 

///////////////////////////////////////////////////////////////////////
										 $mode      = 1; 
									$send=modFormmaker::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cca, $bcc, $attachment, $replyto, $replytoname);  
									}	
									
									
									if($row->mail)
									{

										if($c)
										{
											 $from      = $c;
											 $fromname  = $c; 
										}
										else
										{
											 $from      = $site_mailfrom;
											 $fromname  = $site_fromname; 
										}
											 $recipient = $row->mail;
											 $subject   = $row->title;
											 $new_script = $row->script_mail;

											foreach($label_order_original as $key => $label_each) 

											{	

												if(strpos($row->script_mail, "%".$label_each."%")!=-1)

												{				

													$type =$input_get->getString($key."_type".$id);

													if($type!="type_submit_reset" or $type!="type_map" or $type!="type_editor" or  $type!="type_captcha" or  $type!="type_recaptcha" or  $type!="type_button")

													{

														$new_value ="";

														switch ($type)

															{

																case 'type_text':

																case 'type_password':

																case 'type_textarea':

																case "type_date":

																case "type_own_select":					

																case "type_country":				

																case "type_number":	

																{

																	$element=$input_get->getString($key."_element".$id);

																	if(isset($element))

																	{

																		$new_value = $element;					

																	}

																	break;

																

																

																}

																

																case "type_hidden":				

																{

																	$element=$input_get->getString($element_label);

																	if(isset($element))

																	{

																		$new_value = $element;	

																	}

																	break;

																}

																

																

																case "type_mark_map":

																{

																	$element=$input_get->getString($key."_long".$id);

																	if(isset($element))

																	{

																		$new_value = 'Longitude:'.$input_get->getString($key."_long".$id).'<br/>Latitude:'.$input_get->getString($key."_lat".$id);

																	}

																	break;		

																}

																					

																case "type_submitter_mail":

																{

																	$element=$input_get->getString($key."_element".$id);

																	if(isset($element))

																	{

																		$new_value = $element;					

																	}

																	break;		

																}

																

																case "type_time":

																{

																	

																	$hh=$input_get->getString($key."_hh".$id);

																	if(isset($hh))

																	{

																		$ss=$input_get->getString($key."_ss".$id);

																		if(isset($ss))

																			$new_value = $input_get->getString($key."_hh".$id).':'.$input_get->getString($key."_mm".$id).':'.$input_get->getString($key."_ss".$id);

																		else

																			$new_value = $input_get->getString($key."_hh".$id).':'.$input_get->getString($key."_mm".$id);

																		$am_pm=$input_get->getString($key."_am_pm".$id);

																		if(isset($am_pm))

																			$new_value=$new_value.' '.$input_get->getString($key."_am_pm".$id);

																		

																	}

																		

																	break;

																}

																

																case "type_phone":

																{

																	$element_first=$input_get->getString($key."_element_first".$id);

																	if(isset($element_first))

																	{

																			$new_value = $input_get->getString($key."_element_first".$id).' '.$input_get->getString($key."_element_last".$id);

																	}	

																	break;

																}

																

																case "type_name":

																{

																	$element_first=$input_get->getString($key."_element_first".$id);

																	if(isset($element_first))

																	{

																		$element_title=$input_get->getString($key."_element_title".$id);

																		if(isset($element_title))

																			$new_value = $input_get->getString($key."_element_title".$id).' '.$input_get->getString($key."_element_first".$id).' '.$input_get->getString($i."_element_last".$id).' '.$input_get->getString($i."_element_middle".$id);

																		else

																			$new_value = $input_get->getString($key."_element_first".$id).' '.$input_get->getString($key."_element_last".$id);

																	}	   

																	break;		

																}

																

																case "type_address":
															{
																$street1=$input_get->getString($key."_street1".$id);

																	if(isset($street1))

																	{

																		$new_value=$input_get->getString($key."_street1".$id);

																		break;

																	}
																	
																	$street2=$input_get->getString($key."_street2".$id);

																	if(isset($street2))

																	{

																		$new_value=$input_get->getString($key."_street2".$id);

																		break;

																	}	
																	
																	$city=$input_get->getString($key."_city".$id);
																	
																	if(isset($city))

																	{

																		$new_value=$input_get->getString($key."_city".$id);

																		break;

																	}
																	
																	$state=$input_get->getString($key."_state".$id);
																	
																	if(isset($state))

																	{

																		$new_value=$input_get->getString($key."_state".$id);

																		break;

																	}
																		
																		$postal=$input_get->getString($key."_postal".$id);
																	
																	if(isset($postal))

																	{

																		$new_value=$input_get->getString($key."_postal".$id);

																		break;

																	}	

																	
																	$country = $input_get->getString($key."_country".$id);
																	
																		if(isset($country))
																		{

																		$new_value=$input_get->getString($key."_country".$id);

																		break;		
																		}
																	

																	break;

															}
															

																case "type_date_fields":

																{

																	$day=$input_get->getString($key."_day".$id);

																	if(isset($day))

																	{

																		$new_value = $input_get->getString($key."_day".$id).'-'.$input_get->getString($key."_month".$id).'-'.$input_get->getString($key."_year".$id);

																	}

																	break;

																}

																

																case "type_radio":

																{

																	$element=$input_get->getString($key."_other_input".$id);

																	if(isset($element))

																	{

																		$new_value = $input_get->getString($key."_other_input".$id);

																		break;

																	}	

																	

																	$element=$input_get->getString($key."_element".$id);

																	if(isset($element))

																	{

																		$new_value = $element;					

																	}

																	break;	

																}

																

																case "type_checkbox":

																{

																							

																	$start=-1;

																	for($j=0; $j<100; $j++)

																	{

																		$element=$input_get->getString($key."_element".$id.$j);

																		if(isset($element))

																		{

																			$start=$j;

																			break;

																		}

																	}	

																	

																	$other_element_id=-1;

																	$is_other=$input_get->getString($key."_allow_other".$id);

																	if($is_other=="yes")

																	{

																		$other_element_id=$input_get->getString($key."_allow_other_num".$id);

																	}

																	

															

																	if($start!=-1)

																	{

																		for($j=$start; $j<100; $j++)

																		{

																			

																			$element=$input_get->getString($key."_element".$id.$j);

																			if(isset($element))

																			if($j==$other_element_id)

																			{

																				$new_value = $new_value.$input_get->getString($key."_other_input".$id).'<br>';

																			}

																			else

																			

																				$new_value = $new_value.$input_get->getString($key."_element".$id.$j).'<br>';

																		}

																		

																	}

																	break;

																	}

																	case "type_paypal_price":	

															{		

																$new_value=0;

																if($input_get->getString($key."_element_dollars".$id))

																	$new_value=$input_get->getString($key."_element_dollars".$id);

																

																if($input_get->getString($key."_element_cents".$id))

																	$new_value=$new_value.'.'.$input_get->getString($key."_element_cents".$id);

															

																$new_value=$new_value.$form_currency;





																	

																	

																break;



															}			

				

															case "type_paypal_select":	

															{	

																$new_value=$input_get->getString($key."_element_label".$id).':'.$input_get->getString($key."_element".$id).$form_currency;

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																if(isset($element_quantity_label))

																	$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k);

																	}

																}



																break;

																

															}

													

															case "type_paypal_radio":				

															{

																		

																$new_value=$input_get->getString($key."_element_label".$id).' - '.$input_get->getString($key."_element".$id).$form_currency;

																

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																	if(isset($element_quantity_label))

																		$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($key."_element_property_value".$id.$k);

																	}

																}

																

																break;	

															}



															case "type_paypal_shipping":				

															{

																

																$new_value=$input_get->getString($key."_element_label".$id).' : '.$input_get->getString($key."_element".$id).$form_currency;		



																break;

															}



															case "type_paypal_checkbox":				

															{										

																$start=-1;

																for($j=0; $j<100; $j++)

																{

																	$element=$input_get->getString($key."_element".$id.$j);

																	if(isset($element))

																	{

																		$start=$j;

																		break;

																	}

																}	

																

																if($start!=-1)

																{

																	for($j=$start; $j<100; $j++)

																	{

																		

																		$element=$input_get->getString($key."_element".$id.$j);

																		if(isset($element))

																		{

																			$new_value=$new_value.$input_get->getString($key."_element".$id.$j."_label").' - '.($input_get->getString($key."_element".$id.$j)=='' ? '0'.$form_currency : $input_get->getString($key."_element".$id.$j)).$form_currency.'<br>';

																		}

																	}

																}

																

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																	if(isset($element_quantity_label))

																		$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($key."_element_property_value".$id.$k);

																	}

																}

																							

														

																break;

															}			

															case "type_paypal_total":				

															{
															
															
																$element=$input_get->getString($key."_paypal_total".$id);

																
																$new_value=$new_value.$element;				



																break;

															}
															
															case "type_star_rating":
															{
																$element=$input_get->getString($key."_star_amount".$id);
																$selected=$input_get->getString($key."_selected_star_amount".$id,0);
																//$star_color=$input_get->getString($key."_star_color_id_temp");
																
																if(isset($element))
																{
																	$new_value=$new_value.$selected.'/'.$element;					
																}
																break;
															}
															

															case "type_scale_rating":
															{
															$element=$input_get->getString($key."_scale_amount".$id);
															$selected=$input_get->getString($key."_scale_radio".$id,0);
															
																
																if(isset($element))
																{
																	$new_value=$new_value.$selected.'/'.$element;					
																}
																break;
															}
															
															case "type_spinner":
															{

																$element=$input_get->getString($key."_element".$id);
																if(isset($element))
																{
																	$new_value=$new_value.$element;					
																}
																break;
															}
															
															case "type_slider":
															{

																$element=$input_get->getString($key."_slider_value".$id);
																if(isset($element))
																{
																	$new_value=$new_value.$element;					
																}
																break;
															}
															case "type_range":
															{

																$element0=$input_get->getString($key."_element".$id.'0');
																$element1=$input_get->getString($key."_element".$id.'1');
																if(isset($element0) || isset($element1))
																{
																	$new_value=$new_value.$element0.'-'.$element1;					
																}
																break;
															}
															
															case "type_grading":
															{
																$element=$input_get->getString($key."_hidden_item".$id);
																$grading = explode(":",$element);
																$items_count = sizeof($grading)-1;
																
																$element = "";
																$total = "";
																
																for($k=0;$k<$items_count;$k++)

																{
																	$element .= $grading[$k].":".$input_get->getString($key."_element".$id.$k)." ";
															$total += $input_get->getString($key."_element".$id.$k);
														}

														$element .="Total:".$total;

																											
														if(isset($element))
														{
															$new_value=$new_value.$element;					
														}
														break;
													}
													
														case "type_matrix":
													{
													
														
														$input_type=$input_get->getString($key."_input_type".$id); 
																				
														$mat_rows = $input_get->getString($key."_hidden_row".$id);
														$mat_rows = explode('***', $mat_rows);
														$mat_rows = array_slice($mat_rows,0, count($mat_rows)-1);
														
														$mat_columns = $input_get->getString($key."_hidden_column".$id);
														$mat_columns = explode('***', $mat_columns);
														$mat_columns = array_slice($mat_columns,0, count($mat_columns)-1);
												  
														$row_ids=explode(",",substr($input_get->getString($key."_row_ids".$id), 0, -1));
														$column_ids=explode(",",substr($input_get->getString($key."_column_ids".$id), 0, -1)); 
								
														
														$matrix="<table>";
																	
															$matrix .='<tr><td></td>';
														
														for( $k=0;$k< count($mat_columns) ;$k++)
															$matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
															$matrix .='</tr>';
														
														$aaa=Array();
														   $k=0;
														foreach( $row_ids as $row_id){
														$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
														
														  if($input_type=="radio"){
														 
														$mat_radio = $input_get->getString($key."_input_element".$id.$row_id,0);											
														  if($mat_radio==0){
																$checked="";
																$aaa[1]="";
																}
																else{
																$aaa=explode("_",$mat_radio);
																}
																
																foreach( $column_ids as $column_id){
																	if($aaa[1]==$column_id)
																	$checked="checked";
																	else
																	$checked="";
																$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
																
																}
																
															} 
															else{
															if($input_type=="checkbox")
															{                
																foreach( $column_ids as $column_id){
																 $checked = $input_get->getString($key."_input_element".$id.$row_id.'_'.$column_id);                              
																 if($checked==1)							
																 $checked = "checked";						
																 else									 
																 $checked = "";

																$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
															
															}
															
															}
															else
															{
															if($input_type=="text")
															{
																					  
																foreach( $column_ids as $column_id){
																 $checked = $input_get->getString($key."_input_element".$id.$row_id.'_'.$column_id);
																	
																$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
													
															}
															
															}
															else{
																foreach( $column_ids as $column_id){
																 $checked = $input_get->getString($key."_select_yes_no".$id.$row_id.'_'.$column_id);
																	 $matrix .='<td style="text-align:center">'.$checked.'</td>';
																
												
															
															}
															}
															
															}
															
															}
															$matrix .='</tr>';
															$k++;
														}
														 $matrix .='</table>';

									
									
									
																										
														if(isset($matrix))
														{
															$new_value=$new_value.$matrix;					
														}
													
														break;
													}
												
																

																default: break;

															}

															

														$new_script = str_replace("%".$label_each."%", $new_value, $new_script);	

														}



													

												

												}

											}	

											if(strpos($new_script, "%ip%")>-1)
												$new_script = str_replace("%ip%", $ip, $new_script);	

											if(strpos($new_script, "%all%")!=-1)

												$new_script = str_replace("%all%", $list, $new_script);	



											$body      = $new_script;
											 $mode      = 1; 

										$send=modFormmaker::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cca, $bcc, $attachment, $replyto, $replytoname);  
									}
								}
							}
							else 
							{ 
								if($row->mail)
								{

								 $from      = $site_mailfrom;
								 $fromname  = $site_fromname; 
								 $recipient = $row->mail;
								 $subject     = $row->title;
								 $new_script = $row->script_mail;

											foreach($label_order_original as $key => $label_each) 

											{	

												if(strpos($row->script_mail, "%".$label_each."%")!=-1)

												{				

													$type = $input_get->getString($key."_type".$id);

													if($type!="type_submit_reset" or $type!="type_map" or $type!="type_editor" or  $type!="type_captcha" or  $type!="type_recaptcha" or  $type!="type_button")

													{

														$new_value ="";

														switch ($type)

															{

																case 'type_text':

																case 'type_password':

																case 'type_textarea':

																case "type_date":

																case "type_own_select":					

																case "type_country":				

																case "type_number":	

																{

																	$element=$input_get->getString($key."_element".$id);

																	if(isset($element))

																	{

																		$new_value = $element;					

																	}

																	break;

																

																

																}

																

																case "type_hidden":				

																{

																	$element=$input_get->getString($element_label);

																	if(isset($element))

																	{

																		$new_value = $element;	

																	}

																	break;

																}

																

																

																case "type_mark_map":

																{

																	$element=$input_get->getString($key."_long".$id);

																	if(isset($element))

																	{

																		$new_value = 'Longitude:'.$input_get->getString($key."_long".$id).'<br/>Latitude:'.$input_get->getString($key."_lat".$id);

																	}

																	break;		

																}

																					

																case "type_submitter_mail":

																{

																	$element=$input_get->getString($key."_element".$id);

																	if(isset($element))

																	{

																		$new_value = $element;					

																	}

																	break;		

																}

																

																case "type_time":

																{

																	

																	$hh=$input_get->getString($key."_hh".$id);

																	if(isset($hh))

																	{

																		$ss=$input_get->getString($key."_ss".$id);

																		if(isset($ss))

																			$new_value = $input_get->getString($key."_hh".$id).':'.$input_get->getString($key."_mm".$id).':'.$input_get->getString($key."_ss".$id);

																		else

																			$new_value = $input_get->getString($key."_hh".$id).':'.$input_get->getString($key."_mm".$id);

																		$am_pm=$input_get->getString($key."_am_pm".$id);

																		if(isset($am_pm))

																			$new_value=$new_value.' '.$input_get->getString($key."_am_pm".$id);

																		

																	}

																		

																	break;

																}

																

																case "type_phone":

																{

																	$element_first=$input_get->getString($key."_element_first".$id);

																	if(isset($element_first))

																	{

																			$new_value = $input_get->getString($key."_element_first".$id).' '.$input_get->getString($key."_element_last".$id);

																	}	

																	break;

																}

																

																case "type_name":

																{

																	$element_first=$input_get->getString($key."_element_first".$id);

																	if(isset($element_first))

																	{

																		$element_title=$input_get->getString($key."_element_title".$id);

																		if(isset($element_title))

																			$new_value = $input_get->getString($key."_element_title".$id).' '.$input_get->getString($key."_element_first".$id).' '.$input_get->getString($i."_element_last".$id).' '.$input_get->getString($i."_element_middle".$id);

																		else

																			$new_value = $input_get->getString($key."_element_first".$id).' '.$input_get->getString($key."_element_last".$id);

																	}	   

																	break;		

																}

																

																case "type_address":
															{
																$street1=$input_get->getString($key."_street1".$id);

																	if(isset($street1))

																	{

																		$new_value=$input_get->getString($key."_street1".$id);

																		break;

																	}
																	
																	$street2=$input_get->getString($key."_street2".$id);

																	if(isset($street2))

																	{

																		$new_value=$input_get->getString($key."_street2".$id);

																		break;

																	}	
																	
																	$city=$input_get->getString($key."_city".$id);
																	
																	if(isset($city))

																	{

																		$new_value=$input_get->getString($key."_city".$id);

																	break;

																	}
																	
																	$state=$input_get->getString($key."_state".$id);
																	
																	if(isset($state))

																	{

																		$new_value=$input_get->getString($key."_state".$id);

																		break;

																	}
																		
																		$postal=$input_get->getString($key."_postal".$id);
																	
																	if(isset($postal))

																	{

																		$new_value=$input_get->getString($key."_postal".$id);

																		break;
																	}	

																	
																	$country = $input_get->getString($key."_country".$id);
																	
																		if(isset($country))
																		{

																		$new_value=$input_get->getString($key."_country".$id);

																		break;
																		}
																	

																	break;

															}
															

																case "type_date_fields":

																{

																	$day=$input_get->getString($key."_day".$id);

																	if(isset($day))

																	{

																		$new_value = $input_get->getString($key."_day".$id).'-'.$input_get->getString($key."_month".$id).'-'.$input_get->getString($key."_year".$id);

																	}

																	break;

																}

																

																case "type_radio":

																{

																	$element=$input_get->getString($key."_other_input".$id);

																	if(isset($element))

																	{

																		$new_value = $input_get->getString($key."_other_input".$id);

																		break;

																	}	

																	

																	$element=$input_get->getString($key."_element".$id);

																	if(isset($element))

																	{

																		$new_value = $element;					

																	}

																	break;	

																}

																

																case "type_checkbox":

																{

																							

																	$start=-1;

																	for($j=0; $j<100; $j++)

																	{

																		$element=$input_get->getString($key."_element".$id.$j);

																		if(isset($element))

																		{

																			$start=$j;

																			break;

																		}

																	}	

																	

																	$other_element_id=-1;

																	$is_other=$input_get->getString($key."_allow_other".$id);

																	if($is_other=="yes")

																	{

																		$other_element_id=$input_get->getString($key."_allow_other_num".$id);

																	}

																	

															

																	if($start!=-1)

																	{

																		for($j=$start; $j<100; $j++)

																		{

																			

																			$element=$input_get->getString($key."_element".$id.$j);

																			if(isset($element))

																			if($j==$other_element_id)

																			{

																				$new_value = $new_value.$input_get->getString($key."_other_input".$id).'<br>';

																			}

																			else

																			

																				$new_value = $new_value.$input_get->getString($key."_element".$id.$j).'<br>';

																		}

																		

																	}

																				

																	

																	break;

																}



																case "type_paypal_price":	

															{		

																$new_value=0;

																if($input_get->getString($key."_element_dollars".$id))

																	$new_value=$input_get->getString($key."_element_dollars".$id);

																

																if($input_get->getString($key."_element_cents".$id))

																	$new_value=$new_value.'.'.$input_get->getString($key."_element_cents".$id);

															

																$new_value=$new_value.$form_currency;





																	

																	

																break;

															}			

															

															case "type_paypal_select":	

															{	

													

																$new_value=$input_get->getString($key."_element_label".$id).':'.$input_get->getString($key."_element".$id).$form_currency;

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																if(isset($element_quantity_label))

																	$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($i."_element_property_value".$id.$k);

																	}

																}



											

																break;

															}

															

															case "type_paypal_radio":				

															{

																

																	$new_value=$input_get->getString($key."_element_label".$id).' - '.$input_get->getString($key."_element".$id).$form_currency;

																

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																	if(isset($element_quantity_label))

																		$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($key."_element_property_value".$id.$k);

																	}

																}

																break;

															}



															case "type_paypal_shipping":				

															{

																

																$new_value=$input_get->getString($i."_element_label".$id).' : '.$input_get->getString($i."_element".$id).$form_currency;

																

															

																break;

															}



															case "type_paypal_checkbox":				

															{

																												$start=-1;

																for($j=0; $j<100; $j++)

																{

																	$element=$input_get->getString($key."_element".$id.$j);

																	if(isset($element))

																	{

																		$start=$j;

																		break;

																	}

																}	

																

																if($start!=-1)

																{

																	for($j=$start; $j<100; $j++)

																	{

																		

																		$element=$input_get->getString($key."_element".$id.$j);

																		if(isset($element))

																		{

																			$new_value=$new_value.$input_get->getString($key."_element".$id.$j."_label").' - '.($input_get->getString($key."_element".$id.$j)=='' ? '0'.$form_currency : $input_get->getString($key."_element".$id.$j)).$form_currency.'<br>';

																		}

																	}

																}

																

																$element_quantity_label=$input_get->getString($key."_element_quantity_label".$id);

																	if(isset($element_quantity_label))

																		$new_value.='<br/>'.$input_get->getString($key."_element_quantity_label".$id).': '.$input_get->getString($key."_element_quantity".$id);

														

																for($k=0; $k<50; $k++)

																{

																	$temp_val=$input_get->getString($key."_element_property_value".$id.$k);

																	if(isset($temp_val))

																	{			

																		$new_value.='<br/>'.$input_get->getString($key."_element_property_label".$id.$k).': '.$input_get->getString($key."_element_property_value".$id.$k);

																	}

																}	

																

																break;

															}

															case "type_paypal_total":				

															{
															
															
																$element=$input_get->getString($key."_paypal_total".$id);

																
																$new_value=$new_value.$element;				



																break;

															}

																case "type_star_rating":
															{
																$element=$input_get->getString($key."_star_amount".$id);
																$selected=$input_get->getString($key."_selected_star_amount".$id,0);
																//$star_color=$input_get->getString($key."_star_color_id_temp");
																
																if(isset($element))
																{
																	$new_value=$new_value.$selected.'/'.$element;					
																}
																break;
															}
															

															case "type_scale_rating":
															{
															$element=$input_get->getString($key."_scale_amount".$id);
															$selected=$input_get->getString($key."_scale_radio".$id,0);
															
																
																if(isset($element))
																{
																	$new_value=$new_value.$selected.'/'.$element;					
																}
																break;
															}
															
															case "type_spinner":
															{

																$element=$input_get->getString($key."_element".$id);
																if(isset($element))
																{
																	$new_value=$new_value.$element;					
																}
																break;
															}
															
															case "type_slider":
															{

																$element=$input_get->getString($key."_slider_value".$id);
																if(isset($element))
																{
																	$new_value=$new_value.$element;					
																}
																break;
															}
															case "type_range":
															{

																$element0=$input_get->getString($key."_element".$id.'0');
																$element1=$input_get->getString($key."_element".$id.'1');
																if(isset($element0) || isset($element1))
																{
																	$new_value=$new_value.$element0.'-'.$element1;					
																}
																break;
															}
															
															case "type_grading":
															{
																$element=$input_get->getString($key."_hidden_item".$id);
																$grading = explode(":",$element);
																$items_count = sizeof($grading)-1;
																
																$element = "";
																$total = "";
																
																for($k=0;$k<$items_count;$k++)

																{
																	$element .= $grading[$k].":".$input_get->getString($key."_element".$id.$k)." ";
															$total += $input_get->getString($key."_element".$id.$k);
														}

														$element .="Total:".$total;

																											
														if(isset($element))
														{
															$new_value=$new_value.$element;					
														}
														break;
													}
													
														case "type_matrix":
													{
													
														
														$input_type=$input_get->getString($key."_input_type".$id); 
																				
														$mat_rows = $input_get->getString($key."_hidden_row".$id);
														$mat_rows = explode('***', $mat_rows);
														$mat_rows = array_slice($mat_rows,0, count($mat_rows)-1);
														
														$mat_columns = $input_get->getString($key."_hidden_column".$id);
														$mat_columns = explode('***', $mat_columns);
														$mat_columns = array_slice($mat_columns,0, count($mat_columns)-1);
												  
														$row_ids=explode(",",substr($input_get->getString($key."_row_ids".$id), 0, -1));
														$column_ids=explode(",",substr($input_get->getString($key."_column_ids".$id), 0, -1)); 
																				  
																	
														$matrix="<table>";
																	
															$matrix .='<tr><td></td>';
														
														for( $k=0;$k< count($mat_columns) ;$k++)
															$matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
															$matrix .='</tr>';
														
														$aaa=Array();
														   $k=0;
														foreach($row_ids as $row_id)
														{
														$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
														
														  if($input_type=="radio"){
														 
														$mat_radio = $input_get->getString($key."_input_element".$id.$row_id,0);											
														  if($mat_radio==0){
																$checked="";
																$aaa[1]="";
																}
																else{
																$aaa=explode("_",$mat_radio);
																}
																
																foreach($column_ids as $column_id){
																	if($aaa[1]==$column_id)
																	$checked="checked";
																	else
																	$checked="";
																$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
																
																}
																
															} 
															else{
															if($input_type=="checkbox")
															{                
																foreach($column_ids as $column_id){
																 $checked = $input_get->getString($key."_input_element".$id.$row_id.'_'.$column_id);                              
																 if($checked==1)							
																 $checked = "checked";						
																 else									 
																 $checked = "";

																$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
															
															}
															
															}
															else
															{
															if($input_type=="text")
															{
																					  
																foreach($column_ids as $column_id){
																 $checked = $input_get->getString($key."_input_element".$id.$row_id.'_'.$column_id);
																	
																$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
													
															}
															
															}
															else{
																foreach($column_ids as $column_id){
																 $checked = $input_get->getString($i."_select_yes_no".$id.$row_id.'_'.$column_id);
																	 $matrix .='<td style="text-align:center">'.$checked.'</td>';
																
												
															
																}
															}
															
															}
															
															}
															$matrix .='</tr>';
															$k++;
														}
														 $matrix .='</table>';

									
									
									
																										
														if(isset($matrix))
														{
															$new_value=$new_value.$matrix;					
														}
													
														break;
													}
												
												



																

																default: break;

															}

															

														$new_script = str_replace("%".$label_each."%", $new_value, $new_script);	

														}



													

												

												}

											}	

											if(strpos($new_script, "%ip%")>-1)
												$new_script = str_replace("%ip%", $ip, $new_script);	

											if(strpos($new_script, "%all%")!=-1)

												$new_script = str_replace("%all%", $list, $new_script);	



											$body      = $new_script;
								 $mode        = 1; 
            
								 $send=modFormmaker::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cca, $bcc, $attachment, $replyto, $replytoname); 
								} 
							}
		
			$succes=1;		
			if($row->mail)
				{
					if ( $send !== true ) 
					{
						$msg=JText::_('WDF_MAIL_SEND_ERROR');
						$succes = 0;
					}
					else 
						$msg=JText::_('WDF_MAIL_SENT');
				}
			else	
				$msg=JText::_('WDF_SUBMITTED');
		
		}
		
		switch($row->submit_text_type)
		{
					case "2":
					{
						$redirect_url=JUri::root()."index.php?option=com_content&view=article&id=".$row->article_id."&Itemid=".$Itemid;
						//$mainframe->redirect("index.php?option=com_content&view=article&id=".$row->article_id."&Itemid=".$Itemid, $msg);
						break;
					}
					case "3":
					{
						$_SESSION['show_submit_text'.$id]=1;
						$redirect_url=$_SERVER["HTTP_REFERER"];
						//$mainframe->redirect($_SERVER["REQUEST_URI"], $msg);
						break;
					}											
					case "4":
					{
						$redirect_url=$row->url;
						//$mainframe->redirect($row->url, $msg);
						break;
					}
					default:
					{
						$redirect_url=$_SERVER["HTTP_REFERER"];
						//$mainframe->redirect($_SERVER["REQUEST_URI"], $msg);
						break;
					}
		}	

			if(!$str)
			{
				if($msg == JText::_('WDF_SUBMITTED') || $msg == JText::_('WDF_MAIL_SENT'))
					$mainframe->redirect($redirect_url, $msg, 'message');
				else
					$mainframe->redirect($redirect_url, $msg, 'error');
			}
			else

			{

				$_SESSION['redirect_paypal'.$id]=1;
				$redirect_url.="&succes=".$succes;
				$str.="&return=".urlencode($redirect_url);

				$mainframe->redirect($str);



			}					
	}
	
	 public static function custom_fields_mail($type, $key, $id, $attachment)
	{
		$input_get = JFactory::getApplication()->input;
	
		$disabled_fields	= explode(',',$input_get->getString("disabled_fields".$id));		
		$disabled_fields 	= array_slice($disabled_fields,0, count($disabled_fields)-1);   
	
		if($type!="type_submit_reset" or $type!="type_map" or $type!="type_editor" or  $type!="type_captcha" or  $type!="type_recaptcha" or  $type!="type_button")
		{
			$new_value ="";
			if(!in_array($key,$disabled_fields))
			switch ($type)
			{
					case 'type_text':
					case 'type_password':
					case 'type_textarea':
					case "type_date":
					case "type_own_select":					
					case "type_country":				
					case "type_number":	
					{
						$element=$input_get->getString('wdform_'.$key."_element".$id);
						if(isset($element))
						{
							$new_value = $element;					
						}
						break;
					
					
					}
					case "type_file_upload":
					{
						if($attachment)
							foreach($attachment as $attachment_temp)
							{
								$uploadedFileNameParts = explode('.',$attachment_temp[1]);
								$uploadedFileExtension = array_pop($uploadedFileNameParts);
								
								$invalidFileExts = array('gif', 'jpg', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc', 'aiff', 'wbmp', 'xbm' );
								$extOk = false;

								foreach($invalidFileExts as $key => $valuee)
								{
									if(is_numeric(strpos(strtolower($valuee), strtolower($uploadedFileExtension) )) )
										$extOk = true;
								}
								 
								if ($extOk == true) 
									$new_value .= '<img src="'.JURI::root().'/'.$attachment_temp[0].'" alt="'.$attachment_temp[1].'"/>';
							}
						break;
					}
					case "type_hidden":				
					{
						$element=$input_get->getString($element_label);
						if(isset($element))
						{
							$new_value = $element;	
						}
						break;
					}
					
					
					case "type_mark_map":
					{
						$element=$input_get->getString('wdform_'.$key."_long".$id);
						if(isset($element))
						{
							$new_value = 'Longitude:'.$input_get->getString('wdform_'.$key."_long".$id).'<br/>Latitude:'.$input_get->getString('wdform_'.$key."_lat".$id);
						}
						break;		
					}
										
					case "type_submitter_mail":
					{
						$element=$input_get->getString('wdform_'.$key."_element".$id);
						if(isset($element))
						{
							$new_value = $element;					
						}
						break;		
					}
					
					case "type_time":
					{
						
						$hh=$input_get->getString('wdform_'.$key."_hh".$id);
						if(isset($hh))
						{
							$ss=$input_get->getString('wdform_'.$key."_ss".$id);
							if(isset($ss))
								$new_value = $input_get->getString('wdform_'.$key."_hh".$id).':'.$input_get->getString('wdform_'.$key."_mm".$id).':'.$input_get->getString('wdform_'.$key."_ss".$id);
							else
								$new_value = $input_get->getString('wdform_'.$key."_hh".$id).':'.$input_get->getString('wdform_'.$key."_mm".$id);
							$am_pm=$input_get->getString('wdform_'.$key."_am_pm".$id);
							if(isset($am_pm))
								$new_value=$new_value.' '.$input_get->getString('wdform_'.$key."_am_pm".$id);
							
						}
							
						break;
					}
					
					case "type_phone":
					{
						$element_first=$input_get->getString('wdform_'.$key."_element_first".$id);
						if(isset($element_first))
						{
								$new_value = $input_get->getString('wdform_'.$key."_element_first".$id).' '.$input_get->getString('wdform_'.$key."_element_last".$id);
						}	
						break;
					}
					
					case "type_name":
					{
						$element_first=$input_get->getString('wdform_'.$key."_element_first".$id);
						if(isset($element_first))
						{
							$element_title=$input_get->getString('wdform_'.$key."_element_title".$id);
							if(isset($element_title))
								$new_value = $input_get->getString('wdform_'.$key."_element_title".$id).' '.$input_get->getString('wdform_'.$key."_element_first".$id).' '.$input_get->getString('wdform_'.$key."_element_last".$id).' '.$input_get->getString('wdform_'.$key."_element_middle".$id);
							else
								$new_value = $input_get->getString('wdform_'.$key."_element_first".$id).' '.$input_get->getString('wdform_'.$key."_element_last".$id);
						}	   
						break;		
					}
					
					case "type_address":
					{

						$street1=$input_get->getString('wdform_'.$key."_street1".$id);

						if(isset($street1))

						{

							$new_value=$input_get->getString('wdform_'.$key."_street1".$id);

							break;

						}
						
						$street2=$input_get->getString('wdform_'.$key."_street2".$id);

						if(isset($street2))

						{

							$new_value=$input_get->getString('wdform_'.$key."_street2".$id);

							break;

						}	
						
						$city=$input_get->getString('wdform_'.$key."_city".$id);
						
						if(isset($city))

						{

							$new_value=$input_get->getString('wdform_'.$key."_city".$id);

							break;

						}
						
						$state=$input_get->getString('wdform_'.$key."_state".$id);
						
						if(isset($state))

						{

							$new_value=$input_get->getString('wdform_'.$key."_state".$id);

							break;

						}
							
							$postal=$input_get->getString('wdform_'.$key."_postal".$id);
						
						if(isset($postal))

						{

							$new_value=$input_get->getString('wdform_'.$key."_postal".$id);

							break;

						}	

						
						$country = $input_get->getString('wdform_'.$key."_country".$id);
						
							if(isset($country))
							{

							$new_value=$input_get->getString('wdform_'.$key."_country".$id);

							break;		
							}
						

						break;

					}

					case "type_date_fields":
					{
						$day=$input_get->getString('wdform_'.$key."_day".$id);
						if(isset($day))
						{
							$new_value = $input_get->getString('wdform_'.$key."_day".$id).'-'.$input_get->getString('wdform_'.$key."_month".$id).'-'.$input_get->getString('wdform_'.$key."_year".$id);
						}
						break;
					}
					
					case "type_radio":
					{
						$element=$input_get->getString('wdform_'.$key."_other_input".$id);
						if(isset($element))
						{
							$new_value = $input_get->getString('wdform_'.$key."_other_input".$id);
							break;
						}	
						
						$element=$input_get->getString('wdform_'.$key."_element".$id);
						if(isset($element))
						{
							$new_value = $element;					
						}
						break;	
					}
					
					case "type_checkbox":
					{
												
						$start=-1;
						for($j=0; $j<100; $j++)
						{
							$element=$input_get->getString('wdform_'.$key."_element".$id.$j);
							if(isset($element))
							{
								$start=$j;
								break;
							}
						}	
						
						$other_element_id=-1;
						$is_other=$input_get->getString('wdform_'.$key."_allow_other".$id);
						if($is_other=="yes")
						{
							$other_element_id=$input_get->getString('wdform_'.$key."_allow_other_num".$id);
						}
						
				
						if($start!=-1)
						{
							for($j=$start; $j<100; $j++)
							{
								
								$element=$input_get->getString('wdform_'.$key."_element".$id.$j);
								if(isset($element))
								if($j==$other_element_id)
								{
									$new_value = $new_value.$input_get->getString('wdform_'.$key."_other_input".$id).'<br>';
								}
								else
								
									$new_value = $new_value.$input_get->getString('wdform_'.$key."_element".$id.$j).'<br>';
							}
							
						}
						break;
						}
						case "type_paypal_price":	
					{		
						$new_value=0;
						if($input_get->getString('wdform_'.$key."_element_dollars".$id))
							$new_value=$input_get->getString('wdform_'.$key."_element_dollars".$id);
						
						if($input_get->getString('wdform_'.$key."_element_cents".$id))
							$new_value=$new_value.'.'.$input_get->getString('wdform_'.$key."_element_cents".$id);
					
						$new_value=$new_value.$form_currency;


							
							
						break;

					}			

					case "type_paypal_select":	
					{	
						$new_value=$input_get->getString('wdform_'.$key."_element_label".$id).':'.$input_get->getString('wdform_'.$key."_element".$id).$form_currency;
						$element_quantity_label=$input_get->getString('wdform_'.$key."_element_quantity_label".$id);
						if(isset($element_quantity_label))
							$new_value.='<br/>'.$input_get->getString('wdform_'.$key."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$key."_element_quantity".$id);
				
						for($k=0; $k<50; $k++)
						{
							$temp_val=$input_get->getString('wdform_'.$key."_property".$id.$k);
							if(isset($temp_val))
							{			
								$new_value.='<br/>'.$input_get->getString('wdform_'.$key."_element_property_label".$id.$k).': '.$input_get->getString($i."_property".$id.$k);
							}
						}

						break;
						
					}
			
					case "type_paypal_radio":				
					{
								
						$new_value=$input_get->getString('wdform_'.$key."_element_label".$id).' - '.$input_get->getString('wdform_'.$key."_element".$id).$form_currency;
						
						$element_quantity_label=$input_get->getString('wdform_'.$key."_element_quantity_label".$id);
							if(isset($element_quantity_label))
								$new_value.='<br/>'.$input_get->getString('wdform_'.$key."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$key."_element_quantity".$id);
				
						for($k=0; $k<50; $k++)
						{
							$temp_val=$input_get->getString('wdform_'.$key."_property".$id.$k);
							if(isset($temp_val))
							{			
								$new_value.='<br/>'.$input_get->getString('wdform_'.$key."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$key."_property".$id.$k);
							}
						}
						
						break;	
					}

					case "type_paypal_shipping":				
					{
						
						$new_value=$input_get->getString('wdform_'.$key."_element_label".$id).' : '.$input_get->getString('wdform_'.$key."_element".$id).$form_currency;		

						break;
					}

					case "type_paypal_checkbox":				
					{										
						$start=-1;
						for($j=0; $j<100; $j++)
						{
							$element=$input_get->getString('wdform_'.$key."_element".$id.$j);
							if(isset($element))
							{
								$start=$j;
								break;
							}
						}	
						
						if($start!=-1)
						{
							for($j=$start; $j<100; $j++)
							{
								
								$element=$input_get->getString('wdform_'.$key."_element".$id.$j);
								if(isset($element))
								{
									$new_value=$new_value.$input_get->getString('wdform_'.$key."_element".$id.$j."_label").' - '.($input_get->getString('wdform_'.$key."_element".$id.$j)=='' ? '0'.$form_currency : $input_get->getString('wdform_'.$key."_element".$id.$j)).$form_currency.'<br>';
								}
							}
						}
						
						$element_quantity_label=$input_get->getString('wdform_'.$key."_element_quantity_label".$id);
							if(isset($element_quantity_label))
								$new_value.='<br/>'.$input_get->getString('wdform_'.$key."_element_quantity_label".$id).': '.$input_get->getString('wdform_'.$key."_element_quantity".$id);
				
						for($k=0; $k<50; $k++)
						{
							$temp_val=$input_get->getString('wdform_'.$key."_property".$id.$k);
							if(isset($temp_val))
							{			
								$new_value.='<br/>'.$input_get->getString('wdform_'.$key."_element_property_label".$id.$k).': '.$input_get->getString('wdform_'.$key."_property".$id.$k);
							}
						}
													
				
						break;
					}			
							
					case "type_paypal_total":				

					{
					
					
						$element=$input_get->getString('wdform_'.$key."_paypal_total".$id);

						
						$new_value=$new_value.$element;				



						break;

					}
					
					case "type_star_rating":
				{
					$element=$input_get->getString('wdform_'.$key."_star_amount".$id);
					$selected=$input_get->getString('wdform_'.$key."_selected_star_amount".$id,0);
					//$star_color=$input_get->getString($key."_star_color_id_temp");
					
					if(isset($element))
					{
						$new_value=$new_value.$selected.'/'.$element;					
					}
					break;
				}
				

				case "type_scale_rating":
				{
				$element=$input_get->getString('wdform_'.$key."_scale_amount".$id);
				$selected=$input_get->getString('wdform_'.$key."_scale_radio".$id,0);
				
					
					if(isset($element))
					{
						$new_value=$new_value.$selected.'/'.$element;					
					}
					break;
				}
				
				case "type_spinner":
				{

					$element=$input_get->getString('wdform_'.$key."_element".$id);
					if(isset($element))
					{
						$new_value=$new_value.$element;					
					}
					break;
				}
				
				case "type_slider":
				{

					$element=$input_get->getString('wdform_'.$key."_slider_value".$id);
					if(isset($element))
					{
						$new_value=$new_value.$element;					
					}
					break;
				}
				case "type_range":
				{

					$element0=$input_get->getString('wdform_'.$key."_element".$id.'0');
					$element1=$input_get->getString('wdform_'.$key."_element".$id.'1');
					if(isset($element0) || isset($element1))
					{
						$new_value=$new_value.$element0.'-'.$element1;					
					}
					break;
				}
				
				case "type_grading":
				{
					$element=$input_get->getString('wdform_'.$key."_hidden_item".$id);
					$grading = explode(":",$element);
					$items_count = sizeof($grading)-1;
					
					$element = "";
					$total = "";
					
					for($k=0;$k<$items_count;$k++)

					{
						$element .= $grading[$k].":".$input_get->getString('wdform_'.$key."_element".$id.'_'.$k)." ";
				$total += $input_get->getString('wdform_'.$key."_element".$id.'_'.$k);
			}

			$element .="Total:".$total;

																
			if(isset($element))
			{
				$new_value=$new_value.$element;					
			}
			break;
		}
		
			case "type_matrix":
				{
				
					$input_type=$input_get->getString('wdform_'.$key."_input_type".$id); 
						
					$mat_rows=explode("***",$input_get->getString('wdform_'.$key."_hidden_row".$id));
					$rows_count= sizeof($mat_rows)-1;
					$mat_columns=explode("***",$input_get->getString('wdform_'.$key."_hidden_column".$id));
					$columns_count= sizeof($mat_columns)-1;

								
					$matrix="<table>";
								
						$matrix .='<tr><td></td>';
				
			
			
						for( $k=1;$k< count($mat_columns) ;$k++)
							$matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
							$matrix .='</tr>';
						
						$aaa=Array();
						
						for($k=1; $k<=$rows_count; $k++)
						{
						$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
						
							if($input_type=="radio")
							{
						
						
								$mat_radio = $input_get->getString('wdform_'.$key."_input_element".$id.$k,0);											
								if($mat_radio==0)
								{
									$checked="";
									$aaa[1]="";
								}
								else
								$aaa=explode("_",$mat_radio);
								
								
								for($j=1; $j<=$columns_count; $j++)
								{
									if($aaa[1]==$j)
									$checked="checked";
									else
									$checked="";
									
									$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
								
								}
						
							} 
							else
							{
								if($input_type=="checkbox")
								{                
									for($j=1; $j<=$columns_count; $j++)
									{
										$checked = $input_get->getString('wdform_'.$key."_input_element".$id.$k.'_'.$j);                     
										if($checked==1)						
										$checked = "checked";				
										else								
										$checked = "";

										$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
								
									}
							
								}
								else
								{
									if($input_type=="text")
									{
															  
										for($j=1; $j<=$columns_count; $j++)
										{
											$checked = $input_get->getString('wdform_'.$key."_input_element".$id.$k.'_'.$j);
											$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
							
										}
									
									}
									else{
										for($j=1; $j<=$columns_count; $j++)
										{
											$checked = $input_get->getString('wdform_'.$key."_select_yes_no".$id.$k.'_'.$j);
											$matrix .='<td style="text-align:center">'.$checked.'</td>';
										
						
									
										}
									}
								
								}
								
							}
							$matrix .='</tr>';
						
						}
						 $matrix .='</table>';

		
																	
					if(isset($matrix))
					{
						$new_value=$new_value.$matrix;					
					}
				
					break;
				}
				
					default: break;
			}
						
		}

		return $new_value;
	}
	
	

	 public static function sendMail(&$from, &$fromname, &$recipient, &$subject, &$body, &$mode=0, &$cc=null, &$bcc=null, &$attachment=null, &$replyto=null, &$replytoname=null)
    {
		$input_get = JFactory::getApplication()->input;
				$recipient=explode (',', str_replace(' ', '', $recipient ));
				$cc=explode (',', str_replace(' ', '', $cc ));
				$bcc=explode (',', str_replace(' ', '', $bcc ));
                // Get a JMail instance
                $mail = JFactory::getMailer();
 
				if(filter_var($from, FILTER_VALIDATE_EMAIL))
				$mail->setSender(array($from, $fromname));
			
                $mail->setSubject($subject);
                $mail->setBody($body);
 
                // Are we sending the email as HTML?
                if ($mode) {
                        $mail->IsHTML(true);
                }
 
			if(filter_var($recipient[0], FILTER_VALIDATE_EMAIL))	
              $mail->addRecipient($recipient);
		
			if(filter_var($cc[0], FILTER_VALIDATE_EMAIL))
              $mail->addCC($cc);
		  
			if(filter_var($bcc[0], FILTER_VALIDATE_EMAIL))
             $mail->addBCC($bcc);
				
				if($attachment)
					foreach($attachment as $attachment_temp)
					{
						$mail->AddEmbeddedImage($attachment_temp[0], $attachment_temp[1], $attachment_temp[2]);
					}
 
                // Take care of reply email addresses
				if (is_array($replyto)) {
                        $numReplyTo = count($replyto);
                        for ($i=0; $i < $numReplyTo; $i++){
							if(filter_var($replyto[$i], FILTER_VALIDATE_EMAIL))
                                $mail->addReplyTo($replyto[$i]);
                        }
                } elseif (isset($replyto) and filter_var($replyto, FILTER_VALIDATE_EMAIL) ) {
                        $mail->addReplyTo($replyto);
                }
 
                return  $mail->Send();
        }
		
	 public static function remove($group_id)
	{
		$input_get = JFactory::getApplication()->input;
		$db = JFactory::getDBO();
		$db->setQuery('DELETE FROM #__formmaker_submits WHERE group_id="'.$db->escape((int)$group_id).'"');
		$db->query();
	}
	
	public static function checkpaypal()
	{
	
		$input_get = JFactory::getApplication()->input;
	//	$File = "components/com_formmaker/models/request.txt"; 
 	//	$Handle = fopen($File, 'w');
	
		$id=(int)$input_get->getString( 'form_id',0);
		$group_id=(int)$input_get->getString( 'group_id',0);
		
		$form = JTable::getInstance('formmaker', 'Table');
		$form->load( $id);
		


	if($form->checkout_mode=="production")
		$paypal_action="https://www.paypal.com/cgi-bin/webscr";
	else
		$paypal_action="https://www.sandbox.paypal.com/cgi-bin/webscr";
		
	
	$payment_status=$input_get->getString( 'payment_status','');
	
		
	$postdata=""; 
	
	foreach (JRequest::get('post') as $key=>$value) 
		$postdata.=$key."=".urlencode($value)."&"; 
		
	$postdata .= "cmd=_notify-validate"; 
	$curl = curl_init($paypal_action); 
	curl_setopt ($curl, CURLOPT_HEADER, 0); 
	curl_setopt ($curl, CURLOPT_POST, 1); 
	curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata); 
	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0); 
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1); 
	$response = curl_exec ($curl); 
	curl_close ($curl); 
	
		$option			=$input_get->getString('option');
		$total			=$input_get->getString( 'mc_gross');
		$tax_total		=$input_get->getString( 'tax');
		$shipping_total	=$input_get->getString( 'mc_shipping');

		$refresh=0;

		$tax=0;

		$shipping=0;

		$total_cost=0;

		$total_count=0;
		

		$form_currency='$';
		$currency_code=array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
		$currency_sign=array('$'  , '€'  , '£'  , '¥'  , 'C$', 'Mex$', 'HK$', 'Ft' , 'kr' , 'NZ$', 'S$' , 'kr' , 'zł' , 'A$' , 'kr' , 'CHF' , 'Kč', '₪'  , 'R$' , 'NT$', 'RM' , '₱'  , '฿'  );
		
		if($form->payment_currency)
			$form_currency=	$currency_sign[array_search($form->payment_currency, $currency_code)];

		$tax=$form->tax;
		$shipping=$input_get->getString( 'mc_shipping',0);

		$db = JFactory::getDBO();

		$query = "UPDATE #__formmaker_submits SET `element_value`='".$db->escape($payment_status)."' WHERE group_id='".$db->escape((int)$group_id)."' AND element_label='0'";

		$db->setQuery( $query);
		$db->query();
		if($db->getErrorNum()){	echo $db->stderr();	return false;}

	

			$row = JTable::getInstance('sessions', 'Table');
			$query = "SELECT id FROM #__formmaker_sessions WHERE group_id=".$group_id;
			$db->setQuery( $query);
			$ses_id=$db->LoadResult();
			if($db->getErrorNum()){	echo $db->stderr();	return false;}
			
			if($ses_id)
				$row->load( $ses_id);
			$row->form_id=$id;			$row->group_id=$group_id;

			$row->full_name=$input_get->getString( 'first_name','')." ".$input_get->getString( 'last_name','');
			
			
			
			
			$row->email=$input_get->getString( 'payer_email','');

			$row->phone=$input_get->getString( 'night_ phone_a','')."-".$input_get->getString( 'night_ phone_b','')."-".$input_get->getString( 'night_ phone_c','');

			$row->address="Country: ".$input_get->getString( 'address_country','')."<br>";
			if($input_get->getString( 'address_state','')!="")
			$row->address.="State: ".$input_get->getString( 'address_state','')."<br>";
			if($input_get->getString( 'City','')!="")
			$row->address.="City: ".$input_get->getString( 'address_city','')."<br>";
			if($input_get->getString( 'address_street','')!="")
			$row->address.="Street: ".$input_get->getString( 'address_street','')."<br>";
			if($input_get->getString( 'address_zip','')!="")
			$row->address.="Zip Code: ".$input_get->getString( 'address_zip','')."<br>";
			if($input_get->getString( 'address_zip','')!="")
			$row->address.="Address Status: ".$input_get->getString( 'address_status','')."<br>";
			if($input_get->getString( 'address_name','')!="")
			$row->address.="Name: ".$input_get->getString( 'address_name','')."";
			$row->status =$input_get->getString( 'payment_status','');
			
			
			$row->ipn =$response;
			$row->currency =$form->payment_currency.' - '.$form_currency;
			
			$row->paypal_info ="";
			
			
			if($input_get->getString( 'payer_status','')!="")
			$row->paypal_info .= "Payer Status -".$input_get->getString( 'payer_status','')."<br>";
		
			if($input_get->getString( 'payer_email','')!="")
			$row->paypal_info .= "Payer Email -".$input_get->getString( 'payer_email','')."<br>";
		
			if($input_get->getString( 'txn_id','')!="")
			$row->paypal_info .= "Transaction -".$input_get->getString( 'txn_id','')."<br>";
			
			if($input_get->getString( 'payment_type','')!="")
			$row->paypal_info .= "Payment Type -".$input_get->getString( 'payment_type','')."<br>";
			
			if($input_get->getString( 'residence_country','')!="")
			$row->paypal_info .= "Residence Country -".$input_get->getString( 'residence_country','')."<br>";
			
			$row->ord_last_modified = date( 'Y-m-d H:i:s' );

			$row->tax = $tax;

			$row->shipping = $shipping;
			
			$row->total = $total;

		if (!$row->store())
		{	
		
		echo "<script> alert('".$row->getError()."');
			window.history.go(-1); </script>\n";
			exit();
		}
		
		
		
		$list='
		<table class="admintable" border="1" >
	<tr>
		<td class="key">Currency</td>
		<td> '.$row->currency.'</td>
	</tr>
	<tr>
		<td class="key">Date</td>
		<td> '.$row->ord_last_modified.'</td>
	</tr>

	<tr>
		<td class="key">Status</td>
		<td> '.$row->status.'</td>
	</tr>
	<tr>
		<td class="key">Full name</td>
		<td> '.$row->full_name.'</td>
	</tr>
	<tr>
		<td class="key">Email</td>
		<td> '.$row->email.'</td>
	</tr>
	<tr>
		<td class="key">Phone</td>
		<td> '.$row->phone.'</td>
	</tr>
	<tr>
		<td class="key">Mobile phone</td>
		<td> '.$row->mobile_phone.'</td>
	</tr>
	<tr>
		<td class="key">Fax</td>
		<td> '.$row->fax.'</td>
	</tr>
	<tr>
		<td class="key">Address</td>
		<td> '.$row->address.'</td>
	</tr>
	<tr>
		<td class="key">Paypal info</td>
		<td> '.$row->paypal_info.'</td>
	</tr>	
	<tr>
		<td class="key">IPN</td>
		<td> '.$row->ipn.'</td>
	</tr>
	<tr>
		<td class="key">tax</td>
		<td> '.$row->tax.'%</td>
	</tr>
	<tr>
		<td class="key">shipping</td>
		<td> '.$row->shipping.'</td>
	</tr>
	
	<tr>
		<td class="key"><b>Item total</b></td>
		<td> '.($total-$tax_total-$shipping_total).$form_currency.'</td>
	</tr>
	<tr>
		<td class="key"><b>Tax</b></td>
		<td> '.$tax_total.$form_currency.'</td>
	</tr>
	<tr>
		<td class="key"><b>Shipping and handling</b></td>
		<td> '.$shipping_total.$form_currency.'</td>
	</tr>
	<tr>
		<td class="key"><b>Total</b></td>
		<td> '.$total.$form_currency.'</td>
	</tr>
</table>
';

		
		if($form->mail)
		{
										
						
							$config = JFactory::getConfig();
							if($form->mail_from)
							$site_mailfrom = $form->mail_from;
							else
							$site_mailfrom=$config->get( 'config.mailfrom' );								
	
							
							
											 $from      = $site_mailfrom;
											 $fromname  = '';
										
											 $recipient = $form->mail;
											 $cca = $form->mail_cc;
											 $bcc = $form->mail_bcc;
											 $subject   = "Payment information";
											 $body      = $list;
											 $mode      = 1; 

											$send=modFormmaker::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cca, $bcc, $attachment, $replyto, $replytoname);  
		
		}

		
	//	fwrite($Handle, $req); 
	//	fclose($Handle); 

		return 0;
	 
	}

	
	
	 public static function defaultphp($row, $Itemid, $label_id,$label_type, $form_theme, $id, $ok)
	{
		ob_start();
        static $embedded;
        if(!$embedded)
        {
            $embedded=true;
        }
	?>
    
<?php 
$input_get = JFactory::getApplication()->input;

@session_start();
$mainframe = JFactory::getApplication();

$old = false;

if(isset($_SESSION['redirect_paypal'.$id]))
	if($_SESSION['redirect_paypal'.$id]==1)
	{
		$_SESSION['redirect_paypal'.$id]=0;
		$succes=$input_get->getString('succes');
		if(isset($succes))
			if($succes==0)
			{
				JError::raiseWarning( 100, JText::_('WDF_MAIL_SEND_ERROR') );
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(JText::_('WDF_SUBMITTED'));
			}
	}

if(isset($_SESSION['show_submit_text'.$id]))
	if($_SESSION['show_submit_text'.$id]==1)
	{
		$_SESSION['show_submit_text'.$id]=0;
		echo $row->submit_text;
		return;
	}
	
		$db = JFactory::getDBO();
		$db->setQuery("SELECT `views` FROM #__formmaker_views WHERE form_id=".$db->escape((int)$id) ); 
		$views = $db->loadResult();
		if ($db->getErrorNum())	{echo $db->stderr(); return false;}	
	
		if($views==NULL)
		{
			$db->setQuery("INSERT INTO #__formmaker_views (form_id, views) VALUES('".$id."', 0)"); 
			$db->query();
			if ($db->getErrorNum())	{echo $db->stderr();	return false;}
		}
		else
		{
			$db->setQuery("UPDATE #__formmaker_views SET  views=".($views+1)." where form_id=".$db->escape((int)$id) ); 
			$db->query();
			if ($db->getErrorNum())	{echo $db->stderr();	return false;}
		}
		
		

		$document = JFactory::getDocument();

		$cmpnt_js_path = JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl';
		$document->addStyleSheet($cmpnt_js_path.'/jquery-ui-spinner.css');
		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/wdform.js');
		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/jquery-ui.js');
		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/noconflict.js');
		$document->addScript(JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/if_gmap.js');
		$document->addScript( JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/min.js');
		$document->addScript( JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/file-upload.js');
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
			$document->addScript('https://maps.google.com/maps/api/js?sensor=false');
		else	
			$document->addScript('http://maps.google.com/maps/api/js?sensor=false');
		
		JHTML::_('behavior.tooltip');	
		JHTML::_('behavior.calendar');

		$is_editor=false;

		$plugin = JPluginHelper::getPlugin('editors', 'tinymce');
		if (isset($plugin->type))
		{ 
			$editor	= JFactory::getEditor('tinymce');
			$is_editor=true;
		}

		$editor	= JFactory::getEditor('tinymce');

		
		$document->addStyleSheet($cmpnt_js_path.'/jquery-ui-spinner.css');
		
		if(isset($row->form) )
			$old = true;
			
			$article=$row->article_id;
			echo '<script type="text/javascript">'.str_replace('form_id_temp', $id,$row->javascript).'</script>';
	

			$css_rep1=array("[SITE_ROOT]", "}");
			$css_rep2=array(JURI::root(true), "}#form".$id." ");
			$order   = array("\r\n", "\n", "\r");
			$form_theme=str_replace($order,'',$form_theme);
			$form_theme=str_replace($css_rep1,$css_rep2,$form_theme);
			$form_theme="#form".$id.' '.$form_theme;
			$form_currency='$';
			$check_js='';
			$onload_js='';
			$onsubmit_js='';

			$currency_code=array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');

		$currency_sign=array('$'  , '€'  , '£'  , '¥'  , 'C$', 'Mex$', 'HK$', 'Ft' , 'kr' , 'NZ$', 'S$' , 'kr' , 'zł' , 'A$' , 'kr' , 'CHF' , 'Kč', '₪'  , 'R$' , 'NT$', 'RM' , '₱'  , '฿'  );
			

			if($row->payment_currency)

				$form_currency=	$currency_sign[array_search($row->payment_currency, $currency_code)];

			$form_paypal_tax = $row->tax;		
				
			echo '<style>'.$form_theme.'
			
			img
{
max-width:none;
}

.mini_label
{
display: inline;
}

			</style>';

//			echo '<h3>'.$row->title.'</h3><br />';
?>
<form name="form<?php echo $id; ?>" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post" id="form<?php echo $id; ?>" enctype="multipart/form-data" onsubmit="check_required<?php echo $id ?>('submit', '<?php echo $id; ?>'); return false;">
<div id="<?php echo $id; ?>pages" class="wdform_page_navigation" show_title="<?php echo $row->show_title; ?>" show_numbers="<?php echo $row->show_numbers; ?>" type="<?php echo $row->pagination; ?>"></div>
<input type="hidden" id="counter<?php echo $id ?>" value="<?php echo $row->counter?>" name="counter<?php echo $id ?>" />
<input type="hidden" id="Itemid<?php echo $id ?>" value="<?php echo $Itemid?>" name="Itemid<?php echo $id ?>" />

<?php


	if($old == false || ($old == true && $row->form=='')) 
	{
		$document->addScript( JURI::root(true).'/components/com_formmaker/views/formmaker/tmpl/main_div.js');
			
		
	
	$is_type	= array();
	$id1s	 	= array();
	$types 		= array();
	$labels 	= array();
	$paramss 	= array();
	$required_sym=$row->requiredmark;
	$fields=explode('*:*new_field*:*',$row->form_fields);
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
	
	$form_id=$id;	
	
	$show_hide	= array();
	$field_label	= array();
	$all_any 	= array();
	$condition_params 	= array();
	$type_and_id = array();
	
	$condition_js='';
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

		foreach($id1s as $id1s_key => $id1)
		{	
			$type_and_id[$id1]=$types[$id1s_key];
		}
		
		for($k=0; $k<$count_of_conditions; $k++)
		{	
	
			if($show_hide[$k])
			{
				$display = 'removeAttr("style")';
				$display_none = 'css("display", "none")';
			}
			else
			{
				$display = 'css("display", "none")';
				$display_none = 'removeAttr("style")';
			}

			if($all_any[$k]=="and")
				$or_and = '&&';
			else
				$or_and = '||';
				
				if($condition_params[$k])
				{
					$cond_params =explode('*:*next_condition*:*',$condition_params[$k]);
					$cond_params 	= array_slice($cond_params,0, count($cond_params)-1); 
					for($l=0; $l<count($cond_params); $l++)
					{
						$params_value = explode('***',$cond_params[$l]);
						if(!isset($type_and_id[$params_value[0]]))
							unset($cond_params[$l]);
					}
					$cond_params = array_values($cond_params);
					$if = '';
					$keyup = '';
					$change = '';
					$click = '';
					
					for($m=0; $m<count($cond_params); $m++)
					{				
						$params_value = explode('***',$cond_params[$m]);
					
						switch($type_and_id[$params_value[0]])
						{
							case "type_text":
							case "type_password":
							case "type_textarea":
							case "type_number":
							case "type_submitter_mail":	
							case "type_spinner":	
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";						
									}		
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}
								$keyup .= '#wdform_'.$params_value[0].'_element'.$form_id.', ';	
								if($type_and_id[$params_value[0]] == "type_spinner")
									$click .= '#wdform_'.$params_value[0].'_element'.$form_id.' ~ a, ';
							break;
							
							case "type_name":

								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$extended0 = '';
									$extended1 = '';
									$extended2 = '';
									$extended3 = '';
									$normal0 = '';
									$normal1 = '';
									$normal2 = '';
									$normal3 = '';
									
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$name_fields = explode(' ',$params_value[2]);
									if($name_fields[0]!='')
									{
										$extended0 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").val().indexOf("'.$name_fields[0].'")'.$like_or_not.'-1 ';
										$normal0 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$name_fields[0].'")'.$like_or_not.'-1 ';
									}
									
									if(isset($name_fields[1]) && $name_fields[1]!='')
									{
										$extended1 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$name_fields[1].'")'.$like_or_not.'-1 ';
										$normal1 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val().indexOf("'.$name_fields[1].'")'.$like_or_not.'-1 ';
									}
									
									if(isset($name_fields[2]) && $name_fields[2]!='')
									{
										$extended2 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val().indexOf("'.$name_fields[2].'")'.$like_or_not.'-1 ';
										$normal2 = '';
									}
									
									if(isset($name_fields[3]) && $name_fields[3]!='')
									{
										$extended3 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").val().indexOf("'.$name_fields[3].'")'.$like_or_not.'-1 ';
										$normal3 = '';
									}

									if(isset($name_fields[3]))
									{
										$extended ='';
										$normal ='';
	
										if($extended0)
										{	
											$extended = $extended0;
											if($extended1)
											{
												$extended .= ' && '.$extended1;
												if($extended2)
													$extended .= ' && '.$extended2;	
													
												if($extended3)
													$extended .= ' && '.$extended3;
											}
											else
											{
												if($extended2)
													$extended .= ' && '.$extended2;
												if($extended3)
													$extended .= ' && '.$extended3;	
											}
										}
										else
										{
											if($extended1)
											{	
												$extended = $extended1;
												if($extended2)
													$extended .= ' && '.$extended2;
													
												if($extended3)
													$extended .= ' && '.$extended3;
											}
											else
											{
												if($extended2)
												{
													$extended = $extended2;
													if($extended3)
														$extended .= ' && '.$extended3;
												}
												else
													if($extended3)
														$extended = $extended3;
											}		
										}
									
										if($normal0)
										{	
											$normal = $normal0;
											if($normal1)
												$normal .= ' && '.$normal1;
										}
										else
										{
											if($normal1)
												$normal = $normal1;			
										}
									}
									else
									{
										if(isset($name_fields[2]))
										{
											$extended ="";
											$normal ="";
											if($extended0)
											{	
												$extended = $extended0;	
												if($extended1)
													$extended .= ' && '.$extended1;

												if($extended2)
														$extended .= ' && '.$extended2;

											}
											else
											{
												if($extended1)
												{
													$extended = $extended1;
													if($extended2)
														$extended .= ' && '.$extended2;	
												}		
												else
													if($extended2)
														$extended = $extended2;	
											}
											
											
											if($normal0)
											{	
												$normal = $normal0;	
												if($normal1)
													$normal .= ' && '.$normal1;
											}
											else
											{
												if($normal1)
													$normal = $normal1;
											}
											
										}
										else
										{
											if(isset($name_fields[1]))
											{
												$extended ='';
												$normal ='';
												if($extended0)
												{	
													if($extended1)
														$extended = $extended0.' && '.$extended1;
													else
														$extended = $extended0;
												}
												else
												{
													if($extended1)
														$extended = $extended1;
												}
												
												
												if($normal0)
												{	
													if($normal1)
														$normal = $normal0.' && '.$normal1;
													else
														$normal = $normal0;
												}
												else
												{
													if($normal1)
														$normal = $normal1;
												}
											}
											else
											{
												$extended = $extended0;
												$normal = $normal0;
											}
										}	
									}
		
									if($extended!="" && $normal!="")			
										$if .= ' ((wdformjQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").length != 0) ?  '.$extended.' : '.$normal.') ';
									else
										$if .= ' true';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$name_and_or = $params_value[1] == "=" ? "&&" : "||";			
										$name_empty_or_not = $params_value[1]."=";	
										$extended = ' (wdformjQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").val()'.$name_empty_or_not.'"" '.$name_and_or.' wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$name_empty_or_not.'"" '.$name_and_or.' wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$name_empty_or_not.'"" '.$name_and_or.' wdformjQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").val()'.$name_empty_or_not.'"") ';		
										$normal = ' (wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$name_empty_or_not.'"" '.$name_and_or.' wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$name_empty_or_not.'"") ';
										
										$if .= ' ((wdformjQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").length != 0) ?  '.$extended.' : '.$normal.') ';
									}
									else
									{
										$extended0 = '';
										$extended1 = '';
										$extended2 = '';
										$extended3 = '';
										$normal0 = '';
										$normal1 = '';
										$normal2 = '';
										$normal3 = '';
									
										$name_fields = explode(' ',$params_value[2]);
										if($name_fields[0]!='')
										{
											$extended0 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[0].'"';
											$normal0 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[0].'"';
										}
										
										if(isset($name_fields[1]) && $name_fields[1]!='')
										{
											$extended1 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[1].'"';
											$normal1 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[1].'"';
										}
										
										if(isset($name_fields[2]) && $name_fields[2]!='')
										{
											$extended2 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[2].'"';
											$normal2 = '';
										}
										
										if(isset($name_fields[3]) && $name_fields[3]!='')
										{			
											$extended3 = 'wdformjQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[3].'"';
											$normal3 = '';
										}
										
										
										if(isset($name_fields[3]))
										{
											$extended ='';
											$normal ='';
		
											if($extended0)
											{	
												$extended = $extended0;
												if($extended1)
												{
													$extended .= ' && '.$extended1;
													if($extended2)
														$extended .= ' && '.$extended2;	
														
													if($extended3)
														$extended .= ' && '.$extended3;
												}
												else
												{
													if($extended2)
														$extended .= ' && '.$extended2;
													if($extended3)
														$extended .= ' && '.$extended3;	
												}
											}
											else
											{
												if($extended1)
												{	
													$extended = $extended1;
													if($extended2)
														$extended .= ' && '.$extended2;
														
													if($extended3)
														$extended .= ' && '.$extended3;
												}
												else
												{
													if($extended2)
													{
														$extended = $extended2;
														if($extended3)
															$extended .= ' && '.$extended3;
													}
													else
														if($extended3)
															$extended = $extended3;
												}		
											}
										
											if($normal0)
											{	
												$normal = $normal0;
												if($normal1)
													$normal .= ' && '.$normal1;
											}
											else
											{
												if($normal1)
													$normal = $normal1;			
											}
										}
										else
										{
											if(isset($name_fields[2]))
											{
												$extended ="";
												$normal ="";
												if($extended0)
												{	
													$extended = $extended0;	
													if($extended1)
														$extended .= ' && '.$extended1;

													if($extended2)
															$extended .= ' && '.$extended2;

												}
												else
												{
													if($extended1)
													{
														$extended = $extended1;
														if($extended2)
															$extended .= ' && '.$extended2;	
													}		
													else
														if($extended2)
															$extended = $extended2;	
												}
												
												
												if($normal0)
												{	
													$normal = $normal0;	
													if($normal1)
														$normal .= ' && '.$normal1;
												}
												else
												{
													if($normal1)
														$normal = $normal1;
												}
												
											}
											else
											{
												if(isset($name_fields[1]))
												{
													$extended ='';
													$normal ='';
													if($extended0)
													{	
														if($extended1)
															$extended = $extended0.' && '.$extended1;
														else
															$extended = $extended0;
													}
													else
													{
														if($extended1)
															$extended = $extended1;
													}
													
													
													if($normal0)
													{	
														if($normal1)
															$normal = $normal0.' && '.$normal1;
														else
															$normal = $normal0;
													}
													else
													{
														if($normal1)
															$normal = $normal1;
													}
												}
												else
												{
													$extended = $extended0;
													$normal = $normal0;
												}
											}	
										}

										if($extended!="" && $normal!="")			
											$if .= ' ((wdformjQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").length != 0) ?  '.$extended.' : '.$normal.') ';
										else
											$if .= ' true';
									}	
								}
								$keyup .= '#wdform_'.$params_value[0].'_element_title'.$form_id.', #wdform_'.$params_value[0].'_element_first'.$form_id.', #wdform_'.$params_value[0].'_element_last'.$form_id.', #wdform_'.$params_value[0].'_element_middle'.$form_id.', ';		
							break;
							
							case "type_phone":	
								if($params_value[1] == "==" || $params_value[1] == "!=")
								{
									$phone_fields = explode(' ',$params_value[2]);
									if(isset($phone_fields[1]))
									{
										if($phone_fields[0]!='' && $phone_fields[1]!='')
											$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$phone_fields[0].'" && wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$phone_fields[1].'") ';	
										else
										{
											if($phone_fields[0]=='')
												$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$phone_fields[1].'") ';	
											else
												if($phone_fields[1]=='')
													$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$phone_fields[1].'") ';	
										}
									}
									else
										$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}
								
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$phone_fields = explode(' ',$params_value[2]);
									if(isset($phone_fields[1]))
									{
										if($phone_fields[0]!='' && $phone_fields[1]!='')
											$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$phone_fields[0].'")'.$like_or_not.'-1 && wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val().indexOf("'.$phone_fields[1].'")'.$like_or_not.'-1)';
										else
										{
											if($phone_fields[0]=='')
												$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val().indexOf("'.$phone_fields[1].'")'.$like_or_not.'-1) ';	
											else
												if($phone_fields[1]=='')
													$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$phone_fields[0].'")'.$like_or_not.'-1) ';															
										}
									}
									else
										$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$phone_fields[0].'")'.$like_or_not.'-1) ';
								}
								
								if($params_value[1] == "=" || $params_value[1] == "!")
								{
									$params_value[2] = "";
									$and_or_phone = ($params_value[1]=="=" ? "&&" : "||");	
									$params_value[1] = $params_value[1]."=";	
									
									$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" '.$and_or_phone.' wdformjQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'") ';										
								}
						
								$keyup .= '#wdform_'.$params_value[0].'_element_first'.$form_id.', #wdform_'.$params_value[0].'_element_last'.$form_id.', ';
							break;
						
							case "type_paypal_price":
								if($params_value[1] == "==" || $params_value[1] == "!=")
									$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_td_name_cents").attr("style")=="display: none;" ? wdformjQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" : parseFloat(wdformjQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()+"."+wdformjQuery("#wdform_'.$params_value[0].'_element_cents'.$form_id.'").val())'.$params_value[1].'parseFloat("'.str_replace('.0', '.', $params_value[2]).'"))';

								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_td_name_cents").attr("style")=="display: none;" ? wdformjQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 : (wdformjQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()+"."+wdformjQuery("#wdform_'.$params_value[0].'_element_cents'.$form_id.'").val()).indexOf("'.str_replace('.0', '.', $params_value[2]).'")'.$like_or_not.'-1) ';									
								}	
									
								if($params_value[1] == "=" || $params_value[1] == "!")
								{
									$params_value[2] = "";
									$and_or_price = ($params_value[1]=="=" ? "&&" : "||");	
									$params_value[1] = $params_value[1]."=";
									$if .= ' (wdformjQuery("#wdform_'.$params_value[0].'_td_name_cents").attr("style")=="display: none;" ? wdformjQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" : (wdformjQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" '.$and_or_price.' wdformjQuery("#wdform_'.$params_value[0].'_element_cents'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'"))';										
								}
							
								
								$keyup .= '#wdform_'.$params_value[0].'_element_dollars'.$form_id.', #wdform_'.$params_value[0].'_element_cents'.$form_id.', ';
							break;
							
							case "type_own_select":
							case "type_paypal_select":
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";						
									}
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}
								$change .= '#wdform_'.$params_value[0].'_element'.$form_id.', ';
							break;
							
							case "type_address":
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_country'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";						
									}								
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_country'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}	
								$change .= '#wdform_'.$params_value[0].'_country'.$form_id.', ';
							break;
							case "type_country":
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";						
									}								
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}	
								$change .= '#wdform_'.$params_value[0].'_element'.$form_id.', ';
							break;
							
							case "type_radio":
							case "type_paypal_radio":
							case "type_paypal_shipping":
								if($params_value[1] == "==" || $params_value[1] == "!=")
								{
									
									$if .= ' wdformjQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val()'.$params_value[1].'"'.$params_value[2].'" ';
									$click .= 'div[wdid='.$params_value[0].'] input[type=\'radio\'], ';
								}
								
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$click .= 'div[wdid='.$params_value[0].'] input[type=\'radio\'], ';
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' (wdformjQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val() ? (wdformjQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").attr("other") ? false  : (wdformjQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 )) : false) ';

								}
								
								if($params_value[1] == "=" || $params_value[1] == "!")
								{	
									$ckecked_or_no = ($params_value[1] == "=" ? "!" : "");
									$if .= ' '.$ckecked_or_no.'wdformjQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val()';
									$click .= 'div[wdid='.$params_value[0].'] input[type=\'radio\'], ';
								}	
								
							break;
							
							case "type_checkbox":
							case "type_paypal_checkbox":	
								
								if($params_value[1] == "==" || $params_value[1] == "!=")
								{
									if($params_value[2])
									{
										$choises = explode('@@@',$params_value[2]);
										$choises 	= array_slice($choises,0, count($choises)-1); 
										
										if($params_value[1]=="!=")
											$is = "!";
										else
											$is = "";
											
										foreach($choises as $key1=>$choise)
										{
											if($type_and_id[$params_value[0]]=="type_paypal_checkbox")
											{
												$choise_and_value = explode("*:*value*:*",$choise);
												$if .= ' '.$is.'(wdformjQuery("div[wdid='.$params_value[0].'] input[value=\"'.$choise_and_value[1].'\"]").is(":checked") && wdformjQuery("div[wdid='.$params_value[0].'] input[title=\"'.$choise_and_value[0].'\"]"))';

											}
											else
											$if .= ' '.$is.'wdformjQuery("div[wdid='.$params_value[0].'] input[value=\"'.$choise.'\"]").is(":checked") ';
											
											if($key1!=count($choises)-1)
												$if .= '&&';
										}
									
										$click .= 'div[wdid='.$params_value[0].'] input[type=\'checkbox\'], ';
									}
									else
									{
										if($or_and=='&&')
											$if .= ' true';
										else
											$if .= ' false';
									}
								}
								
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									if($params_value[2])
									{
										$choises = explode('@@@',$params_value[2]);
										$choises 	= array_slice($choises,0, count($choises)-1);

										if($type_and_id[$params_value[0]]=="type_paypal_checkbox")
										{
											foreach($choises as $key1=>$choise)
											{
										
												$choise_and_value = explode("*:*value*:*",$choise);
					
												$if .= ' wdformjQuery("div[wdid='.$params_value[0].']  input[type=\"checkbox\"]:checked").serialize().indexOf("'.$choise_and_value[1].'")'.$like_or_not.'-1 ';
													
												if($key1!=count($choises)-1)
													$if .= '&&';			
											}																			
										}
										else
										{
											foreach($choises as $key1=>$choise)
											{
												$if .= ' wdformjQuery("div[wdid='.$params_value[0].']  input[type=\"checkbox\"]:checked").serialize().indexOf("'.str_replace(" ","+",$choise).'")'.$like_or_not.'-1 ';
												
												if($key1!=count($choises)-1)
													$if .= '&&';
											}	
										}
									
											
											
								
										$click .= 'div[wdid='.$params_value[0].'] input[type=\'checkbox\'], ';
									}
									else
									{
										if($or_and=='&&')
											$if .= ' true';
										else
											$if .= ' false';
									}
						
								}
								
								if($params_value[1] == "=" || $params_value[1] == "!")
								{
									$ckecked_or_no = ($params_value[1] == "=" ? "==" : ">");				
									$if .= ' wdformjQuery("div[wdid='.$params_value[0].'] input[type=\"checkbox\"]:checked").length'.$ckecked_or_no.'0 ';
									$click .= 'div[wdid='.$params_value[0].'] input[type=\'checkbox\'], ';	
									
								}

								break;
						}	
						
						if($m!=count($cond_params)-1)
						{
							$params_value_next = explode('***',$cond_params[$m+1]);
							if(isset($type_and_id[$params_value_next[0]]))
								$if .= $or_and;
						}		
					}
					

					if($if)
					{
						$condition_js .= '
					
							if('.$if.')
								wdformjQuery("div[wdid='.$field_label[$k].']").'.$display .';
							else
								wdformjQuery("div[wdid='.$field_label[$k].']").'.$display_none .';';							
					}

					if($keyup)
						$condition_js .= '
							wdformjQuery("'.substr($keyup,0,-2).'").keyup(function() { 

								if('.$if.')
									wdformjQuery("div[wdid='.$field_label[$k].']").'.$display .';
								else
									wdformjQuery("div[wdid='.$field_label[$k].']").'.$display_none .'; });';
					
					if($change)
						$condition_js .= '
							wdformjQuery("'.substr($change,0,-2).'").change(function() { 
								if('.$if.')
									wdformjQuery("div[wdid='.$field_label[$k].']").'.$display .';
								else
									wdformjQuery("div[wdid='.$field_label[$k].']").'.$display_none .'; });';
									
					if($click)
					{					
						$condition_js .= '
							wdformjQuery("'.substr($click,0,-2).'").click(function() { 
								if('.$if.')
									wdformjQuery("div[wdid='.$field_label[$k].']").'.$display .';
								else
									wdformjQuery("div[wdid='.$field_label[$k].']").'.$display_none .'; });';		
			
					}
								

			}
			
			
		}
	
	}
	

	if($row->autogen_layout==0)
		$form=$row->custom_front;
	else
		$form=$row->form_front;

	foreach($id1s as $id1s_key => $id1)
	{	
		$label=$labels[$id1s_key];
		$type=$types[$id1s_key];
		$params=$paramss[$id1s_key];
		if( strpos($form, '%'.$id1.' - '.$label.'%') || strpos($form, '%'.$id1.' -'.$label.'%'))
		{
			$rep='';
			$required=false;
			$param=array();
			$param['attributes'] = '';
			$is_type[$type]=true;
			
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

					$rep ='<div type="type_section_break" class="wdform-field-section-break"><div class="wdform_section_break">'.$param['w_editor'].'</div></div>';
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
					

					$rep ='<div type="type_editor" class="wdform-field">'.$param['w_editor'].'</div>';
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					$input_active = ($param['w_first_val']=='true' ? "checked='checked'" : "");	
					$post_value=$input_get->getString("counter".$form_id);

					if(isset($post_value))
					{
						$post_temp=$input_get->getString('wdform_'.$id1.'_element'.$form_id);
						$input_active = (isset($post_temp) ? "checked='checked'" : "");	
					}
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					
					$rep ='<div type="type_send_copy" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label"><label for="wdform_'.$id1.'_element'.$form_id.'">'.$label.'</label></span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';

					$rep.='</div>
					<div class="wdform-element-section" style="'.$param['w_field_label_pos2'].'" >
						<div class="checkbox-div" style="left:3px">
						<input type="checkbox" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" '.$input_active.' '.$param['attributes'].'/>
						<label for="wdform_'.$id1.'_element'.$form_id.'"></label>
						</div>
					</div></div>';

					$onsubmit_js.='
					if(!wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").is(":checked"))
						wdformjQuery("<input type=\"hidden\" name=\"wdform_send_copy_'.$form_id.'\" value = \"1\" />").appendTo("#form'.$form_id.'");
						';
					
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).length == 0)
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								
								return false;
							}						
						}
						';	
					
					
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}

					$param['w_first_val']=htmlspecialchars($input_get->getString('wdform_'.$id1.'_element'.$form_id, $param['w_first_val']));
			
					$wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] : max($param['w_field_label_size'],$param['w_size']));
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required = ($param['w_required']=="yes" ? true : false);	

					$rep ='<div type="type_text" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'"  style="width: 100%" '.$param['attributes'].'></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="'.$param['w_title'].'" || wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
						';		
					
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
					
					$param['w_first_val']=htmlspecialchars($input_get->getString('wdform_'.$id1.'_element'.$form_id, $param['w_first_val']));

					$wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] : max($param['w_field_label_size'],$param['w_size']));	
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required = ($param['w_required']=="yes" ? true : false);	
									
					$rep ='<div type="type_number" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section"  class="'.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'" style="width:100%;" '.$param['attributes'].'></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="'.$param['w_title'].'" || wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
						';		
					
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
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	

					$rep ='<div type="type_password" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section"  class="'.$param['w_class'].'" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><input type="password" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;" '.$param['attributes'].'></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
						';		
					
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
				
					$param['w_first_val']=htmlspecialchars($input_get->getString('wdform_'.$id1.'_element'.$form_id, $param['w_first_val']));			
						
					$wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size_w'] : max($param['w_field_label_size'],$param['w_size_w']));	
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required = ($param['w_required']=="yes" ? true : false);	
				
					$rep ='<div type="type_textarea" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size_w'].'px"><textarea class="'.$input_active.'" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" title="'.$param['w_title'].'"  style="width: 100%; height: '.$param['w_size_h'].'px;" '.$param['attributes'].'>'.$param['w_first_val'].'</textarea></div></div>';

					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="'.$param['w_title'].'" || wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
						';		
					

					break;
				}
				case 'type_wdeditor':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_size_w','w_size_h','w_title','w_required','w_class');
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
			
					
					$wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size_w']+10 : max($param['w_field_label_size'],$param['w_size_w']));	
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					
					$required = ($param['w_required']=="yes" ? true : false);	
				
					$rep ='<div type="type_wdeditor" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size_w'].'px">';
					
					if($is_editor) 
						$wd_editor = $editor->display('wdform_'.$id1.'_wd_editor'.$form_id,'',$param['w_size_w'],$param['w_size_h'],'40','6');
					else
					{
						$wd_editor='
						<textarea  class="'.$param['w_class'].'" name="wdform_'.$id1.'_wd_editor'.$form_id.'" id="wdform_'.$id1.'_wd_editor'.$form_id.'" style="width: '.$param['w_size_w'].'px; height: '.$param['w_size_h'].'px; " class="mce_editable" aria-hidden="true">'.$param['w_title'].'</textarea>';
					}	
					
					$rep.= $wd_editor.'</div></div>';
			

					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(tinyMCE.get("wdform_'.$id1.'_wd_editor'.$form_id.'").getContent()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_wd_editor'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_wd_editor'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_wd_editor'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
						';	

						$onload_js .='tinyMCE.get("wdform_'.$id1.'_wd_editor'.$form_id.'").setContent("'.$param['w_title'].'");';
						
					
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					
					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);
					
					$param['w_first_val']=htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_first'.$form_id, $w_first_val[0])).'***'.htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_last'.$form_id, $w_first_val[1]));
					
					$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']+65) : max($param['w_field_label_size'],($param['w_size']+65)));	
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required = ($param['w_required']=="yes" ? true : false);	
					
					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);
					$w_mini_labels = explode('***',$param['w_mini_labels']);
			

					$rep ='<div type="type_phone" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label" >'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='
					</div>
					<div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.($param['w_size']+65).'px;">
						<div style="display: table-cell;vertical-align: middle;">
							<div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.$w_first_val[0].'" title="'.$w_title[0].'" style="width: 50px;" '.$param['attributes'].'></div>
							<div><label class="mini_label">'.$w_mini_labels[0].'</label></div>
						</div>
						<div style="display: table-cell;vertical-align: middle;">
							<div class="wdform_line" style="margin: 0px 4px 10px 4px; padding: 0px;">-</div>
						</div>
						<div style="display: table-cell;vertical-align: middle; width:100%; min-width: 100px;">
							<div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.$w_first_val[1].'" title="'.$w_title[1].'" style="width: 100%;" '.$param['attributes'].'></div>
							<div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
						</div>
					</div>
					</div>';
				
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="'.$w_title[0].'" || wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="'.$w_title[1].'" || wdformjQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").focus();
								return false;
							}
							
						}
						';		
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);
					$w_mini_labels = explode('***',$param['w_mini_labels']);

					
					
					$element_title = $input_get->getString('wdform_'.$id1.'_element_title'.$form_id);
					$element_first = $input_get->getString('wdform_'.$id1.'_element_first'.$form_id);
					if(isset($element_title))
						$param['w_first_val']=htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_title'.$form_id, $w_first_val[0])).'***'.htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_first'.$form_id, $w_first_val[1])).'***'.htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_last'.$form_id, $w_first_val[2])).'***'.htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_middle'.$form_id, $w_first_val[3]));
					else
						if(isset($element_first))
							$param['w_first_val']=htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_first'.$form_id, $w_first_val[0])).'***'.htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_last'.$form_id, $w_first_val[1]));
						
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required = ($param['w_required']=="yes" ? true : false);	
				
					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);

					
					
					if($param['w_name_format']=='normal')
					{
						$w_name_format = '
						<div style="display: table-cell; width:50%">
							<div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.$w_first_val[0].'" title="'.$w_title[0].'"  style="width: 100%;"'.$param['attributes'].'></div>
							<div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
						</div>
						<div style="display:table-cell;"><div style="margin: 0px 8px; padding: 0px;"></div></div>
						<div  style="display: table-cell; width:50%">
							<div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.$w_first_val[1].'" title="'.$w_title[1].'" style="width: 100%;" '.$param['attributes'].'></div>
							<div><label class="mini_label">'.$w_mini_labels[2].'</label></div>
						</div>
						';
						$w_size=2*$param['w_size'];

					}
					else
					{
						$w_name_format = '
						<div style="display: table-cell;">
							<div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_title'.$form_id.'" name="wdform_'.$id1.'_element_title'.$form_id.'" value="'.$w_first_val[0].'" title="'.$w_title[0].'" style="width: 40px;"></div>
							<div><label class="mini_label">'.$w_mini_labels[0].'</label></div>
						</div>
						<div style="display:table-cell;"><div style="margin: 0px 1px; padding: 0px;"></div></div>
						<div style="display: table-cell; width:30%">
							<div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.$w_first_val[1].'" title="'.$w_title[1].'" style="width:100%;"></div>
							<div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
						</div>
						<div style="display:table-cell;"><div style="margin: 0px 4px; padding: 0px;"></div></div>
						<div style="display: table-cell; width:30%">
							<div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.$w_first_val[2].'" title="'.$w_title[2].'" style="width:  100%;"></div>
							<div><label class="mini_label">'.$w_mini_labels[2].'</label></div>
						</div>
						<div style="display:table-cell;"><div style="margin: 0px 4px; padding: 0px;"></div></div>
						<div style="display: table-cell; width:30%">
							<div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_middle'.$form_id.'" name="wdform_'.$id1.'_element_middle'.$form_id.'" value="'.$w_first_val[3].'" title="'.$w_title[3].'" style="width: 100%;"></div>
							<div><label class="mini_label">'.$w_mini_labels[3].'</label></div>
						</div>						
						';
						$w_size=3*$param['w_size']+80;
					}
		
					$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$w_size) : max($param['w_field_label_size'],$w_size));	
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					

					$rep ='<div type="type_name" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$w_size.'px;">'.$w_name_format.'</div></div>';

					if($required)
					{
						if($param['w_name_format']=='normal')
						{
							$check_js.='
							if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
							{
								if(wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="'.$w_title[0].'" || wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="'.$w_title[1].'" || wdformjQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="")
								{
									alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
									wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").focus();
									return false;
								}
							}
							';	
						}
						else
						{
							$check_js.='
							if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
							{
								if(wdformjQuery("#wdform_'.$id1.'_element_title'.$form_id.'").val()=="'.$w_title[0].'" || wdformjQuery("#wdform_'.$id1.'_element_title'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="'.$w_title[1].'" || wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="'.$w_title[2].'" || wdformjQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_element_middle'.$form_id.'").val()=="'.$w_title[3].'" || wdformjQuery("#wdform_'.$id1.'_element_middle'.$form_id.'").val()=="")
								{
									alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
									wdformjQuery("#wdform_'.$id1.'_element_first'.$form_id.'").focus();
									return false;
								}
							}
							';		
						}
					}
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
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					$w_disabled_fields = explode('***',$param['w_disabled_fields']);
				

					$rep ='<div type="type_address" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
						
						
						
			
					$address_fields ='';
					$g=0;
					if($w_disabled_fields[0]=='no')
					{
					$g+=2;
					$address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="wdform_'.$id1.'_street1'.$form_id.'" name="wdform_'.$id1.'_street1'.$form_id.'" value="'.$input_get->getString('wdform_'.$id1.'_street1'.$form_id).'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[0].'</label></span>';
					}
					
					if($w_disabled_fields[1]=='no')
					{
					$g+=2;
					$address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="wdform_'.$id1.'_street2'.$form_id.'" name="wdform_'.($id1+1).'_street2'.$form_id.'" value="'.$input_get->getString('wdform_'.($id1+1).'_street2'.$form_id).'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[1].'</label></span>';
					}
					
					if($w_disabled_fields[2]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: left; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_city'.$form_id.'" name="wdform_'.($id1+2).'_city'.$form_id.'" value="'.$input_get->getString('wdform_'.($id1+2).'_city'.$form_id).'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[2].'</label></span>';
					}
					if($w_disabled_fields[3]=='no')
					{
					$g++;
					

					$w_states = array("","Alabama","Alaska", "Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District Of Columbia","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming");	
					$w_state_options = '';
					foreach($w_states as $w_state)
					{
					
					if($w_state == $input_get->getString('wdform_'.($id1+3).'_state'.$form_id))					
					$selected = 'selected="selected"';
					else
					$selected = '';
					$w_state_options .= '<option value="'.$w_state.'" '.$selected.'>'.$w_state.'</option>';
					}
					if($w_disabled_fields[5]=='yes' && $w_disabled_fields[6]=='yes')
					{
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><select type="text" id="wdform_'.$id1.'_state'.$form_id.'" name="wdform_'.($id1+3).'_state'.$form_id.'" style="width: 100%;" '.$param['attributes'].'>'.$w_state_options.'</select><label class="mini_label" style="display: block;" id="'.$id1.'_mini_label_state">'.$w_mini_labels[3].'</label></span>';
					}
					else
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_state'.$form_id.'" name="wdform_'.($id1+3).'_state'.$form_id.'" value="'.$input_get->getString('wdform_'.($id1+3).'_state'.$form_id).'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[3].'</label></span>';
					}
					if($w_disabled_fields[4]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_postal'.$form_id.'" name="wdform_'.($id1+4).'_postal'.$form_id.'" value="'.$input_get->getString('wdform_'.($id1+4).'_postal'.$form_id).'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[4].'</label></span>';
					}
					$w_countries = array("","Afghanistan","Albania",	"Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepa","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");	
					$w_options = '';
					foreach($w_countries as $w_country)
					{
					
					if($w_country == $input_get->getString('wdform_'.($id1+5).'_country'.$form_id))					
					$selected = 'selected="selected"';
					else
					$selected = '';
					$w_options .= '<option value="'.$w_country.'" '.$selected.'>'.$w_country.'</option>';
					}
				
					if($w_disabled_fields[5]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;display: inline-block;"><select type="text" id="wdform_'.$id1.'_country'.$form_id.'" name="wdform_'.($id1+5).'_country'.$form_id.'" style="width: 100%;" '.$param['attributes'].'>'.$w_options.'</select><label class="mini_label">'.$w_mini_labels[5].'</span>';
					}				

				
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><div>
					'.$address_fields.'</div></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_street1'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_street2'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_city'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_state'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_postal'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_country'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_street1'.$form_id.'").focus();
								return false;
							}
							
						}
						';	
						
						$post=$input_get->getString('wdform_'.($id1+5).'_country'.$form_id);
						if(isset($post))
							$onload_js .=' wdformjQuery("#wdform_'.$id1.'_country'.$form_id.'").val("'.$input_get->getString('wdform_'.($id1+5)."_country".$form_id, '').'");';
						
						if($w_disabled_fields[6]=='yes')
							$onload_js .=' wdformjQuery("#wdform_'.$id1.'_country'.$form_id.'").change(function() { 
							if( wdformjQuery(this).val()=="United States") 
							{
								wdformjQuery("#wdform_'.$id1.'_state'.$form_id.'").parent().append("<select type=\"text\" id=\"wdform_'.$id1.'_state'.$form_id.'\" name=\"wdform_'.($id1+3).'_state'.$form_id.'\" style=\"width: 100%;\" '.$param['attributes'].'>'.addslashes($w_state_options).'</select><label class=\"mini_label\" style=\"display: block;\" id=\"'.$id1.'_mini_label_state\">'.$w_mini_labels[3].'</label>");
								wdformjQuery("#wdform_'.$id1.'_state'.$form_id.'").parent().children("input:first, label:first").remove();
							}
							else
							{
								if(wdformjQuery("#wdform_'.$id1.'_state'.$form_id.'").prop("tagName")=="SELECT")
								{
						
									wdformjQuery("#wdform_'.$id1.'_state'.$form_id.'").parent().append("<input type=\"text\" id=\"wdform_'.$id1.'_state'.$form_id.'\" name=\"wdform_'.($id1+3).'_state'.$form_id.'\" value=\"'.$input_get->getString('wdform_'.($id1+3).'_state'.$form_id).'\" style=\"width: 100%;\" '.$param['attributes'].'><label class=\"mini_label\">'.$w_mini_labels[3].'</label>");
									wdformjQuery("#wdform_'.$id1.'_state'.$form_id.'").parent().children("select:first, label:first").remove();	
								}
							}
						
						});';
						
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
					
				
					$param['w_first_val']=$input_get->getString('wdform_'.$id1.'_element'.$form_id, $param['w_first_val']);
						
					$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required = ($param['w_required']=="yes" ? true : false);	
				

					$rep ='<div type="type_submitter_mail" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'"  style="width: 100%;" '.$param['attributes'].'></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="'.$param['w_title'].'" || wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
						';	
							
					$check_js.='
					if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
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
					if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
				
					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");
					$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");	

					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
				
					if(isset($param['w_choices_value']))
					{
						$param['w_choices_value'] = explode('***',$param['w_choices_value']);
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
					}
			
					$post_value=$input_get->getString("counter".$form_id);
					$is_other=false;

					if(isset($post_value))
					{
						if($param['w_allow_other']=="yes")
						{
							$is_other=false;
							$other_element=$input_get->getString('wdform_'.$id1."_other_input".$form_id);
							if(isset($other_element))
								$is_other=true;
						}
					}
					else
						$is_other=($param['w_allow_other']=="yes" && $param['w_choices_checked'][$param['w_allow_other_num']]=='true') ;

					$rep='<div type="type_checkbox" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
				
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

									if(isset($post_value))
									{
										$post_valuetemp=$input_get->getString('wdform_'.$id1."_element".$form_id.($key1+$k));
										$param['w_choices_checked'][$key]=(isset($post_valuetemp) ? 'checked="checked"' : '');
									}

									$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" value="'.htmlspecialchars($choice_value).'" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'onclick="if(set_checked(&quot;wdform_'.$id1.'&quot;,&quot;'.($key1+$k).'&quot;,&quot;'.$form_id.'&quot;)) show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);"' : '').' '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';
									
								}
							}	
						}	
						else
						{
							if($key1%$param['w_rowcol']==0 && $key1>0)
								$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';
							if(!isset($post_value))
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
							else
							{
								$post_valuetemp=$input_get->getString('wdform_'.$id1."_element".$form_id.$key1);
								$param['w_choices_checked'][$key]=(isset($post_valuetemp) ? 'checked="checked"' : '');
							}
							$choice_value = isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice;
							
							$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.''.$key1.'" value="'.htmlspecialchars($choice_value).'" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'onclick="if(set_checked(&quot;wdform_'.$id1.'&quot;,&quot;'.$key1.'&quot;,&quot;'.$form_id.'&quot;)) show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);"' : '').' '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';
							
							$param['w_allow_other_num'] = $param['w_allow_other_num']==$key ? $key1 : $param['w_allow_other_num'];

						}
					}
					$rep.='</div>';

					$rep.='</div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).length == 0 ||   wdformjQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								
								return false;
							}						
						}
						';	
					if($is_other)
						$onload_js .='show_other_input("wdform_'.$id1.'","'.$form_id.'"); wdformjQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val("'.$input_get->getString('wdform_'.$id1."_other_input".$form_id, '').'");';
					
					if($param['w_randomize']=='yes')
					{
						$onload_js .='wdformjQuery("#form'.$form_id.' div[wdid='.$id1.'] .wdform-element-section> div").shuffle();
						';
					}
					
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other'.$form_id.'\" value = \"'.$param['w_allow_other'].'\" />").appendTo("#form'.$form_id.'");
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other_num'.$form_id.'\" value = \"'.$param['w_allow_other_num'].'\" />").appendTo("#form'.$form_id.'");
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
						
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");
					$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);

					if(isset($param['w_choices_value']))
					{
						$param['w_choices_value'] = explode('***',$param['w_choices_value']);
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
					}
					
					$post_value=$input_get->getString("counter".$form_id);
					$is_other=false;

					if(isset($post_value))
					{
						if($param['w_allow_other']=="yes")
						{
							$is_other=false;
							$other_element=$input_get->getString('wdform_'.$id1."_other_input".$form_id);
							if(isset($other_element))
								$is_other=true;
						}
					}
					else
						$is_other=($param['w_allow_other']=="yes" && $param['w_choices_checked'][$param['w_allow_other_num']]=='true') ;
					
					$rep='<div type="type_radio" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
				
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
								
								if(!isset($post_value))
									$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
									
								for($k=0; $k<$columns_count_radio; $k++)
								{
									$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : $choice_label;
										
									if(($key1+$k)%$param['w_rowcol']==0 && ($key1+$k)>0)
										$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

									if(isset($post_value))
									{
										$post_valuetemp=$input_get->getString('wdform_'.$id1."_element".$form_id);
										$param['w_choices_checked'][$key]=(isset($post_valuetemp) ? 'checked="checked"' : '');
									}
									
									$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.htmlspecialchars($choice_value).'" onclick="set_default(&quot;wdform_'.$id1.'&quot;,&quot;'.($key1+$k).'&quot;,&quot;'.$form_id.'&quot;); '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);' : '').'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';
								}
							}	
						}	
						else
						{
					
							if($key1%$param['w_rowcol']==0 && $key1>0)
								$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';
							if(!isset($post_value))
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
							else
								$param['w_choices_checked'][$key]=(htmlspecialchars($choice)==htmlspecialchars($input_get->getString('wdform_'.$id1."_element".$form_id)) ? 'checked="checked"' : '');
							
							$choice_value = isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice;
							
							$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.htmlspecialchars($choice_value).'" onclick="set_default(&quot;wdform_'.$id1.'&quot;,&quot;'.$key1.'&quot;,&quot;'.$form_id.'&quot;); '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);' : '').'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';
							
							$param['w_allow_other_num'] = $param['w_allow_other_num']==$key ? $key1 : $param['w_allow_other_num'];
						}	
					}
							$rep.='</div>';

					$rep.='</div></div>';
				
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).length == 0 ||   wdformjQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								
								return false;
							}						
						}
						';		
					if($is_other)
						$onload_js .='show_other_input("wdform_'.$id1.'","'.$form_id.'"); wdformjQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val("'.$input_get->getString('wdform_'.$id1."_other_input".$form_id, '').'");';
					
					if($param['w_randomize']=='yes')
					{
						$onload_js .='wdformjQuery("#form'.$form_id.' div[wdid='.$id1.'] .wdform-element-section> div").shuffle();
						';
					}
					
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other'.$form_id.'\" value = \"'.$param['w_allow_other'].'\" />").appendTo("#form'.$form_id.'");
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other_num'.$form_id.'\" value = \"'.$param['w_allow_other_num'].'\" />").appendTo("#form'.$form_id.'");
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
					
					$required = ($param['w_required']=="yes" ? true : false);	
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
	
					$post_value=$input_get->getString("counter".$form_id);

					$rep='<div type="type_own_select" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
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
									if(!isset($post_value))
										$param['w_choices_checked'][$key]=(($param['w_choices_checked'][$key]=='true' && $k == 0) ? 'selected="selected"' : '');
									else
										$param['w_choices_checked'][$key]=($choice_value==htmlspecialchars($input_get->getString('wdform_'.$id1."_element".$form_id)) ? 'selected="selected"' : '');

									$rep.='<option value="'.htmlspecialchars($choice_value).'" '.$param['w_choices_checked'][$key].'>'.$choice_label.'</option>';
												
								}		
						}
						else
						{
							if(!isset($post_value))
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'selected="selected"' : '');
							else
								$param['w_choices_checked'][$key]=(htmlspecialchars($choice)==htmlspecialchars($input_get->getString('wdform_'.$id1."_element".$form_id)) ? 'selected="selected"' : '');
				
							$choice_value = $param['w_choices_disabled'][$key]=="true" ? '' : (isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice);
								
							$rep.='<option value="'.htmlspecialchars($choice_value).'" '.$param['w_choices_checked'][$key].'>'.$choice.'</option>';	
						}

					}
					$rep.='</select></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if( wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
								{
									alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
									wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
									wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
									wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
									return false;
								}
						}
						';		

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
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_countries']	= explode('***',$param['w_countries']);
					
					$post_value=$input_get->getString("counter".$form_id);
					$selected='';

					$rep='<div type="type_country" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;"  '.$param['attributes'].'>';
					foreach($param['w_countries'] as $key => $choice)
					{
						if(isset($post_value))
							$selected=(htmlspecialchars($choice)==htmlspecialchars($input_get->getString('wdform_'.$id1."_element".$form_id)) ? 'selected="selected"' : '');

						$choice_value=$choice;
						$rep.='<option value="'.$choice_value.'" '.$selected.'>'.$choice.'</option>';
					}
					$rep.='</select></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if( wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
								{
									alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
									wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
									wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
									wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
									return false;
								}
						}
						';		
					
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
				
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$w_mini_labels = explode('***',$param['w_mini_labels']);

					$w_sec = '';
					$w_sec_label='';
				
					if($param['w_sec']=='1')
					{
						$w_sec = '<div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div style="display: table-cell;"><input type="text" value="'.$input_get->getString('wdform_'.$id1."_ss".$form_id, $param['w_ss']).'" class="time_box" id="wdform_'.$id1.'_ss'.$form_id.'" name="wdform_'.$id1.'_ss'.$form_id.'" onkeypress="return check_second(event, &quot;wdform_'.$id1.'_ss'.$form_id.'&quot;)" '.$param['attributes'].'></div>';
						
						$w_sec_label='<div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[2].'</label></div>';
					}

					
					if($param['w_time_type']=='12')
					{
						if($input_get->getString('wdform_'.$id1."_am_pm".$form_id, $param['w_am_pm'])=='am')
						{
							$am_ = "selected=\"selected\"";
							$pm_ = "";
						}	
						else
						{
							$am_ = "";
							$pm_ = "selected=\"selected\"";
							
						}	
					
					$w_time_type = '<div style="display: table-cell;"><select class="am_pm_select" name="wdform_'.$id1.'_am_pm'.$form_id.'" id="wdform_'.$id1.'_am_pm'.$form_id.'" style="width:50px;" '.$param['attributes'].'><option value="am" '.$am_.'>AM</option><option value="pm" '.$pm_.'>PM</option></select></div>';
					
					$w_time_type_label = '<div ><label class="mini_label">'.$w_mini_labels[3].'</label></div>';
					
					}
					else
					{
					$w_time_type='';
					$w_time_type_label = '';
					}
					
					
					
					$rep ='<div type="type_time" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;"><input type="text" value="'.$input_get->getString('wdform_'.$id1."_hh".$form_id, $param['w_hh']).'" class="time_box" id="wdform_'.$id1.'_hh'.$form_id.'" name="wdform_'.$id1.'_hh'.$form_id.'" onkeypress="return check_hour(event, &quot;wdform_'.$id1.'_hh'.$form_id.'&quot;, &quot;23&quot;)" '.$param['attributes'].'></div><div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div style="display: table-cell;"><input type="text" value="'.$input_get->getString('wdform_'.$id1."_mm".$form_id, $param['w_mm']).'" class="time_box" id="wdform_'.$id1.'_mm'.$form_id.'" name="wdform_'.$id1.'_mm'.$form_id.'" onkeypress="return check_minute(event, &quot;wdform_'.$id1.'_mm'.$form_id.'&quot;)" '.$param['attributes'].'></div>'.$w_sec.$w_time_type.'</div><div style="display: table-row;"><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[0].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[1].'</label></div>'.$w_sec_label.$w_time_type_label.'</div></div></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_mm'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_hh'.$form_id.'").val()=="" || (wdformjQuery("#wdform_'.$id1.'_ss'.$form_id.'").length != 0 ? wdformjQuery("#wdform_'.$id1.'_ss'.$form_id.'").val()=="" : false))
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_hh'.$form_id.'").focus();
								return false;
							}
						}
						';		
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
					
				
						
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
				
					$param['w_date']=$input_get->getString('wdform_'.$id1."_element".$form_id, $param['w_date']);

					$rep ='<div type="type_date" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><input type="text" value="'.$param['w_date'].'" class="wdform-date" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" maxlength="10" '.$param['attributes'].'><input id="wdform_'.$id1.'_button'.$form_id.'" class="wdform-calendar-button" type="reset" value="'.$param['w_but_val'].'" format="'.$param['w_format'].'" alt="calendar" '.$param['attributes'].' "></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
						';		
					
					$onload_js.= 'Calendar.setup({inputField: "wdform_'.$id1.'_element'.$form_id.'",	ifFormat: "'.$param['w_format'].'",button: "wdform_'.$id1.'_button'.$form_id.'",align: "Tl",singleClick: true,firstDay: 0});';

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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					$param['w_day']=$input_get->getString('wdform_'.$id1."_day".$form_id, $param['w_day']);
					$param['w_month']=$input_get->getString('wdform_'.$id1."_month".$form_id, $param['w_month']);
					$param['w_year']=$input_get->getString('wdform_'.$id1."_year".$form_id, $param['w_year']);
						
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
				
	
					if($param['w_day_type']=="SELECT")
					{
						$w_day_type = '<select id="wdform_'.$id1.'_day'.$form_id.'" name="wdform_'.$id1.'_day'.$form_id.'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'><option value=""></option>';
						
						for($k=1; $k<=31; $k++)
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
						$w_day_type = '<input type="text" value="'.$param['w_day'].'" id="wdform_'.$id1.'_day'.$form_id.'" name="wdform_'.$id1.'_day'.$form_id.'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_day'.$form_id.'").blur(function() {if (wdformjQuery(this).val()=="0") wdformjQuery(this).val(""); else add_0(this)});';
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_day'.$form_id.'").keypress(function() {return check_day(event, this)});';
					}
					
					
					if($param['w_month_type']=="SELECT")
					{
					
						$w_month_type = '<select id="wdform_'.$id1.'_month'.$form_id.'" name="wdform_'.$id1.'_month'.$form_id.'" style="width: '.$param['w_month_size'].'px;" '.$param['attributes'].'><option value=""></option><option value="01" '.($param['w_month']=="01" ? "selected=\"selected\"": "").'  >'.JText::_("January").'</option><option value="02" '.($param['w_month']=="02" ? "selected=\"selected\"": "").'>'.JText::_("February").'</option><option value="03" '.($param['w_month']=="03"? "selected=\"selected\"": "").'>'.JText::_("March").'</option><option value="04" '.($param['w_month']=="04" ? "selected=\"selected\"": "").' >'.JText::_("April").'</option><option value="05" '.($param['w_month']=="05" ? "selected=\"selected\"": "").' >'.JText::_("May").'</option><option value="06" '.($param['w_month']=="06" ? "selected=\"selected\"": "").' >'.JText::_("June").'</option><option value="07" '.($param['w_month']=="07" ? "selected=\"selected\"": "").' >'.JText::_("July").'</option><option value="08" '.($param['w_month']=="08" ? "selected=\"selected\"": "").' >'.JText::_("August").'</option><option value="09" '.($param['w_month']=="09" ? "selected=\"selected\"": "").' >'.JText::_("September").'</option><option value="10" '.($param['w_month']=="10" ? "selected=\"selected\"": "").' >'.JText::_("October").'</option><option value="11" '.($param['w_month']=="11" ? "selected=\"selected\"": "").'>'.JText::_("November").'</option><option value="12" '.($param['w_month']=="12" ? "selected=\"selected\"": "").' >'.JText::_("December").'</option></select>';
					
					}
					else
					{
						$w_month_type = '<input type="text" value="'.$param['w_month'].'" id="wdform_'.$id1.'_month'.$form_id.'" name="wdform_'.$id1.'_month'.$form_id.'"  style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_month'.$form_id.'").blur(function() {if (wdformjQuery(this).val()=="0") wdformjQuery(this).val(""); else add_0(this)});';
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_month'.$form_id.'").keypress(function() {return check_month(event, this)});';
					}
					
					
					if($param['w_year_type']=="SELECT" )
					{
						$w_year_type = '<select id="wdform_'.$id1.'_year'.$form_id.'" name="wdform_'.$id1.'_year'.$form_id.'"  from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_year_size'].'px;" '.$param['attributes'].'><option value=""></option>';
						
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
						$w_year_type = '<input type="text" value="'.$param['w_year'].'" id="wdform_'.$id1.'_year'.$form_id.'" name="wdform_'.$id1.'_year'.$form_id.'" from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_year'.$form_id.'").keypress(function() {return check_year1(event, this)});';
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_year'.$form_id.'").change(function() {change_year(this)});';
					}
					
					
					
					$rep ='<div type="type_date_fields" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;">'.$w_day_type.'</div><div style="display: table-cell;"><span class="wdform_separator">'.$param['w_divider'].'</span></div><div style="display: table-cell;">'.$w_month_type.'</div><div style="display: table-cell;"><span class="wdform_separator">'.$param['w_divider'].'</span></div><div style="display: table-cell;">'.$w_year_type.'</div></div><div style="display: table-row;"><div style="display: table-cell;"><label class="mini_label">'.$param['w_day_label'].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label" >'.$param['w_month_label'].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$param['w_year_label'].'</label></div></div></div></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_day'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_month'.$form_id.'").val()=="" || wdformjQuery("#wdform_'.$id1.'_year'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_day'.$form_id.'").focus();
								return false;
							}
						}
						';		
					
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
				
						
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$multiple = ($param['w_multiple']=="yes" ? "multiple='multiple'" : "");	
				
	
					
					
					$rep ='<div type="type_file_upload" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><label class="file-upload" style="display: inline-block;"><div class="file-picker"></div><input type="file" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_file'.$form_id.'[]" '.$multiple.' '.$param['attributes'].'></label></div></div>';
		
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								return false;
							}
						}
						';	

					$check_js.='
					if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
					{
						ext_available=getfileextension(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val(),"'.$param['w_extension'].'");
						if(!ext_available)
						{
							alert("'.JText::_("WDF_FILE_TYPE_ERROR").'");
							old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
							x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
							wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
							return false;
						}
					}
					';		
						
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					
					$rep ='<div type="type_captcha" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].'"><div style="display: table;"><div style="display: table-cell;vertical-align: middle;"><div valign="middle" style="display: table-cell; text-align: center; vertical-align:top;"><img type="captcha" digit="'.$param['w_digit'].'" src="index.php?option=com_formmaker&amp;view=wdcaptcha&amp;format=raw&amp;tmpl=component&amp;digit='.$param['w_digit'].'&amp;i='.$form_id.'" id="wd_captcha'.$form_id.'" class="captcha_img" style="display:none" '.$param['attributes'].'></div><div valign="middle" style="display: table-cell;"><div class="captcha_refresh" id="_element_refresh'.$form_id.'" '.$param['attributes'].'></div></div></div><div style="display: table-cell;vertical-align: middle;"><div style="display: table-cell;"><input type="text" class="captcha_input" id="wd_captcha_input'.$form_id.'" name="captcha_input" style="width: '.($param['w_digit']*10+15).'px;" '.$param['attributes'].'></div></div></div></div></div>';		
					
					$onload_js .='wdformjQuery("#wd_captcha'.$form_id.'").click(function() {captcha_refresh("wd_captcha","'.$form_id.'")});';
					$onload_js .='wdformjQuery("#_element_refresh'.$form_id.'").click(function() {captcha_refresh("wd_captcha","'.$form_id.'")});';
					
					$check_js.='
					if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
					{
						if(wdformjQuery("#wd_captcha_input'.$form_id.'").val()=="")
						{
							alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
							old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
							x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
							wdformjQuery("#wd_captcha_input'.$form_id.'").focus();
							return false;
						}
					}
					';
					
					$onload_js.= 'captcha_refresh("wd_captcha", "'.$form_id.'");';

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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
				
						
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$publickey=($row->public_key ? $row->public_key : '0');
					$rep =' <script src="https://www.google.com/recaptcha/api.js"></script><div type="type_recaptcha" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><div class="g-recaptcha" data-sitekey="'.$publickey.'"></div></div></div>';


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
					
					$rep ='<div type="type_hidden" class="wdform-field"><div class="wdform-label-section" style="display: table-cell;"></div><div class="wdform-element-section" style="display: table-cell;"><input type="hidden" value="'.$param['w_value'].'" id="wdform_'.$id1.'_element'.$form_id.'" name="'.$param['w_name'].'" '.$param['attributes'].'></div></div>';
					
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
				
					$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_width']) : max($param['w_field_label_size'], $param['w_width']));	
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
				
					$rep ='<div type="type_mark_map" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_width'].'px;"><input type="hidden" id="wdform_'.$id1.'_long'.$form_id.'" name="wdform_'.$id1.'_long'.$form_id.'" value="'.$param['w_long'].'"><input type="hidden" id="wdform_'.$id1.'_lat'.$form_id.'" name="wdform_'.$id1.'_lat'.$form_id.'" value="'.$param['w_lat'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" long0="'.$param['w_long'].'" lat0="'.$param['w_lat'].'" zoom="'.$param['w_zoom'].'" info0="'.$param['w_info'].'" center_x="'.$param['w_center_x'].'" center_y="'.$param['w_center_y'].'" style="width: 100%; height: '.$param['w_height'].'px;" '.$param['attributes'].'></div></div></div>	';
					
					$onload_js .='if_gmap_init("wdform_'.$id1.'", '.$form_id.');';
					$onload_js .='add_marker_on_map("wdform_'.$id1.'", 0, "'.$param['w_long'].'", "'.$param['w_lat'].'", "'.$param['w_info'].'", '.$form_id.',true);';
					
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					$marker='';
					
					$param['w_long']	= explode('***',$param['w_long']);
					$param['w_lat']	= explode('***',$param['w_lat']);
					$param['w_info']	= explode('***',$param['w_info']);
					foreach($param['w_long'] as $key => $w_long )
					{
						$marker.='long'.$key.'="'.$w_long.'" lat'.$key.'="'.$param['w_lat'][$key].'" info'.$key.'="'.$param['w_info'][$key].'"';
					}

					$rep ='<div type="type_map" class="wdform-field" style="width:'.($param['w_width']).'px"><div class="wdform-label-section" style="display: table-cell;"><span id="wdform_'.$id1.'_element_label'.$form_id.'" style="display: none;">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_width'].'px;"><div id="wdform_'.$id1.'_element'.$form_id.'" zoom="'.$param['w_zoom'].'" center_x="'.$param['w_center_x'].'" center_y="'.$param['w_center_y'].'" style="width: 100%; height: '.$param['w_height'].'px;" '.$marker.' '.$param['attributes'].'></div></div></div>';
					
					$onload_js .='if_gmap_init("wdform_'.$id1.'", '.$form_id.');';
					
					foreach($param['w_long'] as $key => $w_long )
					{
						$onload_js .='add_marker_on_map("wdform_'.$id1.'",'.$key.', "'.$w_long.'", "'.$param['w_lat'][$key].'", "'.$param['w_info'][$key].'", '.$form_id.',false);';
					}
					
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);
	
					$param['w_first_val']=htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_dollars'.$form_id, $w_first_val[0])).'***'.htmlspecialchars($input_get->getString('wdform_'.$id1.'_element_cents'.$form_id, $w_first_val[1]));

					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
					$required = ($param['w_required']=="yes" ? true : false);	
					$hide_cents = ($param['w_hide_cents']=="yes" ? "none;" : "table-cell;");	
					
					$w_first_val = explode('***',$param['w_first_val']);
					$w_title = explode('***',$param['w_title']);
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					
					$rep ='<div type="type_paypal_price" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><input type="hidden" value="'.$param['w_range_min'].'" name="wdform_'.$id1.'_range_min'.$form_id.'" id="wdform_'.$id1.'_range_min'.$form_id.'"><input type="hidden" value="'.$param['w_range_max'].'" name="wdform_'.$id1.'_range_max'.$form_id.'" id="wdform_'.$id1.'_range_max'.$form_id.'"><div id="wdform_'.$id1.'_table_price" style="display: table;"><div id="wdform_'.$id1.'_tr_price1" style="display: table-row;"><div id="wdform_'.$id1.'_td_name_currency" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;"><!--repstart-->&nbsp;'.$form_currency.'&nbsp;<!--repend--></span></div><div id="wdform_'.$id1.'_td_name_dollars" style="display: table-cell;"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_dollars'.$form_id.'" name="wdform_'.$id1.'_element_dollars'.$form_id.'" value="'.$w_first_val[0].'" title="'.$w_title[0].'" onkeypress="return check_isnum(event)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].'></div><div id="wdform_'.$id1.'_td_name_divider" style="display: '.$hide_cents.';"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;.&nbsp;</span></div><div id="wdform_'.$id1.'_td_name_cents" style="display: '.$hide_cents.'"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_cents'.$form_id.'" name="wdform_'.$id1.'_element_cents'.$form_id.'" value="'.$w_first_val[1].'" title="'.$w_title[1].'" style="width: 30px;" '.$param['attributes'].'></div></div><div id="wdform_'.$id1.'_tr_price2" style="display: table-row;"><div style="display: table-cell;"><label class="mini_label"></label></div><div align="left" style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[0].'</label></div><div id="wdform_'.$id1.'_td_name_label_divider" style="display: '.$hide_cents.'"><label class="mini_label"></label></div><div align="left" id="wdform_'.$id1.'_td_name_label_cents" style="display: '.$hide_cents.'"><label class="mini_label">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
					
					$onload_js .='wdformjQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").blur(function() {add_0(this)});';
					$onload_js .='wdformjQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").keypress(function() {return check_isnum_interval(event,this,0,99)});';

					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val()=="'.$w_title[0].'" || wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").focus();
								return false;
							}
						}
						';		
					$check_js.='
					if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
					{
						dollars=0;
						cents=0;
					
						if(wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val()!="'.$w_title[0].'" || wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val())
							dollars =wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val();
						
						if(wdformjQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").val()!="'.$w_title[1].'" || wdformjQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").val())
							cents =wdformjQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").val();

						var price=dollars+"."+cents;

						if(isNaN(price))
						{
							alert("Invalid value of number field");
							old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
							x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
							wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").focus();
							return false;
						}
					
						var range_min='.($param['w_range_min'] ? $param['w_range_min'] : 0).';
						var range_max='.($param['w_range_max'] ? $param['w_range_max'] : -1).';

						
						if('.($required ? 'true' : 'false').' || wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val()!="'.$w_title[0].'" || wdformjQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").val()!="'.$w_title[1].'")
							if((range_max!=-1 && parseFloat(price)>range_max) || parseFloat(price)<range_min)
							{		
								alert("'.JText::sprintf('WDF_RANGE_FIELD', $label, ($param['w_range_min'] ? $param['w_range_min'] : 0), ($param['w_range_max'] ? $param['w_range_max'] : "any")).'");

								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").focus();
								return false;
							}
					}
					';
					
					break;
				}
				
				case 'type_paypal_select':
				{

					$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity', 'w_quantity_value','w_class','w_property','w_property_values');
					$temp=$params;
					if(strpos($temp, 'w_choices_params') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity', 'w_quantity_value', 'w_choices_params','w_class','w_property','w_property_values');

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
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_price']	= explode('***',$param['w_choices_price']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					$param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);
					$param['w_property']	= explode('***',$param['w_property']);
					$param['w_property_values']	= explode('***',$param['w_property_values']);

					if(isset($param['w_choices_params']))
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
				
					$post_value=$input_get->getString('wdform_'.$id1."_element".$form_id);
					
					$rep='<div type="type_paypal_select" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].'; width: '.$param['w_size'].'px;"><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width:100%;"  '.$param['attributes'].'>';
					foreach($param['w_choices'] as $key => $choice)
					{	
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

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));
							$value_column = $param['w_choices_disabled'][$key]=="true" ? '' : $value_table_and_column[1];

							if($value_column)
							{
								$db->setQuery("SELECT `".$value_column."` FROM ".$table.$where." ORDER BY ".$order_by); 
								$choices_values = $db->loadColumn();
								if ($db->getErrorNum())	{echo $db->stderr(); return false;}
							}		
							
							$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							
							for($k=0; $k<$columns_count; $k++)
							{
								$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';
								$choice_value = isset($choices_values[$k]) ? (float)$choices_values[$k] : '';
								
								if(isset($post_value))
								{							
									if($post_value==$choice_value && $choice_label==$input_get->getString("wdform_".$id1."_element_label".$form_id))
										$param['w_choices_checked'][$key]='selected="selected"';
									else
										$param['w_choices_checked'][$key]='';
								}	
								else
									$param['w_choices_checked'][$key]=(($param['w_choices_checked'][$key]=='true' && $k == 0) ? 'selected="selected"' : '');

								$rep.='<option value="'.$choice_value.'" '.$param['w_choices_checked'][$key].'>'.$choice_label.'</option>';
							}			
						}
						else
						{
							$choice_value = $param['w_choices_disabled'][$key]=="true" ? '' : $param['w_choices_price'][$key];
							if(isset($post_value))
							{	
								if($post_value==$choice_value && $choice==$input_get->getString("wdform_".$id1."_element_label".$form_id))
									$param['w_choices_checked'][$key]='selected="selected"';
								else
									$param['w_choices_checked'][$key]='';	
							}	
							else	
							{
								if($param['w_choices_checked'][$key]=='true')
									$param['w_choices_checked'][$key]='selected="selected"';
								else
									$param['w_choices_checked'][$key]='';
							}
								
							$rep.='<option value="'.$choice_value.'" '.$param['w_choices_checked'][$key].'>'.$choice.'</option>';
						}	
					}
					$rep.='</select><div id="wdform_'.$id1.'_div'.$form_id.'">';
					if($param['w_quantity']=="yes")
					{
						$rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">'.JText::_("WDF_QUANTITY").'</label><input type="text" value="'.$input_get->getString('wdform_'.$id1."_element_quantity".$form_id, $param['w_quantity_value']).'" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" class="wdform-quantity"></div>';
					}
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{
	
					$rep.='
					<div id="wdform_'.$id1.'_property_'.$key.'" class="paypal-property">
					<div style="width:150px; display:inline-block;">
					<label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="width: 100%; margin: 2px 0px;">';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.($input_get->getString('wdform_'.$id1.'_property'.$form_id.''.$key)==$property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';
					}
					$rep.='</select></div></div>';
					}
					
					$rep.='</div></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
						';	

					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\"  />").val(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.' option:selected").text()).appendTo("#form'.$form_id.'");
						';
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("'.JText::_("WDF_QUANTITY").'").appendTo("#form'.$form_id.'");
						';
					foreach($param['w_property'] as $key => $property)
					{
						$onsubmit_js.='
							wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.$key.'\"  />").val("'.$property.'").appendTo("#form'.$form_id.'");
							';
					}
					break;
				}
				
				case 'type_paypal_checkbox':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity', 'w_quantity_value');	

					$temp=$params;
					if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class','w_property','w_property_values','w_quantity', 'w_quantity_value');	

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
					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
				
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");
					$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_price']	= explode('***',$param['w_choices_price']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					$param['w_property']	= explode('***',$param['w_property']);
					$param['w_property_values']	= explode('***',$param['w_property_values']);
					if(isset($param['w_choices_params']))
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

					$rep='<div type="type_paypal_checkbox" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
				
					$post_value=$input_get->getString("counter".$form_id);
					$total_queries = 0;
					foreach($param['w_choices'] as $key => $choice)
					{	
						$key1 = $key + $total_queries;
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
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

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));
							$value_column = $value_table_and_column[1];

							if($value_column)
							{
								$db->setQuery("SELECT `".$value_column."` FROM ".$table.$where." ORDER BY ".$order_by); 
								$choices_values = $db->loadColumn();
								if ($db->getErrorNum())	{echo $db->stderr(); return false;}
							}		
							
							$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							
							if(array_filter($choices_labels) || array_filter($choices_values))
							{
								$total_queries = $total_queries + $columns_count-1;
								if(!isset($post_value))
									$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
									
								for($k=0; $k<$columns_count; $k++)
								{
									$choice_label = isset($choices_labels) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values) ? (float)$choices_values[$k] : '';
									
									if(isset($post_value))
									{	
										$param['w_choices_checked'][$key]="";
										$checkedvalue = $input_get->getString('wdform_'.$id1."_element".$form_id.($key1+$k));

										if(isset($checkedvalue))
											$param['w_choices_checked'][$key]='checked="checked"';							
									}

									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" value="'.$choice_value.'" title="'.htmlspecialchars($choice_label).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div><input type="hidden" name="wdform_'.$id1.'_element'.$form_id.($key1+$k).'_label" value="'.htmlspecialchars($choice_label).'" /></div>';
								}
							}	
						}
						else
						{
							if(isset($post_value))
							{
								$param['w_choices_checked'][$key]="";
								$checkedvalue=$input_get->getString('wdform_'.$id1."_element".$form_id.$key1);
								if(isset($checkedvalue))
									$param['w_choices_checked'][$key]='checked="checked"';		
							}
							else
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
								
							$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.''.$key1.'" value="'.$param['w_choices_price'][$key].'" title="'.htmlspecialchars($choice).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div><input type="hidden" name="wdform_'.$id1.'_element'.$form_id.$key1.'_label" value="'.htmlspecialchars($choice).'" /></div>';
						
						}
					}
			
					$rep.='<div id="wdform_'.$id1.'_div'.$form_id.'">';
					if($param['w_quantity']=="yes")
						$rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">'.JText::_("WDF_QUANTITY").'</label><input type="text" value="'.$input_get->getString('wdform_'.$id1."_element_quantity".$form_id, $param['w_quantity_value']).'" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" class="wdform-quantity"></div>';
						
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{

					$rep.='
					<div class="paypal-property">
					<div style="width:150px; display:inline-block;">
					<label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="width: 100%; margin: 2px 0px;">';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.($input_get->getString('wdform_'.$id1.'_property'.$form_id.''.$key)==$property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';
					}
					$rep.='</select></div></div>';
					}
					
					$rep.='</div></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).length == 0)
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								
								return false;
							}						
						}
						';
						
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\"  />").val((x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).length != 0) ? wdformjQuery("#"+x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).prop("id").replace("element", "elementlabel_")) : "").appendTo("#form'.$form_id.'");
						';

					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("'.JText::_("WDF_QUANTITY").'").appendTo("#form'.$form_id.'");
						';
					foreach($param['w_property'] as $key => $property)
					{
						$onsubmit_js.='
							wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.$key.'\"  />").val("'.$property.'").appendTo("#form'.$form_id.'");
							';
					}
					
						
					break;
				}

				case 'type_paypal_radio':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity', 'w_quantity_value');
					$temp=$params;
					
					if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class', 'w_property','w_property_values','w_quantity', 'w_quantity_value');

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
					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");
					$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_price']	= explode('***',$param['w_choices_price']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					$param['w_property']	= explode('***',$param['w_property']);
					$param['w_property_values']	= explode('***',$param['w_property_values']);
					if(isset($param['w_choices_params']))
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
							
						
					$rep='<div type="type_paypal_radio" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
				
					$post_value=$input_get->getString('wdform_'.$id1."_element".$form_id);
					$total_queries = 0;
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

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));
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
									$choice_label = isset($choices_labels) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values) ? (float)$choices_values[$k] : '';
			
									if(isset($post_value))
										$param['w_choices_checked'][$key]=(($post_value==$choice_value && htmlspecialchars($choice_label)==htmlspecialchars($input_get->getString('wdform_'.$id1."_element_label".$form_id))) ? 'checked="checked"' : '');
									else
										$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');	
		
									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$choice_value.'" title="'.htmlspecialchars($choice_label).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';
								}
							}			
						}
						else
						{
							if(isset($post_value))
								$param['w_choices_checked'][$key]=(($post_value==$param['w_choices_price'][$key] && htmlspecialchars($choice)==htmlspecialchars($input_get->getString('wdform_'.$id1."_element_label".$form_id))) ? 'checked="checked"' : '');
							else
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');	
								
							$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_choices_price'][$key].'" title="'.htmlspecialchars($choice).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';
						}
					}

					$rep.='<div id="wdform_'.$id1.'_div'.$form_id.'">';
					if($param['w_quantity']=="yes")
						$rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">'.JText::_("WDF_QUANTITY").'</label><input type="text" value="'.$input_get->getString('wdform_'.$id1."_element_quantity".$form_id,  $param['w_quantity_value']).'" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" class="wdform-quantity"></div>';
						
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{

					$rep.='
					<div class="paypal-property">
					<div style="width:150px; display:inline-block;">
					<label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="width: 100%; margin: 2px 0px;">';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.($input_get->getString('wdform_'.$id1.'_property'.$form_id.''.$key)==$property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';
					}
					$rep.='</select></div></div>';
					}
					
					$rep.='</div></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).length == 0)
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								
								return false;
							}						
						}
						';		
				
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\" />").val(
						wdformjQuery("label[for=\'"+wdformjQuery("input[name^=\'wdform_'.$id1.'_element'.$form_id.'\']:checked").prop("id")+"\']").eq(0).text()
						).appendTo("#form'.$form_id.'");

						';
						
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("'.JText::_("WDF_QUANTITY").'").appendTo("#form'.$form_id.'");
						';
						
					foreach($param['w_property'] as $key => $property)
					{	
						$onsubmit_js.='
							wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.$key.'\"  />").val("'.$property.'").appendTo("#form'.$form_id.'");
							';
					}						
					break;
				}
				

				case 'type_paypal_shipping':
				{
					$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
					$temp=$params;
					
					if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_choices_params', 'w_class');
						
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
					if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
				
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");
					$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_choices']	= explode('***',$param['w_choices']);
					$param['w_choices_price']	= explode('***',$param['w_choices_price']);
					$param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
					if(isset($param['w_choices_params']))
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
						
						
					$rep='<div type="type_paypal_shipping" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
		
					$post_value=$input_get->getString('wdform_'.$id1."_element".$form_id);
					$total_queries = 0;
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

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));
							$value_column = $value_table_and_column[1];

							if($value_column)
							{
								$db->setQuery("SELECT `".$value_column."` FROM ".$table.$where." ORDER BY ".$order_by); 
								$choices_values = $db->loadColumn();
								if ($db->getErrorNum())	{echo $db->stderr(); return false;}
							}		

							$columns_count_shipping = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							
							if(array_filter($choices_labels) || array_filter($choices_values))
							{
								$total_queries = $total_queries + $columns_count_shipping-1;
								for($k=0; $k<$columns_count_shipping; $k++)
								{
									$choice_label = isset($choices_labels) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values) ? (float)$choices_values[$k] : '';
			
									if(isset($post_value))
										$param['w_choices_checked'][$key]=(($post_value==$choice_value && htmlspecialchars($choice_label)==htmlspecialchars($input_get->getString('wdform_'.$id1."_element_label".$form_id))) ? 'checked="checked"' : '');
									else
										$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');	
										
									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$choice_value.'" title="'.htmlspecialchars($choice_label).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';
								}	
							}	
						}
						else
						{
							if(isset($post_value))
								$param['w_choices_checked'][$key]=(($post_value==$param['w_choices_price'][$key] && htmlspecialchars($choice)==htmlspecialchars($input_get->getString('wdform_'.$id1."_element_label".$form_id))) ? 'checked="checked"' : '');
							else
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');	

							$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_choices_price'][$key].'" title="'.htmlspecialchars($choice).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';
						}
					}

					$rep.='</div></div>';

					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).length == 0)
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								
								return false;
							}						
						}
						';		
				
				
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\" />").val(
						wdformjQuery("label[for=\'"+wdformjQuery("input[name^=\'wdform_'.$id1.'_element'.$form_id.'\']:checked").prop("id")+"\']").eq(0).text()
						).appendTo("#form'.$form_id.'");

						';
						
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					
					$param['w_act'] = ($param['w_act']=="false" ? 'style="display: none;"' : "");	
					
					$rep='<div type="type_submit_reset" class="wdform-field"><div class="wdform-label-section" style="display: table-cell;"></div><div class="wdform-element-section '.$param['w_class'].'" style="display: table-cell;"><button type="button" class="button-submit" onclick="check_required'.$form_id.'(&quot;submit&quot;, &quot;'.$form_id.'&quot;);" '.$param['attributes'].'>'.$param['w_submit_title'].'</button><button type="button" class="button-reset" onclick="check_required'.$form_id.'(&quot;reset&quot;);" '.$param['w_act'].' '.$param['attributes'].'>'.$param['w_reset_title'].'</button></div></div>';
				
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
							$param['attributes'] = $param['attributes'].' '.$attr;
					}
					
					$param['w_title']	= explode('***',$param['w_title']);
					$param['w_func']	= explode('***',$param['w_func']);
					
					
					$rep.='<div type="type_button" class="wdform-field"><div class="wdform-label-section" style="display: table-cell;"><span style="display: none;">button_'.$id1.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="display: table-cell;">';

					foreach($param['w_title'] as $key => $title)
					{
					$rep.='<button type="button" name="wdform_'.$id1.'_element'.$form_id.''.$key.'" onclick="'.$param['w_func'][$key].'" '.$param['attributes'].'>'.$title.'</button>';				
					}				
					$rep.='</div></div>';
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
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					
	
					$images = '';	
					for($i=0; $i<$param['w_star_amount']; $i++)
					{
						$images .= '<img id="wdform_'.$id1.'_star_'.$i.'_'.$form_id.'" src="components/com_formmaker/images/star.png" >';
						
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").mouseover(function() {change_src('.$i.',"wdform_'.$id1.'", '.$form_id.', "'.$param['w_field_label_col'].'");});';
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").mouseout(function() {reset_src('.$i.',"wdform_'.$id1.'", '.$form_id.');});';
						$onload_js .='wdformjQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").click(function() {select_star_rating('.$i.',"wdform_'.$id1.'", '.$form_id.',"'.$param['w_field_label_col'].'", "'.$param['w_star_amount'].'");});';
					}
					
					$rep ='<div type="type_star_rating" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" '.$param['attributes'].'>'.$images.'</div><input type="hidden" value="" id="wdform_'.$id1.'_selected_star_amount'.$form_id.'" name="wdform_'.$id1.'_selected_star_amount'.$form_id.'"></div></div>';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_selected_star_amount'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								return false;
							}
						}
						';
						
						
						
					$post=$input_get->getString('wdform_'.$id1.'_selected_star_amount'.$form_id);
					if(isset($post))
						$onload_js .=' select_star_rating('.($post-1).',"wdform_'.$id1.'", '.$form_id.',"'.$param['w_field_label_col'].'", "'.$param['w_star_amount'].'");';
					
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_star_amount'.$form_id.'\" value = \"'.$param['w_star_amount'].'\" />").appendTo("#form'.$form_id.'");
						';
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
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					
					$w_mini_labels = explode('***',$param['w_mini_labels']);
					
					$numbers = '';	
					$radio_buttons = '';	
					$to_check=0;
					$post_value=$input_get->getString('wdform_'.$id1.'_scale_radio'.$form_id);

					if(isset($post_value))
						$to_check=$post_value;

					for($i=1; $i<=$param['w_scale_amount']; $i++)
					{
						$numbers.= '<div  style="text-align: center; display: table-cell;"><span>'.$i.'</span></div>';
						$radio_buttons.= '<div style="text-align: center; display: table-cell;"><div class="radio-div"><input id="wdform_'.$id1.'_scale_radio'.$form_id.'_'.$i.'" name="wdform_'.$id1.'_scale_radio'.$form_id.'" value="'.$i.'" type="radio" '.( $to_check==$i ? 'checked="checked"' : '' ).'><label for="wdform_'.$id1.'_scale_radio'.$form_id.'_'.$i.'"></label></div></div>';
					}
	
					$rep ='<div type="type_scale_rating" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
	
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" style="float: left;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[0].'</label><div  style="display: inline-table; vertical-align: middle;border-spacing: 7px;"><div style="display: table-row;">'.$numbers.'</div><div style="display: table-row;">'.$radio_buttons.'</div></div><label class="mini_label" >'.$w_mini_labels[1].'</label></div></div></div>';
					

					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(x.find(wdformjQuery("div[wdid='.$id1.'] input:checked")).length == 0)
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								
								return false;
							}						
						}
						';		
				
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_scale_amount'.$form_id.'\" value = \"'.$param['w_scale_amount'].'\" />").appendTo("#form'.$form_id.'");
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
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_field_value']=$input_get->getString('wdform_'.$id1.'_element'.$form_id, $param['w_field_value']);

					$rep ='<div type="type_spinner" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					 
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><input type="text" value="'.($param['w_field_value']!= 'null' ? $param['w_field_value'] : '').'" name="wdform_'.$id1.'_element'.$form_id.'" id="wdform_'.$id1.'_element'.$form_id.'" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'></div></div>';
					
					$onload_js .='
						wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'")[0].spin = null;
						spinner = wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner();
						spinner.spinner( "value", "'.($param['w_field_value']!= 'null' ? $param['w_field_value'] : '').'");
						wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ min: "'.$param['w_field_min_value'].'"});    
						wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ max: "'.$param['w_field_max_value'].'"});
						wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ step: "'.$param['w_field_step'].'"});
					';

					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( wdformjQuery(this).val()!="" ) wdformjQuery(this).removeClass("form-error"); else wdformjQuery(this).addClass("form-error");});
								return false;
							}
						}
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
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_field_value']=$input_get->getString('wdform_'.$id1.'_slider_value'.$form_id, $param['w_field_value']);

					$rep ='<div type="type_slider" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
 
					 
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><input type="hidden" value="'.$param['w_field_value'].'" id="wdform_'.$id1.'_slider_value'.$form_id.'" name="wdform_'.$id1.'_slider_value'.$form_id.'"><div name="'.$id1.'_element'.$form_id.'" id="wdform_'.$id1.'_element'.$form_id.'" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'"></div><div align="left" style="display: inline-block; width: 33.3%; text-align:left;"><span id="wdform_'.$id1.'_element_min'.$form_id.'" class="wdform-label">'.$param['w_field_min_value'].'</span></div><div align="right" style="display: inline-block; width: 33.3%; text-align: center;"><span id="wdform_'.$id1.'_element_value'.$form_id.'" class="wdform-label">'.$param['w_field_value'].'</span></div><div align="right" style="display: inline-block; width: 33.3%; text-align:right;"><span id="wdform_'.$id1.'_element_max'.$form_id.'" class="wdform-label">'.$param['w_field_max_value'].'</span></div></div></div>';
							
							
					$onload_js .='
						wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'")[0].slide = null;
						wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'").slider({
							range: "min",
							value: eval('.$param['w_field_value'].'),
							min: eval('.$param['w_field_min_value'].'),
							max: eval('.$param['w_field_max_value'].'),
							slide: function( event, ui ) {	
							
								wdformjQuery("#wdform_'.$id1.'_element_value'.$form_id.'").html("" + ui.value)
								wdformjQuery("#wdform_'.$id1.'_slider_value'.$form_id.'").val("" + ui.value)

							}
							});
					';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_slider_value'.$form_id.'").val()=='.$param['w_field_min_value'].')
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								return false;
							}
						}
						';		

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
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					$param['w_field_value1']=$input_get->getString('wdform_'.$id1.'_element'.$form_id.'0', $param['w_field_value1']);
					$param['w_field_value2']=$input_get->getString('wdform_'.$id1.'_element'.$form_id.'1', $param['w_field_value2']);

					$w_mini_labels = explode('***',$param['w_mini_labels']);
					
					$rep ='<div type="type_range" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';


					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div style="display: table;"><div style="display: table-row;"><div valign="middle" align="left" style="display: table-cell;"><input type="text" value="'.($param['w_field_value1']!= 'null' ? $param['w_field_value1'] : '').'" name="wdform_'.$id1.'_element'.$form_id.'0" id="wdform_'.$id1.'_element'.$form_id.'0" style="width: '.$param['w_field_range_width'].'px;"  '.$param['attributes'].'></div><div valign="middle" align="left" style="display: table-cell; padding-left: 4px;"><input type="text" value="'.($param['w_field_value2']!= 'null' ? $param['w_field_value2'] : '').'" name="wdform_'.$id1.'_element'.$form_id.'1" id="wdform_'.$id1.'_element'.$form_id.'1" style="width: '.$param['w_field_range_width'].'px;" '.$param['attributes'].'></div></div><div style="display: table-row;"><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="wdform_'.$id1.'_mini_label_from">'.$w_mini_labels[0].'</label></div><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="wdform_'.$id1.'_mini_label_to">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
											
					
					
					
					$onload_js .='
						wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'0")[0].spin = null;
						wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'1")[0].spin = null;
						
						spinner0 = wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'0").spinner();
						spinner0.spinner( "value", "'.($param['w_field_value1']!= 'null' ? $param['w_field_value1'] : '').'");
						wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ step: '.$param['w_field_range_step'].'});
						
						spinner1 = wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'1").spinner();
						spinner1.spinner( "value", "'.($param['w_field_value2']!= 'null' ? $param['w_field_value2'] : '').'");
						wdformjQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ step: '.$param['w_field_range_step'].'});
					';
					
					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'0").val()=="" || wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'1").val()=="")
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'0").focus();
								return false;
							}
						}
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
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					
					$w_items = explode('***',$param['w_items']);
					$required_check='true';
					$w_items_labels =implode(':',$w_items);
					
					$grading_items ='';
					
				
					for($i=0; $i<count($w_items); $i++)
					{
						$value=$input_get->getString('wdform_'.$id1.'_element'.$form_id.'_'.$i, '');

						$grading_items .= '<div class="wdform_grading"><input type="text" id="wdform_'.$id1.'_element'.$form_id.'_'.$i.'" name="wdform_'.$id1.'_element'.$form_id.'_'.$i.'"  value="'.$value.'" '.$param['attributes'].'><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.'_'.$i.'">'.$w_items[$i].'</label></div>';
						
						$required_check.=' && wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'_'.$i.'").val()==""';
					}
						
					$rep ='<div type="type_grading" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';

						
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><input type="hidden" value="'.$param['w_total'].'" name="wdform_'.$id1.'_grading_total'.$form_id.'" id="wdform_'.$id1.'_grading_total'.$form_id.'"><div id="wdform_'.$id1.'_element'.$form_id.'">'.$grading_items.'<div id="wdform_'.$id1.'_element_total_div'.$form_id.'" class="grading_div">Total: <span id="wdform_'.$id1.'_sum_element'.$form_id.'">0</span>/<span id="wdform_'.$id1.'_total_element'.$form_id.'">'.$param['w_total'].'</span><span id="wdform_'.$id1.'_text_element'.$form_id.'"></span></div></div></div></div>';
					
					$onload_js.='
					wdformjQuery("#wdform_'.$id1.'_element'.$form_id.' input").change(function() {sum_grading_values("wdform_'.$id1.'",'.$form_id.');});';
					
					$onload_js.='
					wdformjQuery("#wdform_'.$id1.'_element'.$form_id.' input").keyup(function() {sum_grading_values("wdform_'.$id1.'",'.$form_id.');});';
					
					$onload_js.='
					sum_grading_values("wdform_'.$id1.'",'.$form_id.');';

					if($required)
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if('.$required_check.')
							{
								alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
								old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								wdformjQuery("#wdform_'.$id1.'_element'.$form_id.'0").focus();
								return false;
							}
						}

						';		
						
						$check_js.='
						if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
						{
							if(parseInt(wdformjQuery("#wdform_'.$id1.'_sum_element'.$form_id.'").html()) > '.$param['w_total'].')
							{
								alert("'.addslashes(JText::sprintf('WDF_INVALID_GRADING', '"'.$label.'"', $param['w_total'] )).'");
								return false;
							}
						}
						';		
					
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_item'.$form_id.'\" value = \"'.$w_items_labels.':'.$param['w_total'].'\" />").appendTo("#form'.$form_id.'");
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
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
					$required = ($param['w_required']=="yes" ? true : false);	
					
					
					$w_rows = explode('***',$param['w_rows']);
					$w_columns = explode('***',$param['w_columns']);
					
					
					$column_labels ='';
				
					for($i=1; $i<count($w_columns); $i++)
					{
						$column_labels .= '<div><label class="wdform-ch-rad-label">'.$w_columns[$i].'</label></div>';
					}
					
					$rows_columns = '';
					
					
					
					for($i=1; $i<count($w_rows); $i++)
					{
					
						$rows_columns .= '<div class="wdform-matrix-row'.($i%2).'" row="'.$i.'"><div class="wdform-matrix-column"><label class="wdform-ch-rad-label" >'.$w_rows[$i].'</label></div>';
						
					
						for($k=1; $k<count($w_columns); $k++)
						{
							$rows_columns .= '<div class="wdform-matrix-cell">';
							if($param['w_field_input_type']=='radio')
							{
								$to_check=0;
								$post_value=$input_get->getString('wdform_'.$id1.'_input_element'.$form_id.''.$i);

								if(isset($post_value))
									$to_check=$post_value;	
							
								$rows_columns .= '<div class="radio-div"><input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"  type="radio" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'" value="'.$i.'_'.$k.'" '.($to_check==$i.'_'.$k ? 'checked="checked"' : '').'><label for="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"></label></div>';
								
							}
							else
								if($param['w_field_input_type']=='checkbox')
								{
									$to_check=0;
									$post_value=$input_get->getString('wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k);

									if(isset($post_value))
										$to_check=$post_value;	
										
									$rows_columns .= '<div class="checkbox-div"><input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" type="checkbox" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" value="1" '.($to_check=="1" ? 'checked="checked"' : '').'><label for="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"></label></div>';
								}
								else
									if($param['w_field_input_type']=='text')
										$rows_columns .= '<input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" type="text" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" value="'.$input_get->getString('wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k).'">';
									else
										if($param['w_field_input_type']=='select')
											$rows_columns .= '<select id="wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k.'" name="wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k.'" ><option value="" '.($input_get->getString('wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k)=="" ? "selected=\"selected\"": "").'> </option><option value="yes" '.($input_get->getString('wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k)=="yes" ? "selected=\"selected\"": "").'>Yes</option><option value="no" '.($input_get->getString('wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k)=="no" ? "selected=\"selected\"": "").'>No</option></select>';
							$rows_columns.='</div>';
						}
							
						$rows_columns .= '</div>';	
					}
						
					$rep ='<div type="type_matrix" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';


						
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" class="wdform-matrix-table" '.$param['attributes'].'><div style="display: table-row-group;"><div class="wdform-matrix-head"><div style="display: table-cell;"></div>'.$column_labels.'</div>'.$rows_columns.'</div></div></div></div>';
					
					$onsubmit_js.='
						wdformjQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_input_type'.$form_id.'\" value = \"'.$param['w_field_input_type'].'\" /><input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_row'.$form_id.'\" value = \"'.addslashes($param['w_rows']).'\" /><input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_column'.$form_id.'\" value = \"'.addslashes($param['w_columns']).'\" />").appendTo("#form'.$form_id.'");
						';	

					if($required)
					{
						if($param['w_field_input_type']=='radio')
						{
							$check_js.='
							if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
							{
								var radio_checked=true;
								for(var k=1; k<'.count($w_rows).';k++)
								{
									if(x.find(wdformjQuery("div[wdid='.$id1.']")).find(wdformjQuery("div[row="+k+"]")).find(wdformjQuery("input[type=\'radio\']:checked")).length == 0)
									{
										radio_checked=false;
										break;
									}
								}
								
								if(radio_checked==false)
								{
									alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
									return false;
								}
							}
							';		
						}
						
						if($param['w_field_input_type']=='checkbox')
						{
							$check_js.='
							if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
							{
								if(x.find(wdformjQuery("div[wdid='.$id1.']")).find(wdformjQuery("input[type=\'checkbox\']:checked")).length == 0)
								{
									alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
									return false;
								}
							}
							
							';		
						}
						
						if($param['w_field_input_type']=='text')
						{
							$check_js.='
							if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
							{
								if(x.find(wdformjQuery("div[wdid='.$id1.']")).find(wdformjQuery("input[type=\'text\']")).filter(function() {return this.value.length !== 0;}).length == 0)
								{
									alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
									return false;
								}
							}
							
							';		
						}
						
						if($param['w_field_input_type']=='select')
						{
							$check_js.='
							if(x.find(wdformjQuery("div[wdid='.$id1.']")).length != 0 && x.find(wdformjQuery("div[wdid='.$id1.']")).css("display") != "none")
							{
								if(x.find(wdformjQuery("div[wdid='.$id1.']")).find(wdformjQuery("select")).filter(function() {return this.value.length !== 0;}).length == 0)
								{
									alert("'.addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '"'.$label.'"') ).'");
									old_bg=x.find(wdformjQuery("div[wdid='.$id1.']")).css("background-color");
									x.find(wdformjQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
									return false;
								}
							}
							
							';		
						}
					}

						
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
					
					$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
					$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
					
				
								
					$rep ='<div type="type_paypal_total" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
									
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div id="wdform_'.$id1.'paypal_total'.$form_id.'" class="wdform_paypal_total paypal_total'.$form_id.'"><input type="hidden" value="" name="wdform_'.$id1.'_paypal_total'.$form_id.'" class="input_paypal_total'.$form_id.'"><div id="wdform_'.$id1.'div_total'.$form_id.'" class="div_total'.$form_id.'" style="margin-bottom: 10px;"></div><div id="wdform_'.$id1.'paypal_products'.$form_id.'" class="paypal_products'.$form_id.'" style="border-spacing: 2px;"><div style="border-spacing: 2px;"></div><div style="border-spacing: 2px;"></div></div><div id="wdform_'.$id1.'paypal_tax'.$form_id.'" class="paypal_tax'.$form_id.'" style="border-spacing: 2px; margin-top: 7px;"></div></div></div></div>';
				
					$onload_js .='set_total_value('.$form_id.');';

					break;
				}



			}
			
			$form=str_replace('%'.$id1.' - '.$labels[$id1s_key].'%', $rep, $form);
			$form=str_replace('%'.$id1.' -'.$labels[$id1s_key].'%', $rep, $form);
		}
		
	}
	$onload_js.='wdformjQuery("<input type=\"hidden\" name=\"hidden_field_for_validation'.$form_id.'\" value =\"\" />").appendTo("#form'.$form_id.'")';
	$onsubmit_js.='
	var disabled_fields ="";	
	wdformjQuery("div[wdid]").each(function() {
		if(wdformjQuery(this).css("display")=="none")
		{		
			disabled_fields += wdformjQuery(this).attr("wdid");
			disabled_fields += ",";
		}	

			if(disabled_fields)
			wdformjQuery("<input type=\"hidden\" name=\"disabled_fields'.$form_id.'\" value =\""+disabled_fields+"\" />").appendTo("#form'.$form_id.'");
			
	})';	
	
	$rep1=array('form_id_temp');
	$rep2=array($id);

	$form = str_replace($rep1,$rep2,$form);

	echo $form;
?>

<div class="wdform_preload"></div>

</form>
<script type="text/javascript">
JURI_ROOT				='<?php echo JURI::root(true) ?>';  
WDF_GRADING_TEXT		='<?php echo JText::_("WDF_GRADING_TEXT") ?>'; 
WDF_INVALID_GRADING 	= '<?php echo JText::sprintf('WDF_INVALID_GRADING', '`grading_label`', '`grading_total`') ?>';   
FormCurrency				='<?php echo $form_currency ?>';  
FormPaypalTax				='<?php echo $form_paypal_tax ?>';  



function formOnload<?php echo $id; ?>()
{
	if (wdformjQuery.browser.msie  && parseInt(wdformjQuery.browser.version, 10) === 8) 
	{
		wdformjQuery("#form<?php echo $id; ?>").find(wdformjQuery("input[type='radio']")).click(function() {wdformjQuery("input[type='radio']+label").removeClass('if-ie-div-label'); wdformjQuery("input[type='radio']:checked+label").addClass('if-ie-div-label')});	
		wdformjQuery("#form<?php echo $id; ?>").find(wdformjQuery("input[type='radio']:checked+label")).addClass('if-ie-div-label');
		
		wdformjQuery("#form<?php echo $id; ?>").find(wdformjQuery("input[type='checkbox']")).click(function() {wdformjQuery("input[type='checkbox']+label").removeClass('if-ie-div-label'); wdformjQuery("input[type='checkbox']:checked+label").addClass('if-ie-div-label')});	
		wdformjQuery("#form<?php echo $id; ?>").find(wdformjQuery("input[type='checkbox']:checked+label")).addClass('if-ie-div-label');
	}
	
	if(wdformjQuery.browser.msie)
	{

		wdformjQuery(".wdform-calendar-button").click(function() {
	
			var pos = wdformjQuery(this).offset().top;
			setTimeout(function(){
				wdformjQuery(".calendar").each(function (){
				if(wdformjQuery(this).css("display") == "block"){
				wdformjQuery(this).css("top", parseInt(pos)-wdformjQuery(this).height()+"px");
				}
			});

			},300);	
		
		});
	}

	wdformjQuery("div[type='type_text'] input, div[type='type_number'] input, div[type='type_phone'] input, div[type='type_name'] input, div[type='type_submitter_mail'] input, div[type='type_paypal_price'] input, div[type='type_textarea'] textarea").focus(function() {delete_value(this)}).blur(function() {return_value(this)});
	wdformjQuery("div[type='type_number'] input, div[type='type_phone'] input, div[type='type_spinner'] input, div[type='type_range'] input, .wdform-quantity").keypress(function(evt) {return check_isnum(evt)});
	
	wdformjQuery("div[type='type_grading'] input").keypress(function() {return check_isnum_or_minus(event)});
	
	wdformjQuery("div[type='type_paypal_checkbox'] input[type='checkbox'], div[type='type_paypal_radio'] input[type='radio'], div[type='type_paypal_shipping'] input[type='radio']").click(function() {set_total_value(<?php echo $form_id; ?>)});
	wdformjQuery("div[type='type_paypal_select'] select, div[type='type_paypal_price'] input").change(function() {set_total_value(<?php echo $form_id; ?>)});
	wdformjQuery(".wdform-quantity").change(function() {set_total_value(<?php echo $form_id; ?>)});

	wdformjQuery("div[type='type_time'] input").blur(function() {add_0(this)});

	wdformjQuery('.wdform-element-section').each(function() {
		if(!wdformjQuery(this).parent()[0].style.width && parseInt(wdformjQuery(this).width())!=0)
		{
			
			if(wdformjQuery(this).css('display')=="table-cell")
			{
				if(wdformjQuery(this).parent().attr('type')!="type_captcha")
					wdformjQuery(this).parent().css('width', parseInt(wdformjQuery(this).width()) + parseInt(wdformjQuery(this).parent().find(wdformjQuery(".wdform-label-section"))[0].style.width)+15);
				else
					wdformjQuery(this).parent().css('width', (parseInt(wdformjQuery(this).parent().find(wdformjQuery(".captcha_input"))[0].style.width)*2+50) + parseInt(wdformjQuery(this).parent().find(wdformjQuery(".wdform-label-section"))[0].style.width)+15);
			}
		}
	
		if(parseInt(wdformjQuery(this)[0].style.width.replace('px', '')) < parseInt(wdformjQuery(this).css('min-width').replace('px', '')))
			wdformjQuery(this).css('min-width', parseInt(wdformjQuery(this)[0].style.width.replace('px', ''))-10);
	});
	
	wdformjQuery('.wdform-label').each(function() {
		if(parseInt(wdformjQuery(this).height()) >= 2*parseInt(wdformjQuery(this).css('line-height').replace('px', '')))
		{
			wdformjQuery(this).parent().css('max-width',wdformjQuery(this).parent().width());
			wdformjQuery(this).parent().css('width','');
		}
	});
	
	(function(wdformjQuery){
		wdformjQuery.fn.shuffle = function() {
			var allElems = this.get(),
				getRandom = function(max) {
					return Math.floor(Math.random() * max);
				},
				shuffled = wdformjQuery.map(allElems, function(){
					var random = getRandom(allElems.length),
						randEl = wdformjQuery(allElems[random]).clone(true)[0];
					allElems.splice(random, 1);
					return randEl;
			   });
			this.each(function(i){
				wdformjQuery(this).replaceWith(wdformjQuery(shuffled[i]));
			});
			return wdformjQuery(shuffled);
		};
	})(wdformjQuery);	
	
	<?php echo $onload_js; ?>
	<?php echo $condition_js; ?>
	
	if(window.before_load)

	{

		before_load();

	}	

}

function formAddToOnload<?php echo $id ?>()
{ 

	if(formOldFunctionOnLoad<?php echo $id ?>){ formOldFunctionOnLoad<?php echo $id ?>(); }
	formOnload<?php echo $id ?>();
}

function formLoadBody<?php echo $id ?>()
{
	formOldFunctionOnLoad<?php echo $id ?> = window.onload;
	window.onload = formAddToOnload<?php echo $id ?>;
}

var formOldFunctionOnLoad<?php echo $id ?> = null;
formLoadBody<?php echo $id ?>();

	form_view_count<?php echo $id ?>=0;
	for(i=1; i<=30; i++)
	{
		if(document.getElementById('<?php echo $id ?>form_view'+i))
		{
			form_view_count<?php echo $id ?>++;
			form_view_max<?php echo $id ?>=i;

		}
	}

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


	function check_required<?php echo $form_id ?>(but_type)
	{
		if(but_type=='reset')
		{
			if(window.before_reset)
			{
				before_reset();
			}
			window.location="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>";
			return;
		}

		if(window.before_submit)
		{
			before_submit();
		}

		x=wdformjQuery("#form<?php echo $form_id; ?>");
		<?php echo $check_js ?> ;

		if(a[<?php echo $form_id ?>]==1)
			return;
		<?php echo $onsubmit_js; ?>;
		a[<?php echo $form_id ?>]=1;
		document.getElementById("form"+<?php echo $form_id ?>).submit();
	}


	function check<?php echo $form_id ?>(id)
	{
		x=wdformjQuery("#<?php echo $form_id ?>form_view"+id);
		<?php echo $check_js ?> ;
		return true;
	}
	
//	wdformjQuery('.wdform-element-section select').each(function() { reselect(this,''); })

</script>
	<?php
	}
	else
	{



//inch@ petq chi raplace minchev form@ tpi			
			
			
			$rep1=array(
			"<!--repstart-->Title<!--repend-->",
			"<!--repstart-->First<!--repend-->",
			"<!--repstart-->Last<!--repend-->",
			"<!--repstart-->Middle<!--repend-->",
			"<!--repstart-->January<!--repend-->",
			"<!--repstart-->February<!--repend-->",
			"<!--repstart-->March<!--repend-->",
			"<!--repstart-->April<!--repend-->",
			"<!--repstart-->May<!--repend-->",
			"<!--repstart-->June<!--repend-->",
			"<!--repstart-->July<!--repend-->",
			"<!--repstart-->August<!--repend-->",
			"<!--repstart-->September<!--repend-->",
			"<!--repstart-->October<!--repend-->",
			"<!--repstart-->November<!--repend-->",
			"<!--repstart-->December<!--repend-->",
			"<!--repstart-->Street Address<!--repend-->",
			"<!--repstart-->Street Address Line 2<!--repend-->",
			"<!--repstart-->City<!--repend-->",
			"<!--repstart-->State / Province / Region<!--repend-->",
			"<!--repstart-->Postal / Zip Code<!--repend-->",
			"<!--repstart-->Country<!--repend-->",
			"<!--repstart-->Area Code<!--repend-->",
			"<!--repstart-->Phone Number<!--repend-->",
			"<!--repstart-->Dollars<!--repend-->",

			"<!--repstart-->Cents<!--repend-->",

			"<!--repstart-->&nbsp;$&nbsp;<!--repend-->",

			"<!--repstart-->Quantity<!--repend-->",
			"<!--repstart-->From<!--repend-->",				
			"<!--repstart-->To<!--repend-->",
			"<!--repstart-->$300<!--repend-->",
			"<!--repstart-->product 1 $100<!--repend-->",
			"<!--repstart-->product 2 $200<!--repend-->",
			'class="captcha_img"',

			'form_id_temp',
			'../index.php?option=com_formmaker&amp;view=wdcaptcha',
			'style="padding-right:170px"');
			
			$rep2=array(
			JText::_("WDF_NAME_TITLE_LABEL"),
			JText::_("WDF_FIRST_NAME_LABEL"),
			JText::_("WDF_LAST_NAME_LABEL"),
			JText::_("WDF_MIDDLE_NAME_LABEL"),
			JText::_("January"),
			JText::_("February"),
			JText::_("March"),
			JText::_("April"),
			JText::_("May"),
			JText::_("June"),
			JText::_("July"),
			JText::_("August"),
			JText::_("September"),
			JText::_("October"),
			JText::_("November"),
			JText::_("December"),
			JText::_("WDF_STREET_ADDRESS"),
			JText::_("WDF_STREET_ADDRESS2"),
			JText::_("WDF_CITY"),
			JText::_("WDF_STATE"),
			JText::_("WDF_POSTAL"),
			JText::_("WDF_COUNTRY"),
			JText::_("WDF_AREA_CODE"),
			JText::_("WDF_PHONE_NUMBER"),
			JText::_("WDF_DOLLARS"),

			JText::_("WDF_CENTS"),
			
			'&nbsp;'.$form_currency.'&nbsp;',

			JText::_("WDF_QUANTITY"),
			JText::_("WDF_FROM"),		
			JText::_("WDF_TO"),
			'',
			'',
			'',
			'class="captcha_img" style="display:none"',

			$id,
			'index.php?option=com_formmaker&amp;view=wdcaptcha',
			'');
			
			$untilupload = str_replace($rep1,$rep2,$row->form_front);
			while(strpos($untilupload, "***destinationskizb")>0)
			{
				$pos1 = strpos($untilupload, "***destinationskizb");
				$pos2 = strpos($untilupload, "***destinationverj");
				$untilupload=str_replace(substr($untilupload, $pos1, $pos2-$pos1+22), "", $untilupload);
			}
echo $untilupload;

$is_recaptcha=false;
	
?>
<script src="<?php echo $cmpnt_js_path ?>/main.js" type="text/javascript"></script>
<script type="text/javascript">
//genid='<?php echo $id ?>';
//genform_view='<?php echo $id ?>form_view';
//genpage_nav='<?php echo $id ?>page_nav';
//genpages='<?php echo $id ?>pages';
WDF_FILE_TYPE_ERROR 	= '<?php echo JText::_("WDF_FILE_TYPE_ERROR"); ?>';
WDF_INVALID_EMAIL 		= '<?php echo JText::_("WDF_INVALID_EMAIL"); ?>';
WDF_GRADING_TEXT 		= '<?php echo JText::_("WDF_GRADING_TEXT"); ?>';
WDF_INVALID_GRADING 		= '<?php echo JText::sprintf('WDF_INVALID_GRADING', '`grading_label`', '`grading_total`') ?>'; 
REQUEST_URI				= "<?php echo $_SERVER['REQUEST_URI'] ?>";
ReqFieldMsg				='<?php echo addslashes(JText::sprintf('WDF_REQUIRED_FIELD', '`FIELDNAME`') ) ?>';  
RangeFieldMsg			='<?php echo JText::sprintf('WDF_RANGE_FIELD', '`FIELDNAME`', '`FROM`','`TO`') ?>';  
JURI_ROOT				='<?php echo JURI::root(true) ?>';  
FormCurrency				='<?php echo $form_currency ?>';
FormPaypalTax				='<?php echo $form_paypal_tax ?>';  

function formOnload<?php echo $id; ?>()
{
	//enable maps and refresh captcha
<?php 
	foreach($label_type as $key => $type)
	{
		switch ($type)
		{
			case 'type_map':?>
		
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>"))
		{
			if_gmap_init(<?php echo $label_id[$key] ?>, <?php echo $id ?>);
			for(q=0; q<20; q++)
				if(document.getElementById(<?php echo $label_id[$key] ?>+"_element"+<?php echo $id ?>).getAttribute("long"+q))
				{
				
					w_long=parseFloat(document.getElementById(<?php echo $label_id[$key] ?>+"_element"+<?php echo $id ?>).getAttribute("long"+q));
					w_lat=parseFloat(document.getElementById(<?php echo $label_id[$key] ?>+"_element"+<?php echo $id ?>).getAttribute("lat"+q));
					w_info=parseFloat(document.getElementById(<?php echo $label_id[$key] ?>+"_element"+<?php echo $id ?>).getAttribute("info"+q));
					add_marker_on_map(<?php echo $label_id[$key] ?>,q, w_long, w_lat, w_info, <?php echo $id ?>,false);
				}
		}
<?php
			break;
	
			case 'type_mark_map':?>
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>"))
	//if(!document.getElementById("<?php echo $label_id[$key] ?>_long<?php echo $id ?>"))	
	{      	
	
		var longit = document.createElement('input');
         	longit.setAttribute("type", 'hidden');
         	longit.setAttribute("id", '<?php echo $label_id[$key] ?>_long<?php echo $id ?>');
         	longit.setAttribute("name", '<?php echo $label_id[$key] ?>_long<?php echo $id ?>');

		var latit = document.createElement('input');
         	latit.setAttribute("type", 'hidden');
         	latit.setAttribute("id", '<?php echo $label_id[$key] ?>_lat<?php echo $id ?>');
         	latit.setAttribute("name", '<?php echo $label_id[$key] ?>_lat<?php echo $id ?>');

		document.getElementById("<?php echo $label_id[$key] ?>_element_section<?php echo $id ?>").appendChild(longit);
		document.getElementById("<?php echo $label_id[$key] ?>_element_section<?php echo $id ?>").appendChild(latit);
	
		if_gmap_init(<?php echo $label_id[$key] ?>, <?php echo $id ?>);
		
		w_long=parseFloat(document.getElementById(<?php echo $label_id[$key] ?>+"_element"+<?php echo $id ?>).getAttribute("long0"));
		w_lat=parseFloat(document.getElementById(<?php echo $label_id[$key] ?>+"_element"+<?php echo $id ?>).getAttribute("lat0"));
		w_info=parseFloat(document.getElementById(<?php echo $label_id[$key] ?>+"_element"+<?php echo $id ?>).getAttribute("info0"));
		longit.value=w_long;
		latit.value=w_lat;
		add_marker_on_map(<?php echo $label_id[$key] ?>,0, w_long, w_lat, w_info, <?php echo $id ?>, true);
		
	}
<?php			break;
			
			case 'type_date':?>
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>"))
			Calendar.setup({
						inputField: "<?php echo $label_id[$key] ?>_element<?php echo $id ?>",
						ifFormat: document.getElementById("<?php echo $label_id[$key] ?>_button<?php echo $id ?>").getAttribute('format'),
						button: "<?php echo $label_id[$key] ?>_button<?php echo $id ?>",
						align: "Tl",
						singleClick: true,
						firstDay: 0
						});
			
			
<?php
			break;
	
			case 'type_captcha':?>
	if(document.getElementById('_wd_captcha<?php echo $id ?>'))
		captcha_refresh('_wd_captcha', '<?php echo $id ?>');
<?php
			break;
			
			case 'type_recaptcha':
			$is_recaptcha=true;
			
			break;
	
	
			case 'type_radio':
			case 'type_checkbox':?>
	if(document.getElementById('<?php echo $label_id[$key] ?>_randomize<?php echo $id ?>'))
		if(document.getElementById('<?php echo $label_id[$key] ?>_randomize<?php echo $id ?>').value=="yes")
			choises_randomize('<?php echo $label_id[$key] ?>', '<?php echo $id ?>');
<?php
			break;
	
	case 'type_spinner':?>		
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>"))		
	var spinner_value = document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>").get( "aria-valuenow" );		
	
	if(document.getElementById("<?php echo $label_id[$key] ?>_min_value<?php echo $id ?>"))			
	var spinner_min_value = document.getElementById("<?php echo $label_id[$key] ?>_min_value<?php echo $id ?>").value;		
	
	if(document.getElementById("<?php echo $label_id[$key] ?>_max_value<?php echo $id ?>"))			
	var spinner_max_value = document.getElementById("<?php echo $label_id[$key] ?>_max_value<?php echo $id ?>").value;	

	if(document.getElementById("<?php echo $label_id[$key] ?>_step<?php echo $id ?>"))	        
    var spinner_step = document.getElementById("<?php echo $label_id[$key] ?>_step<?php echo $id ?>").value;	

	jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>" ).removeClass( "ui-spinner-input" )		
	.prop( "disabled", false )	
	.removeAttr( "autocomplete" )		
	.removeAttr( "role" )		
	.removeAttr( "aria-valuemin" )	
	.removeAttr( "aria-valuemax" )		
	.removeAttr( "aria-valuenow" );		
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>"))
	{			
		span_ui= document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>").parentNode;		
		span_ui.parentNode.appendChild(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>"));		
		span_ui.parentNode.removeChild(span_ui);		
		jQuery("#<?php echo $label_id[$key] ?>_element<?php echo $id ?>")[0].spin = null;		
	}	
	
	spinner = jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>" ).spinner();	
	spinner.spinner( "value", spinner_value );		
	
		jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>" ).spinner({ min: spinner_min_value});       
		jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>" ).spinner({ max: spinner_max_value});     
		jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>" ).spinner({ step: spinner_step});		
	<?php		
	break;		
	case 'type_slider':?>	
	
	if(document.getElementById("<?php echo $label_id[$key] ?>_slider_value<?php echo $id ?>"))		
	var slider_value = document.getElementById("<?php echo $label_id[$key] ?>_slider_value<?php echo $id ?>").value;
	
	if(document.getElementById("<?php echo $label_id[$key] ?>_slider_min_value<?php echo $id ?>"))			
	var slider_min_value = document.getElementById("<?php echo $label_id[$key] ?>_slider_min_value<?php echo $id ?>").value;	
	
	if(document.getElementById("<?php echo $label_id[$key] ?>_slider_max_value<?php echo $id ?>"))			
	var slider_max_value = document.getElementById("<?php echo $label_id[$key] ?>_slider_max_value<?php echo $id ?>").value;
	
	if(document.getElementById("<?php echo $label_id[$key] ?>_element_value<?php echo $id ?>"))				
	var slider_element_value = document.getElementById("<?php echo $label_id[$key] ?>_element_value<?php echo $id ?>" );	
	
	if(document.getElementById("<?php echo $label_id[$key] ?>_slider_value<?php echo $id ?>"))			
	var slider_value_save = document.getElementById( "<?php echo $label_id[$key] ?>_slider_value<?php echo $id ?>" );	

	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>"))	
	{				
		document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>").innerHTML = "";		
		document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>").removeAttribute( "class" );	
		document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>").removeAttribute( "aria-disabled" );	
	}			  		
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>"))		
	
	jQuery("#<?php echo $label_id[$key] ?>_element<?php echo $id ?>")[0].slide = null;			
	jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>").slider({		
	range: "min",				value: eval(slider_value),	
	min: eval(slider_min_value),	
	max: eval(slider_max_value),		
	slide: function( event, ui ) 
	{		
	slider_element_value.innerHTML = "" + ui.value ;		
	slider_value_save.value = "" + ui.value; 			
	}		
	});	
	<?php	
	break;			
	case 'type_range':?>		
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>0"))			
	var spinner_value0 = document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>0").getAttribute( "aria-valuenow" );		

	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>1"))			
	var spinner_value1 = document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>1").getAttribute( "aria-valuenow" );	

	if(document.getElementById("<?php echo $label_id[$key] ?>_range_step<?php echo $id ?>"))			
	var spinner_step = document.getElementById("<?php echo $label_id[$key] ?>_range_step<?php echo $id ?>").value;		

	jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>0" ).removeClass( "ui-spinner-input" )		
	.prop( "disabled", false )	
	.removeAttr( "autocomplete" )		
	.removeAttr( "role" )			
	.removeAttr( "aria-valuenow" );		
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>0"))
	{		
		span_ui= document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>0").parentNode;		
		span_ui.parentNode.appendChild(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>0"));	
		span_ui.parentNode.removeChild(span_ui);	
		jQuery("#<?php echo $label_id[$key] ?>_element<?php echo $id ?>0")[0].spin = null;			
	}								
		spinner0 = jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>0" ).spinner();		
		spinner0.spinner( "value", spinner_value0 );	
	
	jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>0" ).spinner({ step: spinner_step});
	jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>1" ).removeClass( "ui-spinner-input" )			
	.prop( "disabled", false )		
	.removeAttr( "autocomplete" )		
	.removeAttr( "role" )				
	.removeAttr( "aria-valuenow" );			
	
	if(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>1"))
	{		
		span_ui1= document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>1").parentNode;	
		span_ui1.parentNode.appendChild(document.getElementById("<?php echo $label_id[$key] ?>_element<?php echo $id ?>1"));	
		span_ui1.parentNode.removeChild(span_ui1);		
		jQuery("#<?php echo $label_id[$key] ?>_element<?php echo $id ?>1")[0].spin = null;		
	}				
		spinner1 = jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>1" ).spinner();		
		spinner1.spinner( "value", spinner_value1 );     
		jQuery( "#<?php echo $label_id[$key] ?>_element<?php echo $id ?>1" ).spinner({ step: spinner_step});	
	<?php		
	break;			
	
		case 'type_paypal_total':?>	
	set_total_value(<?php echo $label_id[$key] ?>,<?php echo $id ?>);
	<?php		
	break;
			default:
			break;
		}
	}

?>
	if(window.before_load)
	{
		before_load();
	}	
}

function formAddToOnload<?php echo $id ?>()
{ 
	if(formOldFunctionOnLoad<?php echo $id ?>){ formOldFunctionOnLoad<?php echo $id ?>(); }
	formOnload<?php echo $id ?>();
}

function formLoadBody<?php echo $id ?>()
{
	formOldFunctionOnLoad<?php echo $id ?> = window.onload;
	window.onload = formAddToOnload<?php echo $id ?>;
}

var formOldFunctionOnLoad<?php echo $id ?> = null;
formLoadBody<?php echo $id ?>();

<?php

$captcha_input=$input_get->getString("captcha_input");
$recaptcha_response_field=$input_get->getString("recaptcha_response_field");
$counter=$input_get->getString("counter".$id);
$old_key=-1;
if(isset($counter))
{
	foreach($label_type as $key => $type)
	{
			switch ($type)
			{
			case "type_text":
			case "type_number":		
			case "type_submitter_mail":{
								echo 
	"if(document.getElementById('".$label_id[$key]."_element".$id."'))
		if(document.getElementById('".$label_id[$key]."_element".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element".$id))."')
		{	document.getElementById('".$label_id[$key]."_element".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element".$id))."';
			document.getElementById('".$label_id[$key]."_element".$id."').className='input_active';
		}
	";
								break;
							}
									
			case "type_textarea":{
			$order   = array("\r\n", "\n", "\r");
								echo 
	"if(document.getElementById('".$label_id[$key]."_element".$id."'))
		if(document.getElementById('".$label_id[$key]."_element".$id."').title!='".str_replace($order,'\n',addslashes($input_get->getString($label_id[$key]."_element".$id)))."')
		{	document.getElementById('".$label_id[$key]."_element".$id."').innerHTML='".str_replace($order,'\n',addslashes($input_get->getString($label_id[$key]."_element".$id)))."';
			document.getElementById('".$label_id[$key]."_element".$id."').className='input_active';
		}
	";
								break;
							}
			case "type_name":{
								$element_title=$input_get->getString($label_id[$key]."_element_title".$id);
								if(isset($element_title))
								{
									echo 
	"if(document.getElementById('".$label_id[$key]."_element_first".$id."'))
	{
		if(document.getElementById('".$label_id[$key]."_element_title".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_title".$id))."')
		{	document.getElementById('".$label_id[$key]."_element_title".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_title".$id))."';
			document.getElementById('".$label_id[$key]."_element_title".$id."').className='input_active';
		}
		
		if(document.getElementById('".$label_id[$key]."_element_first".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_first".$id))."')
		{	document.getElementById('".$label_id[$key]."_element_first".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_first".$id))."';
			document.getElementById('".$label_id[$key]."_element_first".$id."').className='input_active';
		}
		
		if(document.getElementById('".$label_id[$key]."_element_last".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_last".$id))."')
		{	document.getElementById('".$label_id[$key]."_element_last".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_last".$id))."';
			document.getElementById('".$label_id[$key]."_element_last".$id."').className='input_active';
		}
		
		if(document.getElementById('".$label_id[$key]."_element_middle".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_middle".$id))."')
		{	document.getElementById('".$label_id[$key]."_element_middle".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_middle".$id))."';
			document.getElementById('".$label_id[$key]."_element_middle".$id."').className='input_active';
		}
		
	}";
								}
								else
								{
								echo 
	"if(document.getElementById('".$label_id[$key]."_element_first".$id."'))
	{
		
		if(document.getElementById('".$label_id[$key]."_element_first".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_first".$id))."')
		{	document.getElementById('".$label_id[$key]."_element_first".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_first".$id))."';
			document.getElementById('".$label_id[$key]."_element_first".$id."').className='input_active';
		}
		
		if(document.getElementById('".$label_id[$key]."_element_last".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_last".$id))."')
		{	document.getElementById('".$label_id[$key]."_element_last".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_last".$id))."';
			document.getElementById('".$label_id[$key]."_element_last".$id."').className='input_active';
		}
		
	}";
								}
								break;
							}
							
			case "type_phone":{
	
								echo 
	"if(document.getElementById('".$label_id[$key]."_element_first".$id."'))
	{
		if(document.getElementById('".$label_id[$key]."_element_first".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_first".$id))."')
		{	document.getElementById('".$label_id[$key]."_element_first".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_first".$id))."';
			document.getElementById('".$label_id[$key]."_element_first".$id."').className='input_active';
		}
		
		if(document.getElementById('".$label_id[$key]."_element_last".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_last".$id))."')
		{	document.getElementById('".$label_id[$key]."_element_last".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_last".$id))."';
			document.getElementById('".$label_id[$key]."_element_last".$id."').className='input_active';
		}
	}";
								
								break;
								}
			
			case "type_paypal_price":{

	

								echo 

	"if(document.getElementById('".$label_id[$key]."_element_dollars".$id."'))

	{

		if(document.getElementById('".$label_id[$key]."_element_dollars".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_dollars".$id))."')

		{	document.getElementById('".$label_id[$key]."_element_dollars".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_dollars".$id))."';

			document.getElementById('".$label_id[$key]."_element_dollars".$id."').className='input_active';

		}

		

		if(document.getElementById('".$label_id[$key]."_element_cents".$id."').title!='".addslashes($input_get->getString($label_id[$key]."_element_cents".$id))."')

		{	document.getElementById('".$label_id[$key]."_element_cents".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_cents".$id))."';

			document.getElementById('".$label_id[$key]."_element_cents".$id."').className='input_active';

		}

	}";

								

								break;

								}

								

								case "type_paypal_select":{

	

								echo 

	"if(document.getElementById('".$label_id[$key]."_element".$id."')){

		document.getElementById('".$label_id[$key]."_element".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element".$id))."';

	

	if(document.getElementById('".$label_id[$key]."_element_quantity".$id."'))

		document.getElementById('".$label_id[$key]."_element_quantity".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_quantity".$id))."';

		";

		for($j=0; $j<100; $j++)

								{

										$element=$input_get->getString($label_id[$key]."_property".$id.$j);

										if(isset($element))

												{

												echo

	"document.getElementById('".$label_id[$key]."_property".$id.$j."').value='".addslashes($input_get->getString($label_id[$key]."_property".$id.$j))."';

	";

												}

								}

		echo "

		}";

	

								

								break;

								}

					case "type_paypal_checkbox":{

							

							echo

	"

	for(k=0; k<30; k++)

		if(document.getElementById('".$label_id[$key]."_element".$id."'+k))

			document.getElementById('".$label_id[$key]."_element".$id."'+k).removeAttribute('checked');

		else break;

	";

								for($j=0; $j<100; $j++)

								{

										$element=$input_get->getString($label_id[$key]."_element".$id.$j);

										if(isset($element))

												{

												echo

	"document.getElementById('".$label_id[$key]."_element".$id.$j."').setAttribute('checked', 'checked');

	";

												}

								}

		

								echo 

	"

	if(document.getElementById('".$label_id[$key]."_element_quantity".$id."'))

		document.getElementById('".$label_id[$key]."_element_quantity".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_quantity".$id))."';

		";

		for($j=0; $j<100; $j++)

								{

										$element=$input_get->getString($label_id[$key]."_property".$id.$j);

										if(isset($element))

												{

												echo

	"document.getElementById('".$label_id[$key]."_property".$id.$j."').value='".addslashes($input_get->getString($label_id[$key]."_property".$id.$j))."';

	";

												}

								};	

								break;

								}		

	case "type_paypal_radio":{

							

							echo

	"

	for(k=0; k<50; k++)

		if(document.getElementById('".$label_id[$key]."_element".$id."'+k))

		{

			document.getElementById('".$label_id[$key]."_element".$id."'+k).removeAttribute('checked');

			if(document.getElementById('".$label_id[$key]."_element".$id."'+k).value=='".addslashes($input_get->getString($label_id[$key]."_element".$id))."')

			{

				document.getElementById('".$label_id[$key]."_element".$id."'+k).setAttribute('checked', 'checked');

								

			}

		}

		



	if(document.getElementById('".$label_id[$key]."_element_quantity".$id."'))

		document.getElementById('".$label_id[$key]."_element_quantity".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element_quantity".$id))."';

		";

		for($j=0; $j<100; $j++)

								{

										$element=$input_get->getString($label_id[$key]."_property".$id.$j);

										if(isset($element))

												{

												echo

	"document.getElementById('".$label_id[$key]."_property".$id.$j."').value='".addslashes($input_get->getString($label_id[$key]."_property".$id.$j))."';

	";

												}

								};

		

	

								

								break;

								}

								

				case "type_paypal_shipping":{

			

								echo

	"

	for(k=0; k<50; k++)

		if(document.getElementById('".$label_id[$key]."_element".$id."'+k))

		{

			document.getElementById('".$label_id[$key]."_element".$id."'+k).removeAttribute('checked');

			if(document.getElementById('".$label_id[$key]."_element".$id."'+k).value=='".addslashes($input_get->getString($label_id[$key]."_element".$id))."')

			{

				document.getElementById('".$label_id[$key]."_element".$id."'+k).setAttribute('checked', 'checked');

								

			}

		}

	

	";

	

						break;

							}					

				case "type_star_rating":{		
						
						echo	
					
					"if(document.getElementById('".$label_id[$key]."_element".$id."'))
					{		
						document.getElementById('".$label_id[$key]."_selected_star_amount".$id."').value='".addslashes($input_get->getString($label_id[$key]."_selected_star_amount".$id))."';	
													
							if(document.getElementById('".$label_id[$key]."_selected_star_amount".$id."').value)	
							select_star_rating((document.getElementById('".$label_id[$key]."_selected_star_amount".$id."').value-1),".$label_id[$key].",".$id.");	
					}	
					";	
					
					break;				
					}			

				case "type_scale_rating":{		
						
						echo	
						
					"for(k=0; k<100; k++)
					{	
						if(document.getElementById('".$label_id[$key]."_scale_radio".$id."_'+k))
						{	
							document.getElementById('".$label_id[$key]."_scale_radio".$id."_'+k).removeAttribute('checked');	
								
							if(document.getElementById('".$label_id[$key]."_scale_radio".$id."_'+k).value=='".$input_get->getString($label_id[$key]."_scale_radio".$id)."')	
								document.getElementById('".$label_id[$key]."_scale_radio".$id."_'+k).setAttribute('checked', 'checked');	
							
						}			
					}";	
					
					break;	
				
				}		
				case "type_spinner":{		
						echo	
				
					"if(document.getElementById('".$label_id[$key]."_element".$id."'))
						document.getElementById('".$label_id[$key]."_element".$id."').setAttribute('aria-valuenow','".$input_get->getString($label_id[$key]."_element".$id)."');	
					";			
				
					break;				
				}			
				case "type_slider":{	
						echo	
						
					"if(document.getElementById('".$label_id[$key]."_element".$id."'))		
					document.getElementById('".$label_id[$key]."_element".$id."').setAttribute('aria-valuenow','".$input_get->getString($label_id[$key]."_slider_value".$id)."');		
					
					if(document.getElementById('".$label_id[$key]."_slider_value".$id."'))		
					document.getElementById('".$label_id[$key]."_slider_value".$id."').value='".$input_get->getString($label_id[$key]."_slider_value".$id)."';		
									
					if(document.getElementById('".$label_id[$key]."_element_value".$id."'))	
					document.getElementById('".$label_id[$key]."_element_value".$id."').innerHTML='".$input_get->getString($label_id[$key]."_slider_value".$id)."';		
					";						
				
					break;					
				}				 
				case "type_range":{			
						echo		
						
						"if(document.getElementById('".$label_id[$key]."_element".$id."0'))		
						document.getElementById('".$label_id[$key]."_element".$id."0').setAttribute('aria-valuenow','".$input_get->getString($label_id[$key]."_element".$id."0")."');		
						
						if(document.getElementById('".$label_id[$key]."_element".$id."1'))		
						document.getElementById('".$label_id[$key]."_element".$id."1').setAttribute('aria-valuenow','".$input_get->getString($label_id[$key]."_element".$id."1")."');	
						";	
						
					break;				
				}				
				
				case "type_grading":{		
					for($k=0; $k<100; $k++)
					{	
						echo "	if(document.getElementById('".$label_id[$key]."_element".$id.$k."')){		
						document.getElementById('".$label_id[$key]."_element".$id.$k."').value='".$input_get->getString($label_id[$key]."_element".$id.$k)."';		
						}";
					}	
						echo "sum_grading_values(".$label_id[$key].",".$id.");";	
						
					break;					
				}	
				
				case "type_matrix":{	
						echo	
					"if(document.getElementById('".$label_id[$key]."_input_type".$id."').value=='radio')	
					{";	
						for($k=1; $k<40; $k++){		
							for($l=1; $l<40; $l++)
							{		
								echo	
									"if(document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."'))
									{			
										document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').removeAttribute('checked');	
										
										if(document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').value=='".$input_get->getString($label_id[$key]."_input_element".$id.$k)."')		
										document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').setAttribute('checked', 'checked');	
									
									}";	
							}		
						}	
						echo 
					"}	
					else	
					if(document.getElementById('".$label_id[$key]."_input_type".$id."').value=='checkbox')		
					{";		
						for($k=1; $k<40; $k++)
						{		
							for($l=1; $l<40; $l++)
							{		
								echo	
								"if(document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."'))
								{		
									document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').removeAttribute('checked');
									if(document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').value=='".$input_get->getString($label_id[$key]."_input_element".$id.$k."_".$l)."')		
									
									document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').setAttribute('checked', 'checked');			
										
								}";	
							}	
						}	
						echo
					"}	
					else	
					if(document.getElementById('".$label_id[$key]."_input_type".$id."').value=='text')		
					{		
						";		
						for($k=1; $k<40; $k++)
						{		
							for($l=1; $l<40; $l++)
							{		
								echo	
								"if(document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."'))
								
								document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').value='".$input_get->getString($label_id[$key]."_input_element".$id.$k."_".$l)."';		
								";		
							}	
						}	
						echo "
					}	
					else	
						if(document.getElementById('".$label_id[$key]."_input_type".$id."').value=='select')	
						{		
						";		
							for($k=1; $k<40; $k++)
							{	
								for($l=1; $l<40; $l++)
								{		
									echo	
									"if(document.getElementById('".$label_id[$key]."_select_yes_no".$id.$k."_".$l."'))
								
									document.getElementById('".$label_id[$key]."_select_yes_no".$id.$k."_".$l."').value='".$input_get->getString($label_id[$key]."_select_yes_no".$id.$k."_".$l)."';			
									";	
								}	
							}	
						echo 
						"}";	
				break;			
				}				
							
							
			case "type_address":{	
								if($key>$old_key)
								{
								echo 
	"if(document.getElementById('".$label_id[$key]."_street1".$id."'))
	{
			document.getElementById('".$label_id[$key]."_street1".$id."').value='".addslashes($input_get->getString($label_id[$key]."_street1".$id))."';
			document.getElementById('".$label_id[$key]."_street2".$id."').value='".addslashes($input_get->getString($label_id[$key+1]."_street2".$id))."';
			document.getElementById('".$label_id[$key]."_city".$id."').value='".addslashes($input_get->getString($label_id[$key+2]."_city".$id))."';
			document.getElementById('".$label_id[$key]."_state".$id."').value='".addslashes($input_get->getString($label_id[$key+3]."_state".$id))."';
			document.getElementById('".$label_id[$key]."_postal".$id."').value='".addslashes($input_get->getString($label_id[$key+4]."_postal".$id))."';
			document.getElementById('".$label_id[$key]."_country".$id."').value='".addslashes($input_get->getString($label_id[$key+5]."_country".$id))."';
		
	}";
									$old_key=$key+5;
									}
									break;
		
								}
								
							
							
							
			case "type_checkbox":{
			
											
			$is_other=false;
	
			if( $input_get->getString($label_id[$key]."_allow_other".$id)=="yes")
			{
				$other_element=$input_get->getString($label_id[$key]."_other_input".$id);
				$other_element_id=$input_get->getString($label_id[$key]."_allow_other_num".$id);
				if(isset($other_element))
					$is_other=true;
			}

								echo
	"
	if(document.getElementById('".$label_id[$key]."_other_input".$id."'))
	{
	document.getElementById('".$label_id[$key]."_other_input".$id."').parentNode.removeChild(document.getElementById('".$label_id[$key]."_other_br".$id."'));
	document.getElementById('".$label_id[$key]."_other_input".$id."').parentNode.removeChild(document.getElementById('".$label_id[$key]."_other_input".$id."'));
	}

	for(k=0; k<30; k++)
		if(document.getElementById('".$label_id[$key]."_element".$id."'+k))
			document.getElementById('".$label_id[$key]."_element".$id."'+k).removeAttribute('checked');
		else break;
	";
								for($j=0; $j<100; $j++)
								{
										$element=$input_get->getString($label_id[$key]."_element".$id.$j);
										if(isset($element))
												{
												echo
	"document.getElementById('".$label_id[$key]."_element".$id.$j."').setAttribute('checked', 'checked');
	";
												}
								}
								
	if($is_other)
		echo
	"
		show_other_input('".$label_id[$key]."','".$id."');
		document.getElementById('".$label_id[$key]."_other_input".$id."').value='".$input_get->getString($label_id[$key]."_other_input".$id)."';
	";
	
								
								
								break;
								}
			case "type_radio":{
			
			$is_other=false;
			
			if( $input_get->getString($label_id[$key]."_allow_other".$id)=="yes")
			{
				$other_element=$input_get->getString($label_id[$key]."_other_input".$id);
				if(isset($other_element))
					$is_other=true;
			}
			
			
								echo
	"
	if(document.getElementById('".$label_id[$key]."_other_input".$id."'))
	{
	document.getElementById('".$label_id[$key]."_other_input".$id."').parentNode.removeChild(document.getElementById('".$label_id[$key]."_other_br".$id."'));
	document.getElementById('".$label_id[$key]."_other_input".$id."').parentNode.removeChild(document.getElementById('".$label_id[$key]."_other_input".$id."'));
	}
	
	for(k=0; k<50; k++)
		if(document.getElementById('".$label_id[$key]."_element".$id."'+k))
		{
			document.getElementById('".$label_id[$key]."_element".$id."'+k).removeAttribute('checked');
			if(document.getElementById('".$label_id[$key]."_element".$id."'+k).value=='".addslashes($input_get->getString($label_id[$key]."_element".$id))."')
			{
				document.getElementById('".$label_id[$key]."_element".$id."'+k).setAttribute('checked', 'checked');
								
			}
		}
		else break;
	";
	if($is_other)
								echo
	"
		show_other_input('".$label_id[$key]."','".$id."');
		document.getElementById('".$label_id[$key]."_other_input".$id."').value='".$input_get->getString($label_id[$key]."_other_input".$id)."';
	";
	
						break;
							}
			
			case "type_time":{
								$ss=$input_get->getString($label_id[$key]."_ss".$id);
								if(isset($ss))
								{
									echo 
	"if(document.getElementById('".$label_id[$key]."_hh".$id."'))
	{
		document.getElementById('".$label_id[$key]."_hh".$id."').value='".$input_get->getString($label_id[$key]."_hh".$id)."';
		document.getElementById('".$label_id[$key]."_mm".$id."').value='".$input_get->getString($label_id[$key]."_mm".$id)."';
		document.getElementById('".$label_id[$key]."_ss".$id."').value='".$input_get->getString($label_id[$key]."_ss".$id)."';
	}";
								}
								else
								{
									echo 
	"if(document.getElementById('".$label_id[$key]."_hh".$id."'))
	{
		document.getElementById('".$label_id[$key]."_hh".$id."').value='".$input_get->getString($label_id[$key]."_hh".$id)."';
		document.getElementById('".$label_id[$key]."_mm".$id."').value='".$input_get->getString($label_id[$key]."_mm".$id)."';
	}";
								}
								$am_pm=$input_get->getString($label_id[$key]."_am_pm".$id);
								if(isset($am_pm))
									echo 
	"if(document.getElementById('".$label_id[$key]."_am_pm".$id."'))
		document.getElementById('".$label_id[$key]."_am_pm".$id."').value='".$input_get->getString($label_id[$key]."_am_pm".$id)."';
	";
								break;
							}
							
							
			case "type_date_fields":{
				$date_fields=explode('-',$input_get->getString($label_id[$key]."_element".$id));
									echo 
	"if(document.getElementById('".$label_id[$key]."_day".$id."'))
	{
		document.getElementById('".$label_id[$key]."_day".$id."').value='".$date_fields[0]."';
		document.getElementById('".$label_id[$key]."_month".$id."').value='".$date_fields[1]."';
		document.getElementById('".$label_id[$key]."_year".$id."').value='".$date_fields[2]."';
	}";
							break;
							}
							
			case "type_date":
			case "type_own_select":					
			case "type_country":{
									echo
	"if(document.getElementById('".$label_id[$key]."_element".$id."'))
		document.getElementById('".$label_id[$key]."_element".$id."').value='".addslashes($input_get->getString($label_id[$key]."_element".$id))."';
	";
							break;
							}
							
			default:{
							break;
						}
	
			}
		
	}
}

?>

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
	
</script>
</form>
<?php if($is_recaptcha) {
		$document->addScriptDeclaration('var RecaptchaOptions = {
theme: "'.$row->recaptcha_theme.'"
};
');

?>

<div id="main_recaptcha" style="display:none;">
<?php
// Get a key from https://www.google.com/recaptcha/admin/create
if($row->public_key)
	$publickey = $row->public_key;
else
	$publickey = '0';
$error = null;
echo recaptcha_get_html($publickey, $error);
?>

</div>
    <script>
	recaptcha_html=document.getElementById('main_recaptcha').innerHTML.replace('Recaptcha.widget = Recaptcha.$("recaptcha_widget_div"); Recaptcha.challenge_callback();',"");
	document.getElementById('main_recaptcha').innerHTML="";
	if(document.getElementById('wd_recaptcha<?php echo $id ?>'))
	{
		document.getElementById('wd_recaptcha<?php echo $id ?>').innerHTML=recaptcha_html;
		
		Recaptcha.widget = Recaptcha.$("recaptcha_widget_div");
		
		Recaptcha.challenge_callback();
	}
    </script>



<?php }
}
?>
	<?php
		$content=ob_get_contents();
		ob_end_clean();
		return $content;


}
}?>
