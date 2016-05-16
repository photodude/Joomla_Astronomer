<?php
/**
 * @version    CVS: 1.0.4
 * @package    Com_Astronomer
 * @author     Troy Hall <troy@jowwow.net>
 * @copyright  2016 Troy Hall
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Lists list controller class.
 *
 * @since  1.6
 */
class AstronomerControllerLists extends AstronomerController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Lists', $prefix = 'AstronomerModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
