<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoconnectivity\Blocks;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Custom {
	static $name = 'custom';
	static $title = 'Custom';
	var $view = null;
	
	public static function config($area = 'front'){
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Connection[extras]['.$area.'][blocks][custom][header]', array('type' => 'textarea', 'label' => l_('HEADER_CODE'), 'style' => 'width:auto;', 'rows' => 15, 'cols' => 100, 'sublabel' => 'HTML/PHP code for the header of the list, this code is rendered only once.'));
		echo \GCore\Helpers\Html::formLine('Connection[extras]['.$area.'][blocks][custom][body]', array('type' => 'textarea', 'label' => l_('BODY_CODE'), 'style' => 'width:auto;', 'rows' => 24, 'cols' => 100, 'sublabel' => 'HTML/PHP code for each record in the list, this code is rendered once for every record in 1 page.'));
		echo \GCore\Helpers\Html::formLine('Connection[extras]['.$area.'][blocks][custom][footer]', array('type' => 'textarea', 'label' => l_('FOOTER_CODE'), 'style' => 'width:auto;', 'rows' => 15, 'cols' => 100, 'sublabel' => 'HTML/PHP code for the footer of the list, this code is rendered only once.'));
		echo \GCore\Helpers\Html::formSecEnd();
	}
	
	public function display($connection, $area = 'front', $rows = array()){
		$doc = \GCore\Libs\Document::getInstance();
		//display header
		$custom_headers = array();
		if(!empty(\GCore\Helpers\DataPresenter::$headers)){
			foreach(\GCore\Helpers\DataPresenter::$headers as $k => $header){
				$custom_headers[$k] = \GCore\Helpers\Html::container($header['tag'], $header['text'], $header['atts']);
			}
		}
		ob_start();
		eval('?>'.$connection['Connection']['extras'][$area]['blocks']['custom']['header']);
		$header = ob_get_clean();
		$header = $this->view->Lister->prepare($connection, $header);
		$header = \GCore\Libs\Str::replacer($header, $custom_headers, array('exploder' => '*'));
		echo $this->view->Lister->translate($connection, \GCore\Libs\Str::replacer($header, $rows));
		
		//display rows
		foreach($rows as $c_r_k => $row){
			ob_start();
			eval('?>'.$connection['Connection']['extras'][$area]['blocks']['custom']['body']);
			$body = ob_get_clean();
			if(isset(\GCore\Helpers\DataPresenter::$cells_data[$c_r_k])){
				$body = \GCore\Libs\Str::replacer($body, \GCore\Helpers\DataPresenter::$cells_data[$c_r_k], array('exploder' => '*'));
			}
			echo $this->view->Lister->translate($connection, \GCore\Libs\Str::replacer($body, $row));
		}
		
		//display footer
		ob_start();
		eval('?>'.$connection['Connection']['extras'][$area]['blocks']['custom']['footer']);
		$footer = ob_get_clean();
		$footer = $this->view->Lister->prepare($connection, $footer);
		$footer = \GCore\Libs\Str::replacer($footer, $custom_headers, array('exploder' => '*'));
		echo $this->view->Lister->translate($connection, \GCore\Libs\Str::replacer($footer, $rows));
	}
}