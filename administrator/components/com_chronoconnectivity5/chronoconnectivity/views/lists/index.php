<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<div class="chrono-page-container">
	<form action="<?php echo r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$connection['Connection']['title']); ?>" method="post" name="admin_form" id="admin_form">
	<?php
		if(!empty($connection['Connection']['extras'][$area]['display']['pre_process'])){
			eval('?>'.$connection['Connection']['extras'][$area]['display']['pre_process']);
		}
		$this->Lister->setup($connection, $area, $rows);
		$this->$helper->display($connection, $area, $rows);
	?>
	</form>
</div>