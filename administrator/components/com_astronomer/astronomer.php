<?php
/**
 * @version    CVS: 1.0.2
 * @package    Com_Astronomer
 * @author     Troy Hall <troy@jowwow.net>
 * @copyright  2016 Troy Hall
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_astronomer'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Astronomer', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Astronomer');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
