<?php 

 /**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
class formmakerViewsubmissions extends JViewLegacy
{
    function display($tpl = null)
	{	
			$input_get = JFactory::getApplication()->input;
			
			$id=JRequest::getVar('id',0);
			
			if($id==0)
				return;
	
			$model  = $this->getModel();
			$task = $input_get->getString('task'); 

			if($task)
			{
				switch($task)
				{
					case 'generate_csv':
						$model->generate_csv();
						break;
				
					case 'generate_xml':
						$model->generate_xml();
						break;
						
					case 'show_matrix':
						$model->show_matrix();
						break;	
						
					case 'show_map':
						$model->show_map();
						break;	
					case 'paypal_info':
						$model->paypal_info();
						break;			
				}
			
				return;
			}
			
			$result = $model->showsubmissions();

			if(!$result)
				return;

			$this->assignRef( 'rows',	$result[0] );
			$this->assignRef( 'lists',	$result[1] );
			$this->assignRef( 'pageNav',	$result[2] );
			$this->assignRef( 'sorted_labels',	$result[3] );
			$this->assignRef( 'label_titles',	$result[4] );
			$this->assignRef( 'rows_ord',	$result[5] );
			$this->assignRef( 'sorted_labels_id',	$result[6] );
			$this->assignRef( 'sorted_labels_type',	$result[7] );
			$this->assignRef( 'total_entries',	$result[8] );
			$this->assignRef( 'total_views',	$result[9] );
			$this->assignRef( 'join_count',	$result[10] );
			$this->assignRef( 'form_title',	$result[11] );
			
			parent::display($tpl);

		}
}
?>