<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<div class="chrono-page-container">
<div class="container" style="width:100%;">
<div class="row" id="page_top"></div>
<?php
	$doc = \GCore\Libs\Document::getInstance();
	$doc->_('jquery');
	$doc->_('bootstrap');
	$doc->_('keepalive');
	//$doc->addCssFile('extensions/chronoconnectivity/assets/css/fixes.css');

	$areas_supported = array('front', 'admin');

	$this->Toolbar->addButton('apply', r_('index.php?ext=chronoconnectivity&act=save&save_act=apply'), 'Apply', $this->Assets->image('apply', 'toolbar/'));
	$this->Toolbar->addButton('save', r_('index.php?ext=chronoconnectivity&act=save'), 'Save', $this->Assets->image('save', 'toolbar/'));
	$this->Toolbar->addButton('cancel', r_('index.php?ext=chronoconnectivity'), 'Cancel', $this->Assets->image('cancel', 'toolbar/'), 'link');

?>

<?php
	ob_start();
?>
<script>
	jQuery(document).ready(function($){
		$('.gbs3 .nav > li:first-child').addClass('active');
		$('.gbs3 .tab-content > div.tab-pane:first-child').addClass('active').css('display', '');
		//stop enter key from firing buttons
		/*$(document).on('keypress', function(e){
			if(e.which == 13){
				e.preventDefault();
			}
		});*/
	});
</script>
<script>
	jQuery(document).ready(function($){
		/*$('#add_new_locale').on('show.bs.modal', function () {
			$('#locale_name').val('');
		});*/
		$('#add_new_locale_button').on('click', function(){
			var locale_name = $('#locale_name').val();
			if(locale_name == '')return false;
			$('#locales_tabs_heads').append('<li><a href="#locale-'+locale_name+'" data-g-toggle="tab">'+locale_name+'</a></li>');
			var tab_content = $('#locale_generic_config').html().replace(/{N}/ig, $('#locale_name').val());
			$('#locales_tabs_contents').append('<div class="tab-pane" id="locale-'+locale_name+'">'+tab_content+'</div>');
			//$('#locales_tabs_heads a:last').tab('show');
			$('#locales_tabs_heads').gtabs({
				'pane_selector':'.tab-pane',
				'tab_selector':'[data-g-toggle="tab"]',
			});
			$('#locales_tabs_heads').gtabs('get').show($('#locales_tabs_heads a:last'));
			$('#locale_name').val('');
		});
	});
	function remove_locale(id){
		jQuery('#locale-'+id).remove();
		jQuery('a[href="#locale-'+id+'"]').parent('li').remove();
		//jQuery('#locales_tabs_heads a:last').tab('show');
		//jQuery('#locales_tabs_heads').gtabs('get').show($('#locales_tabs_heads a:last'));
		jQuery('html, body').animate({
			scrollTop: jQuery('#page_top').offset().top
		}, 300);
		return false;
	}
</script>
<script>
	jQuery(document).ready(function($){
		/*$('#add_new_model').on('show.bs.modal', function () {
			$('#model_name').val('');
		});*/
		$('#add_new_model_button').on('click', function(){
			var model_name = $('#model_name').val();
			var models_count = $('#models_count').val();
			if(model_name == '')return false;
			$('#models_tabs_heads').append('<li><a href="#modelslist-'+models_count+'" data-g-toggle="tab">'+model_name+'</a></li>');
			var tab_content = $('#model_generic_config').html().replace(/{N}/ig, models_count);
			$('#models_tabs_contents').append('<div class="tab-pane" id="modelslist-'+models_count+'">'+tab_content+'</div>');
			//$('#models_tabs_heads a:last').tab('show');
			$('#models_tabs_heads').gtabs('get').show($('#models_tabs_heads a:last'));
			$('#model_name').val('');
			models_count++;
			$('#models_count').val(models_count);
		});
	});
	function remove_model(id){
		jQuery('#modelslist-'+id).remove();
		jQuery('a[href="#modelslist-'+id+'"]').parent('li').remove();
		//jQuery('#models_tabs_heads a:last').tab('show'); //activate last tab
		jQuery('html, body').animate({
			scrollTop: jQuery('#page_top').offset().top
		}, 300);
		return false;
	}
</script>
<?php foreach($areas_supported as $area): ?>
<script>
	jQuery(document).ready(function($){
		/*$('#add_new_action_<?php echo $area; ?>').on('show.bs.modal', function () {
			$('#action_name_<?php echo $area; ?>').val('');
		});*/
		$('#add_new_action_button_<?php echo $area; ?>').on('click', function(){
			var action_name = $('#action_name_<?php echo $area; ?>').val();
			if(action_name == '')return false;
			$('#actions_tabs_heads_<?php echo $area; ?>').append('<li><a href="#'+action_name+'" data-g-toggle="tab">'+action_name+'</a></li>');
			var tab_content = $('#action_generic_config').html().replace(/{N}/ig, $('#action_name_<?php echo $area; ?>').val());
			var tab_content = tab_content.replace(/{AREA}/ig, '<?php echo $area; ?>');
			$('#actions_tabs_contents_<?php echo $area; ?>').append('<div class="tab-pane" id="'+action_name+'">'+tab_content+'</div>');
			//$('#actions_tabs_heads_<?php echo $area; ?> a:last').tab('show');
			$('#actions_tabs_heads_<?php echo $area; ?>').gtabs('get').show($('#actions_tabs_heads_<?php echo $area; ?> a:last'));
			$('#action_name_<?php echo $area; ?>').val('');
		});
	});
</script>
<?php endforeach; ?>
<?php
	$wizard_jscode = ob_get_clean();
	$doc->addHeaderTag($wizard_jscode);
?>
<div class="row" style="margin-top:20px;">
	<div class="col-md-6">
		<h3><?php echo !empty($this->data['Connection']['title']) ? $this->data['Connection']['title'] : 'New connection...'; ?></h3>
	</div>
	<div class="col-md-6 pull-right text-right">
		<?php
			echo $this->Toolbar->renderBar();
		?>
	</div>
</div>
<div class="row">
<form action="<?php echo r_('index.php?ext=chronoconnectivity&act=save'); ?>" method="post" name="admin_form" id="admin_form">
	<?php echo $this->Html->input('Connection[id]', array('type' => 'hidden')); ?>
	<?php //l_('ADMIN_LIST'); ?><?php //l_('FRONT_LIST'); ?>
	<div id="details-panel">
		<div class="panel panel-default">
			<div class="panel-heading">
				<ul class="nav nav-pills">
					<li class="active"><a href="#general" data-g-toggle="tab"><?php echo l_('GENERAL'); ?></a></li>
					<li><a href="#models" data-g-toggle="tab"><?php echo l_('MODELS'); ?></a></li>
					<?php
						foreach($areas_supported as $area):
					?>
					<li><a href="#<?php echo $area; ?>-list" data-g-toggle="tab"><?php echo l_(strtoupper($area).'_LIST'); ?></a></li>
					<?php endforeach; ?>
					<li><a href="#ndb" data-g-toggle="tab"><?php echo l_('CC_EXTERNAL_DATABASE'); ?></a></li>
					<li><a href="#plugins" data-g-toggle="tab"><?php echo l_('CONN_PLUGINS'); ?></a></li>
					<li><a href="#locales" data-g-toggle="tab"><?php echo l_('CF_LOCALES'); ?></a></li>
					<li><a href="#help" data-g-toggle="tab"><?php echo l_('HELP'); ?></a></li>
				</ul>
			</div>
			<div class="panel-body">
				<div class="tab-content">
					<div id="general" class="tab-pane active">
						<?php echo $this->Html->formStart(); ?>
						<?php echo $this->Html->formSecStart(); ?>
						<?php echo $this->Html->formLine('Connection[title]', array('type' => 'text', 'id' => 'con_name', 'label' => l_('CONNECTION_NAME'), 'class' => 'XL', 'sublabel' => 'Unique connection name without spaces or any special characters, underscores _ or dashes -')); ?>
						<?php echo $this->Html->formLine('Connection[published]', array('type' => 'dropdown', 'label' => l_('PUBLISHED'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 1)); ?>
						<?php echo $this->Html->formSecEnd(); ?>
						<?php echo $this->Html->formEnd(); ?>
					</div>
					<div id="ndb" class="tab-pane">
						<?php echo $this->Html->formStart(); ?>
						<?php echo $this->Html->formSecStart(); ?>
						<?php echo $this->Html->formLine('Connection[extras][ndb][enabled]', array('type' => 'dropdown', 'label' => l_('CC_EXTERNAL_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CC_EXTERNAL_ENABLED_DESC'))); ?>
						<?php echo $this->Html->formLine('Connection[extras][ndb][driver]', array('type' => 'text', 'label' => l_('CC_EXTERNAL_DB_DRIVER'), 'value' => 'mysql', 'class' => 'L', 'sublabel' => l_('CC_EXTERNAL_DB_DRIVER_DESC'))); ?>
						<?php echo $this->Html->formLine('Connection[extras][ndb][host]', array('type' => 'text', 'label' => l_('CC_EXTERNAL_DB_HOST'), 'value' => 'localhost', 'class' => 'L', 'sublabel' => l_('CC_EXTERNAL_DB_HOST_DESC'))); ?>
						<?php echo $this->Html->formLine('Connection[extras][ndb][database]', array('type' => 'text', 'label' => l_('CC_EXTERNAL_DB_NAME'), 'class' => 'L', 'sublabel' => l_('CC_EXTERNAL_DB_NAME_DESC'))); ?>
						<?php echo $this->Html->formLine('Connection[extras][ndb][user]', array('type' => 'text', 'label' => l_('CC_EXTERNAL_DB_USER'), 'class' => 'L', 'sublabel' => l_('CC_EXTERNAL_DB_USER_DESC'))); ?>
						<?php echo $this->Html->formLine('Connection[extras][ndb][password]', array('type' => 'password', 'label' => l_('CC_EXTERNAL_DB_PASSWORD'), 'class' => 'L', 'sublabel' => l_('CC_EXTERNAL_DB_PASSWORD_DESC'))); ?>
						<?php echo $this->Html->formLine('Connection[extras][ndb][prefix]', array('type' => 'text', 'label' => l_('CC_EXTERNAL_DB_PREFIX'), 'sublabel' => l_('CC_EXTERNAL_DB_PREFIX_DESC'))); ?>
						<?php echo $this->Html->formSecEnd(); ?>
						<?php echo $this->Html->formEnd(); ?>
					</div>
					<div id="models" class="tab-pane">

						<button type="button" class="btn btn-success" data-g-toggle="modal" data-g-target="#add_new_model">
							<?php echo l_('ADD_NEW_MODEL'); ?>
						</button>
						<br>
						<!-- Modal -->
						<div class=" fade" id="add_new_model" tabindex="-1" role="dialog" aria-labelledby="Model_ModalLabel" aria-hidden="true" style="display:none;">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-g-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="Model_ModalLabel"><?php echo l_('ADD_NEW_MODEL'); ?></h4>
									</div>
									<div class="modal-body">
										<?php echo $this->Html->formSecStart(); ?>
										<?php echo $this->Html->formLine('model_name', array('type' => 'text', 'id' => 'model_name', 'label' => l_('MODEL_NAME'), 'sublabel' => 'An identifier for your model, it should be unique and should be in alphanumeric letters only, and should not start with a numeric.')); ?>
										<?php echo $this->Html->formSecEnd(); ?>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-success" data-g-dismiss="modal" id="add_new_model_button"><?php echo l_('ADD_NEW_MODEL'); ?></button>
									</div>
								</div>
							</div>
						</div>

						<div id="modelslist" class="actions_tabs" style="margin-top:10px;">
							<ul class="nav nav-tabs" id="models_tabs_heads">
								<li class="active"><a href="#modelslist-1" data-g-toggle="tab"><?php echo !empty($this->data['Connection']['extras']['models']['name'][1]) ? $this->data['Connection']['extras']['models']['name'][1] : 'Model1'; ?></a></li>
								<?php if(isset($this->data['Connection']['extras']['models']['name'][2])):	?>
									<?php foreach($this->data['Connection']['extras']['models']['name'] as $k => $name): ?>
										<?php if($k != 1): ?>
										<li><a href="#modelslist-<?php echo $k; ?>" data-g-toggle="tab"><?php echo $name; ?></a></li>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
							<div class="tab-content" id="models_tabs_contents">
								<div id="modelslist-1" class="tab-pane active">
									<?php echo $this->Html->formStart(); ?>
									<?php echo $this->Html->formSecStart(); ?>
									<?php echo $this->Html->formLine('Connection[extras][models][name][1]', array('type' => 'text', 'id' => 'model_name_1', 'label' => l_('MODEL_TITLE'), 'sublabel' => 'An identifier for your model, it should be unique and should be in alphanumeric letters only, and should not start with a numeric.')); ?>
									<?php echo $this->Html->formLine('Connection[extras][models][tablename][1]', array('type' => 'dropdown', 'id' => 'model_table_1', 'options' => $tables, 'label' => l_('TABLE_NAME'), 'sublabel' => 'Select the database table presented by this model.')); ?>
									<?php echo $this->Html->formLine('Connection[extras][models][conditions][1]', array('type' => 'textarea', 'label' => l_('CONDITIONS'), 'rows' => 10, 'cols' => 80, 'sublabel' => 'The where conditions in array format: return array("field_name" => "XYZ");')); ?>
									<?php echo $this->Html->formLine('Connection[extras][models][fields][1]', array('type' => 'textarea', 'label' => l_('FIELDS'), 'rows' => 3, 'cols' => 80, 'sublabel' => 'comma separated list of fields to be retrieved, you can leave this empty to load all fields.')); ?>
									<?php echo $this->Html->formLine('Connection[extras][models][order][1]', array('type' => 'textarea', 'label' => l_('ORDER'), 'rows' => 3, 'cols' => 80, 'sublabel' => 'comma separated list of fields to be used for ordering.')); ?>
									<?php echo $this->Html->formLine('Connection[extras][models][group][1]', array('type' => 'textarea', 'label' => l_('GROUP'), 'rows' => 3, 'cols' => 80, 'sublabel' => 'comma separated list of fields to be used for grouping, useful for hasMany models only.')); ?>
									<?php echo $this->Html->formSecEnd(); ?>
									<?php echo $this->Html->formEnd(); ?>
								</div>
								<?php if(isset($this->data['Connection']['extras']['models']['name'][2])):	?>
									<?php foreach($this->data['Connection']['extras']['models']['name'] as $k => $name): ?>
									<?php if($k != 1): ?>
									<div id="modelslist-<?php echo $k; ?>" class="tab-pane">
										<?php echo $this->Html->formStart(); ?>
										<?php echo $this->Html->formSecStart(); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][name]['.$k.']', array('type' => 'text', 'id' => 'model_name_'.$k, 'label' => l_('MODEL_TITLE'), 'sublabel' => 'An identifier for your model, it should be unique and should be in alphanumeric letters only, and should not start with a numeric.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][tablename]['.$k.']', array('type' => 'dropdown', 'id' => 'model_table_'.$k, 'options' => $tables, 'label' => l_('TABLE_NAME'), 'sublabel' => 'Select the database table presented by this model.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][relation]['.$k.']', array('type' => 'dropdown', 'id' => 'model_relation_'.$k, 'options' => array('' => '', 'hasOne' => 'hasOne', 'belongsTo' => 'belongsTo', 'hasMany' => 'hasMany'), 'label' => l_('CONN_RELATION'), 'sublabel' => 'The relation associating the associated model selected below to this model, this can be left empty if there is no relation exists.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][associated_model]['.$k.']', array('type' => 'text', 'id' => 'associated_model_'.$k, 'label' => l_('CONN_ASSOC_MODEL'), 'sublabel' => 'The model associated to this one, leave empty and it will use the main model.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][assoc_save]['.$k.']', array('type' => 'dropdown', 'id' => 'model_assoc_save_'.$k, 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'label' => l_('CONN_ASSOCIATIVE_SAVING'), 'sublabel' => 'Save the record of this model when saving a record of the main model.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][assoc_delete]['.$k.']', array('type' => 'dropdown', 'id' => 'model_assoc_delete_'.$k, 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'label' => l_('CONN_ASSOCIATIVE_DELETE'), 'sublabel' => 'Delete the record of this model when deleting a record of the main model.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][foreignKey]['.$k.']', array('type' => 'text', 'id' => 'model_foreignkey_'.$k, 'label' => l_('CONN_FOREIGN_KEY'), 'sublabel' => 'The foreign key field used in the relation, either in the main model or in this model, this depends on the relation type.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][conditions]['.$k.']', array('type' => 'textarea', 'label' => l_('CONDITIONS'), 'style' => 'width:auto;', 'rows' => 5, 'cols' => 80, 'sublabel' => 'The where conditions in array format: return array("field_name" => "XYZ");')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][join_conditions]['.$k.']', array('type' => 'textarea', 'label' => l_('JOIN_CONDITIONS'), 'style' => 'width:auto;', 'rows' => 5, 'cols' => 80, 'sublabel' => 'The join conditions in array format, if not provided, it will be auto generated for hasOne and belongsTo.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][join_type]['.$k.']', array('type' => 'text', 'label' => l_('JOIN_TYPE'), 'sublabel' => 'the type of the join, "left" is used by default for hasOne and belongsTo.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][fields]['.$k.']', array('type' => 'text', 'label' => l_('FIELDS'), 'class' => 'XXL', 'sublabel' => 'comma separated list of fields to be retrieved from this table, PLEASE LEAVE THIS BOX EMPTY IF YOU HAVE MORE THAN 1 MODEL, the Join will fail if the primary/foreign keys are not retreieved.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][order]['.$k.']', array('type' => 'text', 'label' => l_('ORDER'), 'class' => 'XXL', 'sublabel' => 'comma separated list of fields to be used for ordering.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][group]['.$k.']', array('type' => 'text', 'label' => l_('GROUP'), 'class' => 'XXL', 'sublabel' => 'comma separated list of fields to be used for grouping, useful for hasMany models only.')); ?>
										<?php echo $this->Html->formLine('Connection[extras][models][pkey]['.$k.']', array('type' => 'text', 'id' => 'pkey_'.$k, 'label' => l_('CONN_PKEY'), 'sublabel' => 'The primary key for this model, you should only fill this if your table does NOT have a primary key, the field name here will be used for some operations.')); ?>

										<?php echo $this->Html->formSecEnd(); ?>
										<?php echo $this->Html->formEnd(); ?>
										<button type="button" class="remove_model_button btn btn-danger" onclick="remove_model('<?php echo $k; ?>');">
											<?php echo l_('REMOVE_MODEL'); ?>
										</button>
									</div>
									<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php
						foreach($areas_supported as $area):
					?>
					<div id="<?php echo $area; ?>-list" class="tab-pane">
						<div id="<?php echo $area; ?>-list-details">
							<ul class="nav nav-tabs" id="">
								<li class="active"><a href="#<?php echo $area; ?>-list-settings" data-g-toggle="tab"><?php echo l_('SETTINGS'); ?></a></li>
								<li><a href="#<?php echo $area; ?>-list-display" data-g-toggle="tab"><?php echo l_('LIST_DISPLAY'); ?></a></li>
								<li><a href="#<?php echo $area; ?>-list-actions" data-g-toggle="tab"><?php echo l_('ACTIONS'); ?></a></li>
								<li><a href="#<?php echo $area; ?>-list-permissions" data-g-toggle="tab"><?php echo l_('PERMISSIONS'); ?></a></li>
							</ul>
							<div class="tab-content" id="">
								<div id="<?php echo $area; ?>-list-settings" class="tab-pane active">
									<?php echo $this->Html->formSecStart(); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][display][block]', array('type' => 'dropdown', 'label' => l_('CONN_DISPLAY_TYPE'), 'options' => $blocks, 'values' => 'table')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][list]', array('type' => 'textarea', 'label' => l_('CONN_COLUMNS_LIST'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields to be processed, e.g:Model.field:Title')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][sortable]', array('type' => 'textarea', 'label' => l_('CONN_SORTABLES'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields to be sortable, e.g:Model.field:Model.field')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][view_linkable]', array('type' => 'textarea', 'label' => l_('CONN_VIEW_LINKABLE'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields to be linkable to the view action, e.g:Model.field:Model.field')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][linkable]', array('type' => 'textarea', 'label' => l_('CONN_EDIT_LINKABLE'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields to be linkable to the edit action, e.g:Model.field:Model.field')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][binary]', array('type' => 'textarea', 'label' => l_('CONN_BINARY'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields to be binary (0/1 values), e.g:Model.field:Model.field')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][filters]', array('type' => 'textarea', 'label' => l_('CONN_FILTERS'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields to be used as filters, e.g:Model.field, filter fields will have to be created by user, fields names should be in this format: "fltr[Model][field]"')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][searchable]', array('type' => 'textarea', 'label' => l_('CONN_SEARCHABLE'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields to be searchable, e.g:Model.field, the search field will have to be created by user, field name should be "srch"')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][functions]', array('type' => 'textarea', 'label' => 'PHP Functions', 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => "Multi line list of fields with function definition, the definition string is passed to a create_function function which accepts 2 params, \$cell for current field value and \$row for current row data,<br> e.g:Model.field:return \$cell;")); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][htmls]', array('type' => 'textarea', 'label' => 'HTML', 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields with their HTML code, e.g:Model.field:MY HTML CODE')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][images]', array('type' => 'textarea', 'label' => 'images', 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields with their images path, e.g:Model.field:http://path_to_image')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][links]', array('type' => 'textarea', 'label' => 'Links', 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields with their links, e.g:Model.field:index.php?opt=xyz')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][fields]', array('type' => 'textarea', 'label' => 'Fields', 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields with their fields, e.g:Model.field:FIELD_CODE')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][styles]', array('type' => 'textarea', 'label' => 'Styles', 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of fields with their styles, e.g:Model.field:width:10%;text-align:left')); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][columns][sessioned]', array('type' => 'textarea', 'label' => 'Stored fields', 'style' => 'width:auto;', 'rows' => 10, 'cols' => 100, 'sublabel' => 'Multi line list of view fields to be saved in sessions, useful for custom search/filtering fields.')); ?>
									<?php //echo $this->Html->formLine('Connection[extras]['.$area.'][display][jquery]', array('type' => 'dropdown', 'label' => l_('CONN_DISPLAY_JQUERY'), 'values' => 1, 'options' => array(0 => l_('NO'), 1 => l_('YES')))); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][display][debug]', array('type' => 'dropdown', 'label' => l_('CONN_DISPLAY_DEBUG'), 'options' => array(0 => l_('NO'), 1 => l_('YES')))); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][display][page_limit]', array('type' => 'text', 'label' => l_('CONN_PAGE_LIMIT'), 'sublabel' => l_('CONN_PAGE_LIMIT_DESC'))); ?>
									<?php echo $this->Html->formLine('Connection[extras]['.$area.'][display][pre_process]', array('type' => 'textarea', 'label' => "Pre display processing", 'style' => 'width:auto;', 'rows' => 7, 'cols' => 100, 'sublabel' => "Any PHP code to be processed before the list is displayed.")); ?>
									<?php echo $this->Html->formSecEnd(); ?>
								</div>
								<div id="<?php echo $area; ?>-list-display" class="tab-pane">
									<div id="<?php echo $area; ?>-list-display-options">
										<div class="container" style="width:100%; margin-top:20px;">
											<div class="row">
												<div class="col-md-2">
													<ul class="nav nav-pills nav-stacked" id="">
														<?php foreach($blocks_classes as $blocks_class): ?>
															<li><a href="#block-name-<?php echo $area; ?>-<?php echo $blocks_class::$name; ?>" data-g-toggle="tab"><?php echo $blocks_class::$title; ?></a></li>
														<?php endforeach; ?>
													</ul>
												</div>
												<div class="col-md-10">
													<div class="tab-content" id="">
														<?php foreach($blocks_classes as $blocks_class): ?>
															<div id="block-name-<?php echo $area; ?>-<?php echo $blocks_class::$name; ?>" class="tab-pane">
																<div class="panel panel-info">
																	<div class="panel-heading"><?php echo $blocks_class::$title; ?></div>
																	<div class="panel-body">
																		<?php $blocks_class::config($area); ?>
																	</div>
																</div>
															</div>
														<?php endforeach; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div id="<?php echo $area; ?>-list-actions" class="tab-pane">
									<div id="<?php echo $area; ?>-list-actions-options">
										<div class="container" style="width:100%; margin-top:20px;">
											<div class="row" style="margin-bottom:10px;">
												<div class="col-md-2">
												</div>
												<div class="col-md-10">
													<button type="button" class="btn btn-success" data-g-toggle="modal" data-g-target="#add_new_action_<?php echo $area; ?>">
														<?php echo l_('CF_ADD_NEW_ACTION'); ?>
													</button>
												</div>
												<br>
												<div class=" fade" id="add_new_action_<?php echo $area; ?>" tabindex="-1" role="dialog" aria-labelledby="Action_<?php echo $area; ?>_ModalLabel" aria-hidden="true" style="display:none;">
													<div class="modal-dialog">
														<div class="modal-content">
															<div class="modal-header">
																<button type="button" class="close" data-g-dismiss="modal" aria-hidden="true">&times;</button>
																<h4 class="modal-title" id="Action_<?php echo $area; ?>_ModalLabel"><?php echo l_('CF_ADD_NEW_ACTION'); ?></h4>
															</div>
															<div class="modal-body">
																<?php echo $this->Html->formSecStart(); ?>
																<?php echo $this->Html->formLine('action_name_'.$area, array('type' => 'text', 'id' => 'action_name_'.$area, 'label' => l_('CONN_ACTION_NAME'), 'sublabel' => 'An identifier for your action, it should be unique and should be in alphanumeric letters only, and should not start with a numeric.')); ?>
																<?php echo $this->Html->formSecEnd(); ?>
															</div>
															<div class="modal-footer">
																<button type="button" class="btn btn-success" data-g-dismiss="modal" id="add_new_action_button_<?php echo $area; ?>"><?php echo l_('CF_ADD_NEW_ACTION'); ?></button>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-2">
													<ul class="nav nav-pills nav-stacked" id="actions_tabs_heads_<?php echo $area; ?>">
														<?php if(isset($this->data['Connection']['extras'][$area]['actions'])):	?>
															<?php foreach($this->data['Connection']['extras'][$area]['actions'] as $name => $data): ?>
																<?php //if($name != 'edit'): ?>
																<li><a href="#actions-<?php echo $area; ?>-<?php echo $name; ?>" data-g-toggle="tab"><?php echo $name; ?></a></li>
																<?php //endif; ?>
															<?php endforeach; ?>
														<?php endif; ?>
													</ul>
												</div>
												<div class="col-md-10">
													<div class="tab-content" id="actions_tabs_contents_<?php echo $area; ?>">
														<?php if(isset($this->data['Connection']['extras'][$area]['actions'])):	?>
															<?php foreach($this->data['Connection']['extras'][$area]['actions'] as $name => $data): ?>
																<?php //if($name != 'edit'): ?>
																	<div id="actions-<?php echo $area; ?>-<?php echo $name; ?>" class="tab-pane">
																		<div class="panel panel-info">
																			<div class="panel-heading"><?php echo $name; ?></div>
																			<div class="panel-body">
																				<?php echo $this->Html->formSecStart(); ?>
																				<?php echo $this->Html->formLine('Connection[extras]['.$area.'][actions]['.$name.'][form_event]', array('type' => 'text', 'label' => 'Form event', 'sublabel' => 'Form name and event to use, e.g:FORM_NAME:EVENT_NAME')); ?>
																				<?php echo $this->Html->formLine('Connection[extras]['.$area.'][actions]['.$name.'][code]', array('type' => 'textarea', 'label' => l_('CONN_CODE'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 80, 'sublabel' => 'The action code, may contain PHP code with tags.')); ?>
																				<?php echo $this->Html->formSecEnd(); ?>
																			</div>
																		</div>
																	</div>
																<?php //endif; ?>
															<?php endforeach; ?>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div id="<?php echo $area; ?>-list-permissions" class="tab-pane">
									<div id="<?php echo $area; ?>-list-permissions-options">
										<div class="container" style="width:100%; margin-top:20px;">
											<div class="row">
												<div class="col-md-2">
												</div>
												<div class="col-md-10">
													<?php echo $this->Html->formSecStart(); ?>
													<?php echo $this->Html->formLine('Connection[extras]['.$area.'][permissions_conf][owner_id_column]', array('type' => 'text', 'label' => l_('OWENR_ID_COLUMN'))); ?>
													<?php echo $this->Html->formSecEnd(); ?>
												</div>
											</div>
											<div class="row">
												<div class="col-md-2">
													<ul class="nav nav-pills nav-stacked" id="">
														<?php foreach($actions_list as $action): ?>
															<li><a href="#<?php echo $area; ?>-list-permissions-<?php echo $action; ?>" data-g-toggle="tab"><?php echo $action; ?></a></li>
														<?php endforeach; ?>
														<?php if(isset($this->data['Connection']['extras'][$area]['actions'])):	?>
															<?php foreach($this->data['Connection']['extras'][$area]['actions'] as $name => $data): ?>
															<?php if(!in_array($name, $standard_actions)): ?>
															<li><a href="#<?php echo $area; ?>-list-permissions-<?php echo $name; ?>" data-g-toggle="tab"><?php echo $name; ?></a></li>
															<?php endif; ?>
															<?php endforeach; ?>
														<?php endif; ?>
													</ul>
												</div>
												<div class="col-md-10">
													<div class="tab-content" id="">
														<?php foreach($actions_list as $action): ?>
															<div id="<?php echo $area; ?>-list-permissions-<?php echo $action; ?>" class="tab-pane">
																<div class="panel panel-info">
																	<div class="panel-heading"><?php echo $action; ?></div>
																	<div class="panel-body">
																		<?php foreach($rules as $g_id => $g_name): ?>
																			<?php echo $this->Html->formSecStart(); ?>
																			<?php echo $this->Html->formLine('Connection[extras]['.$area.'][permissions]['.$action.']['.$g_id.']', array('type' => 'dropdown', 'label' => $g_name, 'options' => array(0 => l_('INHERITED'), '' => l_('NOT_SET'), 1 => l_('ALLOWED'), -1 => l_('DENIED')))); ?>
																			<?php echo $this->Html->formSecEnd(); ?>
																		<?php endforeach; ?>
																	</div>
																</div>
															</div>
														<?php endforeach; ?>
														<?php if(isset($this->data['Connection']['extras'][$area]['actions'])):	?>
															<?php foreach($this->data['Connection']['extras'][$area]['actions'] as $name => $data): ?>
															<?php if(!in_array($name, $standard_actions)): ?>
																<div id="<?php echo $area; ?>-list-permissions-<?php echo $name; ?>" class="tab-pane">
																	<div class="panel panel-info">
																		<div class="panel-heading"><?php echo $name; ?></div>
																		<div class="panel-body">
																			<?php foreach($rules as $g_id => $g_name): ?>
																				<?php echo $this->Html->formSecStart(); ?>
																				<?php echo $this->Html->formLine('Connection[extras]['.$area.'][permissions]['.$name.']['.$g_id.']', array('type' => 'dropdown', 'label' => $g_name, 'options' => array(0 => l_('INHERITED'), '' => l_('NOT_SET'), 1 => l_('ALLOWED'), -1 => l_('DENIED')))); ?>
																				<?php echo $this->Html->formSecEnd(); ?>
																			<?php endforeach; ?>
																		</div>
																	</div>
																</div>
															<?php endif; ?>
															<?php endforeach; ?>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
					<div id="plugins" class="tab-pane">
						<div class="container" style="width:100%; margin-top:20px;">
							<div class="row">
								<div class="col-md-2">
									<ul class="nav nav-pills nav-stacked" id="">
										<?php foreach($plugins_classes as $plugin_class): ?>
											<li><a href="#<?php echo $plugin_class::$name; ?>-plugin-tab" data-g-toggle="tab"><?php echo $plugin_class::$title; ?></a></li>
										<?php endforeach; ?>
									</ul>
								</div>
								<div class="col-md-10">
									<div class="tab-content" id="">
										<?php foreach($plugins_classes as $plugin_class): ?>
											<div id="<?php echo $plugin_class::$name; ?>-plugin-tab" class="tab-pane">
												<div class="panel panel-info">
													<div class="panel-heading"><?php echo $plugin_class::$title; ?></div>
													<div class="panel-body">
														<?php $plugin_class::config(); ?>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="locales" class="tab-pane">
						<button type="button" class="btn btn-success" data-g-toggle="modal" data-g-target="#add_new_locale">
							<?php echo l_('CF_ADD_NEW_LOCALE'); ?>
						</button>
						<br>
						<div class=" fade" id="add_new_locale" tabindex="-1" role="dialog" aria-labelledby="Locale_ModalLabel" aria-hidden="true" style="display:none;">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-g-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="Locale_ModalLabel"><?php echo l_('CF_ADD_NEW_LOCALE'); ?></h4>
									</div>
									<div class="modal-body">
										<?php echo $this->Html->formSecStart(); ?>
										<?php echo $this->Html->formLine('locale_name', array('type' => 'text', 'id' => 'locale_name', 'label' => l_('CF_LANG_NAME'), 'sublabel' => l_('CF_LANG_NAME_DESC'))); ?>
										<?php echo $this->Html->formSecEnd(); ?>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-success" data-g-dismiss="modal" id="add_new_locale_button"><?php echo l_('CF_ADD_NEW_LOCALE'); ?></button>
									</div>
								</div>
							</div>
						</div>
						<ul class="nav nav-tabs" id="locales_tabs_heads" style="margin-top:10px;">
							<?php if(isset($this->data['Connection']['extras']['locales'])):	?>
								<?php foreach($this->data['Connection']['extras']['locales'] as $k => $locale): ?>
									<li class=""><a href="#locale-<?php echo $k; ?>" data-g-toggle="tab"><?php echo $k; ?></a></li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
						<div class="tab-content" id="locales_tabs_contents">
							<?php if(isset($this->data['Connection']['extras']['locales'])):	?>
								<?php foreach($this->data['Connection']['extras']['locales'] as $k => $locale): ?>
									<div class="tab-pane" id="locale-<?php echo $k; ?>">
										<?php echo $this->Html->formStart(); ?>
										<?php echo $this->Html->formSecStart(); ?>
											<?php echo $this->Html->formLine('Connection[extras][locales]['.$k.'][lang_tag]', array('type' => 'text', 'label' => l_('CF_LANG_TAG'), 'sublabel' => l_('CF_LANG_TAG_DESC'))); ?>
											<?php echo $this->Html->formLine('Connection[extras][locales]['.$k.'][strict]', array('type' => 'dropdown', 'label' => l_('CF_LANG_STRICT'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_LANG_STRICT_DESC'))); ?>
											<?php echo $this->Html->formLine('Connection[extras][locales]['.$k.'][strings]', array('type' => 'textarea', 'style' => 'width:auto;', 'rows' => 10, 'cols' => 80, 'label' => l_('CF_LANG_STRINGS'), 'sublabel' => l_('CF_LANG_STRINGS_DESC'))); ?>
										<?php echo $this->Html->formSecEnd(); ?>
										<?php echo $this->Html->formEnd(); ?>
										<button type="button" class="remove_locale_button btn btn-danger" onclick="remove_locale('<?php echo $k; ?>');">
											<?php echo l_('REMOVE_LOCALE'); ?>
										</button>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
					<div id="help" class="tab-pane">
						<p>
						You can display pagenation in header/footer boxes using these functions:<br>
						<ol>
						<li>displays a drop down to change the limit: _PAGINATOR_LIST_ OR $this->view->Paginator->getList();</li>
						<li>displays info about the viewed records and the total: _PAGINATOR_INFO_ OR $this->view->Paginator->getInfo();</li>
						<li>displays the pagination links (next, prev..etc): _PAGINATOR_NAV_ OR $this->view->Paginator->getNav();</li>
						</ol>
						</p>
						<p>
						You can load different toolbar buttons using the following strings:<br>
						<ol>
						<li>_TOOLBAR_NEW_</li>
						<li>_TOOLBAR_DELETE_</li>
						<li>_TOOLBAR_SAVELIST_</li>
						<li>_TOOLBAR_CANCEL_</li>
						<li>_TOOLBAR_SAVE_</li>
						<li>_TOOLBAR_APPLY_</li>
						</ol>
						OR $this->view->Toolbar->renderButton("button_id", "link_href", "TEXT", "image_path", "submit_selectors");
						</p>
						<p>
						The fields list will accept fields names prefxied with the model name, e.g: Model.field, but may also accept the following placeholders:<br>
						<ol>
						<li>_SELECTOR_: for selectors checkboxes</li>
						<li>_EDIT_: for an edit link</li>
						<li>_DELETE_: for a delete link</li>
						</ol>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
</div>
<input name="models_count" id="models_count" value="<?php echo (!empty($this->data['Connection']['extras']['models']['name']) AND count($this->data['Connection']['extras']['models']['name']) > 1) ? max(array_keys($this->data['Connection']['extras']['models']['name'])) + 1 : 2; ?>" type="hidden" />
<div id="model_generic_config" class="generic_action_config" title="Generic" style="display:none;">
	<?php echo $this->Html->formStart(); ?>
	<?php echo $this->Html->formSecStart(); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][name][{N}]', array('type' => 'text', 'id' => 'model_name_{N}', 'label' => l_('MODEL_TITLE'), 'sublabel' => 'An identifier for your model, it should be unique and should be in alphanumeric letters only, and should not start with a numeric.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][tablename][{N}]', array('type' => 'dropdown', 'id' => 'model_table_{N}', 'options' => $tables, 'label' => l_('TABLE_NAME'), 'sublabel' => 'Select the database table presented by this model.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][relation][{N}]', array('type' => 'dropdown', 'id' => 'model_relation_{N}', 'options' => array('' => '', 'hasOne' => 'hasOne', 'belongsTo' => 'belongsTo', 'hasMany' => 'hasMany'), 'label' => l_('CONN_RELATION'), 'sublabel' => 'The relation associating the main model (Model#1) to this model, this can be left empty if there is no relation exists.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][associated_model][{N}]', array('type' => 'text', 'id' => 'associated_model_{N}', 'label' => l_('CONN_ASSOC_MODEL'), 'sublabel' => 'The model associated to this one, leave empty and it will use the main model.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][assoc_save][{N}]', array('type' => 'dropdown', 'id' => 'model_assoc_save_{N}', 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'label' => l_('CONN_ASSOCIATIVE_SAVING'), 'sublabel' => 'Save the record of this model when saving a record of the main model.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][assoc_delete][{N}]', array('type' => 'dropdown', 'id' => 'model_assoc_delete_{N}', 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'label' => l_('CONN_ASSOCIATIVE_DELETE'), 'sublabel' => 'Delete the record of this model when deleting a record of the main model.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][foreignKey][{N}]', array('type' => 'text', 'id' => 'model_foreignkey_{N}', 'label' => l_('CONN_FOREIGN_KEY'), 'sublabel' => 'The foreign key field used in the relation, either in the main model or in this model, this depends on the relation type.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][conditions][{N}]', array('type' => 'textarea', 'label' => l_('CONDITIONS'), 'style' => 'width:auto;', 'rows' => 5, 'cols' => 80, 'sublabel' => 'The where conditions in array format: return array("field_name" => "XYZ");')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][join_conditions][{N}]', array('type' => 'textarea', 'label' => l_('JOIN_CONDITIONS'), 'style' => 'width:auto;', 'rows' => 5, 'cols' => 80, 'sublabel' => 'The join conditions in array format, if not provided, it will be auto generated for hasOne and belongsTo.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][join_type][{N}]', array('type' => 'text', 'label' => l_('JOIN_TYPE'), 'sublabel' => 'the type of the join, "left" is used by default for hasOne and belongsTo.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][fields][{N}]', array('type' => 'text', 'label' => l_('FIELDS'), 'class' => 'XXL', 'sublabel' => 'comma separated list of fields to be retrieved from this table.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][order][{N}]', array('type' => 'text', 'label' => l_('ORDER'), 'class' => 'XXL', 'sublabel' => 'comma separated list of fields to be used for ordering.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][group][{N}]', array('type' => 'text', 'label' => l_('GROUP'), 'class' => 'XXL', 'sublabel' => 'comma separated list of fields to be used for grouping, useful for hasMany models only.')); ?>
	<?php echo $this->Html->formLine('Connection[extras][models][pkey][{N}]', array('type' => 'text', 'id' => 'pkey_{N}', 'label' => l_('CONN_PKEY'), 'sublabel' => 'The primary key for this model, you should only fill this if your table does NOT have a primary key, the field name here will be used for some operations.')); ?>

	<?php echo $this->Html->formSecEnd(); ?>
	<?php echo $this->Html->formEnd(); ?>
	<button type="button" class="remove_model_button btn btn-danger" onclick="remove_model('{N}');">
		<?php echo l_('REMOVE_MODEL'); ?>
	</button>
</div>

<div id="action_generic_config" class="generic_action_config" title="Generic" style="display:none;">
	<div class="panel panel-info">
		<div class="panel-heading">{N}</div>
		<div class="panel-body">
			<?php echo $this->Html->formSecStart(); ?>
			<?php echo $this->Html->formLine('Connection[extras][{AREA}][actions][{N}][form_event]', array('type' => 'text', 'label' => 'Form event', 'sublabel' => 'Form name and event to use, e.g:FORM_NAME:EVENT_NAME')); ?>
			<?php echo $this->Html->formLine('Connection[extras][{AREA}][actions][{N}][code]', array('type' => 'textarea', 'label' => l_('CONN_CODE'), 'style' => 'width:auto;', 'rows' => 10, 'cols' => 80, 'sublabel' => 'The action code, may contain PHP code with tags.')); ?>
			<?php echo $this->Html->formSecEnd(); ?>
		</div>
	</div>
</div>

<div id="locale_generic_config" class="generic_config" title="Generic" style="display:none;">
	<?php echo $this->Html->formStart(); ?>
	<?php echo $this->Html->formSecStart(); ?>
	<?php echo $this->Html->formLine('Connection[extras][locales][{N}][lang_tag]', array('type' => 'text', 'label' => l_('CF_LANG_TAG'), 'sublabel' => l_('CF_LANG_TAG_DESC'))); ?>
	<?php echo $this->Html->formLine('Connection[extras][locales][{N}][strict]', array('type' => 'dropdown', 'label' => l_('CF_LANG_STRICT'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_LANG_STRICT_DESC'))); ?>
	<?php echo $this->Html->formLine('Connection[extras][locales][{N}][strings]', array('type' => 'textarea', 'rows' => 10, 'cols' => 80, 'label' => l_('CF_LANG_STRINGS'), 'style' => 'width:auto;', 'sublabel' => l_('CF_LANG_STRINGS_DESC'))); ?>

	<?php echo $this->Html->formSecEnd(); ?>
	<?php echo $this->Html->formEnd(); ?>
	<button type="button" class="remove_locale_button btn btn-danger" onclick="remove_locale('{N}');">
		<?php echo l_('REMOVE_LOCALE'); ?>
	</button>
</div>

</div>
</div>