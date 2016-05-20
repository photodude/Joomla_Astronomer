<?php
/* declare variables */
$yep = '';
$nope = '';
date_default_timezone_set('UTC');
$i = 0;
$foundCount = 0;
$errors = '';
$uid = '424'; // Doc's user id, we'll need to pull this for each observer post shown

/* we need the params from the user profile plugin */
$plugin = JPluginHelper::getPlugin('user', '	astronomerprofile');
if ($plugin) {
	//get plugin params
	$pluginParams = new JRegistry($plugin->params);
	$header = $pluginParams->get('profile-require_header');
}

/* we need some functions */

function decimal_to_time($time, $precision = '5') {
	$DoW = sprintf("%02d", $time);
	$time = $time - $DoW;
	$time = bcmul($time, '24', $precision);
	$hours = sprintf("%02d", $time);
	$time = bcmul($time - $hours, '60', $precision);
	$minutes = sprintf("%02d", $time);
	$seconds = bcmul($time - $minutes, '60', $precision);

	return $DoW . ' ' . $hours . ':' . $minutes . ':' . $seconds;
}

/* NOW LETS CODE */

/* lets make sure last character is a EOL */
$data = htmlspecialchars($_POST["entrybox"]) . PHP_EOL;
/* convert line endings to *nix format and strip empty lines */
$data = preg_replace('/\n$/', '', preg_replace('/^\n/', '', preg_replace('/[\r\n]+/', "\n", $data)));

/* convert to an array now. */
$data = explode("\n", $data);

/* process array into entries */
$valid = array();
$invalid = array();
foreach ($data as $line) {
	if (!strlen($line) === '80') {
		$invalid[] = $line;
		$nope .= $line . '\r';
	} else {
		$valid[] = $line;
		$yep .= $line . "\r";
		$foundCount++;

		/* make sure we have a 4 digit year */
		$year = substr($line, 15, 4);

		/* make sure we have a 2 digit month */
		$month = sprintf("%02d", substr($line, 20, 2));

		/* get designation & strip the spaces, we dont' need / want them for searches */
		$desg = trim(substr($line, 0, 14), " ");

		/* we need 8 chars for DD.nnnnn ( n = decimal hours ) */
		$day = substr($line, 23, 8);

		/* only get the magnitude itself */
		$mag = substr($line, 65, 4);

		/* get the observatory identifer */
		$observatory = substr($line, 77, 3);

		/* lets get the day date & time in "standard" format */
		$dayTime = decimal_to_time($day);

		/* date in system useable format, YYYY-MM-DD HH:MM:SS.sssss, like we are used to */
		$humanDate = $year . '-' . $month . '-' . $dayTime;

		/* 2 digit singular day */
		$day = sprintf("%02d", $day);

		/* ITS DATABASE TIME */

// Get a db connection.
		$db = JFactory::getDbo();
		try {

// Create a new query object.
			$query = $db->getQuery(true);

			// Columns to insert.
			$columns = array('Human_Date', 'Designation', 'Year', 'Month', 'Day', 'Mag', 'Observatory', 'Entry');

// get the values we're going to place in the db
			$values = array($humanDate, $desg, $year, $month, $day, $mag, $observatory, $line);

// Prepare the insert query.
			$query
			->insert($db->quoteName('#__aso_astrometry'))
			->columns($db->quoteName($columns))
			->values(implod(',', $values));

// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			$result = $db->execute();
			$db->transactionCommit();
		} catch (Exception $errors) {
			// catch any database errors.
			$db->tranasctionRollback();
			JErrorPage::render($errors);
		}
	}
	$i++;
}
?>


<div class="well well-sm">
	<?php if ($nope) { ?>
		<div class="alert alert-danger">
			<?php
			echo "You have entered<pre>" . $nope . "</pre> Invalid lines<br> This data will NOT be processed. Please correct your format and try again.";
		}
		?>
	</div>
	<?php if ($yep) { ?>
		<div class="alert alert-success">
			<?php
			echo "You have entered <pre>" . $yep . "</pre><br> Your data will be processed";
		}
		?>
	</div>
</div>