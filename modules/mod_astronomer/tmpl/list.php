<?php
/**
 * @version     CVS: 1.0.6
 * @package     com_astronomer
 * @subpackage  mod_astronomer
 * @author      Troy "Bear" Hall <webmaster@arksky.org>
 * @copyright   2016 Troy Hall & Arkansas Sky Observatory
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$elements = ModAstronomerHelper::getList($params);
?>

<?php if (!empty($elements)) : ?>
	<table class="table">
		<?php foreach ($elements as $element) : ?>
			<tr>
				<th><?php echo ModAstronomerHelper::renderTranslatableHeader($params, $params->get('field')); ?></th>
				<td><?php echo ModAstronomerHelper::renderElement(
						$params->get('table'), $params->get('field'), $element->{$params->get('field')}
					); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif;
