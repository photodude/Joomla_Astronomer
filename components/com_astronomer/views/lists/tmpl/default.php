<?php
/**
 * @version    CVS: 1.0.4
 * @package    Com_Astronomer
 * @author     Troy Hall <troy@jowwow.net>
 * @copyright  2016 Troy Hall
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
?>
<!-- What data is requested -->
	<!-- check the search form ->

<!-- Get the data requested -->
	<!-- query db and put into "$rows" array -->

<!-- show the data -->
<div class="astrometry-raw-list">
	<div class="container">
		<div class="row">
			<pre class="raw-row col-sm-12">
				<?php
				foreach ($rows as $row) {
					echo $row . '<br>';
				}
				?>
			</pre>
		</div>
	</div>
</div>


<!-- copy as text button? -->
	<!-- copy raw data to their clipboard or download as raw -->


<!-- END OF FORM -->