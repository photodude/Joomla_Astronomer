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
class Download {
	static $name = 'download';
	static $title = 'Download';
	
	public static function config(){
		?>
		<ul class="nav nav-tabs">
			<li><a href="#download-config" data-g-toggle="tab"><?php echo l_('CCON_CONFIG'); ?></a></li>
			<li><a href="#download-help" data-g-toggle="tab"><?php echo l_('CCON_HELP'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="download-config" class="tab-pane">
			<?php
				echo \GCore\Helpers\Html::formSecStart();
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][download][enabled]', array('type' => 'dropdown', 'label' => l_('CONN_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 0));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][download][priority]', array('type' => 'text', 'label' => l_('CONN_PRIORITY'), 'value' => 0, 'sublabel' => l_('CONN_PRIORITY_DESC')));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][download][download_path]', array('type' => 'text', 'label' => l_('CONN_DOWNLOAD_PATH'), 'class' => 'XL', 'sublabel' => l_('CONN_DOWNLOAD_PATH_DESC')));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][download][path_field]', array('type' => 'text', 'label' => l_('CONN_PATH_FIELD'), 'sublabel' => l_('CONN_PATH_FIELD_DESC')));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][download][display_icon]', array('type' => 'dropdown', 'label' => l_('CONN_DISPLAY_ICON'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 1));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][download][download_action]', array('type' => 'text', 'label' => l_('CONN_DOWNLOAD_ACTION'), 'class' => 'A', 'sublabel' => l_('CONN_DOWNLOAD_ACTION_DESC')));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][download][download_link_class]', array('type' => 'text', 'label' => l_('CONN_DOWNLOAD_LINK_CLASS'), 'class' => 'L', 'value' => 'btn btn-success btn-xs'));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][download][filetime_format]', array('type' => 'text', 'label' => l_('CONN_FILETIME_FORMAT'), 'class' => 'L', 'value' => 'd-m-Y H:i'));
				echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="download-help" class="tab-pane">
				<p></p>
				<p>Use "_DOWNLOAD_.link" as your field name to render the download link.</p>
				<p>Use "_DOWNLOAD_.filename" as your field name to output the file's name.</p>
				<p>Use "_DOWNLOAD_.filesize" as your field name to output the file's size.</p>
				<p>Use "_DOWNLOAD_.filetime" as your field name to output the file's last modified time.</p>
			</div>
		</div>
		<?php
	}
	
	public static function on_finalize($controller){
		$config = new \GCore\Libs\Parameter($controller->connection['Connection']['extras']['plugins']['download']);
		$path_field = $config->get('path_field');
		$download_action = $config->get('download_action');
		if(!empty($path_field) AND !empty($download_action)){
			if($controller->action == $download_action){
				if(!empty($controller->data['gcb'])){
					$path_pcs = explode('.', $path_field);
					$field = array_pop($path_pcs);
					$model = array_pop($path_pcs);
					$row = $controller->connection_models[$model]->load($controller->data['gcb']);
					if(!empty($row)){
						$file_path = \GCore\Libs\Arr::getVal($row, explode('.', $path_field));
						if($config->get('download_path')){
							$file_path = rtrim($config->get('download_path'), DS).DS.$file_path;
						}
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
							\GCore\Libs\Download::send($file, 'D');
						}else{
							//this is a file
							$file = $file_path;
							\GCore\Libs\Download::send($file, 'D');
						}
					}
				}
			}
		}
	}
}