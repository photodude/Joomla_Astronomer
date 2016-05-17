<?php 
  
 /**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined('_JEXEC') or die('Restricted access');

class Tableformmaker extends JTable

{

var $id = null;
var $mail = null;
var $title = null;
var $form = null;
var $form_front = null;
var $pagination = null;
var $show_title = null;
var $show_numbers = null;
var $counter = null;
var $submit_text = null;
var $url = null;
var $submit_text_type = null;
var $javascript = null;
var $theme = null;
var $script_mail = null;
var $script_mail_user = null;
var $article_id = null;
var $label_order = null;
var $label_order_current = null;
var $published = null;
var $public_key = null;
var $private_key = null;
var $recaptcha_theme = null;
var $paypal_mode = null;
var $checkout_mode = null;
var $paypal_email = null;
var $payment_currency	    = null;
var $tax 					= null;
var $form_fields 			= null;
var $savedb 				= null;
var $sendemail 				= null;
var $requiredmark			= null;
var $reply_to	 			= null;
var $send_to				= null;
var $autogen_layout			= null;
var $custom_front			= null;
var $mail_from				= null;
var $mail_from_name			= null;
var $mail_from_user			= null;
var $mail_from_name_user	= null;
var $reply_to_user			= null;
var $condition				= null;
var $mail_cc				= null;
var $mail_cc_user			= null;
var $mail_bcc				= null;
var $mail_bcc_user			= null;
var $mail_subject			= null;
var $mail_subject_user		= null;
var $mail_mode_user			= null;
var $mail_attachment		= null;
var $mail_attachment_user	= null;
var $user_id			    = null;
var $sortable			    = null;
	
	function __construct(&$db)
	{

		parent::__construct('#__formmaker','id',$db);

	}

}

?>