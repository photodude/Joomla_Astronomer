<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoconnectivity\Plugins\Hits;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class HitsHelper {
	static $name = 'hits';
	static $title = 'Hits';
	var $view = null;
	
	public function on_list($fld, $ttl, &$data_columns){
		$config = new \GCore\Libs\Parameter($this->connection['Connection']['extras']['plugins']['hits']);
		if($config->get('hits_field')){
			$hits = $config->get('hits_field');
			if($fld == '_HITS_.count'){
				$data_columns[$fld]['html'] = '{'.$hits.'}';
				if($config->get('display_icon', 1)){
					$data_columns[$fld]['html'] = '<i class="fa fa-eye fa-fw"></i> '.$data_columns[$fld]['html'];
				}
			}
			if($fld == '_HITS_.hot'){
				$data_columns[$fld]['function'] = create_function('$value,$row', '
					if(\GCore\Libs\Arr::getVal($row, explode(".", "'.$hits.'"), 0) >= (int)'.$config->get('hot_limit', '1000').'){
						return 1;
					}else{
						return 0;
					}
				');
				$data_columns[$fld]['html'] = array(0 => '', 1 => '<span class="label label-danger">'.$config->get('hot_text', 'Hot').'</span>');
			}
		}
	}
	
}