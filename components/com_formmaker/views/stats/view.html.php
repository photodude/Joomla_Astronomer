<?php 

 /**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
class formmakerViewstats extends JViewLegacy
{
	function display($tpl = null)
	{
		$id=JRequest::getVar('id',0);		if($id==0)			return;		$model  = $this->getModel();		$result = $model->show_stats();		if(!$result)			return;		$this->assignRef( 'choices',$result[0] );				parent::display($tpl);
	}
}
?>