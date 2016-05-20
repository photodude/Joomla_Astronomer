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
class Core {
	static $name = 'core';
	static $title = 'Core';
	
	public static function config(){
		?>
		<ul class="nav nav-tabs">
			<li><a href="#core-config" data-g-toggle="tab"><?php echo l_('CCON_CONFIG'); ?></a></li>
			<li><a href="#core-help" data-g-toggle="tab"><?php echo l_('CCON_HELP'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="core-config" class="tab-pane">
			<?php
				echo \GCore\Helpers\Html::formSecStart();
				echo \GCore\Helpers\Html::formLine('Connection[extras][plugins][core][enabled]', array('type' => 'dropdown', 'label' => l_('CONN_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 1));
				echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="core-help" class="tab-pane">
				<p></p>
				<p>Use "_EDIT_" as your field name to render an Edit link, the column title will be used as the link text.</p>
				<p>Use "_DELETE_" as your field name to render a Delete link, the column title will be used as the link text.</p>
			</div>
		</div>
		<?php
	}
	
	public function on_list(){
		
	}
}