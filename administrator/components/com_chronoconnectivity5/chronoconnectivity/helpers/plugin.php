<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoconnectivity\Helpers;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Plugin {
	var $view = null;
	var $connection = array();
	var $model = array();
	var $plugins = array();
	
	function __construct(){
		
	}
	
	function initialize(){
		
	}
	/*
	public function setup($connection, $model){
		$this->connection = $connection;
		$this->model = $model;
		if(!empty($connection['Connection']['extras']['plugins'])){
			foreach($connection['Connection']['extras']['plugins'] as $plugin => $plugin_data){
				if(!empty($plugin_data['enabled'])){
					$this->plugins[] = $plugin;
					$helper = \GCore\Libs\Str::camilize($plugin.'_helper');
					$this->view->$helper->setup($connection, $model);
				}
			}
		}
	}
	*/
	public function on_list($fld, $ttl, &$row){
		foreach($this->plugins as $plugin){
			$helper = \GCore\Libs\Str::camilize($plugin.'_helper');
			$this->view->$helper->on_list($fld, $ttl, $row);
		}
	}
}