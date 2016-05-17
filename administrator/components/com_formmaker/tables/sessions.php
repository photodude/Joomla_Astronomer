<?php

 /**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 
defined('_JEXEC') or die('Restricted access');
class Tablesessions extends JTable
{
var $id = null;
var $form_id = null;
var $group_id = null;
var $ip = null;
var $ord_date = null;
var $ord_last_modified = null;
var $status = null;
var $full_name = null;
var $fax = null;
var $mobile_phone = null;
var $email = null;
var $phone = null;
var $address = null;
var $paypal_info = null;
var $without_paypal_info = null;
var $ipn=null;
var $checkout_method=null;
var $tax = null;
var $shipping = null;
var $shipping_type = null;
var $read = null;
var $total = null;
var $currency = null;

	function __construct(&$db)
	{
	parent::__construct('#__formmaker_sessions','id',$db);
	}
}
?>