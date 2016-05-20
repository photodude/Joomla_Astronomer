<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoconnectivity\Plugins\Download;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class DownloadHelper {
	static $name = 'download';
	static $title = 'Download';
	var $view = null;
	
	public function on_list($fld, $ttl, &$data_columns){
		$config = new \GCore\Libs\Parameter($this->connection['Connection']['extras']['plugins']['download']);
		if($config->get('path_field') AND $config->get('download_action')){
			$path = $config->get('path_field');
			$action = $config->get('download_action');
			if($fld == '_DOWNLOAD_.link'){
				$link = r_('index.php?ext=chronoconnectivity&cont=lists&act='.$action.'&ccname='.$this->connection['Connection']['title'].'&gcb={'.$this->model->alias.'.'.$this->model->pkey.'}');
				$text = $ttl;
				if($config->get('display_icon', 1)){
					$text = '<i class="fa fa-download fa-fw"></i> '.$text;
				}
				$data_columns[$fld]['html'] = '<a class="'.$config->get('download_link_class', 'btn btn-success btn-xs').'" href="'.$link.'">'.$text.'</a>';
			}
			
			$file_info = array();
			$file_info['filesize'] = '';
			$file_info['filetime'] = '';
			$file_info['filename'] = '';
			
			$get_file_info = function($cell, $row, $column) use ($path, $file_info, $config){
				$file_path = \GCore\Libs\Arr::getVal($row, explode('.', $path));
				if($config->get('download_path')){
					$file_path = rtrim($config->get('download_path'), DS).DS.$file_path;
				}
				//$file_info = array();
				if(file_exists($file_path)){
					if(is_dir($file_path)){
						//this is a folder
						$files = @\GCore\Libs\Folder::getFiles($file_path, false);
						foreach($files as $file){
							$found[$file] = filemtime($file);
						}
						if(empty($found)){
							return false;
						}
						arsort($found);
						$files = array_keys($found);
						sort($files);
						$file = array_pop($files);
						
						$file_info['filesize'] = \GCore\Libs\File::humanSize(filesize($file));
						$file_info['filetime'] = date($config->get('filetime_format', 'd-m-Y H:i'), filemtime($file));
						$file_info['filename'] = basename($file);
					}else{
						//this is a file
						$file = $file_path;
						$file_info['filesize'] = \GCore\Libs\File::humanSize(filesize($file));
						$file_info['filetime'] = date($config->get('filetime_format', 'd-m-Y H:i'), filemtime($file));
						$file_info['filename'] = basename($file);
					}
				}
				foreach($file_info as $k => $info){
					if($column == '_DOWNLOAD_.'.$k){
						return $file_info[$k];
					}
				}
				return '';//$file_info;
			};
			
			foreach($file_info as $k => $info){
				if($fld == '_DOWNLOAD_.'.$k){
					$data_columns[$fld]['function'] = $get_file_info;
				}
			}
			if($fld == '_HkkITS_.hot'){
				$data_columns[$fld]['function'] = create_function('$value,$row', '
					if(\GCore\Libs\Arr::getVal($row, explode(".", "'.$download.'"), 0) >= (int)'.$config->get('hot_limit', '1000').'){
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