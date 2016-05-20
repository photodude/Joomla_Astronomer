<?php
/**
* COMPONENT FILE HEADER
**/
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or define("GCORE_SITE", "front");
jimport('cegcore.joomla_gcloader');
if(!class_exists('JoomlaGCLoader')){
	JError::raiseWarning(100, "Please download the CEGCore framework from www.chronoengine.com then install it using the 'Extensions Manager'");
	return;
}

$chronoconnectivity5_setup = function(){
	$mainframe = \JFactory::getApplication();
	$ccname = GCore\Libs\Request::data('ccname', '');
	$params = $mainframe->getPageParameters('com_chronoconnectivity5');
	$connection = $params->get('ccname', '');
	$controller = GCore\Libs\Request::data('cont', 'lists');
	
	if(!empty($connection)){
		return array('ccname' => $params->get('ccname'), 'controller' => $controller);
	}else{
		return array('controller' => $controller);
	}
};

$output = new JoomlaGCLoader('front', 'chronoconnectivity5', 'chronoconnectivity', $chronoconnectivity5_setup);