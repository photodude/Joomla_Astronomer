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
class Table {
	static $name = 'table';
	static $title = 'Table';
	var $view = null;
	
	public static function config($area = 'front'){
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Connection[extras]['.$area.'][blocks][table][header]', array('type' => 'textarea', 'label' => l_('HEADER_CODE'), 'style' => 'width:auto;', 'rows' => 15, 'cols' => 100, 'sublabel' => 'HTML/PHP code for the header of the list, this code is rendered only once.'));
		echo \GCore\Helpers\Html::formLine('Connection[extras]['.$area.'][blocks][table][footer]', array('type' => 'textarea', 'label' => l_('FOOTER_CODE'), 'style' => 'width:auto;', 'rows' => 15, 'cols' => 100, 'sublabel' => 'HTML/PHP code for the footer of the list, this code is rendered only once.'));
		echo \GCore\Helpers\Html::formSecEnd();
	}
	
	public function display($connection, $area = 'front', $rows = array()){
		$doc = \GCore\Libs\Document::getInstance();
		
		$tds = array();
		$trs = array();
		$ths = array();
		
		//display header
		ob_start();
		eval('?>'.$connection['Connection']['extras'][$area]['blocks']['table']['header']);
		$header = ob_get_clean();
		$header = $this->view->Lister->translate($connection, $this->view->Lister->prepare($connection, $header));
		echo \GCore\Libs\Str::replacer($header, $rows);
		
		$thead = \GCore\Helpers\Html::container('thead', implode("\n", $ths), array());
		$tbody = \GCore\Helpers\Html::container('tbody', implode("\n", $trs), array());
		if(!empty(\GCore\Helpers\DataPresenter::$columns)){
			//\GCore\Helpers\DataPresenter::set_cells_data();
			$trs = \GCore\Helpers\DataTable::trs();
			$tbody = \GCore\Helpers\Html::container('tbody', implode("\n", $trs), array());
			foreach(\GCore\Helpers\DataPresenter::$headers as $k => $header){
				$th_tag = \GCore\Helpers\Html::container($header['tag'], $header['text'], $header['atts']);
				$ths[] = \GCore\Helpers\Html::container('th', $th_tag, array('class' => 'th-'.$k));
			}
			$thead = \GCore\Helpers\Html::container('thead', \GCore\Helpers\Html::container('tr', implode("\n", $ths)), array());
		}
		$table = \GCore\Helpers\Html::container('table', $thead.$tbody, array('class' => 'table table-hover table-censored', 'id' => 'gcore_table_list__#'));
		
		$table = \GCore\Helpers\DataLoader::load($table, \GCore\Helpers\DataPresenter::$cells_data);
		\GCore\Helpers\DataPresenter::_flush();
		
		//show table
		echo $this->view->Lister->translate($connection, $table);
		//display footer
		ob_start();
		eval('?>'.$connection['Connection']['extras'][$area]['blocks']['table']['footer']);
		$footer = ob_get_clean();
		$footer = $this->view->Lister->translate($connection, $this->view->Lister->prepare($connection, $footer));
		echo \GCore\Libs\Str::replacer($footer, $rows);
		//echo '</div>';
	}
	
}