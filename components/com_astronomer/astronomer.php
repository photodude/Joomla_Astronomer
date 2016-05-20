<?php
/**
 * @version    CVS: 1.0.6
 * @package    Com_Astronomer
 * @author     Troy "Bear" Hall <webmaster@arksky.org>
 * @copyright  2016 Troy Hall & Arkansas Sky Observatory
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
