<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoconnectivity\Plugins\Core;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class CoreHelper {//extends \GCore\Admin\Extensions\Chronoconnectivity\Helpers\Plugin{
	static $name = 'core';
	static $title = 'Core';
	var $view = null;
	
	public function on_list($fld, $ttl, &$data_columns){
		if($fld == '_EDIT_'){
			$data_columns[$fld]['a'] = r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$this->connection['Connection']['title'].'&act=edit');
			if($this->model->pkey){
				$data_columns[$fld]['a'] .= '&gcb={'.$this->model->alias.'.'.$this->model->pkey.'}';
			}
			$data_columns[$fld]['html'] = '<a href="'.$data_columns[$fld]['a'].'">'.$ttl.'</a>';
		}
		if($fld == '_DELETE_'){
			$data_columns[$fld]['a'] = r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$this->connection['Connection']['title'].'&act=delete');
			if($this->model->pkey){
				$data_columns[$fld]['a'] .= '&gcb={'.$this->model->alias.'.'.$this->model->pkey.'}';
			}
			$data_columns[$fld]['html'] = '<a href="'.$data_columns[$fld]['a'].'">'.$ttl.'</a>';
		}
	}
	
}