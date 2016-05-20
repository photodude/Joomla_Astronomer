<?php
/**
* COMPONENT FILE HEADER
**/
namespace GCore\Admin\Extensions\Chronoconnectivity\Controllers;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Lists extends \GCore\Libs\GController {
	var $models = array('\GCore\Admin\Extensions\Chronoconnectivity\Models\Connection', '\GCore\Admin\Models\Group');
	var $libs = array('\GCore\Libs\Request');
	var $helpers= array(
		'\GCore\Helpers\DataTable', 
		'\GCore\Helpers\Assets', 
		'\GCore\Helpers\Html', 
		'\GCore\Helpers\Toolbar', 
		'\GCore\Helpers\Tasks', 
		'\GCore\Helpers\Paginator', 
		'\GCore\Admin\Extensions\Chronoconnectivity\Helpers\Lister', 
		'\GCore\Admin\Extensions\Chronoconnectivity\Helpers\Plugin', 
		'\GCore\Helpers\Sorter'
	);
	var $area = 'admin';
	var $plugins = array();
	var $connection_models = array();
	
	function _initialize(){
		//$this->area = 'front';
		$session = \GCore\Libs\Base::getSession();
		if($this->get('ccname', null)){
			$this->data['ccname'] = $this->get('ccname');
		}
		if(!empty($this->data['ccname'])){
			$this->connection = $connection = $this->Connection->find('first', array('conditions' => array('title' => $this->data['ccname'], 'published' => 1)));
			if(!empty($connection['Connection']['extras']['models']['name'][1]) AND !empty($connection['Connection']['extras']['models']['tablename'][1])){
				$dbo_config = array();
				if(!empty($connection['Connection']['extras']['ndb']['enabled'])){
					$dbo_config = array(
						'type' => $connection['Connection']['extras']['ndb']['driver'], 
						'host' => $connection['Connection']['extras']['ndb']['host'], 
						'name' => $connection['Connection']['extras']['ndb']['database'], 
						'user' => $connection['Connection']['extras']['ndb']['user'], 
						'pass' => $connection['Connection']['extras']['ndb']['password'], 
						'prefix' => $connection['Connection']['extras']['ndb']['prefix'], 
					);
				}
				//primary model available
				\GCore\Libs\GModel::generateModel(trim($connection['Connection']['extras']['models']['name'][1]), array(
					'tablename' => trim($connection['Connection']['extras']['models']['tablename'][1]),
					'dbo_config' => $dbo_config,
				));
				$pmodel = '\GCore\Models\\'.$connection['Connection']['extras']['models']['name'][1];
				$this->pmodel = $pmodel::getInstance();
				$this->connection_models[$this->pmodel->alias] = $this->pmodel;
				
				//find secondary models if available
				foreach($this->connection['Connection']['extras']['models']['name'] as $k => $name){
					if($k != 1){
						$name = trim($this->connection['Connection']['extras']['models']['name'][$k]);
						$tablename = trim($this->connection['Connection']['extras']['models']['tablename'][$k]);
						$pkey = !empty($this->connection['Connection']['extras']['models']['pkey'][$k]) ? trim($this->connection['Connection']['extras']['models']['pkey'][$k]) : NULL;
						if(!empty($name) AND !empty($tablename)){
							\GCore\Libs\GModel::generateModel($name, array(
								'tablename' => $tablename,
								'pkey' => $pkey,
								'dbo_config' => $dbo_config,
							));
							//setup relation to primary if exists
							$relation = $this->connection['Connection']['extras']['models']['relation'][$k];
							if(!empty($relation)){
								$relation_settings = array('className' => '\GCore\Models\\'.$name);
								if(!empty($this->connection['Connection']['extras']['models']['foreignKey'][$k])){
									$relation_settings['foreignKey'] = trim($this->connection['Connection']['extras']['models']['foreignKey'][$k]);
								}
								if(!empty($this->connection['Connection']['extras']['models']['assoc_save'][$k])){
									$relation_settings['save_on_save'] = true;
								}
								if(!empty($this->connection['Connection']['extras']['models']['assoc_delete'][$k])){
									$relation_settings['delete_on_delete'] = true;
								}
								if(!empty($this->connection['Connection']['extras']['models']['conditions'][$k])){
									$conditions = eval('?>'.$this->connection['Connection']['extras']['models']['conditions'][$k]);
									$relation_settings['conditions'] = is_array($conditions) ? $conditions : array();
								}
								if(!empty($this->connection['Connection']['extras']['models']['join_conditions'][$k])){
									$join_conditions = eval('?>'.$this->connection['Connection']['extras']['models']['join_conditions'][$k]);
									$relation_settings['join_conditions'] = is_array($join_conditions) ? $join_conditions : array();
								}
								if(!empty($this->connection['Connection']['extras']['models']['join_type'][$k])){
									$relation_settings['type'] = $this->connection['Connection']['extras']['models']['join_type'][$k];
								}
								if(!empty($this->connection['Connection']['extras']['models']['fields'][$k])){
									$relation_settings['fields'] = $this->_process_fields_list($this->connection['Connection']['extras']['models']['fields'][$k]);
								}
								if(!empty($this->connection['Connection']['extras']['models']['order'][$k])){
									$relation_settings['order'] = $this->_process_fields_list($this->connection['Connection']['extras']['models']['order'][$k]);
								}
								if(!empty($this->connection['Connection']['extras']['models']['group'][$k])){
									$relation_settings['group'] = $this->_process_fields_list($this->connection['Connection']['extras']['models']['group'][$k]);
								}
								
								if(empty($this->connection['Connection']['extras']['models']['associated_model'][$k])){
									$this->pmodel->bindModels($relation, array(
										$name => $relation_settings
									));
									$this->connection_models[$name] = $this->pmodel->$name;
								}else{
									$associated_model = $this->connection['Connection']['extras']['models']['associated_model'][$k];
									$associated_model_class = '\GCore\Models\\'.$this->connection['Connection']['extras']['models']['associated_model'][$k];
									if($associated_model == $this->pmodel->alias){
										$this->pmodel->bindModels($relation, array(
											$name => $relation_settings
										));
										$this->connection_models[$name] = $this->pmodel->$name;
									}else{
										$check_model = $this->pmodel;
										check_again:
										if(in_array($associated_model, array_keys($check_model->associated_models))){
											$check_model->$associated_model->bindModels($relation, array(
												$name => $relation_settings
											));
											$this->connection_models[$name] = $check_model->$associated_model->$name;
										}else{
											$sub_models = array_keys($check_model->associated_models);
											foreach($sub_models as $sub_model){
												$check_model = $sub_model;
												goto check_again;
											}
										}
									}
								}
							}
						}
					}
				}
				
			}else{
				$session->setFlash('error', l_('CONN_CONNECTION_NOT_FOUND'));
				return false;
			}
		}else{
			$session->setFlash('error', l_('CONN_NO_CONNECTION_NAME'));
			return false;
		}
		//check permissions
		if(!empty($this->connection['Connection']['extras'][$this->area]['permissions'][$this->action])){
			$gcb = $this->Request->data('gcb', null);
			$owner_id = null;
			if($this->connection['Connection']['extras'][$this->area]['permissions'][$this->action]['owner'] == 1){
				if(!empty($gcb) AND !empty($this->connection['Connection']['extras'][$this->area]['permissions_conf']['owner_id_column'])){
					$user_id_column = $this->connection['Connection']['extras'][$this->area]['permissions_conf']['owner_id_column'];
					$records = $this->pmodel->find('all', array('recursive' => -1, 'fields' => array($this->pmodel->pkey, $user_id_column), 'conditions' => array($this->pmodel->pkey => $gcb)));
					if(strpos($user_id_column, '.') === false){
						$user_id_column = $this->pmodel->alias.'.'.$user_id_column;
					}
					$owners = \GCore\Libs\Arr::getVal($records, explode('.', '[n].'.$user_id_column));
					$owners = array_values(array_unique($owners));
					if(count($owners) > 1){
						goto check_all;
					}else{
						$owner_id = $owners[0];
					}
				}
			}
			check_all:
			if(!\GCore\Libs\Authorize::check_rules($this->connection['Connection']['extras'][$this->area]['permissions'][$this->action], \GCore\Libs\Authenticate::get_user_groups(), $owner_id)){
				$session->setFlash('error', l_('CONNECTIVITY_ACCESS_DENIED'));
				return false;
			}
		}
		//load plugins
		if(!empty($this->pmodel)){
			if(!empty($this->connection['Connection']['extras'][$this->area]['display']['page_limit']) AND is_numeric($this->connection['Connection']['extras'][$this->area]['display']['page_limit'])){
				$this->pmodel->page_limit = (int)$this->connection['Connection']['extras'][$this->area]['display']['page_limit'];
			}
			//run plugins
			if(!empty($this->connection['Connection']['extras']['plugins'])){
				//sort plugins execution
				$plugins_priority = array();
				foreach($this->connection['Connection']['extras']['plugins'] as $plg => $plg_data){
					$plugins_priority[$plg] = \GCore\Libs\Arr::getVal($plg_data, array('priority'), 0);
				}
				array_multisort($plugins_priority, SORT_DESC, $this->connection['Connection']['extras']['plugins']);
				
				foreach($this->connection['Connection']['extras']['plugins'] as $plugin => $plugin_data){
					if(!empty($plugin_data['enabled'])){
						$this->plugins[] = $plugin;
						if(class_exists('\GCore\Admin\Extensions\Chronoconnectivity\Plugins\\'.\GCore\Libs\Str::camilize($plugin).'\\'.\GCore\Libs\Str::camilize($plugin.'_helper'))){
							$plugin_helper = '\GCore\Admin\Extensions\Chronoconnectivity\Plugins\\'.\GCore\Libs\Str::camilize($plugin).'\\'.\GCore\Libs\Str::camilize($plugin.'_helper');
							$this->helpers[$plugin_helper]['connection'] = $this->connection;
							$this->helpers[$plugin_helper]['model'] = $this->pmodel;
						}
					}
				}
			}
			$this->helpers['\GCore\Admin\Extensions\Chronoconnectivity\Helpers\Plugin']['plugins'] = $this->plugins;
			$this->_process_plugins('on_initialize', $this);
		}
	}
	
	function _process_fields_list($fields){
		ob_start();
		$list = eval('?>'.$fields);
		ob_end_clean();
		$fields_list = is_array($list) ? $list : array_map('trim', explode(',', trim($fields)));
		return array_filter($fields_list);
	}
	
	function _process_plugins(){
		$args = func_get_args();
		if(!empty($args)){
			$fn = array_shift($args);
			foreach($this->plugins as $plugin){
				$class = '\GCore\Admin\Extensions\Chronoconnectivity\Plugins\\'.\GCore\Libs\Str::camilize($plugin).'\\'.\GCore\Libs\Str::camilize($plugin);
				if(is_callable(array($class, $fn))){
					call_user_func_array(array($class, $fn), $args);
				}
			}
		}
	}
	
	function _finalize(){
		$this->_process_plugins('on_finalize', $this);
		if(!empty($this->pmodel) AND (bool)$this->connection['Connection']['extras'][$this->area]['display']['debug'] === true){
			pr($this->pmodel->dbo->log);
		}
		$tvout = \GCore\Libs\Request::data('tvout', '');
		parent::_settings('chronoconnectivity');
		$settings = isset($this->data['Chronoconnectivity']) ? $this->data['Chronoconnectivity'] : array();
		$this->fparams = new \GCore\Libs\Parameter($settings);
		if($this->_validated($this->fparams) === false AND $tvout != 'ajax'){
			echo '<p class="chrono_credits"><a href="http://www.chronoengine.com" target="_blank">Powered by ChronoConnectivity - ChronoEngine.com</a></p>';
		}
	}
	
	function _validated($params){
		if((bool)$params->get('validated', 0) === true){
			return true;
		}
		return false;
	}
	
	function index(){
		if(!empty($this->pmodel)){
			
			
			
			$session = \GCore\Libs\Base::getSession();
			
			$sessioned_string = trim($this->connection['Connection']['extras'][$this->area]['columns']['sessioned']);
			if(!empty($sessioned_string)){
				$columns = explode("\n", $sessioned_string);
				$fields = array_map('trim', $columns);
				foreach($fields as $k => $field){
					$value = \GCore\Libs\Request::data($field, null);
					if(!is_null($value)){
						$session->set($field, $value);
					}else{
						\GCore\Libs\Request::set($field, $session->get($field));
						$this->data[$field] = $session->get($field);
					}
				}
			}
			
			
			$find_params = array();
			$conditions = eval('?>'.$this->connection['Connection']['extras']['models']['conditions'][1]);
			$this->pmodel->conditions = is_array($conditions) ? $conditions : array();
			
			if(!empty($this->connection['Connection']['extras']['models']['fields'][1])){
				$find_params['fields'] = $this->_process_fields_list($this->connection['Connection']['extras']['models']['fields'][1]);
			}
			if(!empty($this->connection['Connection']['extras']['models']['order'][1])){
				$this->pmodel->order_by = $this->_process_fields_list($this->connection['Connection']['extras']['models']['order'][1]);
			}
			if(!empty($this->connection['Connection']['extras']['models']['group'][1])){
				//$find_params['group'] = array_map('trim', explode(',', $this->connection['Connection']['extras']['models']['group'][1]));
				$this->pmodel->group = $this->_process_fields_list($this->connection['Connection']['extras']['models']['group'][1]);
			}
			
			//sorting
			$this->sort_model = $this->pmodel;
			$this->_sortable();
			//filtering
			$this->filter_model = $this->pmodel;
			$_f = function($e){
				$cs = explode(':', $e, 2);
				return trim($cs[0]);
			};
			//filters
			$filters_fields = array();
			$filters_string = trim($this->connection['Connection']['extras'][$this->area]['columns']['filters']);
			if(!empty($filters_string)){
				$columns = explode("\n", $filters_string);
				$filters_fields = array_map($_f, $columns);
			}
			$this->_filter($filters_fields);
			//search
			$this->search_model = $this->pmodel;
			$searchable_fields = array();
			$searchable_string = trim($this->connection['Connection']['extras'][$this->area]['columns']['searchable']);
			if(!empty($searchable_string)){
				$columns = explode("\n", $searchable_string);
				$searchable_fields = array_map($_f, $columns);
			}
			$this->_search($searchable_fields);
			
			//paginating
			$this->paginate_model = $this->pmodel;
			$this->_paginate();
			//find records list
			$rows = $this->pmodel->find('all', $find_params);
			//pr($rows);
			$this->set('rows', $rows);
			$this->set('connection', $this->connection);
			$this->set('area', $this->area);
			//add the correct display helper
			$helper = $this->connection['Connection']['extras'][$this->area]['display']['block'];
			$this->helpers[] = '\GCore\Admin\Extensions\Chronoconnectivity\Blocks\\'.\GCore\Libs\Str::camilize($helper);
			$this->set('helper', \GCore\Libs\Str::camilize($helper));
			
			$this->view = \GCore\C::ext_path('chronoconnectivity', 'admin').'views'.DS.'lists'.DS.'index.php';
		}
	}
	
	function toggle(){
		if(!empty($this->data['model'])){
			$model = $this->data['model'];
			$model = '\GCore\Models\\'.$model;
			$this->update_model = $model::getInstance();
		}else{
			$this->update_model = $this->pmodel;
		}
		parent::_toggle();
		$this->redirect(r_('index.php?ext=chronoconnectivity&cont=lists&act=index&ccname='.$this->connection['Connection']['title']));
	}
	
	function view(){
		$this->edit();
		$this->view = \GCore\C::ext_path('chronoconnectivity', 'admin').'views'.DS.'lists'.DS.'custom_act.php';
		$this->set('act_name', 'view');
	}
	
	//data reading
	function edit(){
		$id = $this->Request->data('gcb', null);
		$this->pmodel->id = $id;
		$record = $this->pmodel->find('first', array('conditions' => array($this->pmodel->alias.'.'.$this->pmodel->pkey => $id)));
		if(!empty($record)){
			$this->data = array_merge($record, $this->data);
		}
		$this->set(array('row' => $record));
		$this->set('connection', $this->connection);
		$this->set('area', $this->area);
		
		$this->view = \GCore\C::ext_path('chronoconnectivity', 'admin').'views'.DS.'lists'.DS.'edit.php';
	}
	
	function save(){
		$this->save_model = $this->pmodel;
		$result = parent::_save();
		if($result){
			if($this->Request->get('save_act') == 'apply'){
				$this->redirect(r_('index.php?ext=chronoconnectivity&cont=lists&act=edit&ccname='.$this->connection['Connection']['title'].'&gcb='.$this->pmodel->id));
			}else{
				$this->redirect(r_('index.php?ext=chronoconnectivity&cont=lists&act=index&ccname='.$this->connection['Connection']['title']));
			}
		}else{
			$this->edit();
			$this->view = 'edit';
			$session = \GCore\Libs\Base::getSession();
			$session->setFlash('error', \GCore\Libs\Arr::flatten($this->pmodel->errors));
		}
	}
	
	function save_list(){
		$this->save_list_model = $this->pmodel;
		parent::_save_list();
		$this->redirect(r_('index.php?ext=chronoconnectivity&cont=lists&act=index&ccname='.$this->connection['Connection']['title']));
	}
	
	function delete(){
		$this->delete_model = $this->pmodel;
		parent::_delete();
		$this->redirect(r_('index.php?ext=chronoconnectivity&cont=lists&act=index&ccname='.$this->connection['Connection']['title']));
	}
	
	function __call($name, $arguments = array()){
		if(isset($this->connection['Connection']['extras'][$this->area]['actions'][$name]['code'])){
			//$this->view = 'custom_act';
			$this->view = \GCore\C::ext_path('chronoconnectivity', 'admin').'views'.DS.'lists'.DS.'custom_act.php';
			$this->set('act_name', $name);
		}
		$this->set('connection', $this->connection);
		$this->set('area', $this->area);
	}
}
?>