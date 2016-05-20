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
class Hits {
	static $name = 'hits';
	static $title = 'Hits';
	
	public static function config(){
		?>
		<ul class="nav nav-tabs">
			<li><a href="#hits-config" data-g-toggle="tab"><?php echo l_('CCON_CONFIG'); ?></a></li>
			<li><a href="#hits-help" data-g-toggle="tab"><?php echo l_('CCON_HELP'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="hits-config" class="tab-pane">
			<?php
				echo \GCore\Helpers\Html::formSecStart();
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][hits][enabled]', array('type' => 'dropdown', 'label' => l_('CONN_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 0));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][hits][priority]', array('type' => 'text', 'label' => l_('CONN_PRIORITY'), 'value' => 0, 'sublabel' => l_('CONN_PRIORITY_DESC')));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][hits][hits_field]', array('type' => 'text', 'label' => l_('CONN_HITS_FIELD'), 'sublabel' => l_('CONN_HITS_FIELD_DESC')));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][hits][display_icon]', array('type' => 'dropdown', 'label' => l_('CONN_DISPLAY_ICON'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 1));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][hits][hot_text]', array('type' => 'text', 'label' => l_('CONN_HOT_TEXT'), 'value' => 'Hot', 'sublabel' => l_('CONN_HOT_TEXT_DESC')));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][hits][hot_limit]', array('type' => 'text', 'label' => l_('CONN_HOT_LIMIT'), 'value' => '1000', 'sublabel' => l_('CONN_HOT_LIMIT_DESC')));
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][hits][update_actions]', array('type' => 'text', 'label' => l_('CONN_UPDATE_ACTIONS'), 'class' => 'L', 'sublabel' => l_('CONN_UPDATE_ACTIONS_DESC')));
				echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="hits-help" class="tab-pane">
				<p></p>
				<p>Use "_HITS_.count" as your field name to render the download count (with icon) or use "_HITS_.hot" to render a hot icon.</p>
				<p>You may also supply actions names in order to update the download field, the "gcb" value should be present in the action's page data in order for the record(s) to be updated.</p>
			</div>
		</div>
		<?php
	}
	
	public static function on_finalize($controller){
		$config = new \GCore\Libs\Parameter($controller->connection['Connection']['extras']['plugins']['hits']);
		if($config->get('hits_field')){
			$hits = $config->get('hits_field');
			$update_actions = $config->get('update_actions', '');
			if(!empty($update_actions)){
				$update_actions = explode(',', $update_actions);
				if(in_array($controller->action, $update_actions)){
					if(!empty($controller->data['gcb'])){
						$hits = explode('.', $hits);
						$field = array_pop($hits);
						$model = array_pop($hits);
						$controller->connection_models[$model]->id = $controller->data['gcb'];
						$controller->connection_models[$model]->updateField($field);
					}
				}
			}
		}
	}
}