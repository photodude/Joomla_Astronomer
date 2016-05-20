<?php

/**
 * @version    CVS: 1.0.6
 * @package    Com_Astronomer
 * @author     Troy "Bear" Hall <webmaster@arksky.org>
 * @copyright  2016 Troy Hall & Arkansas Sky Observatory
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class AstronomerFrontendHelper
 *
 * @since  1.6
 */
class AstronomerHelpersAstronomer
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_astronomer/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_astronomer/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'AstronomerModel');
		}

		return $model;
	}
}
