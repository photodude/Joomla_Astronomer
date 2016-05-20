<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<?php
	if(!empty($connection['Connection']['extras'][$area]['actions']['edit']['form_event'])){
		$form_data = explode(':', trim($connection['Connection']['extras'][$area]['actions']['edit']['form_event']));
		$form_name = $form_data[0];
		$form_event = \GCore\Libs\Request::data('event', $form_data[1]);
		ob_start();
		echo \GCore\Libs\App::call('front', 'chronoforms', '', '', array('chronoform' => $form_name, 'event' => $form_event));
		$output = ob_get_clean();
		/*
		$pattern = '/<form([^>]*?)([^>]*?)>/is';
		preg_match_all($pattern, $output, $matches);
		if(!empty($matches[0][0])){
			$form_tag = $matches[0][0];
			$form_tag = preg_replace('/ action=("|\')(.*?)("|\')/i', ' action="'.r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$connection['Connection']['title'].'&act=save').'"', $form_tag);
			$form_tag = preg_replace('/ name=("|\')(.*?)("|\')/i', ' name="admin_form"', $form_tag);
			$form_tag = preg_replace('/ id=("|\')(.*?)("|\')/i', ' id="admin_form"', $form_tag);
		}
		$output = str_replace($matches[0][0], $form_tag, $output);
		*/
		echo $output;
	}else{
		$code = $connection['Connection']['extras'][$area]['actions']['edit']['code'];
		ob_start();
		eval('?>'.$code);
		$code = ob_get_clean();
		$code = $this->Lister->prepare($connection, $code);
		$code = $this->Lister->translate($connection, $code);
		?>
		<form action="<?php echo r_('index.php?ext=chronoconnectivity&cont=lists&ccname='.$connection['Connection']['title'].'&act=save'); ?>" method="post" name="admin_form" id="admin_form">
		<?php echo $code; ?>
		</form>
		<?php
	}
?>