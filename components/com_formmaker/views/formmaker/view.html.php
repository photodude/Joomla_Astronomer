<?php 

 /**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
class formmakerViewformmaker extends JViewLegacy
{
    function display($tpl = null)
		{
			$id=JRequest::getVar('id',0);
			if($id==0)
				return;
				
			$model  = $this->getModel();
			$result = $model->showform();
			
			if(!$result)
				return;
			$ok	= $model->savedata($result[0]);
			if(is_numeric($ok))		
				$model->remove($ok);
			
			$this->assignRef( 'row',	$result[0] );
			$this->assignRef( 'Itemid',	$result[1] );
			$this->assignRef( 'label_id',	$result[2] );
			$this->assignRef( 'label_type',	$result[3] );
			$this->assignRef( 'form_theme',	$result[4] );
			$this->assignRef( 'ok',	$ok	);
			parent::display($tpl);
		}
}
?>