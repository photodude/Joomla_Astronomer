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
class Lister {
	var $view = null;

	function __construct(){

	}

	public function prepare($connection, $string = ''){
		if(strpos($string, '_TOOLBAR_NEW_') !== false){
			$button = $this->view->Toolbar->renderButton('add', r_('index.php?ext=chronoconnectivity&cont=lists&act=edit&ccname='.$connection['Connection']['title']), l_('_NEW_'), $this->view->Assets->image('add', 'toolbar/'));
			$string = str_replace('_TOOLBAR_NEW_', $button, $string);
		}
		if(strpos($string, '_TOOLBAR_DELETE_') !== false){
			$button = $this->view->Toolbar->renderButton('remove', r_('index.php?ext=chronoconnectivity&cont=lists&act=delete&ccname='.$connection['Connection']['title']), l_('DELETE'), $this->view->Assets->image('remove', 'toolbar/'), 'submit_selectors');
			$string = str_replace('_TOOLBAR_DELETE_', $button, $string);
		}
		if(strpos($string, '_TOOLBAR_SAVELIST_') !== false){
			$button = $this->view->Toolbar->renderButton('save_list', r_('index.php?ext=chronoconnectivity&cont=lists&act=save_list&ccname='.$connection['Connection']['title']), l_('UPDATE_LIST'), $this->view->Assets->image('save_list', 'toolbar/'), 'submit_selectors');
			$string = str_replace('_TOOLBAR_SAVELIST_', $button, $string);
		}
		if(strpos($string, '_TOOLBAR_SAVE_') !== false){
			$button = $this->view->Toolbar->renderButton('save', r_('index.php?ext=chronoconnectivity&cont=lists&act=save&ccname='.$connection['Connection']['title']), l_('SAVE'), $this->view->Assets->image('save', 'toolbar/'));
			$string = str_replace('_TOOLBAR_SAVE_', $button, $string);
		}
		if(strpos($string, '_TOOLBAR_APPLY_') !== false){
			$button = $this->view->Toolbar->renderButton('apply', r_('index.php?ext=chronoconnectivity&cont=lists&act=save&ccname='.$connection['Connection']['title'].'&save_act=apply'), l_('APPLY'), $this->view->Assets->image('apply', 'toolbar/'));
			$string = str_replace('_TOOLBAR_APPLY_', $button, $string);
		}
		if(strpos($string, '_TOOLBAR_CANCEL_') !== false){
			$button = $this->view->Toolbar->renderButton('cancel', r_('index.php?ext=chronoconnectivity&cont=lists&act=index&ccname='.$connection['Connection']['title']), l_('CANCEL'), $this->view->Assets->image('cancel', 'toolbar/'), 'link');
			$string = str_replace('_TOOLBAR_CANCEL_', $button, $string);
		}

		if(strpos($string, '_PAGINATOR_LIST_') !== false){
			$string = str_replace('_PAGINATOR_LIST_', $this->view->Paginator->getList(), $string);
		}
		if(strpos($string, '_PAGINATOR_INFO_') !== false){
			$string = str_replace('_PAGINATOR_INFO_', $this->view->Paginator->getInfo(), $string);
		}
		if(strpos($string, '_PAGINATOR_NAV_') !== false){
			$string = str_replace('_PAGINATOR_NAV_', $this->view->Paginator->getNav(), $string);
		}
		return $string;
	}

	public function setup($connection, $area = 'front', $rows = array()){
		$pmodel = '\GCore\Models\\'.$connection['Connection']['extras']['models']['name'][1];
		$pmodel_inst = $pmodel::getInstance();
		//$this->view->Plugin->setup($connection, $pmodel_inst);

		$columns = array();
		$_f = function($e){
			$cs = explode(':', $e, 2);
			return trim($cs[0]);
		};
		$_t = function($e){
			$cs = explode(':', $e, 2);
			if(isset($cs[1])){
				if(strpos($cs[1], 'array(') !== false AND strpos($cs[1], 'array(') == 0){
					eval('?>'.'<?php $tmp = '.$cs[1].'; ?>');
					return $tmp;
				}
				return $cs[1];
			}else{
				return $cs[0];
			}
			return isset($cs[1]) ? $cs[1] : $cs[0];
		};
		$columns_string = trim($connection['Connection']['extras'][$area]['columns']['list']);
		if(!empty($columns_string)){
			ob_start();
			eval('?>'.$columns_string);
			$columns_string = ob_get_clean();
			$columns = explode("\n", $columns_string);
			$fields = array_map($_f, $columns);
			$titles = array_map($_t, $columns);
			$headers = array_combine($fields, $titles);


			$sortables_string = trim($connection['Connection']['extras'][$area]['columns']['sortable']);
			if(!empty($sortables_string)){
				$columns = explode("\n", $sortables_string);
				$fields = array_map($_f, $columns);
				$titles = array_map($_t, $columns);
				foreach($fields as $k => $field){
					if(isset($headers[$field])){
						$headers[$field] = $this->view->Sorter->link($headers[$field], trim($titles[$k]));
					}
				}
			}

			$data_columns = array();
			//chek for selectors
			foreach($headers as $fld => $ttl){
				if($fld == '_SELECTOR_'){
					$headers[$fld] = $this->view->Toolbar->selectAll();
					$data_columns[$fld]['html'] = $this->view->Toolbar->selector('{'.$pmodel_inst->alias.'.'.$pmodel_inst->pkey.'}');
				}
				$this->view->Plugin->on_list($fld, $ttl, $data_columns);
				/*if($fld == '_EDIT_'){
					//$headers[$fld] = $this->view->Toolbar->selectAll();
					$data_columns[$fld]['a'] = r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$connection['Connection']['title'].'&act=edit');
					if($pmodel_inst->pkey){
						$data_columns[$fld]['a'] .= '&gcb={'.$pmodel_inst->alias.'.'.$pmodel_inst->pkey.'}';
					}
					$data_columns[$fld]['html'] = '<a href="'.$data_columns[$fld]['a'].'">'.$ttl.'</a>';
				}
				if($fld == '_DELETE_'){
					//$headers[$fld] = $this->view->Toolbar->selectAll();
					$data_columns[$fld]['a'] = r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$connection['Connection']['title'].'&act=delete');
					if($pmodel_inst->pkey){
						$data_columns[$fld]['a'] .= '&gcb={'.$pmodel_inst->alias.'.'.$pmodel_inst->pkey.'}';
					}
					$data_columns[$fld]['html'] = '<a href="'.$data_columns[$fld]['a'].'">'.$ttl.'</a>';
				}*/
			}

			$view_linkable_string = isset($connection['Connection']['extras'][$area]['columns']['view_linkable']) ? trim($connection['Connection']['extras'][$area]['columns']['view_linkable']) : '';
			if(!empty($view_linkable_string)){
				$columns = explode("\n", $view_linkable_string);
				$fields = array_map($_f, $columns);
				$linkable = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$data_columns[$field]['link'] = r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$connection['Connection']['title'].'&act=view');
					if($pmodel_inst->pkey){
						$data_columns[$field]['link'] .= '&gcb={'.$pmodel_inst->alias.'.'.$pmodel_inst->pkey.'}';
					}
				}
			}

			$edit_linkable_string = trim($connection['Connection']['extras'][$area]['columns']['linkable']);
			if(!empty($edit_linkable_string)){
				$columns = explode("\n", $edit_linkable_string);
				$fields = array_map($_f, $columns);
				$linkable = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$data_columns[$field]['link'] = r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$connection['Connection']['title'].'&act=edit');
					if($pmodel_inst->pkey){
						$data_columns[$field]['link'] .= '&gcb={'.$pmodel_inst->alias.'.'.$pmodel_inst->pkey.'}';
					}
				}
			}

			$binary_string = trim($connection['Connection']['extras'][$area]['columns']['binary']);
			if(!empty($binary_string)){
				$columns = explode("\n", $binary_string);
				$fields = array_map($_f, $columns);
				$binary = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$model = $pmodel_inst->alias;
					$fld = $field;
					if(strpos($field, '.') !== false){
						$pcs = explode('.', $field);
						$model = $pcs[0];
						$fld = $pcs[1];
					}
					$data_columns[$field]['link'] = array(
						r_('index.php?ext=chronoconnectivity&cont=lists&act=toggle&ccname='.$connection['Connection']['title'].'&gcb={'.$pmodel_inst->alias.'.'.$pmodel_inst->pkey.'}&val=1&fld='.$fld.'&model='.$model),
						r_('index.php?ext=chronoconnectivity&cont=lists&act=toggle&ccname='.$connection['Connection']['title'].'&gcb={'.$pmodel_inst->alias.'.'.$pmodel_inst->pkey.'}&val=0&fld='.$fld.'&model='.$model),
					);
					$data_columns[$field]['image'] = array($this->view->Assets->image('disabled.png'), $this->view->Assets->image('enabled.png'));
				}
			}

			$links_string = trim($connection['Connection']['extras'][$area]['columns']['links']);
			if(!empty($links_string)){
				$columns = explode("\n", $links_string);
				$fields = array_map($_f, $columns);
				$links = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$data_columns[$field]['link'] = $links[$k];
				}
			}
			$htmls_string = trim($connection['Connection']['extras'][$area]['columns']['htmls']);
			if(!empty($htmls_string)){
				$columns = explode("\n", $htmls_string);
				$fields = array_map($_f, $columns);
				$htmls = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$data_columns[$field]['html'] = $htmls[$k];
				}
			}
			$styles_string = trim($connection['Connection']['extras'][$area]['columns']['styles']);
			if(!empty($styles_string)){
				$columns = explode("\n", $styles_string);
				$fields = array_map($_f, $columns);
				$styles = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$data_columns[$field]['style'] = $styles[$k];
				}
			}
			$images_string = trim($connection['Connection']['extras'][$area]['columns']['images']);
			if(!empty($images_string)){
				$columns = explode("\n", $images_string);
				$fields = array_map($_f, $columns);
				$images = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$data_columns[$field]['image'] = $images[$k];
				}
			}
			$functions_string = trim($connection['Connection']['extras'][$area]['columns']['functions']);
			if(!empty($functions_string)){
				$columns = explode("\n", $functions_string);
				$fields = array_map($_f, $columns);
				$functions = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$data_columns[$field]['function'] = create_function('$cell, $row', $functions[$k]);
				}
			}
			$fields_string = trim($connection['Connection']['extras'][$area]['columns']['fields']);
			if(!empty($fields_string)){
				$columns = explode("\n", $fields_string);
				$fields = array_map($_f, $columns);
				$fields_info = array_map($_t, $columns);
				foreach($fields as $k => $field){
					$data_columns[$field]['field'] = $fields_info[$k];
				}
			}

			\GCore\Helpers\DataPresenter::create();
			\GCore\Helpers\DataPresenter::header($headers);
			\GCore\Helpers\DataPresenter::cells($rows, $data_columns);
			\GCore\Helpers\DataPresenter::set_cells_data();
		}
	}

	function translate($connection, $data = ''){
		$_f = function($e){
			$cs = explode('=', $e, 2);
			return array_map('trim', $cs);
		};
		$site_lang = \GCore\Libs\Str::camilize(str_replace('-', '_', strtolower(\GCore\Libs\Base::getConfig('site_language'))));
		if(!empty($connection['Connection']['extras']['locales'])){
			foreach($connection['Connection']['extras']['locales'] as $tag => $lang_data){
				$tag_cap = \GCore\Libs\Str::camilize($lang_data['lang_tag']);
				if($tag_cap == $site_lang){
					$lines = explode("\n", $lang_data['strings']);
					$strings = array_map($_f, $lines);
					$texts =  \GCore\Libs\Arr::getVal($strings, array('[n]', 0));
					if(!empty($lang_data['strict'])){
						$texts = array_map(function($text){return '['.$text.']';}, $texts);
					}
					$locales = \GCore\Libs\Arr::getVal($strings, array('[n]', 1));
					$data = str_replace($texts, $locales, $data);
				}
			}
		}
		return $data;
	}
}