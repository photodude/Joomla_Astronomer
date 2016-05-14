<?php

/**
 * @version     CVS: 1.0.0
 * @package     com_astronomer
 * @subpackage  mod_astronomer
 * @author      Troy Hall <troy@jowwow.net>
 * @copyright   2016 Troy Hall
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include the syndicate functions only once
JLoader::register('ModAstronomerHelper', dirname(__FILE__) . '/helper.php');

$doc = JFactory::getDocument();

/* */
$doc->addStyleSheet(JURI::base() . '/media/mod_astronomer/css/style.css');

/* */
$doc->addScript(JURI::base() . '/media/mod_astronomer/js/script.js');

require JModuleHelper::getLayoutPath('mod_astronomer', $params->get('content_type', 'blank'));
