<?php
/**
 * @version    CVS: 1.0.5
 * @package    Com_Astronomer
 * @author     Troy Hall <troy@jowwow.net>
 * @copyright  2016 Troy Hall
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Astronomer', JPATH_COMPONENT);
JLoader::register('AstronomerController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Astronomer');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
