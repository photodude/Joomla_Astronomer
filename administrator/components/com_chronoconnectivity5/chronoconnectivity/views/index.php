<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<div class="chrono-page-container">
<div class="container" style="width:100%;">
<?php

	//$this->Toolbar->setTitle(l_('CONNECTIONS_MANAGER'));
	$this->Toolbar->addButton('add', r_('index.php?ext=chronoconnectivity&act=edit'), l_('_NEW_'), $this->Assets->image('add', 'toolbar/'));
	$this->Toolbar->addButton('remove', r_('index.php?ext=chronoconnectivity&act=delete'), l_('DELETE'), $this->Assets->image('remove', 'toolbar/'), 'submit_selectors');
	$this->Toolbar->addButton('copy', r_('index.php?ext=chronoconnectivity&act=copy'), l_('CONN_COPY'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoconnectivity/assets/images/copy.png', 'submit_selectors');
	$this->Toolbar->addButton('create_table', r_('index.php?ext=chronoconnectivity&act=create_table'), l_('CONN_CREATE_TABLE'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoconnectivity/assets/images/database_table.png', 'link');
	$this->Toolbar->addButton('delete_cache', r_('index.php?ext=chronoconnectivity&act=delete_cache'), l_('CONN_DELETE_CACHE'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoconnectivity/assets/images/delete_cache.png', 'link');
	$this->Toolbar->addButton('backup', r_('index.php?ext=chronoconnectivity&act=backup'), l_('CONN_BACKUP'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoconnectivity/assets/images/backup.png', 'submit_selectors');
	$this->Toolbar->addButton('restore', r_('index.php?ext=chronoconnectivity&act=restore'), l_('CONN_RESTORE'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoconnectivity/assets/images/restore.png');

?>
<div class="row">
	<form action="<?php echo r_('index.php?ext=chronoconnectivity'); ?>" method="post" name="admin_form" id="admin_form">
		<?php
			echo $this->DataTable->headerPanel($this->DataTable->_l('<h4>'.l_('CONNECTIONS_MANAGER').'</h4>').$this->DataTable->_r($this->Toolbar->renderBar()));
			$this->DataTable->create();
			$this->DataTable->header(
				array(
					'CHECK' => $this->Toolbar->selectAll(),
					'Connection.title' => $this->Sorter->link(l_('TITLE'), 'Connection.title'),
					'Connection.aview' => l_('ADMIN_VIEW'),
					'Connection.fview' => l_('FRONT_VIEW'),
					'Connection.published' => l_('PUBLISHED'),
					'Connection.id' => $this->Sorter->link('ID', 'Connection.id')
				)
			);
			$this->DataTable->cells($connections, array(
				'CHECK' => array(
					'style' => array('width' => '5%'),
					'html' => $this->Toolbar->selector('{Connection.id}')
				),
				'Connection.title' => array(
					'link' => r_('index.php?ext=chronoconnectivity&act=edit&id={Connection.id}'),
					'style' => array('text-align' => 'left')
				),
				'Connection.aview' => array(
					'html' => '<a href="'.r_('index.php?ext=chronoconnectivity&cont=lists&act=index&ccname={Connection.title}').'" target="_blank">View Connection</a>',
					'style' => array('width' => '15%'),
				),
				'Connection.fview' => array(
					'html' => '<a href="'.\GCore\C::get('GCORE_ROOT_URL').r_('index.php?ext=chronoconnectivity&cont=lists&act=index&ccname={Connection.title}').'" target="_blank">View Connection</a>',
					'style' => array('width' => '15%'),
				),
				'Connection.published' => array(
					'link' => array(r_('index.php?ext=chronoconnectivity&act=toggle&gcb={Connection.id}&val=1&fld=published'), r_('index.php?ext=chronoconnectivity&act=toggle&gcb={Connection.id}&val=0&fld=published')),
					'image' => array($this->Assets->image('disabled.png'), $this->Assets->image('enabled.png')),
					'style' => array('width' => '10%'),
				),
				'Connection.id' => array(
					'style' => array('width' => '5%'),
				)
			));
			echo $this->DataTable->build();
			echo $this->DataTable->footerPanel($this->DataTable->_l($this->Paginator->getInfo()).$this->DataTable->_r($this->Paginator->getNav()));
			echo $this->DataTable->footerPanel($this->DataTable->_r($this->Paginator->getList()));
		?>
	</form>
</div>
</div>
</div>